<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2020 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

$script = 'var kkkkk = "YYYYY";
var js_vars = {concurrent: 3};
js_vars.h5uM = {
	selAlb: "'.JText::_('COM_MEEDYA_H5U_ALBMSELMSG').'",
	aborted: "'.JText::_('COM_MEEDYA_H5U_ABORTED').'",
	type_err: "'.JText::_('COM_MEEDYA_H5U_TYPE_ERR').'",
	size_err: "'.JText::_('COM_MEEDYA_H5U_SIZE_ERR').'",
	extallow: "'.JText::_('COM_MEEDYA_H5U_EXTALLOW').'",
	q_stop: "'.JText::_('COM_MEEDYA_H5U_Q_STOP').'",
	q_go: "'.JText::_('COM_MEEDYA_H5U_Q_RESUME').'",
	q_can: "'.JText::_('COM_MEEDYA_H5U_Q_CANCEL').'"
};
js_vars.timestamp = "'.$this->dbTime.'";
js_vars.frmtkn = "'.JSession::getFormToken().'";
js_vars.user_id = '.JFactory::getUser()->id.';
js_vars.site_url = "'.JUri::base().'index.php?option=com_meedya";
js_vars.H5uPath = "'.JUri::base(true).'/components/com_meedya/static/";
//js_vars.upLink = "'.JUri::base().'index.php?option=com_meedya&format=raw";
js_vars.upLink = "'.Route::_('index.php?option=com_meedya&format=raw&Itemid='.$this->itemId, false).'";
js_vars.fup_payload = {task: "manage.upfile", galid: "'.$this->galid.'"};
js_vars.maxfilesize = '.($this->maxUploadFS/1048576).';';

//JHtml::_('bootstrap.loadCss', true);
JHtml::_('jquery.framework');

$this->jDoc->addScriptDeclaration($script);
//$this->jDoc->addCustomTag('<script src="'.JUri::base(true).'/'.MeedyaHelper::scriptVersion('upload').'" type="text/javascript"></script>');

$this->jDoc->addStyleSheet('components/com_meedya/static/css/gallery.css');

$this->jDoc->addStyleSheet('//cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.0/min/dropzone.min.css');
$this->jDoc->addScript('//cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.0/min/dropzone.min.js');
$this->jDoc->addScript('//cdnjs.cloudflare.com/ajax/libs/exif-js/2.3.0/exif.min.js');
$this->jDoc->addScript('components/com_meedya/static/js/fileup.js');

$this->jDoc->addStyleSheet('components/com_meedya/static/css/upload.css');

JText::script('COM_MEEDYA_Q_STOPPED');

$qcolors = array('#eeeeee','#fff888','#ff8888');
$quota = MeedyaHelper::getStoreQuota($this->params);	//echo'<pre>';var_dump($this->params);echo'</pre>';
if ($quota) {
	$qper = $this->totStore / $quota;
//	if ($qper > 1) {
//		$qper = 100;
//	} else {
		$qper = (int)($qper*100);
//	}
	if ($qper > 90) {
		$bcolr = $qcolors[2];
		$bclas = 'danger';
	} elseif ($qper > 80) {
		$bcolr = $qcolors[1];
		$bclas = 'warning';
	} else {
		$bcolr = $qcolors[0];
		$bclas = 'success';
	}
}
?>
<?php if ($quota): ?>
<style>
#quotaBar { width: 400px; border: 1px solid #BBB; border-radius: 4px; }
#qBar {
	background-color: <?=$bcolr?>;
	/*height: 20px;*/
	border-radius: 3px;
	text-align: center;
	font-size: large;
	/*color: white;*/
	width: <?=$qper>100 ? 100 : $qper?>%;
}
.progress { height:22px; }
.progress .bar { font-size:16px; }
.dropzone {
	min-height: 130px;
	border: 1px solid rgba(0,0,0,0.3);
	padding: 10px 10px;
	border-radius: 5px;
}
</style>
<?php endif; ?>
<div class="meedya-gallery">
<?php echo JHtml::_('meedya.manageMenu', $this->userPerms, 0, $this->itemId); ?>
<?php echo JHtml::_('meedya.pageHeader', $this->params, $this->action); ?>
<?php if (false && $quota): ?>
<h3>Storage Quota</h3>
<div class="progress progress-<?=$bclas?>">
	<div id="mdy-totupld" class="bar" role="progressbar" aria-valuenow="<?=$qper?>" aria-valuemin="0" aria-valuemax="100" style="width:<?=$qper?>%">
		<?=$qper?>%
	</div>
</div>
<?php endif; ?>
<h4><?=JText::_('COM_MEEDYA_QUOTA_VALUE')?> <?=MeedyaHelper::formatBytes($quota)?></h4>
<div id="quotaBar"><div id="qBar"><?=$qper?>%</div></div>
<!-- <p><big>== UPLOADS HERE ==</big></p>
<p>--@@-- STORAGE QUOTA: <?=MeedyaHelper::formatBytes($quota)?></p>
<p>--@@-- STORAGE USED: <?=MeedyaHelper::formatBytes($this->totStore)?></p> -->
<?php if ($this->totStore < $quota): ?>
<h4><?=JText::_('COM_MEEDYA_MAX_UPLD_SIZE')?> <?=$this->maxupld?></h4>
<p>
	<!-- <?php var_dump($this->params); ?> -->
</p>

<div class="albctl">
	<label for="h5u_album"><?=JText::_('COM_MEEDYA_H5U_ALB_SELECT')?></label>
	<select id="h5u_album" name="h5u_album" onchange="album_select(this)">
		<option value="-1"><?=JText::_('COM_MEEDYA_H5U_NEWALBUM')?></option>
		<option value=""<?=($this->aid?'':' selected')?>><?=JText::_('COM_MEEDYA_H5U_SELECT')?></option>
		<?=JHtml::_('meedya.albumsHierOptions', $this->albums, $this->aid)?>
	</select>
	<div id="crealbm" style="display:none;">
		<input type="text" name="nualbnam" id="nualbnam" value="" style="margin-left:2em" onkeyup="watchAlbNam(this)" />
<?php if ($this->albums): ?>
		<select id="h5u_palbum" name="h5u_palbum">
			<option value=""><?=JText::_('COM_MEEDYA_H5U_SELPAR')?></option>
			<option value="0"><?=JText::_('COM_MEEDYA_H5U_NONE')?></option>
			<?=JHtml::_('meedya.albumsHierOptions', $this->albums)?>
		</select>
<?php endif; ?>
		<button type="button" id="creab" onclick="createAlbum(this)" style="vertical-align:text-bottom" disabled><?=JText::_('COM_MEEDYA_H5U_CREALBM')?></button>
		<img src="<?=JUri::base(true)?>/components/com_meedya/static/css/process.gif" style="vertical-align:baseline;visibility:hidden;" />
	</div>
</div>

<div class="row-fluid">
	<div id="dzupui" class="span12"<?= ($this->aid ? '' : ' style="display:none"') ?>>
		<form action="<?php echo Route::_('index.php?option=com_meedya&Itemid='.$this->itemId, false); ?>" class="dropzone" id="fileuploader" enctype="multipart/form-data">
			<p class="dz-message" style="font-size:18px">Drop files here to upload<br />(or click to select)</p>
			<input type="hidden" name="task" value="manage.upfile">
			<input type="hidden" name="galid" value="<?php echo $this->galid; ?>">
			<input type="hidden" name="format" value="raw">
			<?php echo JHtml::_('form.token'); ?>
			<div class="fallback">
				<input name="file" type="file" multiple />
			</div>
		</form>
	</div>
</div>
<?php else: ?>
<h2>File Upload Not Available</h2>
<h3>You have exceeded your storage quota.</h3>
<?php endif; ?>
</div>
<script>
Dropzone.options.fileuploader = {
	paramName: 'userpicture',
	acceptedFiles: 'image/*,video/*',
	maxFilesize: js_vars.maxfilesize, // + 134217728,
//	addRemoveLinks: true,
	init: function() {
		var self = this;
//		var prgelm = document.getElementById("mdy-totupld");
//		var prgelm = document.getElementById("qBar");
		this.on('sending', function(file, xhr, formData) {
			formData.append('album', jQuery('#h5u_album').val());
		});
		this.on('success', function(file, resp) {
			setTimeout(function(){ self.removeFile(file); }, 2500);
		});
		this.on('error', function(file, emsg, xhr) {
			if (xhr && xhr.status==403) {
				if (self.options.autoProcessQueue) {
					var emsg = file.xhr.responseText;
					self.options.autoProcessQueue = false;
					alert(emsg+"\n"+Joomla.JText._('COM_MEEDYA_Q_STOPPED'));
				}
			}
		});
		this.on('queuecomplete', function() {
			console.log(this.getRejectedFiles());
			if (!this.getRejectedFiles())
			setTimeout(function(){
			 	redirURL = js_vars.site_url + '&task=manage.imgEdit&after=' + js_vars.timestamp;
				window.location = redirURL;
			}, 2500);
		});
//		this.on('totaluploadprogress', function(pct,totb,bsnt) {
//		//	console.log(pct,totb,bsnt);
//			prgelm.style.width = pct+"%";
//		});
		//	console.log(file);
	}
};
</script>
