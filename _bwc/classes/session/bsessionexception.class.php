<?php
/**
 * File contains {@link BSessionException} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bsessionexception.class.php,v 1.1 2009-04-12 17:53:59 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Session
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * BWC session exception class
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bsessionexception.class.php,v 1.1 2009-04-12 17:53:59 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Session
 */
class BSessionException extends Exception
{
	/**
   * constructor
   * @param string $message
   * @param int $code
   */
	function __construct($message, $code = E_USER_ERROR)
  {
    parent::__construct($message, $code);
  }
  
}
?>