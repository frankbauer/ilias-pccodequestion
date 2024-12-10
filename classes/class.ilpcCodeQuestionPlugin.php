<?php

include_once("./Services/COPage/classes/class.ilPageComponentPlugin.php");
require_once "./Services/Component/classes/class.ilPlugin.php";

/**
 * Question plugin Example
 *
 * @author Frank Bauer <frank.bauer@fau.de>
 * @version $Id$
 * @ingroup ModulesTestQuestionPool
 */
class ilpcCodeQuestionPlugin extends ilPageComponentPlugin
{
	const DATA_VERSION = 2;
	/** @var ilassCodeQuestionPlugin */
	protected $plugin;
	public function __construct(
		\ilDBInterface $db,
		\ilComponentRepositoryWrite $component_repository,
		string $id
	) {
		parent::__construct($db, $component_repository, $id);

		$this->plugin = ilpcCodeQuestionPlugin::initPluginObject("assCodeQuestion");
	}
	final function getPluginName(): string
	{
		return "pcCodeQuestion";
	}

	public static function initPluginObject(string $plugin_name): ilPlugin|null
	{
		global $DIC;
		$ilLog = $DIC->logger()->root();

		try {
			$component_repository = $DIC["component.repository"];
			$component_factory = $DIC["component.factory"];
			$info = $component_repository->getPluginByName($plugin_name);

			$plugin_obj = $component_factory->getPlugin($info->getId());

			if (!is_null($info) && $info->isActive()) {
				return $plugin_obj;
			} else {
				throw new ilPluginException($plugin_name . ' plugin is not active');
			}
		} catch (ilPluginException $e) {
			$ilLog->write("Error loading Plugin " . $plugin_name . ": " . $e->getMessage(), $ilLog->ERROR);			
		}

		return null;
	}

	/**
	 * Get plugin name 
	 *
	 * @return string
	 */
	function isValidParentType(string $a_type): bool
	{
		//return in_array($a_type, array("lm", "wpg", "cont"));
		return in_array($a_type, array("lm", "wpg", "cont"));
	}

	/**
	 * Get Javascript files
	 */
	function getJavascriptFiles(string $a_mode): array
	{
		// if ($a_mode=='presentation'){			
		//  	return array("js/legacyHelper.js");
		// }
		return array();
	}

	/**
	 * Get css files
	 */
	function getCssFiles(string $a_mode): array
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
	public function onClone(array &$a_properties, string $a_plugin_version): void
	{
		if ($question_id = $a_properties['id']) {
			$data = $this->loadDataForID($question_id);

			$id = $this->storeData(trim($a_properties['data']));
			$a_properties['id'] = $id;

			//make sure v is the last property, and data ends with a space
			$oldv = $a_properties['v'] + 0;
			unset($a_properties['v']);
			$a_properties['data'] = base64_encode($a_properties['data']);
			$a_properties['is_base64'] = true;
			$a_properties['v'] = $oldv;
		}
	}

	/**
	 * This function is called before the page content is deleted
	 * @param array 	$a_properties		properties saved in the page (will be deleted afterwards)
	 * @param string	$a_plugin_version	plugin version of the properties
	 */
	public function onDelete(array $a_properties, string $a_plugin_version, bool $move_operation = false): void
	{
		if ($question_id = $a_properties['id']) {
			$this->deleteDataWithID($question_id);
		}
	}

	function storeData($data)
	{
		/** @var $ilDB \ilDBInterface  */
		global $ilDB;

		$query = "INSERT INTO `copg_pgcp_codeqstpage` (`data`) VALUES (%s);";
		$result = $ilDB->manipulateF($query, array('text'), array($data));
		$id = $ilDB->getLastInsertId();
		return $id;
	}

	function updateDataForID($data, $id)
	{
		/** @var $ilDB \ilDBInterface  */
		global $ilDB;

		$query = "UPDATE `copg_pgcp_codeqstpage` SET `data` = %s WHERE `code_id` = %s";
		$result = $ilDB->manipulateF($query, array('text', 'integer'), array($data, $id));
	}



	function deleteDataWithID($id)
	{
		/** @var $ilDB \ilDBInterface  */
		global $ilDB;

		$query = "DELETE FROM `copg_pgcp_codeqstpage` WHERE `code_id` = %s";
		$result = $ilDB->manipulateF($query, array('integer'), array($id));
	}

	function loadDataForID($id)
	{
		/** @var $ilDB \ilDBInterface  */
		global $ilDB;

		$query = "SELECT `data` FROM `copg_pgcp_codeqstpage` WHERE `code_id` = %s";
		$result = $ilDB->queryF($query, array('integer'), array($id));

		$return = ['data' => ''];
		while ($row = $ilDB->fetchAssoc($result)) {
			$return['data'] = $row['data'];
		}

		return $return;
	}
}
?>