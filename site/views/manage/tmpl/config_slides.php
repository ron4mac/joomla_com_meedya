<?php
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
//JHtml::stylesheet('components/com_meedya/static/js/rscp/colorPicker.css');

JHtml::_('jquery.framework', false);
JHtml::_('behavior.colorpicker');

$jdoc = JFactory::getDocument();
$jdoc->addScript('components/com_meedya/static/js/slide_config.js');
//$jdoc->addScript('components/com_meedya/static/js/rscp/jquery.colorPicker.min.js');

if ($this->album) {
//	$thead = '<img src="plugins/html5slideshow/css/slideshow.png" style="vertical-align:text-bottom" alt="" /> '.$JText::_('cfgtitle'). helpButton('usr');
//	$thead .= ' :: ' . $albname;
} else {
//	$thead = $JText::_('html5slide')." - ".$lang_gallery_admin_menu['admin_lnk']. helpButton('adm|usr');
}
$cfg = $this->html5slideshowCfg;
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
		<input type="checkbox" name="ss[atAlbum]" {$atAlbum_checked} />
		<img src="components/com_meedya/static/img/slideshow.png" style="margin-left:12px;vertical-align:text-bottom" />
	</td>
</tr>
<tr>
	<td class="tableb">
		atThumbs
	</td>
	<td class="tableb">
		<input type="checkbox" name="ss[atThumbs]" {$atThumbs_checked} />
	</td>
</tr>
<tr>
	<td class="tableb">
		uAllow
	</td>
	<td class="tableb">
		<input type="checkbox" name="ss[uAllow]" {$uAllow_checked} />
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

$dcolors = explode(',', $cfg['dC']);

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
		<input type="checkbox" name="ss[newWin]" {$newWin_checked} />
	</td>
</tr>
<tr>
	<td class="tableb">
		imgSize
	</td>
	<td class="tableb">
		<select class="listbox" name="ss[imgSize]">
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
		<select class="listbox" name="ss[trnType]">
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
		<input type="checkbox" name="ss[shuffle]" {$shuffle_checked} />
	</td>
</tr>
<tr>
	<td class="tableb">
		autoPlay
	</td>
	<td class="tableb">
		<input type="checkbox" name="ss[autoPlay]" {$autoPlay_checked} />
	</td>
</tr>
<tr>
	<td class="tableb">
		seconds
	</td>
	<td class="tableb">
		<select class="listbox" name="ss[seconds]">{$choices}</select>
	</td>
</tr>
<tr>
	<td class="tableb">
		loopShow
	</td>
	<td class="tableb">
		<input type="checkbox" name="ss[loopShow]" {$loopShow_checked} />
	</td>
</tr>
<tr>
	<td class="tableb">
		txt2show
	</td>
	<td class="tableb">
		<input type="checkbox" name="ss[dispTitl]" id="dispTitl" {$dispTitl_checked} onchange="setText()" /> <label for="dispTitl">title</label>
		<input type="checkbox" name="ss[dispDesc]" id="dispDesc" {$dispDesc_checked} onchange="setText()" style="margin-left:3em" /> <label for="dispDesc">caption</label>
	</td>
</tr>
<tr>
	<td class="tableb">
		iconset
	</td>
	<td class="tableb">
		<select id="h5iconsel" class="listbox" name="ss[iconset]" onchange="H5applyis(this)">{$ichoices}</select>
	</td>
</tr>
<tr>
	<td class="tableb">
		colors
	</td>
	<td class="tableb">
		<div id="smpl" style="width:25em;float:left;text-align:center;margin-right:20px">
			<div id="smpl_c">controls<img id="h5ssicons" src="components/com_meedya/static/img/sldctl/{$iconset}.png" style="margin-left:10px;padding:2px;vertical-align:middle;" alt="iconset"/></div>
			<div id="smpl_t" style="padding-top:1px">{$ptext}</div>
			<div id="smpl_p" style="height:160px"><img src="components/com_meedya/static/img/smplpic.jpg" alt="" /></div>
		</div>
		<table class="dspcolr" style="margin-top:1em">
			<tr><th></th><th>background</th><th>foreground</th></tr>
			<tr><td>ctlarea</td><td class="tac"><input id="h5ctrl_b" type="text" class="minicolors" onchange="setCtrlB(this.value)" name="ss[ctrl_b]" value="{$dcolors[0]}" /></td><td class="tac"><input id="h5ctrl_t" type="text" class="minicolors" name="ss[ctrl_t]" value="{$dcolors[1]}" /></td></tr>
			<tr><td>txtarea</td><td class="tac"><input id="h5text_b" type="text" class="minicolors" name="ss[text_b]" value="{$dcolors[2]}" /></td><td class="tac"><input id="h5text_t" type="text" class="minicolors" name="ss[text_t]" value="{$dcolors[3]}" /></td></tr>
			<tr><td>picarea</td><td class="tac"><input id="h5pica_b" type="text" class="minicolors" name="ss[backgrnd]" value="{$dcolors[4]}" /></td><td></td></tr>
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
<script>
$(document).ready(function() {
	$('#h5ctrl_b').data().minicolorsSettings.change = setCtrlB;
	$('#h5ctrl_t').data().minicolorsSettings.change = setCtrlT;
	$('#h5text_b').data().minicolorsSettings.change = setTextB;
	$('#h5text_t').data().minicolorsSettings.change = setTextT;
	$('#h5pica_b').data().minicolorsSettings.change = setPicaB;
});
</script>