<?php
// Redirect to ssl/tls
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    $location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $location);
    exit;
}

// Regenerate session id each time
session_regenerate_id(true);

// Session cookie $_SESSION
session_set_cookie_params(0, '/', '.'.$_SERVER["HTTP_HOST"], isset($_SERVER["HTTPS"]), true);

// Secure php session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies',1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_lifetime', 0);
ini_set('session.cookie_domain','.'.$_SERVER['HTTP_HOST']);

// Cookie $_COOKIE
// setcookie("SESSTOKEN", "ERRORTOKEN", time() + 15 * 60 * 60 , "/", $_SERVER['HTTP_HOST'], false, false);

