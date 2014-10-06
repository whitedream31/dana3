<html>
  <body>
    <h1>Gallery Test</h1>
<?php
require 'class.table.account.php';
$account = account::StartInstance(4);

$start = 0;
$imagesperpage = 10;
$incdescription = true;

$gallery = new gallery(2);
echo ArrayToString($gallery->BuildGalleryViewer($start, $imagesperpage, 3, $incdescription));
?>
  </body>
</html>