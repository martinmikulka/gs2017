<?php
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');
global $Session;

# idle in seconds
define('SESSION_IDLE' , 1800); # 30 min

# start session
$Session = new BCookieSession();
$Session->idle = SESSION_IDLE;
if ($Session->StartSession() === false)
{
  $Session->KillSession();
  $Session->StartSession();
}
?>
