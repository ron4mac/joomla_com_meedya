<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

JHtml::_('jquery.framework');

MeedyaHelper::addScript('manage');
MeedyaHelper::addStyle('jquery.tagsinput', 'vendor/tags/');
MeedyaHelper::addScript('jquery.tagsinput', 'vendor/tags/');

JHtml::stylesheet('components/com_meedya/static/css/manage.css');
//echo'<pre>';var_dump($this->iids);echo'</pre>';
$this->jDoc->addScriptDeclaration('
var iZoomURL = "'.Route::_('index.php?option=com_meedya&format=raw&task=manage.getZoomItem&Itemid='.$this->itemId, false).'";
');
?>
</script>
<form name="adminform" method="POST">
<?=JHtml::_('meedya.submissionButtons')?>
<input type="hidden" name="task" value="manage.iedSave" />
<input type="hidden" name="referer" value="<?=base64_encode($this->referer)?>" />
<?php foreach ($this->iids as $iid): ?>
<hr style="clear:both" />
<?php
	$namx = $iid->id;
	$idx = '_'.$namx;
	$mTyp = substr($iid->mtype, 0, 1);
	if ($mTyp == 'v') {
		$tPath = 'components/com_meedya/static/img/video.png';
	} else {
		$tPath = $this->gallpath.'/thm/'.$iid->file;
	}
	$iFile = $iid->file;
?>
<div class="ied-img">
	<div class="eitem">
		<img src="<?=$tPath?>" data-img="<?=$iFile?>" class="mitem" />
		<div class="item-overlay top">
			<i class="icon-expand" title="expand image" onclick="iZoomOpen(<?=$namx?>)"></i>
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
<?=JHtml::_('meedya.submissionButtons')?>
</form>
<script>
	jQuery(".itmtags").tagsInput();
</script>
