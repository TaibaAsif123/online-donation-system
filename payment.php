<?php
/**
 * Online Donation System — Simulated Payment Step
 * Part 2 (Member 2 — Eiman Asmat)
 *
 *   donate.php  →  payment.php  →  confirmation.php
 *
 * The donation row already exists with payment_status = 'Pending'.
 * This screen stands in for a real payment gateway: confirming flips the
 * status to 'Success', cancelling flips it to 'Failed'. No card, wallet or
 * bank details are asked for or stored — the whole step is a simulation for
 * the project, which is why the donations table only records the method and
 * the resulting status.
 */

session_start();
require_once __DIR__ . '/php/db_connect.php';

$donation_id = isset($_GET['donation_id']) ? (int) $_GET['donation_id'] : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donation_id = isset($_POST['donation_id']) ? (int) $_POST['donation_id'] : 0;
}

/* Only the person who just filled the form can act on this donation. */
$allowed = isset($_SESSION['pending_donation_id'])
        && (int) $_SESSION['pending_donation_id'] === $donation_id;

/* Load the pending donation. */
$donation = null;
if ($donation_id > 0 && $allowed) {
    $sql = "SELECT d.donation_id, d.amount, d.payment_method, d.payment_status,
                   dn.name AS donor_name, dn.email AS donor_email,
                   c.cause_id, c.cause_name
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

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

$error = '';

/* ------------------------------------------------------------------
   Handle "Pay now" / "Cancel".
   ------------------------------------------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $donation) {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'Your session expired. Please start the donation again.';

    } elseif ($donation['payment_status'] !== 'Pending') {
        $error = 'This donation has already been processed.';

    } else {
        $action    = $_POST['action'] ?? '';
        $newStatus = $action === 'pay' ? 'Success' : ($action === 'cancel' ? 'Failed' : '');

        if ($newStatus === '') {
            $error = 'Unknown action.';
        } else {
            $stmt = $conn->prepare("UPDATE donations SET payment_status = ? WHERE donation_id = ?");
            $stmt->bind_param("si", $newStatus, $donation_id);
            $stmt->execute();
            $stmt->close();

            unset($_SESSION['pending_donation_id']);

            if ($newStatus === 'Success') {
                $_SESSION['last_donation_id'] = $donation_id;
                header('Location: confirmation.php?donation_id=' . $donation_id);
                exit;
            }

            $_SESSION['last_donation_id'] = $donation_id;
            header('Location: confirmation.php?donation_id=' . $donation_id . '&cancelled=1');
            exit;
        }
    }
}

$accent_map = [
    'Education'        => 'education',
    'Health'           => 'health',
    'Food'             => 'food',
    'Emergency Relief' => 'relief',
];
$accent = $donation ? ($accent_map[$donation['cause_name']] ?? 'education') : 'education';

function e($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Payment — HopeFund</title>
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

<main class="donate-page donate-page--<?php echo e($accent); ?>">
  <div class="container">

<?php if (!$donation): ?>

    <section class="notice notice--warn">
      <h1>Nothing to pay for</h1>
      <p>This payment link has expired or the donation was already processed. Start again from the causes page.</p>
      <p><a class="btn" href="index.php#causes">Back to causes</a></p>
    </section>

<?php else: ?>

    <!-- Step indicator -->
    <ol class="steps-bar" aria-label="Progress">
      <li class="is-done">Your details</li>
      <li class="is-current" aria-current="step">Payment</li>
      <li>Confirmation</li>
    </ol>

    <section class="gateway">
      <div class="gateway-banner" role="note">
        <strong>Simulated payment</strong>
        <span>This is an academic project. No real transaction is made and no card,
        wallet or bank details are collected.</span>
      </div>

      <h1>Confirm your payment</h1>
      <p class="lede">
        Your donation is saved as <strong>Pending</strong>. Confirming below marks it
        as paid and takes you to your receipt.
      </p>

      <?php if ($error !== ''): ?>
        <div class="alert alert--error" role="alert">
          <strong>Payment not processed</strong>
          <p><?php echo e($error); ?></p>
        </div>
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
          <dt>Donor</dt>
          <dd><?php echo e($donation['donor_name']); ?></dd>
        </div>
        <div>
          <dt>Payment method</dt>
          <dd><?php echo e($donation['payment_method']); ?></dd>
        </div>
        <div>
          <dt>Status</dt>
          <dd><span class="badge badge--pending"><?php echo e($donation['payment_status']); ?></span></dd>
        </div>
      </dl>

      <p class="gateway-total">
        <span>Total to pay</span>
        <strong>Rs. <?php echo number_format((float) $donation['amount'], 2); ?></strong>
      </p>

      <form method="post" action="payment.php?donation_id=<?php echo (int) $donation['donation_id']; ?>"
            class="gateway-actions" id="paymentForm">
        <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
        <input type="hidden" name="donation_id" value="<?php echo (int) $donation['donation_id']; ?>">

        <button type="submit" name="action" value="pay" class="btn btn-block">
          Pay Rs. <?php echo number_format((float) $donation['amount'], 2); ?> with <?php echo e($donation['payment_method']); ?>
        </button>
        <button type="submit" name="action" value="cancel" class="btn btn-ghost btn-block">
          Cancel payment
        </button>
      </form>

      <p class="fine-print">
        Cancelling records the donation as <em>Failed</em> so the admin report shows what happened.
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

<script>
/* Stop double-clicks from posting the gateway twice.
   The buttons are disabled on a timeout so the clicked button's
   name/value is still included in the submitted form data. */
(function () {
  var form = document.getElementById('paymentForm');
  if (!form) return;
  form.addEventListener('submit', function () {
    setTimeout(function () {
      form.querySelectorAll('button').forEach(function (b) { b.disabled = true; });
    }, 0);
  });
})();
</script>
</body>
</html>
