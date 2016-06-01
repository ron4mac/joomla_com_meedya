<?php
defined('_JEXEC') or die;

JHtml::stylesheet('components/com_meedya/static/css/manage.css');
//echo'<pre>';var_dump($this->iids);echo'</pre>';
?>
<form name="adminform" method="POST">
<?=JHtml::_('meedya.submissionButtons')?>
<input type="hidden" name="task" value="manage.iedSave" />
<?php foreach ($this->iids as $iid): ?>
<hr style="clear:both" />
<?php
	$namx = $iid['id'];
	$idx = '_'.$iid['id'];
?>
<div>
	<div class="ied-img">
		<img src="<?= $this->gallpath.'/thm/'.$iid['file'] ?>" />
	</div>
	<div class="ied-attr">
		<label for="title<?=$idx?>">Title</label><input type="text" name="attr[<?=$namx?>][title]" id="title<?=$idx?>" value="<?=$iid['title']?>" />
		<label for="desc<?=$idx?>">Description</label><input type="text" name="attr[<?=$namx?>][desc]" id="desc<?=$idx?>" value="<?=$iid['desc']?>" />
		<label for="kywrd<?=$idx?>">Key words</label><input type="text" name="attr[<?=$namx?>][kywrd]" id="kywrd<?=$idx?>" value="<?=$iid['kywrd']?>" />
	</div>
</div>
<?php endforeach; ?>
<?=JHtml::_('meedya.submissionButtons')?>
</form>