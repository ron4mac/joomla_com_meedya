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

extract($displayData);	//link,parent

$script = <<<EOD
Meedya._id('copyBtn').addEventListener('click', function() {
    const copyText = Meedya._id('shrLink');
    // Find the <i> icon element inside the button
    const icon = this.querySelector('i');
    
    const text = copyText.innerText;

    navigator.clipboard.writeText(text)
        .then(() => {
            // 1. Swap classes to show the checkmark icon
            icon.classList.remove('fa-copy');
            icon.classList.add('fa-check');
            
            // 2. Add an optional active color class for a green success flash
            this.classList.add('success');

            // 3. Wait 2000 milliseconds (2 seconds) then switch back
            setTimeout(() => {
                icon.classList.remove('fa-check');
                icon.classList.add('fa-copy');
                this.classList.remove('success');
            }, 2000);
        })
        .catch(err => {
            console.error("Failed to copy text: ", err);
        });
});
EOD;

$parent->add2btmscript($script);

$smmdl = HTMLHelper::_(
	'bootstrap.renderModal',
	'shareDlg', // selector
	[ // options
		'title'  => Text::_('COM_MEEDYA_SHARE_ALBUM_TITLE'),
		//'modalWidth' => 20
	],
	'<div class="clipboard"><span id="shrLink" class="shrLink">'.$link.'</span><button id="copyBtn" aria-label="Copy to clipboard"><i class="fa fa-light fa-copy"></i></button></div>'
);

echo str_replace('modal-lg', '', $smmdl);
