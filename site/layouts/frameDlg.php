<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.5.0
*/
defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use RJCreations\Component\Meedya\Site\Helper\HtmlMeedya;

extract($displayData);	//name,link,parent

$script = <<<EOD
const validateFrame = (elm) => {
	const pubb = Meedya._id('pubb');
	if (elm.value.trim()) {
		pubb.disabled = false;
	} else {
		pubb.disabled = true;
	}
};
const invokeFrame = () => {
	const ldom = Meedya._id('framelink').value.trim();
	const pfx = ldom.startsWith('http') ? '' : 'http://';
	const newW = window.open(pfx+ldom+"/static/cgetnpl.html?nplt={$name}&nplk={$link}", "_blank", "popup");
	if (newW) {
		const mi = bootstrap.Modal.getInstance(Meedya._id('frameDlg'));
		if (mi) mi.hide();
	} else {
		alert("Please allow popups for this website.");
	}
};
EOD;

$parent->add2btmscript($script);

$smmdl = HTMLHelper::_(
	'bootstrap.renderModal',
	'frameDlg', // selector
	[ // options
		'title'  => Text::_('COM_MEEDYA_FRAME_ALBUM_TITLE'),
		'footer' => HtmlMeedya::modalButtons('COM_MEEDYA_FRAME_PUBLISH', 'invokeFrame()', 'pubb'),
		//'modalWidth' => 20
	],
	'<div>'.Text::_('COM_MEEDYA_FRAME_DOMAIN').'<input type="text" id="framelink" onkeyup="validateFrame(this)" placeholder="picframe.local" autocomplete></div>'
);

echo str_replace('modal-lg', '', $smmdl);
