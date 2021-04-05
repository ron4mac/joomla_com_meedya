<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2021 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

function buildTree(array $albums, &$html, $paid = 0) {
	$branch = [];
	foreach ($albums as $alb) {
		if ($alb['paid'] == $paid) {
		//	$itms = $alb['items'] ? count(explode('|',$alb['items'])) : 'no';
		//	$html[] = '<div data-aid="'.$alb['aid'].'" class="album" draggable="true"><big><b>'.$alb['title'].'</b></big> ( '.$itms.' items )';
			$html[] = '<div data-aid="'.$alb['aid'].'" class="album" draggable="true">';
			$html[] = '<span class="icon-delete"> </span><span class="icon-edit"> </span>';
			$html[] = '<big><b>'.$alb['title'].'</b></big> ( '.$alb['items'].' items )';
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
$this->btmscript[] = 'var albStruct = '. json_encode($this->galStruct).';';
$this->btmscript[] = '$("#gstruct .icon-edit").on("click", function () { albEdtAction(this); });';
$this->btmscript[] = '$("#gstruct .icon-delete").on("click", function () { albDelAction(this); });';
$this->btmscript[] = 'AArrange.init("gstruct","album");';
?>
<!-- <script type="text/javascript">
;	var albStruct = <?=json_encode($this->galStruct)?>;
</script> -->
<style>
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
<h2>Content of first panel goes here!</h2>
<p>You can use JLayouHelper to render a layout if you want to</p>
<div id="gstruct"><div data-aid="0" class="album">
<?php echo implode("\n",$html); ?>
</div></div>
