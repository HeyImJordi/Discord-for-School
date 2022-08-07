<?php
include 'main.php';

$msg = '';

if (isset($_GET['email'], $_GET['code']) && !empty($_GET['code'])) {
    $stmt = $con->prepare('SELECT * FROM accounts WHERE email = ? AND activation_code = ?');
    $stmt->bind_param('ss', $_GET['email'], $_GET['code']);
    $stmt->execute();
    $stmt->store_results();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        $stmt = $con->prepare('UPDATE accounts SET activation_code = "activated" WHERE email = ? and activation_code = ?');
        $stmt->bind_param('ss', $_GET['email'], $_GET['code']);
        $stmt->execute();
        $stmt->close();
        $msg = 'Your account is now activated';
    } else {
        $msg = 'The account is already activated';
    }
} else {
    $msg = 'No code and/or email was specified';
}
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>Activate Account</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body class="loggedin">
		<div class="content">
			<p><?=$msg?></p>
		</div>
	</body>
</html>