<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.4
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;

MeedyaHelper::addStyle('gallery');
MeedyaHelper::addStyle('manage');
HTMLHelper::_('jquery.framework', false);
MeedyaHelper::addScript(['common','manage']);
$jdoc = Factory::getDocument();

echo '<div class="meedya-config">';

if ($this->manage) echo HtmlMeedya::manageMenu($this->userPerms, 0, $this->itemId);
echo HtmlMeedya::pageHeader($this->params, $this->action/*.'XXXX'*/);

echo HTMLHelper::_('bootstrap.startTabSet', 'mdya_tabs', ['active'=>'cfg-ah'])
	,HTMLHelper::_('bootstrap.addTab', 'mdya_tabs', 'cfg-ah', Text::_('Panel Title 1'))
	,$this->loadTemplate('gallery')
	,HTMLHelper::_('bootstrap.endTab')
	,HTMLHelper::_('bootstrap.addTab', 'mdya_tabs', 'cfg-ss', Text::_('CUSTOM_PANEL_TITLE'))
	,$this->loadTemplate('slides')
	,HTMLHelper::_('bootstrap.endTab')
	,HTMLHelper::_('bootstrap.addTab', 'mdya_tabs', 'cfg-up', Text::_('CUSTOM_PANEL_TITLE2'))
	,$this->loadTemplate('upload')
	,HTMLHelper::_('bootstrap.endTab')
	,HTMLHelper::_('bootstrap.endTabSet')
	;

?>
</div>
<!-- <script type="text/javascript">
	AArrange.init('gstruct','album');
</script> -->
