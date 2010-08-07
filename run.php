#!C:\Program Files (x86)\PHP\php.exe -q
<?php
require_once('libs/tumblr-php/Tumblr.php');

$config = parse_ini_file('config.ini');

$Tumblr = new Tumblr();
$result = $Tumblr->init($email, $password);