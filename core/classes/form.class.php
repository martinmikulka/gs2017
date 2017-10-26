<?php

class Form
{
    private $id;
    public $success = false;
    public $data = array();
    public $messages = array();


    public function __construct($id)
    {
        $this->id = $id;
    }


    public function StoreToTmp()
    {
        $_SESSION[get_class() . $this->id] = array(
            'success' => $this->success,
            'data' => $this->data,
            'messages' => $this->messages,
        );
    }


    public function LoadFromTmp()
    {
        $stored = value($_SESSION, get_class() . $this->id, array());
        $this->success = value($stored, 'success', false);
        $this->data = value($stored, 'data', array());
        $this->messages = value($stored, 'messages', array());
        unset($_SESSION[get_class() . $this->id]);
    }


    public function GetMessageClass($id) {
        return in_array($id, $this->messages) ? " active" : "";
    }

}
