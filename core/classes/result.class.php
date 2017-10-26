<?php

class Result
{
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';


    private $status = self::STATUS_SUCCESS;
    private $messages = array();


    public function AddMessage($code)
    {
        $this->messages[] = $code;
    }


    public function AddError($code)
    {
        $this->status = self::STATUS_ERROR;
        $this->messages[] = $code;
    }


    public function GetMessages()
    {
        return $this->messages;
    }


    public function OK()
    {
        return ($this->status == self::STATUS_SUCCESS);
    }


    public function ToJSON()
    {
        $result = new stdClass;
        $result->status = $this->status;
        $result->messages = $this->messages;
        return json_encode($result);
    }


}
