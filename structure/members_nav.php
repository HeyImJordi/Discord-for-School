<main>
            <div class="col-lg-8 mx-auto p-3 py-md-5">
                <header class="d-flex align-items-center pb-3 mb-5 border-bottom">
                    <a href="index.php" class="d-flex align-items-center text-dark text-decoration-none">
                        <span class="fs-4">DEV-IT Academy</span>
                    </a>
                    <nav class="d-inline-flex mt-2 mt-md-0 ms-md-auto">
                        <a class="me-3 py-2 text-dark text-decoration-none" href="index.php">Home</a>
                        <a class="me-3 py-2 text-dark text-decoration-none" href="home.php">Profile</a>
                        <?php if ($_SESSION['role'] == 'Admin'): ?>
                        <a class="me-3 py-2 text-dark text-decoration-none" href="admin/index.php" target="_blank">Admin</a>
                        <?php endif; ?>
                        <a class="me-3 py-2 text-dark text-decoration-none" href="logout.php">Logout</a>
                    </nav>   
                </header>