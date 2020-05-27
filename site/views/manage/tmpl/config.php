<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2020 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

MeedyaHelper::addStyle('gallery');
MeedyaHelper::addStyle('manage');
JHtml::_('jquery.framework', false);
MeedyaHelper::addScript('manage');
$jdoc = JFactory::getDocument();
$jdoc->addScriptDeclaration('
var formTokn = "'.JSession::getFormToken().'";
');

echo '<div class="meedya-config">';

if ($this->manage) echo JHtml::_('meedya.manageMenu', $this->userPerms, 0, $this->itemId);
echo JHtml::_('meedya.pageHeader', $this->params, $this->action/*.'XXXX'*/);

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
