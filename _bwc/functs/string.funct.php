<?php
/**
 * File contains string related functions
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: string.funct.php,v 1.5 2009-05-02 11:39:04 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Core
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');



/**
 * function splits string into array filled with either quoted or unquoted parts
 *
 * example string to escape: <samp>this is 'some escaped' string 'i\'d like' to 'split' into chunks \'cause "I'd like" to see "the \"main\" result" now</samp>
 * used ' and " as quotation marks and \ as escape character
 * <pre>
 * result: Array
 * (
 *     [0] => this is
 *     [1] => 'some escaped'
 *     [2] =>  string
 *     [3] => 'i\'d like'
 *     [4] =>  to
 *     [5] => 'split'
 *     [6] =>  into chunks \'cause
 *     [7] => "I'd like"
 *     [8] =>  to see
 *     [9] => "the \"main\" result"
 *     [10] =>  now
 * )
 * </pre>
 *
 * <p>Parts 0, 1, 2, 4, 5, 8, 10 are simple unquoted or quoted parts.</p>
 * <p>Parts 3, 9 are quoted parts containing escaped quotation mark.</p>
 * <p>Parts 6 is unquoted part containing escaped quotation mark.</p>
 * <p>Parts 7 is quoted part containing one of passed quotation mark not actually quoting.</p>
 *
 * @param string $string string to split
 * @param string|string[] $quoteChars quote characters
 * @param char $escapeChar character to escape quote marks
 * @return string[]
 */
function splitQuotedString($string, $quoteChars = "'", $escapeChar = '\\')
{
  if (!is_array($quoteChars)) $quoteChars = array($quoteChars);

  //all double occurences of $escapeChar should be preserved and not considered as escaping
  //e.g \\' does not escape quotation
  $pregReplacemenet = "`~``~~`2`sana,kha9p129y=-[    127312hhH  A7t120";
  $string = preg_replace("~".preg_quote($escapeChar.$escapeChar)."~", $pregReplacemenet, $string);

  $result = array();
  $position = 0; // position in string
  $strLength = strlen($string);
  $inQuotes = false; //whether $position is currently in quoted part of string
  $quote = ''; //current quote
  $last = '';//previously processed char;
  while($position < $strLength)
  {
    $chunk = ''; //currently processed string chunk
    $char = $string{$position}; //current character

    //find part before first quote char
    while ((!in_array($char, $quoteChars) || ($last == $escapeChar)) && ($position < $strLength))
    {
      $chunk .= $char;
      ++$position;
      $last = $char;
      if ($position < $strLength)
        $char = $string{$position};
    }

    //quote char founded
    if ($chunk) array_push($result, $chunk);
    $chunk = '';
    $quote = $char;
    $last = $escapeChar;
    while($position < $strLength)
    {
      ++$position;
      if (($char == $quote) && ($last != $escapeChar))
      {
        $chunk .= $char;
        break;
      }
      $chunk .= $char;
      $last = $char;
      if ($position < $strLength)
        $char = $string{$position};
    }
    if ($chunk) array_push($result, $chunk);
  }
  for ($i = 0; $i < sizeOf($result); ++$i)
  {
    $result[$i] = preg_replace("/".preg_quote($pregReplacemenet)."/", preg_quote($escapeChar.$escapeChar), $result[$i]);
  }

  return $result;
}

/**
 * function converts camelCase or PascalCase notation to under_score one
 *
 * <p>all occurences of capital letter is prefixed by undercore and converted to lowercase
 * (first letter case is ignored and considered small):</p>
 *
 * <p><samp>under_score_notation => under_score_notation</samp></p>
 * <p><samp>camelCaseNotation => camel_case_notation</samp></p>
 * <p><samp>PascalCaseNotation => pascal_case_notation</samp></p>
 *
 * @param string $string
 * @return string
 */
function strToUnderscore($string)
{
  $string{0} = strtolower($string{0});
  for ($i = 1; $i < strlen($string); ++$i)
  {
    if ($string{$i} != strtolower($string{$i}))
    {
      $string = substr($string, 0, $i) . "_" . strtolower($string{$i}) . substr($string, $i + 1);
    }
  }
  return $string;
}


/**
 * generate random hash (in lower case)
 *
 * hash does not have to be unique, it depends on length of hash
 *
 * @param int $length hash length
 * @param bool $onlyAlpha if true, only a-z character are used
 */
function randomHash($length = 32, $onlyAlpha = false)
{
  $result = '';
  while (strlen($result) < $length)
  {
    $hash = strtolower(md5(uniqid(rand(), true)));
    if ($onlyAlpha) $hash = preg_replace("/[^a-z]/i", '', $hash);
    $hash = (rand(0, 10) < 5) ? str_rot13($hash) : $hash;
    $result .= $hash;
  }
  $result = substr($result, 0, $length);
  return $result;
}

/**
 * function unifyes quotation in string
 * @param string to process $string
 * @param mixed $quotationToChange list of quotation characters to be replaced
 * @param string[1] $quote new quote character
 * @param string[1] $escapeChar character used for escaping
 * @return string
 */
function unifyQuotation($string, $quotationToChange, $quote, $escapeChar = '\\')
{
  if (in_array($quote, $quotationToChange)) throw new Exception("unifyQuotation: quote exists in quotationToChange");
  $result = splitQuotedString($string, array_merge($quotationToChange, array($quote)));
  printr($result);

  for ($i = 0; $i < sizeOf($result); ++$i)
  {
    $line = $result[$i];
    if (strlen($line) < 1) continue;

    if (in_array($line{0}, $quotationToChange))
    {
      $quoteOld = $line{0};
      $line = substr($line, 1, strlen($line) - 2);
      $line = preg_replace("/".preg_quote("$escapeChar$quoteOld")."/", preg_quote($quoteOld), $line);
      $line = preg_replace("/".preg_quote("$quote")."/", preg_quote("$escapeChar$quote"), $line);
      $line = "'" . $line . "'";
    }
    $result[$i] = $line;


    //$result[$i] = preg_replace("/".preg_quote($pregReplacemenet)."/", preg_quote($escapeChar.$escapeChar), $result[$i]);
  }
  printr($result);

  return join("", $result);
}

/**
* validate email
*
* @param string $email, email to validate
* @return bool
*/
function validateEmail($email)
{
  if(!preg_match("/^((?:(?:(?:[a-zA-Z0-9][\.\-\+_]?)*)[a-zA-Z0-9])+)\@((?:(?:(?:[a-zA-Z0-9][\.\-_]?){0,62})[a-zA-Z0-9])+)\.([a-zA-Z0-9]{2,6})\$/", $email))
  {
    return false;
  }
  return true;
}
?>
