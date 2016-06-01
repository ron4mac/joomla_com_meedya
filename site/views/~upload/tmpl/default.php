<?php
// no direct access
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
js_vars.user_id = '.JFactory::getUser()->id.';
js_vars.site_url = "'.JUri::base().'index.php?option=com_meedya";
js_vars.H5uPath = "'.JUri::base(true).'/components/com_meedya/static/";
js_vars.upLink = "'.JUri::base().'index.php?option=com_meedya&format=raw";
js_vars.fup_payload = {task: "manage.upfile", galid: "'.$this->galid.'"};
js_vars.maxfilesize = '.($this->params->get('max_upload')+(8*1024*1024)).';';

JHtml::stylesheet('components/com_meedya/static/css/upload.css');
JHtml::_('jquery.framework');
$jdoc = JFactory::getDocument();
$jdoc->addScriptDeclaration($script);
//$jdoc->addScript('components/com_meedya/static/dynamic.js');
//$jdoc->addScript('components/com_meedya/static/upload.js');
$jdoc->addCustomTag('<script src="'.JUri::base(true).'/components/com_meedya/static/js/upload.js" type="text/javascript"></script>');
?>
<div class="meedya-gallery">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
<p><big>UPLOADS HERE</big></p>
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
	<tr id="h5upldrow">
		<!-- <td class="tableb"><?= JText::_('COM_MEEDYA_H5U_FILES') ?></td> -->
		<td class="tableb" style="padding:1em">
			<div style="width:480px">
				<input type="file" name="userpictures" id="upload_field" multiple="multiple" <?=$this->acptmime?>/>
				&nbsp;<br />
				<div id="dropArea"><?= JText::_('COM_MEEDYA_H5U_DROP_FILES') ?></div>
				&nbsp;<br />
				<div id="progress_report" style="position:relative">
					<div id="progress_report_name"></div>
					<div id="progress_report_status" style="font-style: italic;"></div>
					<div id="totprogress">
						<div id="progress_report_bar" style="background-color: blue; width: 0; height: 100%;"></div>
					</div>
					<div>
						<?= JText::_('COM_MEEDYA_H5U_FILES_LEFT') ?><span id="qcount">0</span><div class="acti" id="qstop"><img src="components/com_meedya/static/css/stop.png" title="<?= JText::_('COM_MEEDYA_H5U_Q_STOP') ?>" onclick="H5uQctrl.stop()" /></div><div class="acti" id="qgocan"><img src="components/com_meedya/static/css/play-green.png" title="<?= JText::_('COM_MEEDYA_H5U_Q_RESUME') ?>" onclick="H5uQctrl.go()" /><img src="components/com_meedya/static/css/cross.png" title="<?= JText::_('COM_MEEDYA_H5U_Q_CANCEL') ?>" onclick="H5uQctrl.cancel()" /></div>
					</div>
					<div id="fprogress"></div>
					<div id="server_response"></div>
				</div>
			</div>
		</td>
	</tr>
	<tr id="gotoedit" style="display:none">
		<!-- <td class="tableb tableb_alternate"><?= JText::_('COM_MEEDYA_H5U_CONTINUE') ?></td> -->
		<td class="tableb tableb_alternate">
			<button type="button" onclick="window.location=redirURL"><?= JText::_('COM_MEEDYA_H5U_GOTOEDIT') ?></button>
		</td>
	</tr>
</table>
</div>