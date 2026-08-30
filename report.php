<?php
/**
 * HopeFund — Admin Donation Report
 * Part 4 (Member 4)
 */

session_start();

// Block anyone who isn't logged in as admin
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== true) {
    header("Location: login.php");
    exit;
}

require_once "php/donation_functions.php";
// Get all donations using Member 3's reusable function
$donations = getAllDonations($conn);


// ============================================================
// Total donation amount
// ============================================================

$totalDonations = 0;

$result = $conn->query("
    SELECT COALESCE(SUM(amount), 0) AS total
    FROM donations
");

if ($result) {
    $row = $result->fetch_assoc();
    $totalDonations = $row["total"];
}


// ============================================================
// Number of unique donors
// ============================================================

$numberOfDonors = 0;

$result = $conn->query("
    SELECT COUNT(DISTINCT donor_id) AS total_donors
    FROM donations
");

if ($result) {
    $row = $result->fetch_assoc();
    $numberOfDonors = $row["total_donors"];
}


// ============================================================
// Donations per category
// ============================================================

$categoryTotals = [];

$sql = "
    SELECT
        c.cause_name,
        COALESCE(SUM(d.amount), 0) AS total_amount
    FROM causes c
    LEFT JOIN donations d
        ON c.cause_id = d.cause_id
    GROUP BY c.cause_id, c.cause_name
    ORDER BY c.cause_id
";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categoryTotals[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Admin Report — HopeFund</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">

</head>

<body>

<!-- ============================================================
     HEADER
============================================================ -->

<header class="site-header">

    <div class="container header-inner">

        <a class="brand" href="index.php">

            <svg
                class="brand-mark"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path d="M12 20.5S3.5 15 3.5 9.2A4.7 4.7 0 0 1 12 6.4a4.7 4.7 0 0 1 8.5 2.8c0 5.8-8.5 11.3-8.5 11.3Z"/>
            </svg>

            <span>Hope<strong>Fund</strong></span>

        </a>


        <nav class="site-nav" aria-label="Main">

            <a href="index.php">
                Home
            </a>

            <a href="index.php#causes">
                Causes
            </a>

            <a href="index.php#how">
                How it works
            </a>

                        <a href="report.php" class="is-active">
                Admin
            </a>

            <a href="logout.php">
                Logout
            </a>

            
                href="index.php#causes"
                class="btn btn-sm"
            >
                Donate
            </a>
            </a>

        </nav>

    </div>

</header>


<!-- ============================================================
     ADMIN HERO
============================================================ -->

<main>

<section class="admin-hero">

    <div class="container">

        <p class="eyebrow">
            Administration
        </p>

        <h1>
            Donation Report
        </h1>

        <p class="admin-lede">
            Monitor donations, donor activity, and the impact
            across each cause.
        </p>

    </div>

</section>


<!-- ============================================================
     SUMMARY
============================================================ -->

<section class="section">

    <div class="container">

        <header class="section-head">

            <h2>
                Donation overview
            </h2>

            <p>
                A quick summary of the donations recorded in HopeFund.
            </p>

        </header>


        <div class="admin-stats">

            <!-- Total Donations -->

            <article class="admin-stat">

                <div class="admin-stat-icon admin-stat-icon--brand">
                    $
                </div>

                <div>

                    <span class="admin-stat-label">
                        Total donations
                    </span>

                    <strong class="admin-stat-number">
                        Rs. <?php echo number_format($totalDonations, 2); ?>
                    </strong>

                </div>

            </article>


            <!-- Number of Donors -->

            <article class="admin-stat">

                <div class="admin-stat-icon admin-stat-icon--blue">
                    #
                </div>

                <div>

                    <span class="admin-stat-label">
                        Number of donors
                    </span>

                    <strong class="admin-stat-number">
                        <?php echo $numberOfDonors; ?>
                    </strong>

                </div>

            </article>


            <!-- Number of Donations -->

            <article class="admin-stat">

                <div class="admin-stat-icon admin-stat-icon--purple">
                    +
                </div>

                <div>

                    <span class="admin-stat-label">
                        Donation records
                    </span>

                    <strong class="admin-stat-number">
                        <?php echo count($donations); ?>
                    </strong>

                </div>

            </article>

        </div>

    </div>

</section>


<!-- ============================================================
     DONATIONS BY CATEGORY
============================================================ -->

<section class="section section--alt">

    <div class="container">

        <header class="section-head">

            <h2>
                Donations by category
            </h2>

            <p>
                See how much has been donated to each cause.
            </p>

        </header>


        <div class="category-report">

            <?php foreach ($categoryTotals as $category): ?>

                <?php

                $categoryClass = "category-row";

                $causeName = strtolower($category["cause_name"]);

                if (strpos($causeName, "education") !== false) {

                    $categoryClass .= " category-row--education";

                } elseif (strpos($causeName, "health") !== false) {

                    $categoryClass .= " category-row--health";

                } elseif (strpos($causeName, "food") !== false) {

                    $categoryClass .= " category-row--food";

                } elseif (strpos($causeName, "emergency") !== false) {

                    $categoryClass .= " category-row--relief";

                }

                ?>

                <article class="<?php echo $categoryClass; ?>">

                    <div class="category-info">

                        <span class="category-dot"></span>

                        <div>

                            <h3>

                                <?php

                                echo htmlspecialchars(
                                    $category["cause_name"]
                                );

                                ?>

                            </h3>

                            <span>
                                Cause category
                            </span>

                        </div>

                    </div>


                    <strong class="category-amount">

                        Rs.

                        <?php

                        echo number_format(
                            $category["total_amount"],
                            2
                        );

                        ?>

                    </strong>

                </article>

            <?php endforeach; ?>

        </div>

    </div>

</section>


<!-- ============================================================
     ALL DONATIONS
============================================================ -->

<section class="section">

    <div class="container">

        <header class="section-head">

            <h2>
                All donations
            </h2>

            <p>
                Complete record of donations submitted through the system.
            </p>

        </header>


        <div class="donation-report-card">

            <?php if (count($donations) > 0): ?>

                <div class="table-wrapper">

                    <table class="admin-table">

                        <thead>

                            <tr>

                                <th>
                                    Donor
                                </th>

                                <th>
                                    Cause
                                </th>

                                <th>
                                    Amount
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php foreach ($donations as $donation): ?>

                            <tr>

                                <td>

                                    <div class="donor-cell">

                                        <span class="donor-avatar">

                                            <?php

                                            echo strtoupper(
                                                substr(
                                                    $donation["donor_name"],
                                                    0,
                                                    1
                                                )
                                            );

                                            ?>

                                        </span>


                                        <strong>

                                            <?php

                                            echo htmlspecialchars(
                                                $donation["donor_name"]
                                            );

                                            ?>

                                        </strong>

                                    </div>

                                </td>


                                <td>

                                    <span class="cause-name">

                                        <?php

                                        echo htmlspecialchars(
                                            $donation["cause_name"]
                                        );

                                        ?>

                                    </span>

                                </td>


                                <td>

                                    <strong>

                                        Rs.

                                        <?php

                                        echo number_format(
                                            $donation["amount"],
                                            2
                                        );

                                        ?>

                                    </strong>

                                </td>


                                <td>

                                    <?php

                                    echo date(
                                        "d M Y",
                                        strtotime(
                                            $donation["donation_date"]
                                        )
                                    );

                                    ?>

                                </td>


                                <td>

                                    <?php

                                    $status = strtolower(
                                        $donation["payment_status"]
                                    );

                                    $statusClass = "status";

                                    if (
                                        $status === "success" ||
                                        $status === "completed"
                                    ) {

                                        $statusClass .= " status--success";

                                    } elseif ($status === "pending") {

                                        $statusClass .= " status--pending";

                                    } else {

                                        $statusClass .= " status--other";

                                    }

                                    ?>

                                    <span class="<?php echo $statusClass; ?>">

                                        <?php

                                        echo htmlspecialchars(
                                            $donation["payment_status"]
                                        );

                                        ?>

                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="empty-report">

                    <h3>
                        No donations yet
                    </h3>

                    <p>
                        Donation records will appear here once users
                        submit donations.
                    </p>

                    <a
                        href="index.php#causes"
                        class="btn"
                    >
                        View causes
                    </a>

                </div>

            <?php endif; ?>

        </div>

    </div>

</section>

</main>


<!-- ============================================================
     FOOTER
============================================================ -->

<footer class="site-footer">

    <div class="container footer-inner">

        <div class="footer-brand">

            <span class="brand">
                Hope<strong>Fund</strong>
            </span>

            <p>
                An academic project — Online Donation System
                built with PHP &amp; MySQL.
            </p>

        </div>


        <nav class="footer-links" aria-label="Footer">

            <a href="index.php#causes">
                Causes
            </a>

            <a href="index.php#how">
                How it works
            </a>

            <a href="report.php">
                Admin report
            </a>

        </nav>

    </div>


    <div class="container footer-bottom">

        <p>
            &copy;
            <?php echo date("Y"); ?>
            HopeFund. All donations shown are for demonstration purposes.
        </p>

    </div>

</footer>

</body>
</html>