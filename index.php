<?php
include "php/get_causes.php";
$causes = getAllCauses($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Donation System</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <h1>Give Hope, Make a Difference</h1>
    <p>Choose a cause below and support what matters to you.</p>
</header>

<main class="category-grid">
    <?php foreach ($causes as $cause): ?>
        <div class="category-card" data-cause="<?php echo strtolower($cause['cause_name']); ?>">
            <div class="card-icon">
                <?php
                // Simple icon per cause name
                $icons = [
                    'Education' => '📚',
                    'Health' => '🏥',
                    'Food' => '🍞',
                    'Emergency Relief' => '🚨'
                ];
                echo $icons[$cause['cause_name']] ?? '❤️';
                ?>
            </div>
            <h2><?php echo htmlspecialchars($cause['cause_name']); ?></h2>
            <p><?php echo htmlspecialchars($cause['description']); ?></p>
            <a href="donate.php?cause_id=<?php echo $cause['cause_id']; ?>" class="donate-btn">
                Donate Now
            </a>
        </div>
    <?php endforeach; ?>
</main>

<footer class="site-footer">
    <p>&copy; 2026 Online Donation System | Final Year Project</p>
</footer>

<script src="js/script.js"></script>
</body>
</html>