<?php
function getSnippetContent($filename) {
    $o = file_get_contents($filename);
    $o = trim(str_replace(array('<?php','?>'),'',$o));
    return $o;
}
$snippets = array();
 
$snippets[1]= $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 1, // i dont even think this matters
    'name' => 'Hits',
    'description' => 'Displays a list of Hits.',
    'snippet' => getSnippetContent($sources['elements'].'snippets/snippet.hits.php'),
),'',true,true);
$properties = include $sources['data'].'properties/properties.hits.php';
$snippets[1]->setProperties($properties);
unset($properties);
 
return $snippets;