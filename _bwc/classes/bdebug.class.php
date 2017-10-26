<?php
/**
 * File contains {@link BDebug} class definition and initiates instance of {@link BDebug} class named $BDebug
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bdebug.class.php,v 1.20 2010-11-27 11:05:06 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Core
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');

/**
 * Class allows extended debugging
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bdebug.class.php,v 1.20 2010-11-27 11:05:06 bauglir Exp $
 * @property-read float $LastTime number of microseconds sinc this property was called (or sinc object was created)
 * @package BauglirWebCore
 * @subpackage Core
 * @todo saving errors to file
 */
class BDebug extends BObject
{
	/****************************************
	* fields
	****************************************/
  /**
   * number of microseconds sinc this property was called (or sinc object was created)
   * @var float
   * @ignore
   */
  protected $_fLastTime = 0;

  /**
   * whether extended errr reporting should be applied
   * @var bool
   */
  public $Enabled = true;

  /**
   * whether extended errr reporting should be full (with code) or only simple text
   * @var bool
   */
  public $Extended = true;

  /**
   * callback for returning error string
   * @var function
   */
  public $Callback = null;



	/****************************************
	* field getters & setters
	****************************************/

  /**
   * generic getter
   * @param string $name field name
   * @return mixed
   * @ignore
   */
  protected function &getter($name)
	{
		if (strtolower($name) == "lasttime")
    {
      $time = microtime(true) - $this->_fLastTime;
      $this->_fLastTime = microtime(true);
      return $time;
    }
    else return parent::getter($name);
  }	

	/****************************************
	* constructor & destructor
	****************************************/

	/**
  * constructor is responsible for ensuring this class follows singleton pattern
	*/
	function __construct()
	{
    /**
     * number of instances to ensure singleton
     * @staticvar
     */
    static $instances = 0;                    
    if ($instances) throw new Exception('Cannot instance more than one BDebug class.');
    $instances++;
    parent::__construct();
    $this->_fLastTime = $this->_fTimeCreated;
	}

	/****************************************
	* base functions
	****************************************/

  /**
   * base error handler, displayes error
   *
   * <p>function uses {@link debug_backtrace() backtrace stack}</p>
   * <p>see {@link set_error_handler()}</p>
   *
   * @param int $errno error number
   * @param string $errstr error description
   * @param string $errfile file where error occured
   * @param int $errline line where error occured
   */
  function ErrorHandler($errno, $errstr, $errfile, $errline)
  {
    $array = $this->backtrace();
    array_push($array, array(
      'errno' => $errno,
      'errstr' => $errstr,
      'file' => $errfile,
      'line' => $errline,
      'function' => '',
      'args' => array(),
    ));

    $this->displayStack($array);
  }

  /**
   * database error (connection or sql)
   *
   * <p>function uses {@link debug_backtrace() backtrace stack}</p>
   *
   * @param object $database instance of {@link IBDatabase}
   */
  function DatabaseError($database)
  {
    if (!error_reporting()) return;
    if (!$this->Enabled)
    {
      $estr =$database->Error() ."\n | SQL: " . ($database->connected ? $database->lastSql : 'Cannot connect');
      echo "ERRNO: " . $database->ErrNo() . ";\nERRSTR: $estr;\n";
      return;
    }
    $array = $this->backtrace(1);
    array_push($array, array(
      'errno' => $database->ErrNo(),
      'errstr' => $database->Error() ."\n | SQL: " . ($database->connected ? $database->lastSql : 'Cannot connect'),
      'file' => '',
      'line' => '',
      'function' => '',
      'args' => array(),
    ));
    $this->displayStack($array);
  }

  /**
   * base exception handler, displayes error
   *
   * <p>function uses {@link debug_backtrace() backtrace stack}</p>
   * <p>see {@link set_exception_handler()}</p>
   *
   * @param object $error instance of {@link Exception}
   */
  function Exception($error)
  {
    $eClass = get_class($error);
    $array = array();
    array_push($array, array(
      'errno' => $error->getCode(),
      'errstr' => $eClass . " - " . $error->getMessage(),
      'file' => $error->getFile(),
      'line' => $error->getLine(),
      'function' => '',
      'args' => array(),
    ));
  

    $trace = $error->getTrace();
    foreach($trace as $e)
    {
      array_push($array, array(
        'file' => value($e,'file'),
        'line' => value($e, 'line'),
        'function' => $e['function'],
        'args' => value($e, 'args'),
        'errno' => 0,
        'errstr' => '',
      ));
    }
    $array = array_reverse($array);
    //printr($array);
    $this->displayStack($array);
    return;

    
    $array = array();
    $trace = $error->getTrace();
    foreach($trace as $e)
    {
      array_push($array, array(
        'file' => $e['file'],
        'line' => $e['line'],
        'function' => $e['function'],
        'args' => $e['args'],
        'errno' => 0,
        'errstr' => '',
      ));
    }

    $caller = array_shift($array);
    $array = array_reverse($array);
    array_push($array, array(
      'file' => $caller['args'][2],
      'line' => $caller['args'][3],
      'function' => '',
      'args' => array(),
      'errno' => $caller['args'][0],
      'errstr' => $caller['args'][1],
    ));

    $this->displayStack($array);
  }

	/****************************************
	* private & protected functions
	****************************************/

  /**
   * display error stack
   * @param mixed[] $stack
   * @ignore
   */
  private function displayStack($stack)
  {
    if ($this->Callback)
    {
      ob_start();
    }
    if ($this->Extended)
    {
      echo "\n\n\n\n";
      $this->echoHeader();
      echo "<div class='babama8aepjs9a78as783'>\n";
    }
    for ($j = sizeOf($stack) - 1, $i = $j; $i >= 0; $i--) { $this->displayCode($stack[$i]);}
    if ($this->Extended)
    {        
      echo "</div>\n\n\n";
    }
    else
    {
      echo "\n";
    }
    if ($this->Callback)
    {
      $cnt = ob_get_contents();
      ob_end_clean();
      $fnc = $this->Callback;
      $fnc($cnt, $stack);
    }
  }

  /**
   * function prepares backtrace stack
   * 
   * <p>function uses {@link debug_backtrace() backtrace stack}</p>
   *
   * @param int $level number of backtrace items to remove (errors are handled by series of {@link BDebug} functions,
   *   those should not be displayed)
   * @return mixed[] backtrace stack
   * @ignore
   */
  private function backtrace($level = 2)
  {
    $dbg = debug_backtrace();
    while($level > 0)
    {
      array_shift($dbg);
      $level--;
    }
    $err = array_shift($dbg);
    $array = array();
    for ($j = sizeOf($dbg) - 1, $i = $j; $i >= 0; $i--)
    {
      $e = $dbg[$i];
      array_push($array, array(
        'file' => value($e, 'file', ''),
        'line' => value($e, 'line', ''),
        'function' => $e['function'],
        'args' => value($e, 'args'),
        'errno' => 0,
        'errstr' => '',
      ));

    }
    return $array;
  }

  /**
   * display backtrace stack record as HTML
   * @param mixed[] $errInfo
   * @ignore
   */
  private function displayCode($errInfo)
  {
    if (!$this->Extended)
    {
      //printr($errInfo);
      if ($errInfo['errstr'])
      {
        if ($errInfo['errno']) echo ($this->error2str($errInfo['errno'])) . "; ";
        if ($errInfo['errstr']) echo ($errInfo['errstr']) . "; ";
      }
      if ($errInfo['file'])
      {
        if ($errInfo['file']) echo ("File: " . basename($errInfo['file'])) . "; ";
      }
      if ($errInfo['function'])
      {
        if ($errInfo['function']) echo ("Function: " . $errInfo['function']) . "; ";
        if (sizeOf($errInfo['args'])) echo ("Arguments: " . arrayToString(arrayChangeKeyValue($errInfo['args'], array('password', 'passwd', 'pwd'), '???'))) . "; ";
      }
      if ($errInfo['file'])
      {
        if ($errInfo['file']) echo ("File: " . $errInfo['file']) . "; ";
        if ($errInfo['line']) echo ("Line: " . $errInfo['line']);
      }
      echo "\n";
      return;
    }

    $linesCount = 7;
    $data = file_get_contents($errInfo['file']);
    $lines = explode("\n", $data);
    $sLine = max(1, $errInfo['line'] - $linesCount);
    $eLine = min(sizeOf($lines), $errInfo['line'] + $linesCount + 1);

    //highlight and remove code and span strailing elements
    $res = trim(highlight_string($data, true));

    $res = trim(substr($res, strlen('<code>'), strlen($res) - strlen("</code>") - strlen('<code>')));
    $res = trim(substr($res, strpos($res, '>') + 1));
    $res = trim(substr($res, 0, strlen($res) - strlen("</span>")));
 

    $res = preg_replace("~<br>~i", "<br />", $res);
    $res = preg_replace("~<br/>~i", "<br />", $res);
    $res = join("", explode("\n", $res));
    $res = join("", explode("\r", $res));

    $res .= "<span></span>";
    do
    {
      $old = $res;
      $res = preg_replace("~<br /></span>~i", "</span><br />", $res);
    } while ($old != $res);
    preg_match_all("~(.*)(<span[^>]*>.*</span>)~iU", $res, $data);
    $lines = array();
    foreach($data[0] as $d)
    {
      preg_match_all("~(.*)(<span[^>]*>.*</span>)~i", $d, $data);
      array_push($lines, array("no" => $data[1][0], "sp" => $data[2][0]));
    }
    $allLines = array();
    foreach($lines as $line)
    {
      if ($line['no'])
        array_push($allLines, $line['no']);
      if ($line['sp'])
      {
        preg_match("~(<span[^>]*>)(.*)(</span>)~i", $line['sp'], $data);
        $d = preg_replace("~<br />~", "</span><br />".$data[1], $data[0]);
        array_push($allLines, $d);
      }
    }
    $code = explode("<br />", join("", $allLines));

    $i = 1;
    $divid = "a" . md5(uniqid(rand(), true));
    echo "<div class='errBox'>\n";
    echo "<div class='errInfo' onclick=\"var div = document.getElementById('$divid'); div.style.display = div.style.display == 'block' ? 'none' : 'block'; return false; \" >\n";
    echo "<p>";
    $arrow = "<span>&nbsp;&#x25bc;&nbsp;</span>";
    if ($errInfo['errstr'])
    {
      if (sizeOf($code) && $errInfo['file'] && $errInfo['line'])
      {
        echo $arrow;
        $arrow = '';
      }
      if ($errInfo['errno']) echo "<strong>" . htmlspecialchars($this->error2str($errInfo['errno'])) . "; " . "</strong>";
      if ($errInfo['errstr']) echo "<strong>" . htmlspecialchars($errInfo['errstr']) . "; " . "</strong>";
    }
    if ($errInfo['file'])
    {
      echo $arrow;
      $arrow = '';
      if ($errInfo['file']) echo "<strong>" . htmlspecialchars("File: " . basename($errInfo['file'])) . ";</strong> ";
    }
    if ($errInfo['function'])
    {
      echo $arrow;
      $arrow = '';
      if ($errInfo['function']) echo "<strong>" . htmlspecialchars("Function: " . $errInfo['function']) . "; " . "</strong>";
      if (sizeOf($errInfo['args'])) echo "<strong>" . htmlspecialchars("Arguments: " . arrayToString(arrayChangeKeyValue($errInfo['args'], array('password', 'passwd', 'pwd'), '???'))) . "; " . "</strong>";
    }
    if ($errInfo['file'])
    {
      echo $arrow;
      $arrow = '';
      if ($errInfo['file']) echo htmlspecialchars("File: " . $errInfo['file']) . "; ";
      if ($errInfo['line']) echo htmlspecialchars("Line: " . $errInfo['line']);
    }
    echo "</p>";
    echo "</div>";
    if (sizeOf($code) && $errInfo['file'] && $errInfo['line'])
    {
      echo "<div class='errData' id='$divid'><ul>\n";
      foreach($code as $line)
      {
        if ((($i >= $sLine) && ($i <= $eLine)))
        {
          $c = $i == $errInfo['line'] ? " class='error' " : '';
          echo "<li $c><span class='line'>$i</span><code>".$line."</code></li>";
        }
        $i++;
      }
      echo "</ul></div>";
    }
    echo "</div>";
  }

  /**
   * display header for debugging (CSS and JS)
   * @ignore
   */
  private function echoHeader()
  {
    static $displayed = 0;
    if ($displayed) return;
    $displayed++;

    echo <<<EOT
<style type='text/css'>
.babama8aepjs9a78as783
{
  line-height: 12px;
  font-size: 12px;
  font-family : "Courier New", Courier, monospace;
  background: white;
  margin: 0px;
  padding: 0px;
  overflow: auto;
  border: 3px double red;
  border-left: 14px double red;
  border-right: 14px double red;
  padding: 10px 20px;
  margin: 15px;
}

.babama8aepjs9a78as783 *
{
  font-family : "Courier New", Courier, monospace;
}

.babama8aepjs9a78as783 li
{
  white-space: nowrap;
}

.babama8aepjs9a78as783 code
{
  white-space: pre;
}

.babama8aepjs9a78as783 .errData
{
  overflow: auto;
  display: none;
  border: 1px solid blue;
padding: 1px;
}


.babama8aepjs9a78as783 ul
{
 
}

.babama8aepjs9a78as783 ul
{
 
}

.babama8aepjs9a78as783 ul,
.babama8aepjs9a78as783 ul li
{
  list-style-type: none;
  margin: 0px;
  padding: 0px;
}
.babama8aepjs9a78as783 .line
{
  display: inline-block;
  text-align: right;
  width: 40px;
  padding: 0px 5px;
  background: #ffe57c;
  margin-right: 3px;
}


.babama8aepjs9a78as783 li.error span.line
{
  background: #ff9696;
}
.babama8aepjs9a78as783 li.error
{
  background: #ffc8c8;
}

.babama8aepjs9a78as783 li.error
{
  border: 1px solid red;
}

.babama8aepjs9a78as783 .errInfo
{
  margin: 2px 0px;
  padding: 2px;
  background: #e5efff;
  border: 1px solid #a2c1f5;

  -moz-border-radius: 5px;
  -opera-border-radius: 5px;
  -o-border-radius: 5px;
  -opera-border-radius: 5px;
  -webkit-border-radius: 5px;
  border-radius: 5px;
}

.babama8aepjs9a78as783 .errInfo p
{
  margin: 0px;
  padding: 0px;
  font-size: 85%;
color: #808080;
}

.babama8aepjs9a78as783 .errInfo p strong,
.babama8aepjs9a78as783 .errInfo p span
{
  font-size: 120%;
  color: #7B0808;
}

.babama8aepjs9a78as783 .errInfo
{
  cursor: pointer;
}


</style>
EOT;
  }


  /**
   * convert error as bitmask (integer) to string representation
   * @param int $error
   * @return string
   * @ignore
   */
  private function error2str($error)
  {
    switch($error)
    {
      case "1": return "E_ERROR"; break;
      case "2": return "E_WARNING"; break;
      case "4": return "E_PARSE"; break;
      case "8": return "E_NOTICE"; break;
      case "16": return "E_CORE_ERROR"; break;
      case "32": return "E_CORE_WARNING"; break;
      case "64": return "E_COMPILE_ERROR"; break;
      case "128": return "E_COMPILE_WARNING"; break;
      case "256": return "E_USER_ERROR"; break;
      case "512": return "E_USER_WARNING"; break;
      case "1024": return "E_USER_NOTICE"; break;
      case "6143": return "E_ALL"; break;
      case "2048": return "E_STRICT"; break;
      case "4096": return "E_RECOVERABLE_ERROR"; break;
      case "8192": return "E_DEPRECATED"; break;
      case "16384": return "E_USER_DEPRECATED"; break;
      default: return "UNKNOWN";
    }
  }

  
	/****************************************
	* static functions
	****************************************/
  
}

$GLOBALS['BDebug'] = new BDebug();

set_error_handler('__asj3ja10203jajjas_sldkaadn');
set_exception_handler('__asj3ja10203jajjas_sldkaadnE');
register_shutdown_function('__asj3ja10203jajjas_sldkaadnS');
/**
 * @ignore
 */
function __asj3ja10203jajjas_sldkaadn($errno, $errstr, $errfile, $errline)
{
  global $BDebug;
  if($errno == E_RECOVERABLE_ERROR)
  {
    if(preg_match('/^Argument (\d)+ passed to (?:(\w+)::)?(\w+)\(\) must be an instance of (\w+), (\w+) given/', $errstr, $match))
    {
      if($match[4] == $match[5])
        return ;
    }
  }
  if (error_reporting())
  {
    if ($BDebug->Enabled) $BDebug->errorHandler($errno, $errstr, $errfile, $errline);
    else echo "ERRNO: $errno;\nERRSTR: $errstr;\nERRFILE: $errfile;\nERRLINE: $errline";
  }
}
/**
 * @ignore
 */
function __asj3ja10203jajjas_sldkaadnE($e)
{
  global $BDebug;
  if (error_reporting())
  {
    if ($BDebug->Enabled) $BDebug->exception($e);
    else echo "EX ERRNO: {$e->getCode()};\nERRSTR: {$e->getMessage()};\nERRFILE: {$e->getFile()};\nERRLINE: {$e->getLine()}";
  }
}

/**
 * @ignore
 */
function __asj3ja10203jajjas_sldkaadnS()
{
  global $BDebug;
  if ($eLevel = error_reporting())
  {
    $error = error_get_last();
    if (($error !== NULL) && ($eLevel & $error['type']))
    {
      if ($BDebug->Enabled) $BDebug->errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
      else echo "ERRNO: $errno;\nERRSTR: $errstr;\nERRFILE: $errfile;\nERRLINE: $errline";
    }
  }  
}
?>