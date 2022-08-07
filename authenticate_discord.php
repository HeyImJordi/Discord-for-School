<?php
include 'main.php';

$stmt = $con->prepare('SELECT id, email FROM accounts WHERE email = ?');
$stmt->bind_param('s', $_POST['email']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">Username and/or email already exists</div>';
} else {
    echo'<div class="alert alert-success" role="alert">You have sucessfully registered! You can sign-in right away!</div>';
}