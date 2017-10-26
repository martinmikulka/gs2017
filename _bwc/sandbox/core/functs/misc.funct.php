<?php
/** 
 * File contains misc functions
 * 
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: misc.funct.php,v 1.2 2011/11/21 11:34:16 root Exp $
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * return variable from POST or GET
 * @param string $name variable name
 * @param mixed $default default value if variable id not passed
 * @return mixed
 */
function getRequest($name, $default)
{
  $val = $default;
  if (array_key_exists($name, $_POST)) $val = get_magic_quotes_gpc() ? stripslashes($_POST[$name]) : $_POST[$name];
  elseif (array_key_exists($name, $_GET)) $val = get_magic_quotes_gpc() ? stripslashes($_GET[$name]) : $_GET[$name];
  return $val;
}

function getString($name, $default = '')
{
  return trim(getRequest($name, $default) . '');
}
function getInt($name, $default = 0)
{
  return getRequest($name, $default) + 0;
}
function getArray($name, $default = array())
{
  return getRequest($name, $default);
}


/**
 * convert string to html string (using HTML entities)
 * @param string $string
 * @return string
 */
function html($string)
{
  return htmlspecialchars($string);
}


/**
 * print string as html string (using HTML entities)
 * @param string $string
 */
function ehtml($string)
{
  echo html($string);
}


/**
 *
 */
function dump($var)
{
  echo '<pre>';
  var_dump($var);
  echo '</pre>';
}


/**
 *
 */
function isAjax()
{
  return ((strtolower(value($_SERVER, 'HTTP_X_REQUESTED_WITH', '')) == strtolower('XMLHttpRequest')) || getRequest("__AJAX_YES", 0)) && (!getRequest("__AJAX_NO", 0));
}


/**
 *
 */
function isLocal()
{
  if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] == '127.0.0.1')
    return true;

  return false;
}
?>
