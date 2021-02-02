<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2020 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

MeedyaHelper::addStyle('album');
MeedyaHelper::addStyle('basicLightbox', 'vendor/blb/');
MeedyaHelper::addStyle('manage');
MeedyaHelper::addStyle('each');
MeedyaHelper::addScript('vuesld');

JHtml::_('bootstrap.tooltip', '.hasTip', array('fixed'=>true));

if ($this->files) {
	foreach ($this->files as $file) {
		//$ftyp = cpg_get_type($row['filename']);
		//if ($ftyp['content'] != 'image') continue;
		$txtinfo = '';
		$txtinfo .= trim($file['title']);
		$txtinfo .= ($txtinfo ? ' ... ' : '') . trim($file['desc']);
		$fileentry = array(
				'fpath' => $file['file'],
				'title' => $txtinfo,
				'mTyp' => substr($file['mtype'], 0, 1)
				);
		$filelist[] = $fileentry;
	}
}

$ttscript = '
	var baseUrl = "'.JUri::root(true).'/'.$this->gallpath.'/med/";
	var imgerror = "'.JText::_('COM_MEEDYA_SS_IMGERROR').'";
	var imagelist = '.json_encode($filelist).';
	var startx = '.$this->six.';
	var _imgP = "components/com_meedya/static/img/";
	ssCtl.repeat = true;
	jQuery(document).ready(function() {
		jQuery(\'[data-toggle="tooltip"]\').tooltip();
	});
	function showSlides (e, iid) {
		e.preventDefault();
		startx = iid;
	//	jQuery(\'<div class="slideback"></div>\').appendTo(\'body\');
		jQuery(\'#sstage\').appendTo(\'body\').show();
		ssCtl.init();
	}
';

$this->jDoc->addScriptDeclaration($ttscript);

///<form>
///<div class="display-limit">
///	<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?&#160;
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
}
#area {
	display: flex;
	flex-wrap: wrap;
}
.anitem img {
	width: 120px;
	height: 120px;
}
.falbum img {
	width: 104px;
	height: 104px;
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
.itm-alb-ttl {
	position: relative;
	min-width: 120px;
	top: 120px;
	text-align: center;
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
</style>
<?php echo JHtml::_('meedya.pageHeader', $this->params); ?>
<div class="meedya-gallery">
	<div class="crumbs">
	<?php
		foreach ($this->pathWay as $crm) {
			echo '<span class="crumb"><a href="'.$crm->link.'">'.$crm->name.'</a></span> &gt; ';
		}
		echo '<span class="albttl">'.$this->title.'</span>';
	?>
	<?php if (count($this->items)>1): ?>
		<a href="<?=Route::_('index.php?option=com_meedya&view=slides&tmpl=component&aid='.$this->aid.'&Itemid='.$this->itemId, false) ?>" title="<?=JText::_('COM_MEEDYA_SLIDESHOW')?>">
			<img src="components/com_meedya/static/img/slideshow.png" alt="" />
		</a>
	<?php endif; ?>
	</div>
	<div id="albdesc"><?php echo $this->desc; ?></div>
	<div id="area">
	<?php if ($this->state->get('list.start'.$this->state->get('album.id')) == 0): ?>
	<?php foreach ($this->albums as $alb): ?>
		<div class="anitem falbum">
			<a href="<?=Route::_('index.php?option=com_meedya&view=album&aid='.$alb->aid.'&Itemid='.$this->itemId, false) ?>" class="itm-thumb">
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
	$itemLnk = new HtmlElementObject('a', null, $itemImgD);
	$itemLnk->setAttr('class','itm-thumb');
	$itemDiv = new HtmlElementObject('div', null, $itemLnk);
	$itemDiv->setAttr('class','anitem');

	foreach ($this->items as $ix=>$item) {
		if (!$item) continue;
		list($thumb, $ititle, $idesc, $mtype) = $this->getItemThumbPlus($item);
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
		$itemLnk->setAttr('href', Route::_('index.php?option=com_meedya&view=album&layout=each&aid='.$this->aid.'&iid='.$item.'&Itemid='.$this->itemId, false));
		$itemLnk->setAttr('onclick', 'showSlides(event,'.$ix.')');
		echo $itemDiv->render();
	}
	?>
	<!-- <div id="itmend" class="noitem"></div> -->
	</div>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>

<script src="components/com_meedya/static/vendor/blb/basicLightbox.min.js"></script>
<script>
	var blb_path = "<?=JUri::root(true).'/'.$this->gallpath?>/med/";
	document.querySelectorAll('.itm-thm-ttl').forEach(function(elem) {
		elem.onclick = function(e) {
			const src = blb_path + elem.getAttribute('data-src');
			const html = '<img src="' + src + '">';
			basicLightbox.create(html).show();
			return false;
		}
	});

	//initArrange();
//	jquvb = "<?=JUri::root(true).'/'.$this->gallpath?>/";
//	$("#area img").unveil();
	echo.init({
		baseUrl: "<?=JUri::root(true).'/'.$this->gallpath?>/",
		offset: 200,
		throttle: 250,
		debounce: false
	});
</script>
<div id="sstage" class="slideback" style="display:none">
	<div id="iarea" tabindex="0" onclick="ssCtl.doMnu(0);">
		<div id="ptext"></div>
		<p id="loading" style="display:none">∙∙∙LOADING∙∙∙</p>
	</div>
</div>
