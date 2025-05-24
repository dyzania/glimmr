<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glimmr - <?php echo $page_title ?? 'Social Media App'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="/glimmr/assets/css/style.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="/assets/js/nav-transitions.js" defer></script>
    <link rel="icon" href="/assets/img/logo.png">
    <script src="assets/js/script.js"></script>

</head>
<body>
    <header class="header">
        <div class="d-flex justify-content-between align-items-center w-100 p-0 m-0">
            <div class="logo-container d-flex align-items-center">
                <a href="../pages/home.php"><img src="../assets/img/logo.png" alt="Glimmr Logo" class="logo"></a>
            </div>

            <nav class="navbar">
                <ul class="nav-list d-flex">
                    <li class="nav-item"><a href="../pages/home.php" class="nav-link" style="text-decoration: none; ">
                    <i class="fas fa-house"></i> HOME
                    </a></li>


                    <?php if(isset($_SESSION['user_id'])): ?>

                        <li class="nav-item"><a href="../pages/feed.php" class="nav-link" style="text-decoration: none;">
                        <i class="fas fa-newspaper"></i> FEED
                        </a></li>

                        <li class="nav-item"><a href="../includes/auth.php?logout=true" onclick="showLogoutConfirmation()" class="nav-link" style="text-decoration: none;">
                        <i class="fas fa-right-from-bracket me-1"></i> LOGOUT
                        </a></li>

                    <?php else: ?>

                        <li class="nav-item">
                        <a href="../pages/login.php" class="nav-link" style="text-decoration: none;"> 
                            <i class="fas fa-sign-in-alt me-1"></i>LOGIN</a>
                        </li>
                        
                        <li class="nav-item"> <a href="../pages/signup.php" class="nav-link" style="text-decoration: none; color: black;">
                        <i class="fas fa-user-plus me-1"></i> SIGN-UP
                        </a></li>

                    <?php endif; ?>

                </ul>
            </nav>
        </div>
    </header>
    
</body>


    

    