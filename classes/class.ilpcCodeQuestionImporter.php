<?php
/**
 * Importer class for the pcCodeQuestionPlugin Plugin
 *
 * @author Frank Bauer <frank.bauer@fau.de>
 *
 * @ingroup ServicesCOPage
 */
class ilpcCodeQuestionImporter extends ilPageComponentPluginImporter {
	public function init(): void {
	}



	/**
	 * Import xml representation
	 *
	 * @param	string			$a_entity
	 * @param	string			$a_id
	 * @param	string			$a_xml
	 * @param	ilImportMapping	$a_mapping
	 */
	public function importXmlRepresentation(string $a_entity, string $a_id, string $a_xml, ilImportMapping $a_mapping): void {
		global $DIC;
		$component_factory = $DIC["component.factory"];
		return;
		foreach ($component_factory->getActivePluginsInSlot("pgcp") as $plugin) {
			if ($plugin->getPluginName() == 'pcCodeQuestion') {
				$new_id = self::getPCMapping($a_id, $a_mapping);

				$properties = self::getPCProperties($new_id);
				$version = self::getPCVersion($new_id);


				// write the mapped file id to the properties
				if (isset($properties['page_file']) && ($old_file_id = $properties['page_file'])) {
					$new_file_id = $a_mapping->getMapping("Modules/File", 'file', $old_file_id);
					$properties['page_file'] = $new_file_id;
				}

				// save the data from the imported xml and write its id to the properties
				if (isset($properties['id']) && $additional_data_id = $properties['id']) {
					$data = base64_decode(substr($a_xml, 6, -7));
					$id = $plugin->storeData($data);
					$properties['id'] = $id;
				}
				
				self::setPCProperties($new_id, $properties);
				self::setPCVersion($new_id, $version);

			}
		}
	}
}