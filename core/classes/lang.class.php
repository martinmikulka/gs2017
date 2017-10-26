<?php

defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');

abstract class Lang
{
    const CS = 'cs';
    const SK = 'sk';

    private static $id;
    private static $strings = array();

    public static function Init($id = self::CS)
    {
        self::$id = $id;
        self::LoadStrings();
    }

    public static function GetAvailableLangs()
    {
        return array(
            self::CS,
            self::SK
        );
    }

    private static function LoadStrings()
    {
        $path = ROOT_DIR . '/core/langs/' . self::$id . '.csv';
        if (is_file($path)) {
            if ($fh = fopen($path, 'r')) {
                $i = 0;
                while ($row = fgets($fh)) {
                    $row = trim($row);
                    if ($row) {
                        list($key, $translation) = explode(';', $row);
                        self::$strings[$key] = $translation;
                    }
                }
                fclose($fh);
            }
        }
    }


    public static function Tr($key, $arg = array())
    {
        if (isset(self::$strings[$key])) {
            return vsprintf(self::$strings[$key], $arg);
        }

        return $key;
    }


    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }


    public function __wakeup()
    {
        trigger_error('Unserializing is not allowed.', E_USER_ERROR);
    }


}