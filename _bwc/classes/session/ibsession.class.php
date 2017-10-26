<?php
/**
 * File contains {@link IBSession} interface definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: ibsession.class.php,v 1.4 2009-07-22 14:35:22 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Session
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * This interface represent session functionality
 *
 * session ID should be reffered as SID regardless of actual name
 *
 * Session object should implement both IBSession and {@link BSession}
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: ibsession.class.php,v 1.4 2009-07-22 14:35:22 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Session
 */
interface IBSession
{

	/****************************************
	* base functions
	****************************************/

  /**
   * drop variable from session
   * @param string $name variable name
   */
  public function Drop($name);

  /**
   * retreive variable associated with session
   * @param string $name variable name
   * @param mixed $default default value if no varible with name $name is registered
   * @return mixed variable value
   */
  public function Get($name, $default = 0);

  /**
   * function returns all variables associated with session
   * @return mixed[]
   */
  public function GetAllVariables();

  /**
   * kill (destroy) session and all associated data
   *
   * if $sessionId is passed, session associated with this SID is destroyed
   *
   * notice that for some session implementations $sessionId can have no meaning
   *
   * @param string $sessionId
   */
  public function KillSession($sessionId = '');

  /**
   * set variable associated with session
   * @param string $name variable name
   * @param mixed $value variable value
   */
  public function Set($name, $value);

  /**
   * function to start session
   *
   * if SID exists, session is connected to already existing, if not, new session is created
   *
   * if $sessionId is passed, new session with this ID is created
   *
   * @param string $sessionId session ID
   * @return bool true if session is continuous, false if new session is created
   */
  public function StartSession($sessionId = '');

  /**
   * function loads actual session (and does not create new one)
   *
   * function resets session timeout
   *
   * @return bool true is session exists
   */
  public function Touch();

}
?>