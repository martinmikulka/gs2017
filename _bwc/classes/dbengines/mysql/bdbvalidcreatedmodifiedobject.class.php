<?php
/**
 * File contains {@link BDbValidCreatedModifiedObject} class definition
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bdbvalidcreatedmodifiedobject.class.php,v 1.4 2010-11-27 11:05:06 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Database
 */
defined('BWC_VALID_INCLUDE') or header('HTTP/1.1 401 Unauthorized') or die('401 Unauthorized');


/**
 * generic object with id, valid, created, modified properties
 *
 * @author Bronislav Klucka <Bronislav.Klucka@bauglir.com>
 * @copyright Copyright (c) 2009+, Bronislav Klučka
 * @license http://licence.bauglir.com/bsd.php BSD License
 * @version $Id: bdbvalidcreatedmodifiedobject.class.php,v 1.4 2010-11-27 11:05:06 bauglir Exp $
 * @package BauglirWebCore
 * @subpackage Database
 */
class BDbValidCreatedModifiedObject extends BDbIdObject
{
	/****************************************
	* base functions
	****************************************/

  /**
   * @ignore
	*/
	function Save($data)
	{
    $data = $this->fixSaveData($data);

    $data['modified'] = datetime();
    if (!$data['id']) $data['created'] = $data['modified'];
    return parent::Save($data);
	}

  
}
?>