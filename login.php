<?php
session_start();
include "../layout/header.php";  // Include the header

// Initialize variables
$email = "";
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include "../include/config.php"; // Include DB connection function

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($email) || empty($password)) {
        $error = "Email and Password are required!";
    } else {
        $conn = getDatabaseConnection();

        // Prepare SQL
        $statement = $conn->prepare("SELECT id, first_name, last_name, phone, password, created_at FROM users WHERE email=?");
        $statement->bind_param('s', $email);
        $statement->execute();
        $statement->store_result();

        if ($statement->num_rows > 0) {
            $statement->bind_result($id, $first_name, $last_name, $phone, $stored_password, $created_at);
            $statement->fetch();

            // Verify password
            if (password_verify($password, $stored_password)) {
                // ✅ Store session data
                $_SESSION['authenticated'] = true;
                $_SESSION['id'] = $id;
                $_SESSION['email'] = $email;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                $_SESSION['phone'] = $phone;
                $_SESSION['created_at'] = $created_at;

                // ✅ Corrected redirect path
                header("Location: ../index.php");
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Email not found.";
        }

        $statement->close();
        $conn->close();
    }
}
?>

<div class="container py-5">
    <div class="mx-auto border shadow p-4" style="width: 400px">
        <h2 class="text-center mb-4">Login</h2>
        <hr />

        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><?= htmlspecialchars($error) ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input class="form-control" type="password" name="password" required>
            </div>

            <div class="row mb-3">
                <div class="col d-grid">
                    <button type="submit" class="btn btn-primary">Log in</button>
                </div>
                <div class="col d-grid">
                    <a href="../index.php" class="btn btn-outline-primary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include "../layout/footer.php"; ?>
