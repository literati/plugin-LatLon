<?php




use \Omeka_Record;


/**
 * This class ... 
 */
class LatLon extends Omeka_Plugin_Abstract {

/**
 * The hooks that you declare you are 
 * using in the $_hooks array 
 * must have a corresponding public 
 * method of the form hook{Hookname} as above. 
 */
    protected $_hooks = array(
        'install', 
        'initialize' 
//        'public_theme_header', 
//        'public_theme_body', 
//        'uninstall', 
//        'define_acl'
    );

    /**
     *
     * @var string[] the aset of filters we are implementing
     */
    //protected $_filters = array('admin_navigation_main');


    public function hookInitialize() {
        

    }

    /**
     * @param type $request 
     */
    public function hookPublicThemeHeader($request) {
        
    }

    /**
     * Insert something into the theme body
     * @param type $request 
     */
    public function hookPublicThemeBody($request) {

    }

    /**
     * Do things when the user clicks install, 
     * like build DB tables, etc
     * @throws Exception
     */
    function hookInstall() {
        
        debug("begin installation!");
        $sucess = $this->_createElements($this->_createElementSets());
        if(!$success){
            debug("installation failure!");
            
        }
        debug("installation success!");
    }
    
    
    /**
     * when the user deletes us from Omeka, cleanup after ourselves by DROPPING or db tables, etc
     */
    function hookUninstall() {
        
    }
    
    
    /**
     * 
     * 
     * 
     */
    function filterAdminNavigationMain() {
        
    }

    
    /**
     * 
     * This hook runs when Omeka's ACL is instantiated. 
     * It allows plugin writers to manipulate the ACL 
     * that controls user access in Omeka.
     * In general, plugins use this hook to restrict 
     * and/or allow access for specific user roles 
     * to the pages that it creates. 
     * @param Zend_Acl $acl The ACL object (a subclass of Zend_Acl)
     */
    function hookDefineAcl($acl) {
        
    }
    
        private function _createElementSets() {

        debug("begin createElementSets");
        $elSets = array("LatLon");
        
        $tbl = get_db()->getTable('ElementSet');
        
        $elSet = null;
        
        $alreadyCreated = 4;
        
        foreach ($elSets as $es) {
        
            if (($elSet = $tbl->findByName($es)) == null) {
                
                $elSet = insert_element_set($es);
                $elSet->record_type_id = 2;
                $elSet->description = "Provides fields for storing latitude and longitude info";
                $elSet->save();
                _log("browsing for element set id... " . $elSet->id);
                $alreadyCreated = 1;
                

            }
        }
        
        $message = $alreadyCreated == 4 ? " already exists in the DB!" : " created in the DB";
        debug(" Element set '" . $es . "', with ID ". $elSet->id . $message, $alreadyCreated);
        
        return $elSet->id;
        
    }

    private function _createElements($elSetId) {
        $elSet= get_db()->getTable('ElementSet')->find($elSetId);
        $elTbl = new ElementTable('Element', get_db());
        $elements = array(
            array(
                "name"              => 'Latitude',
                "description"       => 'Latitude',
                "record_type_id"    => 2,
                "data_type_id"      => 1,
                "element_set_id"    => $elSet->id
                ),
            array(
                "name"              => 'Longitude',
                "description"       => "Longitude",
                "record_type_id"    => 2,
                "data_type_id"      => 1,
                "element_set_id"    => $elSet->id
                )
            );
        foreach($elements as $element) {
            $el = null;
            if (($el = $elTbl->findByElementSetNameAndElementName(
                                    $elSet->name,$element['name']))==null){
                $el = new Element();
                _log("creating new element");
                $el->record_type_id = $element["record_type_id"];
                $el->data_type_id   = $element["data_type_id"];
                $el->name           = $element["name"];
                $el->description    = $element["description"];
                $el->element_set_id = $element["element_set_id"];
                $el->save();

               
            }else{
                debug(sprintf("trying to create element that already exists! %s",$element["name"]));
                return false;
            }
        }
        
        return "Elements saved";
    }
    
}

$latlon = new LatLon();
$latlon->setUp();