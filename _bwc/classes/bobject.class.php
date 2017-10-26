<?php
/**
 * File contains {@link BObject} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id
 * @package BauglirWebCore
 * @subpackage Core
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');

/**
 * general object parent
 *
 * <p>This class should be parent of all classes.</p>
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bobject.class.php,v 1.11 2010-11-27 11:05:06 bauglir Exp $
 * @property-read float $_TimeCreated number of microseconds sinc object was created
 * @todo assign function
 * @package BauglirWebCore
 * @subpackage Core
 */
class BObject
{
	/****************************************
	* fields
	****************************************/
  /**
   * time of object creation (see {@link microtime})
   * @var float
   * @ignore
   */
  protected $_fTimeCreated             = 0;
  
  /**
   * list to store rewrited names so there does not have to be processing every time
   * @var string[]
   */
  protected $_fConvertFields           = array();


	/****************************************
	* field getters & setters
	****************************************/

	/**
	* function is called at the beginnig of magic __get and __set function to rewrite passed name
  * E.g. LastName, SurName can be rewrite to lName, sName and __get function processes only this name
	*
	* @param string $id field name to be rewrited
	* @uses BObject::$_fConvertFields if name does not exists as key in this array, function process the name
  *   and saves it (original as key, new one as value), if exists, already processed is returned
  * @return string new field name
	*/
	protected function pcConvert($id)
	{
    return $id;
	}

  /**
   * generic getter
   *
   * <p>should be overwritten by child classes</p>
   * <p>if class does not process passed name, function should call parent's implementation (this should provide bubbling to all parents until name is processed or fieald actualy does not exists)</p>
   *
   * @param string $name field name
   * @return mixed
   * @see BObject::__get()
   */
  protected function &getter($name)
  {
    throw new Exception("Object::getter($name)", E_USER_ERROR);
  }

  /**
	 * generic setter
   * 
   * <p>should be overwritten by child classes</p>
   * <p>if class does not process passed name, function should call parent's implementation (this should provide bubbling to all parents until name is processed or fieald actualy does not exists)</p>
	 * @param string name field name
	 * @param mixed value new value
   * @see BObject::__set()
	 */
  protected function setter($name, $value)
  {

    throw new Exception("Object::setter($name, $value)", E_USER_ERROR);
  }

	/**
	 * magic __get function implementation
   *
	 * <p>Function steps:</p>
   *   - {@link BObject::pcConvert()} function to convert name
   *   - {@link BObject::getter()} function to retreive value
   *   - if no retreived, {@link BObject} properties are processed
   *   - if no one processed {@link Exception} if thrown
   *
   * @param string name field name
	 * @return mixed
	 */
  public function &__get($name)
	{
    $name = $this->pcConvert($name);
    $result = null;
    try
    {
      $result = $this->getter($name);
      return $result;
    }
    catch (Exception $e)
    {
      if (strtolower($name) == "_timecreated")
      {
        $result = microtime(true) - $this->_fTimeCreated;
      }
      else throw new Exception("Object::__get($name) - field does not exists", E_USER_ERROR);
    }
    return $result;
  }

	/**
	 * magic __set function implementation
   *
	 * <p>Function steps:</p>
   *   - {@link BObject::pcConvert()} function to convert name
   *   - {@link BObject::setter()} function to set value
   *   - if no set, {@link BObject} properties are processed
   *   - if no set {@link Exception} if thrown
   *
	 * @param string name field name
	 * @param mixed value new value
	 * @return mixed
	 */
  public function __set($name, $value)
	{
    $name = $this->pcConvert($name);
    try
    {
      $this->setter($name, $value);
    }
    catch (Exception $e)
    {
      if ($name == '`1&^jkhasbnma979-e') echo "D";
      else throw new Exception("Object::__set($name, $value) - field does not exists", E_USER_ERROR);
    }
  }

  /**
   * convert object to JSON
   * @param bool $onlyData whether to return only data and do not JSONize them
   * @param mixed[] $params extended parameters
   * @return string
   */
  public function ToJSON($onlyData = false, $params = array())
  {
    return $onlyData ? $this : json_encode($this);
  }


	/****************************************
	* constructor & destructor
	****************************************/

	/**
	 * constructor
   * each descendant should call parent's constructor
	 */
	function __construct()
	{
    $this->_fTimeCreated = microtime(true);
	}

	/****************************************
	* base functions
	****************************************/

  /**
   * function to initialize fields
   */
  function Init() {}




	/****************************************
	* private & protected functions
	****************************************/




  
	/****************************************
	* static functions
	****************************************/

  
}
?>