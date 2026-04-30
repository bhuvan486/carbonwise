<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carbonwise — Carbon Footprint Calculator</title>
  <link rel="stylesheet" href="css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>

  <nav class="navbar">
    <div class="nav-inner">
      <a href="index.php" class="logo">
        <div class="logo-mark">◆</div>
        <span class="logo-name">Carbonwise</span>
      </a>
      <div class="nav-links">
        <a href="index.php" class="nav-link">About</a>
        <a href="calculate.php" class="nav-link active">Calculator</a>
        <a href="calculate.php" class="nav-cta">Get Started</a>
      </div>
    </div>
  </nav>

  <section class="hero-section">
    <div class="hero-inner">
      <div class="hero-badge">Free · No signup needed</div>
      <h1 class="hero-brand">Carbonwise</h1>
      <p class="hero-title">Measure your carbon <em>footprint in seconds.</em></p>
      <p class="hero-sub">
        Enter your monthly electricity, travel, and fuel usage.
        Get an instant breakdown, AI-powered summary, and a personalised 30-day reduction plan.
      </p>
      <div class="hero-actions">
        <a href="#calculator" class="btn-hero-primary">Get Started</a>
        <a href="index.php" class="btn-hero-outline">Learn More</a>
      </div>
    </div>
  </section>

  <div class="stats-bar">
    <div class="stats-inner">
      <div class="stat-item">
        <span class="stat-num">3</span>
        <span class="stat-lbl">Emission sources tracked</span>
      </div>
      <div class="stat-item">
        <span class="stat-num">167 kg</span>
        <span class="stat-lbl">India avg monthly CO₂</span>
      </div>
      <div class="stat-item">
        <span class="stat-num">AI</span>
        <span class="stat-lbl">Smart summary + 30-day plan</span>
      </div>
    </div>
  </div>

  <div class="page-body narrow" id="calculator" style="padding-top:3rem;">
    <span class="eyebrow">Carbon Calculator</span>
    <h2 class="section-title">Enter your monthly usage</h2>
    <p style="font-size:14px;color:var(--ink-3);margin-bottom:1.75rem;line-height:1.65;">
      Fill in your monthly figures below. We use verified emission factors to calculate your total CO₂ instantly.
    </p>

    <form action="result.php" method="POST" class="calc-form" id="carbonForm">

      <div class="input-group">
        <div class="input-header">
          <div class="input-icon">⚡</div>
          <div>
            <label class="input-label" for="electricity">Electricity Usage</label>
            <span class="input-desc">From your monthly electricity bill</span>
          </div>
        </div>
        <div class="input-row">
          <input type="number" id="electricity" name="electricity" placeholder="e.g. 150" min="0" step="0.1" required>
          <span class="unit">kWh</span>
        </div>
        <span class="factor-hint">Emission factor: 0.82 kg CO₂ per kWh · Source: India CEA 2023</span>
      </div>

      <div class="input-group">
        <div class="input-header">
          <div class="input-icon">🚗</div>
          <div>
            <label class="input-label" for="travel">Travel Distance</label>
            <span class="input-desc">Total kilometres driven this month</span>
          </div>
        </div>
        <div class="input-row">
          <input type="number" id="travel" name="travel" placeholder="e.g. 200" min="0" step="0.1" required>
          <span class="unit">km</span>
        </div>
        <span class="factor-hint">Emission factor: 0.21 kg CO₂ per km · Source: IPCC</span>
      </div>

      <div class="input-group">
        <div class="input-header">
          <div class="input-icon">🔥</div>
          <div>
            <label class="input-label" for="lpg">LPG / Fuel Usage</label>
            <span class="input-desc">Cooking gas or fuel consumed this month</span>
          </div>
        </div>
        <div class="input-row">
          <input type="number" id="lpg" name="lpg" placeholder="e.g. 10" min="0" step="0.1" required>
          <span class="unit">kg</span>
        </div>
        <span class="factor-hint">Emission factor: 2.98 kg CO₂ per kg · Source: IPCC</span>
      </div>

      <span id="live-preview"></span>
      <button type="submit" class="submit-btn" id="submitBtn">Calculate My Footprint →</button>

    </form>
  </div>

  <footer class="site-footer">
    <p>Carbonwise &nbsp;·&nbsp; Emission factors: IPCC &amp; India CEA 2023 &nbsp;·&nbsp; HTML · CSS · PHP · Gemini AI</p>
  </footer>

  <script src="js/main.js"></script>
</body>
</html>