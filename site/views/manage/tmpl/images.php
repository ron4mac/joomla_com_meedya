<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Layout\LayoutHelper;

//HTMLHelper::_('bootstrap.framework');
//HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');
//HTMLHelper::_('bootstrap.tooltip');

MeedyaHelper::addStyle('gallery');
MeedyaHelper::addStyle('manage');
MeedyaHelper::addStyle('jquery.tagsinput', 'vendor/tags/');
MeedyaHelper::addScript('manage');
MeedyaHelper::addScript('bootbox');
MeedyaHelper::addScript('jquery.tagsinput', 'vendor/tags/');

Text::script('COM_MEEDYA_PERM_DELETE');
Text::script('JCANCEL');
Text::script('JACTION_DELETE');

function dateF ($dt)
{
	if (!$dt) return '';
	return date('M j, Y, g:i a', strtotime($dt));
}
?>
<script>
function editImg (iid) {
	window.location = "<?=Route::_('index.php?option=com_meedya&task=manage.imgEdit&Itemid='.$this->itemId)?>&items="+iid;
}
</script>

<style>
.btn-group {display:inline-block;}
.ordering-select {display:inline-flex; margin-left:8px;}
.js-stools {margin-bottom:0.5rem}
.js-stools-container-filters-visible {display:inline-flex;padding:10px 0;}
.js-stools-container-bar {display:inline-flex; padding:0;}
.btn-wrapper.input-append {display:inline-flex;}
.js-stools-container-list {display:inline-flex;}
.mitem, .litem {width:120px; height:120px;}
.mitem {/*border:1px dashed transparent;*/}
.litem {cursor:pointer;}
.islct {border-color:blue;}
/* icons and buttons */
.actbuts {display:inline-block;}
.action-icon {
	font-size: larger;
	margin-right: 0.5em;
}
.action-icon.acticon {
	/*color: rgba(51,51,51,1);*/
}
.action-icon.inaicon {
	/*color: rgba(51,51,51,0.5);*/
	opacity: 0.3;
/*	cursor: pointer;*/
}
.modal-backdrop.fade.in {opacity:0.4}
/*div.modal.bootbox-confirm {left: 50%; width: 400px; margin-left: -200px;}*/
.bootbox-body {padding: 12px; font-size: larger;}
.modal-footer {padding: 8px 10px}
#filter_tag{margin-bottom:0}
</style>

<div class="meedya-gallery">
	<?php if ($this->manage) echo HTMLHelper::_('meedya.manageMenu', $this->userPerms, 0, $this->itemId); ?>
	<?php echo HTMLHelper::_('meedya.pageHeader', $this->params, $this->action/*.'XXXX'*/); ?>
	<form action="<?=Route::_('index.php?option=com_meedya&view=manage&Itemid='.$this->itemId)?>" method="post" name="adminForm" id="adminForm">
		<?php $fOpts = ['filterButton' => true]; ?>
		<?php echo LayoutHelper::render('search', ['view' => $this, 'options' => $fOpts], JPATH_ROOT.'/components/com_meedya/layouts'); ?>
		<?php if ($this->iids): ?>
		<?php
			if ($this->mode == 'L') {
				echo '<a href="'.$this->linkUrl.'&mode=G"><span class="icon-grid-2 action-icon inaicon" title="Grid View"> </span></a>';
				echo '<span class="icon-list-2 action-icon acticon" title="List View"> </span>';
			} else {
				echo '<span class="icon-grid-2 action-icon acticon" title="Grid View"> </span>';
				echo '<a href="'.$this->linkUrl.'&mode=L"><span class="icon-list-2 action-icon inaicon" title="List View"> </span></a>';
			}
		?>
		<div class="actbuts">
			<?php echo HTMLHelper::_('meedya.actionButtons', ['sela','seln','edts','adds','dels']); ?>
		</div>
		<?php endif; ?>
		<?php echo $this->loadTemplate($this->mode == 'G' ? 'grid' : 'list'); ?>
		<input type="hidden" name="task" value="manage.editImgs" />
		<input type="hidden" name="albumid" value="" />
		<input type="hidden" name="nualbnam" value="" />
		<input type="hidden" name="nualbpar" value="" />
		<input type="hidden" name="nualbdesc" value="" />
		<input type="hidden" name="mode" value="<?=$this->mode?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>

<?php
echo HTMLHelper::_(
	'bootstrap.renderModal',
	'add2albdlg',
	[
		'title' => Text::_('COM_MEEDYA_ADD_ALBUM_ITEMS'),
		'footer' => HTMLHelper::_('meedya.modalButtons', 'COM_MEEDYA_ADD2ALBUM', 'Meedya.addItems2Album(this)', 'creab'),
		'modalWidth' => '40'
	],
	$this->loadTemplate('add2alb')
	);
?>

<script>
jQuery('#system-message-container').delay(5000).slideUp("slow");
echo.init({
	baseUrl: "<?=JUri::root(true).'/'.$this->gallpath?>/",
	offset: 200,
	throttle: 250,
	debounce: false
});
</script>
