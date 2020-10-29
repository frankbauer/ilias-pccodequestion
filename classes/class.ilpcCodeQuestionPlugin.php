<?php

include_once("./Services/COPage/classes/class.ilPageComponentPlugin.php");

/**
* Question plugin Example
*
* @author Frank Bauer <frank.bauer@fau.de>
* @version $Id$
* @ingroup ModulesTestQuestionPool
*/
class ilpcCodeQuestionPlugin extends ilPageComponentPlugin
{
    const DATA_VERSION = 1;
	final function getPluginName()
	{
		return "pcCodeQuestion";
	}
	
	/**
	 * Get plugin name 
	 *
	 * @return string
	 */
	function isValidParentType($a_parent_type)
	{
        //return in_array($a_parent_type, array("lm", "wpg", "cont"));
        return in_array($a_parent_type, array("lm", "wpg"));
	}

	/**
	 * Get Javascript files
	 */
	function getJavascriptFiles($a_mode)
	{		
		// if ($a_mode=='presentation'){			
		//  	return array("js/legacyHelper.js");
		// }
		return array();
	}
 
	/**
	 * Get css files
	 */
	function getCssFiles($a_mode)
	{
		// if ($a_mode=='presentation'){
		// 	return codeBlocksUI::getCSSFiles('../../../../../../../'.ilpcCodeQuestionPluginGUI::URL_PATH);
		// }
		return array();
	}

	/**
     * This function is called when the page content is cloned
     * @param array 	$a_properties		(properties saved in the page, should be modified if neccessary)
     * @param string	$a_plugin_version	(plugin version of the properties)
     */
    public function onClone(&$a_properties, $a_plugin_version)
    {		
		if ($question_id = $a_properties['id'])
		{
			$data = $this->loadDataForID($question_id);
			$id = $this->storeData(trim($a_properties['data']));
            $a_properties['id'] = $id;

            //make sure v is the last property, and data ends with a space
            $oldv = $a_properties['v'] + 0;
            unset($a_properties['v']);
            $a_properties['data'] = trim($a_properties['data']).' ';
            $a_properties['v'] = $oldv;
            
		}
    }

    /**
     * This function is called before the page content is deleted
     * @param array 	$a_properties		properties saved in the page (will be deleted afterwards)
     * @param string	$a_plugin_version	plugin version of the properties
     */
    public function onDelete($a_properties, $a_plugin_version)
    {		
		if ($question_id = $a_properties['id']){
			$this->deleteDataWithID($question_id);
		}
	}	

	function storeData($data){
		/** @var $ilDB \ilDBInterface  */
		global $ilDB;
				
		$query = "INSERT INTO `copg_pgcp_codeqstpage` (`data`) VALUES (%s);";        
		$result = $ilDB->manipulateF($query, array('text'), array($data));
		$id = $ilDB->getLastInsertId();
		return $id;
	}

	function updateDataForID($data, $id){
		/** @var $ilDB \ilDBInterface  */
        global $ilDB;
        
        $query = "UPDATE `copg_pgcp_codeqstpage` SET `data` = %s WHERE `code_id` = %s";        
		$result = $ilDB->manipulateF($query, array('text', 'integer'), array($data, $id));
	}



	function deleteDataWithID($id){
		/** @var $ilDB \ilDBInterface  */
        global $ilDB;
				
        $query = "DELETE FROM `copg_pgcp_codeqstpage` WHERE `code_id` = %s";        
		$result = $ilDB->manipulateF($query, array('integer'), array($id));
	}

	function loadDataForID($id){
		/** @var $ilDB \ilDBInterface  */
        global $ilDB;
        
        $query = "SELECT `data` FROM `copg_pgcp_codeqstpage` WHERE `code_id` = %s";        
		$result = $ilDB->queryF($query, array('integer'), array($id));

		$return = ['data'=>''];
		while ($row = $ilDB->fetchAssoc($result))
        {
            $return['data'] = $row['data'];            
		}

		return $return;
	}
}
?>