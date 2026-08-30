<?php
/**
 * HopeFund — Register Admin (ONE-TIME USE)
 * Run this once to create your admin account, then DELETE this file.
 */

require_once "php/db_connect.php"; // adjust path if your db_connect.php lives elsewhere

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === "" || $password === "") {
        $message = "Both fields are required.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword);

        if ($stmt->execute()) {
            $success = true;
            $message = "Admin '$username' created successfully. You can now log in at login.php. Delete this file now.";
        } else {
            $message = "Error: " . $stmt->error . " (username may already exist)";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register Admin — HopeFund</title>
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
    .login-field { margin-bottom: 1rem; }
    .login-field label { display: block; font-size: .82rem; font-weight: 600; margin-bottom: .3rem; }
    .login-field input {
        width: 100%; padding: .65rem .8rem;
        border: 1px solid var(--line); border-radius: var(--radius-sm); font-size: .95rem;
    }
    .msg-success { background: #e7f6ec; color: #1a7a3a; padding: .6rem .8rem; border-radius: var(--radius-sm); font-size: .85rem; margin-bottom: 1rem; }
    .msg-error { background: #fdecea; color: #b3261e; padding: .6rem .8rem; border-radius: var(--radius-sm); font-size: .85rem; margin-bottom: 1rem; }
    .login-wrap .btn { width: 100%; text-align: center; cursor: pointer; border: none; }
  </style>
</head>
<body>
<main>
  <div class="login-wrap">
    <h1>Register Admin</h1>
    <p style="color:var(--ink-3); font-size:.9rem; margin-bottom:1.5rem;">One-time setup. Delete this file after use.</p>

    <?php if ($message): ?>
      <div class="<?php echo $success ? 'msg-success' : 'msg-error'; ?>">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <?php if (!$success): ?>
      <form method="POST" action="register_admin.php">
        <div class="login-field">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required autofocus>
        </div>
        <div class="login-field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Create Admin</button>
      </form>
    <?php endif; ?>
  </div>
</main>
</body>
</html>