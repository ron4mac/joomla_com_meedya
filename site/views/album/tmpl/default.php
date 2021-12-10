<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/* ========== NOTICE! THIS TEMPLATE IS REUSED BY SEARCH DISPLAY AND PUBLIC ALBUM DISPLAY ========== */

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;

HTMLHelper::_('jquery.framework');
//HTMLHelper::_('bootstrap.modal');
MeedyaHelper::addStyle('album');
MeedyaHelper::addScript('meedya');
if ($this->useFanCB) {
	$this->jDoc->addStyleSheet('https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css');
	$this->jDoc->addScript('https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js');
} else {
	MeedyaHelper::addStyle('each');
	MeedyaHelper::addScript('vuesld');
}
MeedyaHelper::addScript('bootbox');
MeedyaHelper::addScript('rating');

$jslang = [
		'no_sterm' => Text::_('COM_MEEDYA_MSG_STERM'),
		'ru_sure' => Text::_('COM_USERNOTES_RU_SURE'),
		'rate_item' => Text::_('COM_MEEDYA_RATE_ITEM')
	];
$this->jDoc->addScriptDeclaration('Meedya.L = '.json_encode($jslang).';
');

HTMLHelper::_('bootstrap.tooltip', '.hasTip', ['fixed'=>true]);

$filelist = [];
if ($this->items) {		//var_dump($this->items);
	foreach ($this->items as $file) {
		if (!$file) continue;
		//$ftyp = cpg_get_type($row['filename']);
		//if ($ftyp['content'] != 'image') continue;
		$txtinfo = '';
		$txtinfo .= trim($file['title']);
		$desc = trim($file['desc']);
		$txtinfo .= (($txtinfo && $desc) ? ' ... ' : '') . $desc;
if ($this->useFanCB) {
		$mTyp = substr($file['mtype'], 0, 5);
		$murl = JUri::root(true).'/'.$this->gallpath.($mTyp=='image' ? '/med/' : '/img/').$file['file'];
		$fileentry = [
			'src' => $murl,
			'opts' => ['caption' => $txtinfo],
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

$pgidparm = isset($this->pgid) ? '&pgid='.$this->pgid : '';

$ttscript = '
	Meedya.rawURL = "'.Route::_('index.php?option=com_meedya&format=raw'.$pgidparm.'&Itemid='.$this->itemId, false).'";
	Meedya.formTokn = "'.Session::getFormToken().'";
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
//echo'<xmp>';var_dump($this->params);echo'</xmp>';

$use_ratings = $this->params->get('use_ratings');
$use_comments = $this->params->get('use_comments');
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
	width: 160px;
	height: 160px;
}
.falbum img {
	width: 160px;
	height: 160px;
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
	width: 160px;
	height: 160px;
}
.itm-alb-ttl {
	position: absolute;
	min-width: 160px;
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
.fancybox-caption__body {
	font-size: larger;
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
	$ttmpl = '
<div class="anitem" data-ix="{{IX}}" data-iid="{{IID}}">
	<div class="itm-thumb">
		<div data-toggle="tooltip" data-placement="bottom" title="{{TITLE}}">
			<img src="{{SRC}}">';
	if ($use_ratings || $use_comments) {
		$ttmpl .= '
			<div class="starcmnt">';
		if ($use_ratings) $ttmpl .= '
				<div class="strate"><div class="strback"><div class="strating" style="width:{{PCNT}}%"></div></div></div>';
		if ($use_comments) $ttmpl .= '
				<span class="mycmnts{{CCLAS}}">'.HTMLHelper::_('meedya.cmntsIcon').' {{CCNT}}</span>';
		$ttmpl .= '
			</div>
';
	}
	$ttmpl .= '
		</div>
	</div>
</div>
';
	$rplcds = ['{{IX}}','{{IID}}','{{TITLE}}','{{SRC}}','{{PCNT}}','{{CCLAS}}','{{CCNT}}'];
	$do_stars = $this->params->get('use_ratings');
	$do_cmnts = $this->params->get('use_comments');

	foreach ($this->items as $ix=>$item) {
		if (!$item) continue;
		$rplvals = [];
		$rplvals[] = $ix;
		$rplvals[] = $item['id'];
		list($thumb, $ititle, $idesc, $mtype) = $this->getItemThumbPlus($item['id']);
		$rplvals[] = ($ititle && $idesc) ? $ititle.'<br />'.$idesc : $ititle.$idesc;
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
		$rplvals[] = 'components/com_meedya/static/img/'.$thmsrc;
	//	if ($do_stars || $do_cmnts) $itemImgD->setFoot(HTMLHelper::_('meedya.starcmnt', $item, $do_stars, $do_cmnts));
		$rplvals[] = $item['ratecnt'] ? $item['ratetot']/$item['ratecnt']*20 : 0;
		$rplvals[] = $item['cmntcnt'] ? ' hasem' : '';
		$rplvals[] = $item['cmntcnt'] ?: '&nbsp;';
		echo str_replace($rplcds, $rplvals, $ttmpl);
	}
	?>
	<!-- <div id="itmend" class="noitem"></div> -->
	</div>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
<?php
if ($this->uid) {
	include_once JPATH_COMPONENT . '/layouts/rating.php';
	include_once JPATH_COMPONENT . '/layouts/comments.php';
	include_once JPATH_COMPONENT . '/layouts/comment.php';
}
?>
<script>
	echo.init({
		baseUrl: "<?=JUri::root(true).'/'.$this->gallpath?>/",
		offset: 200,
		throttle: 250,
		debounce: false
	});
<?php if ($this->uid): ?>
	jQuery(".strate").on("click", function(e){
		e.stopPropagation();
		var iid = this.parentElement.parentElement.parentElement.parentElement.dataset.iid;
		Meedya.dorate(iid, this);
	});
	jQuery(".mycmnts").on("click", function(e){
		e.stopPropagation();
		var iid = this.parentElement.parentElement.parentElement.parentElement.dataset.iid;
		Meedya.doComments(iid, this);
	});
<?php endif; ?>
	jQuery(".itm-thumb").on("click", function(e){
		e.stopPropagation();
		Meedya.viewer.showSlide(e,jQuery(this).parent()[0].dataset.ix);
	});
	document.querySelector("#comments-modal .modal-dialog").classList.add('modal-dialog-scrollable');;
</script>
<?php if (!$this->useFanCB): ?>
<div id="sstage" class="slideback" style="display:none">
	<div id="iarea" tabindex="0" onclick="ssCtl.doMnu(0);">
		<div id="ptext"></div>
		<p id="loading" style="display:none">∙∙∙LOADING∙∙∙</p>
	</div>
</div>
<?php endif; ?>

