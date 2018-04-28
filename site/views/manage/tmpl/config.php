<?php
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

$jdoc = JFactory::getDocument();
$jdoc->addStyleSheet('components/com_meedya/static/css/gallery.css'.$this->bgt);
$jdoc->addStyleSheet('components/com_meedya/static/css/manage.css'.$this->bgt);
JHtml::_('jquery.framework', false);
$jdoc->addScript('components/com_meedya/static/js/manage.js'.$this->bgt);
$jdoc->addScriptDeclaration('var baseURL = "'.JUri::base().'";
//var aBaseURL = "'.JUri::base().'index.php?option=com_meedya&format=raw&mID='.urlencode($this->meedyaID).'&task=";
var formTokn = "'.JSession::getFormToken().'";
');

echo '<div class="meedya-config">';

if ($this->manage) echo JHtml::_('meedya.manageMenu', 1);
echo JHtml::_('meedya.pageHeader', $this->params, $this->action.'XXXX');

echo JHtml::_('bootstrap.startTabSet', 'mdya_tabs', array('active'=>'cfg-ah'))
	,JHtml::_('bootstrap.addTab', 'mdya_tabs', 'cfg-ah', JText::_('Panel Title 1'))
	,$this->loadTemplate('gallery')
	,JHtml::_('bootstrap.endTab')
	,JHtml::_('bootstrap.addTab', 'mdya_tabs', 'cfg-ss', JText::_('CUSTOM_PANEL_TITLE'))
	,$this->loadTemplate('slides')
	,JHtml::_('bootstrap.endTab')
	,JHtml::_('bootstrap.addTab', 'mdya_tabs', 'cfg-up', JText::_('CUSTOM_PANEL_TITLE2'))
	,$this->loadTemplate('upload')
	,JHtml::_('bootstrap.endTab')
	,JHtml::_('bootstrap.endTabSet')
	;

?>
</div>
<!-- <script type="text/javascript">
	AArrange.init('gstruct','album');
</script> -->