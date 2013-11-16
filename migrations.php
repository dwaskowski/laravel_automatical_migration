<?php

/**
 * Automatical migration from modules
 *
 * @author Dmitrij Waśkowski <dima@waskowscy.pl>
 * 
 */

/**
 * Path to application
 */
$application_path = realpath(__DIR__.'/app');

/**
 * Create Laravel application
 */
require __DIR__.'/bootstrap/autoload.php';
$app = require_once __DIR__.'/bootstrap/start.php';
$app->boot();

/**
 * Function for automatical migrate
 */
echo "Start automatical migration\n";
echo "---------------------------\n";
function search_migration_dir($path,&$app){
    if($handle = opendir($path)){
        while (false !== ($entry = readdir($handle))) {
            $full_path = realpath($path."/".$entry);
            if($entry!='.' && $entry!='..' && is_dir($full_path)){
                if($entry=='migrations'){
                    echo "run migrations from: ".$full_path."\n";
                    $app['migrator']->run($full_path);
                }else{
                    search_migration_dir($full_path,$app);
                }                
            }
        }
    }
}
search_migration_dir($application_path,$app);

/**
 * Distroy Laravel application
 */
$app->shutdown();
