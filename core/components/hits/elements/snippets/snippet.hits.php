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

		Get the four most hit resources, discluding the first
		[[!Hits? &parents=`0` &limit=`4` &offset=`1` &outputSeparator=`,`]]

		Knockout resource 3 then add 2 hits (knockout zeros value before adding punches)
		[[!Hits? &punch=`3` &amount=`2` &knockout=`1`]]
		
*/ 

// get the hit service
$defaultHitsCorePath = $modx->getOption('core_path').'components/hits/';
$hitsCorePath = $modx->getOption('hits.core_path',null,$defaultHitsCorePath);
$hitService = $modx->getService('hits','Hits',$hitsCorePath.'model/hits/',$scriptProperties);

if (!($hitService instanceof Hits)) return 'failed'; // you'll need another fool to do your dirty work

$s = '';

/* setup default properties */
$punch = $modx->getOption('punch',$scriptProperties,null); 
(integer)$amount = $modx->getOption('amount',$scriptProperties,1);
$sort = $modx->getOption('sort',$scriptProperties,'hit_count');
$dir = $modx->getOption('dir',$scriptProperties,'DESC');
$parents = $modx->getOption('parents',$scriptProperties,null);
$hit_keys = explode(',',$modx->getOption('hit_keys',$scriptProperties,null));
$tpl = $modx->getOption('tpl',$scriptProperties,'hitTpl'); 
$limit = $modx->getOption('limit',$scriptProperties,5);
(integer)$depth = $modx->getOption('depth',$scriptProperties,10);
$outputSeparator = $modx->getOption('outputSeparator',$scriptProperties,"\n");
$toPlaceholder = $modx->getOption('toPlaceholder',$scriptProperties,"");
$offset = isset($offset) ? (integer) $offset : 0;
$knockout = (bool)$modx->getOption('knockout',$scriptProperties,false);

if(trim($parents) == '0') $parents = array(0); // i know, i know
else if($parents) $parents = explode(',', $parents);

if($depth < 1) $depth = 1;

// don't just go throwing punches blindy, only store a page hit if told to do so
if($punch && $amount) {
	$hit = $modx->getObject('Hit',array(
		'hit_key' => $punch
	));

	if($hit) {
		// increment the amount
		$hit->set('hit_count',($knockout ? 0 : (integer)$hit->get('hit_count')) + $amount); 
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
// create an array of child ids to compare hits
    
$hits = array();
$childIds = array();
if(count($parents)) {
	foreach($parents as $parent) {
		$childIds = array_merge($childIds,$modx->getChildIds($parent,$depth));
	} 
    $childIds = array_unique($childIds);
    $hits = $hitService->getHits($childIds, $sort, $dir, $limit, $offset);
} 

if(!is_null($hit_keys)) {
    $hit_keys = array_diff($hit_keys,$childIds);
    $hits = array_merge($hits,$hitService->getHits($hit_keys, $sort, $dir, $limit, $offset));
}

$hs = $hitService->processHits($hits,$tpl);
$s = implode($outputSeparator, $hs);


if($toPlaceholder) { // would you like that for here or to go?
	$modx->setPlaceholder($toPlaceholder,$s);
	return;
}


return $s;