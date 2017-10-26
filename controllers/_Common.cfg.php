<?php

defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');

/**
 * Constants definition
 */
define('EMAIL_SENDER_EMAIL', 'coop@profisms.cz');
define('EMAIL_SENDER_NAME', 'Soutěže');
define('ADMIN_EMAIL', 'coop@profisms.cz');
define('DATA_DIR', '/data');
define('BILLS_DIR', DATA_DIR . '/bills');
define('PATH_RULES', DATA_DIR . '');
define('GA', '');

/**
 * Session config
 */
session_start();

/**
 * Database connection config
 */
global $BDatabase;

# default database connection parameters
$p = array(
    'driver' => 'mysqli',
    'host' => 'localhost',
    'login' => 'gs',
    'password' => 'aH3KLuLb',
    'port' => '3306',
    'db' => 'gs_2017',
);

$BDatabase = new BMySql($p);
$BDatabase->SelectDatabase($p['db']);
$BDatabase->Query('set names utf8');

/*
GRANT ALL PRIVILEGES ON `gs_2017`.* TO 'gs'@'localhost' IDENTIFIED BY 'aH3KLuLb';
*/

# set default language
$id = Lang::CS;

# recognize language by domain
if (preg_match('~.sk$~', $_SERVER['HTTP_HOST'], $m)) {
    $id = Lang::SK;
}

# set language through parameter
if (isset($_REQUEST['lang'])) {
    if (in_array($_REQUEST['lang'], Lang::GetAvailableLangs())) {
        $id = strtolower($_REQUEST['lang']);
    }
}

# initialize language object
Lang::Init($id);
