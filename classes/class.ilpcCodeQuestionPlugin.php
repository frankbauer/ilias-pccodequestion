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
}
?>