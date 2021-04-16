<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

MeedyaHelper::addStyle('gallery');
MeedyaHelper::addStyle('manage');
MeedyaHelper::addStyle('pell.min', 'vendor/pell/');
HTMLHelper::_('jquery.framework');
MeedyaHelper::addScript('manage');
MeedyaHelper::addScript('bootbox');
MeedyaHelper::addScript('pell.min', 'vendor/pell/');

$this->jDoc->addScriptDeclaration('
Meedya.rawURL = "'.Route::_('index.php?option=com_meedya&format=raw&Itemid='.$this->itemId, false).'";
');

Text::script('COM_MEEDYA_REMOVE');
Text::script('COM_MEEDYA_VRB_REMOVE');

//var_dump($this->album);
?>
<style>
	.mitem, .litem {width:120px; height:120px;}
	.modal-backdrop.fade.in {opacity:0.4}
	.bootbox-body {padding: 12px; font-size: larger;}
	.modal-footer {padding: 8px 10px}
</style>
<div class="meedya-gallery">
<?php if ($this->manage) echo HTMLHelper::_('meedya.manageMenu', $this->userPerms, 0, $this->itemId); ?>
<h3>ALBUM EDIT: <?=$this->album['title']?></h3>
<button class="btn btn-primary" onclick="saveAlbum()">Save Changes</button>
<button class="btn" onclick="cancelEdt()">Cancel</button>
<form action="<?=Route::_('index.php?option=com_meedya&Itemid='.$this->itemId)?>" id="albForm" name="albForm" method="POST">
	<div class="albman">
		<div class="albprp">
			Title:<br />
			<input type="text" name="albttl" value="<?=$this->album['title']?>" /><br />
			Description:<br />
			<textarea name="albdsc" id="albdsc" style="display:none"><?=$this->album['desc']?></textarea>
			<div id="peditor" class="pell"></div>
			<input type="hidden" name="albthmid" id="albthmid" value=<?=$this->album['thumb']?> />
		</div>
		<div class="albthm" id="albthm">
			<img id="albthmimg" src="<?=$this->aThum?>" width="120px" height="120px" title="album thumbnail image" alt="album thumbnail" />
		</div>
	</div>
	<input type="hidden" name="task" value="manage.saveAlbum" />
	<input type="hidden" name="aid" value="<?=$this->aid?>" />
	<input type="hidden" name="thmord" value="" />
	<input type="hidden" name="referer" value="<?=base64_encode($this->referer)?>" />
	<?=HTMLHelper::_('form.token')?>
</form>

<?php if ($this->params->def('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>

<div class="actbuts">
	<?php echo HTMLHelper::_('meedya.actionButtons', ['sela','seln','edts','rems']); ?>
</div>
<form id="actform" method="POST" action="<?=Route::_('index.php?option=com_meedya&Itemid='.$this->itemId)?>" style="display:none">
	<input name="task" id="atask" type="hidden" value="manage.imgEdit" />
	<input name="items" id="aitems" type="hidden" value="" />
</form>
<form action="<?=Route::_('index.php?option=com_meedya&Itemid='.$this->itemId)?>" method="POST" name="adminForm" id="adminForm">
<div id="area" style="display:flex;flex-wrap:wrap">
<?php
	foreach ($this->items as $item) {
		if (!$item) continue;
		echo HTMLHelper::_('meedya.imageThumbElement', (object)$this->getItemFile($item), false, 'item');
	}
?>
	<div id="itmend" class="noitem item"></div>
</div>
<input type="hidden" name="task" value="manage.editImgs" />
</form>
</div>

<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>

<script>

function cancelEdt () {
	window.location = atob(document.albForm.referer.value);
}
echo.init({
	baseUrl: "<?=JUri::root(true).'/'.$this->gallpath?>/",
	offset: 200,
	throttle: 250,
	debounce: false
});

Arrange.init('area','item');

var albthm = document.getElementById("albthm");
albthm.addEventListener('dragover', handleAlbthmDragOver, false);
albthm.addEventListener('drop', handleAlbthmDrop, false);
albthm.addEventListener('dragenter', function () { this.style.opacity = '0.5'; }, false);
albthm.addEventListener('dragleave', function () { this.style.opacity = '1.0'; }, false);
var albfrm = document.getElementById("albForm");
albfrm.addEventListener('dragstart', function(e){ e.dataTransfer.setData('albthm','X'); }, false);
albfrm.addEventListener('dragover', function(e){ if (e.dataTransfer.types.indexOf('albthm')>0) { _pd(e);e.dataTransfer.dropEffect = 'move'; } }, false);
albfrm.addEventListener('dragenter', function(e){ if (e.dataTransfer.types.indexOf('albthm')>0) { _pd(e);e.dataTransfer.dropEffect = 'move'; } }, false);
albfrm.addEventListener('drop', function(e){ _pd(e); removeAlbThm(); }, false);

var editor = window.pell.init({
		element: document.getElementById('peditor'),
		defaultParagraphSeparator: 'p',
		onChange: function (html) {
			document.getElementById('albdsc').textContent = html;
		},
		actions: ['bold','italic','underline','heading2','quote','olist','ulist','link']
	});
editor.content.innerHTML = document.getElementById('albdsc').textContent;

</script>
