<?php
/**
 * File contains {@link BMVCException} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bmvcexception.class.php,v 1.1 2009-04-16 05:00:09 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage MVC
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * BWC MVC exception class
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bmvcexception.class.php,v 1.1 2009-04-16 05:00:09 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage MVC
 */
class BMVCException extends Exception
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