<?php
class Log {
	public function __construct() {
	
	}

	public static function error($error) {
		$this->log(STDERR, $err);
	}
	
	public static function log($str, $method = STDOUT) {
		$str.= "\n";
		fwrite($method, $str);
	}
}