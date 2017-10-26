<?php

/**
 *  basic confuguration
 *  @version $Id: bconfig.class.php,v 1.2 2011/11/21 11:34:16 root Exp $
 *  @author Bronislav Klucka, Bronislav.Klucka@bauglir.com,
 *          Copyright &copy; 2009+ Bronislav Klucka
 *
 * This script is licenced under BSD licence: http://licence.bauglir.com/bsd.php
 *
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');

/**
 *  basic configuration
 *  @version $Id: bconfig.class.php,v 1.2 2011/11/21 11:34:16 root Exp $
 *  @author Bronislav Klucka, Bronislav.Klucka@bauglir.com,
 *          Copyright &copy; 2009+ Bronislav Klucka
 *
 *
 */
class BConfig
{
    /**
     * full path to BWC root dir, path is used for including files
     *
     * has to contain slash at the end
     * @var string
     */
    public $Core = '_bwc/';

    /**
     * path to web pages root dir, path is used for including files
     *
     * has to contain slash at the end
     * @var string
     */
    public $WwwRoot = './';

    /**
     * used database driver, see {@link BVersion}
     * @var string
     */
    public $DBDriver = 'mysql';


    /*     * **************************************
     * constructor & destructor
     * ************************************** */


    /**
     * constructor
     */
    function __construct()
    {
        static $instances = 0;                            /*         * < int, number of instances to ensure singleton */
        //if ($instances) trigger_error('Cannot instance more than one Config class.', E_USER_WARNING);
        if ($instances)
            throw new Exception('BConfig::__construct -> Cannot instance more than one Config class.');
        $instances++;
    }


}
$GLOBALS['BConfig'] = new BConfig();
require_once($BConfig->Core . "config.php");
