<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
* @since		1.3.2
*/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Layout\LayoutHelper;

Text::script('COM_MEEDYA_MOVE_FAIL');
Text::script('COM_MEEDYA_IMPORT');

//MeedyaHelper::addStyle(['gallery','manage']);
MeedyaHelper::oneStyle('gM');
//HTMLHelper::_('jquery.framework');
//MeedyaHelper::addScript(['common','manage','alb_dand']);
MeedyaHelper::oneScript('MA');

function buildTree(array $albums, &$html, $paid = 0) {
	$branch = [];
	foreach ($albums as $alb) {
		if ($alb['paid'] == $paid) {
			$xclass = $alb['isClone'] ? (' aclone" data-oaid="'.$alb['oaid']) : '';
			$html[] = '<div data-aid="'.$alb['aid'].'" class="album'.$xclass.'" draggable="true">';
			$html[] = '<span class="icon-delete" title="Delete Album"> </span>';
			//$xclass = $alb['isClone'] ? ' clone' : '';
			$html[] = '<span class="icon-edit'.$xclass.'" title="Edit Album"> </span>';
			$info = $alb['isClone'] ? ' clone ' : ($alb['items'].' items');
			$html[] = '<big><b>'.$alb['title'].'</b></big> ( '.$info.' )';
			if (!$alb['isClone']) $html[] = '<span class="icon-upload" title="Upload to Album"> </span>';
			if ($alb['visib']==1) {
				$html[] = '<span class="pubalb">'.Text::_('COM_MEEDYA_PUBLIC').'</span>';
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
//HTMLHelper::_('meedya.buildTree', $this->galStruct, $html);	echo'<xmp>';var_dump($html);echo'</xmp>';
//$this->btmscript[] = 'var albStruct = '. json_encode($this->galStruct).';';

// use a catch-all click handler for the albums list
$this->btmscript[] = 'Meedya._ae("gstruct", "click", (e) => Meedya.albAction(e) );';
// initialize dragndrop for the albums list
$this->btmscript[] = 'Meedya.AArrange.init("gstruct","album");';
// cause input focus for new album dialog
$this->btmscript[] = 'Meedya._ae("newalbdlg", "shown.bs.modal", () => Meedya._id("nualbnam").focus() );';
// set new album dlg parent to current list selection
$this->btmscript[] = 'Meedya._ae("newalbdlg", "show.bs.modal", () => { Meedya._id("h5u_palbum").value = Meedya.AArrange.selalb(); });';

// setup clone album dlg initialization
$this->btmscript[] = 'Meedya._ae("clnalbdlg", "show.bs.modal", (evt) => {
	//console.log(evt);
	if (evt.relatedTarget.tagName == "A") {
		Meedya._id("clalbnam").value = "";
		Meedya._id("cln_palbum").value = 0;
		Meedya._id("clalbdesc").innerHTML = "";
	}
});';

$hasImport = Folder::exists($this->gallpath.'/import');
?>
<style>
/*.modal-body {
	padding:1em;
	width:100%;
	box-sizing:border-box;
	max-height: 400px;
	overflow-y: scroll;
}
.modal-backdrop.fade.in {opacity:0.2}*/
.meedya-gallery a.disabled {pointer-events:none; opacity:.5}
#trashall {margin:0 6px 0 0;position:relative;bottom:1px;vertical-align:middle;}
#trashall + label {display:inline}
/*#gstruct div {
	border: 1px solid #AAA;
	border-radius: 5px;
	margin: 12px;
	padding: 8px;
	background-color: white;
	color: #555;
}
#gstruct div.album.aclone {
	background-image: url(css/clone2.png);
	border-style: dotted;
	border-width: 2px;
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
	color: #EAA;
	float: right;
	cursor: pointer;
}
#gstruct .icon-delete:hover {
	color: #F33;
}
#gstruct .slctd {
	background-color: #E0E8FF;
	cursor: grab;
}
#gstruct .album.moving {
	cursor: grabbing;
}
.pubalb {
	font-variant-caps: all-small-caps;
	font-size: large;
	color: crimson;
	margin-left: 1em;
}*/
#myProgress { width:100%; background-color:#ddd; display:none; }
#myBar { width:0; background-color:#4CAF50; font-size:larger; padding:3px 0; }
</style>
<div class="meedya-gallery">
	<?php if ($this->manage) echo HTMLHelper::_('meedya.manageMenu', $this->userPerms, 0, $this->itemId); ?>
	<?php echo HTMLHelper::_('meedya.pageHeader', $this->params, $this->action/*.'XXXX'*/); ?>
	<div id="toolbar">
		<a href="#newalbdlg" data-toggle="modal" data-bs-toggle="modal"><?=Text::_('COM_MEEDYA_NEW_ALBUM')?></a>&nbsp;
		<a href="#clnalbdlg" data-toggle="modal" data-bs-toggle="modal" id="clone_a" class="disabled"><?=Text::_('COM_MEEDYA_CLNALBM')?></a>
	<?php if ($hasImport): ?>
		<a href="#importdlg" data-toggle="modal"><?=Text::_('COM_MEEDYA_IMPORT')?></a>
	<?php endif; ?>
	</div>
	<div><?=Text::_('COM_MEEDYA_TOTSTORE')?> <?=MeedyaHelper::formatBytes($this->totStore)?></div>
	<div id="myProgress"><div id="myBar"> <span>&nbsp;<?=Text::_('COM_MEEDYA_IMPORTING')?></span></div></div>
	<div id="gstruct"><div data-aid="0" class="album">
	<?php echo implode("\n",$html); ?>
	</div></div>
</div>
<div class="page-footer">
	<?php echo $this->pagination->getListFooter(); ?>
</div>
<?php
	echo LayoutHelper::render('newalbum', ['script'=>'Meedya.ae_createAlbum(this)', 'albums'=>$this->albums]);
	echo LayoutHelper::render('delalbum', ['itemId'=>$this->itemId]);
	echo LayoutHelper::render('clnalbum', ['itemId'=>$this->itemId, 'script'=>'Meedya.ae_cloneAlbum(this)', 'albums'=>$this->albums]);
	echo LayoutHelper::render('clnalbedt', ['script'=>'Meedya.ae_cloneAlbSave(this)', 'albums'=>$this->albums]);
?>
<?php if ($hasImport):
echo HTMLHelper::_(
	'bootstrap.renderModal',
	'importdlg',
	['title' => Text::_('COM_MEEDYA_IMPORT_ITEMS'),
	'footer' => HTMLHelper::_('meedya.modalButtons', Text::_('COM_MEEDYA_IMPORT'),'importItems(this)', 'imporb'),
	//'modalWidth' => '40'
	],
	$this->loadTemplate('import')
	);
endif;
?>
<script>
	Meedya.alb2delete = 0;
	Meedya.clone_a = document.getElementById('clone_a');
<?php if ($hasImport): ?>
	function importItems (dlg) {
	//	jQuery("#importdlg input:checked").each(function(){console.log(jQuery(this).val())});
		var fld = jQuery("#importdlg .impfld input:checked").val();
		var prms = {task:'manage.impstps', 'fld':fld};
		var fast = jQuery("#fast").prop('checked');
		jQuery.post(Meedya.rawURL, prms, function(data) {
			//console.log(data);
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
			//console.log(v);
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
