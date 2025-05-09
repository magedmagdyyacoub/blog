<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Blog</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Bootstrap CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

  <!-- Custom Stylesheets -->
   <link rel="stylesheet" href="/blog/public/css/style.css">
  <link rel="stylesheet" href="/blog/public/css/header.css">
  <link rel="stylesheet" href="/blog/public/css/footer.css">
  <link rel="stylesheet" href="/blog/public/css/index.css">
  <link rel="stylesheet" href="/blog/public/css/login.css">
  <link rel="stylesheet" href="/blog/public/css/register.css">
  <link rel="stylesheet" href="/blog/public/css/dashboard.css">
</head>
<body>

<header class="bg-dark text-light py-3">
  <div class="container">
    <h1><a href="/index.php" class="text-warning text-decoration-none">My Blog</a></h1>
    
    <!-- Bootstrap Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
      <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">

            <?php if (isset($_SESSION['user'])): ?>
              <li class="nav-item"><a class="nav-link" href="/blog/public/dashboard.php">Dashboard</a></li>
              <li class="nav-item"><a class="nav-link" href="/blog/public/logout.php">Logout</a></li>
            <?php else: ?>
              <li class="nav-item"><a class="nav-link" href="/blog/public/login.php">Login</a></li>
              <li class="nav-item"><a class="nav-link" href="/blog/public/register.php">Register</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>
    
    <hr class="border-warning mt-1">
  </div>
</header>
