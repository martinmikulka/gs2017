<?php
/**
 * File contains {@link BController} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bcontroller.class.php,v 1.6 2011-04-27 15:27:17 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage MVC
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * class represents controller part of MVC
 *
 * @tutorial BauglirWebCore.MVC.pkg
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bcontroller.class.php,v 1.6 2011-04-27 15:27:17 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage MVC
 */
class BController extends BMVCPart
{

	/****************************************
	* constants
	****************************************/
  /**
   * see BController::SetAction() for usage
   * only load files
   */
  const PROCESS_NONE = 0;
  /**
   * see BController::SetAction() for usage
   * process controller files
   */
  const PROCESS_CONTROLLER = 1;
  /**
   * see BController::SetAction() for usage
   * process controller files and load view files
   */
  const PROCESS_VIEW = 2;
  /**
   * see BController::SetAction() for usage
   * process controller files and view files
   */
  const PROCESS_FULL = 3;

	/****************************************
	* fields
	****************************************/
  /**< type, description */

  /**
   * array containign reload parameters
   * if array is not empty, Location header (redirect) is sent right after
   * BController files are processed. If key CTRL is missing or empty, current action is
   * passed as CTRL, be aware of possiblity of looping indefinitely!!!
   * @var array()
   */
  public $Reload = array();




	/****************************************
	* constructor & destructor
	****************************************/

	/**
	 * constructor
   *
   * @param string $action see object description for $Action property
   * @param string $directory entry directory (default value is the project controllers directory)
	 */
	function __construct($action = '', $directory = '')
	{
    global $BConfig;
    if (!$directory)
    {
      $directory = $BConfig->WwwRoot . "controllers";
    }
    parent::__construct($directory, $action);
	}



	/****************************************
	* base functions
	****************************************/

  /**
   * process basic $Action functionality file
   */
  public function Process()
  {
    if (sizeOf($this->ConfigFiles))
    {
      foreach($this->ConfigFiles as $cfg)
      {
        include($cfg);
        $this->checkForReload();
      }
    }
    if ($this->File)
    {
      if (is_file($this->File))
      {
        include($this->File);
        $this->checkForReload();
      }
    }
  }

  /**
   * function to load all files according to action and process controller
   * @param string $action see BMVCPart::SetAction() for description
   * @param bool $process level of automatic processing files see constants definitions
   */
  public function SetAction($action, $process = 1)
  {
    global $BView;
    parent::SetAction($action);
    if ($process >= BController::PROCESS_CONTROLLER)
      $this->Process();
    
    if ($process >= BController::PROCESS_VIEW)
    {
      $wAction = value($this->Params, 'VIEW', '');
      if (!$wAction)
        $wAction = value($_POST, 'VIEW', '');
      if (!$wAction)
        $wAction = value($_GET, 'VIEW', '');
      if (!$wAction)
        $wAction = $action;
      $BView->SetAction($wAction);
    }
    if ($process >= BController::PROCESS_FULL)
    {
      $BView->Process(BView::PROCESS_FULL);
    }
  }


	/****************************************
	* private & protected functions
	****************************************/

  /**
   * function checks if there ara any variables in BController::$Reload property
   * and if there are any, Location header is inserted and script is terminated.
   */
  protected function checkForReload()
  {
    if (sizeOf($this->Reload))
    {
      if (!array_key_exists('CTRL', $this->Reload))
        $this->Reload['CTRL'] = $this->_fAction;
      $query = http_build_query($this->Reload, '', '&');
      $url = "?$query";
      header("Location: $url");
      exit;
    }
    
  }


  
	/****************************************
	* static functions
	****************************************/

  
}
?>