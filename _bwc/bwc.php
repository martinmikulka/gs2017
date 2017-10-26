<?php
/** 
 * File contains basic Bauglir Web Core metadata for documentation
 * 
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bwc.php,v 1.9 2010-09-16 11:55:38 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Core
 */
die('This is only example of BConfig type.');
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');

/**
 * object containing web bage configuration
 *
 * <p>Every web project using BWC has to create <var>$BConfig</var> variable as BConfig class template instance.</p>
 *
 * <p>This class is special. BConfig class is not inside BWC directory (this is just teplate and is not used), specification of
 *   this class is a part of web project itsef but BWC is depended of its existence and structure specified here</p>
 *
 * <p><b>Web project BConfig class is not extension of this class. That's why it's marked as final.</b></p>
 *
 * <p>This class definition serves only for documentation purposes.</p>
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bwc.php,v 1.9 2010-09-16 11:55:38 bauglir Exp $
 * @final
 * @package BauglirWebCore
 * @subpackage Core
 */
final class BConfig
{
  /**
   * full path to BWC root dir, path is used for including files
   *
   * has to contain slash at the end
   * @var string
   */
  public $Core          = '/full/path/to/BWC/';
  /**
   * path to web pages root dir, path is used for including files (core, controllers and views project directories)
   *
   * has to contain slash at the end
   * @var string
   */
  public $WwwRoot       = './';

  /**
   * used database driver, see {@link BVersion}
   * @var string
   */
  public $DBDriver = 'mysql';

  /**
   * used include class paths
   * @var array
   */
  public $ClassPaths = array();

  /**
   * used include (include all files from those paths)
   * @var array
   */
  public $IncludePaths = array();


	/****************************************
	* constructor & destructor
	****************************************/

	/**
	 * constructor
   * force sigleton pattern
	 */
	function __construct()
	{
    static $instances = 0;                            /**< int, number of instances to ensure singleton */
    //if ($instances) trigger_error('Cannot instance more than one Config class.', E_USER_WARNING);
    if ($instances) throw new Exception('BConfig::__construct -> Cannot instance more than one Config class.');
    $instances++;
	}
}

/**
 * @name $BConfig
 */
$GLOBALS['BConfig'] = new BConfig();


require_once($BConfig->Core . "config.php");

?>
