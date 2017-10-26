<?php
/**
 * File contains {@link BTranslation} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: btranslation.class.php,v 1.2 2009-04-16 04:57:53 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage MVC
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 *
 * DO NOT USE, NEEDS TO BE CHANGED
 *
 * object handling string translations
 *
 * <p>Literals to be translated should be closed into double braces: <samp>{{text to translate}}</samp>.</p>
 *
 * <p>If you want to process string to be translated:</p>
 * <ul>
 *    <li>replace double opening braces with chr(1) (<samp>{{ -> &#x01;</samp>)</li>
 * </ul>
 * <p>Or use {@link BTranslation::Escape()}</p>
 *
 * <p>Language parameters are passed as language code by ISO 639-1.</p>
 *
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: btranslation.class.php,v 1.2 2009-04-16 04:57:53 bauglir Exp $
 * @property-read string $Language VIEW language (alias $Lang)
 * @package BauglirWebCore
 * @subpackage MVC
 * @ignore
 */
class BTranslation extends BObject
{
	/****************************************
	* fields
	****************************************/
  /**
   * VIEW language (ISO 639-1 Code)
   * @var string
   */
  protected $_fLang = 'en';

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
      case "lang":
      case "language":
        $result = $this->_fLang;
      break;
      default:          $result = parent::getter($name);
    }
		return $result;
  }

	/****************************************
	* constructor & destructor
	****************************************/

	/**
	 * constructor
   * @param string $language VIEW language, if none passed en is used
	 */
	function __construct($language = 'en')
  {
    $this->_fLang = $language ? $language : 'en';
  }

	/**
	* destructor
	*/
	function __destruct()	{}

	/****************************************
	* base functions
	****************************************/

  /**
   * escape string to be used before translations
   *
   * use this function to escape unknown content (e.g. data read from database) in file
   * that is about to be processed by Translate* function, do not use otherwise
   *
   * @param string $string
   * @return string
   */
  function Escape($string)
  {
    //return preg_replace("/{{/", "~{~{", ($string));
    return preg_replace("/{{/", "\x01", ($string));
  }

  /**
   * function to translate file
   *
   * file is handled as PHP file, use for working with VIEW
   *
   * @param string $filename name of file to be translated
   * @param string $language language string should be translated to, if not passed, default {@link BTranslation::$Lang} is used
   * @return string
   */
  function TranslateFile($filename, $language = '')
  {
    $content = file_get_contents($filename);
    return $this->TranslateString($content, $language);
  }

  /**
   * function to translate string
   *
   * $string represents PHP code
   *
   * @param string $string string to be translated
   * @param string $language language string should be translated to, if not passed, default {@link BTranslation::$Lang} is used
   * @return string
   */
  function TranslateString($string, $language = '')
  {
    if (!$language) $language = $this->Language;
    $string = (getEvalContent($string));

    $string = preg_replace_callback(
      "/{{([^}]*)}}/",
      create_function('$matches', 'return "[" . $matches[1] . "]";'),
      $string
    );

    $string = $this->UnEscape(($string));

    return $string;
    

  }


  /**
   * unescape string after
   *
   * @param string $string
   * @return string
   */
  function UnEscape($string)
  {
    $string = (preg_replace("/\x01/", "{{", $string));
    return (preg_replace("/&#x01;/", "{{", $string));

  }


	/****************************************
	* private & protected functions
	****************************************/


  
	/****************************************
	* static functions
	****************************************/

  
}
?>