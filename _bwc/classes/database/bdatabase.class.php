<?php
/**
 * File contains {@link BDatabase} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bdatabase.class.php,v 1.8 2011-04-27 15:27:17 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Database
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');

/**
 * Generic class to work with database
 *
 *
 * <p>Object handles pattern expandation, if founded (not in literals), pattern is expanded according to rules described below.</p>
 *
 * <p>PATTERN: SQL queries can use ## identifier as current database e.g. <samp>select * from ##.table</samp>.
 * If no database is selected, pattern is removed.</p>
 *
 * <p>PATTERN: SQL queries can use #_ identifier as table prefix (in {@link BDatabase::$tablePrefix}) e.g. <samp>select * from #_user</samp>.
 * If no {@link BDatabase::$tablePrefix} specified, pattern is removed.</p>
 *
 * <p>If database pattern is used, identifier shouldn't be quoted.</p>
 *
 * <p>Default {@link BDbObject BWC database objects} uses table prefix set to bwc_.</p>
 *
 * <p><b>Class is not to be used, is used only as predecessor for DB engines classes.</b></p>
 * 
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bdatabase.class.php,v 1.8 2011-04-27 15:27:17 bauglir Exp $
 * @property-read string $LastSql last SQL query
 * @property-read string $Database selected database
 * @property-read string $Db selected database (BDatabase::$Database alias);
 * @property-read bool $Connected true if database is connected
 * @package BauglirWebCore
 * @subpackage Database
 */
class BDatabase extends BObject implements IBDatabase
{
	/****************************************
	* fields
	****************************************/
  /**
   * selected database name
   * @var string
   * @ignore
   */
	protected $_fDb             = '';
  /**
   * last SQL query
   * @var string
   * @ignore
   */
	protected $_fLastSql        = '';
  /**
   * connection resource
   * @var resource
   */
	protected $fConnection       = null;
  /**
   * database id
   * @var string
   */
	protected $fId                = '';
  /**
   * connection parameters
   * @var mixed[]
   */
  protected $fParams           = array();
  /**
   * error callback
   *
   * if error occures while connecting or in query this callback is called
   * otherwise {@link BDebug::DatabaseError()} error handler is called
   *
   * @var string
   */
  public    $OnError          = null;

  /**
   * see {@link BDatabase} description (patters)
   * @var string
   */
  public $TablePrefix         = 'bwc_';

  /**
   * if property is set to true, there can be no nested transactions
   * @var boolean
   */
  public $SupressNestedTransactions   = false;


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
      case "lastsql":   $result = $this->_fLastSql; break;
      case "connected": $result = $this->fConnection ? true : false; break;
      case "db":
      case "database":
                        $result = $this->_fDb; break;
      case "id":        $result = $this->fId; break;
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
    $names = strtolower($name);
    switch($names)
    {
      default:          parent::setter($name, $value);
    }
  }

	/****************************************
	* constructor & destructor
	****************************************/

	/**
	* constructor
  * @param mixed[] $params connection parameters
  * @param bool $connect whether to connect automatically
	*/
	function __construct($params = array(), $connect = true)
	{
    parent::__construct();
    $this->fId = randomHash();
    $this->fParams = $params;
    if ($connect && sizeOf($this->fParams)) $this->Open($this->fParams);
	}

	/**
	* close connection
	*/
	function __destruct()
  {
    $this->Close();
  }

	/****************************************
	* base functions
	****************************************/

  /**
   * begin transaction
   */
  public function Begin()
  {
    throw new BDbException('BDatabase::Begin => inherited class has to override this function');
  }


  /**
   * close connection to database
   */
  public function Close()
  {
    $this->_fLastSql = '';
    $this->fConnection = null;
  }

  /**
   * commit transaction
   */
  public function Commit()
  {
    throw new BDbException('BDatabase::Commit => inherited class has to override this function');
  }
  
	/**
	* return last connection error number
	* @return int
	*/
	public function Errno()
  {
    return 0;
  }

	/**
	* return last connection error string
	* @return string
	*/
	public function Error()
  {
    return "";
  }

  /**
   * return SQL escaped string
   * @param string $literal string to be escaped
   * @return string
   */
  public function Escape($literal)
  {
    return $literal;
  }

	/**
	* Fetch a row as an associative array
	*
	* @param resource $result query result resource
	* @return mixed[]
	*/
  public function FetchAssoc($result)
  {
    throw new BDbException('BDatabase::FetchAssoc => inherited class has to override this function');
  }

  /**
   * Free the memory associated with a result
   *
   * <p><b>FreeRes</b> can be used as alias
   *
   * @param resource $result query result resource
   */
  public function FreeResult($result)
  {
    throw new BDbException('BDatabase::FreeResult => inherited class has to override this function');
  }
  /**
   * @see BDatabase::FreeResult()
   */
  public function FreeRes($result)
  {
    $this->FreeResult($result);
  }

	/**
	* return last inserted id
	*
	* @return int
	*/
	function InsertId()
  {
    throw new BDbException('BDatabase::InsertId => inherited class has to override this function');
  }


	/**
	* return number of rows
	*
	* @param resource $result query result resource
	* @return int
	*/
	function NumRows($result)
  {
    throw new BDbException('BDatabase::NumRows => inherited class has to override this function');
  }

  /**
   * open connection to database
   * @param mixed[] $params connection parameters, if not passed, parameters used in {@link BDatabase::__construct()} are used
   * @return bool success
   */
  public function Open($params = array())
  {
    if ($this->Connected) $this->Close();
    if (sizeOf($params)) $this->fParams = $params;
    return false;
  }

  /**
   * return connection parameter/parameter
   * @param string $name if passed, one parameter value is returned, else all parameters are returned
   * @param mixed $default default value
   * @return mixed
   */
  public function Params($name = null, $default = '')
  {
    if ($name) return value($this->fParams, $name, $default);
    else return $this->fParams;
  }

	/**
	 * perform query
   *
   * function should be never used to change database (e.g. "use mydatabase"), {@see IBDatabase::SelectDatabase())
   *
   * <p>Because of {@link vsprintf} usage, % must be passed as %% when used directly in $sql, or $params should be used to pass such parameter.</p>
   *
	 * @param string $sql sql query string
	 * @param mixed[] $params query parameters (uses {@link vsprintf} function for formating)
   *    contains list of literals
	 * @return resource query result resource
	 */
	public function Query($sql, $params = array())
  {
    $this->_fLastSql = $sql;
  }

  /**
   * rollback transaction
   */
  public function Rollback()
  {
    throw new BDbException('BDatabase::Rollback => inherited class has to override this function');
  }

  /**
   * select database
   * @param string $database
   * @return bool
   */
  public function SelectDatabase($database)
  {
    throw new BDbException('BDatabase::SelectDatabase => inherited class has to override this function');
  }


	/****************************************
	* private & protected functions
	****************************************/

  /**
   * function fixes patterns in SQL query, should be called right before particular SQL driver query PHP function is called
   *
   * <p><b>Important: </b> Implementation in {@link BDatabase} is responsible for pattern expansions. Childs implementations
   * should always call parent one</p>
   *
   * see {@link BDatabase} description for available patters
   *
   * @param string $sql
   * @return string
   */
  protected function fixQuery($sql)
  {
    $quotation = array("'", '"');
    $params = splitQuotedString($sql, $quotation);
    $dbPatern = $this->Db ? $this->quoteIdentifier($this->Db) . "." : '';
    $tbPatern = $this->TablePrefix ? ($this->TablePrefix) : '';
    for($i = 0, $j = sizeOf($params); $i < $j; $i++)
    {
      $line = $params[$i];
      if ($line)
      {
        if (!in_array($line{0}, $quotation))
        {
          $line = str_replace("##.", $dbPatern, $line);
          $line = str_replace("#_", $tbPatern, $line);
        }
      }
      $params[$i] = $line;
    }
    return join('', $params);
  }

  /**
   * function calls error handlers
   *
   * function should be called after every process that can end up with database error
   * @return bool true if error occured
   */
  protected function handleError()
  {
    global $BDebug;
    if ($res = $this->Errno())
    {
      if ($fcn = $this->OnError) $fcn($this);
      else if ($BDebug) $BDebug->DatabaseError($this);
    }
    return ($res != 0);
  }

  /**
   * function used to quote identifier
   * @param string $ident
   * @return string;
   */
  protected function quoteIdentifier($ident)
  {
    return $ident;
  }

  
	/****************************************
	* static functions
	****************************************/

  
}
?>