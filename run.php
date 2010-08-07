#!C:\Program Files (x86)\PHP\php.exe
<?php
error_reporting(E_ALL);
require_once('libs/TumblrBackup.php');
require_once('libs/idiorm/idiorm.php');

$fields = array(
    'id' => 'INTEGER PRIMARY KEY',
    'url' => 'TEXT',
    'url_with_slug' => 'TEXT',
    'type' => 'TEXT',
    'date_gmt' => 'TEXT',
    'date' => 'TEXT',
    'bookmarklet' => 'INTEGER',
    'mobile' => 'INTEGER',
    'feed_item' => 'TEXT',
    'from_feed_id' => 'INTEGER',
    'unix_timestamp' => 'INTEGER',
    'format' => 'TEXT',
    'reblog_key' => 'TEXT',
    'slug' => 'TEXT',
    'regular_title' => 'TEXT',
    'regular_body' => 'TEXT',
    'tags' => 'TEXT',
);

$config = parse_ini_file('config.ini', true);

$TumblrBackup = new TumblrBackup(
                $config['tumblr_login']['email'],
                $config['tumblr_login']['password'],
                $config['tumblr_login']['site']
);
$TumblrBackup->debug = false;

//setup ORM
try {
    ORM::configure('sqlite:./dbs/db-' . time() . '.sqlite');
    $db = ORM::get_db();
    $SQL = 'CREATE TABLE IF NOT EXISTS posts (';
    $SQL_fields = array();
    foreach ($fields as $field => $data_type) {
        $SQL_fields[] = $field . ' ' . $datatype;
    }
    $SQL .= implode(',', $SQL_fields) . ');';
    $db->exec($SQL);
} catch (Exception $e) {
    fwrite(STDERR, $e . "\n");
}
$offset = 0;
$limit = 50;
$post_counter = 0;
while (true) {
    try {
        $response = $TumblrBackup->get_posts($offset, $limit);
        if(!is_object($response)) {
            continue;
        }
        foreach ($response->posts as $post) {
            $orm_post = ORM::for_table('posts')->create();
            foreach ($fields as $field => $data_type) {
                $tumblr_field = repUnd($field);
                if (isset($post->$tumblr_field)) {
                    $value = $post->$tumblr_field;
                    if('tags' == $field) {
                        $value = json_encode($value);
                    }
                    $orm_post->$field = $value;
                }
            }
            $orm_post->save();
            $post_counter++;
        }
        $offset = $offset + $limit;
        $response_total = 'posts-total';
        if(($offset - (int)$response->$response_total) >= $limit) {
            break;
        }
    } catch (Exception $e) {
        fwrite(STDERR, print_r($e,1) . "\n");
    }
}
fwrite(STDERR, "post count: $post_counter \n");
function repUnd($var) {
    return str_replace('_', '-', $var);
}