<?php
require_once('libs/disqus/disqus/disqus.php');
/**
 *
 * @author Simon Holywell
 */
class DisqusBackup extends DisqusAPI {
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
        'id'                 => 'INTEGER PRIMARY KEY',
        'status'             => 'TEXT',
        'message'            => 'TEXT',
        'ip_address'         => 'TEXT',
        'id'                 => 'INTEGER',
        'parent_post'        => 'INTEGER',
        'anonymous_author'   => 'TEXT',
        'imported'           => 'INTEGER',
        'forum'              => 'TEXT',
        'thread'             => 'TEXT',
        'author'             => 'TEXT',
        'created_at'         => 'TEXT',
        'is_anonymous'       => 'INTEGER',
        'points'             => 'INTEGER',
        'has_been_moderated' => 'INTEGER',
        'raw'                => 'TEXT',
    );

    /**
     * Table name
     * @var string
     */
    private $table = 'comments';
    
    public function __construct($api_key='', $short_name='') {
        $this->initialise($api_key, $short_name);
    }

    public function initialise($api_key, $short_name) {
        $this->api_key = $api_key;
        $this->short_name = $short_name;
        parent::__construct($this->api_key, '');
        $this->setForumApiKey();
        $this->setupTable();
    }

    private function setupTable() {
        $db = ORM::get_db();
        $SQL = 'CREATE TABLE IF NOT EXISTS ' . $this->table . ' (';
        $SQL_fields = array();
        foreach ($this->fields as $field => $data_type) {
            $SQL_fields[] = $field . ' ' . $data_type;
        }
        $SQL .= implode(',', $SQL_fields) . ');';
        $db->exec($SQL);
    }

    private function setForumApiKey() {
        $forums = $this->get_forum_list();
        foreach ($forums as $forum) {
            if($this->short_name == $forum->shortname) {
                $this->forum_api_key = $this->get_forum_api_key($forum->id);
                $this->forum_id = $forum->id;
            }
        }
    }

    public function get_comments($offset=0, $limit=25) {
        $params = array(
            'start' => $offset,
            'limit' => $limit,
        );
        return $this->get_forum_posts($this->forum_id, $params);
    }

    public function get_all_comments() {
        $offset = 0;
        $limit = 50;
        $comments = array();
        while (true) {
            $response = $this->get_comments($offset, $limit);
            if(!is_array($response)) {
                continue;
            }
            foreach ($response as $comment) {
                $comments[] = $comment;
            }
            $offset = $offset + $limit;
            if(count($response) <= 0) {
                break;
            }
        }
        return $comments;
    }

    public function save_all_comments() {
        $comments = $this->get_all_comments();
        foreach($comments as $comment_key => $comment) {
            $orm_comment = ORM::for_table($this->table)->create();
            $orm_comment->id = $comment_key;
            foreach($this->fields as $field => $data_type) {
                if(isset($comment->$field)) {
                    $value = $comment->$field;
                    if('anonymous_author' == $field or
                       'forum' == $field or
                       'thread' == $field or
                       'author' == $field) {
                        $value = json_encode($value);
                    }
                    $orm_comment->$field = $value;
                }
            }
            $orm_comment->raw = json_encode($comment);
            $orm_comment->save();
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