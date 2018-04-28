<?php
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

MeedyaHelper::addScript('each');

JHtml::stylesheet('components/com_meedya/static/css/each.css');

if ($this->files) {
	foreach ($this->files as $file) {
		//$ftyp = cpg_get_type($row['filename']);
		//if ($ftyp['content'] != 'image') continue;
		$txtinfo = '';
		$txtinfo .= trim($file['title']);
		$txtinfo .= ($txtinfo ? ' ... ' : '') . trim($file['desc']);
		$fileentry = array(
				'fpath' => $this->gallpath .'/med/'. $file['file'],
				'title' => $txtinfo
				);
		$filelist[] = $fileentry;
	}
}

//echo'<xmp>';var_dump($this->files);echo'</xmp>';
?>
<script>
	var imagelist = <?=json_encode($filelist)?>;
	var startx = <?=$this->six?>
</script>
<div class="meedya-gallery">
<?php if ($this->params->def('show_page_heading', 1)) : ?>
	<h3>
		<?php echo $this->escape($this->title); ?>
	</h3>
<?php endif; ?>
	<span id="slidnum"> </span>
	<div class="controls">
		<span class="icon-arrow-first"> </span>
		<span class="icon-leftarrow"> </span>
		<span class="icon-rightarrow"> </span>
		<span class="icon-arrow-last"> </span>
	</div>
	<div id="iarea">
		<div id="ptext"></div>
		<p id="loading" style="display:none">∙∙∙LOADING∙∙∙</p>
	</div>
</div>
<script>
//initArrange();
</script>