<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

require_once __DIR__ . '/meedya.php';

class MeedyaModelSocial extends MeedyaModelMeedya
{

	// store submitted ratings for items
	// return average of all submitted ratings
	public function rate ($iid, $val)
	{
		// add a new rating to an item's collective rating
		$db = $this->getDbo();
		$db->transactionStart();
		$db->setQuery('SELECT ratecnt,ratetot FROM meedyaitems WHERE id='.$iid);
		$r = $db->loadAssoc();
		if (!$r) return -1;
		$rcnt = $r['ratecnt'] + 1;
		$rtot = $r['ratetot'] + $val;
		$db->setQuery('UPDATE meedyaitems SET ratecnt='.$rcnt.',ratetot='.$rtot.' WHERE id='.$iid)->execute();
		$db->transactionCommit();

		// remember where the rating came from to inhibit multiples
		// MAY WANT TO PROVIDE A METHOD TO CLEAR AWAY OLD RECORDS (maybe older than 90 days)
		$uid = Factory::getUser()->get('id');
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
	// return false if there has been no recorded submission
	public function rateChk ($iid)
	{
		$db = $this->getDbo();
		$uid = Factory::getUser()->get('id');
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
