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
    
    <link rel="icon" href="/assets/img/logo.png">

</head>
<body>
    <header class="header">
        <div class="container d-flex justify-content-between align-items-center w-100 p-0 m-0">

            <div class="logo-container d-flex align-items-center">
                <a href="../pages/home.php"><img src="../assets/img/logo.png" alt="Glimmr Logo" class="logo"></a>
            </div>

            <nav class="navbar">
                <ul class="nav-list d-flex">

                    <li class="nav-item"><a href="../pages/home.php" class="btn btn-link" style="text-decoration: none;">
                    <i class="fas fa-house"></i> Home
                    </a></li>


                    <?php if(isset($_SESSION['user_id'])): ?>

                        <li class="nav-item"><a href="../pages/feed.php" class="btn btn-link" style="text-decoration: none;">
                        <i class="fas fa-newspaper"></i> Feed
                        </a></li>

                        <li class="nav-item"><a href="../includes/auth.php?logout=true">
                        <img border="0" alt="Logout" src="../assets/img/logout.png" width="70" height="28" 
                        class="nav-link"></a></li>

                    <?php else: ?>

                        <li class="nav-item">
                        <a href="../pages/login.php" class="btn btn-link" style="text-decoration: none;"> 
                            <i class="fas fa-sign-in-alt me-1"></i> Login</a>
                        </li>
                        
                        <li class="nav-item"> <a href="../pages/signup.php" class="btn btn-link" style="text-decoration: none; color: black;">
                        <i class="fas fa-user-plus me-1"></i> Register
                        </a></li>

                    <?php endif; ?>

                </ul>
            </nav>
        </div>
    </header>


    

    