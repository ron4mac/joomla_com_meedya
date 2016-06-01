<?php
defined('_JEXEC') or die;

require_once __DIR__ . '/meedya.php';

JLoader::register('MeedyaHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/meedya.php');

class MeedyaModelGallery extends MeedyaModelMeedya
{
	protected function getListQuery ()
	{
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('meedyaitems');
		return $query;
	}

}