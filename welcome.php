<?php
/**
 * HopeFund — Welcome / Role Selection
 * First page visitors see. They choose Donor or Admin/Organization.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Welcome — HopeFund</title>
  <meta name="description" content="Choose how you'd like to continue to HopeFund.">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .welcome-main {
        min-height: calc(100vh - 90px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 1.25rem;

        background:
            radial-gradient(800px 320px at 10% -20%, var(--brand-lt), transparent 65%),
            radial-gradient(650px 300px at 95% 0%, #f2eefb, transparent 60%);
    }

    .welcome-wrap {
        width: 100%;
        max-width: 780px;
        text-align: center;
    }

    .welcome-wrap .eyebrow {
        display: inline-block;
        margin-bottom: .8rem;
    }

    .welcome-wrap h1 {
        font-size: clamp(1.9rem, 4.5vw, 2.6rem);
        letter-spacing: -.02em;
        margin-bottom: .6rem;
    }

    .welcome-wrap > p {
        color: var(--ink-3);
        max-width: 46ch;
        margin: 0 auto 2.5rem;
    }

    .role-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.25rem;
        text-align: left;
    }

    .role-card {
        display: flex;
        flex-direction: column;
        gap: .9rem;

        padding: 1.75rem;

        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        text-decoration: none;
        color: inherit;

        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .role-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--brand);
    }

    .role-icon {
        display: grid;
        place-items: center;
        width: 52px;
        height: 52px;
        border-radius: var(--radius-sm);
        background: var(--brand-lt);
        color: var(--brand);
    }

    .role-icon svg {
        width: 26px;
        height: 26px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .role-card h2 {
        font-size: 1.15rem;
        margin: 0;
    }

    .role-card p {
        color: var(--ink-3);
        font-size: .9rem;
        margin: 0;
    }

    .role-cta {
        margin-top: auto;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        color: var(--brand);
        font-weight: 700;
        font-size: .88rem;
    }

    .role-cta svg {
        width: 16px;
        height: 16px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
    }

    @media (max-width: 620px) {
        .role-grid {
            grid-template-columns: 1fr;
        }
    }
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

<main class="welcome-main">
  <div class="welcome-wrap">
    <p class="eyebrow">Welcome to HopeFund</p>
    <h1>How would you like to continue?</h1>
    <p>Choose the option that fits you — you can always come back and switch later.</p>

    <div class="role-grid">

      <!-- Donor -->
      <a class="role-card" href="index.php">
        <div class="role-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <path d="M12 20.5S3.5 15 3.5 9.2A4.7 4.7 0 0 1 12 6.4a4.7 4.7 0 0 1 8.5 2.8c0 5.8-8.5 11.3-8.5 11.3Z"/>
          </svg>
        </div>
        <h2>Continue as a Donor</h2>
        <p>Browse causes, choose an amount, and donate securely in a few clicks.</p>
        <span class="role-cta">
          Continue
          <svg viewBox="0 0 24 24"><path d="M5 12h13M13 6l6 6-6 6"/></svg>
        </span>
      </a>

      <!-- Admin -->
      <a class="role-card" href="register_admin.php">
        <div class="role-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24">
            <rect x="4" y="10" width="16" height="10" rx="1.5"/>
            <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
          </svg>
        </div>
        <h2>Admin / Organization</h2>
        <p>Sign in to view donation reports and manage HopeFund data.</p>
        <span class="role-cta">
          Sign in
          <svg viewBox="0 0 24 24"><path d="M5 12h13M13 6l6 6-6 6"/></svg>
        </span>
      </a>

    </div>
  </div>
</main>

</body>
</html>