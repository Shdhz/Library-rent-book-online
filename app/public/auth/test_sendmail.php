<?php
require '../include/mail.php';

$email = 'recipient@gmail.com';
$subject = 'Test Email';
$message = '<p>This is a test email from PHPMailer using Gmail SMTP.</p>';

if (sendMail($email, $subject, $message)) {
    echo 'Test email sent successfully!';
} else {
    echo 'Failed to send test email.';
}
?>
