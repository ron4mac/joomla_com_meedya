<?php
// no direct access
defined('_JEXEC') or die;

//require_once JPATH_COMPONENT.'/helpers/meedya.php';

no class MeedyaControllerGManage extends JControllerLegacy
{
/*
	public function save ()
	{
		$app = JFactory::getApplication();
		$input = $app->input->post;
		echo'<xmp>';var_dump($input->post->get('ss',null,'array'));echo'</xmp>';
		if ($input->get('save',0,'int')) {
			if (!JSession::checkToken()) {
				echo'bad token';
				return;
			}
			$app->enqueueMessage('Gallery settings sucessfully saved');
		}
	//	$this->setRedirect(base64_decode($input->get('return','','base64')));
	}

	public function delAlbums ()
	{
		$a = $this->input->get('albs','','string');
		$w = $this->input->get('wipe',false,'boolean');
		if ($a) {
			$albs = explode('|', $a);
			$m = $this->getModel('manage');
			$m->removeAlbums($albs, $w);
		}
		$this->setRedirect(JRoute::_('index.php?option=com_meedya&view=manage&limitstart=0', false));
	}
*/
/*
	public function removeItems ()
	{
		$parm = $this->input->post->get('items','','string');
		$items = explode('|',$parm);
		$m = $this->getModel();
		$m->removeItems($items);
	}
*/
	public function delItems ()
	{
	}
}
