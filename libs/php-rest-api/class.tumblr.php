<?php
/*
  PHP REST API
  Jason Tan
  http://code.google.com/p/php-rest-api/
 */

require_once('class.restapi.php');

class Tumblr extends RestApi {

    protected $cache_ext = 'tumblr';
    protected $endpoint;

    function __construct($endpoint) {
        if (strpos($endpoint, '.') !== false)
            $this->endpoint = "http://$endpoint/api/";
        else
            $this->endpoint = "http://$endpoint.tumblr.com/api/";
    }

    /*
     * start - The post offset to start from. The default is 0.
     * num - The number of posts to return. The default is 20, and the maximum is 50.
     * type - The type of posts to return. If unspecified or empty, all types of posts are returned. Must be one of text, quote, photo, link, chat, video, or audio.
     * id - A specific post ID to return. Use instead of start, num, or type.
     * filter - Alternate filter to run on the text content. Allowed values:
      o text - Plain text only. No HTML.
      o none - No post-processing. Output exactly what the author entered. (Note: Some authors write in Markdown, which will not be converted to HTML when this option is used.)
     * tagged - Return posts with this tag in reverse-chronological order (newest first). Optionally specify chrono=1 to sort in chronological order (oldest first).
     * search - Search for posts with this query.
     */

    function read($params = array(), $authenticate = false) {
        $url = $this->endpoint . "read";
        if ($this->format == 'json')
            $url .= "/json";

        $method = 'get';
        if(true === $authenticate) {
            $method = 'post';
        }
        return $this->request($url, array($method => $params));
    }

    // overload objectify to strip out JSON callback
    function objectify($response) {
        if ($this->format == 'json') {
            $response = preg_replace('/^var [^\\{]*/', '', $response);
            $response = preg_replace('/;$/', '', $response);
        }
        return parent::objectify($response);
    }
}