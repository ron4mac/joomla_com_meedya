<?php
defined('_JEXEC') or die;

abstract class JHtmlMeedya
{
	public static function manageMenu ($aid)
	{
		$html = '<div class="btn-group mgmenu">
	<a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-pencil"></i>'.JText::_('COM_MEEDYA_MENU_MANAGE').' <span class="caret"></span></a>
	<ul class="dropdown-menu">
		<li><a href="' . JRoute::_('index.php?option=com_meedya&task=manage.doUpload&aid='.$aid, false) . '"><i class="icon-upload"></i>'.JText::_('COM_MEEDYA_MENU_UPLOAD').'</a></li>
		<li><a href="' . JRoute::_('index.php?option=com_meedya&view=manage') . '"><i class="icon-grid"></i>'.JText::_('COM_MEEDYA_MENU_EDALBS').'</a></li>
		<li><a href="' . JRoute::_('index.php?option=com_meedya&task=manage.editImgs') . '"><i class="icon-images"></i>'.JText::_('COM_MEEDYA_MENU_EDIMGS').'</a></li>
		<li><a href="' . JRoute::_('index.php?option=com_meedya&task=manage.doConfig') . '"><i class="icon-options"></i>'.JText::_('COM_MEEDYA_MENU_CONFIG').'</a></li>
	</ul>
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
			$html .= '<option value="'.$alb->aid.'"'.($alb->aid==$sel ? ' selected' : '').'>'.str_repeat('&nbsp;&nbsp;',$d-1).$alb->title.'</option>';
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

	public static function imageThumbElement ($item, $edt=false, $iclss='item')
	{	//var_dump($item);
	$id = $item->id;
	$iDat = 'data-echo="thm/'.$item->file.'"';
	if ($edt) {
		$acts = '<i class="icon-expand" onclick="lboxPimg(\''.$item->file.'\')"></i>
			<i class="icon-info-2 pull-left"></i>
			<i class="icon-edit pull-right" onclick="editImg('.$id.')"></i>';
	} else {
		$acts = '<i class="icon-info-2 pull-left"></i>
			<i class="icon-expand pull-right" onclick="lboxPimg(\''.$item->file.'\')"></i>';
	}
	return '
	<div class="'.$iclss.'">
		<label for="slctimg'.$id.'">
		<img src="components/com_meedya/static/img/img.png" '.$iDat.' class="mitem" />
		</label>
		<div class="item-overlay top">
			'.$acts.'
		</div>
		<input type="checkbox" name="slctimg[]" id="slctimg'.$id.'" value="'.$id.'" />
		<div class="iSlct"><i class="icon-checkmark"></i></div>
	</div>';
	}

	public static function __imageThumbElement ($item, $edt=false)
	{
	$html = [];
	$id = $item->id;
	$iDat = 'data-iid="'.$item->id.'" data-echo="thm/'.$item->file.'" data-img="'.$item->file.'"';
	return '
	<div class="item">
		<label for="slctimg'.$id.'">
		<img src="components/com_meedya/static/img/img.png" '.$iDat.' class="mitem" onclick="//return slctImg(event, this)" />
		</label>
		<div class="item-overlay top">
			<i class="icon-expand" onclick="lboxPimg(event, this)"></i>
			<i class="icon-info-2 pull-left"></i>
			<i class="icon-edit pull-right" onclick="editImg(event, this)"></i>
		</div>
		<input type="checkbox" name="slctimg[]" id="slctimg'.$id.'" value="'.$id.'" />
		<div class="iSlct"><i class="icon-checkmark"></i></div>
	</div>';
	}

}