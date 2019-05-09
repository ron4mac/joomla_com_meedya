<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2019 Ron Crans. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JHtml::stylesheet('components/com_meedya/static/vendor/blb/basicLightbox.min.css');
JHtml::stylesheet('components/com_meedya/static/css/manage.css');
//echo'<pre>';var_dump($this->iids);echo'</pre>';
?>
<script src="components/com_meedya/static/vendor/blb/basicLightbox.min.js"></script>
<script>
var blb_path = "<?=JUri::root(true).'/'.$this->gallpath?>/med/";
function lboxPimg (evt, elm) {
	const pimg = elm.parentElement.previousElementSibling;	console.log(pimg);
	const src = blb_path + pimg.getAttribute('data-img');	console.log(src);
	const html = '<img src="' + src + '">';
	basicLightbox.create(html).show();
}
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
	$tPath = $this->gallpath.'/thm/'.$iid->file;
	$iFile = $iid->file;
?>
<div class="ied-img">
	<div class="eitem">
		<img src="<?=$tPath?>" data-img="<?=$iFile?>" />
		<div class="item-overlay top">
			<i class="icon-expand" title="expand image" onclick="lboxPimg(event, this)"></i>
			<i class="icon-info-2 pull-left" title="image info"></i>
			<i class="icon-upload pull-right" title="replace image" onclick="editImg(event, this)"></i>
		</div>
	</div>
	<div class="ied-attr">
		<div class="ied-div2">
		<div><label for="title<?=$idx?>">Title</label><input type="text" name="attr[<?=$namx?>][title]" id="title<?=$idx?>" value="<?=$iid->title?>" /></div>
		<div><label for="kywrd<?=$idx?>">Key words</label><input type="text" name="attr[<?=$namx?>][kywrd]" id="kywrd<?=$idx?>" value="<?=$iid->kywrd?>" /></div>
		</div>
		<div class="ied-div3"><label for="desc<?=$idx?>">Description</label><textarea name="attr[<?=$namx?>][desc]" id="desc<?=$idx?>" cols="60" rows="5"><?=$iid->desc?></textarea></div>
	</div>
</div>
<?php endforeach; ?>
<?=JHtml::_('meedya.submissionButtons')?>
</form>
