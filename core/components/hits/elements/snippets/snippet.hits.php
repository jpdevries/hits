<?php
/*          __             	// Hits for MODX Revolution
/\ \      __/\ \__          	    come to us for your dirty work
\ \ \___ /\_\ \ ,_\   ____  		created by:
 \ \  _ `\/\ \ \ \/  /',__\ 		JP DeVries @jpdevries
  \ \ \ \ \ \ \ \ \_/\__, `\		YJ Tso @sepiariver
   \ \_\ \_\ \_\ \__\/\____/		Jason Coward @drumshaman
    \/_/\/_/\/_/\/__/\/__*/ 
		/* tracks page hits per hit_key

		USAGE: (assumes a chunk named hitID contains "[[+hit_key]]")
		Get a comma seperated list of ids of the 10 most visited pages 10 levels down from the web context
		[[!Hits? &parents=`0` &depth=`10` &limit=`10` &outSeperator=`,` &chunk=`hitID`]]

		Get a comma seperated list of ids of the 4 least visited pages that are children of resource 2 and set results to a placeholder
		[[!Hits? &parents=`2` limit=`4` &dir=`ASC`  &outSeperator=`,` &chunk=`hitID` &toPlaceholder=`hits`]]

		Record a hit for resource 3
		[[!Hits? &punch=`3`]]

		Record 20 hit for resource 4
		[[!Hits? &punch=`4` &amount=`20`]]

		Remove 4 hit from resource 5
		[[!Hits? &punch=`5` &amount=`-4`]]

		
*/ 

// get the hit service
$defaultHitsCorePath = $modx->getOption('core_path').'components/hits/';
$hitsCorePath = $modx->getOption('hits.core_path',null,$defaultHitsCorePath);
$hitService = $modx->getService('hits','Hits',$hitsCorePath.'model/hits/',$scriptProperties);

if (!($hitService instanceof Hits)) return 'failed'; // you'll need another fool to do your dirty work

$s = '';

/*$m = $modx->getManager();
$created = $m->createObjectContainer('Hit');
return $created ? 'Table created.' : 'Table not created.';*/

/* setup default properties */
$punch = $modx->getOption('punch',$scriptProperties,null); 
$amount = $modx->getOption('amount',$scriptProperties,1);
$sort = $modx->getOption('sort',$scriptProperties,'hit_count');
$dir = $modx->getOption('dir',$scriptProperties,'DESC');
$parents = $modx->getOption('parents',$scriptProperties,null);
$tpl = $modx->getOption('tpl',$scriptProperties,'hitTpl'); 
$limit = $modx->getOption('limit',$scriptProperties,5);
$depth = $modx->getOption('depth',$scriptProperties,10);
$outputSeparator = $modx->getOption('outputSeparator',$scriptProperties,"\n");
$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,"");

if($parents === 0) $parents = array(0); // i know, i know
else if($parents) $parents = explode(',', $parents);


// don't just go throwing punches blindy, only store a page hit if told to do so
if($punch && $amount) {
	$hit = $modx->getObject('Hit',array(
		'hit_key' => $punch
	));

	if($hit) {
		// increment the amount
		$hit->set('hit_count',(integer)$hit->get('hit_count') + $amount); 
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
if(count($parents)) { // return results if requested (keyed off parents parameter)
	// create an array of child ids to compare hits
	
	$childIds = array();
	foreach($parents as $parent) {
		$childIds = array_merge($childIds,$modx->getChildIds($parent,$depth));
	} 

	$childIds = array_unique(array_filter($childIds));

	// who's got the most hits kids?
	$c = $modx->newQuery('Hit');
	$c->sortby($sort,$dir);
	$c->where(array(
		'hit_key:IN' => $childIds
	));
	if($limit) $c->limit($limit);

	// render the results
	$hits = $modx->getCollection('Hit',$c);
	foreach($hits as $hit) { 
		$s .= $hitService->getChunk($tpl,$hit->toArray()) . $outputSeparator;	
	}
}


if($toPlaceholder) { // would you like that for here or to go?
	$modx->setPlaceholder($toPlaceholder,$s);
	return;
}

return $s;