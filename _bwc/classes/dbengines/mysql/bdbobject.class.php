<?php
/**
 * File contains {@link BDbObject} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bdbobject.class.php,v 1.10 2011-10-17 11:56:17 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Database
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * generic object that represent row in database table ("holder table")
 *
 * <p>properties are automatically generated to corespond table columns (can be accesed using underscore, camel or pascal case)</p>
 * <p> See {@tutorial BauglirWebCore.Database.ColumnPropertiesRewriting.pkg} for correct implementation of mirroring
 * object vs. database</p>
 *
 * <p>See {@tutorial BauglirWebCore.Database.NamingConventions.pkg} for linking object to table.</p>
 *
 * <p>Not existing properties as rewriten to underscore case, be awere if magic property is defined. E.g. _Table is rewritten to __table before passed to getter.</p>
 *
 *
 * @method mixed LoadBy*() LoadBy*(mixed $value) set of method where asterix can be replaced by column name (LoadById, LoadByUniqueData) where column name is converted to underscore
 *   (id, unique_data), see {@link BDbObject::_loadByValue()} used internally
 * @tutorial BauglirWebCore/Database/BauglirWebCore.Database.NamingConventions.pkg, BauglirWebCore.Database.ColumnPropertiesRewriting.pkg
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @property-read float $_Table table where object representations as stored {@see BDbObject::$fTable}
 * @version $Id: bdbobject.class.php,v 1.10 2011-10-17 11:56:17 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Database
 */
class BDbObject extends BObject
{
	/****************************************
	* fields
	****************************************/
  /**
   * table holding rows that should be mirrored to this object
   *
   * <p>default value (if not other passed in constructor) is object name converted to underscore notation without first 3 letters.
   * e.g. <samp>BDbUserValue => user_value</samp>
   * </p>
   *
   * @var string
   */
  protected $_fTable = '';

  /**
   * array holding database connection
   * @var resource
   */
  protected $fDb = null;

  /**
   * array holding table row values as column name => value pairs (array key => value)
   * @var mixed[]
   */
  protected $fRow = array();

  /**
   * array holding table row values as column name => default type value (0, '') (array key => value)
   *
   * array is filled as soon as first instance is created
   *
   * @var mixed[]
   */
  protected $fRowDefault = array();

	/****************************************
	* field getters & setters
	****************************************/

	/**
	* rewrite unknown field to underscore one used as DB table column identifiers
	*
	* @param string $id field name to be rewrited
  * @return string new field name
	*/
	protected function pcConvert($id)
	{
		if (strtolower($id) == $id) return $id;
		else
		{
			if (array_key_exists($id, $this->_fConvertFields))
			{
				return $this->_fConvertFields[$id];
			}
			else
			{
				$oid = $id;
        $id = strToUnderscore($id);
				$this->_fConvertFields[$oid] = $id;
			}
			return $id;
		}
	}

  /**
   * generic getter
   * @param string $name field name
   * @return mixed
   * @ignore
   */
  protected function &getter($name)
	{
    global $BDatabase;
    $names = strtolower($name);
    $result = null;
    if (array_key_exists($names, $this->fRow)) return $this->fRow[$names];
    switch($names)
    {
      case "__table":   $result = $this->_fTable; break;
      case "db":        $result = ($this->fDb == null) ? $BDatabase : $this->fDb;   break;
      default:          $result = parent::getter($name);
    }
		return $result;
  }

  /**
	 * generic setter
   *
   * <p>should be overwritten by child classes</p>
   * <p>if class does not process passed name, function should call parent's implementation (this should provide bubbling to all parents until name is processed or fieald actualy does not exists)</p>
	 * @param string name field name
	 * @param mixed value new value
   * @ignore
	 */
  protected function setter($name, $value)
	{
    global $BVersion;
    $names = strtolower($name);

    if (array_key_exists($names, $this->fRow))
		{
    	$this->fRow[$names] = $value;
			return;
    }

    switch($names)
    {
      case "db":        $this->fDb = $value;   break;
      default:          parent::setter($name, $value);
    }
  }


  /**
   * magic function to handle undefined methods
   *
   * function handles LoadBy* functions (see object description)
   * 
   * @param string $function, function to call
   * @param mixed[] $args, arguments
   */
  public function __call($function, $args)
  {
    $functionS = strtolower($function);
    if (strpos($functionS, "loadby") === 0)
    {
      $name = $this->pcConvert(substr($function, strlen('loadby')));
      $this->_loadByValue($name, $args[0]);
    }
    else throw new Exception("BDbObject::__call -> unknown function: $function");
  }

	/****************************************
	* constructor & destructor
	****************************************/

	/**
	 * constructor
   * @param string $tableName table name, if not passed, table name is based on class name, see {@tutorial BauglirWebCore.Database.NamingConventions.pkg}
	 */

	function __construct($tableName = '', $dbConnection = null)
	{
    parent::__construct();
    $this->fDb = $dbConnection;
    if ($tableName) $this->_fTable = $tableName;
    else
    {
      $cName = get_class($this);
      if (strpos(strtolower($cName), 'bdb') === 0) $this->_fTable = strToUnderscore(substr($cName, 3));
      else $this->_fTable = strToUnderscore($cName);
      
    }
    $this->loadColumns();
    $this->Init();
	}


	/****************************************
	* base functions
	****************************************/

  /**
   * return DB based properties as associative array
   * @return mixed[]
   */
  function Data()
  {
    return $this->fRow;
  }

  /**
   * function to initialize fields
   * @ignore
   */
  function Init()
  {
    $this->fRow = $this->fRowDefault;
  }

	/**
	 * inserts data into database and load object according to those data
   *
   * <p>Function does not call {@link BObject::Init()} function to initilaize all data
   * so only passed data are changed.</p>
   *
   * <p>If no (or empty) data is passed {@link BDbObject::fRow} is used. This means if not empty $data is passed,
   * setting properties to object has no effect while saving.</p>
	 *
	 * @param mixed[] $data
	 */
	function Insert($data = array())
	{
		if (!sizeOf($data)) $data = $this->fRow;
		$this->LoadFromRow($data, false);
    $sets = array();
    $vals = array();
    $keys = array();

    foreach($data as $k => $v)
    {
      $k = $this->pcConvert($k);
      
      if ($this->isColumnNumeric($k))
      {
        if ('!#NULL#!' === $v)
        {
          array_push($sets, "%s");
        }
        else if ($this->isColumnUnsigned($k))
        {
          array_push($sets, "%u");
        }
        else
        {
          array_push($sets, "%d");
        }
      }
      else array_push($sets, "%s");
      array_push($vals, $v);
      array_push($keys, "`".$k."`");
    }
    $sql = "insert into ##.`{$this->_fTable}` (" .implode(",", $keys) . ") values (" .implode(",", $sets) . ")";
    //printr($sql);
    //printr($vals);
    $this->Query($sql, $vals);
    return $this->Db->InsertId();
	}

	/**
	 * load object from associative array
   *
   * keys in $row are converted to underscore
	 * @uses BDbObject::pcConvert();
	 * @param mixed[] $row
   * @param bool $initialize if true {@link BDbObject::Init()} is called
	 */
	function LoadFromRow($row, $initialize = true)
	{
		if ($initialize) $this->Init();
		if (sizeOf($row)) foreach($row as $k => $v)
    {
      $k = $this->pcConvert($k);
      if (array_key_exists($k, $this->fRow))
      {
        if ($this->isColumnNumeric($k)) $this->fRow[$k] = ($v == '!#NULL#!') ? 0 : $v + 0;
        elseif ($this->isColumnFloat($k)) $this->fRow[$k] = ($v == '!#NULL#!') ? 0 : $v + 0;
        else $this->fRow[$k] = ($v == '!#NULL#!') ? '' : $v . "";
      }
    }
	}

  /**
   * convert object to JSON
   * @return string
   */
  public function ToJSON($onlyData = false, $params = array())
  {
    $arr = $this->Data();
    return $onlyData ? $arr : json_encode($arr);
  }


  // custom string representation of object
  public function __toString()
  {
    $arr = $this->Data();
    $arr['__type'] = __CLASS__;
    return json_encode($arr);
  }

	/****************************************
	* private & protected functions
	****************************************/

  /*
   * function to change storage table name
   *
   * function also reloads row default values ({@see BDbObject::$fRowDefault})
   *
   * @param string $newName
   *
   * protected function setTable($newName)
  {
    $this->_fTable
  }
   */
  

	/**
	 * perform query
   *
   *
   * function should be never used to change database (e.g. "use mydatabase"), see {@link BDatabase::SelectDatabase()} )
   *
	 * @param string $sql sql query string
	 * @param mixed[] $params query parameters (uses {@link vsprintf} function for formating)
   *    contains list of literals
	 * @return resource query result resource
	 */
	protected function query($sql, $params = array())
  {
    return $this->Db->query($sql, $params);
  }

  /**
   * function returns table identifier
   *
   * @return string "`{$BDatabase->Db}`.{$this->_Table}";
   */
  protected function getDBIdent()
  {
    return "`{$this->Db->Db}`.`{$this->_Table}`";
  }

  /**
   * determine if column is unsigned
   * @param string $name
   * @return bool
   */
  protected function isColumnUnsigned($name)
  {
    $name = $this->pcConvert($name);
    $ident = $this->getDBIdent();
    if (array_key_exists($ident, $GLOBALS['_bwc_fDatabases'][$this->Db->Id]['unsigned']))
    {
      return in_array($name, $GLOBALS['_bwc_fDatabases'][$this->Db->Id]['unsigned'][$ident]);
    }
    return false;
  }

  /**
   * determine if column is numeric
   * @param string $name
   * @return bool
   */
  protected function isColumnNumeric($name)
  {
    $name = $this->pcConvert($name);
    $ident = $this->getDBIdent();
    if (array_key_exists($ident, $GLOBALS['_bwc_fDatabases'][$this->Db->Id]['numeric']))
    {
      return in_array($name, $GLOBALS['_bwc_fDatabases'][$this->Db->Id]['numeric'][$ident]);
    }
    return false;
  }
  
  /**
   * determine if column is float
   * @param string $name
   * @return bool
   */
  protected function isColumnFloat($name)
  {
    $name = $this->pcConvert($name);
    $ident = $this->getDBIdent();
    if (array_key_exists($ident, $GLOBALS['_bwc_fDatabases'][$this->Db->Id]['float']))
    {
      return in_array($name, $GLOBALS['_bwc_fDatabases'][$this->Db->Id]['float'][$ident]);
    }
    return false;
  }  
  
  
  

  /**
   * function to load columns
   *
   * get object properties names and default values from table
   */
  protected function loadColumns()
  {
    global $BConfig;
    $ident = $this->getDBIdent();
    $this->fRowDefault = array();

    if (!array_key_exists($this->Db->Id, $GLOBALS['_bwc_fDatabases']))
    {
      $GLOBALS['_bwc_fDatabases'][$this->Db->Id] = array(
        'unsigned' => array(),
        'numeric' => array(),
        'float' => array(),
        'default' => array(),
        '_default' => array(),
      );
    }


    if (!array_key_exists($ident, $GLOBALS['_bwc_fDatabases'][$this->Db->Id]['default']))
    {
      $GLOBALS['_bwc_fDatabases'][$this->Db->Id]['unsigned'][$ident] = array();
      $GLOBALS['_bwc_fDatabases'][$this->Db->Id]['numeric'][$ident] = array();
      $GLOBALS['_bwc_fDatabases'][$this->Db->Id]['float'][$ident] = array();
      $GLOBALS['_bwc_fDatabases'][$this->Db->Id]['default'][$ident] = array();
      $GLOBALS['_bwc_fDatabases'][$this->Db->Id]['_default'][$ident] = array();
      if ($BConfig->DBDriver == 'mysql')
      {
        
        $rows = $this->getDbFields();
        //if ($res = $this->query("SHOW COLUMNS from ##.`{$this->_fTable}`"))
        {
          //while ($row = $this->Db->FetchAssoc($res))
          if (sizeOf($rows)) foreach ($rows as $row)
          {
            $row = array_change_key_case($row, CASE_LOWER);
            $row['type'] = strtolower($row['type']);
            $val = '';
            
            if (strpos($row['type'], 'int') === 0) $val = 0;
            elseif (strpos($row['type'], 'float') === 0) $val = 0.0;
            elseif (strpos($row['type'], 'double') === 0) $val = 0.0;
            elseif (strpos($row['type'], 'decimal') === 0) $val = 0.0;

            elseif (strpos($row['type'], 'text') === 0) $val = '';
            elseif (strpos($row['type'], 'varchar') === 0) $val = '';
            elseif (strpos($row['type'], 'date') === 0) $val = '';
            elseif (strpos($row['type'], 'time') === 0) $val = '';

            elseif (strpos($row['type'], 'decimal') === 0) $val = 0;
            elseif (strpos($row['type'], 'double') === 0) $val = 0;
            elseif (strpos($row['type'], 'bigint') === 0) $val = 0;
            elseif (strpos($row['type'], 'mediumint') === 0) $val = 0;
            elseif (strpos($row['type'], 'smallint') === 0) $val = 0;
            elseif (strpos($row['type'], 'bool') === 0) $val = 0;
            elseif (strpos($row['type'], 'tinyint') === 0) $val = 0;
            elseif (strpos($row['type'], 'bit') === 0) $val = 0;

            
            if (strpos($row['type'], 'unsigned') !== false) array_push($GLOBALS['_bwc_fDatabases'][$this->Db->Id]['unsigned'][$ident], $row['field']);
            if ($val === 0) array_push($GLOBALS['_bwc_fDatabases'][$this->Db->Id]['numeric'][$ident], $row['field']);
            else if ($val === 0.0) array_push($GLOBALS['_bwc_fDatabases'][$this->Db->Id]['float'][$ident], $row['field']);
            if ($row['default'])
            {
              if ($val === 0) $val = $row['default'] + 0;
              else if ($val === 0.0) $val = $row['default'] + 0.0;
              else $val = $row['default'] . "";
              $GLOBALS['_bwc_fDatabases'][$this->Db->Id]['_default'][$ident][$row['field']] = $val;
            }
            $GLOBALS['_bwc_fDatabases'][$this->Db->Id]['default'][$ident][$row['field']] = $val;
          }
          //$this->Db->FreeRes($res);
        }
      }
    }
    $this->fRowDefault = $GLOBALS['_bwc_fDatabases'][$this->Db->Id]['default'][$ident];
  }
  
  protected function getDbFields()
  {
    //echo $this->_fTable . "<br />";
    $result = array();
    if ($res = $this->query("SHOW COLUMNS from ##.`{$this->_fTable}`"))
    {
      while ($row = $this->Db->FetchAssoc($res))
      {
        $row = array_change_key_case($row, CASE_LOWER);
        $row['type'] = strtolower($row['type']);
        array_push($result, $row);
      }
      $this->Db->FreeRes($res);
    }   
    //var_export($result);
    return $result;
  }

	/**
	 * load object from database
   *
   * if more than one occurence of record is founded, {@link BDbException} is thrown
   *
	 * @param string $name column name
	 * @param mixed $value row value
	 */
	function _loadByValue($name, $value)
	{
    $name = $this->pcConvert($name);
    if ($this->isColumnNumeric($name)) $sql = "select * from ##.{$this->_fTable} where `$name` = %d";
		else $sql = "select * from ##.{$this->_fTable} where `$name` = %s";
    //printr($sql);
		$this->Init();
		if ($res = $this->query($sql, array($value)))
		{
      if ($this->Db->NumRows($res) > 1) throw new BDbException('BDbObject::LoadByValue -> multiple result');
			if ($row = $this->Db->FetchAssoc($res)) $this->LoadFromRow($row);
			$this->Db->FreeRes($res);
		}
	}




  
	/****************************************
	* static functions
	****************************************/

}
$GLOBALS['_bwc_fDatabases'] = array();

/*
$GLOBALS['_bwc_fRowDefault'] = array();
$GLOBALS['_bwc_fRowNumeric'] = array();
$GLOBALS['_bwc_fRowFloat'] = array();
*/
?>
