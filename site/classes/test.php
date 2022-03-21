<?php
/**
* @package		com_meedya
* @copyright	Copyright (C) 2022 RJCreations. All rights reserved.
* @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

require 'HtmlObject.php';

$heobj1 = new HtmlElementObject('div', 'This is just some text in a div. We\'ll reuse it with a different background.');
$heobj2 = new HtmlElementObject('p','This is a paragraph');
$heobj3 = new HtmlElementObject('footer',$heobj2);
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<?php
echo $heobj1->render();
$heobj1->setAttr('style','background-color:yellow');
$heobj1->setFoot($heobj3);
echo $heobj1->render();
$heobj3->addCont(new HtmlElementObject('p','This is an added content paragraph'));
echo $heobj1->render();
?>
</body>
</html>