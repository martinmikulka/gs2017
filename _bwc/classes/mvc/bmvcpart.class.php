<?php
/**
 * File contains {@link BMVCPpart} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bmvcpart.class.php,v 1.4 2010-09-21 23:12:17 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage MVC
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * class represents basic predecesor for Controller and View
 *
 * @tutorial BauglirWebCore.MVC.pkg
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bmvcpart.class.php,v 1.4 2010-09-21 23:12:17 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage MVC
 */
class BMVCPart extends BGetMVCFiles
{
	/****************************************
	* fields
	****************************************/
  /**
   * list of configuration directives
   *
   *
   * @var mixed[]
   */
  public $Params = array();


	/****************************************
	* constructor & destructor
	****************************************/



	/****************************************
	* base functions
	****************************************/

  /**
   * function to initialize fields
   */
  function Init()
  {
    parent::Init();
    $this->Params = array();
  }

  /**
   * process basic $Action functionality file
   */
  public function Process()
  {
    if (sizeOf($this->ConfigFiles))
      foreach($this->ConfigFiles as $cfg)
        include($cfg);
    if ($this->File)
      if (is_file($this->File))
        include($this->File);
  }



	/****************************************
	* private & protected functions
	****************************************/

  /**
   * function load files for $Action
   *
   * function includes all config files
   */
  protected function LoadFiles($directory = '')
  {
    $this->ConfigFiles = array();
    $this->Params = array();
    parent::LoadFiles($directory);
  }
  
	/****************************************
	* static functions
	****************************************/

  
}
?>