<?php
/**
 * File contains {@link BCookieSession} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bcookiesession.class.php,v 1.6 2011-06-02 19:12:51 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Session
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * object implements session functionality using PHP built-in session functions
 *
 * some of session variables might have _bwc_ prefix, those variables are for internal purpose only
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bcookiesession.class.php,v 1.6 2011-06-02 19:12:51 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Session
 */
class BCookieSession extends BSession implements IBSession
{

	/****************************************
	* base functions
	****************************************/

  /**
   * drop variable from session
   * @param string $name variable name
   * @ignore
   */
  public function Drop($name)
  {
    if ($this->valid)
      if (array_key_exists($name, $_SESSION))
        unset($_SESSION[$name]);
  }


  /**
   * retreive variable associated with session
   * @param string $name variable name
   * @return mixed variable value
   * @ignore
   */
  public function Get($name, $default = 0)
  {
    if ($this->valid)
      return value($_SESSION, $name, $default);
    else
      throw new BSessionException("BCookieSession::Get() -> session not valid");
  }

  /**
   * function returns all variables associated with session
   * @return mixed[]
   * @ignore
   */
  public function GetAllVariables()
  {
    if ($this->valid)
      return $_SESSION;
    else
      throw new BSessionException("BCookieSession::GetAllVariables() -> session not valid");
  }


  /**
   * kill (destroy) session and all associated data
   *
   * if $sessionId is passed, session associated with this SID is destroyed
   *
   * notice that for some session implementations $sessionId can have no meaning
   *
   * @param string $sessionId
   * @ignore
   */
  public function KillSession($sessionId = '')
  {
    if ($this->valid)
    {
      if ($sessionId) session_id($sessionId);
      $_SESSION = array();
      //if ($this->UsesCookies)
      {
        if (isset($_COOKIE[$this->name]))
        {
          //setcookie($this->name, '', time()-$this->Idle, '/');
          setcookie(session_name(), session_id(), time() - $this->Idle);
        }
      }
      session_destroy();
    }
  }

  /**
   * set variable associated with session
   * @param string $name variable name
   * @param mixed $value variable value
   * @ignore
   */
  public function Set($name, $value)
  {
    if ($this->valid)
      $_SESSION[$name] = $value;
    else
      throw new BSessionException("BCookieSession::Set() -> session not valid");
  }

  /**
   * function to start session
   *
   * if SID exists, session is connected to already existing, if not, new session is created
   *
   * if $sessionId is passed, new session with this ID is created
   *
   * @param string $sessionId session ID
   * @return bool true if session is continuous, false if new session is created
   * @ignore
   */
  public function StartSession($sessionId = '')
  {
    session_name($this->name);
    if ($sessionId) session_id($sessionId);
    session_start();
    setcookie(session_name(), session_id(), time() + $this->Idle);
    $continuous = $this->valid;
    $_SESSION['_bwc_last_action'] = time();
    $_SESSION['_bwc_ip'] = $_SERVER['REMOTE_ADDR'];
    return $continuous;
  }



	/****************************************
	* private & protected functions
	****************************************/

  /**
   * function returns timestamp of last (@link BSession::SessionStart()} call
   * @return timestamp
   * @ignore
   */
  protected function getLastAction()
  {
    if (session_id())
      return value($_SESSION, '_bwc_last_action', 0) + 0;
    else
      return 0;
  }

  /**
   * function returns session identifier
   * @return string
   * @ignore
   */
  protected function getSessionIdent()
  {
    return session_id();
  }

  /**
   * function to set $Idle time, see object description
   * @param int $value
   * @ignore
   */
  protected function setIdle($value)
  {
    if (session_id())
    {
      $_SESSION['_bwc_idle'] = $value + 0;
      setcookie(session_name(), session_id(), time() + $this->Idle);
    }
  }

  /**
   * check whether session is valid
   * @return bool
   * @ignore
   */
  protected function getValid()
  {
    $result = session_id();
    if ($result)
    {
      $time = time();
      $totime = $this->getLastAction() + $this->Idle;
      $result = $totime > $time;
    }
    if ($result && $this->CheckIp)
    {
      $result = value($_SESSION, '_bwc_ip') == $this->ip;
    }
    return $result;
  }
  

  
}
?>
