<?php
include 'main.php';
if (isset($_SESSION['loggedin'])) {
    header('Location: home.php');
    exit;
}

if (isset($_COOKIE['rememberme']) && !empty($_COOKIE['rememberme'])) {
    $stmt = $con->prepare('SELECT id, username, role FROM accounts WHERE username = ?');
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
                            <img src="img/register.png" alt="Register" class="img-fluid" style="border-top-left-radius: .25rem; border-bottom-left-radius: .25rem;" />
                        </div>
                        <div class="col-xl-6">
                            <div class="card-body p-md-5 text-black">
                            <form name="register" method="post" action="authenticate.php">
                                <div class="card-body p-md-5 text-black">
                                    <h3 class="mb-5 text-uppercase">Login</h3>
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="email ">Email</label>
                                        <input type="text" name="username" placeholder="Username" id="username" class="form-control form-control-lg" />
                                    </div>
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="password">Password</label>
                                        <input type="password" name="password" placeholder="Password" id="password" class="form-control form-control-lg" />
                                    </div>
                                    <input type="checkbox" name="rememberme" id="rememberme"> Remember Me
                                    <a href="forgotpassword.php" ">Forgot Password?</a>
                                    <input type="hidden" name="token" value="<?=$_SESSION['token']?>">    
                                    <div class="msg"> </div>
                                    <div class="d-flex justify-content-end pt-3">
                                        <input type="submit" value="Login" class="btn btn-warning btn-lg ms-2">
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
