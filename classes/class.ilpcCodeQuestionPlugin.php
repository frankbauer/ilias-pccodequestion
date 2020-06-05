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
		return in_array($a_parent_type, array("lm", "wpg", "stys"));
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