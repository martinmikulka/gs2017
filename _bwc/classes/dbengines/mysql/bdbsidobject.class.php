<?php
/**
 * File contains {@link BDbSIdObject} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bdbsidobject.class.php,v 1.2 2010-11-27 11:05:06 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Database
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * generic object extending {@link BDbIdObject} object having primary key as string
 *
 *
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bdbsidobject.class.php,v 1.2 2010-11-27 11:05:06 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Database
 */
class BDbSIdObject extends BDbIdObject
{
	/****************************************
	* fields
	****************************************/


	/****************************************
	* constructor & destructor
	****************************************/

	/**
	 * constructor
   *
   * @param string $id value of primary key to load object according to
   * @param string $ident table name 
	 */

	function __construct($id = '', $ident = '', $dbConnection = null)
	{
    if ($ident && is_string($ident))
    {
      parent::__construct($ident, 0, $dbConnection);
      if ($id) $this->LoadById($id);
    }
    else
    {
      parent::__construct('', 0, $dbConnection);
      if ($id) $this->LoadById($id);
    }
	}

	/****************************************
	* base functions
	****************************************/

  /**
   * delete
   */
  function Delete()
  {
    global $BDatabase;
    $query = "DELETE FROM `{$this->_fTable}` WHERE id = '{$this->id}'";
    $BDatabase->Query($query);
    $this->Init();
  }


  /**
	 * function to save and store data
   *
   * <p>Function does not call {@link BObject::Init()} function to initilaize all data
   * so only passed data are changed.</p>
   *
   * <p>If no (or empty) data is passed {@link BDbObject::fRow} is used. This means if not empty $data is passed,
   * setting properties to object has no effect while saving.</p>
	 *
	 * @param mixed[] $data
	*/
	function Save($data)
	{
    $data = $this->fixSaveData($data);
    $oid = $this->id;
		$this->LoadFromRow($data, false);
    $this->BeforeSave($this->fRow);
		if ($oid)
		{
      $this->update($data);
		}
		else
		{
      $this->Insert($data);
		}
		return true;
	}


	/****************************************
	* static functions
	****************************************/




}
?>