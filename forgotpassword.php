<?php
include 'main.php';
// Output message
$msg = '';
// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if (isset($_POST['email'])) {
    // Prepare our SQL, preparing the SQL statement will prevent SQL injection.
    $stmt = $con->prepare('SELECT * FROM accounts WHERE email = ?');
    $stmt->bind_param('s', $_POST['email']);
    $stmt->execute();
    $stmt->store_result();
    // Check if the email exists...
    if ($stmt->num_rows > 0) {
    	$stmt->close();
        // Email exist, the $msg variable will be used to show the output message (on the HTML form)
        // Update the reset code in the database
    	$uniqid = uniqid();
        $stmt = $con->prepare('UPDATE accounts SET reset = ? WHERE email = ?');
        $stmt->bind_param('ss', $uniqid, $_POST['email']);
        $stmt->execute();
        $stmt->close();
        // Email to send below, customize this
    	$subject = 'Password Reset';
    	$headers = 'From: ' . mail_from . "\r\n" . 'Reply-To: ' . mail_from . "\r\n" . 'Return-Path: ' . mail_from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
        // Change the link below from "yourdomain.com" to your own domain name where the PHP login system is hosted
        $reset_link = 'http://localhost/dc/resetpassword.php?email=' . $_POST['email'] . '&code=' . $uniqid;
    	// Feel free to customize the email message below
    	$message = '<p>Please click the following link to reset your password: <a href="' . $reset_link . '">' . $reset_link . '</a></p>';
        // Send email to the user
    	mail($_POST['email'], $subject, $message, $headers);
        $msg = '
        <div class="alert alert-success fade show alert-dismissible" role="alert">Reset password link has been sent to your email!
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        </div>';
    } else {
        $msg = '
        <div class="alert alert-warning fade show alert-dismissible" role="alert">
        We do not have an account with that email!
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        </div>';
    }
}
?>
<?php include 'structure/header.php'?>
<?php include 'structure/navigation.php'?>

<div class="p-5 mb-4 bg-light border rounded-3">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col">
                    <div class="row g-0">
                        <div class="col-xl-6 d-none d-xl-block">
                            <img src="img/register.png" alt="Register" class="img-fluid" style="border-top-left-radius: .25rem; border-bottom-left-radius: .25rem;" />
                        </div>
                        <div class="col-xl-6">
						<form action="forgotpassword.php" method="post">
                            <div class="card-body p-md-5 text-black">
                                <div class="card-body p-md-5 text-black">
                                    <h3 class="mb-5 text-uppercase">Forgot Password</h3>
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="email ">Email</label>
                                        <input type="email" name="email" placeholder="Your Email" id="email" class="form-control form-control-lg" />
                                    </div>
                                        <div class="msg"><?=$msg?></div>
                                    <div class="d-flex justify-content-end pt-3">
                                        <input type="submit" value="Submit"class="btn btn-warning btn-lg ms-2">
                                    </div>   
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<script>
            let loginForm = document.querySelector(".card-body form");
            loginForm.onsubmit = event => {
                event.preventDefault();
                fetch(loginForm.action, { method: 'POST', body: new FormData(loginForm) }).then(response => response.text()).then(result => {
                    if (result.toLowerCase().includes("success")) {
                        window.location.href = "home.php";
                    } else {
                        document.querySelector(".msg").innerHTML = result;
                    }
                });
            };
        </script>
<?php include 'structure/footer.php'?>
<?php include 'structure/page_end.php'?>