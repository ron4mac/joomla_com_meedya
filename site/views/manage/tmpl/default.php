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
use Joomla\CMS\Session\Session;

Text::script('COM_MEEDYA_MOVE_FAIL');
Text::script('COM_MEEDYA_IMPORT');

MeedyaHelper::addStyle('gallery');
MeedyaHelper::addStyle('manage');
HTMLHelper::_('jquery.framework');
//HTMLHelper::_('jquery.framework', false);
//HTMLHelper::_('bootstrap.modal');
MeedyaHelper::addScript('manage');
$this->jDoc->addScriptDeclaration('
Meedya.rawURL = "'.Route::_('index.php?option=com_meedya&format=raw&Itemid='.$this->itemId, false).'";
Meedya.formTokn = "'.Session::getFormToken().'";
');

function buildTree(array $albums, &$html, $paid = 0) {
	$branch = [];
	foreach ($albums as $alb) {
		if ($alb['paid'] == $paid) {
			$html[] = '<div data-aid="'.$alb['aid'].'" class="album" draggable="true">';
			$html[] = '<span class="icon-delete" title="Delete Album"> </span>';
			$html[] = '<span class="icon-edit" title="Edit Album"> </span>';
			$html[] = '<big><b>'.$alb['title'].'</b></big> ( '.$alb['items'].' items )';
			$html[] = '<span class="icon-upload" title="Upload to Album"> </span>';
		//	if ($alb['desc']) {
		//		$html[] = '<br />'.$alb['desc'];
		//	}
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
//HTMLHelper::_('meedya.buildTree', $this->galStruct, $html);	echo'<xmp>';var_dump($html);echo'</xmp>';
//$this->btmscript[] = 'var albStruct = '. json_encode($this->galStruct).';';
$this->btmscript[] = 'jQuery("#gstruct .icon-edit").on("click", function (e) { Meedya.albEdtAction(e,this); });';
$this->btmscript[] = 'jQuery("#gstruct .icon-upload").on("click", function (e) { Meedya.albUpldAction(e,this); });';
$this->btmscript[] = 'jQuery("#gstruct .icon-delete").on("click", function (e) { Meedya.albDelAction(e,this); });';
$this->btmscript[] = 'Meedya.AArrange.init("gstruct","album");';

$hasImport = JFolder::exists($this->gallpath.'/import');
?>
<style>
.modal-body {
	padding:1em;
	width:100%;
	box-sizing:border-box;
	max-height: 400px;
	overflow-y: scroll;
}
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
	color: #0BD;
	cursor: pointer;
}
#gstruct .icon-edit:hover {
	color: orange;
}
#gstruct .icon-upload {
	color: #0BD;
	cursor: pointer;
}
#gstruct .icon-upload:hover {
	color: blue;
}
#gstruct .icon-delete {
	color: #FDD;
	float: right;
	cursor: pointer;
}
#gstruct .icon-delete:hover {
	color: #F33;
}
#gstruct .slctd {
	background-color: #E0E8FF;
}
#myProgress { width:100%; background-color:#ddd; display:none; }
#myBar { width:0; background-color:#4CAF50; font-size:larger; padding:3px 0; }
</style>
<div class="meedya-gallery">
	<?php if ($this->manage) echo HTMLHelper::_('meedya.manageMenu', $this->userPerms, 0, $this->itemId); ?>
	<?php echo HTMLHelper::_('meedya.pageHeader', $this->params, $this->action/*.'XXXX'*/); ?>
	<div id="toolbar">
		<!-- <a href="#" onclick="Meedya.goUpload(event)" title="Upload Files">Upload</a> -->
		<!-- <a href="<?php echo Route::_('index.php?option=com_meedya&task=manage.doUpload&aid=0&Itemid='.$this->itemId, false); ?>">Upload Items</a> -->
		<a href="#newalbdlg" data-toggle="modal" onclick="Meedya.setDlgParAlb();">New Album</a>
	<?php if ($hasImport): ?>
		<a href="#importdlg" data-toggle="modal">Import Items</a>
	<?php endif; ?>
	</div>
	<div>Total storage: <?=MeedyaHelper::formatBytes($this->totStore)?></div>
	<div id="myProgress"><div id="myBar"> <span>&nbsp;Importing...</span></div></div>
	<div id="gstruct"><div data-aid="0" class="album">
	<?php echo implode("\n",$html); ?>
	</div></div>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
<div id="delact" tabindex="-1" class="modal hide fade jviewport-width30">
	<div class="modal-body">
		<?php echo Text::_('COM_MEEDYA_CREATE_DELETE_ALBUM_BLURB'); ?><br /><br />
		<input type="checkbox" name="trashall" id="trashall" value="true" /><label for="trashall"><?php echo Text::_('COM_MEEDYA_CREATE_DELETE_ALL_IMAGES'); ?></label>
	</div>
	<div class="modal-footer">
		<?php echo HTMLHelper::_('meedya.modalButtons', 'COM_MEEDYA_CREATE_DELETE_ALBUM','Meedya.deleteAlbum(this)', 'deliB', false); ?>
	</div>
</div>
<?php
echo HTMLHelper::_(
	'bootstrap.renderModal',
	'newalbdlg',
	['title' => Text::_('COM_MEEDYA_CREATE_NEW_ALBUM'),
	'footer' => HTMLHelper::_('meedya.modalButtons', 'COM_MEEDYA_H5U_CREALBM','Meedya.ae_createAlbum(this)', 'creab'),
	'modalWidth' => '40'],
	$this->loadTemplate('newalb')
	);
?>
<?php if ($hasImport):
echo HTMLHelper::_(
	'bootstrap.renderModal',
	'importdlg',
	['title' => Text::_('COM_MEEDYA_IMPORT_ITEMS'),
	'footer' => HTMLHelper::_('meedya.modalButtons', Text::_('COM_MEEDYA_IMPORT'),'importItems(this)', 'imporb'),
	'modalWidth' => '40'],
	$this->loadTemplate('import')
	);
endif;
?>
<script>
	Meedya.alb2delete = 0;
	Meedya.deleteAlbum = function (elm) {
		//alert(jQuery(elm).parent().attr('data-aid'));
		var wipe = _id('trashall').checked ? '&wipe=1' : '';
		window.location = '<?=Route::_('index.php?option=com_meedya&task=manage.delAlbum&Itemid='.$this->itemId.'&aid=', false)?>' + this.alb2delete + wipe;
	}
	Meedya.albEdtAction = function (e, elm) {
		_pd(e);
		var alb2edit = jQuery(elm).parent().attr('data-aid');
		//window.location = '<?=Route::_('index.php?option=com_meedya&view=manage&layout=albedit&aid=', false)?>' + alb2edit;
		window.location = '<?=Route::_('index.php?option=com_meedya&task=manage.editAlbum&Itemid='.$this->itemId.'&aid=', false)?>' + alb2edit;
	}
	Meedya.albUpldAction = function (e, elm) {
		_pd(e);
		var alb2upld = jQuery(elm).parent().attr('data-aid');
		//window.location = '<?=Route::_('index.php?option=com_meedya&view=manage&layout=albedit&aid=', false)?>' + alb2edit;
		window.location = '<?=Route::_('index.php?option=com_meedya&task=manage.doUpload&Itemid='.$this->itemId.'&aid=', false)?>' + alb2upld;
	}
	Meedya.albDelAction = function (e, elm) {
		_pd(e);
		//alert(jQuery(elm).parent().attr('data-aid'));
		Meedya.alb2delete = jQuery(elm).parent().attr('data-aid');
		jQuery("#delact").modal();
	}
	Meedya.goUpload = function (e) {
		_pd(e);
		window.location = '<?=Route::_('index.php?option=com_meedya&task=manage.doUpload&Itemid='.$this->itemId.'&aid=', false)?>' + Meedya.AArrange.selalb();
	}
<?php if ($hasImport): ?>
	function importItems (dlg) {
	//	jQuery("#importdlg input:checked").each(function(){console.log(jQuery(this).val())});
		var fld = jQuery("#importdlg .impfld input:checked").val();
		var prms = {task:'manage.impstps', 'fld':fld};
		var fast = jQuery("#fast").prop('checked');
		jQuery.post(Meedya.rawURL, prms, function(data) {
			console.log(data);
			meedya_importer.init(data, Meedya.AArrange.selalb(), fast);
		},'json');
	}

	var meedya_importer = (function($) {

		var steps = [],
			sct = 0,
			sx = 0,
			fast = false,
			pb = null,
			aid = [];

		// post and get response
		function pagr (dat, cb) {
			dat.task = 'manage.impact';
//			dat.format = 'raw';	console.log(dat);
			$.post(Meedya.rawURL, dat, function(d,t){ cb(d); }, 'json');
		}

		function _L (v)
		{
			console.log(v);
		}

		function process (stp) {
			var wp = sx/sct;
			if (wp>1.0) wp = 1;
			pb.style.width = wp*100 + '%';
			if (stp)
			switch (stp.act) {
				case 'na':
					stp.pid = aid[0];
					pagr(stp, function(r){ _L(r); aid.unshift(r.r); process(steps[sx++]); });
					break;
				case 'ii':
					stp.aid = aid[0];
					stp.fat = fast ? 1 : 0;
					pagr(stp, function(r){ _L(r); if (!r.r) alert("Error:"+r.r); process(steps[sx++]); });
					break;
				case 'pa':
					aid.shift();
					process(steps[sx++]);
					break;
			}
			else window.location.reload(true);
		}

		return {
			init: function (stps, baid, fat) {
				$('#importdlg').modal('hide');
				steps = stps;
				aid.unshift(baid);
				fast = fat;
				sct = steps.length;
				pb = document.getElementById("myBar");
				$('#myProgress').show();
				process(steps[sx++]);
			}
		};
	})(jQuery);

<?php endif; ?>
</script>
