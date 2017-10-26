<?php
/**
 * File contains {@link IBDatabase} interface definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: ibdatabase.class.php,v 1.3 2009-04-12 19:11:57 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Database
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');

/**
 * Generic interface to work with database
 *
 * see {@link BDatabase} for extended description
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: ibdatabase.class.php,v 1.3 2009-04-12 19:11:57 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Database
 */
interface IBDatabase
{

  /**
   * begin transaction
   */
  public function Begin();

  /**
   * close connection to database
   */
  public function Close();

  /**
   * commit transaction
   */
  public function Commit();

	/**
	* return last connection error number
	* @return int
	*/
	public function Errno();

	/**
	* return last connection error string
	* @return string
	*/
	public function Error();

  /**
   * return SQL escaped string
   * @param string $literal string to be escaped
   * @return string
   */
  public function Escape($literal);

	/**
	* Fetch a row as an associative array
	*
	* @param resource $result query result resource
	* @return mixed[]
	*/
  public function FetchAssoc($result);

  /**
   * Free the memory associated with a result
   *
   * <p><b>FreeRes</b> can be used as alias
   *
   * @param resource $result query result resource
   */
  public function FreeResult($result);
  /**
   * @see IBDatabase::FreeResult()
   * @ignore
   */
  public function FreeRes($result);

	/**
	* return last inserted id
	*
	* @return int
	*/
	function InsertId();

	/**
	* return number of rows
	*
	* @param resource $result query result resource
	* @return int
	*/
	function NumRows($result);

  /**
   * open connection to database
   * @param mixed[] $params connection parameters, if not passed, parameters used in {@link BDatabase::__construct()} are used
   * @return bool success
   */
  public function Open($params = array());

	/**
	 * perform query
   *
   * function should be never used to change database (e.g. "use mydatabase"), {@see IBDatabase::SelectDatabase())
   *
	 * @param string $sql sql query string
	 * @param mixed[] $params query parameters (uses {@link vsprintf} function for formating)
   *    contains list of literals 
	 * @return resource query result resource
	 */
	public function Query($sql, $params = array());

  /**
   * rollback transaction
   */
  public function Rollback();

  /**
   * select database
   * @param string $database
   */
  public function SelectDatabase($database);
}
?>
