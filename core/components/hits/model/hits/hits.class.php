<?php
class Hits {
    public $modx;
    public $config = array();
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;
 
        $basePath = $this->modx->getOption('hits.core_path',$config,$this->modx->getOption('core_path').'components/hits/');
        $assetsUrl = $this->modx->getOption('hits.assets_url',$config,$this->modx->getOption('assets_url').'components/hits/');
        $this->config = array_merge(array(
            'basePath' => $basePath,
            'corePath' => $basePath,
            'modelPath' => $basePath.'model/',
            'processorsPath' => $basePath.'processors/',
            'templatesPath' => $basePath.'templates/',
            'chunksPath' => $basePath.'elements/chunks/',
            'jsUrl' => $assetsUrl.'js/',
            'cssUrl' => $assetsUrl.'css/',
            'assetsUrl' => $assetsUrl,
            'connectorUrl' => $assetsUrl.'connector.php',
        ),$config);
		$this->modx->addPackage('hits',$this->config['modelPath']);
    }
    
    public function getHit($hit_key) {
        $c = $modx->newQuery('Hit');
        $c->where(array(
        		'hit_key' => $hit_key
        ));
        $c->limit(1);
	
        $hit = $modx->getOne('Hit',$c);
        return $hit;
    }
    
    public function getHits($childIds, $sort, $dir, $limit, $offset) {
    	$c = $this->modx->newQuery('Hit');
    	$c->sortby($sort,$dir);
    	$c->where(array(
    		'hit_key:IN' => $childIds
    	));
    	if($limit) $c->limit($limit,$offset);

    	// render the results
    	$hits = $this->modx->getCollection('Hit',$c);
        return $hits;
    }

	public function getChunk($name,$properties = array()) {
	    $chunk = null;
	    if (!isset($this->chunks[$name])) {
	        $chunk = $this->_getTplChunk($name);
	        if (empty($chunk)) {
	            $chunk = $this->modx->getObject('modChunk',array('name' => $name));
	            if ($chunk == false) return false;
	        }
	        $this->chunks[$name] = $chunk->getContent();
	    } else {
	        $o = $this->chunks[$name];
	        $chunk = $this->modx->newObject('modChunk');
	        $chunk->setContent($o);
	    }
	    $chunk->setCacheable(false);
	    return $chunk->process($properties);
	}
    
    public function processHits($hits,$tpl) {
    	$hs = array();
    	foreach($hits as $hit) { 
    		$hs[] = $this->getChunk($tpl,$hit->toArray());	
    	}
        return $hs;
    }

	private function _getTplChunk($name,$postfix = '.chunk.tpl') {
	    $chunk = false;
	    $f = $this->config['chunksPath'].strtolower($name).$postfix;
	    if (file_exists($f)) {
	        $o = file_get_contents($f);
	        $chunk = $this->modx->newObject('modChunk');
	        $chunk->set('name',$name);
	        $chunk->setContent($o);
	    }
	    return $chunk;
	}

}