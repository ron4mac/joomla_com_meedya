<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2019 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::_('bootstrap.framework');
MeedyaHelper::addStyle('basicLightbox', 'vendor/blb/');
MeedyaHelper::addStyle('gallery');
MeedyaHelper::addStyle('manage');
MeedyaHelper::addScript('manage');
MeedyaHelper::addScript('basicLightbox', 'vendor/blb/');
MeedyaHelper::addScript('bootbox');

JText::script('COM_MEEDYA_PERM_DELETE');
JText::script('JCANCEL');
JText::script('JACTION_DELETE');

function dateF ($dt)
{
	if (!$dt) return '';
	return date('M j, Y, g:i a', strtotime($dt));
}
?>
<script>
function editImg (iid) {
	window.location = "<?=JRoute::_('index.php?option=com_meedya&task=manage.imgEdit&Itemid='.$this->itemId)?>&items="+iid;
}
var myBaseURL = "<?= JRoute::_('index.php?option=com_meedya&Itemid='.$this->itemId, false); ?>";
var formTokn = "<?= JSession::getFormToken(); ?>";
</script>

<style>
.mitem, .litem {width:120px; height:120px;}
.mitem {/*border:1px dashed transparent;*/}
.litem {cursor:pointer;}
.islct {border-color:blue;}
/* icons and buttons */
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
</style>

<div class="meedya-gallery">
	<?php if ($this->manage) echo JHtml::_('meedya.manageMenu', $this->userPerms, 0, $this->itemId); ?>
	<?php echo JHtml::_('meedya.pageHeader', $this->params, $this->action/*.'XXXX'*/); ?>
	<form action="<?=JRoute::_('index.php?option=com_meedya&view=manage&Itemid='.$this->itemId)?>" method="post" name="adminForm" id="adminForm">
		<?php
			if ($this->mode == 'G') {
				echo '<a href="'.$this->linkUrl.'&mode=L"><span class="icon-list-2 action-icon inaicon" title="List View"> </span></a>';
				echo '<span class="icon-grid-2 action-icon acticon" title="Grid View"> </span>';
			} else {
				echo '<span class="icon-list-2 action-icon acticon" title="List View"> </span>';
				echo '<a href="'.$this->linkUrl.'&mode=G"><span class="icon-grid-2 action-icon inaicon" title="Grid View"> </span></a>';
			}
		?>
		<?php $fOpts = array('filterButton' => true); ?>
		<?php echo JLayoutHelper::render('search', array('view' => $this, 'options' => $fOpts), JPATH_ROOT.'/components/com_meedya/layouts'); ?>
		<?php if ($this->iids): ?>
		<div class="actbuts">
			<?php echo JHtml::_('meedya.actionButtons', array('sela','seln','edts','adds','dels')); ?>
		</div>
		<?php endif; ?>
		<?php echo $this->loadTemplate($this->mode == 'G' ? 'grid' : 'list'); ?>
		<input type="hidden" name="task" value="manage.editImgs" />
		<input type="hidden" name="albumid" value="" />
		<input type="hidden" name="nualbnam" value="" />
		<input type="hidden" name="nualbpar" value="" />
		<input type="hidden" name="nualbdesc" value="" />
		<input type="hidden" name="mode" value="<?=$this->mode?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>

<?php
echo JHtml::_(
	'bootstrap.renderModal',
	'add2albdlg',
	array(
		'title' => JText::_('COM_MEEDYA_ADD_ALBUM_ITEMS'),
		'footer' => JHtml::_('meedya.modalButtons', 'COM_MEEDYA_ADD2ALBUM', 'addItems2Album(this)', 'creab'),
		'modalWidth' => '40'
	),
	$this->loadTemplate('add2alb')
	);
?>

<script>
jQuery('#system-message-container').delay(5000).slideUp("slow");
var blb_path = "<?=JUri::root(true).'/'.$this->gallpath?>/med/";
echo.init({
	baseUrl: "<?=JUri::root(true).'/'.$this->gallpath?>/",
	offset: 200,
	throttle: 250,
	debounce: false
});
</script>
