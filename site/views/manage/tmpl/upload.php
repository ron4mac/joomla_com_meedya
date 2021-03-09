<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;

HTMLHelper::addIncludePath(JPATH_COMPONENT.'/helpers');

$script = 'var kkkkk = "YYYYY";
var js_vars = {concurrent: 3};
js_vars.h5uM = {
	selAlb: "'.Text::_('COM_MEEDYA_H5U_ALBMSELMSG').'",
	aborted: "'.Text::_('COM_MEEDYA_H5U_ABORTED').'",
	type_err: "'.Text::_('COM_MEEDYA_H5U_TYPE_ERR').'",
	size_err: "'.Text::_('COM_MEEDYA_H5U_SIZE_ERR').'",
	extallow: "'.Text::_('COM_MEEDYA_H5U_EXTALLOW').'",
	q_stop: "'.Text::_('COM_MEEDYA_H5U_Q_STOP').'",
	q_go: "'.Text::_('COM_MEEDYA_H5U_Q_RESUME').'",
	q_can: "'.Text::_('COM_MEEDYA_H5U_Q_CANCEL').'"
};
js_vars.timestamp = "'.$this->dbTime.'";
js_vars.frmtkn = "'.Session::getFormToken().'";
js_vars.user_id = '.Factory::getUser()->id.';
js_vars.site_url = "'.JUri::base().'index.php?option=com_meedya&Itemid='.$this->itemId.'";
js_vars.H5uPath = "'.JUri::base(true).'/components/com_meedya/static/";
//js_vars.upLink = "'.JUri::base().'index.php?option=com_meedya&format=raw";
js_vars.upLink = "'.Route::_('index.php?option=com_meedya&format=raw&Itemid='.$this->itemId, false).'";
js_vars.fup_payload = {task: "manage.upfile", galid: "'.$this->galid.'"};
js_vars.maxfilesize = '.($this->maxUploadFS/1048576).';';

$chnksize = MeedyaHelper::phpMaxUp() - 262144;
if ($this->uplodr == 'UL')
	$script .= '
	const h5uOptions = {
		upURL: js_vars.upLink,
		payload: function() { return {album: jQuery("#h5u_album").val(), kywrd: jQuery("#h5u_keywords").val()}; },
		dropMessage: "Please drop files here to upload<br>(or click to select)",
		concurrent:4,
	//	allowed_file_types: ["jpg","jpeg","mp4"],
		maxchunksize: '.$chnksize.',
		success: function(resp) { updStorBar(sqb, resp.split(\':\')[1]); },
		doneFunc: uploadDone
	};
	function uploadDone (okcount, errcnt) {
	//	alert("There were "+okcount+" files uploaded with "+errcnt+" errors.");
		redirURL = js_vars.site_url + "&task=manage.imgEdit&after=" + js_vars.timestamp;
		if (confirm("Edit info for files?")) window.location = redirURL;
	}
	function showError (msg, file) {
		$id("errmsgs").style.display = "block";
		var div = document.createElement("div");
		div.innerHTML = "<span class=\"errmsg\">"+msg+"</span> : <span>"+file+"</span>";
		$id("errmsgs").appendChild(div);
	}
';

//HTMLHelper::_('bootstrap.loadCss', true);
HTMLHelper::_('jquery.framework');

$this->jDoc->addScriptDeclaration($script);
//$this->jDoc->addCustomTag('<script src="'.JUri::base(true).'/'.MeedyaHelper::scriptVersion('upload').'" type="text/javascript"></script>');

$this->jDoc->addStyleSheet('components/com_meedya/static/css/gallery.css');
$this->jDoc->addStyleSheet('components/com_meedya/static/css/manage.css');
$this->jDoc->addStyleSheet('components/com_meedya/static/vendor/tags/jquery.tagsinput.css');
$this->jDoc->addStyleSheet('components/com_meedya/static/css/uplodr.css');

$this->jDoc->addScript('components/com_meedya/static/js/fileup.js');
$this->jDoc->addScript('components/com_meedya/static/vendor/tags/jquery.tagsinput.js');

$this->jDoc->addStyleSheet('components/com_meedya/static/css/upload.css');

Text::script('COM_MEEDYA_Q_STOPPED');

$qcolors = ['#eeffee','#fff888','#ff8888'];
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
//HTMLHelper::script(Juri::base() . 'components/com_meedya/static/js/uplodr.js');
?>
<script src="components/com_meedya/static/js/uplodr.js"></script>
<?php if ($quota): ?>
<style>
#quotaBar { width: 400px; border: 1px solid #BBB; border-radius: 4px; }
#qBar {
	background-color: <?=$bcolr?>;
	/*height: 20px;*/
	border-right: 1px solid #CCC;
	border-radius: 3px;
	text-align: center;
	font-size: larger;
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
.modal-backdrop.fade.in {
    opacity: 0.3;
}
</style>
<?php endif; ?>
<div class="meedya-gallery">
<?php echo HTMLHelper::_('meedya.manageMenu', $this->userPerms, 0, $this->itemId); ?>
<?php echo HTMLHelper::_('meedya.pageHeader', $this->params, $this->action); ?>
<?php if (false && $quota): ?>
<h3>Storage Quota</h3>
<div class="progress progress-<?=$bclas?>">
	<div id="mdy-totupld" class="bar" role="progressbar" aria-valuenow="<?=$qper?>" aria-valuemin="0" aria-valuemax="100" style="width:<?=$qper?>%">
		<?=$qper?>%
	</div>
</div>
<?php endif; ?>
<h4><?=Text::_('COM_MEEDYA_QUOTA_VALUE')?> <?=MeedyaHelper::formatBytes($quota)?></h4>
<div id="quotaBar"><div id="qBar"><?=$qper?>%</div></div>
<!-- <p><big>== UPLOADS HERE ==</big></p>
<p>--@@-- STORAGE QUOTA: <?=MeedyaHelper::formatBytes($quota)?></p>
<p>--@@-- STORAGE USED: <?=MeedyaHelper::formatBytes($this->totStore)?></p> -->
<?php if ($this->totStore < $quota): ?>
<h4><?=Text::_('COM_MEEDYA_MAX_UPLD_SIZE')?> <?=$this->maxupld?></h4>
<p>
	<!-- <?php var_dump($this->params); ?> -->
</p>
<?php
if (RJC_DBUG) {
	$ipp = MeedyaHelper::getImgProc('images/powered_by.png');
	echo "<p>{$ipp->ipp} image processor</p>";
}
?>
<div class="albctl">
	<label for="h5u_album"><?=Text::_('COM_MEEDYA_H5U_ALB_SELECT')?></label>
	<select id="h5u_album" name="h5u_album" onchange="album_select(this)">
		<option value="-1"><?=Text::_('COM_MEEDYA_H5U_NEWALBUM')?></option>
		<option value=""<?=($this->aid?'':' selected')?>><?=Text::_('COM_MEEDYA_H5U_SELECT')?></option>
		<?=HTMLHelper::_('meedya.albumsHierOptions', $this->albums, $this->aid)?>
	</select>
</div>
<div class="row-fluid">
	<div id="dzupui" class="span12"<?= ($this->aid ? '' : ' style="display:none"') ?>>
		<label for="h5u_keywords">Tags: </label><input type="text" id="h5u_keywords" />
		<div id="errmsgs"></div>
		<div id="uplodr"></div>
	</div>
</div>
<?php else: ?>
<h2>File Upload Not Available</h2>
<h3>You have exceeded your storage quota.</h3>
<?php endif; ?>
</div>
<?php
echo HTMLHelper::_(
	'bootstrap.renderModal',
	'newalbdlg',
	['title' => Text::_('COM_MEEDYA_CREATE_NEW_ALBUM'),
	'footer' => HTMLHelper::_('meedya.modalButtons', 'COM_MEEDYA_H5U_CREALBM','createAlbum(this)', 'creab'),
	'modalWidth' => '40'],
	$this->loadTemplate('newalb')
	);
?>
<script>
jQuery('#h5u_keywords').tagsInput();
var sqb = document.getElementById("qBar");
H5uSetup();
</script>
