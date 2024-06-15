<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022-2024 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.4
*/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

abstract class HtmlMeedya
{
	public static function pageHeader ($params, $sub='')
	{
		$html = '';
		if ($params->def('show_page_heading', 1)) {
			$html .= '<h1>';
			$html .= $params->get('page_title');
			switch ($params->get('instance_type', 3)) {
				case 0:
					$user = Factory::getUser();
					$html .= ' <small>- '.$user->name.'</small>';
					break;
				case 1:
					break;
				case 2:
					break;
				case 3:
					if ($owner = $params->get('owner'))
						$html .= ' <small>- '.$owner.'</small>';
					break;
			}
			$html .= '</h1>';
		}
		if ($sub) {
			$html .= '<h3>'.$sub.'</h3>';
		//	$html .= '<span class="badge rounded-pill bg-primary">?</span>';
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

	public static function manageMenu5 ($perms, $aid=0, $Itemid=0)
	{
		if (!$perms) return '';
		$itmid = $Itemid ? ('&Itemid='.$Itemid) : '';
		$html = '<div class="mgmenu btn-group dropdown">
	<button class="btn btn-small dropdown-toggle" '.M34C::bs('toggle').'="dropdown" type="button" id="mmnulink">
		<i class="icon-pencil"></i>'.Text::_('COM_MEEDYA_MENU_MANAGE').' <span class="caret"></span>
	</button>
	<div class="dropdown-menu dropdown-menu-end" aria-labelledby="mmnulink">';
		if ($perms->canAdmin || $perms->canUpload) {
			$html .= '<div class="dropdown-item"><a href="' . Route::_('index.php?option=com_meedya&task=manage.doUpload'.($aid?('&aid='.$aid):'') . $itmid, false) . '">
				<i class="icon-upload"></i>'.Text::_('COM_MEEDYA_MENU_UPLOAD').'</a></div>';
		}
		if ($perms->canAdmin) {
			$html .= '
		<div class="dropdown-item"><a href="' . Route::_('index.php?option=com_meedya&view=manage'.$itmid, false) . '"><i class="icon-grid"></i>'.Text::_('COM_MEEDYA_MENU_EDALBS').'</a></div>
		<div class="dropdown-item"><a href="' . Route::_('index.php?option=com_meedya&task=manage.editImgs'.$itmid, false) . '"><i class="icon-images"></i>'.Text::_('COM_MEEDYA_MENU_EDIMGS').'</a></div>';
//		<div class="dropdown-item"><a href="' . Route::_('index.php?option=com_meedya&task=manage.doConfig'.$itmid, false) . '"><i class="icon-options"></i>'.Text::_('COM_MEEDYA_MENU_CONFIG').'</a></div>';
		}
		$html .= '</div>
</div>
';
		return $html;
	}

	public static function manageMenu ($perms, $aid=0, $Itemid=0)
	{
		if (!$perms) return '';
		$itmid = $Itemid ? ('&Itemid='.$Itemid) : '';
		$html = '<div class="btn-group mgmenu dropdown">
	<a class="btn btn-small dropdown-toggle" '.M34C::bs('toggle').'="dropdown" href="#">
		<i class="icon-pencil"></i> '.Text::_('COM_MEEDYA_MENU_MANAGE').' <span class="caret"></span>
	</a>
	<ul class="dropdown-menu">';
		if ($perms->canAdmin || $perms->canUpload) {
			$html .= '<li><a href="' . Route::_('index.php?option=com_meedya&task=manage.doUpload'.($aid?('&aid='.$aid):'') . $itmid, false) . '">
				<i class="icon-upload"></i> '.Text::_('COM_MEEDYA_MENU_UPLOAD').'</a></li>';
		}
		if ($perms->canAdmin) {
			$html .= '
		<li><a href="' . Route::_('index.php?option=com_meedya&view=manage'.$itmid, false) . '"><i class="icon-grid"></i> '.Text::_('COM_MEEDYA_MENU_EDALBS').'</a></li>
		<li><a href="' . Route::_('index.php?option=com_meedya&task=manage.editImgs'.$itmid, false) . '"><i class="icon-images"></i> '.Text::_('COM_MEEDYA_MENU_EDIMGS').'</a></li>';
//		<li><a href="' . Route::_('index.php?option=com_meedya&task=manage.doConfig'.$itmid, false) . '"><i class="icon-options"></i>'.Text::_('COM_MEEDYA_MENU_CONFIG').'</a></li>';
		}
		$html .= '</ul>
</div>
';
		return $html;
	}

	public static function albumsHierOptions ($albs, $sel=0, $exc=[])
	{
		$html='';
		usort($albs, function ($a, $b) { return strnatcmp($a->hord,$b->hord); });
		if (!is_array($exc)) $exc = [$exc];
		foreach ($albs as $alb) {
			$d = count(explode('.',$alb->hord));
		//	$pfx = str_repeat('&nbsp;&nbsp;',$d-1).($d>1?'&#x251c;&#x2500; ':'');
		//	$pfx = str_repeat('&nbsp;&nbsp;', max($d-2, 0)).($d>1?'&#x251c; ':'');
			$pfx = str_repeat('&nbsp;&nbsp;', max($d-2, 0)).($d>1?'&#x21B3; ':'');
			$html .= '<option value="'.$alb->aid.'"'
				.($alb->aid==$sel ? ' selected' : '')
				.(in_array($alb->aid, $exc) ? ' disabled' : '')
				.'>'.$pfx.$alb->title.'</option>';
		}
		return $html;
	}

	public static function submissionButtons ($save='save')
	{
		$html = '<div class="subbuts">';
		$html .= '<button type="submit" name="cancel" value="1" class="btn">'.Text::_('cancel').'</button>';
		$html .= '<button type="submit" name="save" value="1" class="btn btn-primary">'.Text::_($save).'</button>';
		$html .= '</div>';
		return $html;
	}

	public static function actionButtons ($whch)
	{
		$html = [];
		$b = M34C::btn('ss');
		foreach ($whch as $but) {
			switch ($but) {
			case 'sela':
				$html[] = '<button class="'.$b.'" onclick="Meedya.selAllImg(event, true)">'.Text::_('COM_MEEDYA_MANAGE_SELECT_ALL').'</button>';
				break;
			case 'seln':
				$html[] = '<button class="'.$b.'" onclick="Meedya.selAllImg(event, false)">'.Text::_('COM_MEEDYA_MANAGE_SELECT_NONE').'</button>';
				break;
			case 'edts':
				$html[] = '<button class="'.$b.'" onclick="Meedya.editSelected(event)">'.'<i class="icon-pencil"></i> '.Text::_('COM_MEEDYA_MANAGE_EDIT_ITEMS').'</button>';
				break;
			case 'movs':
				$html[] = '<button class="'.$b.'" onclick="Meedya.moveSelected(event)">'.'<i class="icon-move"></i> '.Text::_('COM_MEEDYA_MANAGE_MOVE_ITEMS').'</button>';
				break;
			case 'adds':
				$html[] = '<button class="'.$b.'" onclick="return Meedya.addSelected(event);">'.'<i class="icon-plus-circle"></i> '.Text::_('COM_MEEDYA_MANAGE_ADD2ALBUM').'</button>';
				break;
			case 'rems':
				$html[] = '<button class="'.$b.'" onclick="Meedya.removeSelected(event)">'.'<i class="icon-minus-circle"></i> '.Text::_('COM_MEEDYA_MANAGE_REMOVE').'</button>';
				break;
			case 'dels':
				$html[] = '<button class="'.$b.'" onclick="Meedya.deleteSelected(event)">'.'<i class="icon-minus-circle"></i> '.Text::_('COM_MEEDYA_MANAGE_TOTAL_DEL').'</button>';
				break;
			default:
				$html[] = 'NOACTION';
			}
		}
		return implode("\n\t",$html);
	}

	public static function actionSelect ($acts)
	{
		$html = ['<option value="">'.Text::_('COM_MEEDYA_MANAGE_WITH_SELECTED').'</option>'];
		$b = M34C::btn('ss');
		foreach ($acts as $act) {
			switch ($act) {
			case 'edts':
				$html[] = '<option value="edts">'.Text::_('COM_MEEDYA_MANAGE_EDIT_ITEMS').'</option>';
				break;
			case 'movs':
				$html[] = '<option value="movs">'.Text::_('COM_MEEDYA_MANAGE_MOVE_ITEMS').'</option>';
			//	$html[] = '<button class="'.$b.'" onclick="Meedya.moveSelected(event)">'.'<i class="icon-move"></i> '.Text::_('COM_MEEDYA_MANAGE_MOVE_ITEMS').'</button>';
				break;
			case 'adds':
				$html[] = '<option value="adds">'.Text::_('COM_MEEDYA_MANAGE_ADD2ALBUM').'</option>';
			//	$html[] = '<button class="'.$b.'" onclick="return Meedya.addSelected(event);">'.'<i class="icon-plus-circle"></i> '.Text::_('COM_MEEDYA_MANAGE_ADD2ALBUM').'</button>';
				break;
			case 'rems':
				$html[] = '<option value="rems">'.Text::_('COM_MEEDYA_MANAGE_REMOVE').'</option>';
			//	$html[] = '<button class="'.$b.'" onclick="Meedya.removeSelected(event)">'.'<i class="icon-minus-circle"></i> '.Text::_('COM_MEEDYA_MANAGE_REMOVE').'</button>';
				break;
			case 'dels':
				$html[] = '<option value="dels">'.Text::_('COM_MEEDYA_MANAGE_TOTAL_DEL').'</option>';
			//	$html[] = '<button class="'.$b.'" onclick="Meedya.deleteSelected(event)">'.'<i class="icon-minus-circle"></i> '.Text::_('COM_MEEDYA_MANAGE_TOTAL_DEL').'</button>';
				break;
			default:
			//	$html[] = 'NOACTION';
			}
		}
		return '<select class="form-select form-select-sm actPicker" onchange="Meedya.selectedAction(event,this.value);this.value=\'\'">' . implode('', $html) . '</select>';
	}

	public static function imageThumbElement ($item, $edt=false, $iclss='item')
	{	//var_dump($item);
		$id = $item->id;
		$escfn = str_replace('\'','\\\'',$item->file);

		if (substr($item->mtype, 0, 1) == 'v') {
			$iDat = 'video.png';
			if ($item->thumb) {
				$iDat = 'img.png" data-echo="thm/'.$item->thumb;
			}
		} else {
			$iDat = 'img.png" data-echo="thm/'.$item->file;
		}

		$mvicon = '';
		if ($edt) {
			$acts = '<i class="icon-expand" onclick="Meedya.Zoom.open('.$id.',this)"></i>
				<i class="icon-info-2 pull-left" onclick="Meedya.itmInfo.open('.$id.',this)"></i>';
			//	<i class="icon-edit pull-right" onclick="editImg('.$id.')"></i>';
			$mvicon = '<div class="itmMove" onclick="Meedya.moveItem(event,this)"><i class="icon-move"></i></div>';
		} else {
			$acts = '<i class="icon-info-2 pull-left" onclick="Meedya.itmInfo.open('.$id.',this)"></i>
				<i class="icon-expand pull-right" onclick="Meedya.Zoom.open('.$id.',this)"></i>';
		}

		$nah = $item->album ? '' : ' orphan';
		return '
		<div class="'.$iclss.$nah.'" data-id="'.$id.'">
			<label for="slctimg'.$id.'">
			<img src="components/com_meedya/static/img/'.$iDat.'" class="mitem" />
			</label>
			<div class="item-overlay top">
				'.$acts.'
			</div>
			<input type="checkbox" name="slctimg[]" id="slctimg'.$id.'" value="'.$id.'" />
			<div class="iSlct"><i class="icon-checkmark"></i></div>
			'.$mvicon.'
		</div>';
	}

	public static function modalButtons ($verb, $script, $id, $disab=true, $bclass=false)
	{
		$html = '<button type="button" class="'.M34C::btn('s').'" '.M34C::bs('dismiss').'="modal">'.Text::_('JCANCEL').'</button>';
		$html .= '<button type="button" id="'.$id.'" class="'.($bclass ?: M34C::btn('p')).'"';
		$html .= ' onclick="'.$script.';"';
		if ($disab) $html .= ' disabled';
		$html .= '>'.Text::_($verb).'</button>';
		return $html;
	}

	public static function buildTree (array $albums, &$html, $paid = 0)
	{
		$branch = [];
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

	public static function cmntsIcon ()
	{
		return ((int)JVERSION < 4) ? '&nbsp;<span class="icon-comments-2"> </span>' : '&nbsp;<i class="far fa-comments"></i>';
	}

	public static function starcmnt ($item, $star, $cmnt)
	{
		$strate = '<div class="strback"><div class="strating" style="width:50%"></div></div>';
		$starelm = $star ? new HtmlElementObject('div.strate', $strate) : null;

//		$starelm = $star ? new HtmlElementObject('span.mystars','stars(12)') : null;
		$cmntelm = $cmnt ? new HtmlElementObject('span.mycmnts','&nbsp;&nbsp;5 <i class="far fa-comments"></i>') : null;
		return (new HtmlElementObject('div.starcmnt', null, $starelm, $cmntelm))->setAttr('data-iid', $item['id']);
		return ($star || $cmnt) ? '<div class="starcmnt"><span class="mystars">stars(12)</span><span class="mycmnts">5 <i class="far fa-comments"></i></span></div>' : null;
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
