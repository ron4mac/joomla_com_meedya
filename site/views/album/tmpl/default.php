<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2017 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::_('bootstrap.tooltip','.hasTip',array('fixed'=>true));
$jdoc = JFactory::getDocument();
//$jdoc->addScript('components/com_meedya/static/js/echo.min.js');

$ttscript = '
	jQuery(document).ready(function() {
		jQuery(\'[data-toggle="tooltip"]\').tooltip();
	});
';

$jdoc->addScriptDeclaration($ttscript);
JHtml::stylesheet('components/com_meedya/static/css/album.css');
JHtml::stylesheet('components/com_meedya/static/vendor/blb/basicLightbox.min.css');

///<form>
///<div class="display-limit">
///	<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?&#160;
///	<?php echo $this->pagination->getLimitBox(); ?
///</div>
///</form>

//var_dump($this->albums);
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
</style>
<div class="meedya-gallery">
	<h3>
		<?php if ($this->params->def('show_page_heading', 1)) echo $this->escape($this->title); ?>
		<a href="<?=JRoute::_('index.php?option=com_meedya&view=slides&tmpl=component&aid='.$this->aid) ?>" title="<?=JText::_('COM_MEEDYA_SLIDESHOW')?>">
			<img src="components/com_meedya/static/img/slideshow.png" alt="" />
		</a>
	</h3>
<div id="area">
<?php foreach ($this->albums as $alb): ?>
	<div class="anitem falbum">
		<a href="<?=JRoute::_('index.php?option=com_meedya&view=album&aid='.$alb->aid) ?>">
			<div><img src="<?=$this->getAlbumThumb($alb)?>" class="falbumi" /></div>
		</a>
	</div><?php endforeach; ?>
<?php
	foreach ($this->items as $item) {
		if (!$item) continue;
		//$thumb = $this->getItemThumb($item);
		list($thumb, $ititle, $idesc) = $this->getItemThumbPlus($item);
		$ttip = ($ititle && $idesc) ? $ititle.'<br />'.$idesc : $ititle.$idesc;
		echo '<div class="anitem">'
		//	.'<a href="'.JRoute::_('index.php?option=com_meedya&view=item&iid='.$item).'" class="itm-thumb">'
			.'<a href="'.JRoute::_('index.php?option=com_meedya&view=album&layout=each&aid='.$this->aid.'&iid='.$item).'" class="itm-thumb">'
				.'<div data-toggle="tooltip" data-placement="bottom" title="'.$ttip.'"><img src="" data-echo="thm/'.$thumb.'" /></div>'
				.'<div class="itm-thm-ttl" data-src="'.$thumb.'">'.$item.'</div>'
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
	echo.init({
		baseUrl: "<?=JUri::root(true).'/'.$this->gallpath?>/",
		offset: 100,
		throttle: 250,
		unload: false,
		callback: function (element, op) {
			console.log(element, 'has been', op + 'ed')
		}
	});
</script>