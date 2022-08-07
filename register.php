<?php
include 'main.php';
if (isset($_SESSION['loggedin'])) {
    header('Location: home.php');
    exit;
}

if (isset($_COOKIE['rememberme']) && !empty($_COOKIE['rememberme'])) {
    $stmt = $con->prepare('SELECT id, username role FROM accounts WHERE rememberme = ?');
    $stmt->bind_param('s', $_COOKIE['rememberme']);
    $stmt->execute();
    $stmt->store_results();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $role);
        $stmt->fetch();
        $stmt->close();

        session_regenerate_id();
        $_SESSION['loggedin'] = true;
        $_SESSION['name'] = $username;
        $_SESSION['id'] = $id;
        $_SESSION['role'] = $role;

        $date = date('Y-m-d\TH:i:s');
        $stmt = $con->prepare('UPDATE accounts SET last_seen = ? WHERE id = ?');
        $stmt->bind_param('si', $date, $id);
        $stmt->execute();
        $stmt->close();

        header('Location: home.php');
        exit;
    }
}
$_SESSION['token'] = md5(uniqid(rand(), true));

?>
<?php include 'structure/header.php'?>
<?php include 'structure/navigation.php'?>

<div class="p-5 mb-4 bg-light border rounded-3">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col">
                        <div class="row g-0">
                            <div class="col-xl-6 d-none d-xl-block">
                            <img src="img/register.png"
                                alt="Register" class="img-fluid"
                                style="border-top-left-radius: .25rem; border-bottom-left-radius: .25rem;" />
                            </div>
                            <div class="col-xl-6">
                            <div class="card-body p-md-5 text-black">
                                <form action="register-process.php" method="post">
                                <h3 class="mb-5 text-uppercase">Registration form</h3>
                                <div class="form-outline mb-4"> 
                                <label class="form-label" for="form3Example8">Name</label>
                                <input type="text" name="username" id="username" placeholder="Username" class="form-control form-control-lg" required/>
                                </div>
                                <div class="form-outline mb-4">
                                <label class="form-label" for="form3Example9">Email</label>
                                <input type="text" name="email" id="email" placeholder="Email" class="form-control form-control-lg" required/>
                                </div>
                                <div class="form-outline mb-4">
                                <label class="form-label" for="form3Example90">Password</label>
                                <input type="password" name="password" id="password" placeholder="Password" class="form-control form-control-lg" required/>
                                </div>
                                <div class="form-outline mb-4">
                                <label class="form-label" for="form3Example99">Confirm Password</label>
                                <input type="password" name="cpassword" id="cpassword" placeholder="Confirm password" class="form-control form-control-lg" required/>
                                </div>
                                <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
                                <div class="msg"> </div>
                                <div class="d-flex justify-content-end pt-3">
                                <button type="submit button" value="Register" class="btn btn-warning btn-lg ms-2">Register</button>
                                </div>
                            </form>
                            </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    </div>

<?php include 'structure/footer.php'?>
<?php include 'structure/page_end.php'?>