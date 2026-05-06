<?php
// api/mail_config.php
/**
 * SMTP Configuration for Gmail
 * ----------------------------
 * Host: smtp.gmail.com
 * Port: 587 (TLS) or 465 (SSL)
 */

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 465);
define('SMTP_USER', 'smartcmapusevents@gmail.com');
define('SMTP_PASS', 'ywlw lghw fdsu xwgp'); // UPDATE THIS: You need a new App Password for this account
define('SMTP_FROM', 'smartcmapusevents@gmail.com');
define('SMTP_FROM_NAME', 'Smart Campus Events');

/**
 * Note: If you have 2-Step Verification enabled on your Google Account, 
 * you MUST generate an App Password:
 * 1. Go to your Google Account (myaccount.google.com)
 * 2. Security > 2-Step Verification > App passwords
 * 3. Select 'Mail' and 'Other (Custom name)' as 'Campus Event Portal'
 * 4. Use the 16-character code generated there as SMTP_PASS.
 */
?>