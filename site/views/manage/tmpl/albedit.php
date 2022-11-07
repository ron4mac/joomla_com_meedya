<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

//MeedyaHelper::addStyle(['gallery','manage',['vendor/pell/'=>'pell.min']]);
MeedyaHelper::oneStyle('gMp');
//HTMLHelper::_('jquery.framework');
//MeedyaHelper::addScript(['common','manage','itm_dand','my_bb',['vendor/pell/'=>'pell.min']]);
MeedyaHelper::oneScript('cMabpe');

HTMLHelper::_('bootstrap.modal');

Text::script('COM_MEEDYA_REMOVE');
Text::script('COM_MEEDYA_VRB_REMOVE');
Text::script('COM_MEEDYA_ONE_PUBALB');
Text::script('COM_MEEDYA_LOSE_CHANGE');

$pubchk = $this->album['visib']==1 ? ' checked' : '';
$pubdis = $this->album['pub'] && $this->album['pub']!=$this->album['aid'] ? ' disssabled onclick="alert(Meedya._T(\'COM_MEEDYA_ONE_PUBALB\'));return false"' : '';
//var_dump($this->album);
?>
<style>
	#albForm {margin-top: 1em; border-top: 1px solid #CCC; padding-top: 0.5em;}
	.albman {display:inline-flex}
	.mitem, .litem {width:120px; height:120px}
	.modal-backdrop.fade.in {opacity:0.4}
	.modal-footer {padding: 8px 10px}
	.pell-content {height: 100px}
	.actbuts {margin-top: 1em}
	.albdesc > div {margin-top: 1em}
	#pubalb {/*vertical-align: text-bottom*/}
	.item img {user-drag: none;}
	.itemxxx img {
		-webkit-user-select: none;
		-webkit-touch-callout: none;
		-webkit-user-drag: none;
	}
	@media only screen and (min-width: 768px) {
		/* For desktop: */
		.item imgxxx {-webkit-user-drag: inherit;}
	}
}
</style>
<div class="meedya-gallery">
<?php if ($this->manage) echo HTMLHelper::_('meedya.manageMenu', $this->userPerms, 0, $this->itemId); ?>
<h3><?=Text::_('COM_MEEDYA_ALBEDIT')?> <?=$this->album['title']?></h3>
<button type="button" class="<?=M34C::btn('ss')?>" id="albsavb" onclick="Meedya.saveAlbum()"><?=Text::_('COM_MEEDYA_SAVE')?></button>
<button type="button" class="<?=M34C::btn('ss')?>" onclick="Meedya.cancelEdt()"><?=Text::_('JCANCEL')?></button>
<form action="<?=Route::_('index.php?option=com_meedya&Itemid='.$this->itemId)?>" id="albForm" name="albForm" method="POST">
	<div class="albman">
		<div class="albprp">
			<?=Text::_('COM_MEEDYA_TITLE')?><br>
			<input type="text" name="albttl" value="<?=$this->album['title']?>" onchange="Meedya.albdirt(this)" /><br>
			<?=Text::_('COM_MEEDYA_ALBTHM')?><br>
			<div class="albthm" id="albthm">
				<img id="albthmimg" src="<?=$this->aThum?>" width="120px" height="120px" title="album thumbnail image" alt="album thumbnail" />
			</div>
			<input type="hidden" name="albthmid" id="albthmid" value=<?=$this->album['thumb']?> />
		</div>
		<div class="albdesc">
			<div>
				<input type="checkbox" name="pubalb" id="pubalb" onchange="Meedya.albdirt(this)" value="1"<?=$pubchk.$pubdis?>> <label for="pubalb" style="display:inline"><?=Text::_('COM_MEEDYA_MAKPUB')?></label>
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
	<input type="hidden" name="thmord" value="_" />
	<input type="hidden" name="referer" value="<?=base64_encode($this->referer)?>" />
	<?=HTMLHelper::_('form.token')?>
</form>

<div class="actbuts">
	<?php //echo HTMLHelper::_('meedya.actionButtons', ['sela','seln','edts','movs','rems']); ?>
	<?php echo HTMLHelper::_('meedya.actionButtons', ['sela','seln']); ?>
	<?php echo HTMLHelper::_('meedya.actionSelect', ['edts','movs','rems']); ?>
</div>
<form action="<?=Route::_('index.php?option=com_meedya&Itemid='.$this->itemId)?>" method="POST" name="adminForm" id="adminForm">
<div id="area" style="display:flex;flex-wrap:wrap;-webkit-user-select:none;">
<?php
	foreach ($this->items as $item) {
		if (!$item) continue;
		echo HTMLHelper::_('meedya.imageThumbElement', (object)$this->getItemFile($item), true);
	}
?>
<!-- 	<div id="itmend" class="noitem item"></div> -->
</div>
<input type="hidden" name="task" value="manage.editImgs" />
<input type="hidden" name="fromalb" value="" />
<input type="hidden" name="albumid" value="" />
<input type="hidden" name="nualbnam" value="" />
<input type="hidden" name="nualbpar" value="" />
<input type="hidden" name="nualbdesc" value="" />
<?php echo HTMLHelper::_('form.token'); ?>
</form>
</div>

<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>

<script>
Meedya.albdirty = false;
Meedya.albdirt = () => {
	let sb = Meedya._id('albsavb');
	sb.classList.remove('btn-secondary');
	sb.classList.add('btn-warning');
	Meedya.albdirty = true;
}
Meedya.cancelEdt = () => {
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
		onChange: (html) => {
			document.getElementById('albdsc').textContent = html;
			Meedya.albdirt();
		},
		actions: ['bold','italic','underline','heading2','quote','olist','ulist','link']
	});
Meedya.editor.content.innerHTML = document.getElementById('albdsc').textContent;

</script>
<?php
$mmdl = HTMLHelper::_(
	'bootstrap.renderModal',
	'mov2albdlg',
	[
		'title' => Text::_('COM_MEEDYA_MANAGE_MOV2ALBUM'),
		'footer' => HTMLHelper::_('meedya.modalButtons', 'COM_MEEDYA_MANAGE_MOVE_ITEMS', 'Meedya.movItems2Album(this)', 'creab'),
		//'modalWidth' => '40'
	],
	'',	//$this->loadTemplate('mov2alb')
	);
echo str_replace(' modal-lg', '', $mmdl);
