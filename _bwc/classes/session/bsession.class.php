<?php
/**
 * File contains {@link BSession} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bsession.class.php,v 1.5 2009-07-24 02:31:59 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Session
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * Basic session functionality common for all {@link IBSession} implementations
 * 
 *
 * <p><b>Class is not to be used, is used only as predecessor for different kinds of session implementation.</b></p>
 *
 * Session object should implement both {@link IBSession} and BSession
 * 
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bsession.class.php,v 1.5 2009-07-24 02:31:59 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Session
 * @property-read string $Ip remote ip address
 * @property-read string $SID value of session identifier
 * @property-read boolean $Valid whether session is valid
 * @property-read boolean $Name name of session identifier (set to "_BWC_SID_");
 * @property boolean $Idle determine for how long is session valid (in seconds)
 * @property-read timestamp $LastAction timestamp of last {@link IBSession::StartSession()} or {@link IBSession::Touch()}  call
 * @property-read bool $UsesCookies whether cookies are used to store session ID
 */
class BSession extends BObject
{
	/****************************************
	* fields
	****************************************/

  /**
   * wherther to check IP address across session calls
   * @var bool
   */
  public $CheckIp = false;

  /**
   * see $Idle object description
   * @var int
   */
  protected $_fIdle = 600;

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
      case "ip":          $result = $_SERVER['REMOTE_ADDR']; break;
      case "sid":         $result = $this->getSessionIdent(); break;
      case "valid":       $result = $this->getValid(); break;
      case "idle":        $result = $this->_fIdle; break;
      case "lastaction":  $result = $this->getLastAction(); break;
      case "name":        $result = '_BWC_SID_'; break;
      case "usescookies": $result = isset($_COOKIE[$this->name]); break;
      default:            $result = parent::getter($name);
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
      case "idle":
        $this->_fIdle = $value + 0;
        $this->setIdle($value);
      break;
      case "sid": $this->setSessionIdent($value); break;
      default:          parent::setter($name, $value);
    }
  }

	/****************************************
	* constructor & destructor
	****************************************/

	/****************************************
	* base functions
	****************************************/

  /**
   * function loads actual session
   * @return bool true is session exists
   * @ignore
   */
  public function Touch()
  {
    $result = false;
    if (!$result) $result = value($_GET, $this->name, '');
    if (!$result) $result = value($_POST, $this->name, '');
    if (!$result) $result = value($_COOKIE, $this->name, '');
    if ($result)
    {
      if (!($result = $this->StartSession()))
      {
        $this->KillSession();
      }
    }

    return $result;
  }

	/****************************************
	* private & protected functions
	****************************************/

  /**
   * function returns timestamp of last (@link BSession::SessionStart()} call
   * @return timestamp
   */
  protected function getLastAction()
  {
    throw new BSessionException("BSession::getLastAction() -> must be reimplemented by inherited class");
  }

  /**
   * function returns session identifier
   * @return string
   */
  protected function getSessionIdent()
  {
    throw new BSessionException("BSession::getSessionIdent() -> must be reimplemented by inherited class");
  }

  /**
   * check whether session is valid
   * @return bool
   */
  protected function getValid()
  {
    throw new BSessionException("BSession::getValid() -> must be reimplemented by inherited class");
  }

  /**
   * function to set $Idle time, see object description
   * @param int $value
   */
  protected function setIdle($value)
  {
    throw new BSessionException("BSession::setIdle() -> must be reimplemented by inherited class");
  }

  
	/****************************************
	* static functions
	****************************************/

  
}
?>