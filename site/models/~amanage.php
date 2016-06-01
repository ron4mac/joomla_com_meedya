<?php
defined('_JEXEC') or die;

require_once __DIR__ . '/album.php';

no class MeedyaModelAManage extends MeedyaModelAlbum
{
	protected $album = null;

	public function getAlbum ($aid=0)
	{
		$aid = $aid ?: ($this->state->get('album.id') ?: 0);
		$db = parent::getDBO();
		$db->setQuery('SELECT * FROM `albums` WHERE `aid`='.$aid);
		$r = $db->loadAssoc();

		return $r;
	}

	public function removeItems ($aid, $list)
	{
		if (is_null($this->album) || $this->album['aid']!=$aid) $this->album = $this->getAlbum($aid);
		$cur = explode('|', $this->album['items']);
		$mod = array_diff($cur, $list);
		$items = '\''.implode('|',$mod).'\'';
		$this->updateAlbum(array('items'=>$items));
	}

	private function updateAlbum ($fields)
	{
		if (is_null($this->album)) $this->album = $this->getAlbum();
		$sets = '';
		foreach ($fields as $k=>$v) {
			if ($sets) $sets .= ', ';
			$sets .= $k.' = '.$v;
		}
		$db = parent::getDBO();
		$db->setQuery('UPDATE `albums` SET '.$sets.' WHERE `aid`='.$this->album['aid']);
		//echo $db->getQuery();
		$db->execute();
	}
}