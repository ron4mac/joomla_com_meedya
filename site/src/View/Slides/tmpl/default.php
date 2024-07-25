<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

error_reporting(E_ALL);
ini_set('display_errors', '1');

//HTMLHelper::stylesheet('components/com_meedya/static/css/slides.css');
MeedyaHelper::oneStyle('s');
MeedyaHelper::oneScript('s');

$H5ss_cfg = $this->html5slideshowCfg;

$errmsg = '';
$filelist = [];
$usrisown = false;

$fprefix = $H5ss_cfg['pS'] == 1 ? 'normal_' : '';
$count = 0;
$album_name = $this->album->title;;
if ($this->slides) {
	foreach ($this->slides as $slide) {
		//$ftyp = cpg_get_type($row['filename']);
		//if ($ftyp['content'] != 'image') continue;
		$txtinfo = '';
		if ($H5ss_cfg['vT']) $txtinfo = trim($slide['title']?:'');
		if ($H5ss_cfg['vD'] && trim($slide['desc']?:'')) $txtinfo .= ($txtinfo ? ' ... ' : '') . trim($slide['desc']?:'');
		$fileentry = [
				'fpath' => $slide['file'],
				'title' => $txtinfo,
				'mTyp' => substr($slide['mtype'], 0, 1)
				];
		$filelist[] = $fileentry;
	}
} else {
	$errmsg .= Text::_('COM_MEEDYA_SS_NOIMGERR');
}
//var_dump($filelist);
//if (!count($filelist)) $errmsg .= Text::_('COM_MEEDYA_SS_NOIMGERR');
if ($H5ss_cfg['sI']) shuffle($filelist);
$popdwin = false;	//($superCage->get->getInt('h5sspu') == 1);
$icons = $H5ss_cfg['iS'];
$dcolors = $H5ss_cfg['dC'];		//explode(',', $H5ss_cfg['dC']);
?>
<style type="text/css">
	html { height:100%; overflow:hidden }
	body { background-color:<?=$dcolors[4]?>; width:100%; height:100%; overflow:hidden }
	div#controls { background-color:<?=$dcolors[0]?>; color:<?=$dcolors[1]?>; }
	div#ptext { background-color:<?=$dcolors[2]?>; color:<?=$dcolors[3]?>; }
	div#screen { background-color:<?=$dcolors[4]?>;padding-left:12px;padding-right:12px;position:relative; }
	div.spribut { background: url('<?=JUri::root(true)?>/components/com_meedya/static/css/icons/<?=$icons?>.png') no-repeat; }
</style>
<script type="text/javascript">
	var albumID = '<?=$this->aid?>';
	var popdwin = <?=$popdwin?'true':'false'?>;
	var baseUrl = "<?=JUri::root(true).'/'.$this->gallpath?>/med/";
	var _imgP = "<?=JUri::root(true)?>/components/com_meedya/static/img/";
	var viderror = "COULD NOT PLAY VIDEO";
	var imagelist = <?=json_encode($filelist)?>;
	var imgerror = "<?=Text::_('COM_MEEDYA_SS_IMGERROR')?>";
	ssCtl.autoPlay = <?=$H5ss_cfg['aP']*1?>;
	ssCtl.repeat = <?=$H5ss_cfg['lS']?'true':'false'?>;
	ssCtl.slideDur = <?=$H5ss_cfg['sD']*1000?>;
	ssCtl.trnType = "<?=$H5ss_cfg['tT']?>";
	function asscfg() {
		var csl = "index.php?file=html5slideshow/config&album="+albumID;
		if (popdwin) { parent.opener.location = csl; window.close(); }
		else { window.location = csl; }
	}
</script>
<?php if ($errmsg): ?>
	<div id="ptext"><?=$errmsg?>&nbsp;&nbsp;<button type="button" onclick="ssCtl.doMnu(0)"><?=Text::_('COM_MEEDYA_SS_STOP_A')?></button></div>
<?php else: ?>
	<div id="fullarea">
		<div id="controls">
			<div class="albnam"><p><span id="albNam"><?=$album_name?>&nbsp;&nbsp;::&nbsp;&nbsp;</span><?=sprintf(Text::_('COM_MEEDYA_SS_OF_FORMAT'),'<span id="slidnum"></span>',count($filelist))?></p></div>
		<?php if ($usrisown): ?>
			<img class="sscfg" src="images/icons/config.png" width="16" height="16" onclick="asscfg()" title="<?=Text::_('COM_MEEDYA_SS_CONFIGSS')?>" alt="config" />
		<?php endif; ?>
			<div class="ofslid">
				<div id="cb_less" class="spribut" onclick="ssCtl.sdur(0)" title="<?=Text::_('COM_MEEDYA_SS_MINUS')?>"></div>
				<span id="seconds"></span>&nbsp;<?=Text::_('COM_MEEDYA_SS_SECSABRV')?>
				<div id="cb_more" class="spribut" onclick="ssCtl.sdur(1)" title="<?=Text::_('COM_MEEDYA_SS_PLUS')?>"></div>
			</div>
			<div class="sldctls">
				<div id="cb_stop" class="spribut" onclick="ssCtl.doMnu(0)" title="<?=Text::_('COM_MEEDYA_SS_STOP_T')?>"></div>
				&nbsp;
				<div id="cb_rwnd" class="spribut" onclick="ssCtl.doMnu(1)" title="<?=Text::_('COM_MEEDYA_SS_RWND_T')?>"></div>
				<div id="cb_prev" class="spribut" onclick="ssCtl.doMnu(2)" title="<?=Text::_('COM_MEEDYA_SS_PREV_T')?>"></div>
				<div id="cb_paus" class="spribut" onclick="ssCtl.doMnu(3)" title="<?=Text::_('COM_MEEDYA_SS_TOGL_T')?>"></div>
				<div id="cb_next" class="spribut" onclick="ssCtl.doMnu(4)" title="<?=Text::_('COM_MEEDYA_SS_NEXT_T')?>"></div>
				<div id="cb_last" class="spribut" onclick="ssCtl.doMnu(5)" title="<?=Text::_('COM_MEEDYA_SS_LAST_T')?>"></div>
				&nbsp;&nbsp;
				<div id="cb_full" class="spribut" onclick="ssCtl.doMnu(6)" title="<?=Text::_('COM_MEEDYA_SS_FULL_T')?>"></div>
			</div>
		</div>
		<div id="ptext"></div>
		<div id="screen">
			<div id="loading" style="display:none"><img src="components/com_meedya/static/img/eclipse.svg" /></div>
		</div>
		<!-- <div id="status"></div> -->
	</div>
<?php endif; ?>
