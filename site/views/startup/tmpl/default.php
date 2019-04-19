<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//echo'<xmp>';var_dump($this->params);echo'</xmp>';
?>
<h1><?php echo $this->params->get('page_title'); ?> Startup Screen</h1>
<?php if ($this->userPerms->canAdmin): ?>
<div>
	<p>Great!!! So you want to try this out, do you...</p>
	<p>Please be patient and give me a few weeks to figure out what to do here.</p>
</div>
<div>
	<form>
		<button type="submit" class="btn btn-primary">Start My Gallery</button>
		<input type="hidden" name="task" value="begin" />
	</form>
</div>
<?php else: ?>
<h3>This gallery has not yet been initiated</h3>
<?php endif; ?>
