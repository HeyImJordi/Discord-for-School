<?php
include 'main.php';
// Output message
$msg = '';
// Verify the ID and email provided
if (isset($_SESSION['tfa_code'], $_SESSION['tfa_email'], $_SESSION['tfa_id'])) {
    // Prepare our SQL, preparing the SQL statement will prevent SQL injection.
    $stmt = $con->prepare('SELECT email, tfa_code, username, id, role FROM accounts WHERE id = ? AND email = ?');
    $stmt->bind_param('is', $_SESSION['tfa_id'], $_SESSION['tfa_email']);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $stmt->store_result();
    // If the account exists with the email & ID provided...
    if ($stmt->num_rows > 0) {
    	$stmt->bind_result($email, $tfa_code, $username, $id, $role);
    	$stmt->fetch();
    	$stmt->close();
        // Account exist
        if (isset($_POST['code'])) {
            // Code submitted via the form
            if ($_POST['code'] == $tfa_code) {
                // Code accepted, update the IP address
                $ip = $_SERVER['REMOTE_ADDR'];
                $stmt = $con->prepare('UPDATE accounts SET ip = ? WHERE id = ?');
                $stmt->bind_param('si', $ip, $id);
                $stmt->execute();
                $stmt->close();
                // Destroy tfa session variables
                unset($_SESSION['tfa_code']);
                unset($_SESSION['tfa_email']);
                unset($_SESSION['tfa_id']);
                // Authenticate the user
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $username;
                $_SESSION['id'] = $id;
                $_SESSION['role'] = $role;
                // Redirect to home page
                header('Location: home.php');
                exit;
            } else {
                $msg = 'Incorrect code provided!';
            }
        } else {
            // Send the access code email using the twofactor.html template
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
            $stmt = $con->prepare('UPDATE accounts SET tfa_code = ? WHERE id = ?');
            $stmt->bind_param('si', $code, $id);
            $stmt->execute();
            $stmt->close();
            $subject = 'Your Access Code';
        	$headers = 'From: ' . mail_from . "\r\n" . 'Reply-To: ' . mail_from . "\r\n" . 'Return-Path: ' . mail_from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
        	$email_template = str_replace('%code%', $code, file_get_contents('twofactor.html'));
        	mail($email, $subject, $email_template, $headers, mail_from);
        }
    } else {
        exit('No email and/or ID provided!');
    }
} else {
    exit('No email and/or ID provided!');
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>Two-factor Authentication</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body>
		<div class="login">
			<h1>Two-factor Authentication</h1>
            <p style="padding:15px;margin:0;">Please enter the code that was sent to your email address below.</p>
			<form action="" method="post">
                <label for="code">
					<i class="fas fa-lock"></i>
				</label>
				<input type="text" name="code" placeholder="Your Code" id="code" required>
				<div class="msg"><?=$msg?></div>
				<input type="submit" value="Submit">
			</form>
		</div>
	</body>
</html>