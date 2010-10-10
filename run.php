#!C:\Program Files (x86)\PHP\php.exe
<?php
date_default_timezone_set('Europe/London');
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
require_once('libs/TumblrBackup.php');
require_once('libs/DisqusBackup.php');
require_once('libs/idiorm/idiorm.php');
require_once('libs/Log.php');

$config = parse_ini_file('config.ini', true);

// setup ORM
try {
	Log::log('Create SQLite backup database.');
    ORM::configure('sqlite:./dbs/db-' . date('d-m-Y H-i-s') . '.sqlite');
} catch (Exception $e) {
    Log::error(print_r($e, 1) . "\n");
    exit;
}

if(!empty($config['tumblr_login']['email'])) {
    // backup Tumblr
    try {
        $TumblrBackup = new TumblrBackup(
            $config['tumblr_login']['email'],
            $config['tumblr_login']['password'],
            $config['tumblr_login']['site']
        );
		Log::log('Begin saving Tumblr posts.');
        $TumblrBackup->save_all_posts();
		Log::log('Completed backing up Tumblr.');
    } catch (Exception $e) {
        Log::error(print_r($e, 1) . "\n");
        exit;
    }
}

if(!empty($config['disqus_login']['user_api_key'])) {
    // backup Disqus
    try {
        $DisqusBackup = new DisqusBackup(
            $config['disqus_login']['user_api_key'],
            $config['disqus_login']['short_name']
        );
		Log::log('Backing up Disqus.');
        $DisqusBackup->save_all_comments();
		Log::log('Finished backing up Disqus.');
    } catch (Exception $e) {
        Log::error(print_r($e, 1) . "\n");
        exit;
    }
}

function repUnd($var) {
    return str_replace('_', '-', $var);
}