<?php
session_start();
if (!isset($_SESSION['booking']) || !isset($_SESSION['payment_step']) || $_SESSION['payment_step'] !== 'otp') {
    header("Location: booking.php");
    exit();
}

if (isset($_POST['otp']) && preg_match('/^\d{5}$/', $_POST['otp'])) {
    unset($_SESSION['payment_step']);
    sleep(rand(5, 15)); // simulate payment processing
    header("Location: success.php");
    exit();
}

header("Location: otp.php");
exit();
?>
