<?php
// test_direct_mail.php
$to = "vsivasaipavan333@gmail.com";
$subject = "Test Email from Campus Event Portal";
$message = "This is a test email to check if the default mail() function is working.";
$headers = "From: noreply@campusevents.com";

echo "Attempting to send email to $to...\n";

if (mail($to, $subject, $message, $headers)) {
    echo "SUCCESS: Email sent successfully using mail().\n";
} else {
    echo "FAILURE: Email could not be sent. This is common on XAMPP/Localhost.\n";
    echo "We should use SMTP with the credentials you provided.\n";
}
?>
