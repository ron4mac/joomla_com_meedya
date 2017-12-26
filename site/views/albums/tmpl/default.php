<?php
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

//$jdoc = JFactory::getDocument();
//$jdoc->addScript('components/com_meedya/static/js/blazy.js');
//$jdoc->addScript('components/com_meedya/static/js/echo.min.js');
JHtml::stylesheet('components/com_meedya/static/css/albums.css');

$cid = -1;
foreach ($this->items as $item) {
	if ($item->catid != $cid) {
		$cid = $item->catid;
		echo '<hr />';
	}
	$aThm = $this->getAlbumThumb($item);
	echo '<a href="'.JRoute::_('index.php?option=com_meedya&view=album&aid='.$item->aid).'">';
	echo '<div class="albthumb" title="'.$item->title.'"><img src="1x1.png" data-echo="'.$aThm.'" alt="" /><div>'.$item->title.'</div></div>';
	echo '</a>';
}

//echo'<xmp>';var_dump($this->items);echo'</xmp>';
?>
<script>
/*
window.addEventListener('load', function(){
	var allimages= document.getElementsByTagName('img');
	for (var i=0; i<allimages.length; i++) {
		if (allimages[i].getAttribute('data-src')) {
			allimages[i].setAttribute('src', allimages[i].getAttribute('data-src'));
		}
	}
}, false);
*/
/*
	;(function() {
		// Initialize
		window.bLazy = new Blazy({
		container: '.body',
        success: function(ele){
            // Image has loaded
            // Do your business here
        }
      , error: function(ele, msg){
      	console.log(msg);
            if(msg === 'missing'){
                // Data-src is missing
            }
            else if(msg === 'invalid'){
                // Data-src is invalid
            }  
        }
    });
	})();
*/
	echo.init({
		offset: 100,
		throttle: 250,
		unload: false,
		callback: function (element, op) {
			console.log(element, 'has been', op + 'ed')
		}
	});
</script>