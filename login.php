<?php
/**
 * HopeFund — Admin Login
 * Authenticates against the admins table in the database.
 */

session_start();
require_once "php/db_connect.php"; // adjust path if needed

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === "" || $password === "") {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare("SELECT admin_id, username, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            if (password_verify($password, $admin["password"])) {
                $_SESSION["is_admin"] = true;
                $_SESSION["admin_username"] = $admin["username"];
                header("Location: report.php");
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login — HopeFund</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/admin.css">
  <style>
    .login-wrap {
        max-width: 380px;
        margin: 6rem auto;
        padding: 2rem;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
    }
    .login-wrap h1 { font-size: 1.5rem; margin-bottom: .3rem; }
    .login-wrap p { color: var(--ink-3); font-size: .9rem; margin-bottom: 1.5rem; }
    .login-field { margin-bottom: 1rem; }
    .login-field label { display: block; font-size: .82rem; font-weight: 600; margin-bottom: .3rem; }
    .login-field input {
        width: 100%; padding: .65rem .8rem;
        border: 1px solid var(--line); border-radius: var(--radius-sm); font-size: .95rem;
    }
    .login-error {
        background: #fdecea; color: #b3261e;
        padding: .6rem .8rem; border-radius: var(--radius-sm);
        font-size: .85rem; margin-bottom: 1rem;
    }
    .login-wrap .btn { width: 100%; text-align: center; cursor: pointer; border: none; }
  </style>
</head>
<body>

<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="welcome.php">
      <svg class="brand-mark" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M12 20.5S3.5 15 3.5 9.2A4.7 4.7 0 0 1 12 6.4a4.7 4.7 0 0 1 8.5 2.8c0 5.8-8.5 11.3-8.5 11.3Z"/>
      </svg>
      <span>Hope<strong>Fund</strong></span>
    </a>
  </div>
</header>

<main>
  <div class="login-wrap">
    <h1>Admin Login</h1>
    <p>Sign in to view the donation report.</p>

    <?php if ($error): ?>
      <div class="login-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="login-field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus>
      </div>
      <div class="login-field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="btn">Log in</button>
    </form>
  </div>
</main>

</body>
</html>