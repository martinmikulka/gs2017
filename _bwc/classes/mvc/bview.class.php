<?php
/**
 * File contains {@link BView} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bview.class.php,v 1.5 2011-04-27 15:27:17 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage MVC
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * class represents view part of MVC
 *
 * @tutorial BauglirWebCore.MVC.pkg
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bview.class.php,v 1.5 2011-04-27 15:27:17 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage MVC
 */
class BView extends BMVCPart
{

	/****************************************
	* constants
	****************************************/
  /**
   * see BView::Process() for usage
   * process only config
   */
  const PROCESS_CONFIG = 0;
  /**
   * see BView::Process() for usage
   * process only view files
   */
  const PROCESS_VIEW = 1;
  /**
   * see BView::Process() for usage
   * process config and view files
   */
  const PROCESS_FULL = 2;

	/****************************************
	* fields
	****************************************/
  /**< type, description */

  /**
   * array containing data to be displayed by VIEW
   * @var <type>
   */
  public $Data = array();


	/****************************************
	* constructor & destructor
	****************************************/

	/**
	 * constructor
   *
   * @param string $action see object description for $Action property
   * @param string $directory entry directory (default value is the project views directory)
	 */
	function __construct($action = '', $directory = '')
	{
    global $BConfig;
    if (!$directory)
    {
      $directory = $BConfig->WwwRoot . "views";
    }
    parent::__construct($directory, $action);
	}



	/****************************************
	* base functions
	****************************************/

  /**
   * function to load all files according to action and process view
   * @param string $action see BMVCPart::SetAction() for description
   */
  public function SetAction($action)
  {
    parent::SetAction($action);
    /*if ($process)
      $this->Process();*/
  }

  /**
   * process basic $Action functionality files
   * @param int $level what kind of files (config, view) should be processed
   */
  public function Process($level = 1)
  {
    if (($level == BView::PROCESS_CONFIG) || ($level == BView::PROCESS_FULL))
    {
      if (sizeOf($this->ConfigFiles))
        foreach($this->ConfigFiles as $cfg)
          include($cfg);
    }
    if (($level == BView::PROCESS_VIEW) || ($level == BView::PROCESS_FULL))
    {
      if ($this->File)
        if (is_file($this->File))
        {
          /*
          if ($fd = fOpen(date("His").'.txt', 'a'))
          {
            fWrite($fd, print_r(debug_backtrace(), true));
            fWrite($fd, print_r($_GET, true));
            fWrite($fd, print_r($_POST, true));

          }
           * 
           */
          include_once($this->File);
        }
    }
  }

	/****************************************
	* private & protected functions
	****************************************/


  
	/****************************************
	* static functions
	****************************************/

  
}
?>