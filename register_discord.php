<?php
include 'main.php';
check_loggedin($con);
$stmt = $con->prepare('SELECT password, email, role, registered ,firstName, lastName, studentnumber, dc_username FROM accounts WHERE id = ?');
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email, $role, $registered_date, $firstName, $lastName, $studentNumber, $dc_username);
$stmt->fetch();
$stmt->close();
?>
<?php include 'structure/header.php'?>
<?php include 'structure/navigation.php'?>
<body class="container mt-5">
    <form onsubmit="sendContact(event)">
    <div class="mb-3">
        <label for="emailInput" class="form-label">Student Email</label>
        <input type="email" value="<?=$email?>" placeholder="<?=$email?>" class="form-control" id="emailInput" readonly="readonly">
        <label for="firstNameInput" class="form-label">First Name</label>
        <input type="text" value="<?=$firstName?>" placeholder="<?=$firstName?>" class="form-control" id="firstName" readonly="readonly">
        <label for="lastNameInput" class="form-label">Last Name</label>
        <input type="text" value="<?=$lastName?>" placeholder="<?=$lastName?>" class="form-control" id="lastName" readonly="readonly">
        <label for="studentNumerInput" class="form-label">Student number</label>
        <input type="number" value="<?=$studentNumber?>" placeholder="<?=$studentNumber?>" class="form-control" id="studentNumber" readonly="readonly">
        <label for="dcUsernameInput" class="form-label">Discord username</label>
        <input type="text" value="<?=$dc_username?>" placeholder="username#0000" class="form-control" id="discordUsername">
    </div>
    <div class="mb-3">
        <label for="messageInput" class="form-label">Enter your message</label>
        <textarea class="form-control" id="messageInput" rows="3"></textarea>
    </div>
    <input type="email" value="<?=$email?>" placeholder="<?=$email?>" class="form-control" id="emailInput" readonly="readonly" hidden>
    <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <script type = "text/javascript" src="js/webhook.js"></script>

<?php include 'structure/footer.php'?>
<?php include 'structure/page_end.php'?>
