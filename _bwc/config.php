<?php
/** 
 * File contains generic configuration (autoload classes, functions libraries, debug class, version class, etc.)
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: config.php,v 1.22 2010-09-21 23:12:17 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Core
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');

/**
 * implementation of automatic object loading mechanism
 *
 * process:
 * - load object from web root
 * - if does not exists, load it from BWC
 * this allows object redefinition
 *
 * @see BConfig
 *
 * @global object instance of BConfig
 * @param string $class class name
 */
function __autoload($class)
{
  global $BConfig, $_bwc_classeDirs;
  $class = strtolower($class);
  if (file_exists($BConfig->WwwRoot . "core/classes/{$class}.class.php"))
  {
    require_once($BConfig->WwwRoot . "core/classes/{$class}.class.php");
    return;
  }
  else
  {
    if (array_key_exists("ClassPaths", get_object_vars($BConfig)) && is_array($BConfig->ClassPaths))
    {
      foreach($BConfig->ClassPaths as $path)
      {
        if (file_exists($path . "/{$class}.class.php"))
        {
          require_once($path . "/{$class}.class.php");
          return;
        }
      }
    }
    if (file_exists($BConfig->Core . "classes/{$class}.class.php"))
    {
      require_once($BConfig->Core . "classes/{$class}.class.php");
      return;
    }
    if (file_exists($BConfig->Core . "classes/dbengines/{$BConfig->DBDriver}/{$class}.class.php"))
    {
      require_once($BConfig->Core . "classes/dbengines/{$BConfig->DBDriver}/{$class}.class.php");
      return;
    }
    if (sizeOf($_bwc_classeDirs)) foreach($_bwc_classeDirs as $dir)
    {
      if (file_exists($dir . "/{$class}.class.php"))
      {
        require_once($dir . "/{$class}.class.php");
        return;
      }
    }
  }
  trigger_error("Class $class was not found.", E_USER_ERROR);
}

//load all BWC function files
$d = dir($BConfig->Core . "functs");
while (false !== ($entry = $d->read()))
{
  /** @ignore */
   if (!in_array($entry, array('.', '..', 'CVS'))) include_once($BConfig->Core . "functs/" . $entry);
}
$d->close();


//load all BWC classes directories
$_bwc_classeDirs = array();
$d = dir($BConfig->Core . "classes");
while (false !== ($entry = $d->read()))
{
  /** @ignore */
  if (!in_array($entry, array('.', '..', 'CVS')))
  {
    $name = $BConfig->Core . "classes/" . $entry;
      if (is_dir($name)) array_push($_bwc_classeDirs, $name);
  }
}
$d->close();


$GLOBALS['BController'] = new BController();
$GLOBALS['BView'] = new BView();

//load all project function files
if (is_dir($BConfig->WwwRoot . "core/functs") && ($d = @dir($BConfig->WwwRoot . "core/functs")))
{
  while (false !== ($entry = $d->read()))
  {
    /** @ignore */
     if (!in_array($entry, array('.', '..', 'CVS'))) include_once($BConfig->WwwRoot . "core/functs/" . $entry);
  }
  $d->close();
}

if (array_key_exists("IncludePaths", get_object_vars($BConfig)) && is_array($BConfig->IncludePaths))
{
  foreach($BConfig->IncludePaths as $path)
  {
    if ($d = @dir($path))
    {
      while (false !== ($entry = $d->read()))
      {
        /** @ignore */
         if (!in_array($entry, array('.', '..', 'CVS'))) include_once($path . $entry);
      }
      $d->close();
    }
  }
}


/**#@+
 * @ignore
 */
require_once($BConfig->Core . "classes/bversion.class.php");
require_once($BConfig->Core . "classes/bdebug.class.php");
/**#@-*/




?>
