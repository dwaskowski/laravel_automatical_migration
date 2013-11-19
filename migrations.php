<?php

/**
 * 
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
function searchMigrationDir($path,&$app,&$path_artisan,&$seeds_class) {
    if($handle = opendir($path)){
        while (false !== ($entry = readdir($handle))) {
            $full_path = realpath($path."/".$entry);
            if($entry!='.' && $entry!='..' && is_dir($full_path)){
                $this_path_artisan = $path_artisan."/".$entry;
                if($entry=='migrations'){
                    echo "run Artisan migrate -path=".$path_artisan."/migrations\n";
                    Artisan::call('migrate', array('--path' => $path_artisan.'/migrations'));                    
                }elseif($entry=='seeds'){
                    searchSeedsClass($full_path,$seeds_class);
                }else{
                    searchMigrationDir($full_path,$app,$this_path_artisan,$seeds_class);
                }                
            }
        }
    }
}
function searchSeedsClass($path,&$seeds_class) {
    if($handle = opendir($path)){
        while (false !== ($entry = readdir($handle))) {
            $full_path = realpath($path."/".$entry);
            if($entry!='.' && $entry!='..' && !is_dir($full_path) && preg_match('/Seeder/', $entry)){
                $seeds_class[] = preg_replace('/\.php/', '', $entry);
            }
        }        
    }
}
function startSeeds(&$seeds_class){
    foreach ($seeds_class as $seed) {
        echo "run Artisan db:seed -=class=".$seed."\n";
        Artisan::call('db:seed', array('--class' => $seed));
    }
}

echo "Start automatical migration\n";
echo "---------------------------\n";
$path_artisan = 'app';
$seeds_class = array();
searchMigrationDir($application_path,$app,$path_artisan,$seeds_class);
echo "Run Artisan modules:scan\n";
Artisan::call('modules:scan');
startSeeds($seeds_class);

/**
 * Distroy Laravel application
 */
$app->shutdown();
