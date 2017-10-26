<?php
define('BWC_VALID_INCLUDE', true);
date_default_timezone_set('Europe/Prague');
define('ROOT_DIR', dirname(__FILE__));
define('IS_DEV', file_exists(ROOT_DIR . '/DEV'));

require_once(ROOT_DIR . '/core/classes/bconfig.class.php');

if (IS_DEV) {
	$BDebug->Enabled = true;
	$BDebug->Extended = true;
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
} else {
	$BDebug->Enabled = false;
	$BDebug->Extended = false;
	ini_set('display_errors', 0);
	error_reporting(0);
}

$default = 'index';
$request = getRequest('CTRL', $default);
if (empty($request)) {
	$request = $default;
}
$request = str_replace('/', '_', $request);

# process controller files
$BController->SetAction($request, BController::PROCESS_VIEW);

# 404 check
if (empty($BView->File)) {
	header('Location: /404');
	exit;
}

# process view config files
$BView->Process(BView::PROCESS_CONFIG);

# set the design
$design = getRequest('design', 'normal');
if (strpos($BController->action, 'partial_') === 0) {
	$design = 'none';
} else if (in_array($BController->action, array('404'))) {
	$design = 'empty';
}
require(ROOT_DIR . '/layouts/' . $design . '.php');
