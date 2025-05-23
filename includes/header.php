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
                <img src="../assets/img/logo.png" alt="Glimmr Logo" class="logo">
            </div>
            
            <nav class="navbar">
                <ul class="nav-list d-flex">

                    <li class="nav-item"><a href="../pages/home.php">
                    <img border="0" alt="W3Schools" src="../assets/img/home.png" width="70" height="28" 
                    class="nav-link"></a></li>

                    <?php if(isset($_SESSION['user_id'])): ?>

                        <li class="nav-item"><a href="../pages/feed.php">
                        <img border="0" alt="Feed" src="../assets/img/feed.png" width="70" height="28" 
                        class="nav-link"></a></li>

                        <li class="nav-item"><a href="../includes/auth.php?logout=true">
                        <img border="0" alt="Logout" src="../assets/img/logout.png" width="70" height="28" 
                        class="nav-link"></a></li>

                    <?php else: ?>

                        <li class="nav-item"><a href="../pages/login.php">
                            <img border="0" alt="Login" src="../assets/img/login.png" width="70" height="28" 
                            class="nav-link"></a></li>
                        
                        <li class="nav-item"><a href="../pages/signup.php">
                            <img border="0" alt="Register" src="../assets/img/register.png" width="70" height="28" 
                            class="nav-link"></a></li>

                    <?php endif; ?>

                </ul>
            </nav>
        </div>
    </header>


    

    