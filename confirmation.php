<?php
/**
 * Online Donation System — Donation Confirmation
 * Part 2 (Member 2 — Eiman Asmat)
 *
 * Reached only by redirect from donate.php after a successful insert.
 * The donation id must match the one stored in the session, so people
 * cannot read other donors' records by changing the number in the URL.
 */

session_start();
require_once __DIR__ . '/php/db_connect.php';

$donation_id = isset($_GET['donation_id']) ? (int) $_GET['donation_id'] : 0;
$allowed     = isset($_SESSION['last_donation_id']) && (int) $_SESSION['last_donation_id'] === $donation_id;

$donation = null;
if ($donation_id > 0 && $allowed) {
    $sql = "SELECT d.donation_id, d.amount, d.payment_method, d.payment_status, d.donation_date,
                   dn.name AS donor_name, dn.email AS donor_email,
                   c.cause_name
            FROM donations d
            JOIN donors dn ON d.donor_id = dn.donor_id
            JOIN causes c  ON d.cause_id = c.cause_id
            WHERE d.donation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $donation_id);
    $stmt->execute();
    $donation = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$donor_message = $_SESSION['donor_message'] ?? '';
$failed        = $donation && $donation['payment_status'] !== 'Success';

function e($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $donation ? 'Thank you' : 'Donation not found'; ?> — HopeFund</title>
  <meta name="robots" content="noindex">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/donate.css">
</head>
<body>

<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="index.php">
      <svg class="brand-mark" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M12 20.5S3.5 15 3.5 9.2A4.7 4.7 0 0 1 12 6.4a4.7 4.7 0 0 1 8.5 2.8c0 5.8-8.5 11.3-8.5 11.3Z"/>
      </svg>
      <span>Hope<strong>Fund</strong></span>
    </a>
    <nav class="site-nav" aria-label="Main">
      <a href="index.php">Home</a>
      <a href="index.php#causes">Causes</a>
      <a href="admin/report.php">Admin</a>
    </nav>
  </div>
</header>

<main class="donate-page">
  <div class="container">

<?php if (!$donation): ?>

    <section class="notice notice--warn">
      <h1>We can't show that donation</h1>
      <p>
        Either the reference number is wrong, or this confirmation has already been
        closed. Receipts are only shown to the person who just donated.
      </p>
      <p><a class="btn" href="index.php#causes">Back to causes</a></p>
    </section>

<?php else: ?>

    <ol class="steps-bar" aria-label="Progress">
      <li class="is-done">Your details</li>
      <li class="is-done">Payment</li>
      <li class="is-current" aria-current="step">Confirmation</li>
    </ol>

    <section class="receipt">

<?php if ($failed): ?>

      <div class="receipt-tick receipt-tick--fail" aria-hidden="true">
        <svg viewBox="0 0 24 24"><path d="M7 7l10 10M17 7L7 17"/></svg>
      </div>

      <h1>Payment cancelled</h1>
      <p class="lede">
        Your donation to <strong><?php echo e($donation['cause_name']); ?></strong> was recorded as
        <strong>Failed</strong>, so nothing was collected. You can start again whenever you like.
      </p>

<?php else: ?>

      <div class="receipt-tick" aria-hidden="true">
        <svg viewBox="0 0 24 24"><path d="M4 12.5l5.2 5.2L20 7"/></svg>
      </div>

      <h1>Thank you, <?php echo e(explode(' ', $donation['donor_name'])[0]); ?>.</h1>
      <p class="lede">
        Your donation to <strong><?php echo e($donation['cause_name']); ?></strong> has been recorded.
        A reference number is below — keep it if you need to ask us about this donation.
      </p>

<?php endif; ?>

      <dl class="receipt-list">
        <div>
          <dt>Reference</dt>
          <dd class="ref">#HF-<?php echo str_pad((string) $donation['donation_id'], 5, '0', STR_PAD_LEFT); ?></dd>
        </div>
        <div>
          <dt>Cause</dt>
          <dd><?php echo e($donation['cause_name']); ?></dd>
        </div>
        <div>
          <dt>Amount</dt>
          <dd>Rs. <?php echo number_format((float) $donation['amount'], 2); ?></dd>
        </div>
        <div>
          <dt>Payment method</dt>
          <dd><?php echo e($donation['payment_method']); ?></dd>
        </div>
        <div>
          <dt>Status</dt>
          <dd><span class="badge <?php echo $failed ? 'badge--fail' : 'badge--ok'; ?>"><?php echo e($donation['payment_status']); ?></span></dd>
        </div>
        <div>
          <dt>Date</dt>
          <dd><?php echo date('j M Y, g:i a', strtotime($donation['donation_date'])); ?></dd>
        </div>
        <div>
          <dt>Donor</dt>
          <dd><?php echo e($donation['donor_name']); ?><br><span class="muted"><?php echo e($donation['donor_email']); ?></span></dd>
        </div>
      </dl>

      <?php if ($donor_message !== '' && !$failed): ?>
        <blockquote class="donor-message">
          <p><?php echo e($donor_message); ?></p>
          <footer>— your message to the team</footer>
        </blockquote>
      <?php endif; ?>

      <div class="receipt-actions">
        <?php if ($failed): ?>
          <a class="btn" href="index.php#causes">Try again</a>
        <?php else: ?>
          <a class="btn" href="index.php#causes">Donate to another cause</a>
          <button type="button" class="btn btn-ghost" onclick="window.print()">Print receipt</button>
        <?php endif; ?>
      </div>

      <p class="fine-print">
        This is an academic project. No real payment was processed.
      </p>
    </section>

<?php endif; ?>

  </div>
</main>

<footer class="site-footer">
  <div class="container footer-inner">
    <div class="footer-brand">
      <span class="brand">Hope<strong>Fund</strong></span>
      <p>An academic project — Online Donation System built with PHP &amp; MySQL.</p>
    </div>
    <nav class="footer-links" aria-label="Footer">
      <a href="index.php#causes">Causes</a>
      <a href="admin/report.php">Admin report</a>
    </nav>
  </div>
  <div class="container footer-bottom">
    <p>&copy; <?php echo date('Y'); ?> HopeFund. All donations shown are for demonstration purposes.</p>
  </div>
</footer>

</body>
</html>
