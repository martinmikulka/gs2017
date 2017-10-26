<?php
function defineIndex() { define("BWC_VALID_INCLUDE" , true); } defineIndex();

date_default_timezone_set('Europe/Prague');
define('ROOT_DIR' , dirname(__FILE__));

require_once(ROOT_DIR . '/core/classes/bconfig.class.php');

$default = 'index';
$request = getRequest('CTRL' , $default);
if (empty($request))
    $request = $default;

# process controller files
$BController->SetAction($request , BController::PROCESS_VIEW);

# 404 check
if (empty($BView->File))
{
    header('Location: /404');
    exit;
}

# process view config files
$BView->Process(BView::PROCESS_CONFIG);

# set the design
$design = getRequest('design' , 'normal');
require(ROOT_DIR . '/design/' . $design . '/layout.php');
?>
