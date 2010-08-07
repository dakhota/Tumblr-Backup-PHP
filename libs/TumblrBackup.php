<?php
require_once('php-rest-api/class.tumblr.php');
/**
 *
 * @author Simon Holywell
 */
class TumblrBackup extends Tumblr {
    private $data = array();
    
    public function __construct($email='', $password='', $site='') {
        $this->initialise($email, $password, $site);
    }

    public function initialise($email, $password, $site) {
        $this->email = $email;
        $this->password = $password;
        $this->site = $site;
        parent::__construct($this->site);
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