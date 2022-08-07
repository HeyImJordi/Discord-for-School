<?php
include 'main.php';

if (!isset($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
	exit('Incorrect token provided!');
}

if (!isset($_POST['username'], $_POST['password'], $_POST['cpassword'], $_POST['email'])) {
    exit('<div class="alert alert-warning alert-dismissible fade show" role="alert">Please complete the registration form</div>');
}
if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
    exit('<div class="alert alert-warning alert-dismissible fade show" role="alert">Please complete the registration form</div>');
}
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    exit('<div class="alert alert-warning alert-dismissible fade show" role="alert">Please provide a valid email address</div>');
}
if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['username'])) {
    exit('<div class="alert alert-warning alert-dismissible fade show" role="alert">Please provide a valid username</div>');
}
if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
    exit('<div class="alert alert-warning alert-dismissible fade show" role="alert">Password must be at least 5 characters long</div>');
}
if ($_POST['cpassword'] != $_POST['password']) {
    exit ('<div class="alert alert-warning alert-dismissible fade show" role="alert">Passwords do not match</div>');
}

$stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ? OR email = ?');
$stmt->bind_param('ss', $_POST['username'], $_POST['email']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">Username and/or email already exists</div>';
} else {
    $stmt->close();
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$uniqid = account_activation ? uniqid() : 'activated';
    $role = 'Member';
    $date = date('Y-m-d\TH:i:s');
    $stmt = $con->prepare('INSERT INTO accounts (username, password, email, activation_code, role, registered, last_seen) VALUES (?, ?, ?, ?, ?, ?, ?)');
	$stmt->bind_param('sssssss', $_POST['username'], $password, $_POST['email'], $uniqid, $role, $date, $date);
    $stmt->execute();
    $stmt->close();

    if (account_activation) {
        send_activation_email($_POST['email'], $uniqid);
        echo '<div class="alert alert-primary" role="alert">Please check your email to activate your account</div>';
    } else {
        if (auto_login_after_register) {
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $con->insert_id;
            $_SESSION['role'] = $role;
            echo 'Autologin';
        } else {
            echo '<div class="alert alert-success" role="alert">You have sucessfully registered! You can sign-in right away!</div>';
        }
    }
}
?>