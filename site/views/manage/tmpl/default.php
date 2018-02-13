<?php
defined('_JEXEC') or die;

	//jimport( 'joomla.html.editor' );

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::stylesheet('components/com_meedya/static/css/manage.css');
//JHtml::stylesheet('components/com_meedya/static/css/jqModal.css');
//JHtml::stylesheet('components/com_meedya/static/css/ui.css');
//JHtml::_('jquery.ui');
JHtml::_('jquery.framework');
JHtml::_('bootstrap.modal');
$jdoc = JFactory::getDocument();
//$jdoc->addScriptDeclaration(JEditor::getInstance(JFactory::getConfig()->get('editor')));
//$jdoc->addScript('components/com_meedya/static/js/jqModal.js');
//$jdoc->addScript('components/com_meedya/static/js/ui.js');
	//create an unused editor and get its content in order to force the editor javascript to load
	//$fedit = JEditor::getInstance(JFactory::getConfig()->get('editor'));
	//$fedit->getContent($fedit);
function myModalButtons ($verb, $script)
{
	return '
	<button type="button" class="btn" data-dismiss="modal">'.JText::_('JCANCEL').'</button>
	<button type="button" id="creab" class="btn btn-disabled" onclick="'.$script.';" disabled>'.$verb.'</button>
';
}
?>
<style>
.modal-body {padding:1em;width:100%;box-sizing:border-box}
.modal-backdrop.fade.in {opacity:0.4}
#trashall {margin:0 6px 0 0;position:relative;bottom:1px}
#trashall + label {display:inline}
</style>
<div class="meedya-gallery">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
<div id="toolbar">
	<a href="<?php echo JRoute::_('index.php?option=com_meedya&task=manage.doUpload&aid=0', false); ?>">Upload Images</a>
	<a href="#" onclick="return doDelAct(event, 0)">Delete Selected</a>
	<a href="#newalbdlg" data-toggle="modal">New Album</a>
</div>
<div>Total storage: <?=MeedyaHelper::formatBytes($this->totStore)?></div>
<?php
	//echo'<xmp>';var_dump($this->albums);echo'</xmp>';
	foreach ($this->albums as $item) {
		echo '<div class="albdsp"><div class="albbar" data-aid="'.$item->aid.'" onclick="selAlbum(event,this)"><b>'.$item->title.'</b><!-- <div class="albact albdelb" title="Delete album" onclick="doDelAct(event,'.$item->aid.');"></div> --><div class="albact albedtb" title="Edit album" onclick="doEdtAct(event,'.$item->aid.');"></div></div><div class="albthm">';
		$pics = $item->items ? explode('|', $item->items) : array();
		$thum = $this->getAlbumThumb($item);
		echo '<img src="'.$thum.'" width="100px" height="100px" /></div>';
		echo '<div class="albprp">';
	//	echo '<div>'.$item->title.'</div>';
		echo '<div>'.$item->desc.'</div>';
		echo '<div>'.count($pics).' items in album</div>';
	//	echo '<div class="albbar"><div class="albact" onclick="doDelAct(event,3);">Delete</div></div>';
	//	echo '</div><div class="albbar"><div class="albact" onclick="doDelAct(event,3);">Delete</div></div></div>';
		echo '</div></div>';
	}
?>
<!--
<div id="element_to_pop_up" class="jqmWindow">
	<div class="bpDlgHdr"><span class="bpDlgTtl">TITLE</span><span class="button jqmClose"><img src="components/com_meedya/static/css/closex.png" alt="close" /></span></div>
	<div class="bpDlgCtn"><form class="bp-dctnt" name="myUIform" onsubmit="return false"></form></div>
	<div class="bpDlgFtr"><div class="bp-bttns"></div></div>
</div>
-->
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
		<?php echo 'no creab here!!'/*myModalButtons(JText::_('COM_MEEDYA_CREATE_DELETE_ALBUM'),'createAlbum(this)')*/; ?>
	</div>
</div>
<?php
echo JHtml::_(
	'bootstrap.renderModal',
	'newalbdlg',
	array(
		'title' => JText::_('COM_MEEDYA_CREATE_NEW_ALBUM'),
		'footer' => myModalButtons(JText::_('COM_MEEDYA_H5U_CREALBM'),'createAlbum(this)'),
		'modalWidth' => '80'
	),
	$this->loadTemplate('newalb')
	);
?>
<script>
	function selAlbum (evt, elm) {
		jQuery(elm).toggleClass('aselect');
	}
//	function doNewAlb (evt, aid) {
//		evt.preventDefault();
//	}
	function doDelAct (evt, aid) {
		evt.preventDefault();
		var albs = jQuery('.meedya-gallery .aselect');
		if (albs.length) {
			jQuery("#delact").modal();
			//myOpenDlg(evt,delDlg,{'old':'AAA','new':'BBB'});
		} else {
			alert('No albums are selected.');
		}
		return false;
	}
	function doEdtAct (evt, aid) {
	//	window.location = 'index.php?option=com_meedya&view=amanage&aid=' + aid;
		window.location = 'index.php?option=com_meedya&view=manage&layout=albedit&aid=' + aid;
	}
	var delDlg = {
		cselect: '#delact',
		ctainer: '.meedya-gallery',
		modal: true,
		buttons: {
			'Cancel`prm': function() {
				myCloseDlg(this);
				},
			Delete: function() {
				var albs = $('.meedya-gallery .aselect');
				var aray = [];
				for (var i=0; i < albs.length; i++) {
					aray.push(jQuery(albs[i]).data('aid'));
				}
				var frm = document.myUIform;
				var nurl = 'index.php?option=com_meedya&view=manage&task=manage.delAlbums&albs=' + aray.join("|") + (frm.trashall.checked ? '&wipe=true' : '');
				//window.location = window.location + '?task=manage.delAlbums&albs=' + aray.join("|");
				window.location = nurl;
				}
			}
		};
</script>