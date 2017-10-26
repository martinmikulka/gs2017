<?php
/**
 * File contains {@link BVersion} class definition and is introducing $BVersion variable
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bversion.class.php,v 1.4 2009-04-12 23:21:05 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Core
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * BWC version specific fields holder
 *
 * <p>This class can change from version to version and defines BWC capabilities</p>
 * </p>Changes in this class can have significant consequences and may result in breaking functionality of BWC.</p>
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bversion.class.php,v 1.4 2009-04-12 23:21:05 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Core
 *
 * @property-read string[] $SQLDrivers list of available sql drivers, see {@tutorial BauglirWebCore/Database/BauglirWebCore.Database.Quoting.pkg}
 */
class BVersion extends BObject
{
	/****************************************
	* fields
	****************************************/

  /**
   * see object description
   * @ignore
   * @var string[]
   */
  private $_pSQLDrivers = array('mysql');
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
      case "sqldrivers":   $result = $this->_pSQLDrivers; break;
      default:          throw new Exception("BVersion::getter -> unknown property: {$name}.");
    }
		return $result;
  }

}

$GLOBALS['BVersion'] = new BVersion();
?>