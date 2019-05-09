<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2019 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

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
			$options[] = JHtml::_('select.option', $alb->aid, str_repeat('&nbsp;&nbsp;',$d-1).$alb->title);
		}

		return $options;
	}

}
