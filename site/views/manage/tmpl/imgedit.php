<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('jquery.framework');

MeedyaHelper::addScript('manage');
MeedyaHelper::addStyle('jquery.tagsinput', 'vendor/tags/');
MeedyaHelper::addScript('jquery.tagsinput', 'vendor/tags/');

HTMLHelper::stylesheet('components/com_meedya/static/css/manage.css');
//echo'<pre>';var_dump($this->iids);echo'</pre>';
?>
</script>
<form name="adminform" method="POST">
<?=HTMLHelper::_('meedya.submissionButtons')?>
<input type="hidden" name="task" value="manage.iedSave" />
<input type="hidden" name="referer" value="<?=base64_encode($this->referer)?>" />
<input type="hidden" name="<?=Session::getFormToken()?>" value="1" />
<?php foreach ($this->iids as $iid): ?>
<hr style="clear:both" />
<?php
	$namx = $iid->id;
	$idx = '_'.$namx;
	$mTyp = substr($iid->mtype, 0, 1);
	if ($mTyp == 'v') {
		$tPath = 'components/com_meedya/static/img/video.png';
		if ($iid->thumb) {
			$tPath = $this->gallpath.'/thm/'.$iid->thumb;
		}
	} else {
		$tPath = $this->gallpath.'/thm/'.$iid->file;
	}
	$iFile = $iid->file;
?>
<div class="ied-img">
	<div class="eitem">
		<label><img src="<?=$tPath?>" data-img="<?=$iFile?>" class="mitem" /></label>
		<div class="item-overlay top">
			<i class="icon-expand" title="expand image" onclick="iZoomOpen(<?=$namx?>, this)"></i>
			<i class="icon-info-2 pull-left" title="image info"></i>
			<i class="icon-upload pull-right" title="replace image" onclick="editImg(event, this)"></i>
		</div>
	</div>
	<div class="ied-attr">
		<div class="ied-div2">
		<div><label for="title<?=$idx?>">Title</label><input type="text" name="attr[<?=$namx?>][title]" id="title<?=$idx?>" value="<?=$iid->title?>" /></div>
		<div><label for="kywrd<?=$idx?>">Key words</label><input type="text" name="attr[<?=$namx?>][kywrd]" class="itmtags" id="kywrd<?=$idx?>" value="<?=$iid->kywrd?>" /></div>
		</div>
		<div class="ied-div3"><label for="desc<?=$idx?>">Description</label><textarea name="attr[<?=$namx?>][desc]" id="desc<?=$idx?>" cols="60" rows="5"><?=$iid->desc?></textarea></div>
	</div>
</div>
<?php endforeach; ?>
<?=HTMLHelper::_('meedya.submissionButtons')?>
</form>
<script>
	jQuery(".itmtags").tagsInput();
</script>
