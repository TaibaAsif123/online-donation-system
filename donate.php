<?php
/**
 * Online Donation System — Donation Form
 * Part 2 (Member 2 — Eiman Asmat)
 *
 * Flow:
 *   index.php  →  donate.php?cause_id=N  →  (POST, validate, insert)  →  confirmation.php
 *
 * Server-side validation runs on every POST regardless of the JavaScript in
 * js/donate.js — the client-side checks are only there for faster feedback.
 * Inserts go through getOrCreateDonor() / insertDonation() in
 * php/donation_functions.php, which use prepared statements.
 */

session_start();
require_once __DIR__ . '/php/donation_functions.php';

/* ------------------------------------------------------------------
   1. Which cause? Validate the query-string id against the database.
   ------------------------------------------------------------------ */
$cause_id = isset($_GET['cause_id']) ? (int) $_GET['cause_id'] : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cause_id = isset($_POST['cause_id']) ? (int) $_POST['cause_id'] : 0;
}

$cause = null;
if ($cause_id > 0) {
    $stmt = $conn->prepare("SELECT cause_id, cause_name, description FROM causes WHERE cause_id = ?");
    $stmt->bind_param("i", $cause_id);
    $stmt->execute();
    $cause = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

/* All causes — used for the dropdown so the donor can switch without going back. */
$all_causes = [];
$res = $conn->query("SELECT cause_id, cause_name FROM causes ORDER BY cause_id");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $all_causes[] = $row;
    }
}

/* Slug used for the accent colour on the page (matches css/style.css). */
$accent_map = [
    'Education'        => 'education',
    'Health'           => 'health',
    'Food'             => 'food',
    'Emergency Relief' => 'relief',
];
$accent = $cause ? ($accent_map[$cause['cause_name']] ?? 'education') : 'education';

/* ------------------------------------------------------------------
   2. CSRF token — one per session, checked on POST.
   ------------------------------------------------------------------ */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

/* ------------------------------------------------------------------
   3. Handle the submission.
   ------------------------------------------------------------------ */
$errors = [];
$values = [
    'donor_name'     => '',
    'donor_email'    => '',
    'amount'         => '',
    'payment_method' => '',
    'message'        => '',
];

$payment_methods = ['Credit Card', 'Debit Card', 'Bank Transfer', 'JazzCash', 'EasyPaisa'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- CSRF ---
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $errors['form'] = 'Your session expired. Please submit the form again.';
    }

    // Keep what was typed so the form can be re-filled on error.
    $values['donor_name']     = trim($_POST['donor_name'] ?? '');
    $values['donor_email']    = trim($_POST['donor_email'] ?? '');
    $values['amount']         = trim($_POST['amount'] ?? '');
    $values['payment_method'] = trim($_POST['payment_method'] ?? '');
    $values['message']        = trim($_POST['message'] ?? '');

    // --- Cause ---
    if (!$cause) {
        $errors['cause_id'] = 'Please choose a valid cause.';
    }

    // --- Name ---
    if ($values['donor_name'] === '') {
        $errors['donor_name'] = 'Please enter your full name.';
    } elseif (mb_strlen($values['donor_name']) < 3) {
        $errors['donor_name'] = 'Name must be at least 3 characters.';
    } elseif (mb_strlen($values['donor_name']) > 100) {
        $errors['donor_name'] = 'Name cannot be longer than 100 characters.';
    } elseif (!preg_match("/^[\p{L}][\p{L}\s.'-]*$/u", $values['donor_name'])) {
        $errors['donor_name'] = 'Name can only contain letters, spaces, apostrophes, hyphens and dots.';
    }

    // --- Email ---
    if ($values['donor_email'] === '') {
        $errors['donor_email'] = 'Please enter your email address.';
    } elseif (!filter_var($values['donor_email'], FILTER_VALIDATE_EMAIL)) {
        $errors['donor_email'] = 'That does not look like a valid email address.';
    } elseif (mb_strlen($values['donor_email']) > 150) {
        $errors['donor_email'] = 'Email cannot be longer than 150 characters.';
    }

    // --- Amount ---
    if ($values['amount'] === '') {
        $errors['amount'] = 'Please enter a donation amount.';
    } elseif (!is_numeric($values['amount'])) {
        $errors['amount'] = 'Amount must be a number.';
    } elseif ((float) $values['amount'] < 100) {
        $errors['amount'] = 'The minimum donation is Rs. 100.';
    } elseif ((float) $values['amount'] > 1000000) {
        $errors['amount'] = 'For donations above Rs. 1,000,000 please contact us directly.';
    }

    // --- Payment method ---
    if ($values['payment_method'] === '') {
        $errors['payment_method'] = 'Please select a payment method.';
    } elseif (!in_array($values['payment_method'], $payment_methods, true)) {
        $errors['payment_method'] = 'Please select a payment method from the list.';
    }

    // --- Optional message ---
    if (mb_strlen($values['message']) > 300) {
        $errors['message'] = 'Message cannot be longer than 300 characters.';
    }

    // --- Everything valid → save it as Pending, then go to the payment step ---
    if (!$errors) {
        $amount = round((float) $values['amount'], 2);

        $donor_id = getOrCreateDonor($conn, $values['donor_name'], $values['donor_email']);

        // The row is created as "Pending". payment.php flips it to
        // Success or Failed once the simulated gateway finishes.
        $ok = insertDonation(
            $conn,
            $donor_id,
            (int) $cause['cause_id'],
            $amount,
            $values['payment_method'],
            'Pending'
        );

        if ($ok) {
            // Post/Redirect/Get so a refresh cannot donate twice.
            $donation_id = $conn->insert_id;
            $_SESSION['pending_donation_id'] = $donation_id;
            $_SESSION['donor_message']       = $values['message'];
            header('Location: payment.php?donation_id=' . $donation_id);
            exit;
        }

        $errors['form'] = 'Something went wrong while saving your donation. Please try again.';
    }
}

/* Helper so the markup stays readable. */
function e($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $cause ? 'Donate to ' . e($cause['cause_name']) : 'Donate'; ?> — HopeFund</title>
  <meta name="description" content="Make a secure donation and support the cause you care about.">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/donate.css">
</head>
<body>

<!-- ============ HEADER ============ -->
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
      <a href="index.php#how">How it works</a>
      <a href="admin/report.php">Admin</a>
    </nav>
  </div>
</header>

<main class="donate-page donate-page--<?php echo e($accent); ?>">
  <div class="container">

    <nav class="crumbs" aria-label="Breadcrumb">
      <a href="index.php">Home</a>
      <span aria-hidden="true">/</span>
      <a href="index.php#causes">Causes</a>
      <span aria-hidden="true">/</span>
      <span class="crumbs-current"><?php echo $cause ? e($cause['cause_name']) : 'Donate'; ?></span>
    </nav>

<?php if (!$cause): ?>

    <!-- No valid cause in the URL -->
    <section class="notice notice--warn">
      <h1>Pick a cause first</h1>
      <p>We couldn't find that cause. Choose one of the four causes and the form will open with it selected.</p>
      <p><a class="btn" href="index.php#causes">Browse causes</a></p>
    </section>

<?php else: ?>

    <div class="donate-layout">

      <!-- ============ THE FORM ============ -->
      <section class="donate-form-wrap">
        <header class="donate-head">
          <p class="eyebrow">Donation form</p>
          <h1>Donate to <?php echo e($cause['cause_name']); ?></h1>
          <p class="lede"><?php echo e($cause['description']); ?></p>
        </header>

        <?php if (!empty($errors)): ?>
          <div class="alert alert--error" role="alert">
            <strong><?php echo count($errors) === 1 ? 'One field needs fixing' : count($errors) . ' fields need fixing'; ?></strong>
            <?php if (!empty($errors['form'])): ?>
              <p><?php echo e($errors['form']); ?></p>
            <?php else: ?>
              <p>Check the highlighted fields below and submit again.</p>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <form class="donate-form" method="post"
              action="donate.php?cause_id=<?php echo (int) $cause['cause_id']; ?>"
              novalidate id="donationForm">

          <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">

          <!-- Cause -->
          <div class="field">
            <label for="cause_id">Cause</label>
            <select id="cause_id" name="cause_id" class="input">
              <?php foreach ($all_causes as $c): ?>
                <option value="<?php echo (int) $c['cause_id']; ?>"
                  <?php echo ((int) $c['cause_id'] === (int) $cause['cause_id']) ? 'selected' : ''; ?>>
                  <?php echo e($c['cause_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <p class="hint">Changing this sends your donation to a different cause.</p>
          </div>

          <!-- Name -->
          <div class="field <?php echo isset($errors['donor_name']) ? 'has-error' : ''; ?>">
            <label for="donor_name">Full name <span class="req" aria-hidden="true">*</span></label>
            <input type="text" id="donor_name" name="donor_name" class="input"
                   value="<?php echo e($values['donor_name']); ?>"
                   maxlength="100" autocomplete="name" placeholder="e.g. Eiman Asmat"
                   aria-describedby="err-donor_name">
            <p class="error-text" id="err-donor_name"><?php echo e($errors['donor_name'] ?? ''); ?></p>
          </div>

          <!-- Email -->
          <div class="field <?php echo isset($errors['donor_email']) ? 'has-error' : ''; ?>">
            <label for="donor_email">Email address <span class="req" aria-hidden="true">*</span></label>
            <input type="email" id="donor_email" name="donor_email" class="input"
                   value="<?php echo e($values['donor_email']); ?>"
                   maxlength="150" autocomplete="email" placeholder="you@example.com"
                   aria-describedby="err-donor_email">
            <p class="error-text" id="err-donor_email"><?php echo e($errors['donor_email'] ?? ''); ?></p>
          </div>

          <!-- Amount -->
          <div class="field <?php echo isset($errors['amount']) ? 'has-error' : ''; ?>">
            <label for="amount">Amount (PKR) <span class="req" aria-hidden="true">*</span></label>

            <div class="amount-presets" role="group" aria-label="Suggested amounts">
              <?php foreach ([500, 1000, 2500, 5000] as $preset): ?>
                <button type="button" class="chip" data-amount="<?php echo $preset; ?>">
                  Rs. <?php echo number_format($preset); ?>
                </button>
              <?php endforeach; ?>
            </div>

            <div class="input-prefix">
              <span aria-hidden="true">Rs.</span>
              <input type="number" id="amount" name="amount" class="input"
                     value="<?php echo e($values['amount']); ?>"
                     min="100" max="1000000" step="1" inputmode="numeric"
                     placeholder="1000" aria-describedby="err-amount">
            </div>
            <p class="hint">Minimum Rs. 100.</p>
            <p class="error-text" id="err-amount"><?php echo e($errors['amount'] ?? ''); ?></p>
          </div>

          <!-- Payment method -->
          <div class="field <?php echo isset($errors['payment_method']) ? 'has-error' : ''; ?>">
            <label for="payment_method">Payment method <span class="req" aria-hidden="true">*</span></label>
            <select id="payment_method" name="payment_method" class="input" aria-describedby="err-payment_method">
              <option value="">Select a method…</option>
              <?php foreach ($payment_methods as $pm): ?>
                <option value="<?php echo e($pm); ?>"
                  <?php echo $values['payment_method'] === $pm ? 'selected' : ''; ?>>
                  <?php echo e($pm); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <p class="error-text" id="err-payment_method"><?php echo e($errors['payment_method'] ?? ''); ?></p>
          </div>

          <!-- Optional message -->
          <div class="field <?php echo isset($errors['message']) ? 'has-error' : ''; ?>">
            <label for="message">Message <span class="optional">(optional)</span></label>
            <textarea id="message" name="message" class="input" rows="3" maxlength="300"
                      placeholder="Anything you'd like us to know."
                      aria-describedby="err-message"><?php echo e($values['message']); ?></textarea>
            <p class="hint"><span id="msgCount">0</span>/300</p>
            <p class="error-text" id="err-message"><?php echo e($errors['message'] ?? ''); ?></p>
          </div>

          <button type="submit" class="btn btn-block">Continue to payment</button>

          <p class="fine-print">
            Payment is simulated for this academic project — no real money is transferred
            and no card details are collected.
          </p>
        </form>
      </section>

      <!-- ============ SIDEBAR ============ -->
      <aside class="donate-aside">
        <div class="summary-card">
          <h2>Your donation</h2>
          <dl class="summary-list">
            <div>
              <dt>Cause</dt>
              <dd id="sumCause"><?php echo e($cause['cause_name']); ?></dd>
            </div>
            <div>
              <dt>Amount</dt>
              <dd id="sumAmount">—</dd>
            </div>
            <div>
              <dt>Method</dt>
              <dd id="sumMethod">—</dd>
            </div>
          </dl>
          <p class="summary-note">100% of what you give goes to the cause you picked.</p>
        </div>

        <div class="trust-card">
          <h3>What happens next</h3>
          <ol class="trust-list">
            <li>Your donation is saved as <em>Pending</em>.</li>
            <li>You confirm it on the payment screen.</li>
            <li>You get a receipt with a reference number.</li>
          </ol>
        </div>
      </aside>

    </div>

<?php endif; ?>

  </div>
</main>

<!-- ============ FOOTER ============ -->
<footer class="site-footer">
  <div class="container footer-inner">
    <div class="footer-brand">
      <span class="brand">Hope<strong>Fund</strong></span>
      <p>An academic project — Online Donation System built with PHP &amp; MySQL.</p>
    </div>
    <nav class="footer-links" aria-label="Footer">
      <a href="index.php#causes">Causes</a>
      <a href="index.php#how">How it works</a>
      <a href="admin/report.php">Admin report</a>
    </nav>
  </div>
  <div class="container footer-bottom">
    <p>&copy; <?php echo date('Y'); ?> HopeFund. All donations shown are for demonstration purposes.</p>
  </div>
</footer>

<script src="js/donate.js"></script>
</body>
</html>
