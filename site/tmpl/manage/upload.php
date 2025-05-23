<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;
use RJCreations\Component\Meedya\Site\Helper\HtmlMeedya;
use RJCreations\Component\Meedya\Site\Helper\MeedyaHelper;

HTMLHelper::addIncludePath(JPATH_COMPONENT.'/helpers');

// build and add javascript options
$h5opts = [
	'siteURL' => JUri::base().'index.php?option=com_meedya&Itemid='.$this->itemId,
	'upURL' => Route::_('index.php?option=com_meedya&format=raw&Itemid='.$this->itemId, false),
	'dropMessage' => 'Please drop files here to upload<br>(or click to select)',
//	'failcss' => 'alert-danger',
	'concurrent' => 4,
	'acptmime' => $this->acptmime,
	'mimatch' => $this->mimatch,
	'maxfilesize' => $this->maxUploadFS,
	'maxchunksize' => MeedyaHelper::phpMaxUp() - 32768,
	'fdonetarget' => 'quotaBar',	// element to receive event with server response for each uploaded file
	'timestamp' => $this->dbTime
];
$this->jDoc->addScriptOptions('H5uOpts', $h5opts);

// add stylesheets and javascript
HTMLHelper::_('jquery.framework');
//MeedyaHelper::addStyle(['gallery','manage','uplodr',['vendor/tags/'=>'jquery.tagsinput']]);
MeedyaHelper::oneStyle('gMUt');
//MeedyaHelper::addScript(['common','manage','fileup','uplodr',['vendor/tags/'=>'jquery.tagsinput']]);
MeedyaHelper::oneScript('MuUtb');

$script = '
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
';

if ($this->uplodr == 'UL')
	$script .= '
	Meedya.h5uOptions = {
		payload: function() {
			return {
				task: "ManRaw.upfile",
				[Joomla.getOptions("csrf.token")]: 1,
				album: jQuery("#h5u_album").val(),
				kywrd: jQuery("#h5u_keywords").val(),
			};
		},
		success: function(resp) { updStorBar(sqb, resp.split(\':\')[1]); },
		doneFunc: Meedya.uploadComplete
	};
';

$this->jDoc->addScriptDeclaration($script);

$this->btmscript[] = 'Meedya._ae("newalbdlg", "shown.bs.modal", () => Meedya._id("nualbnam").focus() );';
$this->btmscript[] = 'Meedya._ae("quotaBar", "mdya.totp", (evt) => Meedya.itmUpldRslt(evt.detail));';

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
?>
<?php if ($quota): ?>
<style>
#quotaBar {
	width: 400px;
	border: 1px solid #BBB;
	border-radius: 4px;
	margin-bottom: 1rem;
}
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
</style>
<?php endif; ?>
<div class="meedya-gallery">
<?php echo HtmlMeedya::manageMenu($this->userPerms, 0, $this->itemId); ?>
<?php echo HtmlMeedya::pageHeader($this->params, $this->action); ?>
<?php if (false && $quota): ?>
<h3>Storage Quota</h3>
<div class="progress progress-<?=$bclas?>">
	<div id="mdy-totupld" class="bar" role="progressbar" aria-valuenow="<?=$qper?>" aria-valuemin="0" aria-valuemax="100" style="width:<?=$qper?>%">
		<?=$qper?>%
	</div>
</div>
<?php endif; ?>
<?php
if (RJC_DBUG) {
	$ipp = MeedyaHelper::getImgProc('images/powered_by.png');
	echo "<p>{$ipp->ipp} image processor<br>{$this->phpupld} PHP max upload</p>";
}
?>
<h6><?=Text::_('COM_MEEDYA_QUOTA_VALUE')?> <?=MeedyaHelper::formatBytes($quota)?></h6>
<div id="quotaBar"><div id="qBar"><?=$qper?>%</div></div>
<!-- <p><big>== UPLOADS HERE ==</big></p>
<p>--@@-- STORAGE QUOTA: <?=MeedyaHelper::formatBytes($quota)?></p>
<p>--@@-- STORAGE USED: <?=MeedyaHelper::formatBytes($this->totStore)?></p> -->
<?php if ($this->totStore < $quota): ?>
<h6><?=Text::_('COM_MEEDYA_MAX_UPLD_SIZE')?> <?=$this->maxupld?></h6>
<p>
	<!-- <?php var_dump($this->params); ?> -->
</p>
<div class="albctl">
	<label for="h5u_album"><?=Text::_('COM_MEEDYA_H5U_ALB_SELECT')?></label>
	<select id="h5u_album" name="h5u_album" onchange="Meedya.album_select(this)">
		<option value="-1"><?=Text::_('COM_MEEDYA_H5U_NEWALBUM')?></option>
		<option value=""<?=($this->aid?'':' selected')?>><?=Text::_('COM_MEEDYA_H5U_SELECT')?></option>
		<?=HtmlMeedya::albumsHierOptions($this->albums, $this->aid)?>
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
echo LayoutHelper::render('newalbum', ['script'=>'Meedya.createAlbum(this)', 'albums'=>$this->albums]);
?>
<script>
jQuery('#h5u_keywords').tagsInput();
var sqb = document.getElementById("qBar");
window.addEventListener("load", () => {if (Meedya.h5uOptions) H5uSetup(Meedya.h5uOptions); else alert('UPLOAD ENGINE INITIALIZATION FAILURE')});
</script>
