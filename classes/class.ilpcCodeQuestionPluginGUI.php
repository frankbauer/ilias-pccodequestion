<?php
include_once("./Services/COPage/classes/class.ilPageComponentPluginGUI.php");
/**
 * Code Question Page user interface plugin
 *
 * 
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 * @ilCtrl_isCalledBy ilpcCodeQuestionPluginGUI: ilPCPluggedGUI
 */
class ilpcCodeQuestionPluginGUI extends ilPageComponentPluginGUI
{
    /**
	 * @const	string	URL base path for including special javascript and css files
	 */
	//const URL_PATH = "./Customizing/global/plugins/Services/COPage/PageComponent/pcCodeQuestion";
	const URL_PATH = "./Customizing/global/plugins/Modules/TestQuestionPool/Questions/assCodeQuestion";
    
   /** @var  ilLanguage $lng */
	protected $lng;

	/** @var  ilCtrl $ctrl */
	protected $ctrl;

	/** @var  ilTemplate $tpl */
	protected $tpl;

	/** @var ilpcCodeQuestionPlugin */
	protected $plugin;

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
	public function __construct()
	{
		parent::__construct();
		
		include_once "./Services/Component/classes/class.ilPlugin.php";
		$this->plugin = ilPlugin::getPluginObject(IL_COMP_MODULE, "TestQuestionPool", "qst", "assCodeQuestion");
		$this->plugin->includeClass("ui/codeBlockUI.php");
		$this->plugin->includeClass("class.assCodeQuestion.php");
				
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
		if ('-copg_pgcp_codeqstpage_used_lang-' == $this->lang_user) $this->lang_user = 'en';		
	}

	/**
	 * Execute command
	 *
	 * @param
	 * @return
	 */
	function executeCommand()
	{
		global $ilCtrl;
 
		$next_class = $ilCtrl->getNextClass();
 
		switch($next_class)
		{
			default:
				// perform valid commands
				$cmd = $ilCtrl->getCmd();
				if (in_array($cmd, array("create", "save", "edit", "update", "cancel")))
				{
					$this->$cmd();
				}
				break;
		}
	}

	private function getLanguage() {
		return $this->object->blocks()->getLanguage();
	}

	private function prepareTemplate()
	{
		$this->object->blocks()->ui()->prepareTemplate($this->tpl, self::URL_PATH);			
	}
 
 
	/**
	 * Form for new elements
	 */
	function insert()
	{
		global $tpl;

		$this->setTabs("insert", true);

		$object = new assCodeQuestion();		
		$form = $this->initForm($object, true);        
		$tpl->setContent($form->getHTML());
	}
 
	/**
	 * Save new pc example element
	 */
	public function create()
	{
		$this->setTabs("insert", true);
		$this->store(true);
	}
 
	/**
	 * Edit
	 *
	 * @param
	 * @return
	 */
	function edit()
	{
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
	function update()
	{
		$this->setTabs("edit");
		$this->store(false);	
	}
 
	/**
	 * Cancel
	 */
	function cancel()
	{
		$this->returnToParent();
	}

	/**
	 * Creat new entry in our databse
	 */
	private function createData($object){
		/** @var $ilDB \ilDBInterface  */
        global $ilDB;
        
        $query = "INSERT INTO `copg_pgcp_codeqstpage` (`data`) VALUES (%s);";        
		$result = $ilDB->manipulateF($query, array('text'), array($object->blocks->getJSONEncodedAdditionalData()));
		$id = $ilDB->getLastInsertId();
		$object->setID($id);
        
        return $id;
	}

	/**
	 * Creat new entry in our databse
	 */
	private function updateData($object){
		/** @var $ilDB \ilDBInterface  */
        global $ilDB;
        
        $query = "UPDATE `copg_pgcp_codeqstpage` SET `data` = %s WHERE `code_id` = %s";        
		$result = $ilDB->manipulateF($query, array('text', 'integer'), array($object->blocks->getJSONEncodedAdditionalData(), $object->getID()));
		
        return $object;
	}

	/**
	 * Creat new entry in our databse
	 */
	private function loadData($object, $prop=NULL){
		if ($prop==NULL) {
			$prop = $this->getProperties();
		}
		$id = $prop['id']+0;

		/** @var $ilDB \ilDBInterface  */
        global $ilDB;
        
        $query = "SELECT `data` FROM `copg_pgcp_codeqstpage` WHERE `code_id` = %s";        
		$result = $ilDB->queryF($query, array('integer'), array($id));

		$return = ['data'=>''];
		while ($row = $ilDB->fetchAssoc($result))
        {
            $return['data'] = $row['data'];            
		}
		
		$object->loadDataToBlocks($return, $id);			
        return $object;
	}


	/**
	 * Store changes to Props
	 * 
	 * @param        bool        $a_create        true => create new item, false => update existing item
	 */
	private function store($a_create = true){
		global $tpl, $lng, $ilCtrl, $_POST;
		$object = new assCodeQuestion();				 

		$id = 0;
		if (!$a_create) $id = $this->getProperties()['id']+0;
		$object->setID($id);		
		$object->createBlocksFromPost($_POST, $id);
		
		if ($a_create){
			$this->createData($object);
			$properties = array(
				'id' => $object->getID()
			);
		} else {
			$this->updateData($object);			
		}

		$form = $this->initForm($object, $a_create);

		$form->setValuesByPost();
		$errors = !$form->checkInput();
		$form->setValuesByPost();
		
		if (!$errors) {
			$res = false;
			if ($a_create){
				$res = $this->createElement($properties);
			} else {
				//$res = $this->updateElement($properties);
			}
			if ($res)
			{
				ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
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
	public function initForm($object, $a_create = false)
	{
		global $lng, $ilCtrl;
 
		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$object->blocks()->ui()->prepareTemplate($this->tpl, self::URL_PATH);

		$form = new ilPropertyFormGUI();
        $form->setFormAction($this->ctrl->getFormAction($this));
		$form->setTitle($this->lng->txt("cont_ed_insert_pcqst"));
		
		if (!$a_create)
		{			
			$this->loadData($object);			
		}

		$item = new ilCustomInputGUI('');
		$item->setPostVar('codeblock');	
		$item->setHTML($object->blocks()->ui()->render(true));
		$form->addItem($item);
 
		// save and cancel commands
		if ($a_create)
		{
			$form->addCommandButton("create", $this->lng->txt("save"));
			$form->addCommandButton("cancel", $this->lng->txt("cancel"));			
			$form->setTitle($this->getPlugin()->txt("cmd_insert"));
		}
		else
		{
			$form->addCommandButton("update", $lng->txt("save"));
			$form->addCommandButton("cancel", $lng->txt("cancel"));
			$form->setTitle($this->getPlugin()->txt("edit_ex_el"));
		}

		
 
		return $form;
	}

	private function render($object, $forceAddJSAndCSS=false){					
		$language = $object->blocks()->getLanguage();
		
		$template = $this->plugin->getTemplate("tpl.copg_pgcp_codeqstpage_output.html");	
		$object->blocks()->ui()->prepareTemplate($this->tpl, self::URL_PATH);				
		
		$html = $object->blocks()->ui()->render($false, false, false, NULL, NULL);

		$template->setVariable("UUID", $object->blocks()->ui()->getUUID());
		$template->setVariable("QUESTIONTEXT", "");
		$template->setVariable("BLOCK_HTML", $html);		
		$template->setVariable("LANGUAGE", $language);
		
		$template->setVariable("QUESTION_ID", $object->getId());
		$template->setVariable("LABEL_VALUE1", $object->getPlugin()->txt('label_value1'));

		$template->setVariable("MOUNTY", $object->blocks()->ui()->mountyJSCode(self::URL_PATH, !$forceAddJSAndCSS));		
		return $template->get();	
	}

	private function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}

		return (substr($haystack, -$length) === $needle);
	}

	
 
	/**
	 * Get HTML for element
	 *
	 * @param string $a_mode (edit, presentation, preview, offline)s
	 * @return string $html
	 */
	function getElementHTML($a_mode, array $a_properties, $a_plugin_version)
	{		
		$object = new assCodeQuestion();		
		$this->loadData($object, $a_properties);
		
		return $this->render($object, $a_mode=='presentation');
	}
 
	/**
	 * Set tabs
	 *
	 * @param
	 * @return
	 */
	function setTabs($a_active, $a_create=false)
	{
		global $ilTabs, $ilCtrl;
 
		$pl = $this->getPlugin();
 
		if ($a_create){
			$ilTabs->addTab("insert", $pl->txt("add_tab"),
				$ilCtrl->getLinkTarget($this, "insert"));	
		} else {
			$ilTabs->addTab("edit", $pl->txt("edit_tab"),
				$ilCtrl->getLinkTarget($this, "edit"));		
		}
 
		$ilTabs->activateTab($a_active);
	}
 
 
}

?>