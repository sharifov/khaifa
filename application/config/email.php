<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol'] = 'smtp';
$config['smtp_host'] = get_setting('mail_hostname');
$config['smtp_port'] = get_setting('mail_port');
$config['smtp_user'] = get_setting('mail_username');
$config['smtp_pass'] = get_setting('mail_password');
$config['smtp_crypto'] = 'tls';
$config['_smtp_auth'] = TRUE;
$config['newline'] = "\r\n";
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';