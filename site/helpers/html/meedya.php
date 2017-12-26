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
		usort($albs, function ($a, $b) { return strnatcmp($a['hord'],$b['hord']); });
		foreach ($albs as $alb) {
			$d = count(explode('.',$alb['hord']));
			$html .= '<option value="'.$alb['aid'].'"'.($alb['aid']==$sel ? ' selected' : '').'>'.str_repeat('&nbsp;&nbsp;',$d-1).$alb['title'].'</option>';
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

}