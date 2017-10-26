<?php
/**
 * File contains {@link BDbIdObject} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bdbidobject.class.php,v 1.7 2010-11-27 11:05:06 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Database
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * generic object extending {@link BDbObject} object having primary key
 *
 * primary key has to be: `id` int not null primary key auto_increment (or something coresponding in other DB engines)
 *
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bdbidobject.class.php,v 1.7 2010-11-27 11:05:06 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Database
 */
class BDbIdObject extends BDbObject
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
   *
   *
   * @param string|int $ident table name or row id
   * @param int $id value of primary key to load object according to
	 */

	function __construct($ident = 0, $id = 0, $dbConnection = null)
	{
    if (is_string($ident))
    {
      parent::__construct($ident, $dbConnection);
      if ($id) $this->LoadById($id);
    }
    else
    {
      parent::__construct('', $dbConnection);
      if ($ident) $this->LoadById($ident);
    }
	}

	/****************************************
	* base functions
	****************************************/

  /**
   * function called right before update or insert is called;
   */
  function BeforeSave()
  {
    
  }

  /**
   * delete
   */
  function Delete()
  {
    $query = "DELETE FROM `{$this->_fTable}` WHERE id = {$this->id}";
    $this->Db->Query($query);
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
		$this->LoadFromRow($data, false);
    $this->BeforeSave($this->fRow);
		if ($this->id)
		{
      $this->update($data);
		}
		else
		{
      $this->id = $this->Insert($data);
		}
		return true;
	}

  /**
	 * function to save and store data
   *
	 * @param string $name, value name
   * @param mixed $val, value 
	 */
  function SaveValue($name, $val)
  {
    $this->Save(array($name => $val));
  }


	/****************************************
	* private & protected functions
	****************************************/

  protected function fixSaveData($data)
  {
    if (!(is_array($data) && sizeOf($data))) $data = $this->fRow;
    if (!array_key_exists('id', $data)) $data['id'] = $this->id;
    $result = array();
    foreach($data as $k => $v)
    {
      $result[$this->pcConvert($k)] = $v;
    }
    return $result;
  }

  protected function update($data)
  {
    $sets = array();
    $vals = array();
    foreach($data as $k => $v)
    {
      $k = $this->pcConvert($k);

      if ($this->isColumnNumeric($k))
      {
        if ('!#NULL#!' === $v)
        {
          array_push($sets, "`$k` = %s");
        }
        else if ($this->isColumnUnsigned($k))
        {
          array_push($sets, "`$k` = %u");
        }
        else
        {
          array_push($sets, "`$k` = %d");
        }
      }
      else array_push($sets, "`$k` = %s");
      /*
      if ($this->isColumnNumeric($k) && ($v !== '!#NULL#!')) array_push($sets, "`$k` = %d");
      else array_push($sets, "`$k` = %s");
       * 
       */
      array_push($vals, $v);
    }
    if ($this->isColumnNumeric('id'))
      $sql = "update ##.`{$this->_fTable}` set " .implode(",", $sets) . " where id = {$this->id}";
    else
      $sql = "update ##.`{$this->_fTable}` set " .implode(",", $sets) . " where id = '{$this->id}'";
    $this->Query($sql, $vals);
  }


  
	/****************************************
	* static functions
	****************************************/

  

  
}
?>
