<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calculator — Carbonwise</title>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800;900&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --lime: #c8e63c; --dark: #1a1a1a; --darker: #111111;
      --mid: #2a2a2a; --mid2: #222222; --border: #333333;
      --white: #ffffff; --muted: #999999; --light: #e8e8e8;
      --red: #e74c3c;
    }
    html { font-size: 16px; -webkit-font-smoothing: antialiased; }
    body { font-family: 'Barlow', sans-serif; background: var(--darker); color: var(--white); min-height: 100vh; }

    /* NAVBAR */
    .navbar {
      position: fixed; top: 0; left: 0; right: 0; z-index: 100;
      background: var(--white); height: 68px;
      display: flex; align-items: center; padding: 0 2rem;
      justify-content: space-between; border-bottom: 2px solid var(--lime);
    }
    .nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .nav-logo-hex {
      width: 36px; height: 36px; background: var(--lime);
      clip-path: polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; font-weight: 800; color: var(--dark);
    }
    .nav-logo-text {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 22px; font-weight: 800; color: var(--dark);
      letter-spacing: 1px; text-transform: uppercase;
    }
    .nav-links { display: flex; align-items: center; gap: 2rem; }
    .nav-link {
      font-size: 13px; font-weight: 600; color: var(--dark);
      text-decoration: none; letter-spacing: 0.5px; text-transform: uppercase;
      transition: color 0.15s;
    }
    .nav-link:hover { color: #666; }
    .nav-link.active { border-bottom: 2px solid var(--lime); padding-bottom: 2px; }
    .nav-cta {
      background: var(--lime); color: var(--dark);
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 14px; font-weight: 800; letter-spacing: 1px;
      text-transform: uppercase; padding: 10px 22px; text-decoration: none;
      transition: background 0.15s;
    }
    .nav-cta:hover { background: #b5d030; }

    /* HERO BAND */
    .page-hero {
      padding-top: 68px;
      background: linear-gradient(160deg, #1c1c1c 0%, #222 50%, #1a1a1a 100%);
      padding-bottom: 60px; padding-left: 2rem; padding-right: 2rem;
      border-bottom: 1px solid var(--border);
    }
    .page-hero-inner { max-width: 1200px; margin: 0 auto; padding-top: 60px; }
    .page-eyebrow {
      display: inline-flex; align-items: center; gap: 8px;
      font-size: 11px; font-weight: 600; letter-spacing: 0.15em;
      text-transform: uppercase; color: var(--lime); margin-bottom: 16px;
    }
    .page-eyebrow::before { content: ''; width: 28px; height: 2px; background: var(--lime); }
    .page-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: clamp(52px, 8vw, 90px);
      font-weight: 900; text-transform: uppercase;
      color: var(--white); line-height: 0.92; letter-spacing: -1px; margin-bottom: 16px;
    }
    .page-title .lime { color: var(--lime); }
    .page-desc { font-size: 16px; color: rgba(255,255,255,0.65); line-height: 1.65; max-width: 560px; }

    /* CONTENT */
    .page-body { max-width: 1200px; margin: 0 auto; padding: 60px 2rem 80px; }
    .page-body.narrow { max-width: 760px; }

    /* FACTOR BAR */
    .factor-bar { display: grid; grid-template-columns: repeat(3,1fr); gap: 2px; margin-bottom: 50px; }
    .factor-item {
      background: var(--mid); padding: 1.5rem 1.75rem;
      border-left: 3px solid var(--lime);
    }
    .factor-num {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 32px; font-weight: 900; color: var(--lime); line-height: 1;
    }
    .factor-label { font-size: 12px; color: var(--muted); margin-top: 4px; }

    /* FORM */
    .form-section-label {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 11px; font-weight: 700; letter-spacing: 0.15em;
      text-transform: uppercase; color: var(--lime); margin-bottom: 24px;
      display: flex; align-items: center; gap: 10px;
    }
    .form-section-label::after { content: ''; flex: 1; height: 1px; background: var(--border); }

    .input-group { margin-bottom: 2px; }
    .input-block {
      background: var(--mid); padding: 1.5rem 1.75rem;
      border-left: 3px solid transparent;
      transition: border-color 0.2s;
    }
    .input-block:focus-within { border-left-color: var(--lime); }
    .input-top { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
    .input-icon-box {
      width: 42px; height: 42px; background: var(--dark);
      border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; flex-shrink: 0;
    }
    .input-label-group {}
    .input-label {
      display: block; font-family: 'Barlow Condensed', sans-serif;
      font-size: 15px; font-weight: 700; text-transform: uppercase;
      letter-spacing: 0.5px; color: var(--white); margin-bottom: 2px;
    }
    .input-hint { font-size: 12px; color: var(--muted); }
    .input-row { display: flex; align-items: center; gap: 10px; }
    input[type="number"] {
      flex: 1; height: 48px; background: var(--darker);
      border: 1px solid var(--border); border-radius: 0;
      padding: 0 16px; font-family: 'Barlow', sans-serif;
      font-size: 16px; color: var(--white); outline: none;
      transition: border-color 0.2s; -moz-appearance: textfield;
    }
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; }
    input[type="number"]:focus { border-color: var(--lime); }
    input[type="number"]::placeholder { color: #555; }
    .unit-tag {
      background: var(--dark); border: 1px solid var(--border);
      padding: 0 16px; height: 48px; display: flex; align-items: center;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 13px; font-weight: 700; color: var(--lime);
      letter-spacing: 0.5px; text-transform: uppercase; white-space: nowrap;
    }
    .factor-tag {
      font-size: 11px; color: var(--muted); margin-top: 8px;
      display: flex; align-items: center; gap: 6px;
    }
    .factor-tag::before { content: ''; width: 12px; height: 1px; background: var(--border); }
    .input-error { font-size: 12px; color: var(--red); margin-top: 6px; display: block; }

    /* LIVE PREVIEW */
    #live-preview {
      background: var(--mid); border-left: 3px solid var(--lime);
      padding: 1rem 1.5rem; margin-bottom: 2px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 18px; font-weight: 700; color: var(--lime);
      letter-spacing: 0.5px; min-height: 54px;
      display: flex; align-items: center;
    }
    #live-preview:empty { display: none; }

    /* SUBMIT */
    .submit-btn {
      width: 100%; height: 60px; background: var(--lime); color: var(--dark);
      border: none; font-family: 'Barlow Condensed', sans-serif;
      font-size: 18px; font-weight: 800; letter-spacing: 1.5px;
      text-transform: uppercase; cursor: pointer;
      transition: background 0.15s, transform 0.1s;
      display: flex; align-items: center; justify-content: center; gap: 10px;
    }
    .submit-btn:hover { background: #b5d030; transform: translateY(-1px); }

    /* FOOTER */
    .footer {
      background: var(--darker); color: var(--muted);
      padding: 24px 2rem; text-align: center; font-size: 12px;
      border-top: 1px solid var(--border);
    }
    .footer span { color: var(--lime); }

    @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    .input-block { animation: fadeUp 0.35s ease both; }
    .input-block:nth-child(1){animation-delay:0.05s;}
    .input-block:nth-child(2){animation-delay:0.1s;}
    .input-block:nth-child(3){animation-delay:0.15s;}
    @media(max-width:600px){.factor-bar{grid-template-columns:1fr;} .nav-links{display:none;}}
  </style>
</head>
<body>

  <nav class="navbar">
    <a href="about.php" class="nav-logo">
      <div class="nav-logo-hex">C</div>
      <span class="nav-logo-text">Carbonwise</span>
    </a>
    <div class="nav-links">
      <a href="about.php" class="nav-link">Home</a>
      <a href="about.php#about" class="nav-link">About</a>
      <a href="index.php" class="nav-link active">Calculator</a>
    </div>
    <a href="index.php" class="nav-cta">Get Started</a>
  </nav>

  <div class="page-hero">
    <div class="page-hero-inner">
      <div class="page-eyebrow">Step 2 — Enter your data</div>
      <h1 class="page-title">Carbon<br><span class="lime">Calculator</span></h1>
      <p class="page-desc">Enter your monthly electricity, travel, and fuel figures. We apply verified emission factors and calculate your exact CO₂ footprint instantly.</p>
    </div>
  </div>

  <div class="page-body narrow">

    <div class="factor-bar">
      <div class="factor-item">
        <div class="factor-num">0.82</div>
        <div class="factor-label">kg CO₂ per kWh · Electricity</div>
      </div>
      <div class="factor-item">
        <div class="factor-num">0.21</div>
        <div class="factor-label">kg CO₂ per km · Travel</div>
      </div>
      <div class="factor-item">
        <div class="factor-num">2.98</div>
        <div class="factor-label">kg CO₂ per kg · LPG / Fuel</div>
      </div>
    </div>

    <div class="form-section-label">Monthly usage inputs</div>

    <form action="result.php" method="POST">
      <div class="input-group">
        <div class="input-block">
          <div class="input-top">
            <div class="input-icon-box">⚡</div>
            <div class="input-label-group">
              <label class="input-label" for="electricity">Electricity Usage</label>
              <span class="input-hint">From your monthly electricity bill</span>
            </div>
          </div>
          <div class="input-row">
            <input type="number" id="electricity" name="electricity" placeholder="e.g. 150" min="0" step="0.1" required>
            <div class="unit-tag">kWh</div>
          </div>
          <div class="factor-tag">Factor: 0.82 kg CO₂ per kWh · Source: India CEA 2023</div>
        </div>
      </div>

      <div class="input-group">
        <div class="input-block">
          <div class="input-top">
            <div class="input-icon-box">🚗</div>
            <div class="input-label-group">
              <label class="input-label" for="travel">Travel Distance</label>
              <span class="input-hint">Total kilometres driven this month</span>
            </div>
          </div>
          <div class="input-row">
            <input type="number" id="travel" name="travel" placeholder="e.g. 200" min="0" step="0.1" required>
            <div class="unit-tag">km</div>
          </div>
          <div class="factor-tag">Factor: 0.21 kg CO₂ per km · Source: IPCC</div>
        </div>
      </div>

      <div class="input-group">
        <div class="input-block">
          <div class="input-top">
            <div class="input-icon-box">🔥</div>
            <div class="input-label-group">
              <label class="input-label" for="lpg">LPG / Fuel Usage</label>
              <span class="input-hint">Cooking gas or fuel consumed this month</span>
            </div>
          </div>
          <div class="input-row">
            <input type="number" id="lpg" name="lpg" placeholder="e.g. 10" min="0" step="0.1" required>
            <div class="unit-tag">kg</div>
          </div>
          <div class="factor-tag">Factor: 2.98 kg CO₂ per kg · Source: IPCC</div>
        </div>
      </div>

      <div id="live-preview"></div>
      <button type="submit" class="submit-btn">Calculate My Footprint →</button>
    </form>

  </div>

  <footer class="footer">
    <p>Carbonwise &nbsp;·&nbsp; Emission factors: IPCC &amp; India CEA 2023 &nbsp;·&nbsp; <span>HTML · CSS · PHP · XAMPP</span></p>
  </footer>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const factors = { electricity:0.82, travel:0.21, lpg:2.98 };
    const inputs  = document.querySelectorAll('input[type="number"]');

    function updatePreview() {
      let total = 0;
      inputs.forEach(i => { total += (parseFloat(i.value)||0) * (factors[i.name]||0); });
      const p = document.getElementById('live-preview');
      p.textContent = total > 0 ? `Estimated: ~${total.toFixed(1)} kg CO₂` : '';
    }

    inputs.forEach(input => {
      input.addEventListener('input', () => {
        if (parseFloat(input.value) < 0) input.value = 0;
        clearError(input); updatePreview();
      });
    });

    const form = document.querySelector('form');
    form.addEventListener('submit', e => {
      let valid = true;
      inputs.forEach(input => {
        const v = input.value.trim();
        if (v===''||isNaN(parseFloat(v))) { showError(input,'Please enter a valid number.'); valid=false; }
        else if (parseFloat(v)<0)          { showError(input,'Value cannot be negative.');   valid=false; }
        else if (parseFloat(v)>100000)     { showError(input,'Value seems too high.');        valid=false; }
      });
      if (!valid) { e.preventDefault(); return; }
      const btn = form.querySelector('.submit-btn');
      btn.textContent = 'Calculating… ⏳'; btn.disabled = true;
    });

    function showError(input, msg) {
      clearError(input);
      input.style.borderColor = '#e74c3c';
      const err = document.createElement('span');
      err.className='input-error'; err.textContent=msg;
      input.closest('.input-row').after(err);
    }
    function clearError(input) {
      input.style.borderColor = '';
      const next = input.closest('.input-row')?.nextElementSibling;
      if (next?.classList.contains('input-error')) next.remove();
    }
  });
  </script>
</body>
</html>