<?php
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('bootstrap.framework');
?>
<script>
var slctImgs = Array();
function slctImg (evt, elm) {	//console.log(evt);
	//var img = jQuery(elm).children()[0];
	//if (jQuery(img).hasClass('islct')) return true;
	//jQuery(img).addClass('islct');
	//return false;
	if (jQuery(elm).hasClass('islct')) {
		jQuery("#imgVue").prop('src', jQuery(elm).data('img'));
		jQuery("#imgModl").modal({keyboard:true});
	} else {
		if (!evt.metaKey) {
			for (var i = 0, len = slctImgs.length; i < len; i++) {
				jQuery(slctImgs[i]).removeClass('islct');
			}
			slctImgs = Array();
		}
	//	jQuery("#allimgs").children().removeClass('islct');
		slctImgs.push(elm);
		jQuery(elm).addClass('islct');
	}
	return false;
}
</script>
<style>
.mitem {border:1px dashed transparent;}
.islct {border-color:blue;}
.modal-body {max-height:initial;}
.modal-backdrop.fade.in {opacity:0.4;}
#imgMdl {max-height:90%;}
</style>
<div class="meedya-gallery">
	<?php if ($this->params->def('show_page_heading', 1)) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>
	<form action="index.php?option=com_meedya&amp;view=images&amp;Itemid=<?php echo $this->itemId; ?>" method="post" name="adminForm" id="adminForm">
		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
		<div id="allimgs">
		<?php
			foreach ($this->iids as $item) {
			//	echo '<a href="'.$this->gallpath.'/img/'.$item->file.'" onclick="return slctImg(event, this)"><img src="'.$this->gallpath.'/thm/'.$item->file.'" class="mitem" width="120" height="120" /></a>';
				echo '<img src="" data-echo="'.$this->gallpath.'/thm/'.$item->file
					.'" data-img="'.$this->gallpath.'/img/'.$item->file
					.'" class="mitem" width="120" height="120" onclick="return slctImg(event, this)" />';
			}
			//JUri::base().
		?>
		</div>
	</form>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
<?php
	$modal_params = array();
	$modal_params['title'] = 'Image:';
	$modal_body = '<img id="imgVue" src="" />';
	echo JHTML::_('bootstrap.renderModal', 'imgModl', $modal_params, $modal_body);
?>
<script>
	echo.init({
		offset: 200
	});
</script>