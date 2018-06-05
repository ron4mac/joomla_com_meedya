<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2018 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

MeedyaHelper::addStyle('album');
MeedyaHelper::addStyle('basicLightbox', 'vendor/blb/');
MeedyaHelper::addStyle('manage');

JHtml::_('bootstrap.tooltip','.hasTip',array('fixed'=>true));
//$jdoc->addScript('components/com_meedya/static/js/echo.min.js');

$ttscript = '
	jQuery(document).ready(function() {
		jQuery(\'[data-toggle="tooltip"]\').tooltip();
	});
';

$jdoc = JFactory::getDocument();
$jdoc->addScriptDeclaration($ttscript);

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
	opacity: 0.85;
	filter: alpha(opacity=85);
}
.tooltip-inner {
	color: #000;
	background-color: #EA9;
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
#area {
	display: flex;
	flex-wrap: wrap;
}
.anitem img {
	width: 120px;
	height: 120px;
}
.falbum img {
	width: 100px;
	height: 108px;
}
.itm-alb-ttl {
	position: relative;
	min-width: 120px;
	top: 120px;
	text-align: center;
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
		<a href="<?=JRoute::_('index.php?option=com_meedya&view=slides&tmpl=component&aid='.$this->aid, false) ?>" title="<?=JText::_('COM_MEEDYA_SLIDESHOW')?>">
			<img src="components/com_meedya/static/img/slideshow.png" alt="" />
		</a>
	<?php endif; ?>
	</div>
	<div id="area">
	<?php if($this->state->get('list.start'.$this->state->get('album.id')) == 0): ?>
	<?php foreach ($this->albums as $alb): ?>
		<div class="anitem falbum">
			<a href="<?=JRoute::_('index.php?option=com_meedya&view=album&aid='.$alb->aid, false) ?>" class="itm-thumb">
				<div><img src="<?=$this->getAlbumThumb($alb)?>" class="falbumi" /></div>
				<div class="itm-alb-ttl"><?=$alb->title?></div>
			</a>
		</div>
	<?php endforeach; ?>
	<?php endif; ?>
	<?php
		foreach ($this->items as $item) {
			if (!$item) continue;
			list($thumb, $ititle, $idesc, $mtype) = $this->getItemThumbPlus($item);
			$ttip = ($ititle && $idesc) ? $ititle.'<br />'.$idesc : $ititle.$idesc;
			switch (strstr($mtype, '/', true)) {
				case 'video':
					$thmsrc = 'components/com_meedya/static/img/video.png';
					break;
				default:
					$thmsrc = 'components/com_meedya/static/img/img.png" data-echo="thm/'.$thumb;
			}
			echo '<div class="anitem">'
			//	.'<a href="'.JRoute::_('index.php?option=com_meedya&view=item&iid='.$item, false).'" class="itm-thumb">'
				.'<a href="'.JRoute::_('index.php?option=com_meedya&view=album&layout=each&aid='.$this->aid.'&iid='.$item, false).'" class="itm-thumb">'
					.'<div data-toggle="tooltip" data-placement="bottom" title="'.$ttip.'"><img src="'.$thmsrc.'" /></div>'
					.'<div class="itm-thm-ttl" data-src="'.$thumb.'">'./*$item*/$ititle.'</div>'
				.'</a>'
			.'</div>';
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