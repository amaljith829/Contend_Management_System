<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/PHPMailer-master/src/Exception.php';
require 'vendor/PHPMailer-master/src/PHPMailer.php';
require 'vendor/PHPMailer-master/src/SMTP.php';

require "includes/autoLoader.php";

// Initialize variables
$message = '';
$sent = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message_text = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message_text)) {
        $message = '<div class="alert alert-danger">Please fill in all fields.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-danger">Please enter a valid email address.</div>';
    } else {
        $mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = 'tls';
    $mail->Port = SMTP_PORT;

    $mail->setFrom(SMTP_USER);
    $mail->addAddress(SMTP_USER);
    $mail->addReplyTo($email, $name);
    $mail->Subject = $subject;
    $mail->Body = $message_text;

    $mail->send();
    
    $sent = true;
} catch (Exception $e) {
    $message = '<div class="alert alert-danger">Message could not be sent. Mailer Error: ' . $mail->ErrorInfo . '</div>';
}
        $message = '<div class="alert alert-success">Thank you for your message! We\'ll get back to you soon.</div>';
        
        // Clear form data after successful submission
        $name = $email = $message_text = '';
    }
}
?>
<?php require "includes/header.php"; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="contact-container">
            <h1 class="text-center mb-4">Contact Us</h1>
            <p class="text-center text-muted mb-4">Have a question or want to get in touch? Send us a message below.</p>
            <?php if($sent) : ?>
                <?php echo $message; ?>
            <?php else : ?>
            <form method="post" class="contact-form">
                <div class="form-group">
                    <label for="name">Name *</label>
                    <input class="form-control" name="name" id="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input class="form-control" name="email" id="email" type="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="subject">Subject *</label>
                    <input class="form-control" name="subject" id="subject" value="<?php echo htmlspecialchars($subject ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea class="form-control" name="message" id="message" rows="5" required><?php echo htmlspecialchars($message_text ?? ''); ?></textarea>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php require "includes/footer.php"; ?>