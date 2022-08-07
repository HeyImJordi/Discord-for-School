<?php
include 'main.php';
$msg = '';
if (isset($_POST['email'])) {
    $stmt = $con->prepare('SELECT activation_code FROM accounts WHERE email = ? AND activation_code != "" AND activation_code != "activated"');
    $stmt->bind_param('s', $_POST['email']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($activation_code);
        $stmt->fetch();
        $stmt->close();
        send_activation_email($_POST['email'], $activation_code);
        $msg = 'Activaton link has been sent to your email!';
    } else {
        $msg = 'We do not have an account with that email!';
    }
}
?>
<!DOCTYPE html>
<html>
	<head>
    <title>Resend Activation Email</title>
	</head>
	<body>
    <form action="resendactivation.php" method="post">
    <input type="email" name="email" placeholder="Your Email" id="email" required>
    <div class="msg"><?=$msg?></div>
    <input type="submit" value="Submit">
    </form>
    </body>
</html>