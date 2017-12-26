<?php
defined('_JEXEC') or die;

/*
 * This is a base view class to (hopefully) avoid duplication of code needed by all views
 */

//require_once JPATH_COMPONENT.'/helpers/meedya.php';
JLoader::register('MeedyaHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/meedya.php');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

class MeedyaView extends JViewLegacy
{
	protected $state;
	protected $items = null;
	protected $params;
	protected $meedyaID;
	protected $gallpath;
	protected $pagination;

	public function __construct ($config = array())
	{
		parent::__construct($config);
		$this->params = JFactory::getApplication()->getParams();
//		$this->state = $this->get('State');
		$this->meedyaID = MeedyaHelper::getInstanceID();
		$this->gallpath = MeedyaHelper::userDataPath();
//		$this->pagination = $this->get('Pagination');
	}

	public function display ($tpl = null)
	{
//		$this->params = JFactory::getApplication()->getParams();
//		$this->state = $this->get('State');
//		$this->items = $this->get('Items');
		if (is_null($this->items)) $this->items = $this->getModel()->getItems();
		$this->pagination = $this->get('Pagination');

	//	echo'GOt here';var_dump($this->pagination,$this->items);jexit();
		$jdoc = JFactory::getDocument();
		$jdoc->addScript('components/com_meedya/static/js/'.MeedyaHelper::scriptVersion('echo'));
		parent::display($tpl);
	}

	protected function getAlbumThumb ($albrec)
	{
		$pics = $albrec->items ? explode('|', $albrec->items) : array();
		if (!$albrec->thumb) {
			$albrec->thumb = $pics ? $this->getItemThumb($pics[0]) : false;
		}
		if ($albrec->thumb) {
			$thum = $this->gallpath.'/thm/'.$albrec->thumb;
		} else {
			$thum = 'components/com_meedya/static/img/noimages.jpg';
		}
		return $thum;
	}

	protected function getItemThumb ($iid)
	{
		$m = $this->getModel();
		return $m->getItemThumbFile($iid);
	}

	protected function getItemThumbPlus ($iid)
	{
		$m = $this->getModel();
		return $m->getItemThumbFilePlus($iid);
	}

}