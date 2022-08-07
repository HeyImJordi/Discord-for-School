<?php
include_once 'config.php';
session_start();
$con = mysqli_connect(db_host, db_user, db_pass, db_name);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

mysqli_set_charset($con, db_charset);

function check_loggedin($con, $redirect_file = 'login.php') {
    if (isset($_COOKIE['rememberme']) && !empty($_COOKIE['rememberme']) && !isset($_SESSION['loggedin'])) {
        $stmt = $con->prepare('SELECT id, username, role FROM accounts WHERE rememberme = ?');
        $stmt->bind_param('s', $_COOKIE['rememberme']);
        $stmt->execute();
        $stmt->store_results();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $username, $role);
            $stmt->fetch();
            $stmt->close();

            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $username;
            $_SESSION['id'] = $id;
            $_SESSION['role'] = $role;

            $date = date('Y-m-d\TH:i:s');
            $stmt = $con->prepare('UPDATE accounts SET last_seen = ? WHERE id = ?');
            $stmt->bind_param('si', $date, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            header('Location: ' . $redirect_file);
            exit;
        }
    } else if (!isset($_SESSION['loggedin'])) {
        header('Location: ' . $redirect_file);
        exit;
    }
}

function check($con){
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $role);
        $stmt->fetch();
        $stmt->close();

        session_regenerate_id();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['name'] = $username;
        $_SESSION['id'] = $id;
        $_SESSION['role'] = $role;

        $date = date('Y-m-d\TH:i:s');
        $stmt = $con->prepare('UPDATE accounts SET last_seen = ? WHERE id = ?');
        $stmt->bind_param('si', $date, $id);
        $stmt->execute();
        $stmt->close();
        echo 'TRUE';
    } return false;
}

function logged($con) {
    if (isset($_COOKIE['rememberme']) && !empty($_COOKIE['rememberme']) && !isset($_SESSION['loggedin'])) {
        $stmt = $con->prepare('SELECT id, username, role FROM accounts WHERE rememberme = ?');
        $stmt->bind_param('s', $_COOKIE['rememberme']);
        $stmt->execute();
        $stmt->store_results();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $username, $role);
            $stmt->fetch();
            $stmt->close();

            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $username;
            $_SESSION['id'] = $id;
            $_SESSION['role'] = $role;

            $date = date('Y-m-d\TH:i:s');
            $stmt = $con->prepare('UPDATE accounts SET last_seen = ? WHERE id = ?');
            $stmt->bind_param('si', $date, $id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

function send_activation_email($email, $code) {
    $subject = 'Account Activation Required';
    $headers = 'From: ' . mail_from . "\r\n" . 'Reply-To: ' . mail_from . "\r\n" . 'Return-Path: ' . mail_from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
    $activate_link = activation_link . '?email=' . $email . '$code=' . $code;
    $email_template = str_replace('%link%', $activate_link, file_get_contents('activation-email-template.html'));
    mail($email, $subject, $email_template, $headers);
}

function login_attempts($con, $update = TRUE) {
	$ip = $_SERVER['REMOTE_ADDR'];
	$now = date('Y-m-d H:i:s');
	if ($update) {
		$stmt = $con->prepare('INSERT INTO login_attempts (ip_address, `date`) VALUES (?,?) ON DUPLICATE KEY UPDATE attempts_left = attempts_left - 1, `date` = VALUES(`date`)');
		$stmt->bind_param('ss', $ip, $now);
		$stmt->execute();
		$stmt->close();
	}
	$stmt = $con->prepare('SELECT * FROM login_attempts WHERE ip_address = ?');
	$stmt->bind_param('s', $ip);
	$stmt->execute();
	$result = $stmt->get_result();
	$login_attempts = $result->fetch_array(MYSQLI_ASSOC);
	$stmt->close();
	if ($login_attempts) {
		// The user can try to login after 1 day... change the "+1 day" if you want to increase/decrease this date.
		$expire = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($login_attempts['date'])));
		if ($now > $expire) {
			$stmt = $con->prepare('DELETE FROM login_attempts WHERE ip_address = ?');
			$stmt->bind_param('s', $ip);
			$stmt->execute();
			$stmt->close();
			$login_attempts = array();
		}
	}
	return $login_attempts;
}
?>