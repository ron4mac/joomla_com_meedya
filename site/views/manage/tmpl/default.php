<?php
defined('_JEXEC') or die;

	//jimport( 'joomla.html.editor' );

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::stylesheet('components/com_meedya/static/css/manage.css');
JHtml::stylesheet('components/com_meedya/static/css/jqModal.css');
JHtml::stylesheet('components/com_meedya/static/css/ui.css');
//JHtml::_('jquery.ui');
JHtml::_('jquery.framework', false);
JHtml::_('behavior.modal');
$jdoc = JFactory::getDocument();
//$jdoc->addScriptDeclaration(JEditor::getInstance(JFactory::getConfig()->get('editor')));
$jdoc->addScript('components/com_meedya/static/js/jqModal.js');
$jdoc->addScript('components/com_meedya/static/js/ui.js');
	//create an unused editor and get its content in order to force the editor javascript to load
	//$fedit = JEditor::getInstance(JFactory::getConfig()->get('editor'));
	//$fedit->getContent($fedit);
?>
<div class="meedya-gallery">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
<div id="toolbar">
	<a href="<?php echo JRoute::_('index.php?option=com_meedya&task=manage.doUpload&aid=0', false); ?>">Upload Images</a>
	<a href="#" onclick="return doDelAct(event, 0)">Delete Selected</a>
	<a href="<?php echo JRoute::_('index.php?option=com_meedya&view=manage&layout=newalb&tmpl=component', false); ?>" class="modal" rel="{size:{x:550,y:260},classWindow:'newalb'}">New Album</a>
</div>
<div>Total storage: <?=MeedyaHelper::formatBytes($this->totStore)?></div>
<?php
	foreach ($this->items as $item) {
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
<div id="element_to_pop_up" class="jqmWindow">
	<div class="bpDlgHdr"><span class="bpDlgTtl">TITLE</span><span class="button jqmClose"><img src="components/com_meedya/static/css/closex.png" alt="close" /></span></div>
	<div class="bpDlgCtn"><form class="bp-dctnt" name="myUIform" onsubmit="return false"></form></div>
	<div class="bpDlgFtr"><div class="bp-bttns"></div></div>
</div>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
<div id="delact" class="jqmWindow" title="Delete Album">
	Deleting an album, by default, does not delete the images in the album. Are you sure you want to delete the album(s)?<br /><br />
	<input type="checkbox" name="trashall" id="trashall" value="true" /> Delete all its images, as well.
</div>
<script>
	function selAlbum (evt, elm) {
		$(elm).toggleClass('aselect');
	}
//	function doNewAlb (evt, aid) {
//		evt.preventDefault();
//	}
	function doDelAct (evt, aid) {
		evt.preventDefault();
		var albs = $('.meedya-gallery .aselect');
		if (albs.length) {
			myOpenDlg(evt,delDlg,{'old':'AAA','new':'BBB'});
		} else {
			alert('No albums are selected.');
		}
		return false;
	}
	function doEdtAct (evt, aid) {
	//	window.location = 'index.php?option=com_meedya&view=amanage&aid=' + aid;
		window.location = 'index.php?option=com_meedya&view=manage&aid=' + aid;
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
					aray.push($(albs[i]).data('aid'));
				}
				var frm = document.myUIform;
				var nurl = 'index.php?option=com_meedya&view=manage&task=manage.delAlbums&albs=' + aray.join("|") + (frm.trashall.checked ? '&wipe=true' : '');
				//window.location = window.location + '?task=manage.delAlbums&albs=' + aray.join("|");
				window.location = nurl;
				}
			}
		};
</script>