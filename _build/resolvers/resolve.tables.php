<?php
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('hits.core_path',null,$modx->getOption('core_path').'components/hits/').'model/';
            $modx->addPackage('hits',$modelPath);
 
            $manager = $modx->getManager();
 
            $manager->createObjectContainer('Hit');
 
            break;
        case xPDOTransport::ACTION_UPGRADE:
            $modx =& $object->xpdo;
            $modelPath = $modx->getOption('hits.core_path',null,$modx->getOption('core_path').'components/hits/').'model/';
            $modx->addPackage('hits',$modelPath);
 
            $manager = $modx->getManager();
            $manager->addIndex('Hit', 'hit_key_idx');
            break;
    }
}
return true;
