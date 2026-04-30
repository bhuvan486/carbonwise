<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$electricity = floatval($_POST['electricity'] ?? 0);
$travel      = floatval($_POST['travel']      ?? 0);
$lpg         = floatval($_POST['lpg']         ?? 0);

$elec_kg   = round($electricity * 0.82, 2);
$travel_kg = round($travel      * 0.21, 2);
$lpg_kg    = round($lpg         * 2.98, 2);
$total_kg  = round($elec_kg + $travel_kg + $lpg_kg, 2);

$avg_kg  = 167;
$vs_pct  = $avg_kg > 0 ? round(abs($total_kg - $avg_kg) / $avg_kg * 100) : 0;
$vs_avg  = $total_kg < $avg_kg ? 'below' : ($total_kg == $avg_kg ? 'equal' : 'above');

$sources    = ['Electricity'=>$elec_kg,'Travel'=>$travel_kg,'LPG / Fuel'=>$lpg_kg];
arsort($sources);
$top_source = array_key_first($sources);

$level='Low'; $level_color='#c8e63c'; $level_bg='rgba(200,230,60,0.1)';
$level_msg='Your footprint is below the national average. Great work!';
if ($total_kg>300) {
  $level='High'; $level_color='#e74c3c'; $level_bg='rgba(231,76,60,0.1)';
  $level_msg='Above average. Small daily changes can make a big difference.';
} elseif ($total_kg>150) {
  $level='Moderate'; $level_color='#f39c12'; $level_bg='rgba(243,156,18,0.1)';
  $level_msg='Around average. A few targeted changes could reduce this significantly.';
}

$trees = max(1, round($total_kg / 21));

$tips_map = [
  'Electricity'=>[['icon'=>'💡','tip'=>'Switch to LED bulbs — 75% less energy than incandescent.'],['icon'=>'🔌','tip'=>'Unplug devices on standby; they draw power even when idle.'],['icon'=>'☀️','tip'=>'Consider solar panels to reduce grid electricity dependence.']],
  'Travel'=>[['icon'=>'🚲','tip'=>'Cycle or walk for trips under 3 km — zero emissions.'],['icon'=>'🚗','tip'=>'Carpooling 2 days a week cuts travel emissions by 40%.'],['icon'=>'🚌','tip'=>'Public transport emits 6× less CO₂ per passenger than a car.']],
  'LPG / Fuel'=>[['icon'=>'🍳','tip'=>'Use a pressure cooker — reduces gas usage by up to 70%.'],['icon'=>'⚡','tip'=>'Switch to induction — cleaner, faster, more efficient.'],['icon'=>'🔧','tip'=>'Check for gas leaks regularly; even small leaks waste fuel.']],
];
$tips = $tips_map[$top_source];

$dynamic_tips = [];
if ($electricity>100) $dynamic_tips[] = ['icon'=>'💡','tip'=>'Your electricity is above 100 kWh. LEDs and unplugging idle devices can cut this by up to 30%.'];
if ($travel>150)      $dynamic_tips[] = ['icon'=>'🚌','tip'=>'Your travel is above 150 km. Public transport or carpooling twice a week cuts emissions by 40%.'];
if ($lpg>15)          $dynamic_tips[] = ['icon'=>'🍳','tip'=>'Your LPG is above average. A pressure cooker reduces cooking gas by around 70%.'];
if (empty($dynamic_tips)) $dynamic_tips[] = ['icon'=>'🌱','tip'=>'Your usage across all sources is well managed. Keep up these great habits!'];

$_SESSION['history'][] = ['date'=>date('d M Y'),'total'=>$total_kg,'level'=>$level];
if (count($_SESSION['history'])>5) array_shift($_SESSION['history']);

$max_val = max($total_kg,$avg_kg);
$you_w   = $max_val>0 ? round($total_kg/$max_val*100) : 0;
$avg_w   = $max_val>0 ? round($avg_kg/$max_val*100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Results — Carbonwise</title>
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800;900&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --lime:#c8e63c; --dark:#1a1a1a; --darker:#111111;
      --mid:#2a2a2a; --mid2:#222222; --border:#333333;
      --white:#ffffff; --muted:#999999; --light:#e8e8e8;
    }
    html { font-size:16px; -webkit-font-smoothing:antialiased; }
    body { font-family:'Barlow',sans-serif; background:var(--darker); color:var(--white); min-height:100vh; }

    /* NAVBAR */
    .navbar { position:fixed; top:0; left:0; right:0; z-index:100; background:var(--white); height:68px; display:flex; align-items:center; padding:0 2rem; justify-content:space-between; border-bottom:2px solid var(--lime); }
    .nav-logo { display:flex; align-items:center; gap:10px; text-decoration:none; }
    .nav-logo-hex { width:36px; height:36px; background:var(--lime); clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; color:var(--dark); }
    .nav-logo-text { font-family:'Barlow Condensed',sans-serif; font-size:22px; font-weight:800; color:var(--dark); letter-spacing:1px; text-transform:uppercase; }
    .nav-links { display:flex; align-items:center; gap:2rem; }
    .nav-link { font-size:13px; font-weight:600; color:var(--dark); text-decoration:none; letter-spacing:0.5px; text-transform:uppercase; transition:color 0.15s; }
    .nav-link:hover { color:#666; }
    .nav-cta { background:var(--lime); color:var(--dark); font-family:'Barlow Condensed',sans-serif; font-size:14px; font-weight:800; letter-spacing:1px; text-transform:uppercase; padding:10px 22px; text-decoration:none; }
    .nav-cta:hover { background:#b5d030; }

    /* RESULT HERO */
    .result-hero { padding-top:68px; background:linear-gradient(160deg,#1c1c1c 0%,#222 50%,#1a1a1a 100%); border-bottom:1px solid var(--border); }
    .result-hero-inner { max-width:1200px; margin:0 auto; padding:50px 2rem 60px; display:grid; grid-template-columns:1fr auto; gap:3rem; align-items:center; }
    .result-eyebrow { display:inline-flex; align-items:center; gap:8px; font-size:11px; font-weight:600; letter-spacing:0.15em; text-transform:uppercase; color:var(--lime); margin-bottom:14px; }
    .result-eyebrow::before { content:''; width:28px; height:2px; background:var(--lime); }
    .result-total { font-family:'Barlow Condensed',sans-serif; font-size:clamp(64px,10vw,110px); font-weight:900; line-height:0.9; color:var(--white); letter-spacing:-2px; }
    .result-unit { font-size:clamp(24px,4vw,36px); color:rgba(255,255,255,0.6); }
    .result-level {
      display:inline-block; font-family:'Barlow Condensed',sans-serif;
      font-size:13px; font-weight:700; letter-spacing:0.1em; text-transform:uppercase;
      padding:5px 14px; margin-top:14px;
      background:<?= $level_bg ?>; color:<?= $level_color ?>;
      border:1px solid <?= $level_color ?>;
    }
    .result-msg { font-size:14px; color:rgba(255,255,255,0.6); margin-top:8px; line-height:1.6; }
    .result-meta { text-align:right; }
    .meta-block { background:var(--mid); border-left:3px solid var(--lime); padding:1.25rem 1.5rem; margin-bottom:2px; min-width:200px; }
    .meta-num { font-family:'Barlow Condensed',sans-serif; font-size:28px; font-weight:900; color:var(--lime); }
    .meta-label { font-size:12px; color:var(--muted); margin-top:2px; }

    /* PAGE BODY */
    .page-body { max-width:1200px; margin:0 auto; padding:50px 2rem 80px; }

    /* SECTION HEADER */
    .sec-tag { font-family:'Barlow Condensed',sans-serif; font-size:11px; font-weight:700; letter-spacing:0.15em; text-transform:uppercase; color:var(--lime); display:flex; align-items:center; gap:10px; margin-bottom:20px; }
    .sec-tag::after { content:''; flex:1; height:1px; background:var(--border); }

    /* AI CARD */
    .ai-card { background:var(--mid); border-left:3px solid var(--lime); padding:1.75rem 2rem; margin-bottom:2px; }
    .ai-card-header { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
    .ai-tag { background:var(--lime); color:var(--dark); font-family:'Barlow Condensed',sans-serif; font-size:10px; font-weight:800; padding:3px 10px; letter-spacing:0.08em; text-transform:uppercase; }
    .ai-card-title { font-family:'Barlow Condensed',sans-serif; font-size:15px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--light); }
    .ai-text { font-size:14px; color:rgba(255,255,255,0.75); line-height:1.75; }
    .ai-loading { display:flex; align-items:center; gap:8px; color:var(--muted); font-size:13px; }
    .ai-dot { width:6px; height:6px; border-radius:50%; background:var(--lime); animation:pulse 1.4s ease-in-out infinite; }
    .ai-dot:nth-child(2){animation-delay:.2s;} .ai-dot:nth-child(3){animation-delay:.4s;}
    @keyframes pulse{0%,80%,100%{opacity:.3;transform:scale(.8)}40%{opacity:1;transform:scale(1)}}

    /* GRID */
    .two-col { display:grid; grid-template-columns:1fr 1fr; gap:2px; margin-bottom:2px; }
    @media(max-width:640px){.two-col{grid-template-columns:1fr;}}

    /* DARK PANEL */
    .panel { background:var(--mid); padding:1.75rem; }
    .panel-title { font-family:'Barlow Condensed',sans-serif; font-size:14px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--lime); margin-bottom:16px; }

    /* CHART */
    .chart-wrap { max-width:220px; margin:0 auto; }

    /* BREAKDOWN */
    .breakdown-list { display:flex; flex-direction:column; gap:2px; }
    .breakdown-row { background:var(--darker); padding:.9rem 1rem; border-left:2px solid transparent; transition:border-color 0.2s; }
    .breakdown-row.top { border-left-color:var(--lime); }
    .breakdown-top { display:flex; align-items:center; gap:8px; margin-bottom:7px; }
    .b-icon { font-size:15px; } .b-name { font-size:13px; font-weight:600; color:var(--light); flex:1; }
    .b-top-tag { font-size:10px; font-weight:700; background:var(--lime); color:var(--dark); padding:2px 7px; font-family:'Barlow Condensed',sans-serif; letter-spacing:0.05em; text-transform:uppercase; }
    .b-kg { font-size:13px; font-weight:600; color:var(--white); }
    .bar-track { height:3px; background:var(--border); margin-bottom:5px; }
    .bar-fill { height:100%; background:var(--lime); transition:width .9s; }
    .b-meta { display:flex; justify-content:space-between; font-size:11px; color:var(--muted); }

    /* COMPARISON */
    .cmp-row { display:grid; grid-template-columns:1fr auto 1fr; gap:2px; align-items:center; margin-bottom:16px; }
    .cmp-box { background:var(--darker); padding:1rem; text-align:center; }
    .cmp-box-label { font-size:10px; text-transform:uppercase; letter-spacing:0.07em; color:var(--muted); display:block; margin-bottom:4px; }
    .cmp-box-val { font-family:'Barlow Condensed',sans-serif; font-size:26px; font-weight:900; color:var(--white); }
    .cmp-badge { font-family:'Barlow Condensed',sans-serif; font-size:12px; font-weight:700; letter-spacing:0.05em; text-transform:uppercase; padding:6px 12px; white-space:nowrap; }
    .cmp-good { background:rgba(200,230,60,0.15); color:var(--lime); border:1px solid var(--lime); }
    .cmp-bad  { background:rgba(231,76,60,0.15);  color:#e74c3c; border:1px solid #e74c3c; }
    .cmp-bars { display:flex; flex-direction:column; gap:8px; }
    .cmp-bar-row { display:flex; align-items:center; gap:10px; }
    .cmp-bar-lbl { font-size:11px; color:var(--muted); min-width:26px; font-family:'Barlow Condensed',sans-serif; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; }
    .cmp-bar-track { flex:1; height:6px; background:var(--border); }
    .cmp-bar-you { height:100%; background:var(--lime); transition:width 1s; }
    .cmp-bar-avg { height:100%; background:#555; transition:width 1s; }
    .cmp-bar-num { font-size:11px; color:var(--muted); min-width:58px; text-align:right; font-variant-numeric:tabular-nums; }

    /* TABLE */
    .result-table { width:100%; border-collapse:collapse; font-size:13px; }
    .result-table th { background:var(--darker); color:var(--lime); font-family:'Barlow Condensed',sans-serif; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; padding:10px 12px; text-align:left; border-bottom:2px solid var(--lime); }
    .result-table td { padding:10px 12px; border-bottom:1px solid var(--border); color:rgba(255,255,255,0.7); }
    .result-table td strong { color:var(--white); }
    .result-table tr:hover td { background:var(--darker); }
    .table-total td { background:rgba(200,230,60,0.07) !important; border-top:2px solid var(--lime); }
    .table-total strong { color:var(--lime); }

    /* TREES */
    .trees-band { background:var(--mid); border-left:3px solid var(--lime); padding:1.5rem 2rem; display:flex; align-items:center; gap:1.5rem; margin-bottom:2px; }
    .trees-icon { font-size:36px; flex-shrink:0; }
    .trees-num { font-family:'Barlow Condensed',sans-serif; font-size:28px; font-weight:900; color:var(--lime); display:block; }
    .trees-desc { font-size:13px; color:var(--muted); margin-top:2px; }

    /* TIPS */
    .tips-list { display:flex; flex-direction:column; gap:2px; }
    .tip-row { background:var(--darker); padding:.9rem 1.2rem; display:flex; gap:12px; align-items:flex-start; border-left:2px solid var(--border); transition:border-color 0.2s; }
    .tip-row:hover { border-left-color:var(--lime); }
    .tip-row.dynamic { border-left-color:var(--lime); background:rgba(200,230,60,0.04); }
    .tip-icon { font-size:17px; flex-shrink:0; margin-top:1px; }
    .tip-text { font-size:13px; color:rgba(255,255,255,0.7); line-height:1.65; }

    /* GOAL PLANNER */
    .goal-card { background:var(--mid); overflow:hidden; margin-bottom:2px; }
    .goal-header { background:linear-gradient(135deg,var(--lime) 0%,#a8c020 100%); padding:1.25rem 1.75rem; }
    .goal-header h3 { font-family:'Barlow Condensed',sans-serif; font-size:20px; font-weight:900; text-transform:uppercase; color:var(--dark); letter-spacing:0.5px; }
    .goal-header p { font-size:12px; color:rgba(0,0,0,0.6); margin-top:2px; }
    .goal-body { padding:1.5rem 1.75rem; }
    .goal-slider-row { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
    .goal-slider-row label { font-size:12px; color:var(--muted); min-width:120px; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; }
    input[type="range"] { flex:1; accent-color:var(--lime); cursor:pointer; }
    .goal-pct { font-family:'Barlow Condensed',sans-serif; font-size:18px; font-weight:900; color:var(--lime); min-width:40px; }
    .goal-target { background:var(--darker); border-left:2px solid var(--lime); padding:10px 14px; margin-bottom:12px; font-size:13px; color:rgba(255,255,255,0.7); }
    .goal-target strong { color:var(--lime); }
    .goal-btn { width:100%; height:46px; background:var(--lime); color:var(--dark); border:none; font-family:'Barlow Condensed',sans-serif; font-size:15px; font-weight:800; letter-spacing:1px; text-transform:uppercase; cursor:pointer; transition:background 0.15s; }
    .goal-btn:hover { background:#b5d030; }
    .goal-btn:disabled { opacity:0.6; cursor:default; }
    .plan-output { margin-top:12px; }
    .plan-loading { display:flex; align-items:center; gap:8px; color:var(--muted); font-size:13px; padding:6px 0; }
    .plan-week { background:var(--darker); border-left:2px solid var(--border); padding:.85rem 1rem; margin-bottom:2px; }
    .plan-week:hover { border-left-color:var(--lime); }
    .plan-week-label { font-family:'Barlow Condensed',sans-serif; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--lime); margin-bottom:4px; }
    .plan-week-text { font-size:13px; color:rgba(255,255,255,0.7); line-height:1.65; }

    /* HISTORY */
    .history-list { display:flex; flex-direction:column; gap:2px; }
    .history-row { background:var(--darker); padding:.9rem 1.2rem; display:flex; align-items:center; gap:12px; }
    .h-date { font-size:12px; color:var(--muted); min-width:86px; }
    .h-val  { font-size:14px; font-weight:600; color:var(--white); flex:1; }
    .h-badge { font-family:'Barlow Condensed',sans-serif; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; padding:2px 9px; border:1px solid; }
    .h-low { color:var(--lime); border-color:var(--lime); }
    .h-moderate { color:#f39c12; border-color:#f39c12; }
    .h-high { color:#e74c3c; border-color:#e74c3c; }

    /* ACTIONS */
    .result-actions { display:flex; gap:2px; margin-top:40px; }
    .btn-outline { background:transparent; color:var(--white); border:1px solid var(--border); font-family:'Barlow Condensed',sans-serif; font-size:14px; font-weight:800; letter-spacing:1px; text-transform:uppercase; padding:14px 28px; text-decoration:none; transition:border-color 0.15s,color 0.15s; }
    .btn-outline:hover { border-color:var(--lime); color:var(--lime); }
    .btn-lime { background:var(--lime); color:var(--dark); border:none; font-family:'Barlow Condensed',sans-serif; font-size:14px; font-weight:800; letter-spacing:1px; text-transform:uppercase; padding:14px 28px; cursor:pointer; transition:background 0.15s; }
    .btn-lime:hover { background:#b5d030; }

    /* FOOTER */
    .footer { background:var(--darker); color:var(--muted); padding:24px 2rem; text-align:center; font-size:12px; border-top:1px solid var(--border); }
    .footer span { color:var(--lime); }

    @keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
    .panel,.ai-card,.trees-band,.goal-card { animation:fadeUp .35s ease both; }
    @media(max-width:768px){.result-hero-inner{grid-template-columns:1fr;}.result-meta{display:none;}.nav-links{display:none;}}
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
      <a href="index.php" class="nav-link">Calculator</a>
    </div>
    <a href="index.php" class="nav-cta">Recalculate</a>
  </nav>

  <!-- RESULT HERO -->
  <div class="result-hero">
    <div class="result-hero-inner">
      <div>
        <div class="result-eyebrow">Your Monthly Carbon Footprint</div>
        <div class="result-total"><?= number_format($total_kg,1) ?> <span class="result-unit">kg CO₂</span></div>
        <div class="result-level"><?= $level ?> Impact</div>
        <p class="result-msg"><?= $level_msg ?></p>
      </div>
      <div class="result-meta">
        <div class="meta-block">
          <div class="meta-num"><?= $trees ?></div>
          <div class="meta-label">trees to offset</div>
        </div>
        <div class="meta-block">
          <div class="meta-num"><?= $vs_pct ?>%</div>
          <div class="meta-label"><?= $vs_avg ?> India avg</div>
        </div>
        <div class="meta-block">
          <div class="meta-num"><?= date('d M') ?></div>
          <div class="meta-label"><?= date('Y') ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-body">

    <!-- AI SUMMARY -->
    <div class="sec-tag">AI-powered summary</div>
    <div class="ai-card" style="margin-bottom:30px;">
      <div class="ai-card-header">
        <span class="ai-tag">AI Summary</span>
        <span class="ai-card-title">Your personalised carbon report</span>
      </div>
      <div id="aiSummaryContent">
        <div class="ai-loading">
          <div class="ai-dot"></div><div class="ai-dot"></div><div class="ai-dot"></div>
          <span style="margin-left:6px;">Generating your summary...</span>
        </div>
      </div>
    </div>

    <!-- CHART + BREAKDOWN -->
    <div class="sec-tag">Emission breakdown</div>
    <div class="two-col" style="margin-bottom:30px;">
      <div class="panel">
        <div class="panel-title">Emission Share</div>
        <div class="chart-wrap"><canvas id="emissionChart"></canvas></div>
      </div>
      <div class="panel">
        <div class="panel-title">By Source</div>
        <div class="breakdown-list">
          <?php
          $icons  = ['Electricity'=>'⚡','Travel'=>'🚗','LPG / Fuel'=>'🔥'];
          $inputs = ['Electricity'=>$electricity.' kWh','Travel'=>$travel.' km','LPG / Fuel'=>$lpg.' kg'];
          foreach ($sources as $name => $kg):
            $pct = $total_kg>0 ? round(($kg/$total_kg)*100) : 0;
          ?>
          <div class="breakdown-row <?= ($name===$top_source)?'top':'' ?>">
            <div class="breakdown-top">
              <span class="b-icon"><?= $icons[$name] ?></span>
              <span class="b-name"><?= $name ?></span>
              <?php if($name===$top_source): ?><span class="b-top-tag">Highest</span><?php endif; ?>
              <span class="b-kg"><?= number_format($kg,2) ?> kg</span>
            </div>
            <div class="bar-track"><div class="bar-fill" style="width:<?= $pct ?>%"></div></div>
            <div class="b-meta"><span><?= $inputs[$name] ?></span><span><?= $pct ?>%</span></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- COMPARISON -->
    <div class="sec-tag">You vs. India average</div>
    <div class="panel" style="margin-bottom:30px;">
      <div class="cmp-row">
        <div class="cmp-box">
          <span class="cmp-box-label">Your footprint</span>
          <span class="cmp-box-val"><?= number_format($total_kg,1) ?> kg</span>
        </div>
        <div style="text-align:center;padding:0 1rem;">
          <span class="cmp-badge <?= $vs_avg==='below'?'cmp-good':'cmp-bad' ?>">
            <?php if($vs_avg==='below'): ?><?= $vs_pct ?>% below ↓
            <?php elseif($vs_avg==='above'): ?><?= $vs_pct ?>% above ↑
            <?php else: ?>Exactly avg<?php endif; ?>
          </span>
        </div>
        <div class="cmp-box">
          <span class="cmp-box-label">India average</span>
          <span class="cmp-box-val"><?= $avg_kg ?> kg</span>
        </div>
      </div>
      <div class="cmp-bars">
        <div class="cmp-bar-row">
          <span class="cmp-bar-lbl">You</span>
          <div class="cmp-bar-track"><div class="cmp-bar-you" style="width:<?= $you_w ?>%"></div></div>
          <span class="cmp-bar-num"><?= number_format($total_kg,1) ?> kg</span>
        </div>
        <div class="cmp-bar-row">
          <span class="cmp-bar-lbl">Avg</span>
          <div class="cmp-bar-track"><div class="cmp-bar-avg" style="width:<?= $avg_w ?>%"></div></div>
          <span class="cmp-bar-num"><?= $avg_kg ?> kg</span>
        </div>
      </div>
    </div>

    <!-- CALCULATION TABLE -->
    <div class="sec-tag">Calculation detail</div>
    <div class="panel" style="margin-bottom:30px;">
      <table class="result-table">
        <thead><tr><th>Source</th><th>Your Usage</th><th>Factor</th><th>CO₂ Emitted (kg)</th></tr></thead>
        <tbody>
          <tr><td>⚡ Electricity</td><td><?= $electricity ?> kWh</td><td>× 0.82 kg/kWh</td><td><strong><?= number_format($elec_kg,2) ?></strong></td></tr>
          <tr><td>🚗 Travel</td><td><?= $travel ?> km</td><td>× 0.21 kg/km</td><td><strong><?= number_format($travel_kg,2) ?></strong></td></tr>
          <tr><td>🔥 LPG / Fuel</td><td><?= $lpg ?> kg</td><td>× 2.98 kg/kg</td><td><strong><?= number_format($lpg_kg,2) ?></strong></td></tr>
          <tr class="table-total"><td colspan="3"><strong>Total Carbon Footprint</strong></td><td><strong><?= number_format($total_kg,2) ?> kg CO₂</strong></td></tr>
        </tbody>
      </table>
    </div>

    <!-- TREES -->
    <div class="trees-band" style="margin-bottom:30px;">
      <div class="trees-icon">🌳</div>
      <div>
        <span class="trees-num"><?= $trees ?> trees needed</span>
        <span class="trees-desc">to offset your monthly CO₂ — each tree absorbs ~21 kg CO₂ per year</span>
      </div>
    </div>

    <!-- TIPS -->
    <div class="sec-tag">Personalised suggestions</div>
    <div class="tips-list" style="margin-bottom:30px;">
      <?php foreach($tips as $t): ?>
      <div class="tip-row"><span class="tip-icon"><?= $t['icon'] ?></span><p class="tip-text"><?= $t['tip'] ?></p></div>
      <?php endforeach; ?>
      <?php foreach($dynamic_tips as $t): ?>
      <div class="tip-row dynamic"><span class="tip-icon"><?= $t['icon'] ?></span><p class="tip-text"><?= $t['tip'] ?></p></div>
      <?php endforeach; ?>
    </div>

    <!-- GOAL PLANNER -->
    <div class="sec-tag">30-day reduction plan</div>
    <div class="goal-card" style="margin-bottom:30px;">
      <div class="goal-header">
        <h3>30-Day Reduction Plan</h3>
        <p>Set a target and get a personalised weekly action plan</p>
      </div>
      <div class="goal-body">
        <div class="goal-slider-row">
          <label>Target reduction</label>
          <input type="range" id="goalSlider" min="5" max="50" value="20" step="5" oninput="updateGoal()">
          <span class="goal-pct" id="goalPct">20%</span>
        </div>
        <div class="goal-target" id="goalTarget">
          Reduce from <strong><?= number_format($total_kg,1) ?> kg</strong> to
          <strong id="goalVal"><?= number_format($total_kg*0.8,1) ?> kg CO₂</strong> —
          saving <strong id="goalSave"><?= number_format($total_kg*0.2,1) ?> kg</strong> per month
        </div>
        <button class="goal-btn" id="planBtn" onclick="generatePlan()">Generate My 30-Day Plan →</button>
        <div class="plan-output" id="planOutput" style="display:none;"></div>
      </div>
    </div>

    <!-- HISTORY -->
    <?php if(!empty($_SESSION['history'])&&count($_SESSION['history'])>1): ?>
    <div class="sec-tag">Recent calculations</div>
    <div class="history-list" style="margin-bottom:30px;">
      <?php foreach(array_reverse($_SESSION['history']) as $h): ?>
      <div class="history-row">
        <span class="h-date"><?= $h['date'] ?></span>
        <span class="h-val"><?= number_format($h['total'],1) ?> kg CO₂</span>
        <span class="h-badge h-<?= strtolower($h['level']) ?>"><?= $h['level'] ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="result-actions">
      <a href="index.php" class="btn-outline">← Recalculate</a>
      <button onclick="downloadPDF()" class="btn-lime">Download Report ↓</button>
    </div>

  </div>

  <footer class="footer">
    <p>Carbonwise &nbsp;·&nbsp; IPCC &amp; India CEA 2023 &nbsp;·&nbsp; India avg: 167 kg CO₂/month &nbsp;·&nbsp; <span>HTML · CSS · PHP · XAMPP</span></p>
  </footer>

<script>
const TOTAL=<?= $total_kg ?>,ELEC=<?= $elec_kg ?>,TRAVEL=<?= $travel_kg ?>,LPG=<?= $lpg_kg ?>;
const TOP='<?= addslashes($top_source) ?>',AVG=<?= $avg_kg ?>,VS_AVG='<?= $vs_avg ?>',VS_PCT=<?= $vs_pct ?>;
const LEVEL='<?= $level ?>',TREES=<?= $trees ?>,E_IN=<?= $electricity ?>,T_IN=<?= $travel ?>,L_IN=<?= $lpg ?>;
const API_KEY='YOUR_API_KEY_HERE';

new Chart(document.getElementById('emissionChart').getContext('2d'),{
  type:'doughnut',
  data:{labels:['Electricity','Travel','LPG / Fuel'],datasets:[{data:[ELEC,TRAVEL,LPG],backgroundColor:['#c8e63c','#8ab020','#4a6010'],borderColor:'#2a2a2a',borderWidth:3,hoverOffset:5}]},
  options:{cutout:'62%',plugins:{legend:{position:'bottom',labels:{font:{family:'Barlow',size:11},padding:12,color:'#999999'}},tooltip:{callbacks:{label:c=>` ${c.label}: ${c.parsed.toFixed(2)} kg CO₂`}}}}
});

async function loadSummary(){
  if(API_KEY==='YOUR_API_KEY_HERE'){
    const pct=((TOP==='Electricity'?ELEC:TOP==='Travel'?TRAVEL:LPG)/TOTAL*100).toFixed(0);
    document.getElementById('aiSummaryContent').innerHTML=
      `<p class="ai-text">Your total footprint is <strong style="color:#fff">${TOTAL.toFixed(1)} kg CO₂</strong> this month.
      <strong style="color:#fff">${TOP}</strong> is your biggest source at ${pct}% of your total.
      You are <strong style="color:${VS_AVG==='below'?'#c8e63c':'#e74c3c'}">${VS_PCT}% ${VS_AVG}</strong> the India average of ${AVG} kg.
      <em style="color:#666;font-size:12px;">Add your Claude API key in result.php to get a fully personalised AI summary.</em></p>`;
    return;
  }
  try{
    const res=await fetch('https://api.anthropic.com/v1/messages',{method:'POST',headers:{'Content-Type':'application/json','x-api-key':API_KEY,'anthropic-version':'2023-06-01'},body:JSON.stringify({model:'claude-haiku-4-5-20251001',max_tokens:200,messages:[{role:'user',content:`Write a 2-3 sentence professional carbon analysis. Total:${TOTAL}kg. Electricity:${ELEC}kg(${E_IN}kWh). Travel:${TRAVEL}kg(${T_IN}km). LPG:${LPG}kg(${L_IN}kg). Highest:${TOP}. India avg:${AVG}kg. ${VS_PCT}% ${VS_AVG}. Level:${LEVEL}. Be specific. End with one actionable sentence. No intro.`}]})});
    const data=await res.json();
    document.getElementById('aiSummaryContent').innerHTML=`<p class="ai-text">${data.content?.[0]?.text||''}</p>`;
  }catch(e){
    document.getElementById('aiSummaryContent').innerHTML=`<p class="ai-text">Your footprint of <strong style="color:#fff">${TOTAL.toFixed(1)} kg CO₂</strong> is ${VS_PCT}% ${VS_AVG} the India average. Focus on reducing your ${TOP.toLowerCase()} emissions first.</p>`;
  }
}
loadSummary();

function updateGoal(){
  const pct=parseInt(document.getElementById('goalSlider').value);
  document.getElementById('goalPct').textContent=pct+'%';
  document.getElementById('goalVal').textContent=(TOTAL*(1-pct/100)).toFixed(1)+' kg CO₂';
  document.getElementById('goalSave').textContent=(TOTAL*pct/100).toFixed(1)+' kg';
}

async function generatePlan(){
  const pct=parseInt(document.getElementById('goalSlider').value);
  const save=(TOTAL*pct/100).toFixed(1);
  const btn=document.getElementById('planBtn'),out=document.getElementById('planOutput');
  btn.disabled=true;btn.textContent='Generating...';out.style.display='block';
  out.innerHTML=`<div class="plan-loading"><div class="ai-dot"></div><div class="ai-dot"></div><div class="ai-dot"></div><span style="margin-left:6px;">Building your plan...</span></div>`;
  if(API_KEY==='YOUR_API_KEY_HERE'){
    setTimeout(()=>{
      const w=[
        {w:'Week 1',t:TOP==='Electricity'?'Replace all bulbs with LEDs and unplug idle devices. Target: save ~10 kg CO₂.':TOP==='Travel'?'Replace 2 car trips with cycling or walking. Target: save ~8 kg CO₂.':'Use a pressure cooker for all meals. Target: save ~7 kg CO₂.'},
        {w:'Week 2',t:TOP==='Electricity'?'Set AC to 24°C and avoid peak-hour usage. Target: save ~8 kg CO₂.':TOP==='Travel'?'Use public transport for commute 3 days. Target: save ~10 kg CO₂.':'Batch-cook to reduce daily gas use. Target: save ~5 kg CO₂.'},
        {w:'Week 3',t:'Review progress. Identify one more habit to change and commit to it.'},
        {w:'Week 4',t:`Final push — aim to save ${save} kg. Recalculate on Carbonwise to see your improvement.`},
      ];
      out.innerHTML=w.map(x=>`<div class="plan-week"><div class="plan-week-label">${x.w}</div><div class="plan-week-text">${x.t}</div></div>`).join('')
        +`<p style="font-size:12px;color:#666;margin-top:8px;font-style:italic;">Add your Claude API key for a fully personalised AI plan.</p>`;
      btn.disabled=false;btn.textContent='Regenerate Plan →';
    },900);return;
  }
  try{
    const res=await fetch('https://api.anthropic.com/v1/messages',{method:'POST',headers:{'Content-Type':'application/json','x-api-key':API_KEY,'anthropic-version':'2023-06-01'},body:JSON.stringify({model:'claude-haiku-4-5-20251001',max_tokens:350,messages:[{role:'user',content:`30-day carbon plan. Current:${TOTAL}kg. Reduce ${pct}%(save ${save}kg). Highest:${TOP}. 4 weekly steps. Format: Week 1:[...] Week 2:[...] Week 3:[...] Week 4:[...] No intro.`}]})});
    const data=await res.json();const text=data.content?.[0]?.text||'';
    const lines=text.split('\n').filter(l=>l.trim().match(/^Week\s*\d/i));
    out.innerHTML=lines.length>0?lines.map(l=>{const[label,...rest]=l.split(':');return`<div class="plan-week"><div class="plan-week-label">${label.trim()}</div><div class="plan-week-text">${rest.join(':').trim()}</div></div>`;}).join(''):`<div class="plan-week"><div class="plan-week-text">${text}</div></div>`;
  }catch(e){out.innerHTML=`<div class="plan-week"><div class="plan-week-text">Could not load plan. Check your API key.</div></div>`;}
  btn.disabled=false;btn.textContent='Regenerate Plan →';
}

function downloadPDF(){
  const{jsPDF}=window.jspdf;const doc=new jsPDF();
  const summary=document.getElementById('aiSummaryContent')?.innerText||'';
  doc.setFillColor(26,26,26);doc.rect(0,0,210,38,'F');
  doc.setFillColor(200,230,60);doc.rect(0,36,210,2,'F');
  doc.setFont('helvetica','bold');doc.setFontSize(20);doc.setTextColor(255,255,255);
  doc.text('CARBONWISE',20,18);
  doc.setFont('helvetica','normal');doc.setFontSize(10);doc.setTextColor(150,150,150);
  doc.text('Monthly Carbon Report  ·  '+new Date().toLocaleDateString('en-IN',{day:'numeric',month:'long',year:'numeric'}),20,28);
  doc.setFontSize(30);doc.setFont('helvetica','bold');doc.setTextColor(26,26,26);
  doc.setFillColor(245,245,245);doc.rect(0,40,210,30,'F');
  doc.setTextColor(50,50,50);doc.text(TOTAL.toFixed(1)+' kg CO2',20,60);
  doc.setFontSize(11);doc.setFont('helvetica','normal');doc.setTextColor(100,100,100);
  doc.text('Total monthly footprint  ·  Impact: '+LEVEL,20,70);
  doc.setDrawColor(200,230,60);doc.line(20,78,190,78);
  doc.setFontSize(11);doc.setFont('helvetica','bold');doc.setTextColor(50,50,50);
  doc.text('AI Summary',20,88);
  doc.setFont('helvetica','normal');doc.setFontSize(10);doc.setTextColor(80,80,80);
  const sl=doc.splitTextToSize(summary.replace(/\n/g,' ').trim(),165);
  doc.text(sl.slice(0,4),20,97);
  let y=97+Math.min(sl.length,4)*6+8;
  doc.setDrawColor(200,200,200);doc.line(20,y,190,y);y+=10;
  doc.setFont('helvetica','bold');doc.setFontSize(11);doc.setTextColor(50,50,50);
  doc.text('Breakdown',20,y);y+=9;
  doc.setFont('helvetica','normal');doc.setFontSize(10);doc.setTextColor(80,80,80);
  doc.text('Electricity : '+E_IN+' kWh  x  0.82  =  '+ELEC+' kg CO2',24,y);y+=8;
  doc.text('Travel      : '+T_IN+' km   x  0.21  =  '+TRAVEL+' kg CO2',24,y);y+=8;
  doc.text('LPG / Fuel  : '+L_IN+' kg   x  2.98  =  '+LPG+' kg CO2',24,y);y+=8;
  doc.setFont('helvetica','bold');doc.text('Total       : '+TOTAL.toFixed(2)+' kg CO2',24,y);y+=12;
  doc.setFont('helvetica','normal');doc.setDrawColor(200,200,200);doc.line(20,y,190,y);y+=10;
  doc.setFont('helvetica','bold');doc.setFontSize(11);doc.text('Comparison',20,y);y+=9;
  doc.setFont('helvetica','normal');doc.setFontSize(10);doc.setTextColor(80,80,80);
  doc.text('Your: '+TOTAL.toFixed(1)+' kg  |  India avg: '+AVG+' kg',24,y);y+=8;
  doc.setTextColor(VS_AVG==='below'?50:200,VS_AVG==='below'?150:50,VS_AVG==='below'?50:50);
  doc.text(VS_AVG==='below'?'You are '+VS_PCT+'% BELOW average — great job!':'You are '+VS_PCT+'% ABOVE average.',24,y);y+=12;
  doc.setDrawColor(200,200,200);doc.line(20,y,190,y);y+=10;
  doc.setFont('helvetica','bold');doc.setFontSize(11);doc.setTextColor(50,50,50);
  doc.text('Trees to offset: '+TREES+' trees',20,y);
  doc.setFontSize(8);doc.setTextColor(160,160,160);
  doc.text('Sources: IPCC | India CEA 2023 | India avg 167 kg CO2/month',20,278);
  doc.save('carbonwise-report.pdf');
}
</script>
</body>
</html>