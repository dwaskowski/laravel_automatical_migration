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
$application_path = $application_path_seeds = realpath(__DIR__.'/app');

/**
 * Create Laravel application
 */
require __DIR__.'/bootstrap/autoload.php';
$app = require_once __DIR__.'/bootstrap/start.php';
$app->boot();

$command_m = !empty($argv[1]) ? $argv[1] : '';
$path_artisan = 'app';
echo "Start automatical migration\n";
echo "---------------------------\n";

/**
 * Function for automatical migrate
 */
if(empty($command_m) || $command_m=='all'){
    function searchMigrationDir($path,&$path_artisan) {
        if($handle = opendir($path)){
            while (false !== ($entry = readdir($handle))) {
                $full_path = realpath($path."/".$entry);
                if($entry!='.' && $entry!='..' && is_dir($full_path)){
                    $this_path_artisan = $path_artisan."/".$entry;
                    if($entry=='migrations'){
                        echo "run Artisan migrate -path=".$path_artisan."/migrations\n";
                        Artisan::call('migrate', array('--path' => $path_artisan.'/migrations'));                    
                    }else{
                        searchMigrationDir($full_path,$this_path_artisan);
                    }                
                }
            }
        }
    }
    searchMigrationDir($application_path,$path_artisan);
}

/**
 * Function for automatical seeds
 */
if(!empty($command_m) && ($command_m=='all' || $command_m=='seeds')){
    function searchSeedsDir($path) {
        if($handle = opendir($path)){
            while (false !== ($entry = readdir($handle))) {
                $full_path = realpath($path."/".$entry);
                if($entry!='.' && $entry!='..' && is_dir($full_path)){
                    if($entry=='seeds'){
                        if($handle = opendir($full_path)){
                            while (false !== ($entry = readdir($handle))) {
                                $seed_path = realpath($path."/".$entry);
                                if($entry!='.' && $entry!='..' && !is_dir($seed_path) && preg_match('/Seeder/', $entry)){
                                    $seed = preg_replace('/\.php/', '', $entry);
                                    echo "run Artisan db:seed -=class=".$seed."\n";
                                    Artisan::call('db:seed', array('--class' => $seed));
                                }
                            }        
                        }
                    }else{
                        searchSeedsDir($full_path);
                    }                
                }
            }
        }
    }
    echo "Run Artisan modules:scan\n";
    Artisan::call('modules:scan');
    searchSeedsDir($application_path_seeds);
}

/**
 * Function for refresh application
 */
if(!empty($command_m) && $command_m=='a'){
    echo "Run Artisan modules:scan\n";
    Artisan::call('modules:scan');
    echo "Run Artisan dump-autoload\n";
    Artisan::call('dump-autoload');
}

/**
 * Distroy Laravel application
 */
$app->shutdown();
