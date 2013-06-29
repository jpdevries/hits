<?php
require_once dirname(__FILE__) . '/model/hits/hits.class.php';
abstract class HitsManagerController extends modExtraManagerController {
    /** @var Hits $hits */
    public $hits;
    public function initialize() {
		
        $this->hits = new Hits($this->modx);
 
        //$this->addCss($this->hits->config['cssUrl'].'mgr.css');
        //$this->addJavascript($this->hits->config['jsUrl'].'mgr/hits.js');
        /*$this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            Hits.config = '.$this->modx->toJSON($this->hits->config).';
        });
        </script>');*/
        return parent::initialize();
    }
    public function getLanguageTopics() {
        return array('hits:default');
    }
    public function checkPermissions() { return true;}
}
class IndexManagerController extends HitsManagerController {
    public static function getDefaultController() { return 'home'; }
}