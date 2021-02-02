<?php
/**
 * @package		com_meedya
 * @copyright	Copyright (C) 2020 RJCreations. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');

JHtml::_('jquery.framework', false);

MeedyaHelper::addScript('slide_config');
MeedyaHelper::addScript('spectrum.min');

MeedyaHelper::addStyle('spectrum');

$this->jDoc->addScriptDeclaration('jQuery.fn.spectrum.defaults.showAlpha = true;
	jQuery.fn.spectrum.defaults.showInput = true;
	jQuery.fn.spectrum.defaults.preferredFormat = "rgb";');

if ($this->album) {
//	$thead = '<img src="plugins/html5slideshow/css/slideshow.png" style="vertical-align:text-bottom" alt="" /> '.$JText::_('cfgtitle'). helpButton('usr');
//	$thead .= ' :: ' . $albname;
} else {
//	$thead = $JText::_('html5slide')." - ".$lang_gallery_admin_menu['admin_lnk']. helpButton('adm|usr');
}
$cfg = $this->html5slideshowCfg;
echo <<<EOT
<form action="" method="post">
	<button type="submit" name="save" value="1" class="btn btn-primary pull-right">Save Slideshow Settings</button>
EOT;
echo '<table style="width:100%">';

if (!$this->album && $this->isAdmin) {
$atAlbum_checked = $cfg['aA'] ? 'checked="checked"' : '';
$atThumbs_checked = $cfg['aT'] ? 'checked="checked"' : '';
$uAllow_checked = $cfg['uA'] ? 'checked="checked"' : '';
echo <<<EOT
<tr>
	<td class="tableb">
		atAlbum
	</td>
	<td class="tableb">
		<input type="checkbox" value="1" name="ss[aA]" {$atAlbum_checked} />
		<img src="components/com_meedya/static/img/slideshow.png" style="margin-left:12px;vertical-align:text-bottom" />
	</td>
</tr>
<tr>
	<td class="tableb">
		atThumbs
	</td>
	<td class="tableb">
		<input type="checkbox" value="1" name="ss[aT]" {$atThumbs_checked} />
	</td>
</tr>
<tr>
	<td class="tableb">
		uAllow
	</td>
	<td class="tableb">
		<input type="checkbox" value="1" name="ss[uA]" {$uAllow_checked} />
	</td>
</tr>
EOT;
}

$iconset = $cfg['iS'];

$iconsets = JFolder::files(JPATH_COMPONENT.'/static/img/sldctl');	//form_get_foldercontent('plugins/html5slideshow/css/icons/', 'file', 'png');
$ichoices = '';
foreach ($iconsets as $value) {
	$info = pathinfo($value);
	$value =  basename($value,'.'.$info['extension']);
	$selected = $iconset == $value ? 'selected="selected"' : '';
	$ichoices .= "<option value=\"$value\" $selected>$value</option>";
}

$sizeSel = array('','','');
$sizeSel[$cfg['pS']] = ' selected="selected"';
$tranSel = array('n'=>'','d'=>'','s'=>'');
$tranSel[$cfg['tT']] = ' selected="selected"';

$newWin_checked = $cfg['nW'] ? 'checked="checked"' : '';
$shuffle_checked = $cfg['sI'] ? 'checked="checked"' : '';
$autoPlay_checked = $cfg['aP'] ? 'checked="checked"' : '';
$loopShow_checked = $cfg['lS'] ? 'checked="checked"' : '';
$dispTitl_checked = $cfg['vT'] ? 'checked="checked"' : '';
$dispDesc_checked = $cfg['vD'] ? 'checked="checked"' : '';

$ptext = '';
if ($cfg['vT']) $ptext = 'title';
if ($cfg['vT'] && $cfg['vD']) $ptext .= ' :: ';
if ($cfg['vD']) $ptext .= 'caption';

$seconds = $cfg['sD'];
$choices = '';
for ($value=3; $value<11; $value++) {
	$selected = $seconds == $value ? 'selected="selected"' : '';
	$choices .= "<option value=\"$value\" $selected>$value</option>";
}

$ctrlSet =	'<span class="icon-stop"> </span>'.
			'<span class="icon-arrow-first"> </span>'.
			'<span class="icon-arrow-left"> </span>'.
			'<span class="icon-pause"> </span>'.
			'<span class="icon-play"> </span>'.
			'<span class="icon-arrow-right"> </span>'.
			'<span class="icon-arrow-last"> </span>'.
			'<span class="icon-expand-2"> </span>'.
			'<span class="icon-contract-2"> </span>'.
			'<span class="icon-minus"> </span>'.
			'<span class="icon-plus"> </span>';

$dcolors = $cfg['dC'];	//explode(',', $cfg['dC']);

$action_sel = '';
if ($this->album) {
	$action_sel = <<<EOT
action:
<select class="listbox" name="ss[action]" style="margin-right:2em">
	<option value="sa">savalb</option>
	<option value="su">savusr</option>
	<option value="za">clralb</option>
	<option value="zu">clrusr</option>
</select>
EOT;
}

echo <<<EOT
<tr>
	<td class="tableb">
		newWin
	</td>
	<td class="tableb">
		<input type="checkbox" value="1" name="ss[nW]" {$newWin_checked} />
	</td>
</tr>
<tr>
	<td class="tableb">
		imgSize
	</td>
	<td class="tableb">
		<select class="listbox" name="ss[pS]">
			<option value="1"{$sizeSel[1]}>sizIntr</option>
			<option value="2"{$sizeSel[2]}>sizFull</option>
		</select>
	</td>
</tr>
<tr>
	<td class="tableb">
		trnType
	</td>
	<td class="tableb">
		<select class="listbox" name="ss[tT]">
			<option value="n"{$tranSel['n']}>imgNotr</option>
			<option value="d"{$tranSel['d']}>imgDzlv</option>
			<option value="s"{$tranSel['s']}>imgSlid</option>
		</select>
	</td>
</tr>
<tr>
	<td class="tableb">
		shuffle
	</td>
	<td class="tableb">
		<input type="checkbox" value="1" name="ss[sI]" {$shuffle_checked} />
	</td>
</tr>
<tr>
	<td class="tableb">
		autoPlay
	</td>
	<td class="tableb">
		<input type="checkbox" value="1" name="ss[aP]" {$autoPlay_checked} />
	</td>
</tr>
<tr>
	<td class="tableb">
		seconds
	</td>
	<td class="tableb">
		<select class="listbox" name="ss[sD]">{$choices}</select>
	</td>
</tr>
<tr>
	<td class="tableb">
		loopShow
	</td>
	<td class="tableb">
		<input type="checkbox" value="1" name="ss[lS]" {$loopShow_checked} />
	</td>
</tr>
<tr>
	<td class="tableb">
		txt2show
	</td>
	<td class="tableb">
		<input type="checkbox" value="1" name="ss[vT]" id="dispTitl" {$dispTitl_checked} onchange="setText()" /> <label for="dispTitl">title</label>
		<input type="checkbox" value="1" name="ss[vD]" id="dispDesc" {$dispDesc_checked} onchange="setText()" style="margin-left:3em" /> <label for="dispDesc">caption</label>
	</td>
</tr>
<tr>
	<td class="tableb">
		iconset
	</td>
	<td class="tableb">
		<select id="h5iconsel" class="listbox" name="ss[iS]" onchange="H5applyis(this)">{$ichoices}</select>
	</td>
</tr>
<tr>
	<td class="tableb">
		colors
	</td>
	<td class="tableb">
		<div id="smpl">
			<div>{$ctrlSet}</div>
			<div id="smpl_c">controls<img id="h5ssicons" src="components/com_meedya/static/img/sldctl/{$iconset}.png" alt="iconset"/></div>
			<div id="smpl_t">{$ptext}</div>
			<div id="smpl_p"><img src="components/com_meedya/static/img/smplpic.jpg" alt="" /></div>
		</div>
		<table class="dspcolr" style="margin-top:1em">
			<tr><th></th><th>background</th><th>foreground</th></tr>
			<tr>
				<td>ctlarea</td>
				<td class="tac"><input id="h5ctrl_b" type="text" name="ss[dC][]" value="{$dcolors[0]}" /></td>
				<td class="tac"><input id="h5ctrl_t" type="text" name="ss[dC][]" value="{$dcolors[1]}" /></td>
			</tr>
			<tr>
				<td>txtarea</td>
				<td class="tac"><input id="h5text_b" type="text" name="ss[dC][]" value="{$dcolors[2]}" /></td>
				<td class="tac"><input id="h5text_t" type="text" name="ss[dC][]" value="{$dcolors[3]}" /></td>
			</tr>
			<tr>
				<td>picarea</td><td class="tac"><input id="h5pica_b" type="text" name="ss[dC][]" value="{$dcolors[4]}" /></td>
				<td></td>
			</tr>
		</table>
	</td>
</tr>
EOT;


echo '</table>';

if ($this->album) echo "<input type=\"hidden\" name=\"album\" value=\"{$album}\" />";
//list($timestamp, $form_token) = getFormToken();
//echo "<input type=\"hidden\" name=\"form_token\" value=\"{$form_token}\" />";
//echo "<input type=\"hidden\" name=\"timestamp\" value=\"{$timestamp}\" />";
?>
	<input type="hidden" name="task" value="manage.saveConfig" />
	<input type="hidden" name="return" value="<?=base64_encode($_SERVER['REQUEST_URI'])?>" />
	<?=JHtml::_('form.token')?>
</form>
<script>
jQuery(document).ready(function() {
//	jQuery('#h5ctrl_b').colorPicker(PickerSkin);
//	jQuery('#h5ctrl_t').colorPicker({move:function(c){setCtrlT(c.toRgbString())}});
//	jQuery('#h5text_b').colorPicker({move:function(c){setTextB(c.toRgbString())}});
//	jQuery('#h5text_t').colorPicker({move:function(c){setTextT(c.toRgbString())}});
//	jQuery('#h5pica_b').colorPicker({move:function(c){setPicaB(c.toRgbString())}});
	jQuery('#h5ctrl_b').spectrum({move:function(c){setCtrlB(c.toRgbString())}});
	jQuery('#h5ctrl_t').spectrum({move:function(c){setCtrlT(c.toRgbString())}});
	jQuery('#h5text_b').spectrum({move:function(c){setTextB(c.toRgbString())}});
	jQuery('#h5text_t').spectrum({move:function(c){setTextT(c.toRgbString())}});
	jQuery('#h5pica_b').spectrum({move:function(c){setPicaB(c.toRgbString())}});
});
</script>
