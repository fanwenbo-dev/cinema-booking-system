<?php
session_start();
if (!isset($_SESSION['booking'])) {
    header("Location: booking.php");
    exit();
}

if (isset($_POST['card_number'], $_POST['expiry_date'], $_POST['cvv'])) {
    $card_number = $_POST['card_number'];
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];
    $currentYear = date('Y');
    $currentMonth = date('m');
    list($expYear, $expMonth) = explode('-', $expiry_date);
    if ($expYear < $currentYear || ($expYear == $currentYear && $expMonth < $currentMonth)) {
        die("Error: The card has expired. Please use a valid card.");
    }

    if (preg_match('/^\d{16}$/', $card_number) && preg_match('/^\d{3}$/', $cvv)) {
        $_SESSION['payment_step'] = 'otp';
        sleep(rand(5, 15)); // simulate payment processing
        header("Location: otp.php");
        exit();
    }
}

header("Location: payment.php");    // redirect back if fail verify
exit();
?>
