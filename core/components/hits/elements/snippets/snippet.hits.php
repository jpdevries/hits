<?php
/*          __             	// Hits for MODX Revolution
/\ \      __/\ \__          	// come to us for your dirty work
\ \ \___ /\_\ \ ,_\   ____  		created by:
 \ \  _ `\/\ \ \ \/  /',__\ 		JP DeVries @jpdevries
  \ \ \ \ \ \ \ \ \_/\__, `\		YJ Tso @sepiariver
   \ \_\ \_\ \_\ \__\/\____/		Jason Coward @drumshaman
    \/_/\/_/\/_/\/__/\/__*/ 
		/* tracks page hits per hit_key

		USAGE: (assumes a chunk named hitID contains "[[+hit_key]]")
		Get a comma seperated list of ids of the 10 most visited pages 10 levels down from the web context
		[[!Hits? &parents=`0` &depth=`10` &limit=`10` &outSeperator=`,` &chunk=`hitID`]]

		Get a comma seperated list of ids of the 4 least visited pages that are children of resource 2
		[[!Hits? &parents=`2` limit=`4` &dir=`ASC`  &outSeperator=`,` &chunk=`hitID`]]

		Record a hit for resource 3
		[[!Hits? &punch=`3`]]

		Record 4 hit for resource 5
		[[!Hits? &punch=`5` &amount=`4`]]
*/ 

// get the service
$defaultHitsCorePath = $modx->getOption('core_path').'components/hits/';
$hitsCorePath = $modx->getOption('hits.core_path',null,$defaultHitsCorePath);
$hitService = $modx->getService('hits','Hits',$hitsCorePath.'model/hits/',$scriptProperties);

if (!($hitService instanceof Hits)) return 'failed'; // you'll need another fool to do your dirty work

$s = '';

/*$m = $modx->getManager();
$created = $m->createObjectContainer('Hit');
return $created ? 'Table created.' : 'Table not created.';*/

/* setup default properties */
$tpl = $modx->getOption('tpl',$scriptProperties,'rowTpl');
$punch = $modx->getOption('punch',$scriptProperties,null); 
$amount = $modx->getOption('amount',$scriptProperties,1);
$sort = $modx->getOption('sort',$scriptProperties,'hit_count');
$dir = $modx->getOption('dir',$scriptProperties,'DESC');
$parents = $modx->getOption('parents',$scriptProperties,$modx->resource->get('id') || '');
$chunk = $modx->getOption('chunk',$scriptProperties,'hitTpl');
$limit = $modx->getOption('limit',$scriptProperties,5);
$depth = $modx->getOption('depth',$scriptProperties,10);
$outputSeparator = $modx->getOption('outputSeparator',$scriptProperties,"\n");
$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,"");

$parents = explode(',', $parents);

// don't just go throwing punches blindy, only store a page hit if told to do so
if($punch && $amount) {
	$hit = $modx->getObject('Hit',array(
		'hit_key' => $punch
	));

	if($hit) {
		// increment the amount
		$hit->set('hit_count',$hit->get('hit_count') + $amount); 
		$hit->save();
	} else {
		// create a new hit record
		$hit = $modx->newObject('Hit');
		$hit->fromArray(array(
			'hit_key' => $punch,
			'hit_count' => $amount
		));
		$hit->save();	
		
	}
}

$s = '';
if(count($parents)) { // return results if requested (keyed off parents parameter

	// create an array of child ids to compare hits
	$childIds = array();
	foreach($parents as $parent) {
		$childIds = array_merge($childIds,$modx->getChildIds($parent,$depth));
	} 

	// who's got the most hits kids?
	$c = $modx->newQuery('Hit');
	$c->sortby($sort,$dir);
	$c->where(array(
		'hit_key:IN' => $childIds
	));

	$hits = $modx->getCollection('Hit',$c);
	foreach($hits as $hit) { 
		$s .= $hitService->getChunk($chunk,$hit->toArray()) . $outputSeparator;	
	}
}

if($toPlaceholder) {
	$modx->setPlaceholder($toPlaceholder,$s);
	return;
}

return $s;