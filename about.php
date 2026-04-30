<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Carbonwise — Carbon Footprint Calculator</title>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800;900&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --lime:    #c8e63c;
      --dark:    #1a1a1a;
      --darker:  #111111;
      --mid:     #2a2a2a;
      --border:  #333333;
      --white:   #ffffff;
      --muted:   #999999;
      --light:   #e8e8e8;
    }

    html { font-size: 16px; -webkit-font-smoothing: antialiased; }
    body { font-family: 'Barlow', sans-serif; background: var(--darker); color: var(--white); min-height: 100vh; }

    /* ── NAVBAR ── */
    .navbar {
      position: fixed; top: 0; left: 0; right: 0; z-index: 100;
      background: var(--white);
      height: 68px;
      display: flex; align-items: center;
      padding: 0 2rem;
      justify-content: space-between;
      border-bottom: 2px solid var(--lime);
    }
    .nav-logo {
      display: flex; align-items: center; gap: 10px;
      text-decoration: none;
    }
    .nav-logo-hex {
      width: 36px; height: 36px;
      background: var(--lime);
      clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; font-weight: 800; color: var(--dark);
    }
    .nav-logo-text {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 22px; font-weight: 800;
      color: var(--dark); letter-spacing: 1px; text-transform: uppercase;
    }
    .nav-links { display: flex; align-items: center; gap: 2rem; }
    .nav-link {
      font-family: 'Barlow', sans-serif;
      font-size: 13px; font-weight: 600;
      color: var(--dark); text-decoration: none;
      letter-spacing: 0.5px; text-transform: uppercase;
      transition: color 0.15s;
    }
    .nav-link:hover { color: #666; }
    .nav-link.active { color: var(--dark); border-bottom: 2px solid var(--lime); padding-bottom: 2px; }
    .nav-subscribe {
      background: var(--lime);
      color: var(--dark);
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 14px; font-weight: 800;
      letter-spacing: 1px; text-transform: uppercase;
      padding: 10px 22px; text-decoration: none;
      transition: background 0.15s;
    }
    .nav-subscribe:hover { background: #b5d030; }

    /* ── HERO ── */
    .hero {
      min-height: 100vh;
      padding-top: 68px;
      background: linear-gradient(160deg, #1c1c1c 0%, #222 40%, #1a1a1a 100%);
      position: relative;
      overflow: hidden;
      display: flex; align-items: center;
    }
    .hero::before {
      content: '';
      position: absolute; inset: 0;
      background:
        radial-gradient(ellipse 60% 50% at 80% 60%, rgba(200,230,60,0.06) 0%, transparent 70%),
        radial-gradient(ellipse 40% 60% at 20% 30%, rgba(255,255,255,0.02) 0%, transparent 60%);
    }
    /* Diagonal accent line */
    .hero::after {
      content: '';
      position: absolute;
      top: 0; right: 0;
      width: 3px; height: 100%;
      background: linear-gradient(to bottom, var(--lime), transparent);
      opacity: 0.6;
    }
    .hero-inner {
      position: relative;
      max-width: 1200px; margin: 0 auto; padding: 0 2rem;
      width: 100%;
      display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;
    }
    .hero-left { padding: 4rem 0; }
    .hero-eyebrow {
      display: inline-flex; align-items: center; gap: 8px;
      font-size: 11px; font-weight: 600; letter-spacing: 0.15em;
      text-transform: uppercase; color: var(--lime);
      margin-bottom: 1.5rem;
    }
    .hero-eyebrow::before {
      content: '';
      width: 28px; height: 2px; background: var(--lime);
    }
    .hero-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: clamp(64px, 9vw, 110px);
      font-weight: 900;
      line-height: 0.92;
      letter-spacing: -1px;
      text-transform: uppercase;
      color: var(--white);
      margin-bottom: 1.5rem;
    }
    .hero-title .accent { color: var(--lime); }
    .hero-desc {
      font-size: 17px; font-weight: 400;
      color: rgba(255,255,255,0.75);
      line-height: 1.7;
      max-width: 440px;
      margin-bottom: 2.5rem;
    }
    .hero-desc strong { color: var(--white); font-weight: 600; }
    .hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }
    .btn-primary-dark {
      background: var(--lime); color: var(--dark);
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 14px; font-weight: 800;
      letter-spacing: 1px; text-transform: uppercase;
      padding: 14px 30px; text-decoration: none;
      display: inline-block; transition: background 0.15s, transform 0.1s;
    }
    .btn-primary-dark:hover { background: #b5d030; transform: translateY(-1px); }
    .btn-outline-dark {
      background: transparent; color: var(--white);
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 14px; font-weight: 800;
      letter-spacing: 1px; text-transform: uppercase;
      padding: 13px 30px; text-decoration: none;
      border: 1px solid rgba(255,255,255,0.3); display: inline-block;
      transition: border-color 0.15s, color 0.15s;
    }
    .btn-outline-dark:hover { border-color: var(--lime); color: var(--lime); }

    /* Right side stats panel */
    .hero-right {
      display: flex; flex-direction: column; gap: 2px;
      padding: 4rem 0;
    }
    .stat-block {
      background: var(--mid);
      border-left: 3px solid var(--lime);
      padding: 1.5rem 1.75rem;
      animation: fadeUp 0.5s ease both;
    }
    .stat-block:nth-child(1) { animation-delay: 0.1s; }
    .stat-block:nth-child(2) { animation-delay: 0.2s; }
    .stat-block:nth-child(3) { animation-delay: 0.3s; }
    .stat-block:nth-child(4) { animation-delay: 0.4s; }
    .stat-block-num {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 36px; font-weight: 900; color: var(--lime);
      line-height: 1; margin-bottom: 4px;
    }
    .stat-block-label { font-size: 13px; color: rgba(255,255,255,0.65); font-weight: 400; }

    /* ── ABOUT BAND ── */
    .about-band {
      background: var(--dark);
      padding: 80px 2rem;
      border-top: 1px solid var(--border);
    }
    .about-band-inner { max-width: 1200px; margin: 0 auto; }
    .section-tag {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 11px; font-weight: 700;
      letter-spacing: 0.15em; text-transform: uppercase;
      color: var(--lime); margin-bottom: 12px; display: block;
    }
    .section-heading {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: clamp(36px, 5vw, 54px);
      font-weight: 900; text-transform: uppercase;
      color: var(--white); line-height: 1.05;
      margin-bottom: 2.5rem; letter-spacing: -0.5px;
    }
    .section-heading .lime { color: var(--lime); }

    /* Feature cards */
    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 2px;
      margin-bottom: 4rem;
    }
    .feature-card {
      background: var(--mid);
      padding: 2rem;
      border-bottom: 2px solid transparent;
      transition: border-color 0.2s;
      animation: fadeUp 0.4s ease both;
    }
    .feature-card:hover { border-bottom-color: var(--lime); }
    .feature-card:nth-child(1){animation-delay:0.05s;}
    .feature-card:nth-child(2){animation-delay:0.1s;}
    .feature-card:nth-child(3){animation-delay:0.15s;}
    .feature-card:nth-child(4){animation-delay:0.2s;}
    .feature-icon { font-size: 28px; margin-bottom: 14px; display: block; }
    .feature-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 18px; font-weight: 700; text-transform: uppercase;
      color: var(--white); margin-bottom: 8px; letter-spacing: 0.5px;
    }
    .feature-text { font-size: 13px; color: var(--muted); line-height: 1.7; }
    .feature-text strong { color: var(--light); font-weight: 500; }

    /* ── FLOW SECTION ── */
    .flow-section {
      background: var(--darker); padding: 60px 2rem;
      border-top: 1px solid var(--border);
    }
    .flow-inner { max-width: 1200px; margin: 0 auto; }
    .flow-steps {
      display: flex; align-items: center; gap: 0;
      flex-wrap: wrap; margin-top: 2rem;
    }
    .flow-step-block {
      flex: 1; min-width: 200px;
      background: var(--mid);
      padding: 2rem 1.75rem;
      position: relative;
    }
    .flow-step-block::after {
      content: '→';
      position: absolute; right: -16px; top: 50%;
      transform: translateY(-50%);
      font-size: 20px; color: var(--lime); z-index: 1;
      font-weight: 700;
    }
    .flow-step-block:last-child::after { display: none; }
    .flow-step-block.active-step { background: var(--lime); }
    .flow-step-num {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 42px; font-weight: 900; line-height: 1;
      color: rgba(255,255,255,0.1); margin-bottom: 6px;
    }
    .flow-step-block.active-step .flow-step-num { color: rgba(0,0,0,0.15); }
    .flow-step-label {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 16px; font-weight: 700; text-transform: uppercase;
      color: var(--white); letter-spacing: 0.5px;
    }
    .flow-step-block.active-step .flow-step-label { color: var(--dark); }
    .flow-step-desc { font-size: 12px; color: var(--muted); margin-top: 4px; }
    .flow-step-block.active-step .flow-step-desc { color: rgba(0,0,0,0.6); }

    /* ── TECH SECTION ── */
    .tech-section {
      background: var(--dark); padding: 60px 2rem;
      border-top: 1px solid var(--border);
    }
    .tech-inner { max-width: 1200px; margin: 0 auto; }
    .tech-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2px; margin-top: 2rem; }
    @media(max-width:600px){.tech-grid{grid-template-columns:1fr;}}
    .tech-row {
      display: flex; align-items: center; gap: 1rem;
      background: var(--mid); padding: 1rem 1.5rem;
    }
    .tech-tag {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 11px; font-weight: 700; text-transform: uppercase;
      letter-spacing: 0.08em; color: var(--lime);
      min-width: 80px;
    }
    .tech-val { font-size: 13px; color: var(--light); }

    /* ── VIVA BAND ── */
    .viva-band {
      background: var(--lime); padding: 40px 2rem;
    }
    .viva-inner {
      max-width: 1200px; margin: 0 auto;
      display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;
    }
    .viva-label {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 13px; font-weight: 800; text-transform: uppercase;
      letter-spacing: 0.1em; color: var(--dark); white-space: nowrap;
    }
    .viva-text {
      font-size: 15px; font-style: italic; color: var(--dark);
      line-height: 1.6; flex: 1;
    }

    /* ── CTA BAND ── */
    .cta-band {
      background: var(--mid); padding: 80px 2rem;
      text-align: center; border-top: 1px solid var(--border);
    }
    .cta-band h2 {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: clamp(36px, 6vw, 60px);
      font-weight: 900; text-transform: uppercase;
      color: var(--white); margin-bottom: 12px;
    }
    .cta-band h2 span { color: var(--lime); }
    .cta-band p { font-size: 15px; color: var(--muted); margin-bottom: 2rem; }

    /* ── FOOTER ── */
    .footer {
      background: var(--darker); color: var(--muted);
      padding: 24px 2rem; text-align: center; font-size: 12px;
      border-top: 1px solid var(--border);
    }
    .footer span { color: var(--lime); }

    /* ── ANIMATIONS ── */
    @keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
    .hero-left { animation: fadeUp 0.6s ease both; }
    .hero-right { animation: fadeUp 0.6s 0.2s ease both; }

    @media(max-width:768px){
      .hero-inner { grid-template-columns: 1fr; }
      .hero-right { display: none; }
      .navbar .nav-links { display: none; }
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar">
    <a href="about.php" class="nav-logo">
      <div class="nav-logo-hex">C</div>
      <span class="nav-logo-text">Carbonwise</span>
    </a>
    <div class="nav-links">
      <a href="about.php" class="nav-link active">Home</a>
      <a href="about.php#about" class="nav-link">About</a>
      <a href="index.php" class="nav-link">Calculator</a>
      <a href="result.php" class="nav-link">Results</a>
    </div>
    <a href="index.php" class="nav-subscribe">Get Started</a>
  </nav>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-inner">
      <div class="hero-left">
        <div class="hero-eyebrow">Carbon Footprint Calculator</div>
        <h1 class="hero-title">
          Carbon<br>
          <span class="accent">Wise</span>
        </h1>
        <p class="hero-desc">
          A web-based system that calculates your <strong>monthly CO₂ emissions</strong>
          from electricity, travel, and fuel — then shows you exactly how to reduce them.
        </p>
        <div class="hero-actions">
          <a href="index.php" class="btn-primary-dark">Get Started</a>
          <a href="#about" class="btn-outline-dark">Learn More</a>
        </div>
      </div>

      <div class="hero-right">
        <div class="stat-block">
          <div class="stat-block-num">0.82</div>
          <div class="stat-block-label">kg CO₂ per kWh · Electricity factor</div>
        </div>
        <div class="stat-block">
          <div class="stat-block-num">0.21</div>
          <div class="stat-block-label">kg CO₂ per km · Travel factor</div>
        </div>
        <div class="stat-block">
          <div class="stat-block-num">2.98</div>
          <div class="stat-block-label">kg CO₂ per kg · LPG factor</div>
        </div>
        <div class="stat-block">
          <div class="stat-block-num">167 kg</div>
          <div class="stat-block-label">India average monthly carbon footprint</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ABOUT SECTION -->
  <section class="about-band" id="about">
    <div class="about-band-inner">
      <span class="section-tag">About this project</span>
      <h2 class="section-heading">What is <span class="lime">Carbonwise?</span></h2>

      <div class="features-grid">
        <div class="feature-card">
          <span class="feature-icon">🌍</span>
          <div class="feature-title">Why we built this</div>
          <p class="feature-text">Most people don't know how much CO₂ their daily habits produce. Carbonwise makes that invisible impact visible — so you can act on it.</p>
        </div>
        <div class="feature-card">
          <span class="feature-icon">📐</span>
          <div class="feature-title">How it works</div>
          <p class="feature-text">Enter your monthly electricity, travel, and fuel usage. We apply verified emission factors, calculate your total CO₂, and show you where to focus first.</p>
        </div>
        <div class="feature-card">
          <span class="feature-icon">🤖</span>
          <div class="feature-title">AI-powered features</div>
          <p class="feature-text">Get a natural-language carbon summary and a personalised 30-day reduction plan — generated by Claude AI based on your specific data.</p>
        </div>
        <div class="feature-card">
          <span class="feature-icon">📊</span>
          <div class="feature-title">Verified data</div>
          <p class="feature-text">We use peer-reviewed factors: <strong>0.82 kg CO₂/kWh</strong> electricity, <strong>0.21 kg CO₂/km</strong> travel, <strong>2.98 kg CO₂/kg</strong> LPG. Source: IPCC & India CEA 2023.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- APP FLOW -->
  <section class="flow-section">
    <div class="flow-inner">
      <span class="section-tag">How to use it</span>
      <h2 class="section-heading">App <span class="lime" style="color:var(--lime)">Flow</span></h2>
      <div class="flow-steps">
        <div class="flow-step-block active-step">
          <div class="flow-step-num">01</div>
          <div class="flow-step-label">About Page</div>
          <div class="flow-step-desc">Learn about the project</div>
        </div>
        <div class="flow-step-block">
          <div class="flow-step-num">02</div>
          <div class="flow-step-label">Calculator</div>
          <div class="flow-step-desc">Enter your monthly data</div>
        </div>
        <div class="flow-step-block">
          <div class="flow-step-num">03</div>
          <div class="flow-step-label">Results + AI</div>
          <div class="flow-step-desc">See breakdown & AI plan</div>
        </div>
        <div class="flow-step-block">
          <div class="flow-step-num">04</div>
          <div class="flow-step-label">Download PDF</div>
          <div class="flow-step-desc">Save your carbon report</div>
        </div>
      </div>
    </div>
  </section>

  <!-- TECH STACK -->
  <section class="tech-section">
    <div class="tech-inner">
      <span class="section-tag">Technologies & Applications</span>
      <h2 class="section-heading">Built with</h2>
      <div class="tech-grid">
        <div class="tech-row"><span class="tech-tag">Frontend</span><span class="tech-val">HTML · CSS · JavaScript</span></div>
        <div class="tech-row"><span class="tech-tag">Backend</span><span class="tech-val">PHP — carbon calculation logic & session history</span></div>
        <div class="tech-row"><span class="tech-tag">Charts</span><span class="tech-val">Chart.js — doughnut pie chart</span></div>
        <div class="tech-row"><span class="tech-tag">PDF</span><span class="tech-val">jsPDF — client-side report generation</span></div>
        <div class="tech-row"><span class="tech-tag">AI</span><span class="tech-val">Claude API (Anthropic) — smart summary & 30-day plan</span></div>
        <div class="tech-row"><span class="tech-tag">Server</span><span class="tech-val">XAMPP (Apache) — local PHP environment</span></div>
        <div class="tech-row"><span class="tech-tag">Editor</span><span class="tech-val">Visual Studio Code</span></div>
        <div class="tech-row"><span class="tech-tag">OS</span><span class="tech-val">Windows · Chrome browser for testing</span></div>
      </div>
    </div>
  </section>

  <!-- VIVA LINE -->
  <div class="viva-band">
    <div class="viva-inner">
      <span class="viva-label">Viva one-liner</span>
      <p class="viva-text">"We used HTML, CSS, and JavaScript for the frontend, PHP for backend processing, XAMPP as the local server, and Claude AI to generate personalised summaries and 30-day carbon reduction plans."</p>
    </div>
  </div>

  <!-- CTA -->
  <section class="cta-band">
    <h2>Ready to measure your <span>footprint?</span></h2>
    <p>Enter your monthly electricity, travel, and fuel data. Results in seconds.</p>
    <a href="index.php" class="btn-primary-dark" style="font-size:16px;padding:16px 40px;">Get Started →</a>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <p>Carbonwise &nbsp;·&nbsp; Emission factors: IPCC & India CEA 2023 &nbsp;·&nbsp; <span>HTML · CSS · PHP · XAMPP</span></p>
  </footer>

</body>
</html>