<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2018 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

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
js_vars.upLink = "'.JRoute::_('index.php?option=com_meedya&format=raw', false).'";
js_vars.fup_payload = {task: "manage.upfile", galid: "'.$this->galid.'"};
js_vars.maxfilesize = '.($this->maxUploadFS/1048576).';';

JHtml::_('jquery.framework');
$jdoc = JFactory::getDocument();
$jdoc->addScriptDeclaration($script);
$jdoc->addCustomTag('<script src="'.JUri::base(true).'/'.MeedyaHelper::scriptVersion('upload').'" type="text/javascript"></script>');

$jdoc->addStyleSheet('components/com_meedya/static/css/gallery.css');

$jdoc->addStyleSheet('//cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/min/dropzone.min.css');
$jdoc->addScript('//cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/min/dropzone.min.js');
$jdoc->addScript('//cdnjs.cloudflare.com/ajax/libs/exif-js/2.3.0/exif.min.js');

$jdoc->addStyleSheet('components/com_meedya/static/css/upload.css');

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
	} elseif ($qper > 80) {
		$bcolr = $qcolors[1];
	} else {
		$bcolr = $qcolors[0];
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
</style>
<?php endif; ?>
<div class="meedya-gallery">
<?php if ($this->manage) echo JHtml::_('meedya.manageMenu', 1); ?>
<?php echo JHtml::_('meedya.pageHeader', $this->params, $this->action.'XXXX'); ?>
<?php if ($quota): ?>
<div id="quotaBar"><div id="qBar"><?=$qper?>%</div></div>
<?php endif; ?>
<p><big>UPLOADS HERE</big></p>
<p>STORAGE QUOTA: <?=MeedyaHelper::formatBytes($quota)?></p>
<p>STORAGE USED: <?=MeedyaHelper::formatBytes($this->totStore)?></p>
<?php if ($this->totStore < $quota): ?>
<p>MAX UPLOAD FILE SIZE: <?=$this->maxupld?></p>
<p>
	<!-- <?php var_dump($this->params); ?> -->
</p>
<table>
	<tr>
	<td colspan="1">
		<select id="h5u_album" name="h5u_album" onchange="album_select(this)">
			<option value="-1"><?=JText::_('COM_MEEDYA_H5U_NEWALBUM')?></option>
			<option value=""<?=($this->curalb?'':' selected')?>><?=JText::_('COM_MEEDYA_H5U_SELECT')?></option>
			<?=JHtml::_('meedya.albumsHierOptions', $this->albums, $this->curalb)?>
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
	</td>
	</tr>
</table>
<div class="row-fluid">
	<div id="dzupui" class="span12" style="display:none">
		<form action="<?php echo JRoute::_('index.php?option=com_meedya', false); ?>" class="dropzone" id="fileuploader" enctype="multipart/form-data">
			<p class="dz-message">Drop files here to upload<br />(or click to select)</p>
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
	init: function() {
		self = this;
		this.on('sending', function(file, xhr, formData) {
			formData.append('album', jQuery('#h5u_album').val());
		});
		this.on('complete', function(file) {
		//	console.log(file);
			if (file.accepted && (file.status=='success')) {
				setTimeout(function(){ self.removeFile(file); }, 5000);
			//	self.removeFile(file);
			}
		});
	},
	maxFilesize: js_vars.maxfilesize
};
</script>
