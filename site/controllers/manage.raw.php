<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2016 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once 'manage.php';

/*

JLoader::register('JHtmlMeedya', JPATH_COMPONENT . '/helpers/html/meedya.php');

class MeedyaControllerManage extends JControllerLegacy
{
//	public function __construct ($config = array())
//	{
//		parent::__construct($config);
//		if (JDEBUG) { JLog::addLogger(array('text_file'=>'com_meedya.log.php'), JLog::ALL, array('com_meedya')); }
//	}


	// task to receive and store uploaded files
	public function upfile ()
	{
		if (JDEBUG) { JLog::add('upfile: '.print_r($this->input->post,true), JLog::INFO, 'com_meedya'); }
		$galid = base64_decode($this->input->get('galid', '', 'base64'));
		$file = $this->input->files->get('userpicture');

		try {
			$m = $this->getModel('manage');
			$m->storeFile($file, $this->input->post->get('album', 0, 'int'));
		}
		catch (Exception $e) {
			header("HTTP/1.1 400 Failed to store file");
			echo 'Error storing file: ' . $e->getMessage();
		}
	}


	// task to create a new album
	public function newAlbum ()
	{
		if (JSession::checkToken()) {
			$a = $this->input->post->get('albnam', 'A NEW ALBUM', 'string');
			$p = $this->input->post->get('paralb', 0, 'int');
			$d = $this->input->post->get('albdesc', null, 'string');
			$m = $this->getModel('manage');
			$aid = $m->addAlbum($a, $p, $d);
			if (!$aid) {
				header("HTTP/1.0 400 Could not create album: {$a}");
			} elseif ($this->input->post->get('o', 0, 'int')) {
				$albs = $m->getAlbumsList();
				echo JHtml::_('meedya.albumsHierOptions', $albs, $aid);
			}
		} else {
			echo 'Bad request (token)';
		}
	}


	// task to remove items from an album
	public function removeItems ()
	{
		if (JSession::checkToken()) {
			$aid = $this->input->post->get('aid','','int');
			$parm = $this->input->post->get('items','','string');
			$items = explode('|',$parm);
			$m = $this->getModel('manage');
			$m->removeItems($aid, $items);
		} else {
			echo 'Bad request (token)';
		}
	}


	public function adjustAlbPaid ()
	{
		if (JSession::checkToken()) {
			$aid = $this->input->post->get('aid','','int');
			$paid = $this->input->post->get('paid','','int');
			$m = $this->getModel('manage');
			$m->setAlbumPaid($aid, $paid);
		} else {
			echo 'Bad request (token)';
		}
	}


}
*/
