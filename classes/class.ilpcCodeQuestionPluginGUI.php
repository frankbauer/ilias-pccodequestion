<?php
include_once("./Services/COPage/classes/class.ilPageComponentPluginGUI.php");

/**
 * Code Question Page user interface plugin
 *
 * 
 * @author Alex Killing <alex.killing@gmx.de>
 * @author Frank Bauer <frank.bauer@fau.de>
 * @version $Id$
 * @ilCtrl_isCalledBy ilpcCodeQuestionPluginGUI: ilPCPluggedGUI
 */
class ilpcCodeQuestionPluginGUI extends ilPageComponentPluginGUI {
	/**
	 * @const	string	URL base path for including special javascript and css files
	 */
	//const URL_PATH = "./Customizing/global/plugins/Services/COPage/PageComponent/pcCodeQuestion";
	const URL_PATH = "./Customizing/global/plugins/Modules/TestQuestionPool/Questions/assCodeQuestion";

	/** @var  ilLanguage $lng */
	protected ilLanguage $lng;

	/** @var  ilCtrl $ctrl */
	protected $ctrl;

	/** @var  ilTemplate $tpl */
	protected $tpl;

	/** @var ilassCodeQuestionPlugin */
	protected ilassCodeQuestionPlugin $code_plugin;

	/** @var ilAccessHandler */
	protected $access;

	/** @var ilTabsGUI */
	protected $tabs;

	/** @var ilToolbarGUI */
	protected $toolbar;

	/** @var ilObjUser  */
	protected $user;

	/**
	 * @var pcCodeQuestion	The question object
	 */
	var $object = NULL;

	var $lang_user = 'en';

	/**
	 * Constructor
	 *
	 * @param integer $id The database id of a question object
	 * @access public
	 */
	public function __construct() {
		parent::__construct();

		include_once "./Services/Component/classes/class.ilPlugin.php";
		$this->code_plugin = ilpcCodeQuestionPlugin::initPluginObject("assCodeQuestion");

		global $DIC;

		$this->lng = $DIC->language();
		$this->ctrl = $DIC->ctrl();
		$this->access = $DIC->access();
		$this->tabs = $DIC->tabs();
		$this->lng = $DIC->language();
		$this->user = $DIC->user();
		$this->toolbar = $DIC->toolbar();

		$this->tpl = $DIC['tpl'];

		$this->lng->loadLanguageModule('assessment');
		$this->lng->loadLanguageModule('cont');
		if ('-copg_pgcp_codeqstpage_used_lang-' == $this->lang_user)
			$this->lang_user = 'en';
	}

	/**
	 * Execute command
	 *
	 * @param
	 * @return
	 */
	function executeCommand(): void {
		global $ilCtrl;

		$next_class = $ilCtrl->getNextClass();

		switch ($next_class) {
			default:
				// perform valid commands
				$cmd = $ilCtrl->getCmd();
				if (in_array($cmd, array("create", "save", "edit", "update", "cancel"))) {
					$this->$cmd();
				}
				break;
		}
	}

	private function getLanguage() {
		return $this->object->blocks()->getLanguage();
	}

	private function prepareTemplate() {
		$this->object->blocks()->ui()->prepareTemplate($this->tpl, self::URL_PATH);
	}


	/**
	 * Form for new elements
	 */
	function insert(): void {
		global $tpl;

		$this->setTabs("insert", true);

		$object = new assCodeQuestion();
		$form = $this->initForm($object, true);
		$tpl->setContent($form->getHTML());
	}

	/**
	 * Save new pc example element
	 */
	public function create(): void {
		$this->setTabs("insert", true);
		$this->store(true);
	}

	/**
	 * Edit
	 *
	 * @param
	 * @return
	 */
	function edit(): void {
		global $tpl, $_GET;

		$this->setTabs("edit");

		$object = new assCodeQuestion();
		$form = $this->initForm($object, false);
		$tpl->setContent($form->getHTML());
	}

	/**
	 * Update
	 *
	 * @param
	 * @return
	 */
	function update(): void {
		$this->setTabs("edit");
		$this->store(false);
	}

	/**
	 * Cancel
	 */
	function cancel(): void {
		$this->returnToParent();
	}

	/**
	 * Creat new entry in our database
	 */
	private function createData($object) {
		$id = $this->plugin->storeData($object->blocks->getJSONEncodedAdditionalData());
		$object->setID($id);

		return $id;
	}

	/**
	 * Update entry in our database
	 */
	private function updateData($object) {
		$this->plugin->updateDataForID($object->blocks->getJSONEncodedAdditionalData(), $object->getID());
		return $object;
	}

	/**
	 * Load entry from our database
	 */
	private function loadData($object, $prop = NULL) {
		if ($prop == NULL) {
			$prop = $this->getProperties();
		}
		$id = $prop['id'] + 0;
		$data = '';
		$version = $prop['v'] + 0;
		if (isset($prop['data']) && isset($prop['is_base64']) && $prop['is_base64']) {
			$data = base64_decode($prop['data']);
		}

		if ($data != '' && $version >= 1) {
			$return = array('data' => $data);
		} else {
			$return = $this->plugin->loadDataForID($id);
		}
	  		
		$object->loadDataToBlocks($return, $id);
		$object->setID($id);

		return $object;
	}


	/**
	 * Store changes to Props
	 * 
	 * @param        bool        $a_create        true => create new item, false => update existing item
	 */
	private function store($a_create = true) {
		global $tpl, $lng, $ilCtrl, $_POST;
		$object = new assCodeQuestion();

		$id = 0;
		if (!$a_create)
			$id = $this->getProperties()['id'] + 0;
		$object->setID($id);
		$object->createBlocksFromPost($_POST, $id);

		if ($a_create) {
			$this->createData($object);
			$properties = array(
				'id' => $object->getID(),
				'data' => base64_encode($object->blocks->getJSONEncodedAdditionalData()),
				'is_base64' => true,
				'v' => ilpcCodeQuestionPlugin::DATA_VERSION
			);
		} else {
			$this->updateData($object);
			$properties = array(
				'id' => $object->getID(),
				'data' => base64_encode($object->blocks->getJSONEncodedAdditionalData()),
				'is_base64' => true,
				'v' => ilpcCodeQuestionPlugin::DATA_VERSION
			);
		}

		$form = $this->initForm($object, $a_create);

		$form->setValuesByPost();
		$errors = !$form->checkInput();
		$form->setValuesByPost();

		if (!$errors) {
			$res = false;
			if ($a_create) {
				$res = $this->createElement($properties);
			} else {
				$res = $this->updateElement($properties);
			}
			if ($res) {
				//$this->tpl->setOnScreenMessage('success', $lng->txt("msg_obj_modified"));
				$this->returnToParent();
			}
		}

		$tpl->setContent($form->getHtml());
	}

	/**
	 * Init editing form
	 *
	 * @param        int        $a_mode        Edit Mode
	 */
	public function initForm($object, $a_create = false) {
		global $lng, $ilCtrl;

		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$object->blocks()->ui()->prepareTemplate($this->tpl, self::URL_PATH);

		$form = new ilPropertyFormGUI();
		$form->setFormAction($this->ctrl->getFormAction($this));
		$form->setTitle($this->lng->txt("cont_ed_insert_pcqst"));

		if (!$a_create) {
			$this->loadData($object);
		}

		$item = new ilCustomInputGUI('');
		$item->setPostVar('codeblock');
		$item->setHTML($object->blocks()->ui()->render(true));
		$form->addItem($item);

		// save and cancel commands
		if ($a_create) {
			$form->addCommandButton("create", $this->lng->txt("save"));
			$form->addCommandButton("cancel", $this->lng->txt("cancel"));
			$form->setTitle($this->getPlugin()->txt("cmd_insert"));
		} else {
			$form->addCommandButton("update", $lng->txt("save"));
			$form->addCommandButton("cancel", $lng->txt("cancel"));
			$form->setTitle($this->getPlugin()->txt("edit_ex_el"));
		}



		return $form;
	}

	private function render($object, $forceAddJSAndCSS = false) {
		$language = $object->blocks()->getLanguage();

		$template = $this->plugin->getTemplate("tpl.copg_pgcp_codeqstpage_output.html");
		$object->blocks()->ui()->prepareTemplate($this->tpl, self::URL_PATH);

		$html = $object->blocks()->ui()->render(NULL, false, false, NULL, NULL);

		$template->setVariable("UUID", $object->blocks()->ui()->getUUID());
		$template->setVariable("QUESTIONTEXT", "");
		$template->setVariable("BLOCK_HTML", $html);
		$template->setVariable("LANGUAGE", $language);

		$template->setVariable("QUESTION_ID", $object->getId());
		$template->setVariable("LABEL_VALUE1", $object->getPlugin()->txt('label_value1'));

		$template->setVariable("MOUNTY", $object->blocks()->ui()->mountyJSCode(self::URL_PATH, !$forceAddJSAndCSS));
		return $template->get();
	}

	/**
	 * Get HTML for element
	 *
	 * @param string $a_mode (edit, presentation, preview, offline)s
	 * @return string $html
	 */
	function getElementHTML(string $a_mode, array $a_properties, string $plugin_version): string {
		$object = new assCodeQuestion();
		$this->loadData($object, $a_properties);
		return $this->render($object, $a_mode == 'presentation');
	}

	/**
	 * Set tabs
	 *
	 * @param
	 * @return
	 */
	function setTabs($a_active, $a_create = false) {
		global $ilTabs, $ilCtrl;

		$pl = $this->getPlugin();

		if ($a_create) {
			$ilTabs->addTab(
				"insert",
				$pl->txt("add_tab"),
				$ilCtrl->getLinkTarget($this, "insert")
			);
		} else {
			$ilTabs->addTab(
				"edit",
				$pl->txt("edit_tab"),
				$ilCtrl->getLinkTarget($this, "edit")
			);
		}

		$ilTabs->activateTab($a_active);
	}


}

?>