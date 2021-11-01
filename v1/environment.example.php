<?php
/**
 * © Rolling Array https://rollingarray.co.in/
 *
 *
 * @summary Sample environment file
 * @author code@rollingarray.co.in
 *
 * Created at     : 2021-04-21 10:07:13 
 * Last modified  : 2021-11-01 11:08:11
 */

$environment = [

	'hashKey' => [
		'SALT' => '', //key size 16,
		'METHOD' => 'AES-256-CBC',
		'ALGO' => 'sha256'
	],

	'JWT' => [
		'CLIENT_ID' => '', // client id
		'SERVER_ID' => '', // server id www.xyz.com
		'EXPIRE_IN_SECONDS' => '', // exiporation in seconds, 60480
	],
	
	'db' => [
		'host' => '', // host
		'username' => '', // database username
		'password' => '', // database password
		'database' => '', // database name
		'port' => '' // database post
	],

	'email' => [
		'smtp_host_ip' => '', // smtp host ip
		'port' => 587, // smtp port
		'smtp_username' => '', // smtp_username
		'smtp_password' => '', // smtp_password
		'support_email' => '', // support_email
		'pretty_email_name' => '', // pretty_email_name
		'app_name' => '', // application name
		'app_tag_line' => '', // applicaiton tag line
		'email_track' => 'http://localhost:8888/C2/api/v1/email/track/update/' // email tracker api to track email
	],
];
