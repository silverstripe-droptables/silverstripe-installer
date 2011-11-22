<?php

global $project;
$project = 'mysite';

global $database;
$database = 'SS_coredev3';

require_once('conf/ConfigureFromEnv.php');

MySQLDatabase::set_connection_charset('utf8');

// set NZ locale
i18n::set_locale('en_NZ');
date_default_timezone_set('Pacific/Auckland');

// Allow override of database for testing purposes
// CAUTION: Only to be used for testing, as database is run in-memory
$db = @$_GET['db'];
global $databaseConfig;
if(Director::isDev() && $db) {
	if ($db == 'sqlite3') {
		$databaseConfig['key'] = '';
		$databaseConfig['memory'] = true;
		$databaseConfig['path'] = '../assets/.sqlitedb';
		$databaseConfig['type'] = 'SQLite3Database';
	}
	else if ($db=='posgresql') {
		$databaseConfig['type'] = 'PostgreSQLDatabase';
		$databaseConfig['server'] = SS_PGSQL_DATABASE_SERVER;
		$databaseConfig['username'] = SS_PGSQL_DATABASE_USERNAME;
		$databaseConfig['password'] = SS_PGSQL_DATABASE_PASSWORD;
	}
	else if ($db=='mssql') {
		$databaseConfig['type'] = 'MSSQLDatabase';
		if(defined('SS_MSSQL_DATABASE_SERVER')) $databaseConfig['server'] = SS_MSSQL_DATABASE_SERVER;
		if(defined('SS_MSSQL_DATABASE_USERNAME')) $databaseConfig['username'] = SS_MSSQL_DATABASE_USERNAME;
		if(defined('SS_MSSQL_DATABASE_PASSWORD')) $databaseConfig['password'] = SS_MSSQL_DATABASE_PASSWORD;
		if(defined('SS_MSSQL_DATABASE_SUFFIX')) $databaseConfig['database'] .= SS_MSSQL_DATABASE_SUFFIX;
	}
}

// This line set's the current theme. More themes can be
// downloaded from http://www.silverstripe.org/themes/
SSViewer::set_theme('dew');

// enable nested URLs for this site (e.g. page/sub-page/)
if(class_exists('SiteTree')) SiteTree::enable_nested_urls();

