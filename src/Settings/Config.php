<?php
namespace Xtra\Settings;

class Config
{
	// Mysql db
	const MYSQL_HOST = 'localhost';
	const MYSQL_USER = 'root';
	const MYSQL_PASS = 'toor';
	const MYSQL_PORT = 3306;
	const MYSQL_DBNAME = 'dbname';

	// Smtp settings
	const SMTP_USER = '';
	const SMTP_PASS = '';
	const SMTP_HOST = '127.0.0.1';
	const SMTP_PORT = 25; // 25, 587, 465
	const SMTP_FROM_EMAIL = 'email@domain.here';
	const SMTP_FROM_USER = 'User Name';
	const SMTP_TLS = false;
	const SMTP_AUTH = false;
	const SMTP_DEBUG = 0;
}
?>
