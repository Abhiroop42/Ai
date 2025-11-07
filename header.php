<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Default: not authenticated
$authenticated = false;

// Check login status (set in login.php)
if (isset($_SESSION["authenticated"]) && $_SESSION["authenticated"] === true) {
    $authenticated = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Learning</title>
    <link rel="icon" href="./ai-generate-logo.webp">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top border-bottom shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="/index.php">
      <img src="./ai-generate-logo.webp" width="30" height="30" class="me-2"> AI Learning
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/register.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Courses</a></li>
        <li class="nav-item"><a class="nav-link" href="#">About</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if ($authenticated): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
              <?= htmlspecialchars($_SESSION['first_name'] ?? 'Admin') ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="../ai/profile.php">Profile</a></li>
              <li><hr class="dropdown-divider"></li>
             <li><a class="dropdown-item" href="/ai/include/logout.php">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a href="/ai/register.php" class="btn btn-outline-primary me-2">Register</a>
          </li>
          <li class="nav-item">
            <a href="include/login.php" class="btn btn-primary">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="pt-5 mt-5"><!-- Padding for fixed navbar -->
