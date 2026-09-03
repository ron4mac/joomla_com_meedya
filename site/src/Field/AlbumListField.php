<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2026 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.6.0
*/
namespace RJCreations\Component\Meedya\Site\Field;

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Form\Field\ListField;

class AlbumListField extends ListField
{
	protected $type = 'AlbumList';

	// Gets list of albums options from the form field
	protected function getOptions()
	{
		$albs = json_decode($this->element['albums']);
		$options = parent::getOptions();

		// Build the options array.
		foreach ($albs as $alb) {
			$d = count(explode('.', $alb->hord));
			$options[] = HTMLHelper::_('select.option', $alb->aid, str_repeat('&nbsp;&nbsp;',$d-1).($d>1?'&#x21B3; ':'').$alb->title);
		}

		return $options;
	}

}
