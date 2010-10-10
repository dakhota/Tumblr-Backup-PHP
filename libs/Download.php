<?php

class Download {
	public $path = 'images/';
	public function copy($url) {
		if(!is_dir($this->path)) {
			mkdir($this->path, 0777, true);
		}
		$file_name = basename($url);
		copy($url, $this->path . $file_name);
	}
}