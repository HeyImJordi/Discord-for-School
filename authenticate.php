<?php
include 'main.php';

if (!isset($_POST['token']) || $_POST['token'] != $_SESSION['token']) {
	exit('Incorrect token provided!');
}

$login_attempts = login_attempts($con, FALSE);
if ($login_attempts && $login_attempts['attempts_left'] <= 0) {
	exit('You cannot login right now! Please try again later!');
}

if (!isset($_POST['username'], $_POST['password'])) {
    $login_attempts = login_attempts($con);
    exit('
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
    Please fill both fields with your username and password
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    </div>');
}

$stmt = $con->prepare('SELECT id, password, rememberme, activation_code, role FROM accounts WHERE username = ?');
$stmt->bind_param('s', $_POST['username']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $password, $rememberme, $activation_code, $role);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($_POST['password'], $password)) {
        if (account_activation && $activation_code != 'activated') {
			echo '<div class="alert alert-primary" role="alert">Please activate your account to login! Click <a href="resendactivation.php">here</a> to resend the activation email.</div>';
            //Resend activation code moet hierna nog komen
        } else {
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id;
            $_SESSION['role'] = $role;

            if (isset($_POST['rememberme'])) {
                $cookiehash = !empty($rememberme) ? $rememberme : password_hash($id . $_POST['username'] . 'yoursecretkey', PASSWORD_DEFAULT);
                $days = 30;

                setcookie('rememberme', $cookiehash, (int)(time()+60*60*24*$days));
                $stmt = $con->prepare('UPDATE accounts SET rememberme = ? WHERE id = ?');
                $stmt->bind_param('si', $cookiehash, $id);
                $stmt->execute();
                $stmt->close();
            }
            $date = date('Y-m-d\TH:i:s');
            $stmt = $con->prepare('UPDATE accounts SET last_seen = ? WHERE id = ?');
            $stmt->bind_param('si', $date, $id);
            $stmt->execute();
            $stmt->close();
            $ip = $_SERVER['REMOTE_ADDR'];
            $stmt = $con->prepare('DELETE FROM login_attempts WHERE ip_address = ?');
            $stmt->bind_param('s', $ip);
            $stmt->execute();
            $stmt->close();

            echo 'Success';
        }
    } else {
        $login_attempts = login_attempts($con, TRUE);
        echo '
        <div class="alert alert-danger fade show alert-dismissible" role="alert">
        Incorrect username and/or password! You have ' . $login_attempts['attempts_left'] . ' attempts remaining!
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        </div>';
    }
} else {
        $login_attempts = login_attempts($con, TRUE);
        echo '
        <div class="alert alert-danger fade show alert-dismissible" role="alert">
        Incorrect username and/or password! You have ' . $login_attempts['attempts_left'] . ' attempts remaining!
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        </div>';
    }
?>