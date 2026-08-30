<?php
/**
 * Online Donation System — Homepage / Category Selection
 * Part 2 (Member 1 — Frontend only)
 *
 * No database or PHP logic here by design. The four cause IDs below are
 * hard-coded to match the seed rows Member 3 inserted into the `causes` table:
 *   1 = Education, 2 = Health, 3 = Food, 4 = Emergency Relief
 * Each card links to donate.php?cause_id=N (Member 2's form).
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HopeFund — Online Donation System</title>
  <meta name="description" content="Choose a cause and donate securely. Education, health, food and emergency relief.">
  <link rel="stylesheet" href="css/style.css">
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
      <a href="index.php" class="is-active">Home</a>
      <a href="#causes">Causes</a>
      <a href="#how">How it works</a>
      <a href="admin/report.php">Admin</a>
      <a href="#causes" class="btn btn-sm">Donate</a>
    </nav>
  </div>
</header>

<!-- ============ HERO ============ -->
<section class="hero">
  <div class="container hero-inner">
    <div class="hero-copy">
      <p class="eyebrow">Every contribution counts</p>
      <h1>Give to a cause that<br><em>changes something.</em></h1>
      <p class="lede">
        Pick a category, choose an amount, and your donation goes straight to the
        people who need it. No sign-up. No hidden cuts.
      </p>
      <div class="hero-actions">
        <a href="#causes" class="btn">Choose a cause</a>
        <a href="#how" class="btn btn-ghost">See how it works</a>
      </div>
    </div>

    <div class="hero-stats" aria-label="Impact so far">
      <div class="stat">
        <span class="stat-num">4</span>
        <span class="stat-label">Active causes</span>
      </div>
      <div class="stat">
        <span class="stat-num">100%</span>
        <span class="stat-label">Goes to the cause</span>
      </div>
      <div class="stat">
        <span class="stat-num">24/7</span>
        <span class="stat-label">Donate anytime</span>
      </div>
    </div>
  </div>
</section>

<!-- ============ CATEGORY SELECTION (the core of Part 2) ============ -->
<main>
<section class="section" id="causes">
  <div class="container">
    <header class="section-head">
      <h2>Choose where your donation goes</h2>
      <p>Four causes, each funded and reported on separately.</p>
    </header>

    <div class="cause-grid">

      <!-- Cause 1 — Education -->
      <article class="cause-card cause-card--education">
        <div class="cause-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M12 4 2 9l10 5 8-4v6"/>
            <path d="M6 11.5V16c0 1.4 2.7 2.5 6 2.5s6-1.1 6-2.5v-4.5"/>
          </svg>
        </div>
        <h3>Education</h3>
        <p>School fees, books, uniforms and tuition for children who would otherwise drop out.</p>
        <ul class="cause-meta">
          <li>Books &amp; supplies</li>
          <li>Tuition support</li>
        </ul>
        <a class="cause-btn" href="donate.php?cause_id=1">
          Donate to Education
          <svg class="arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h13M13 6l6 6-6 6"/></svg>
        </a>
      </article>

      <!-- Cause 2 — Health -->
      <article class="cause-card cause-card--health">
        <div class="cause-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M12 20.5S3.5 15 3.5 9.2A4.7 4.7 0 0 1 12 6.4a4.7 4.7 0 0 1 8.5 2.8c0 5.8-8.5 11.3-8.5 11.3Z"/>
            <path d="M12 9.8v3.6M10.2 11.6h3.6"/>
          </svg>
        </div>
        <h3>Health</h3>
        <p>Medicines, treatment costs and medical camps for families who cannot afford care.</p>
        <ul class="cause-meta">
          <li>Medicine</li>
          <li>Treatment costs</li>
        </ul>
        <a class="cause-btn" href="donate.php?cause_id=2">
          Donate to Health
          <svg class="arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h13M13 6l6 6-6 6"/></svg>
        </a>
      </article>

      <!-- Cause 3 — Food -->
      <article class="cause-card cause-card--food">
        <div class="cause-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M7 3v8a2.5 2.5 0 0 0 5 0V3"/>
            <path d="M9.5 11v10"/>
            <path d="M17.5 3c-1.4 1.2-2 3-2 5.5 0 1.6.7 2.5 2 2.5V21"/>
          </svg>
        </div>
        <h3>Food</h3>
        <p>Monthly ration packs and hot meals for households living below the poverty line.</p>
        <ul class="cause-meta">
          <li>Ration packs</li>
          <li>Daily meals</li>
        </ul>
        <a class="cause-btn" href="donate.php?cause_id=3">
          Donate to Food
          <svg class="arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h13M13 6l6 6-6 6"/></svg>
        </a>
      </article>

      <!-- Cause 4 — Emergency Relief -->
      <article class="cause-card cause-card--relief">
        <div class="cause-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M12 3.5 3.5 18.5h17L12 3.5Z"/>
            <path d="M12 10v3.5M12 16.2v.1"/>
          </svg>
        </div>
        <h3>Emergency Relief</h3>
        <p>Rapid response after floods, earthquakes and displacement — shelter, water, first aid.</p>
        <ul class="cause-meta">
          <li>Shelter &amp; water</li>
          <li>Rapid response</li>
        </ul>
        <a class="cause-btn" href="donate.php?cause_id=4">
          Donate to Relief
          <svg class="arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h13M13 6l6 6-6 6"/></svg>
        </a>
      </article>

    </div>
  </div>
</section>

<!-- ============ HOW IT WORKS ============ -->
<section class="section section--alt" id="how">
  <div class="container">
    <header class="section-head">
      <h2>How it works</h2>
      <p>Three steps, under a minute.</p>
    </header>

    <ol class="steps">
      <li class="step">
        <span class="step-num">1</span>
        <h3>Pick a cause</h3>
        <p>Choose the category you want to support from the four above.</p>
      </li>
      <li class="step">
        <span class="step-num">2</span>
        <h3>Fill the form</h3>
        <p>Your name, email, the amount, and how you'd like to pay.</p>
      </li>
      <li class="step">
        <span class="step-num">3</span>
        <h3>Get your confirmation</h3>
        <p>Your donation is recorded and you land on a confirmation page.</p>
      </li>
    </ol>
  </div>
</section>
</main>

<!-- ============ FOOTER ============ -->
<footer class="site-footer">
  <div class="container footer-inner">
    <div class="footer-brand">
      <span class="brand">Hope<strong>Fund</strong></span>
      <p>An academic project — Online Donation System built with PHP &amp; MySQL.</p>
    </div>
    <nav class="footer-links" aria-label="Footer">
      <a href="#causes">Causes</a>
      <a href="#how">How it works</a>
      <a href="admin/report.php">Admin report</a>
    </nav>
  </div>
  <div class="container footer-bottom">
    <p>&copy; <?php echo date('Y'); ?> HopeFund. All donations shown are for demonstration purposes.</p>
  </div>
</footer>

</body>
</html>
