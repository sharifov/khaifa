<?php defined('BASEPATH') or exit('No direct script access allowed');


$config['auth'] = [
/* If user don't have permisssion to see the page he will be redirected the page spesificed. */
'no_permission'                  => true,
/* Name of admin group */
'admin_group'                    => 'admin',
/* Name of default group, the new user is added in it */
'default_group'                  => 'default',
/* Name of Public group , people who not logged in */
'public_group'                   => 'public',
/* The table which contains users */
'users'                          => 'users',
/* The table which contains groups */
'groups'                         => 'groups',
/* */
'group_to_group'                 => 'group_to_group',
/* The table which contains join of users and groups */
'user_to_group'                  => 'user_to_group',
/* The table which contains permissions */
'perms'                          => 'permissions',
/* The table which contains permissions for groups */
'perm_to_group'                  => 'permission_to_group',
/* The table which contains permissions for users */
'perm_to_user'                   => 'permission_to_user',
/* The table which contains private messages */
'pms'                            => 'messages',
/* The table which contains users variables */
'user_variables'                 => 'user_variables',
/* The table which contains login attempts */
'login_attempts'                 => 'login_attempts',
/*
* Remember time (in relative format) elapsed after connecting and automatic LogOut for usage with Cookies
* Relative Format (e.g. '+ 1 week', '+ 1 month', '+ first day of next month')
* for details see http://php.net/manual/de/datetime.formats.relative.php
*/
'remember'                       => ' +3 days',
/* Maximum char long for Password */
'max'                            => 20,
/* Minimum char long for Password */
'min'                            => 5, //
/* Additional valid chars for username. Non alphanumeric characters that are allowed by default */
'additional_valid_chars'         => [],
/* Enables the DDoS Protection, user will be banned temporary when he exceed the login 'try' */
'ddos_protection'                => true,
/* Enables reCAPTCHA (for details see www.google.com/recaptcha/admin) */
'recaptcha_active'               => false,
/* Login Attempts to display reCAPTCHA */
'recaptcha_login_attempts'       => 4,
/* The reCAPTCHA siteKey */
'recaptcha_siteKey'              => '',
/* The reCAPTCHA secretKey */
'recaptcha_secret'               => '',
/* Enables the Time-based One-time Password Algorithm */
'totp_active'                    => false,
/* TOTP only on IP Change */
'totp_only_on_ip_change'         => false,
/* TOTP reset over reset Password */
'totp_reset_over_reset_password' => false,
/* Enables TOTP two step login */
'totp_two_step_login_active'     => false,
/* Redirect path to TOTP Verification page used by control() & is_allowed() */
'totp_two_step_login_redirect'   => '/account/twofactor_verification/',
/* Login attempts time interval (default 10 times in one hour) */
'max_login_attempt'              => 10,
/* Period of time for max login attempts (default "5 minutes") */
'max_login_attempt_time_period'  => "5 minutes",
/* Enables removing login attempt after successful login */
'remove_successful_attempts'     => true,
/* Login Identificator, if TRUE username needed to login else email address. */
'login_with_name'                => false,
/* Sender email address, used for remind_password, send_verification and reset_password */
'email'                          => get_setting('mail_username'),
/* Sender name, used for remind_password, send_verification and reset_password */
'name'                           => 'Mimelon',
/* Array of Config for CI's Email Library */
'email_config'                   => false,
/* User Verification, if TRUE sends a verification email on account creation. */
'verification'                   => false,
/* Link for verification without site_url or base_url */
'verification_link'              => 'account/verification/',
/* Link for reset_password without site_url or base_url */
'reset_password_link'            => 'account/reset_password/',

'reset_seller_link'            => 'become_seller/reset_password/',
/*
* Name of selected hashing algorithm (e.g. "md5", "sha256", "haval160,4", etc..)
* Please, run hash_algos() for know your all supported algorithms
*/
'hash'                           => 'sha256',
/* Enables to use PHP's own password_hash() function with BCrypt, needs PHP5.5 or higher */
'use_password_hash'              => true,
/*
* password_hash algorithm (PASSWORD_DEFAULT, PASSWORD_BCRYPT)
* for details see http://php.net/manual/de/password.constants.php
*/
'password_hash_algo'             => PASSWORD_BCRYPT,
/*
* password_hash options array
* for details see http://php.net/manual/en/function.password-hash.php
*/
'password_hash_options'          => [],
/*
* Enables PM Encryption, needs configured CI Encryption Class.
* for details see: http://www.codeigniter.com/userguide2/libraries/encryption.html
*/
'pm_encryption'                  => false,
/*
* PM Cleanup max age (in relative format), PM's are older than max age get deleted with 'cleanup_pms()'
* Relative Format (e.g. '2 week', '1 month')
* for details see http://php.net/manual/de/datetime.formats.relative.php
*/
'pm_cleanup_max_age'             => "3 months",
];
