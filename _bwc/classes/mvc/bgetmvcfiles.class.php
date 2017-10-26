<?php
/**
 * File contains {@link BGetMVCFiles} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bgetmvcfiles.class.php,v 1.8 2011-06-02 19:12:51 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage MVC
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * Object is responsible for finding all files used in CONTROLLER or VIEW based on required action
 *
 * files are retreived based on passed action (representing CTRL or VIEW parameter)
 *
 * @tutorial BauglirWebCore.MVC.FlowParameters.pkg
 * @tutorial BauglirWebCore.MVC.FilesStructure.pkg
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bgetmvcfiles.class.php,v 1.8 2011-06-02 19:12:51 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage MVC
 * @property-read string $Directory base directory where to start searching (alias $Dir)
 * @property-read string $Action action to search files for
 */
class BGetMVCFiles extends BObject
{
	/****************************************
	* fields
	****************************************/

  /**
   * list of configuration files to be included
   *
   * <p>list is ordered by order in which files have to be included.
   * General files are at the beginning of list, more specific are at the end.</p>
   *
   * <p>property should be used only for reading, do not write into this property unless necesarry.</p>
   *
   * @var string[]
   */
  public $ConfigFiles = array();

  /*
   * list of files to be included
   *
   * <p>list is ordered by order in which files have to be included.
   * General files are at the beginning of list, more specific are at the end.</p>
   *
   * <p>property should be used only for reading, do not write into this property unless necesarry.</p>
   *
   * @var string[]
   *
   * public $Files = array();
   */


  /**
   * basic file to be included to do $Action functionality
   * @var string
   */
  public $File = '';

  /**
   * base directory where to start searching
   *
   * this directory and subdirectories are processed
   *
   * @var string
   */
  protected $_fDirectory = '';


  /**
   * list of custom directories
   *
   * those directories and subdirectories are processed in case of file is not found in $_fDirectory
   *
   * @var string
   */
  protected $_fDirectories = array();

  /**
   * action to search files for
   * @var string
   */
  protected $_fAction = '';

  /**
   * generic getter
   * @param string $name field name
   * @return mixed
   * @ignore
   */
  protected function &getter($name)
	{
    $names = strtolower($name);
    $result = null;
    switch($names)
    {
      case "directories":
        $result = $this->_fDirectories;
      break;
      case "directory":
      case "dir":
        $result = $this->_fDirectory;
      break;
      case "action":    $result = $this->_fAction; break;
      default:          $result = parent::getter($name);
    }
		return $result;
  }

  /**
   * generic setter
   * @ignore
   */
  protected function setter($name, $value)
	{
    $names = strtolower($name);
    $result = null;
    switch($names)
    {
      case "directories":
        if (!is_array($value))
        {
          $this->_fDirectories = array($value);
        }
        else
        {
          $this->_fDirectories = $value;
        }
      break;
      case "directory":
      case "dir":
        if (is_dir($value))
        {
          $this->_fDirectory = $value;
          $this->LoadFiles();
        }
      break;
      case "action":    
        $this->SetAction($value);
      break;
      default:          $result = parent::setter($name);
    }
		return $result;
  }


	/****************************************
	* constructor & destructor
	****************************************/

	/**
	 * constructor
   *
   * @param string $directory see object description for $Directory property
   * @param string $action see object description for $Action property
	 */
	function __construct($directory, $action)
	{
    parent::__construct();
    $this->Init();
    if (strtolower($action) != $action) $action = strToUnderscore($action);
    $this->_fAction = $action;
    if (is_dir($directory)) $this->_fDirectory = $directory;
    //else throw new BMVCException("BGetMVCFiles::__construct() -> unknown directory '$directory'.");
    $this->LoadFiles();
	}

	/**
	* destructor
	*/
	function __destruct()	{}

	/****************************************
	* base functions
	****************************************/

  /**
   * function to initialize fields
   */
  function Init()
  {
    $this->ConfigFiles = array();
    $this->File = '';
    $this->_fDirectory = '';
    $this->_fAction = '';
    $this->_fDirectories = array();
  }

  /**
   * function to load all files according to action
   * @param string $action, action to be processed, can be passed in either casing, automatically is converted do underscore case
   */
  function SetAction($action)
  {
    $action .= '';
    $action = basename($action);
    if (strtolower($action) != $action) $action = strToUnderscore($action);
    $this->_fAction = $action;
    $this->LoadFiles();
  }

	/****************************************
	* private & protected functions
	****************************************/

  /**
   * function load files for $Action
   */
  protected function LoadFiles($directory = '')
  {
    if (!$directory)
    {
      $arr = array_merge($this->_fDirectories, array($this->_fDirectory));
      while(sizeOf($arr))
      {
        $dir = array_pop($arr);
        $this->LoadFiles($dir);
        if ($this->File)
        {
          return;
        }
      }
      $this->LoadFiles($this->_fDirectory);
      return;
    }


    $directory = $directory ? $directory : $this->_fDirectory;
    $this->ConfigFiles = array();
    $this->File = '';
    
    if (!($directory && $this->_fAction)) return;
    if (!is_dir($directory)) throw new BMVCException("BGetMVCFiles::LoadFiles() -> unknown directory '$directory'.");
    //GET CONFIG FILES
    $dir = $directory;
    if (is_file("$dir/_Common.cfg.php")) array_push($this->ConfigFiles, "$dir/_Common.cfg.php");
    $parts = explode("_", $this->_fAction);
    //look in directories defined by action
    for($i = 0, $j = sizeOf($parts); $i < $j; ++$i)
    {
      $part = $parts[$i];
      if (is_dir($dir. "/" . $part))
      {
        if (is_file("$dir/$part/_Common.cfg.php")) array_push($this->ConfigFiles, "$dir/$part/_Common.cfg.php");
        $dir = $dir. "/" . $part;
      }
      else break;
    }


    //look for files defined by action in folder having no subfolder with corresponding directory
    $file = '';
    while ($i < $j)
    {
      $file = $file ? $file . "_" . $parts[$i] : $parts[$i];
      if (is_file("$dir/$file.cfg.php")) array_push($this->ConfigFiles, "$dir/$file.cfg.php");
      $i++;
    }


    //GET FILES
    //printr($this->_fAction);
    $dir = $directory;
    //find deepest existing directory
    for($i = 0, $j = sizeOf($parts); $i < $j; ++$i)
    {
      $part = $parts[$i];
      if (is_dir($dir. "/" . $part))
      {
        $dir = $dir. "/" . $part;
      }
      else break;
      
    }
    
    $prefix = array();
    while($i < $j)
    {
      array_push($prefix, $parts[$i]);
      ++$i;
    }
    $prefix = implode("_", $prefix);
    if ($prefix)
    {
      if (is_file("$dir/$prefix.php")) $this->File = "$dir/$prefix.php";
    }
    else
      if (is_file("$dir/_Index.php")) $this->File = "$dir/_Index.php";



  }


  
	/****************************************
	* static functions
	****************************************/

  
}
?>