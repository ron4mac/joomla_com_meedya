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

// build and add javascript options
$h5opts = [
	'frmtkn' => Session::getFormToken(),
	'siteURL' => JUri::base().'index.php?option=com_meedya&Itemid='.$this->itemId,
	'upURL' => Route::_('index.php?option=com_meedya&format=raw&Itemid='.$this->itemId, false),
	'dropMessage' => 'Please drop files here to upload<br>(or click to select)',
	'concurrent' => 4,
	'maxchunksize' => MeedyaHelper::phpMaxUp() - 262144,
	'timestamp' => $this->dbTime
];
$this->jDoc->addScriptOptions('H5uOpts', $h5opts);

// add stylesheets and javascript
HTMLHelper::_('jquery.framework');
MeedyaHelper::addStyle(['gallery','manage','uplodr',['jquery.tagsinput'=>'vendor/tags/']]);
MeedyaHelper::addScript(['manage','fileup','uplodr','bootbox',['jquery.tagsinput'=>'vendor/tags/']]);

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
		payload: function() { return {album: jQuery("#h5u_album").val(), kywrd: jQuery("#h5u_keywords").val()}; },
		success: function(resp) { updStorBar(sqb, resp.split(\':\')[1]); },
		doneFunc: uploadDone
	};
	function uploadDone (okcount, errcnt) {
	//	alert("There were "+okcount+" files uploaded with "+errcnt+" errors.");
		redirURL = H5uOpts.siteURL + "&task=manage.imgEdit&after=" + H5uOpts.timestamp;
		bootbox.confirm({
			message: "Edit info for the uploaded files?",
			buttons: {
				confirm: { label: "JYES", className: "btn-primary" },
				cancel: { label: "JCANCEL", className: "btn-secondary" }
			},
			callback: function(c){
				if (c) {
					window.location = redirURL;
				}
			}
		});
	}
	function showError (msg, file) {
		$id("errmsgs").style.display = "block";
		var div = document.createElement("div");
		div.innerHTML = "<span class=\"errmsg\">"+msg+"</span> : <span>"+file+"</span>";
		$id("errmsgs").appendChild(div);
	}
';

//HTMLHelper::_('bootstrap.loadCss', true);

$this->jDoc->addScriptDeclaration($script);

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
H5uSetup(Meedya.h5uOptions);
</script>
