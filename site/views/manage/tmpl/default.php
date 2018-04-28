<?php
defined('_JEXEC') or die;

$jdoc = JFactory::getDocument();
$jdoc->addStyleSheet('components/com_meedya/static/css/gallery.css'.$this->bgt);
$jdoc->addStyleSheet('components/com_meedya/static/css/manage.css'.$this->bgt);
JHtml::_('jquery.framework', false);
JHtml::_('bootstrap.modal');

//$jdoc->addScript('components/com_meedya/static/js/manage.js'.$this->bgt);
MeedyaHelper::addScript('manage');
$jdoc->addScriptDeclaration('var baseURL = "'.JUri::base().'";
//var aBaseURL = "'.JUri::base().'index.php?option=com_meedya&format=raw&mID='.urlencode($this->meedyaID).'&task=";
var myBaseURL = "'.JRoute::_('index.php?option=com_meedya', false).'";
var formTokn = "'.JSession::getFormToken().'";
');
function myModalButtons ($verb, $script)
{
	return '
	<button type="button" class="btn" data-dismiss="modal">'.JText::_('JCANCEL').'</button>
	<button type="button" id="creab" class="btn btn-disabled" onclick="'.$script.';" disabled>'.$verb.'</button>
';
}
function buildTree(array $albums, &$html, $paid = 0) {
	$branch = array();
	foreach ($albums as $alb) {
		if ($alb['paid'] == $paid) {
			$html[] = '<div data-aid="'.$alb['aid'].'" class="album" draggable="true">';
			$html[] = '<span class="icon-delete"> </span>';
			$html[] = '<span class="icon-edit"> </span>';
			$html[] = '<big><b>'.$alb['title'].'</b></big> ( '.$alb['items'].' items )';
			if ($alb['desc']) {
				$html[] = '<br />'.$alb['desc'];
			}
			$children = buildTree($albums, $html, $alb['aid']);
			if ($children) {
				$alb['children'] = $children;
			}
			$branch[] = $alb;
			$html[] = '</div>';
		}
	}
	return $branch;
}

$html = [];
buildTree($this->galStruct, $html);
//JHtml::_('meedya.buildTree', $this->galStruct, $html);	echo'<xmp>';var_dump($html);echo'</xmp>';
$this->btmscript[] = 'var albStruct = '. json_encode($this->galStruct).';';
$this->btmscript[] = '$("#gstruct .icon-edit").on("click", function () { albEdtAction(this); });';
$this->btmscript[] = '$("#gstruct .icon-delete").on("click", function () { albDelAction(this); });';
$this->btmscript[] = 'AArrange.init("gstruct","album");';

$hasImport = JFolder::exists($this->gallpath.'/import');
?>
<style>
.modal-body {padding:1em;width:100%;box-sizing:border-box}
.modal-backdrop.fade.in {opacity:0.2}
#trashall {margin:0 6px 0 0;position:relative;bottom:1px}
#trashall + label {display:inline}
#gstruct div {
	border: 1px solid #AAA;
	border-radius: 5px;
	margin: 12px;
	padding: 8px;
	background-color: white;
}
#gstruct div.over {
	background-color: #EF6;
}
#gstruct .icon-edit {
	color: #FD0;
	cursor: pointer;
}
#gstruct .icon-edit:hover {
	color: orange;
}
#gstruct .icon-delete {
	color: #FDD;
	float: right;
	cursor: pointer;
}
#gstruct .icon-delete:hover {
	color: #F33;
}
</style>
<div class="meedya-gallery">
	<?php if ($this->manage) echo JHtml::_('meedya.manageMenu', 1); ?>
	<?php echo JHtml::_('meedya.pageHeader', $this->params, $this->action.'XXXX'); ?>
	<div id="toolbar">
		<a href="<?php echo JRoute::_('index.php?option=com_meedya&task=manage.doUpload&aid=0', false); ?>">Upload Items</a>
		<a href="#newalbdlg" data-toggle="modal">New Album</a>
	<?php if ($hasImport): ?>
		<a href="#importdlg" data-toggle="modal">Import Items</a>
	<?php endif; ?>
	</div>
	<div>Total storage: <?=MeedyaHelper::formatBytes($this->totStore)?></div>
	<div id="gstruct"><div data-aid="0" class="album">
	<?php echo implode("\n",$html); ?>
	</div></div>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
<div id="delact" tabindex="-1" class="modal hide fade">
	<div class="modal-body">
		<?php echo JText::_('COM_MEEDYA_CREATE_DELETE_ALBUM_BLURB'); ?><br /><br />
		<input type="checkbox" name="trashall" id="trashall" value="true" /><label for="trashall"><?php echo JText::_('COM_MEEDYA_CREATE_DELETE_ALL_IMAGES'); ?></label>
	</div>
	<div class="modal-footer">
		<?php echo JHtml::_('meedya.modalButtons', 'COM_MEEDYA_CREATE_DELETE_ALBUM','deleteAlbum(this)', 'deliB', false); ?>
	</div>
</div>
<?php
echo JHtml::_(
	'bootstrap.renderModal',
	'newalbdlg',
	array(
		'title' => JText::_('COM_MEEDYA_CREATE_NEW_ALBUM'),
		'footer' => JHtml::_('meedya.modalButtons', 'COM_MEEDYA_H5U_CREALBM','createAlbum(this)', 'creab'),
		'__footer' => myModalButtons(JText::_('COM_MEEDYA_H5U_CREALBM'),'createAlbum(this)'),
		'modalWidth' => '40'
	),
	$this->loadTemplate('newalb')
	);
?>
<?php if ($hasImport):
echo JHtml::_(
	'bootstrap.renderModal',
	'importdlg',
	array(
		'title' => JText::_('COM_MEEDYA_IMPORT_ITEMS'),
		'footer' => JHtml::_('meedya.modalButtons', 'COM_MEEDYA_IMPORT','importItems(this)', 'imporb'),
		'modalWidth' => '30'
	),
	$this->loadTemplate('import')
	);
endif;
?>
<script>
	var alb2delete = 0;
	function selAlbum (evt, elm) {
		jQuery(elm).toggleClass('aselect');
	}
	function deleteAlbum (elm) {
		//alert($(elm).parent().attr('data-aid'));
		var wipe = document.getElementById('trashall').checked ? '&wipe=1' : '';
		window.location = '<?=JRoute::_('index.php?option=com_meedya&task=manage.delAlbum&aid=', false)?>' + alb2delete + wipe;
	}
	function albEdtAction (elm) {
		var alb2edit = $(elm).parent().attr('data-aid');
		window.location = '<?=JRoute::_('index.php?option=com_meedya&view=manage&layout=albedit&aid=', false)?>' + alb2edit;
	}
	function albDelAction (elm) {
		//alert($(elm).parent().attr('data-aid'));
		alb2delete = $(elm).parent().attr('data-aid');
		jQuery("#delact").modal();
	}
</script>