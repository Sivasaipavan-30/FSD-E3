<?php
// test_smtp.php
require_once 'api/mailer.php';

$to = "smartcmapusevents@gmail.com";
$subject = "SMTP Test: Smart Campus Events";
$body = "<h1>Connection Successful!</h1><p>This email confirms that your SMTP settings for <b>smartcmapusevents@gmail.com</b> are working correctly.</p>";

echo "Testing SMTP connection for " . SMTP_USER . "...\n";

$result = sendEmail($to, $subject, $body, true);

if ($result['success']) {
    echo "SUCCESS: " . $result['message'] . "\n";
} else {
    echo "FAILURE: " . $result['message'] . "\n";
    echo "\nDEBUG TIPS:\n";
    echo "1. Verify your password (or App Password).\n";
    echo "2. Ensure PHP 'openssl' extension is enabled in php.ini.\n";
    echo "3. Check if your firewall blocks port 587.\n";
}
?>
