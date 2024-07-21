<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.4
*/
defined('_JEXEC') or die;

/* ========== NOTICE! THIS TEMPLATE IS REUSED BY SEARCH DISPLAY AND PUBLIC ALBUM DISPLAY ========== */

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

define('MYG_FB4', 1);
$ttscript = 'Meedya.albumID = '.$this->aid.';';

HTMLHelper::_('jquery.framework');
//MeedyaHelper::addStyle('album');
$styles = 'a';
//MeedyaHelper::addScript(['common','meedya','rating','echo']);
$scripts = 'mre';
if (defined('MYG_FB4')) {
	$styles .= 'F';
//	MeedyaHelper::addStyle('fancybox', 'vendor/fancybox/4.0.27/');
	$scripts .= 'F';
//	MeedyaHelper::addScript('fancybox.umd', 'vendor/fancybox/4.0.27/');
	$ttscript .= '
	Fancybox.defaults.infinite = 0;
	Fancybox.defaults.showClass = false;
	Fancybox.defaults.hideClass = false;
	Fancybox.defaults.autoFocus = false;
	Fancybox.Plugins.Thumbs.defaults.autoStart = false;
	Fancybox.Plugins.Toolbar.defaults.display = ["zoom","slideshow","fullscreen","download","close"];
//	Carousel.defaults.friction = 0.75;';
} else {
	$styles .= 'f';		//MeedyaHelper::addStyle('fancybox3','vendor/fancybox/');
	$scripts .= 'f';		//MeedyaHelper::addScript('fancybox3', 'vendor/fancybox/');
}
MeedyaHelper::oneStyle($styles);
//HTMLHelper::_('behavior.core');		// must force 'core' to load before 'meedya' on joomla 3.x
MeedyaHelper::oneScript($scripts);

$jslang = [
		'no_sterm' => Text::_('COM_MEEDYA_MSG_STERM'),
		'ru_sure' => Text::_('COM_USERNOTES_RU_SURE'),
		'rate_item' => Text::_('COM_MEEDYA_RATE_ITEM')
	];
$this->jDoc->addScriptDeclaration('Meedya.L = '.json_encode($jslang).';
');

//HTMLHelper::_('bootstrap.tooltip', '.hasTip', ['fixed'=>true]);

$filelist = [];
if ($this->items) {		//var_dump($this->items);
	foreach ($this->items as $file) {
		if (!$file) continue;
		//$ftyp = cpg_get_type($row['filename']);
		//if ($ftyp['content'] != 'image') continue;
		$txtinfo = '';
		$txtinfo .= trim($file['title']);
		$desc = trim($file['desc'] ?: '');
		$txtinfo .= (($txtinfo && $desc) ? ' ... ' : '') . $desc;
		$mTyp = substr($file['mtype'], 0, 5);
		if (defined('MYG_FB4') && $mTyp=='video') $mTyp = 'html5video';
		$murl = JUri::root(true).'/'.$this->gallpath.($mTyp=='image' ? '/med/' : '/img/').$file['file'];
		$fileentry = [
			'src' => $murl,
			'type' => $mTyp
			];
		if (defined('MYG_FB4') && $txtinfo) {
			$fileentry['caption'] = $txtinfo;
		} else {
			$fileentry['opts'] = ['caption' => $txtinfo];
		}
		$filelist[] = $fileentry;
	}
}

$ttscript .= '
	Meedya.pflURL = "'.Route::_('index.php?option=com_meedya&aid='.$this->aid.'&task=picframe&format=raw&I='.$this->instance.'&Itemid='.$this->itemId, false, 0, true).'";
	Meedya.datatog = "'.M34C::bs('toggle').'";
	Meedya.FB4 = '.(defined('MYG_FB4')?1:0).';
	Meedya.initIV();
	Meedya.items = '.json_encode($filelist).';
	';

$this->jDoc->addScriptDeclaration($ttscript);

///<form>
///<div class="display-limit">
///	<?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?&#160;
///	<?php echo $this->pagination->getLimitBox(); ?
///</div>
///</form>

$use_ratings = $this->params->get('use_ratings', 0);
$use_comments = $this->params->get('use_comments', 0);
$pub_ratings = $this->params->get('pub_ratings', 0);
$cancmnt = $this->uid || $this->params->get('pub_comments', 0);
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
	width: 150px;
	height: 150px;
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
	width: 168px;
	height: 168px;
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
.fancybox__carousel .fancybox__slide.has-html5video .fancybox__content {
	width: 100%;
	height: 100%;
}
.fancybox__container .fancybox__content {
	/* Create white border around the image */
	box-sizing: content-box;
	padding: .3em;
	background: #AEAEAE;
	
	border-radius: 5px;
	color: #374151;
	box-shadow: 0 8px 23px rgb(0 0 0 / 50%);
}

</style>
<div class="meedya-gallery">
<?php echo HtmlMeedya::pageHeader($this->params); ?>
<?php if (!$this->isSearch) echo HtmlMeedya::searchField($this->aid); ?>
	<div class="crumbs">
	<?php
		foreach ($this->pathWay as $crm) {
			echo '<span class="crumb"><a href="'.$crm->link.'">'.$crm->name.'</a></span> &gt; ';
		}
		echo '<span class="albttl">'.$this->title.'</span>';
	?>
	<?php if (false && !$this->isSearch && count($this->items)>1): ?>
		<a href="#" title="<?=Text::_('COM_MEEDYA_SLIDESHOW')?>" onclick="Meedya.viewer.slideShow(event);return false">
			<img src="components/com_meedya/static/img/slideshow.png" alt="" /></a>
	<?php elseif (!$this->isSearch && count($this->items)>1): ?>
		<a href="<?=Route::_('index.php?option=com_meedya&view=slides&tmpl=component&aid='.$this->aid.'&Itemid='.$this->itemId, false) ?>" title="<?=Text::_('COM_MEEDYA_SLIDESHOW')?>">
			<img src="components/com_meedya/static/img/slideshow.png" alt="" /></a>
	<?php endif; ?>
	<?php if ($this->params->get('picframe', 0) && $this->userPerms->canAdmin): ?>
		<a href="http://picframe.local/static/cgetnpl.html?nplt=<?=$this->title?>&nplk=<?=$this->picframekey()?>" title="<?=Text::_('COM_MEEDYA_PICFRAME')?>" onclick="Meedya.doNotPicframe()">
			<img src="components/com_meedya/static/img/picframe.png" alt="" /></a>
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
		<div class="{{TCLAS}}" title="{{TITLE}}">
			<img class="imgthm" src="{{SRC}}">';
	if ($use_ratings || $use_comments) {
		$ttmpl .= '
			<div class="starcmnt">';
		if ($use_ratings) $ttmpl .= '
				<div class="strate"><div class="strback"><div class="strating" style="width:{{PCNT}}%"></div></div></div>';
		if ($use_comments) $ttmpl .= '
				<span class="mycmnts{{CCLAS}}">'.HtmlMeedya::cmntsIcon().' {{CCNT}}</span>';
		$ttmpl .= '
			</div>
';
	}
	$ttmpl .= '
		</div>
	</div>
</div>
';

	$rplcds = ['{{IX}}','{{IID}}','{{TITLE}}','{{SRC}}','{{PCNT}}','{{CCLAS}}','{{CCNT}}','{{TCLAS}}'];

	foreach ($this->items as $ix=>$item) {
		if (!$item) continue;
		$rplvals = [];
		$rplvals[] = $ix;
		$rplvals[] = $item['id'];
		list($thumb, $ititle, $idesc, $mtype) = $this->getItemThumbPlus($item['id']);
		$ttd = ($ititle && $idesc) ? $ititle.'<br />'.$idesc : $ititle.$idesc;
		$rplvals[] = $ttd;
		switch (strstr($mtype, '/', true)) {
			case 'video':
				$thmsrc = 'video.png';
				if (substr($thumb, -5)=='.jpeg') {
					$thmsrc = 'img.png" data-echo="thm/'.$thumb;
				}
				break;
			case 'audio':
				$thmsrc = 'audio.png';
				break;
			default:
				$thmsrc = 'img.png" data-echo="thm/'.$thumb;
		}
		$rplvals[] = 'components/com_meedya/static/img/'.$thmsrc;
	//	if ($parray['use_ratings'] || $parray['use_comments']) $itemImgD->setFoot(HtmlMeedya::starcmnt($item, $parray['use_ratings'], $parray['use_comments']));
		$rplvals[] = $item['ratecnt'] ? $item['ratetot']/$item['ratecnt']*20 : 0;
		$rplvals[] = $item['cmntcnt'] ? ' hasem' : ($cancmnt ? '' : 'no');
		$rplvals[] = $item['cmntcnt'] ?: '&nbsp;';
		$rplvals[] = $ttd ? 'hastip' : 'notip';
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
if ($use_comments) {
	echo LayoutHelper::render('comments', ['cancmnt'=>$cancmnt]);
	if ($cancmnt) {
		echo LayoutHelper::render('comment');
	}
}
if ($use_ratings && ($this->uid || $pub_ratings)) {
	echo LayoutHelper::render('rating');
}
if ($this->userPerms->canAdmin) {
	echo LayoutHelper::render('picframe', ['albttl'=>$this->title]);
}
?>
<script>
	echo.init({
		baseUrl: "<?=JUri::root(true).'/'.$this->gallpath?>/",
		offset: 200,
		throttle: 250,
		debounce: false
	});
	document.getElementById('area').addEventListener('click', Meedya.thmClick, true);
//	document.querySelector("#comments-modal .modal-dialog").classList.add('modal-dialog-scrollable');;
</script>
