<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/* uplodr v0.8 */
// a server side accompnyment for my js uploader script

class Up_Load
{
	protected $target_dir;
	protected $pvals;
	protected $file = null;
	protected $target_file;
	protected $filup_cb = null;
	// for chunking
	protected $ckid;
	protected $ckpath;
	protected $tmpath;
	protected $chnkdone = false;

	public function __construct ($input, &$toname, $config=[])
	{
		// pull in configuration items
		foreach ($config as $k => $v) { $this->$k = $v; }

		// pull in the POSTed values
	//	$this->pvals = (object)$_POST;			$this->upldLog(print_r($_POST, true).print_r($_FILES, true));
		$this->pvals = $input->post;			$this->upldLog(print_r($input->post, true).print_r($input->files, true));
		$this->file = $input->files->get('Filedata', null, 'raw');		$this->upldLog(print_r($this->file, true));

		// process the incoming data
		try {
			if (empty($this->pvals->get('chunkact'))) {
				$this->receiveFile();
				$toname = $this->file['name'];
			} else {
				$this->processChunk();
				if ($this->chnkdone) $toname = $this->pvals->getString('fname');
			}
		}
		catch (Exception $e) {
			header('HTTP/1.1 '.(400+$e->getCode()).' Failed to store file');
			echo 'Error storing file: ' . $e->getMessage();
		}
	}

	public function placeFile ($dest)
	{
		$this->upldLog(print_r($this->file, true).$dest."\n");

		if (empty($this->pvals->get('chunkact'))) {
			if (!move_uploaded_file($this->file['tmp_name'], $dest)) throw new Exception('Could not place file');
		} else {
			// count all the parts of this file
			$total_files = 0;
			foreach(scandir($this->ckpath) as $filepart) {
				if (strpos($filepart,'part') === 0) {
					$total_files++;
				}
			}
			$totalchunks = (int)$this->pvals->get('tchnk');
			if ($total_files !== $totalchunks) die('Missing some file chunk(s)');

			// create the final destination file
		//	$dest = $this->target_dir . $this->pvals->get('fname');
			if (($fp = @fopen($dest, 'w')) !== false) {
				for ($i=1; $i<=$totalchunks; $i++) {
					fwrite($fp, file_get_contents($this->ckpath.'/part'.$i));
				}
				fclose($fp);
			} else {
				$this->upldLog('failed to open destination file: '.$dest);
				die('failed to open destination file: '.$dest);
			}
	
			$this->upldLog('combined chunks: '.$dest);
			$this->cleanup();
		}
	}

	// a method to clean up when file can't be placed
	// probably not necessary but shows good server citizenship
	public function cancel_transfer ()
	{
		if (empty($this->pvals->get('chunkact'))) {
			@unlink($this->file['tmp_name']);
		} else {
			$this->cleanup();
		}
	}

	// check uploaded file data for issues
	private function vetUpload ($fec=true)
	{
		if (!$this->file) throw new Exception('Parameters error', 9);
		switch ($this->file['error']) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				throw new Exception('No file sent.', UPLOAD_ERR_NO_FILE);
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new Exception('Exceeded filesize limit.', UPLOAD_ERR_INI_SIZE);
			case UPLOAD_ERR_PARTIAL:
				throw new Exception('Only partial file uploaded.', UPLOAD_ERR_PARTIAL);
			case UPLOAD_ERR_NO_TMP_DIR:
				throw new Exception('Missing temporary folder.', UPLOAD_ERR_NO_TMP_DIR);
			case UPLOAD_ERR_CANT_WRITE:
				throw new Exception('Failed to write file.', UPLOAD_ERR_CANT_WRITE);
			default:
				throw new Exception('Unknown error. '.$this->file['error']);
		}
		$target_file = $this->target_dir . basename($this->file['name']);
		if ($fec && file_exists($target_file)) throw new Exception('File already exists');
		$this->target_file = $target_file;
	}

	// normal receipt of a single uploaded file
	private function receiveFile ()
	{
		$this->vetUpload();
		if (!$this->aValidFile()) throw new Exception('The file is not acceptable.', 0);
	}

	// receive a file from multiple 'chunks'
	private function processChunk ()
	{
		$this->ckid = $this->pvals->get('ident');
		$this->tmpath = sys_get_temp_dir() . '/';
		$this->ckpath = $this->tmpath . $this->ckid;
		switch ($this->pvals->get('chunkact')) {
			case 'pref':
				$target_file = $this->target_dir . basename($this->pvals->get('file'));
				if (file_exists($target_file)) throw new Exception('File already exists');
				// create the temporary directory, if necessary
				if ($this->ckid && !is_dir($this->ckpath)) {
					mkdir($this->ckpath, 0777, true);
					$this->upldLog('created chunk dir: '.$this->ckpath);
				}
				break;
			case 'chnk':
				$this->vetUpload(false);
				$this->addChunk();
				break;
			case 'abrt':
				$this->cleanup();
				break;
			default:
				echo '?-?-?';
		}
	}

	// receive, process and place a 'chunk' of file data
	private function addChunk ()
	{
		$chnkn = $this->pvals->get('chnkn');
		if ($chnkn == 1) {
			if (!$this->aValidFile()) {
				$this->cleanup();
				throw new Exception('The file is not acceptable.', 0);
			}
		}
		$dest = $this->ckpath.'/part'.$chnkn;
		if (!move_uploaded_file($this->file['tmp_name'], $dest)) {
			$this->upldLog('failed to place chunk: '.$dest);
			die('Failed to place chunk #'.$chnkn);
		}
		$this->upldLog('placed chunk: '.$dest);

		$totalchunks = (int)$this->pvals->get('tchnk');
		if ($chnkn == $totalchunks) {

			// count all the parts of this file
			$total_files = 0;
			foreach(scandir($this->ckpath) as $filepart) {
				if (strpos($filepart,'part') === 0) {
					$total_files++;
				}
			}

			if ($total_files !== $totalchunks) die('Missing some file chunk(s)');
			$this->chnkdone = true;

			return;

			// create the final destination file
			$dest = $this->target_dir . $this->pvals->get('fname');
			if (($fp = @fopen($dest, 'w')) !== false) {
				for ($i=1; $i<=$totalchunks; $i++) {
					fwrite($fp, file_get_contents($this->ckpath.'/part'.$i));
				}
				fclose($fp);
			} else {
				$this->upldLog('failed to open destination file: '.$dest);
				die('failed to open destination file: '.$dest);
			}
	
			$this->upldLog('combined chunks: '.$dest);
			$this->cleanup();
			if ($this->filup_cb) call_user_func($this->filup_cb, basename($dest));
		}
	}

	// check that the file is actually a valid media file
	private function aValidFile ()
	{
		$mime = mime_content_type($this->file['tmp_name']);
		if (preg_match('#image\/|video\/#', $mime)) return true;
		@unlink($this->file['tmp_name']);
		return false;
	}

	// remove temporary storage that was used for chunks
	private function cleanup ()
	{
		if ($this->ckid) $this->rrmdir($this->ckpath);
		$this->upldLog('chunks cleared: '.$this->ckpath);
	}

	private function rrmdir ($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != '.' && $object != '..') {
					if (filetype($dir . '/' . $object) == 'dir') {
						$this->rrmdir($dir . '/' . $object); 
					} else {
						unlink($dir . '/' . $object);
					}
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
	
	private function upldLog ($ntry)
	{	//return;
		file_put_contents('UPLOG.txt', $ntry."\n", FILE_APPEND);
	}

}
