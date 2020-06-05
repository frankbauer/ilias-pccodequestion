<?php
/**
 * Exporter class for the pcCodeQuestionPlugin Plugin
 *
 * @author Frank Bauer <frank.bauer@fau.de>
 *
 * @ingroup ServicesCOPage
 */
class ilpcCodeQuestionExporter extends ilPageComponentPluginExporter
{
	public function init()
	{
	}

	/**
	 * Get head dependencies
	 *
	 * @param		string		entity
	 * @param		string		target release
	 * @param		array		ids
	 * @return		array		array of array with keys "component", entity", "ids"
	 */
	function getXmlExportHeadDependencies($a_entity, $a_target_release, $a_ids)
	{
		// collect the files to export
		$file_ids = array();
		foreach ($a_ids as $id)
		{
			$properties = self::getPCProperties($id);
			if (isset($properties['page_file']))
			{
				$file_ids[] = $properties['page_file'];
			}
        }
        
		// add the files as dependencies
		if (!empty(($file_ids)))
		{
			return array(
				array(
					"component" => "Modules/File",
					"entity" => "file",
					"ids" => $file_ids)
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
	public function getXmlRepresentation($a_entity, $a_schema_version, $a_id)
	{
        if ($a_entity == "pgcp") {

            /** @var ilpcCodeQuestionPlugin $plugin */
            $plugin = ilPluginAdmin::getPluginObject(IL_COMP_SERVICE, 'COPage', 'pgcp', 'pcCodeQuestion');
            $prop = self::getPCProperties($a_id);
            $id = $prop['id']+0;
            $data = $plugin->loadDataForID($id);

            //Get XML
            $xml = '<item>'.base64_encode($data['data']).'</item>';
            return $xml;
        } else {
            return $this->ds->getXmlRepresentation($a_entity, $a_schema_version, $a_id, "", true, true);
        }
	}

	/**
	 * Get tail dependencies
	 *
	 * @param		string		entity
	 * @param		string		target release
	 * @param		array		ids
	 * @return		array		array of array with keys "component", entity", "ids"
	 */
	function getXmlExportTailDependencies($a_entity, $a_target_release, $a_ids)
	{
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
	public function getValidSchemaVersions($a_entity)
	{
		return array(
			'5.3.0' => array(
				'namespace'    => 'http://www.ilias.de/',
				//'xsd_file'     => 'pctpc_5_3.xsd',
				'uses_dataset' => false,
				'min'          => '5.3.0',
				'max'          => ''
			)
		);
	}
}