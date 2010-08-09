#!C:\Program Files (x86)\PHP\php.exe
<?php
require_once('libs/TumblrBackup.php');
require_once('libs/DisqusBackup.php');
require_once('libs/idiorm/idiorm.php');

$config = parse_ini_file('config.ini', true);

// setup ORM
try {
    ORM::configure('sqlite:./dbs/db-' . date('d-m-Y H-i-s') . '.sqlite');
} catch (Exception $e) {
    fwrite(STDERR, print_r($e, 1) . "\n");
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
        $TumblrBackup->save_all_posts();
    } catch (Exception $e) {
        fwrite(STDERR, print_r($e, 1) . "\n");
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
        $DisqusBackup->save_all_comments();
    } catch (Exception $e) {
        fwrite(STDERR, print_r($e, 1) . "\n");
        exit;
    }
}

function repUnd($var) {
    return str_replace('_', '-', $var);
}