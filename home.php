<?php
include 'main.php';
check_loggedin($con);
$msg = '';
$stmt = $con->prepare('SELECT password, email, activation_code, role, registered FROM accounts WHERE id = ?');
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email, $activation_code, $role, $registered_date);
$stmt->fetch();
$stmt->close();
if (isset($_POST['username'], $_POST['password'], $_POST['cpassword'], $_POST['email'])) {
	if (empty($_POST['username']) || empty($_POST['email'])) {
		$msg = '<div class="alert alert-warning" role="alert">The input fields must not be empty!</div>';
	} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$msg = '<div class="alert alert-warning" role="alert">Please provide a valid email address!</div>';
	} else if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['username'])) {
	$msg = '<div class="alert alert-warning" role="alert">Username must contain only letters and numbers!</div>';
	} else if (!empty($_POST['password']) && (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5)) {
		$msg = '<div class="alert alert-warning" role="alert">Password must be between 5 and 20 characters long!</div>';
	} else if ($_POST['cpassword'] != $_POST['password']) {
		$msg = '<div class="alert alert-warning" role="alert">Passwords do not match!</div>';
	}
	if (empty($msg)) {
		$stmt = $con->prepare('SELECT * FROM accounts WHERE (username = ? OR email = ?) AND username != ? AND email != ?');
		$stmt->bind_param('ssss', $_POST['username'], $_POST['email'], $_SESSION['name'], $email);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			$msg = '<div class="alert alert-warning" role="alert">Account already exists with that username and/or email!</div>';
		} else {
			$stmt->close();
			$uniqid = account_activation && $email != $_POST['email'] ? uniqid() : $activation_code;
			$stmt = $con->prepare('UPDATE accounts SET username = ?, password = ?, email = ?, activation_code = ? WHERE id = ?');
			$password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $password;
			$stmt->bind_param('ssssi', $_POST['username'], $password, $_POST['email'], $uniqid, $_SESSION['id']);
			$stmt->execute();
			$stmt->close();
			$_SESSION['name'] = $_POST['username'];
			if (account_activation && $email != $_POST['email']) {
				send_activation_email($_POST['email'], $uniqid);
				unset($_SESSION['loggedin']);
				$msg = '<div class="alert alert-success" role="alert">You have changed your email address! You need to re-activate your account!</div>';
			} else {
				header('Location: login.php');
				exit;
			}
		}
	}
}
?>
<?php include 'structure/header.php'?>
<?php include 'structure/navigation.php'?>

<?php if (!isset($_GET['action'])): ?>
    <div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
        <div class="card-body text-center">
            <img src="img/profile.png" alt="avatar"
            class="rounded-circle img-fluid" style="width: 150px;">
            <h5 class="my-3"><?=$_SESSION['name']?></h5>
            <p class="text-muted mb-1"><?=$_SESSION['role']?></p>
            <p class="text-muted mb-4"></p>
            <div class="d-flex justify-content-center mb-2">
            <a href="home.php?action=edit" class="btn btn-primary">Edit profile</a>
            &ensp;
            </div>
        </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card mb-4">
        <div class="card-body">
            <div class="row">
            <div class="col-sm-3">
                <p class="mb-0">Username</p>
            </div>
            <div class="col-sm-9">
                <p class="text-muted mb-0"><?=$_SESSION['name']?></p>
            </div>
            </div>
            <hr>
            <div class="row">
            <div class="col-sm-3">
                <p class="mb-0">Full Name</p>
            </div>
            <div class="col-sm-9">
                <p class="text-muted mb-0">Jordi Martopawiro</p>
            </div>
            </div>
            <hr>
            <div class="row">
            <div class="col-sm-3">
                <p class="mb-0">School Email</p>
            </div>
            <div class="col-sm-9">
                <p class="text-muted mb-0"><?=$email?></p>
            </div>
            </div>
            <hr>
            <div class="row">
            <div class="col-sm-3">
                <p class="mb-0">Registered</p>
            </div>
            <div class="col-sm-9">
                <p class="text-muted mb-0"><?=$registered_date?></p>
            </div>
            </div>

        </div>
        </div>
        <!-- Password reset starts here-->

<?php elseif ($_GET['action'] == 'edit'): ?>
    <div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
        <div class="card-body text-center">
            <img src="img/profile.png" alt="avatar"
            class="rounded-circle img-fluid" style="width: 150px;">
            <h5 class="my-3"><?=$_SESSION['name']?></h5>
            <p class="text-muted mb-1"><?=$_SESSION['role']?></p>
            <p class="text-muted mb-4"></p>
            <div class="d-flex justify-content-center mb-2">
            </div>
        </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card mb-4">
        <form action="home.php?action=edit" method="post">
        <div class="card-body">
            <div class="row">
            <div class="col-sm-3">
                <p class="mb-0">Username</p>
            </div>
            <div class="col-sm-9">
            <input type="text" value="<?=$_SESSION['name']?>" name="username" id="username" placeholder="Username">
            </div>
            </div>
            <hr>
            <div class="row">
            <div class="col-sm-3">
                <p class="mb-0">New Password</p>
            </div>
            <div class="col-sm-9">
            <input type="password" name="password" id="password" placeholder="New Password">
            </div>
            </div>
            <hr>
            <div class="row">
            <div class="col-sm-3">
                <p class="mb-0">Confirm Password</p>
            </div>
            <div class="col-sm-9">
            <input type="password" name="cpassword" id="cpassword" placeholder="Confirm Password">
            </div>
            </div>
            <hr>
            <div class="row">
            <div class="col-sm-3">
                <p class="mb-0">Email</p>
            </div>
            <div class="col-sm-9">
            <input type="email" value="<?=$email?>" name="email" id="email" placeholder="Email">
            </div>
            </div>
            <p><?=$msg?></p>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
        </div>
    </div>
<?php endif; ?>
</div>
</div>

<?php include 'structure/footer.php'?>
<?php include 'structure/page_end.php'?>
