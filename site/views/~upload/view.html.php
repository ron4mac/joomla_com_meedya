<?php
// No direct access
defined('_JEXEC') or die;

//require_once JPATH_COMPONENT.'/helpers/meedya.php';
JLoader::register('MeedyaHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helpers/meedya.php');
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

no class MeedyaViewUpload extends JViewLegacy
{
	protected $state;
	protected $params;
	protected $galid;
	protected $albums;
	protected $curalb;
	protected $maxupld;
	protected $acptmime = 'accept="image/*" ';

	function display($tpl = null)
	{
		$user = JFactory::getUser();
		$uid = $user->get('id');
		$this->params = JFactory::getApplication()->getParams();
		$this->galid = base64_encode($this->params->get('instance_type').':'.$this->params->get('owner_group').':'.$uid);
		$this->state = $this->get('State');
		$this->albums = $this->get('AlbumsList');
		$this->maxupld = MeedyaHelper::to_KMG($this->params->get('max_upload'));
		$this->dbTime = $this->getModel()->getDbTime();

		parent::display($tpl);
	}
}
