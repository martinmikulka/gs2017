<?php
/**
 * File contains {@link BDBSession} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bdbsession.class.php,v 1.6 2010-09-16 11:55:38 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Session
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * function implements session functionality using database
 *
 * REQUIRED TABLES:
 * <pre>
 * 
 *
 * CREATE TABLE `bwc_session` (
 *   `id` varchar(32) collate utf8_czech_ci NOT NULL,
 *   `last_action` int(11) default '0',
 *   `ip` varchar(15) collate utf8_czech_ci default '',
 *   `idle` int(11) default '600',
 *   PRIMARY KEY  (`id`),
 *   KEY `bwc_sessionLastAction` (`last_action`)
 * ) ENGINE=InnoDB
 *
 * CREATE TABLE `bwc_session_var` (
 *   `session` varchar(32) collate utf8_czech_ci NOT NULL,
 *   `name` varchar(255) collate utf8_czech_ci default '',
 *   `value` text collate utf8_czech_ci,
 *   `serialized` int(1) default '0',
 *   PRIMARY KEY `bwc_session_varSessionName` (`session`,`name`),
 *   KEY `bwc_session_varSession` (`session`),
 *   KEY `bwc_session_varName` (`name`),
 *   CONSTRAINT `fk_bwc_session_varbwc_Session` FOREIGN KEY (`session`)
 *      REFERENCES `bwc_session` (`id`)
 *      ON DELETE CASCADE ON UPDATE CASCADE
 * ) ENGINE=InnoDB
 *
 * </pre>
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bdbsession.class.php,v 1.6 2010-09-16 11:55:38 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Session
 */
class BDBSession extends BSession implements IBSession
{

	/****************************************
	* fields
	****************************************/

  /**
   * array holding database connection
   * @var resource
   */
  protected $fDb = null;

  /**
   * see {@link BSession}
   * @var int
   */
  protected $fLastAction = 0;

  /**
   * session ID
   * @var string
   */
  protected $fSid = '';

  /**
   * list of variables associated with session
   * @var mixed[]
   */
  protected $fValues = array();

  /**
   * whether variables has already been loaded from database
   * @var bool
   */
  protected $fValuesLoaded = false;


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
    switch($names)
    {
      case "db":          $result = ($this->fDb == null) ? $BDatabase : $this->fDb;   break;
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
    global $BVersion;
    $names = strtolower($name);
    switch($names)
    {
      case "db":        $this->fDb = $value;   break;
      default:          parent::setter($name, $value);
    }
  }

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
    {
      $this->loadVariables();
      if (array_key_exists($name, $this->fValues))
      {
        unset($this->fValues[$name]);
        $this->Db->Query("delete from ##.#_session_var where `session` = %s and name = %s", array(
          $this->SID,
          $name
        ));
      }
    }
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
    {
      $this->loadVariables();
      return value($this->fValues, $name, $default);
    }
    else throw new BSessionException("BDBSession::Get() -> session not valid");
  }

  /**
   * function returns all variables associated with session
   * @return mixed[]
   * @ignore
   */
  public function GetAllVariables()
  {
    if ($this->valid)
    {
      $this->loadVariables();
      return $this->fValues;
    }
    else throw new BSessionException("BDBSession::GetAllVariables() -> session not valid");
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
      $this->fValues = array();
      $this->fValuesLoaded = false;
      $this->fLastAction = 0;
      setcookie($this->name, '', time() - $this->Idle);
      $sql = "delete from ##.#_session_var where session = %s";
      $this->Db->Query($sql, array($this->SID));

      $sql = "delete from ##.#_session where id = %s";
      $this->Db->Query($sql, array($this->SID));
    }
  }

  /**
   * delete all invalid sessions from DB
   */
  public function KillAllInvalid()
  {
    $this->Db->Query("delete from ##.#_session where (last_action + idle < %d)", array(time()));
    //DELETE FROM bwc_session WHERE (last_action + idle < UNIX_TIMESTAMP(NOW()));

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
    {
      $this->loadVariables();
      $this->fValues[$name] = $value;
      $serialize = (is_array($value) || is_object($value)) ? 1 : 0;
      $value = $serialize ? serialize($value) : $value;
      $this->Db->Query("replace into ##.#_session_var (`session`, `name`, `value`, `serialized`) values (%s, %s, %s, %s)", array(
        $this->SID,
        $name,
        $value,
        $serialize
      ));
    }
    else throw new BSessionException("BDBSession::Set() -> session not valid");
  }

  /**
   * function to start session
   *
   * if SID exists, session is connected to already existing, if not, new session is created
   *
   * if $sessionId is passed, new session with this ID is created
   *
   * @param string $sessionId session ID
   * @ignore
   */
  public function StartSession($sessionId = '')
  {
    $this->fSid = $sessionId;
    if (!$this->fSid) $this->fSid = value($_COOKIE, $this->name, '');
    if (!$this->fSid) $this->fSid = value($_POST, $this->name, '');
    if (!$this->fSid) $this->fSid = value($_GET, $this->name, '');
    $continuous = $this->Valid;
    if ($continuous)
    {

    }
    else
    {
      if (!$this->fSid) $this->fSid = randomHash();
    }
    $this->fLastAction = time();
    $this->Db->Query("/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;");
    $this->Db->Query("replace into ##.#_session (`id`, `last_action`, `ip`, `idle`) values (%s, %d, %s, %d)", array(
      $this->SID,
      $this->fLastAction,
      $_SERVER['REMOTE_ADDR'],
      $this->idle
    ));
    $this->Db->Query("/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;");
    setcookie($this->name, $this->sid, time() + $this->Idle);
    return $continuous;
  }



	/****************************************
	* private & protected functions
	****************************************/

  /**
   * function returns timestamp of last (@link BSession::SessionStart()} call
   *
   *
   * @return timestamp
   * @ignore
   */
  protected function getLastAction()
  {
    return $this->fLastAction;
  }

  /**
   * function returns session identifier
   * @return string
   * @ignore
   */
  protected function getSessionIdent()
  {
    return $this->fSid;
  }
  
  /**
   * function returns session identifier
   * @return string
   * @ignore
   */
  protected function setSessionIdent($newSession)
  {
    $this->fSid = $newSession;
    setcookie($this->name, $this->sid, time() + $this->Idle);
    $_COOKIE[$this->name] = $this->sid;
  }  
  
  

  /**
   * function to set $Idle time, see object description
   * @param int $value
   * @ignore
   */
  protected function setIdle($value)
  {
    if ($this->valid)
    {
      $this->Db->Query("update ##.#_session set `idle` = %d where id = %s", array(
        $this->idle,
        $this->SID,
        
      ));
    }
  }

  /**
   * check whether session is valid
   * @return bool
   * @ignore
   */
  protected function getValid()
  {
    $result = $this->fSid;
    if ($result)
    {
      $wh = array();
      $params = array();
      array_push($wh, " (last_action + idle > %d) ");
      array_push($params, time());
      array_push($wh, " (id = %s) ");
      array_push($params, $this->fSid);
      if ($this->CheckIp)
      {
        array_push($wh, " (ip = %s) ");
        array_push($params, $this->ip);
      }
      $wh = " where " . implode(" and ", $wh);
      $sql = "select * from ##.#_session $wh";
      $result = false;
      if ($res = $this->Db->Query($sql, $params))
      {
        $result = $this->Db->NumRows($res) > 0;
      }
    }
    return $result;
  }

  

  /**
   * function to load variables from database
   *
   * variables are not loaded until needed and are loaded only once
   *
   */
  protected function loadVariables()
  {
    if ($this->Valid && !$this->fValuesLoaded)
    {
      $this->fValuesLoaded = true;
      $this->fValues = array();
      $sql = "select * from ##.#_session_var where `session` = %s";
      if ($res = $this->Db->Query($sql, array($this->sid)))
      {
        while($row = $this->Db->FetchAssoc($res))
        {
          $n = $row['name'];
          $v = $row['serialized'] ? unserialize($row['value']) : $row['value'];
          $this->fValues[$n] = $v;
        }
        $this->Db->FreeRes($res);
      }
    }
    
  }


}
?>