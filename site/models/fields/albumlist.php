<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

class JFormFieldAlbumList extends JFormFieldList
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
			$options[] = HTMLHelper::_('select.option', $alb->aid, str_repeat('_ ',$d-1).$alb->title);
		}

		return $options;
	}

}
