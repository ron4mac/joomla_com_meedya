<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2020 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

abstract class JHtmlMeedya
{
	public static function pageHeader ($params, $sub='')
	{
		$html = '';
		if ($params->def('show_page_heading', 1)) {
			$html .= '<h1>';
			$html .= $params->get('page_title');
			switch ($params->get('instance_type')) {
				case 0:
					$user = Factory::getUser();
					$html .= ' <small>- '.$user->name.'</small>';
					break;
				case 1:
					break;
				case 2:
					break;
			}
			$html .= '</h1>';
		}
		if ($sub) {
			$html .= '<h3>'.$sub.'</h3>';
		}
		return $html;
	}

	public static function searchField ($aid)
	{
		$fact = self::aiUrl('view=search');
//		$fact = self::aiUrl('view=album');
		return <<<EOD
<div class="search">
	<form name="sqry" action="{$fact}" method="POST" onsubmit="return Meedya.performSearch(this)">
		<input type="hidden" name="task" value="search.search" />
		<input type="hidden" name="aid" value="{$aid}" />
		<input type="search" name="sterm" results="10" autosave="meedya" placeholder="Search..." />
	</form>
</div>
EOD;
	}

	public static function manageMenu ($perms, $aid=0, $Itemid=0)
	{
		if (!$perms) return '';
		$itmid = $Itemid ? ('&Itemid='.$Itemid) : '';
		$html = '<div class="btn-group mgmenu">
	<a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
		<i class="icon-pencil"></i>'.JText::_('COM_MEEDYA_MENU_MANAGE').' <span class="caret"></span>
	</a>
	<ul class="dropdown-menu">';
		if ($perms->canAdmin || $perms->canUpload) {
			$html .= '<li><a href="' . Route::_('index.php?option=com_meedya&task=manage.doUpload'.($aid?('&aid='.$aid):'') . $itmid, false) . '">
				<i class="icon-upload"></i>'.JText::_('COM_MEEDYA_MENU_UPLOAD').'</a></li>';
		}
		if ($perms->canAdmin) {
			$html .= '
		<li><a href="' . Route::_('index.php?option=com_meedya&view=manage'.$itmid, false) . '"><i class="icon-grid"></i>'.JText::_('COM_MEEDYA_MENU_EDALBS').'</a></li>
		<li><a href="' . Route::_('index.php?option=com_meedya&task=manage.editImgs'.$itmid, false) . '"><i class="icon-images"></i>'.JText::_('COM_MEEDYA_MENU_EDIMGS').'</a></li>
		<li><a href="' . Route::_('index.php?option=com_meedya&task=manage.doConfig'.$itmid, false) . '"><i class="icon-options"></i>'.JText::_('COM_MEEDYA_MENU_CONFIG').'</a></li>';
		}
		$html .= '</ul>
</div>
';
		return $html;
	}

	public static function albumsHierOptions ($albs, $sel=0)
	{
		$html='';
		usort($albs, function ($a, $b) { return strnatcmp($a->hord,$b->hord); });
		foreach ($albs as $alb) {
			$d = count(explode('.',$alb->hord));
			$pfx = str_repeat('&nbsp;&nbsp;',$d-1).($d>1?'&#x251c;&#x2500; ':'');
			$html .= '<option value="'.$alb->aid.'"'.($alb->aid==$sel ? ' selected' : '').'>'.$pfx.$alb->title.'</option>';
		}
		return $html;
	}

	public static function submissionButtons ($save='save')
	{
		$html = '<div class="subbuts">';
		$html .= '<button type="submit" name="cancel" value="1" class="btn">'.JText::_('cancel').'</button>';
		$html .= '<button type="submit" name="save" value="1" class="btn btn-primary">'.JText::_($save).'</button>';
		$html .= '</div>';
		return $html;
	}

	public static function actionButtons ($whch)
	{
		$html = [];
		foreach ($whch as $but) {
			switch ($but) {
			case 'sela':
				$html[] = '<button class="btn btn-mini" onclick="selAllImg(event, true)">'.JText::_('COM_MEEDYA_MANAGE_SELECT_ALL').'</button>';
				break;
			case 'seln':
				$html[] = '<button class="btn btn-mini" onclick="selAllImg(event, false)">'.JText::_('COM_MEEDYA_MANAGE_SELECT_NONE').'</button>';
				break;
			case 'edts':
				$html[] = '<button class="btn btn-mini" onclick="editSelected(event)">'.'<i class="icon-pencil"></i> '.JText::_('COM_MEEDYA_MANAGE_EDIT_ITEMS').'</button>';
				break;
			case 'adds':
				$html[] = '<button class="btn btn-mini" onclick="return addSelected(event);">'.'<i class="icon-plus-circle"></i> '.JText::_('COM_MEEDYA_MANAGE_ADD2ALBUM').'</button>';
				break;
			case 'rems':
				$html[] = '<button class="btn btn-mini" onclick="removeSelected(event)">'.'<i class="icon-minus-circle"></i> '.JText::_('COM_MEEDYA_MANAGE_REMOVE').'</button>';
				break;
			case 'dels':
				$html[] = '<button class="btn btn-mini" onclick="deleteSelected(event)">'.'<i class="icon-minus-circle"></i> '.JText::_('COM_MEEDYA_MANAGE_TOTAL_DEL').'</button>';
				break;
			default:
				$html[] = 'NOACTION';
			}
		}
		return implode("\n\t",$html);
	}

	public static function imageThumbElement ($item, $edt=false, $iclss='item')
	{	//var_dump($item);
	$id = $item->id;
	$escfn = str_replace('\'','\\\'',$item->file);

	if (substr($item->mtype, 0, 1) == 'v') {
		$iDat = 'video.png';
	} else {
		$iDat = 'img.png" data-echo="thm/'.$item->file;
	}

	if ($edt) {
		$acts = '<i class="icon-expand" onclick="lboxPimg(\''.$escfn.'\',\''.substr($item->mtype, 0, 1).'\')"></i>
			<i class="icon-info-2 pull-left"></i>
			<i class="icon-edit pull-right" onclick="editImg('.$id.')"></i>';
	} else {
		$acts = '<i class="icon-info-2 pull-left"></i>
			<i class="icon-expand pull-right" onclick="lboxPimg(\''.$escfn.'\',\''.substr($item->mtype, 0, 1).'\')"></i>';
	}
	$nah = $item->album ? '' : ' style="opacity:0.2"';
	return '
	<div class="'.$iclss.'" data-id="'.$id.'">
		<label for="slctimg'.$id.'">
		<img src="components/com_meedya/static/img/'.$iDat.'" class="mitem"'.$nah.' />
		</label>
		<div class="item-overlay top">
			'.$acts.'
		</div>
		<input type="checkbox" name="slctimg[]" id="slctimg'.$id.'" value="'.$id.'" />
		<div class="iSlct"><i class="icon-checkmark"></i></div>
	</div>';
	}

	public static function modalButtons ($verb, $script, $id, $disab=true)
	{
		$html = '<button type="button" class="btn" data-dismiss="modal">'.JText::_('JCANCEL').'</button>';
		$html .= '<button type="button" id="'.$id.'" class="btn';
		$html .= $disab ? ' btn-disabled' : ' btn-primary';
		$html .= '" onclick="'.$script.';"';
		if ($disab) $html .= ' disabled';
		$html .= '>'.JText::_($verb).'</button>';
		return $html;
	}

	public static function buildTree (array $albums, &$html, $paid = 0) {
		$branch = array();
		foreach ($albums as $alb) {
			if ($alb['paid'] == $paid) {
			//	$itms = $alb['items'] ? count(explode('|',$alb['items'])) : 'no';
			//	$html[] = '<div data-aid="'.$alb['aid'].'" class="album" draggable="true"><big><b>'.$alb['title'].'</b></big> ( '.$itms.' items )';
				$html[] = '<div data-aid="'.$alb['aid'].'" class="album" draggable="true">';
				$html[] = '<span class="icon-delete"> </span><span class="icon-edit"> </span>';
				$html[] = '<big><b>'.$alb['title'].'</b></big> ( '.$alb['items'].' items )';
				$children = self::buildTree($albums, $html, $alb['aid']);
				if ($children) {
					$alb['children'] = $children;
				}
				$branch[] = $alb;
				$html[] = '</div>';
			}
		}
		return $branch;
	}


/***** private functions *****/

	private static function aiUrl ($prms, $xml=true)
	{
		static $mnuId = 0;

		if (!$mnuId) {
			$mnuId = Factory::getApplication()->input->getInt('Itemid', 0);
		}
		if (is_array($prms)) $prms = http_build_query($prms);
		$url = Route::_('index.php?option=com_meedya'.($prms?('&'.$prms):'').'&Itemid='.$mnuId, $xml);
		return $url;
	}


}
