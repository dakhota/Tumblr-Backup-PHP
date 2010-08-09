<?php
require_once('php-rest-api/class.tumblr.php');
/**
 *
 * @author Simon Holywell
 */
class TumblrBackup extends Tumblr {
    /**
     * Default property store
     * @var array
     */
    private $data = array();
    
    /**
     * The database/response fields and their types
     * @var array
     */
    private $fields = array(
        'id'             => 'INTEGER PRIMARY KEY',
        'url'            => 'TEXT',
        'url_with_slug'  => 'TEXT',
        'type'           => 'TEXT',
        'date_gmt'       => 'TEXT',
        'date'           => 'TEXT',
        'bookmarklet'    => 'INTEGER',
        'mobile'         => 'INTEGER',
        'feed_item'      => 'TEXT',
        'from_feed_id'   => 'INTEGER',
        'unix_timestamp' => 'INTEGER',
        'format'         => 'TEXT',
        'reblog_key'     => 'TEXT',
        'slug'           => 'TEXT',
        'regular_title'  => 'TEXT',
        'regular_body'   => 'TEXT',
        'tags'           => 'TEXT',
        'raw'                => 'TEXT',
    );

    /**
     * Table name
     * @var string
     */
    private $table = 'posts';
    
    public function __construct($email='', $password='', $site='') {
        $this->initialise($email, $password, $site);
    }

    public function initialise($email, $password, $site) {
        $this->email = $email;
        $this->password = $password;
        $this->site = $site;
        parent::__construct($this->site);
        $this->setupTable();
    }

    private function setupTable() {
        $db = ORM::get_db();
        $SQL = 'CREATE TABLE IF NOT EXISTS ' . $this->table . ' (';
        $SQL_fields = array();
        foreach ($this->fields as $field => $data_type) {
            $SQL_fields[] = $field . ' ' . $datatype;
        }
        $SQL .= implode(',', $SQL_fields) . ');';
        $db->exec($SQL);
    }

    public function get_posts($offset = 0, $limit = 50) {
        $params = array(
            'start' => $offset,
            'num' => $limit,
            'email' => $this->email,
            'password' => $this->password,
        );
        return $this->read($params, true);
    }

    public function get_all_posts() {
        $posts = array();
        $offset = 0;
        $limit = 50;
        while (true) {
            $response = $this->get_posts($offset, $limit);
            if(!is_object($response)) {
                continue;
            }
            foreach ($response->posts as $post) {
                $posts[] = $post;
            }
            $offset = $offset + $limit;
            $response_total = 'posts-total';
            if(($offset - (int)$response->$response_total) >= $limit) {
                break;
            }
        }
        return $posts;
    }

    public function save_all_posts() {
        $posts = $this->get_all_posts();
        foreach($posts as $post_key => $post) {
            $orm_post = ORM::for_table($this->table)->create();
            foreach($this->fields as $field => $data_type) {
                $tumblr_field = repUnd($field);
                if(isset($post->$tumblr_field)) {
                    $value = $post->$tumblr_field;
                    if('tags' == $field) {
                        $value = json_encode($value);
                    }
                    $orm_post->$field = $value;
                }
            }
            $orm_post->raw = json_encode($post);
            $orm_post->save();
        }
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function __unset($name) {
        unset($this->data[$name]);
    }
}