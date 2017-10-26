<?php
/**
 * File contains miscellaneous functions
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: misc.funct.php,v 1.12 2009-07-22 14:35:22 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Core
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * return timestamp in Y-m-d H:i:s (yyyy-mm-dd hh-nn-ss) format according to $type
 * @param timestamp $time if -1 passed, current timestamp is used
 * @param string $type date and/or time to be returned
 *    enum('date', 'time', 'all')
 * @return string
 */
function datetime($time = -1, $type = 'all')
{
  if ($time == -1) $time = time();
  switch($type)
  {
    case "date": return date("Y-m-d", $time);
    case "time": return date("H:i:s", $time);
    default: return date("Y-m-d H:i:s", $time);
  }
}

/**
 * finds whether number is even number
 * @param int $number
 * @return bool
 */
function even($number)
{
  return !odd($number);
}



/**
 * process data using PHP eval construct
 *
 * data should be passed as regular PHP code:
 * <code>
 * <?php
 * $a = 'd';
 * ?>
 * </code>
 * as opposed to regular usage of eval that expect data to automatically start with PHP
 *
 * @param string $content string to process
 * @return string
 */
function getEvalContent($content)
{
  ob_start();
  eval ("?>" . $content);
  $contents = ob_get_contents();
  ob_end_clean();
  return $contents;
}

/**
 * return data processed by PHP include construct
 * @param string $filename name of file to be processed
 * @return string
 */
function getIncludeContent($filename)
{
  return _bwc_phpProcessFile($filename, 'include');
}

/**
 * return data processed by PHP require construct
 * @param string $filename name of file to be processed
 * @return string
 */
function getRequireContent($filename)
{
  return _bwc_phpProcessFile($filename, 'require');
}



/**
 * finds whether number id even number
 * @param int $number
 * @return bool
 */
function odd($number)
{
  return (bool)($number % 2);
}


/**
 * returns formated data to be displayd in HTML
 *
 * <code>
 * echo <pre>
 * print_r($data)
 * echo </pre>
 * </code>
 * @param mixed $data data to be displayed
 */
function printr($data)
{
  echo "<pre>";
  print_r($data);
  echo "</pre>";
}



/**
 * get value from structured variable while preventing error, if error should occured (value cannot be retreived), <var>$default</var> is returned
 *
 * <p>If <var>$container</var> is <b>array</b>, <var>$name</var> is <b>array key</b>, <var>$default</var> is returned if no such key exists</p>
 * <p>If <var>$container</var> is <b>object</b>, <var>$name</var> is <b>object property</b>, <var>$default</var> is returned if no such property exists</p>
 * <p>If <var>$container</var> is <b>string</b>, <var>$name</var> is <b>position in string</b>, <var>$default</var> is returned position value is bigger than string length</p>
 *
 * @param array|string|object $container variable value should be extracted from
 * @param string|integer $name array key, object method or position in string
 * @param mixed $default default value (also used as type parameter, return value must have the same type)
 * @return mixed
 */
function value($container, $name, $default = null)
{
  $od = $default;
  if (is_array($container))
  {
    if (array_key_exists($name, $container))
      $default = $container[$name];
  }
  elseif (is_object($container))
  {
    $vars = get_object_vars(($container));
    $default = value($vars, $name, $default);
  }
  elseif (is_string($container))
  {
    if (is_int($name))
    {
      $name += 0;
      if (strlen($container) > $name)
        $default = $container{$name};
    }
  }

  //check for type, scalar types are considered 'interchangable'
  if (is_array($od) && !is_array($default))
    $default = $od;
  elseif (is_object($od) && !is_object($default))
    $default = $od;
  elseif (is_resource($od) && !is_resource($default))
    $default = $od;
  elseif (is_int($od) || is_float($od))
  {
    if (is_numeric($default))
      $default += 0;
    else
      $default = $od;
  }
  elseif (is_string($od))
  {
    if (is_scalar($default))
      $default .= '';
    else
      $default = $od;
  }

  return $default;
}

/**
 * display $value and add <<br />> at the end
 * used for printing row values in HTML code
 * 
 * @param string $value
 */
function writeln($value)
{
  echo $value . "<br />";
}




//INTERNAL FUNCTIONS

/**
 * @ignore
 */
function _bwc_phpProcessFile($filename, $funtion)
{
  if (is_file($filename))
  {
    ob_start();
    eval("$funtion('$filename');");
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
  }
  return false;
}





?>
