<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.4.0
*/
namespace RJCreations\Component\Meedya\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class SocialModel extends MeedyaModel
{

	// store submitted ratings for items
	// return average of all submitted ratings
	public function rate ($iid, $val)
	{
		// add a new rating to an item's collective rating
		$db = $this->getDbo();
		$db->transactionStart();
		if ($val) {
			$db->setQuery('SELECT ratecnt,ratetot FROM meedyaitems WHERE id='.$iid);
			$r = $db->loadAssoc();
			if (!$r) return -1;
			$rcnt = $r['ratecnt'] + 1;
			$rtot = $r['ratetot'] + $val;
		} else {
			$rcnt = $rtot = 0;
		}
		$db->setQuery('UPDATE meedyaitems SET ratecnt='.$rcnt.',ratetot='.$rtot.' WHERE id='.$iid)->execute();
		$db->transactionCommit();

		// if clearing, delete history and return zero
		if (!$rcnt) {
			$db->setQuery('DELETE FROM uratings WHERE iid='.$iid)->execute();
			$db->setQuery('DELETE FROM gratings WHERE iid='.$iid)->execute();
			return 0;
		}

		// clear away ratings records older that 90 days	%%%%% MAY WANT TO MAKE CONFIGURABLE IN THE FUTURE %%%%%
		$bfd = time()-7776000;	// 90 days (could make configurable)
		$db->setQuery('DELETE FROM uratings WHERE rdate<'.$bfd)->execute();
		$db->setQuery('DELETE FROM gratings WHERE rdate<'.$bfd)->execute();

		// remember where the rating came from to inhibit multiples
		$uid = $this->userId;
		if ($uid) {
			$db->setQuery('INSERT INTO uratings (iid,uid,rdate) VALUES('.$iid.','.$uid.','.time().')');
		} else {
			$db->setQuery('INSERT INTO gratings (iid,ip,rdate) VALUES('.$iid.',\''.$_SERVER['REMOTE_ADDR'].'\','.time().')');		// @@@@@ MAYBE DO THIS REGARDLESS @@@@@
		}
		$db->execute();

		// return average
		return $rtot/$rcnt;
	}

	// check whether a submitter has already rated an item
	// returns false if there has been no recorded submission
	public function rateChk ($iid)
	{
		$db = $this->getDbo();
		$uid = $this->userId;
		if ($uid) {
			$db->setQuery('SELECT rdate FROM uratings WHERE iid='.$iid.' AND uid='.$uid);
		} else {
			$db->setQuery('SELECT rdate FROM gratings WHERE iid='.$iid.' AND ip=\''.$_SERVER['REMOTE_ADDR'].'\'');
		}
		return $db->loadResult();
	}

	// get all the comments for and item
	public function getComments ($iid)
	{
		$db = $this->getDbo();
		$db->setQuery('SELECT * FROM comments WHERE iid='.$iid.' ORDER BY `ctime`');
		return $db->loadAssocList();
	}

	// add a new comment
	public function addComment ($iid, $uid, $cmnt)
	{
		$db = $this->getDbo();
		$db->transactionStart();
		$db->setQuery('INSERT INTO comments (iid,uid,ctime,cmnt) VALUES('.$iid.','.$uid.','.time().','.$db->quote($cmnt).')')->execute();
		$db->setQuery('SELECT cmntcnt FROM meedyaitems WHERE id='.$iid);
		$cnt = $db->loadResult();
		$db->setQuery('UPDATE meedyaitems SET cmntcnt='.(++$cnt).' WHERE id='.$iid)->execute();
		$db->transactionCommit();
		return $cnt;
	}

}
