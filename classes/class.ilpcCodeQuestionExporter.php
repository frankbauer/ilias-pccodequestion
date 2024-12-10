<?php
/**
 * Exporter class for the pcCodeQuestionPlugin Plugin
 *
 * @author Frank Bauer <frank.bauer@fau.de>
 *
 * @ingroup ServicesCOPage
 */
class ilpcCodeQuestionExporter extends ilPageComponentPluginExporter {
	public function init(): void {
	}

	/**
	 * Get head dependencies
	 *
	 * @param		string		entity
	 * @param		string		target release
	 * @param		array		ids
	 * @return		array		array of array with keys "component", entity", "ids"
	 */
	function getXmlExportHeadDependencies(string $a_entity, string $a_target_release, array $a_ids): array {
		// collect the files to export
		$file_ids = array();
		foreach ($a_ids as $id) {
			$properties = self::getPCProperties($id);
			if (isset($properties['page_file'])) {
				$file_ids[] = $properties['page_file'];
			}
		}

		// add the files as dependencies
		if (!empty(($file_ids))) {
			return array(
				array(
					"component" => "Modules/File",
					"entity" => "file",
					"ids" => $file_ids
				)
			);
		}

		return array();
	}


	/**
	 * Get xml representation
	 *
	 * @param	string		entity
	 * @param	string		schema version
	 * @param	string		id
	 * @return	string		xml string
	 */
	public function getXmlRepresentation(string $a_entity, string $a_schema_version, string $a_id): string {
		global $DIC;
		$component_factory = $DIC["component.factory"];
		$xml = '';

		foreach ($component_factory->getActivePluginsInSlot("pgcp") as $plugin) {
			if ($plugin->getPluginName() == 'pcCodeQuestion') {
				$prop = self::getPCProperties($a_id);
				$id = $prop['id'] + 0;
				$data = $plugin->loadDataForID($id);

				$xml = '<item>' . base64_encode($data['data']) . '</item>';
			}
		}

		//no plugin found, so we write at least the passed properties		
		if ($xml == '') {
			$data = self::getPCProperties($a_id);
			$xml = '<item>' . base64_encode($data['data']) . '</item>';
		}
		return $xml;
	}

	/**
	 * Get tail dependencies
	 *
	 * @param		string		entity
	 * @param		string		target release
	 * @param		array		ids
	 * @return		array		array of array with keys "component", entity", "ids"
	 */
	function getXmlExportTailDependencies(string $a_entity, string $a_target_release, array $a_ids): array {
		return array();
	}

	/**
	 * Returns schema versions that the component can export to.
	 * ILIAS chooses the first one, that has min/max constraints which
	 * fit to the target release. Please put the newest on top. Example:
	 *
	 * 		return array (
	 *		"4.1.0" => array(
	 *			"namespace" => "http://www.ilias.de/Services/MetaData/md/4_1",
	 *			"xsd_file" => "ilias_md_4_1.xsd",
	 *			"min" => "4.1.0",
	 *			"max" => "")
	 *		);
	 *
	 *
	 * @return		array
	 */
	public function getValidSchemaVersions(string $a_entity): array {
		return array(
			'5.3.0' => array(
				'namespace' => 'http://www.ilias.de/',
				//'xsd_file'     => 'pctpc_5_3.xsd',
				'uses_dataset' => false,
				'min' => '5.3.0',
				'max' => '9.999.0'
			)
		);
	}
}