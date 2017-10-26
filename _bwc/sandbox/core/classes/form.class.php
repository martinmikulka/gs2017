<?php
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');

class Form
{
	const STATUS_OK = 0;
	const STATUS_INVALID_INPUT = 1;
	const STATUS_DUPLICATE_ENTRY = 2;

	const E_FILE_EMPTY = 1362077466;
	const E_FILE_OVERSIZED = 1362480831;
	const E_FILE_UNKNOWN_ERROR = 1362480838;
	const E_FILE_FILETYPE_INVALID = 1378394180;
	const E_IMAGE_FILETYPE_INVALID = 1378393812;


	private $id;
	public $status;
	public $error;



	/**
	 *
	 */
	public function __construct($id)
	{
		$this->id = $id;
		$this->status = Form::STATUS_OK;
		$this->error = array();
	}



	/**
	 *
	 */
	public function StoreToTmp($data = array())
	{
		global $Session;

		$Session->Set($this->id.'TmpStorage' , array('data' => $data , 'status' => $this->status , 'error' => $this->error));
	}



	/**
	 *
	 */
	public function LoadFromTmp()
	{
		global $Session;

		$tmp = $Session->Get($this->id.'TmpStorage' , array());
		$Session->Drop($this->id.'TmpStorage');

		$this->status = value($tmp , 'status' , Form::STATUS_OK);
		$this->error = value($tmp , 'error' , array());

		return value($tmp , 'data' , array());
	}



	/**
	 *
	 */
	public function OK()
	{
		return ($this->status === Form::STATUS_OK);
	}
}
?>
