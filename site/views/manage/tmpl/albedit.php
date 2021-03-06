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

$pubchk = $this->album['visib']==1 ? ' checked' : '';
$pubdis = $this->album['pub'] && $this->album['pub']!=$this->album['aid'] ? ' disabled' : '';
//var_dump($this->album);
?>
<style>
	.albman {display:inline-flex}
	.mitem, .litem {width:120px; height:120px}
	.modal-backdrop.fade.in {opacity:0.4}
	.bootbox-body {padding: 12px; font-size: larger}
	.modal-footer {padding: 8px 10px}
	.pell-content {height: 100px}
	.actbuts {margin-top: 1em}
	.albdesc > div {margin-top: 1em}
	#pubalb {vertical-align: text-bottom}
}
</style>
<div class="meedya-gallery">
<?php if ($this->manage) echo HTMLHelper::_('meedya.manageMenu', $this->userPerms, 0, $this->itemId); ?>
<h3><?=Text::_('COM_MEEDYA_ALBEDIT')?> <?=$this->album['title']?></h3>
<form action="<?=Route::_('index.php?option=com_meedya&Itemid='.$this->itemId)?>" id="albForm" name="albForm" method="POST">
	<div class="albman">
		<div class="albprp">
			<?=Text::_('COM_MEEDYA_TITLE')?><br>
			<input type="text" name="albttl" value="<?=$this->album['title']?>" /><br>
			<?=Text::_('COM_MEEDYA_ALBTHM')?><br>
			<div class="albthm" id="albthm">
				<img id="albthmimg" src="<?=$this->aThum?>" width="120px" height="120px" title="album thumbnail image" alt="album thumbnail" />
			</div>
			<input type="hidden" name="albthmid" id="albthmid" value=<?=$this->album['thumb']?> />
		</div>
		<div class="albdesc">
			<div>
				<input type="checkbox" name="pubalb" id="pubalb" value="1"<?=$pubchk.$pubdis?>> <label for="pubalb" style="display:inline"><?=Text::_('COM_MEEDYA_MAKPUB')?></label>
			</div>
			<div>
				<?=Text::_('COM_MEEDYA_DESC')?><br>
				<textarea name="albdsc" id="albdsc" style="display:none"><?=$this->album['desc']?></textarea>
				<div id="peditor" class="pell"></div>
			</div>
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

<button class="btn btn-primary" onclick="Meedya.saveAlbum()"><?=Text::_('COM_MEEDYA_SAVE')?></button>
<button class="btn" onclick="Meedya.cancelEdt()"><?=Text::_('JCANCEL')?></button>

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

Meedya.cancelEdt = function () {
	window.location = atob(document.albForm.referer.value);
}
echo.init({
	baseUrl: "<?=JUri::root(true).'/'.$this->gallpath?>/",
	offset: 200,
	throttle: 250,
	debounce: false
});

Meedya.Arrange.init('area','item');

Meedya.setAlbumDanD();

Meedya.editor = window.pell.init({
		element: document.getElementById('peditor'),
		defaultParagraphSeparator: 'p',
		onChange: function (html) {
			document.getElementById('albdsc').textContent = html;
		},
		actions: ['bold','italic','underline','heading2','quote','olist','ulist','link']
	});
Meedya.editor.content.innerHTML = document.getElementById('albdsc').textContent;

</script>
