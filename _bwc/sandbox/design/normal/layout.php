<?php
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');
global $BController , $BView;
?>
<!DOCTYPE html>
<html>
    <head>
	<meta charset="UTF-8" />
	<title>Travelove</title>

	<link href="<?php ehtml('/tools/bootstrap/css/bootstrap.css'); ?>" type="text/css" rel="stylesheet" />
	<link href="<?php ehtml('/design/normal/style.css'); ?>" type="text/css" rel="stylesheet" />

    </head>
    <body>

	<?php $BView->Process(BView::PROCESS_VIEW); ?>

    </body>
</html>

