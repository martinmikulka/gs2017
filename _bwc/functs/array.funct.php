<?php
/**
 * File contains array related functions
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: array.funct.php,v 1.3 2009-04-12 17:53:59 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Core
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * drop key from array recursively
 * @param mixed[] $array array to drop key from
 * @param string $key key to drop
 * @return mixed[]
 */
function arrayDropKey($array, $key)
{
  if (is_array($key))
  {
    if (sizeOf($key)) foreach($key as $k)  $array = arrayDropKey($array, $k);
    return $array;
  }
  else
  {
    $result = array();
    if (sizeOf($array)) foreach ($array as $k => $v)
    {
      if (is_integer($k)) $k = "$k";
      if ($k != $key)
      {
        if (is_array($v)) $v = arrayDropKey($v, $key);
        if (is_integer($k)) array_push($result, $v);
        else $result[$k] = $v;
      }

    }
    return $result;
  }
}
/**
 * @ignore
 */
function dropKey($array, $key) {return arrayDropKey($array, $key); }

/**
 * change value associated with key recursively
 * @param mixed[] $array array where value should be changed
 * @param string $key key identifying values to change
 * @param mixed $newValue new value
 * @return mixed[]
 */
function arrayChangeKeyValue($array, $key, $newValue)
{
  if (is_array($key))
  {
    if (sizeOf($key)) foreach($key as $k)  $array = arrayChangeKeyValue($array, $k, $newValue);
    return $array;
  }
  else
  {
    $result = array();
    if (sizeOf($array)) foreach ($array as $k => $v)
    {
      if (is_integer($k)) $k = "$k";
      if (is_array($v)) $v = arrayChangeKeyValue($v, $key, $newValue);
      if ($k != $key) $value = $v;
      else $value = $newValue;
      if (is_integer($k)) array_push($result, $value);
      else $result[$k] = $value;

    }
    return $result;
  }
}

/**
 * @ignore
 */
function changeKeyValue($array, $key, $newValue) { return arrayChangeKeyValue($array, $key, $newValue); }

/**
 * return array as string
 *
 * <p>Example: </p>
 * <code>
 * $a = array(
 *   'color' => array(
 *     'favorite' => array(
 *       'red',
 *       'green',
 *     ),
 *     'blue',
 *   ),
 *   '5',
 *   '10',
 * );
 * </code>
 * <p>is displayed as: </p>
 * <code>[color => [favorite => [red, green], blue], 5, 10]</code>
 *
 * @param mixed[] $array
 * @return string
 */
function arrayToString($array)
{
  $result = '[';
  $data = array();
  if (sizeOf($array)) foreach($array as $k => $v)
  {
    if (is_array($v)) $v = arrayToString($v);
    if (is_integer($k)) array_push($data, "$v");
    else array_push($data, "$k => $v");
  }
  $result .= implode(", ", $data) . ']';
  return $result;
}

?>
