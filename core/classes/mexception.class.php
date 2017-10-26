<?php

/**
 * Description of MException
 *
 * @author Martin Mikulka
 */
class MException extends PDOException
{


    public function __construct($message = null, $code = null)
    {
        $this->message = $message;
        $this->code = $code;
    }


}