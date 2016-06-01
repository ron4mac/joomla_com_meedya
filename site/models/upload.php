<?php
defined('_JEXEC') or die;

require_once __DIR__ . '/meedya.php';

class MeedyaModelUpload extends MeedyaModelMeedya
{
	public function getDbTime ()
	{
	//	$db = parent::getDBO();
		$db = $this->getDbo();
		$db->setQuery('SELECT CURRENT_TIMESTAMP');
		$r = $db->loadResult();
		return $r;
	}

}