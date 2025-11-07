<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION["email"])) {
    header("Location: ./include/login.php");
    exit;
}

// Include header
include "./layout/header.php";
?>
<div class="container py-5">
  <div class="row">
    <div class="col-lg-6 mx-auto border shadow p-4">
      <h2 class="text-center mb-4">Profile</h2>
      <hr>

      <div class="row mb-3">
        <div class="col-sm-4">First Name</div>
        <div class="col-sm-8"><?= htmlspecialchars($_SESSION["first_name"] ?? "Not available") ?></div>
      </div>

      <div class="row mb-3">
        <div class="col-sm-4">Last Name</div>
        <div class="col-sm-8"><?= htmlspecialchars($_SESSION["last_name"] ?? "Not available") ?></div>
      </div>

      <div class="row mb-3">
        <div class="col-sm-4">Email</div>
        <div class="col-sm-8"><?= htmlspecialchars($_SESSION["email"] ?? "Not available") ?></div>
      </div>

      <div class="row mb-3">
        <div class="col-sm-4">Phone</div>
        <div class="col-sm-8"><?= htmlspecialchars($_SESSION["phone"] ?? "Not available") ?></div>
      </div>

      <div class="row mb-3">
        <div class="col-sm-4">Registered At</div>
        <div class="col-sm-8"><?= htmlspecialchars($_SESSION["created_at"] ?? "Not available") ?></div>
      </div>
    </div>
  </div>
</div>

<?php include "./layout/footer.php"; ?>
