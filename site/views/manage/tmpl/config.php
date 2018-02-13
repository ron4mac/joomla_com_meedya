<?php
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::stylesheet('components/com_meedya/static/css/manage.css');
JHtml::_('jquery.framework', false);
//JHtml::_('behavior.framework');

//Note that the options argument is optional so JHtmlTabs::start() can be called without it
$options = array(
	'onActive' => 'function(title, description){
		description.setStyle("display", "block");
		title.addClass("open").removeClass("closed");
	}',
	'onBackground' => 'function(title, description){
		description.setStyle("display", "none");
		title.addClass("closed").removeClass("open");
	}',
	'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
	'useCookie' => true, // this must not be a string. Don't use quotes.
);
?>

<form action="" method="post">
	<button type="submit" name="save" value="1" class="btn btn-primary pull-right">Save</button>
	<button type="button" class="btn pull-right" onclick="window.location='<?=$_SERVER['HTTP_REFERER']?>'">Cancel</button>
	<?php echo JHtml::_('bootstrap.startTabSet', 'tabs_id', array('active'=>'panel-id-1')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'tabs_id', 'panel-id-1', JText::_('Panel Title 1')); ?>
		<?=$this->loadTemplate('gallery')?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'tabs_id', 'panel-id-2', JText::_('CUSTOM_PANEL_TITLE')); ?>
		<?=$this->loadTemplate('slides')?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'tabs_id', 'panel-id-3', JText::_('CUSTOM_PANEL_TITLE2')); ?>
		<?=$this->loadTemplate('upload')?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	<input type="hidden" name="task" value="manage.saveConfig" />
	<input type="hidden" name="return" value="<?=base64_encode($_SERVER['HTTP_REFERER'])?>" />
	<?=JHtml::_('form.token')?>
</form>