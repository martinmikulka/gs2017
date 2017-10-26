<?php
/**
 * File contains {@link BMySql} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bmysql.class.php,v 1.10 2011-11-15 11:58:39 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Database
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');

/**
 * class to work with MySql database
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bmysql.class.php,v 1.10 2011-11-15 11:58:39 bauglir Exp $
 * @property-read bool $Mi true if mysqli driver is used
 * @package BauglirWebCore
 * @subpackage Database
 * @todo not tested for mysql driver (only mysqli)
 */
class BMySql extends BDatabase
{
	/****************************************
	* fields
	****************************************/

  /**
   * whether connection is in transaction
   * @var bool
   * @ignore
   */
  private $pTransactionCounter = 0;

	/****************************************
	* field getters & setters
	****************************************/

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
      case "mi":   $result = $this->Params('driver') == 'mysqli'; break;
      case "transaction_counter":   $result = $this->pTransactionCounter; break;
      default:     $result = parent::getter($name);
    }
		return $result;
  }

	/****************************************
	* constructor & destructor
	****************************************/


	/**
	* constructor
  * @param mixed[] $params connection parameters
  * @param bool $connect whether to connect automatically
   * @ignore
	*/
	function __construct($params = array(), $connect = true)
	{
    parent::__construct($params, $connect);
	}

  
	/****************************************
	* base functions
	****************************************/

  /**
   * Number of rows affected by last query
   * @return integer
   */
  public function AffectedRows()
  {
    $result = -1;
    if ($this->Connected)
    {
      if ($this->Mi) { if ($this->fConnection) $result = mysqli_affected_rows($this->fConnection); }
      else $result = mysql_affected_rows($this->fConnection);
    }
    return $result;
  }	


  /**
   * begin transaction
   * @ignore
   */
  public function Begin()
  {
    if (!$this->pTransactionCounter)
    {
      $this->Query('begin');
    }
    else
    {
      if ($this->SupressNestedTransactions)
      {
        throw new Exception("BMySql::Begin() - nested transactions supressed", E_USER_ERROR);
      }
    }
    $this->pTransactionCounter = $this->pTransactionCounter + 1;
  }

  /**
   * close connection to database
   * @ignore
   */
  public function Close()
  {
    if ($this->Connected)
    {
      if ($this->Mi) { if ($this->fConnection) mysqli_close($this->fConnection) ;}
      else mysql_close($this->fConnection);
    }
    parent::Close();
  }

  /**
   * commit transaction
   * @ignore
   */
  public function Commit($forceCommit = false)
  {
    $this->pTransactionCounter = $this->pTransactionCounter - 1;
    if (!$this->pTransactionCounter)
    {
      $this->Query('commit');
    }
  }

	/**
	* return last connection error number
   * @ignore
	* @return int
	*/
	public function Errno()
  {
		if ($this->Mi)
		{
			if (!$this->fConnection)	return mysqli_connect_errno();
			else return mysqli_errno($this->fConnection);
		}
    else
    {
      if ($this->fConnection) mysql_errno($this->fConnection);
      else return mysql_errno();
    }
  }

	/**
	* return last connection error string
	* @return string
   * @ignore
	*/
	public function Error()
  {
		if ($this->Mi)
		{
      if (!$this->fConnection)	return mysqli_connect_error();
			else return mysqli_error($this->fConnection);
		}
    else
    {
      if ($this->fConnection) mysql_error($this->fConnection);
      else return mysql_error();
    }
  }

  /**
   * return SQL escaped string
   * @param string $literal string to be escaped
   * @return string
   * @ignore
   */
  public function Escape($literal)
  {
    if ($this->Connected) return $this->Mi ? mysqli_real_escape_string($this->fConnection, $literal) : mysql_real_escape_string($literal, $this->fConnection);
    else return parent::Escape($literal);
  }

	/**
	* Fetch a row as an associative array
	*
	* @param resource $result query result resource
	* @return mixed[]
   * @ignore
	*/
  public function FetchAssoc($result)
  {
		$res = null;
		if ($this->fConnection)
		{
      if ($this->Mi) $res = mysqli_fetch_assoc($result);
		}
		return $res;
  }

  /**
   * Free the memory associated with a result
   *
   * <p><b>FreeRes</b> can be used as alias
   *
   * @param resource $result query result resource
   * @ignore
   */
  public function FreeResult($result)
  {
		if ($this->fConnection)
		{
			if ($this->Mi) mysqli_free_result($result);
		}
  }

	/**
	* return last inserted id
	*
	* @return int
   * @ignore
	*/
	function InsertId()
  {
		$result = 0;
		if ($this->fConnection)
		{
			if ($this->Mi) $result = mysqli_insert_id($this->fConnection);
		}
		return $result;
  }

	/**
	* return number of rows
	*
	* @param resource $result query result resource
	* @return int
   * @ignore
	*/
	function NumRows($result)
  {
		$res = 0;
		if ($this->fConnection)
		{
			if ($this->Mi) $res = mysqli_num_rows($result);
		}
		return $res;
  }

  //[string $host [, string $username [, string $passwd [, string $dbname [, int $port [, string $socket]]]]]] )
  /**
   * open connection to database
   * @param mixed[] $params connection parameters, if not passed, parameters used in {@link BDatabase::__construct()} are used
   *   $params should look like this:
   *   <code>
   *   $params = array(
   *     'driver' => 'mysqli',
   *     'host' => 'localhost',
   *     'username' => 'CoreUser',
   *     'passwd' => '????',
   *     'dbname' => 'core',
   *     'port' => 3306,
   *     'socket' => '/tmp/mysql.sock',
   *   );
   *   </code>
   *
   * @return bool true if connection was opened
   */
  public function Open($params = null)
  {
    global $BDebug;
    parent::Open($params);
    if (!$this->Params('driver')) $this->fParams['driver'] = 'mysql';

    $this->fixParams();
		if ($this->Mi)
			$this->fConnection = @mysqli_connect($this->Params('host'), $this->Params('username'), $this->Params('passwd'), $this->Params('dbname'), $this->Params('port', '3306'), $this->Params('socket'));
    else
      $this->fConnection = @mysql_connect($this->Params('host'), $this->Params('username'), $this->Params('passwd'), true);
		if (!$this->fConnection)
		{
			$this->fConnection = null;
			if ($fcn = $this->OnError) $fcn($this);
      else if ($BDebug) $BDebug->DatabaseError($this);
		}
		$this->_fLastSql = '';
		return $this->fConnection != null;

  }

	/**
	 * perform query
   *
   * function should be never used to change database (e.g. "use mydatabase"), {@see IBDatabase::SelectDatabase())
   *
	 * @param string $sql sql query string
	 * @param mixed[] $params query parameters (uses {@link vsprintf} function for formating)
   *    contains list of literals
	 * @return resource query result resource
   * @ignore
	 */
	public function Query($sql, $params = array())
  {
    global $BDebug;
    parent::Query($sql, $params);
		if (strpos(strtolower(trim($sql)), 'use') === 0) throw new BDBException("MySql::Query - cannot select database using USE query", E_USER_ERROR);
		$result = null;
		if ($this->fConnection)
		{
      //echo $sql;
      //printr($params);
			if (sizeOf($params))
			{
				for ($i = 0; $i < sizeOf($params); $i++)
				{
          //echo $params[$i] . " - ";
          if ($params[$i] === '!#NULL#!')
          {
            $params[$i] = 'null';
            //echo " null - ";
          }
					else if (is_string($params[$i]))
					{
            //echo " string - ";
						$params[$i] = $this->Escape($params[$i]);
						$params[$i] = "'".$params[$i]."'";
					}
          //echo $params[$i] . "<br/>";
				}
				$sql = vsprintf($sql, $params);
			}
      $sql = $this->fixQuery($sql);
      $this->_fLastSql = $sql;
			if ($this->Mi) $result = mysqli_query($this->fConnection, $sql);
      $this->handleError();

		}
		return $result;
  }

  /**
   * rollback transaction
   * @ignore
   */
  public function Rollback()
  {
    $this->pTransactionCounter = $this->pTransactionCounter - 1;
    if (!$this->pTransactionCounter)
    {
      $this->Query('rollback');
    }
  }

  /**
   * select database
   * @param string $database
   * @return bool
   * @ignore
   */
  public function SelectDatabase($database)
  {
		$result = false;
		if ($this->fConnection)
		{
			if ($this->Mi) $result = mysqli_select_db($this->fConnection, $database);

      echo mysqli_error($this->fConnection);

      if ($result) $this->_fDb = $database;
      else $this->handleError();

		}
		return $result;
  }



	/****************************************
	* private & protected functions
	****************************************/

  /**
   * function to fix {@link BDatabase::$params}
   *
   * <p>Different key can be used.</p>
   * 
   * @ignore
   */
  protected function fixParams()
  {
    $this->fParams['host']     = $this->Params('host') ? $this->Params('host') : $this->Params('ip');
    $this->fParams['username'] = $this->Params('username') ? $this->Params('username') :
                                  ($this->Params('user') ? $this->Params('user') : $this->Params('login'));
    $this->fParams['passwd']   = $this->Params('passwd') ? $this->Params('passwd') :
                                  ($this->Params('pwd') ? $this->Params('pwd') : $this->Params('password'));
    $this->fParams['dbname']   = $this->Params('dbname') ? $this->Params('dbname') :
                                  ($this->Params('database') ? $this->Params('database') : $this->Params('db'));
  }


  /**
   * function used to quote identifier
   * @param string $ident
   * @return string
   * @ignore
   */
  protected function quoteIdentifier($ident)
  {
    return "`" . $ident . "`";
  }
  
	/****************************************
	* static functions
	****************************************/

  
}
?>