<?php
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::stylesheet('components/com_meedya/static/css/manage.css');
//JHtml::_('jquery.framework', false);

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
	<?=JHtmlTabs::start('tabs_id')?>
	<?=JHtmlTabs::panel("Panel Title 1",'panel-id-1')?>
		<?=$this->loadTemplate('gallery')?>
	<?=JHtmlTabs::panel(JText::_('CUSTOM_PANEL_TITLE'),'panel-id-2')?>
		<?=$this->loadTemplate('slides')?>
	<?=JHtmlTabs::panel(JText::_('CUSTOM_PANEL_TITLE'),'panel-id-3')?>
		<?=$this->loadTemplate('upload')?>
	<?=JHtmlTabs::end()?>
	<input type="hidden" name="task" value="manage.saveConfig" />
	<input type="hidden" name="return" value="<?=base64_encode($_SERVER['HTTP_REFERER'])?>" />
	<?=JHtml::_('form.token')?>
</form>