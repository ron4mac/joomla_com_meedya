<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/* ========== NOTICE! THIS TEMPLATE IS REUSED BY SEARCH DISPLAY ========== */

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('jquery.framework');
MeedyaHelper::addStyle('album');
MeedyaHelper::addScript('meedya');
if ($this->useFanCB) {
	$this->jDoc->addStyleSheet('https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css');
	$this->jDoc->addScript('https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js');
} else {
	MeedyaHelper::addStyle('each');
	MeedyaHelper::addScript('vuesld');
}

$jslang = [
		'no_sterm' => Text::_('COM_MEEDYA_MSG_STERM'),
		'ru_sure' => Text::_('COM_USERNOTES_RU_SURE')
	];
$this->jDoc->addScriptDeclaration('Meedya.L = '.json_encode($jslang).';
');

HTMLHelper::_('bootstrap.tooltip', '.hasTip', ['fixed'=>true]);

$filelist = [];
if ($this->items) {
	foreach ($this->items as $file) {
		//$ftyp = cpg_get_type($row['filename']);
		//if ($ftyp['content'] != 'image') continue;
		$txtinfo = '';
		$txtinfo .= trim($file['title']);
		$txtinfo .= ($txtinfo ? ' ... ' : '') . trim($file['desc']);
if ($this->useFanCB) {
		$mTyp = substr($file['mtype'], 0, 5);
		$murl = JUri::root(true).'/'.$this->gallpath.($mTyp=='image' ? '/med/' : '/img/').$file['file'];
		$fileentry = [
			'src' => $murl,
			'title' => $txtinfo,
			'type' => $mTyp
			];
} else {
		$fileentry = [
			'fpath' => $file['file'],
			'title' => $txtinfo,
			'mTyp' => substr($file['mtype'], 0, 1)
			];
}
		$filelist[] = $fileentry;
	}
}

$ttscript = '
	Meedya.items = '.json_encode($filelist).';
	var imgerror = "'.Text::_('COM_MEEDYA_SS_IMGERROR').'";
	var viderror = "COULD NOT PLAY VIDEO";
	';
if ($this->useFanCB) {
	$ttscript .= '
	Meedya.initIV();
	';
} else {
	$ttscript .= '
	ssCtl.baseUrl = "'.JUri::root(true).'/'.$this->gallpath.'/med/";
	ssCtl.baseUrlV = "'.JUri::root(true).'/'.$this->gallpath.'/img/";
	ssCtl._imgP = "components/com_meedya/static/img/";
	ssCtl.repeat = true;
	Meedya.initIV(true);
	';
}

$this->jDoc->addScriptDeclaration($ttscript);

///<form>
///<div class="display-limit">
///	<?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?&#160;
///	<?php echo $this->pagination->getLimitBox(); ?
///</div>
///</form>

//var_dump($bx);
//echo'<xmp>';var_dump($this->state);echo'</xmp>';
?>
<style>
.tooltip.in {
	opacity: 1;
	filter: alpha(opacity=100);
}
.tooltip-inner {
	color: #000;
	background-color: #FF0;
	white-space: normal;
}
.tooltip.top .tooltip-arrow {
	border-top-color: #EA9;
}
.tooltip.right .tooltip-arrow {
	border-right-color: #EA9;
}
.tooltip.left .tooltip-arrow {
	border-left-color: #EA9;
}
.tooltip.bottom .tooltip-arrow {
	border-bottom-color: #EA9;
}
#albdesc {
	font-size: large;
	padding: 4px;
	clear: both;
}
#area {
	clear: both;
	display: flex;
	flex-wrap: wrap;
}
.anitem img {
	width: 120px;
	height: 120px;
}
.falbum img {
	width: 120px;
	height: 120px;
}
.falbum img {
	width: 94px;
	height: 94px;
	background: #fff;
	margin-right: 15px;
	box-shadow:
		/* The top layer shadow */
		0 0 1px rgba(0,0,0,0.65),
		/* The second layer */
		5px 5px 0 0 #f4f4f4,
		/* The second layer shadow */
		5px 5px 1px 0 rgba(0,0,0,0.45),
		 /* The third layer */
		10px 10px 0 0 #f4f4f4,
		/* The third layer shadow */
		10px 10px 1px 0 rgba(0,0,0,0.15);
	/* Padding for demo purposes */
	padding: 8px;
}
.falbum a {
	width: 120px;
	height: 120px;
}
.itm-alb-ttl {
	position: absolute;
	min-width: 120px;
	top: 50px;
	text-align: center;
	background-color: rgba(255,255,255,0.7);
}
.itm-alb-ttl:hover {
	background-color: #FFF;
}
.slideback {
	position: fixed;
	background-color: rgba(0, 0, 0, 0.9);
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	padding: 8px;
	box-sizing: border-box;
	z-index: 9999;
}
#iarea {
	width: 100%;
	height: 100%;
}
.fancybox-progress {
	background: rgb(91 192 222 / 60%);
	height: 4px;
}
</style>
<div class="meedya-gallery">
<?php echo HTMLHelper::_('meedya.pageHeader', $this->params); ?>
<?php if (!$this->isSearch) echo HTMLHelper::_('meedya.searchField', $this->aid); ?>
	<div class="crumbs">
	<?php
		foreach ($this->pathWay as $crm) {
			echo '<span class="crumb"><a href="'.$crm->link.'">'.$crm->name.'</a></span> &gt; ';
		}
		echo '<span class="albttl">'.$this->title.'</span>';
	?>
	<?php if ($this->useFanCB && !$this->isSearch && count($this->items)>1): ?>
		<a href="#" title="<?=Text::_('COM_MEEDYA_SLIDESHOW')?>" onclick="Meedya.viewer.slideShow(event);return false">
			<img src="components/com_meedya/static/img/slideshow.png" alt="" />
		</a>
	<?php elseif (!$this->isSearch && count($this->items)>1): ?>
		<a href="<?=Route::_('index.php?option=com_meedya&view=slides&tmpl=component&aid='.$this->aid.'&Itemid='.$this->itemId, false) ?>" title="<?=Text::_('COM_MEEDYA_SLIDESHOW')?>">
			<img src="components/com_meedya/static/img/slideshow.png" alt="" />
		</a>
	<?php endif; ?>
	</div>
	<div id="albdesc"><?php echo $this->desc; ?></div>
	<div id="area">
	<?php if ($this->state->get('list.start'.$this->state->get('album.id')) == 0): ?>
	<?php foreach ($this->albums as $alb): ?>
		<div class="anitem falbum">
			<a href="<?=Route::_('index.php?option=com_meedya'.$alb->link.'&Itemid='.$this->itemId, false) ?>" class="itm-thumb">
				<div><img src="<?=$this->getAlbumThumb($alb)?>" class="falbumi" /></div>
				<div class="itm-alb-ttl"><?=$alb->title?></div>
			</a>
		</div>
	<?php endforeach; ?>
	<?php endif; ?>
	<?php
	$itemImg = new HtmlElementObject('img');
	$itemImgD = new HtmlElementObject('div', null, $itemImg);
	$itemImgD->setAttr(['data-toggle'=>'tooltip', 'data-placement'=>'bottom']);
	$itemZoom = new HtmlElementObject('div', null, $itemImgD);
	$itemZoom->setAttr('class','itm-thumb');
	$itemDiv = new HtmlElementObject('div', null, $itemZoom);
	$itemDiv->setAttr('class','anitem');
//	$itemDiv->setAttr('onclick','alert(\'YYYYYY\')');
//	$itemInf = new HtmlElementObject('div');
//	$itemInf->setAttr('class','iteminf');
//	$itemDiv->addCont($itemInf);

	foreach ($this->items as $ix=>$item) {
		if (!$item) continue;
		list($thumb, $ititle, $idesc, $mtype) = $this->getItemThumbPlus($item['id']);
		$ttip = ($ititle && $idesc) ? $ititle.'<br />'.$idesc : $ititle.$idesc;
		switch (strstr($mtype, '/', true)) {
			case 'video':
				$thmsrc = 'video.png';
				break;
			case 'audio':
				$thmsrc = 'audio.png';
				break;
			default:
				$thmsrc = 'img.png" data-echo="thm/'.$thumb;
		}
		$itemImg->setAttr('src', 'components/com_meedya/static/img/'.$thmsrc);
		$itemImgD->setAttr('title', $ttip);
		$itemZoom->setAttr('onclick', 'Meedya.viewer.showSlide(event,'.$ix.')');
		echo $itemDiv->render();
	}
	?>
	<!-- <div id="itmend" class="noitem"></div> -->
	</div>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>

<script>
	echo.init({
		baseUrl: "<?=JUri::root(true).'/'.$this->gallpath?>/",
		offset: 200,
		throttle: 250,
		debounce: false
	});
</script>
<?php if (!$this->useFanCB): ?>
<div id="sstage" class="slideback" style="display:none">
	<div id="iarea" tabindex="0" onclick="ssCtl.doMnu(0);">
		<div id="ptext"></div>
		<p id="loading" style="display:none">∙∙∙LOADING∙∙∙</p>
	</div>
</div>
<?php endif; ?>

