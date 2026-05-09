<!doctype html>
<html lang="ko">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title>NLSOM IVESTMENT</title>
<style>
:root {
--bg: #0b1020;
--panel: #121b2f;
--panel-2: #17233d;
--line: #243554;
--text: #e6efff;
--muted: #8ea3c9;
--accent: #4da3ff;
--green: #1fc98a;
--red: #ff6b7d;
--yellow: #f0b429;
--buy: #2dd881;
--blue: #5b8cff;
}

* { box-sizing: border-box; }

body {
margin: 0;
background: linear-gradient(180deg, #0b1020 0%, #10182d 100%);
color: var(--text);
font-family: -apple-system, BlinkMacSystemFont, "Apple SD Gothic Neo", "Noto Sans KR", sans-serif;
}

.wrap {
width: 100%;
max-width: 720px;
margin: 0 auto;
padding: 16px 14px 32px;
}

.top {
margin-bottom: 14px;
}

.title {
font-size: 22px;
font-weight: 800;
letter-spacing: -0.02em;
margin: 0 0 6px;
}

.sub {
color: var(--muted);
font-size: 13px;
margin: 0;
}

.toolbar {
display: flex;
gap: 8px;
align-items: center;
margin: 16px 0 18px;
}

.date-input {
flex: 1;
background: var(--panel);
color: var(--text);
border: 1px solid var(--line);
border-radius: 12px;
padding: 12px 14px;
font-size: 15px;
outline: none;
}

.btn {
border: 0;
border-radius: 12px;
background: var(--accent);
color: white;
font-size: 14px;
font-weight: 700;
padding: 12px 14px;
cursor: pointer;
white-space: nowrap;
}

.summary {
display: grid;
grid-template-columns: repeat(2, 1fr);
gap: 10px;
margin-bottom: 14px;
}

.summary-card {
background: var(--panel);
border: 1px solid var(--line);
border-radius: 14px;
padding: 14px;
}

.summary-label {
font-size: 12px;
color: var(--muted);
margin-bottom: 6px;
}

.summary-value {
font-size: 20px;
font-weight: 800;
}

.list {
display: flex;
flex-direction: column;
gap: 12px;
}

.card {
background: var(--panel);
border: 1px solid var(--line);
border-radius: 16px;
padding: 14px;
box-shadow: 0 10px 30px rgba(0,0,0,0.18);
}

.row-top {
display: flex;
justify-content: space-between;
align-items: flex-start;
gap: 10px;
margin-bottom: 10px;
}

.name-box {
min-width: 0;
flex: 1;
}

.ticker {
font-size: 15px;
color: #ffd700;
margin-bottom: 4px;
}

.name {
font-size: 18px;
font-weight: 800;
line-height: 1.3;
word-break: keep-all;
}

.score-badge {
min-width: 66px;
text-align: center;
border-radius: 999px;
padding: 8px 10px;
font-size: 15px;
font-weight: 800;
color: white;
background: linear-gradient(135deg, #355cdd, #5b8cff);
flex-shrink: 0;
}
.score-badge1 {
min-width: 66px;
text-align: center;
border-radius: 999px;
padding: 8px 10px;
font-size: 15px;
font-weight: 800;
color: white;
background: linear-gradient(135deg, #355cdd, #2dd881);
flex-shrink: 0;
}

.meta-grid {
display: grid;
grid-template-columns: repeat(2, 1fr);
gap: 10px;
margin-bottom: 12px;
}

.meta-item {
background: var(--panel-2);
border-radius: 12px;
padding: 10px 12px;
}

.meta-label {
font-size: 11px;
color: var(--muted);
margin-bottom: 5px;
}

.meta-value {
font-size: 15px;
font-weight: 700;
}

.gap-up { color: var(--red); }
.gap-down { color: var(--blue); }
.gap-flat { color: var(--yellow); }

.select-row {
display: flex;
align-items: center;
justify-content: space-between;
gap: 12px;
padding-top: 6px;
border-top: 1px solid var(--line);
}

.check-label {
display: flex;
align-items: center;
gap: 10px;
font-size: 14px;
color: var(--text);
font-weight: 600;
}

.check-label input[type="checkbox"] {
width: 18px;
height: 18px;
accent-color: var(--buy);
}

.buy-btn {
width: 100%;
margin-top: 18px;
border: 0;
border-radius: 14px;
background: linear-gradient(135deg, #21c87a, #34e29c);
color: #062814;
font-size: 16px;
font-weight: 800;
padding: 15px 16px;
cursor: pointer;
box-shadow: 0 10px 24px rgba(33, 200, 122, 0.28);
}

.empty {
background: var(--panel);
border: 1px dashed var(--line);
border-radius: 16px;
padding: 28px 16px;
text-align: center;
color: var(--muted);
font-size: 14px;
}

.footer-note {
margin-top: 14px;
color: var(--muted);
font-size: 12px;
text-align: center;
}

.hidden { display: none; }
</style>
<style>
.signal-box {
margin: 12px 0 16px;
padding: 16px;
border-radius: 14px;
border: 1px solid #22314f;
background: linear-gradient(180deg, #121b2f 0%, #0f1628 100%);
box-shadow: 0 4px 16px rgba(6, 12, 28, 0.28);
}

.signal-head {
display: flex;
justify-content: space-between;
align-items: center;
gap: 12px;
margin-bottom: 10px;
flex-wrap: wrap;
}

.signal-badge {
display: inline-flex;
align-items: center;
justify-content: center;
min-width: 92px;
padding: 6px 12px;
border-radius: 999px;
font-weight: 800;
font-size: 13px;
border: 1px solid transparent;
}

.signal-total-score {
font-size: 14px;
font-weight: 800;
color: #dbe7ff;
}

.signal-detail {
font-size: 13px;
color: #c9d8f5;
margin-bottom: 12px;
}

.signal-grid {
display: grid;
grid-template-columns: repeat(6, minmax(90px, 1fr));
gap: 8px;
}

.signal-item {
background: rgba(255,255,255,0.04);
border: 1px solid #22314f;
border-radius: 10px;
padding: 10px 8px;
text-align: center;
}

.signal-item span {
display: block;
font-size: 11px;
color: #8ea3c9;
margin-bottom: 4px;
}

.signal-item strong {
font-size: 15px;
color: #f4f8ff;
}

.signal-red {
border-color: rgba(255,93,115,.45);
background: linear-gradient(180deg, rgba(255,93,115,.16) 0%, rgba(255,93,115,.06) 100%);
}

.signal-red .signal-badge {
color: #2a0c12;
background: linear-gradient(180deg, #ff9aa8 0%, #ff5d73 100%);
border-color: #ffc1ca;
}

.signal-yellow {
border-color: rgba(240,180,41,.45);
background: linear-gradient(180deg, rgba(240,180,41,.16) 0%, rgba(240,180,41,.06) 100%);
}

.signal-yellow .signal-badge {
color: #1a1202;
background: linear-gradient(180deg, #ffd166 0%, #f0b429 100%);
border-color: #ffe29a;
}

.signal-green {
border-color: rgba(36,192,138,.45);
background: linear-gradient(180deg, rgba(36,192,138,.16) 0%, rgba(36,192,138,.06) 100%);
}

.signal-green .signal-badge {
color: #08130b;
background: linear-gradient(180deg, #3df2a4 0%, #24c08a 100%);
border-color: #69ffc0;
}

@media (max-width: 960px) {
.signal-grid {
grid-template-columns: repeat(3, minmax(90px, 1fr));
}
}

@media (max-width: 520px) {
.signal-grid {
grid-template-columns: repeat(2, minmax(90px, 1fr));
}
}
</style>


<script src="https://code.jquery.com/jquery-latest.min.js"></script>
</head>
<body>
<div class="wrap">
<div class="top">
<h1 class="title">NLSOM IVESTMENT</h1>
<p class="sub">모바일 전용 · 샘플 데이터 화면</p>
</div>

<?php
$attributes = array(
    'id' => 'fstockmanager',
    'method' => 'post',
);
echo form_open('stock/trigger', $attributes);
?>
<input type="hidden" name="xid" id="xid" value="<?=(int)$result1->xid?>" />
<input type="hidden" name="std_yyyymmdd" id="std_yyyymmdd" value="<?=$yyyymmdd?>" />
<input type="hidden" name="set_trigger" id="set_trigger" />

<div style="margin-bottom:12px; padding:12px 14px; border:1px solid #243554; border-radius:14px; background:#121b2f; display:flex; align-items:center; justify-content:space-between; gap:12px;">
<div>
<div style="font-size:14px; font-weight:800; color:#e6efff; margin-bottom:4px;">오늘 거래</div>
<div id="todayTradeStatusText" style="font-size:12px; color:#8ea3c9;">거래 진행</div>
</div>
</form>

<label style="position:relative; display:inline-block; width:54px; height:30px; flex-shrink:0;">
<input id="todayTradeToggle" type="checkbox" style="opacity:0; width:0; height:0;" <?=($result1->trigger == '1' ? 'checked' : '')?> />
<span id="todayTradeSlider" style="
position:absolute;
cursor:pointer;
inset:0;
background:#21c87a;
border-radius:999px;
transition:0.2s;
box-shadow: inset 0 0 0 1px rgba(255,255,255,0.08);
"></span>
<span id="todayTradeKnob" style="
position:absolute;
height:24px;
width:24px;
left:3px;
top:3px;
background:white;
border-radius:50%;
transition:0.2s;
box-shadow:0 2px 8px rgba(0,0,0,0.25);
"></span>
</label>
</div>

<div class="toolbar">
<input id="dateFilter" class="date-input" type="date" />
<button id="btnSearch" class="btn" onClick="selectedDate()">조회</button>
</div>

<script>
  function selectedDate() {
    window.location.replace("/stock/main/"+$("#dateFilter").val().trim());
  }
</script>


<div id="signalBox" class="signal-box" style="display:none;">
<div class="signal-head">
<span id="signalLightBadge" class="signal-badge">신호등</span>
<span id="signalTotalScore" class="signal-total-score">총점 0</span>
</div>

<div class="signal-detail" id="signalSummary">
시장 신호등 요약
</div>

<div class="signal-grid">
<div class="signal-item"><span>EWY</span><strong id="sigEwy">0</strong></div>
<div class="signal-item"><span>SOX</span><strong id="sigSox">0</strong></div>
<div class="signal-item"><span>NASDAQ</span><strong id="sigNasdaq">0</strong></div>
<div class="signal-item"><span>WTI</span><strong id="sigWti">0</strong></div>
<div class="signal-item"><span>USD/KRW</span><strong id="sigUsdkrw">0</strong></div>
<div class="signal-item"><span>미국10년물</span><strong id="sigUs10y">0</strong></div>
</div>
</div>


<?php

  $stock_cnt = 0;
  $final_score = 0;
  foreach ($result as $row) {
    $final_score += (float)$row->final_score;
    $stock_cnt ++;
  }
  
  if (!$stock_cnt) $average_score = 0;
  else $average_score = round($final_score/$stock_cnt);
?>

<div class="summary">
<div class="summary-card">
<div class="summary-label">추천 종목 수</div>
<div id="countValue" class="summary-value"><?=$stock_cnt?>건</div>
</div>
<div class="summary-card">
<div class="summary-label">평균 최종점수</div>
<div id="avgScoreValue" class="summary-value"><?=$average_score?></div>
</div>
</div>

<div id="sampleDate" class="footer-note" style="margin-top:0;margin-bottom:12px;text-align:left;">
기준일: <?=$yyyymmdd?>
</div>


<?php
$attributes = array(
    'name' => 'fbiglucky',
    'id' => 'fbiglucky',
    'onsubmit' => 'return fbiglucky_submit(this);'
);
echo form_open('stock/buy', $attributes);
?>
<input type="hidden" name="buy_yyyymmdd" id="buy_yyyymmdd" value="<?=$yyyymmdd?>" />
<div id="list" class="list">
<?php

  foreach ($result as $row) {
?>
<div class="card">
<div class="row-top">
<div class="name-box">
<div class="ticker"><?=$row->ticker?></div>
<div class="name"><?=$row->name?></div>
</div>
<div class="score-badge"><?=$row->final_score?></div>
</div>

<div class="meta-grid">

<div class="meta-item">
<div class="meta-label">테마</div>
<div class="meta-value gap-flat"><?=$row->theme?></div>
</div>
<div class="meta-item">
<div class="meta-label">이유</div>
<div class="meta-value"><?=$row->reasons?></div>
</div>
</div>

<div class="select-row">
<label class="check-label">
<input type="checkbox" name="buyTicker[]" value="<?=$row->ticker?>" <?=($row->participation == '1' ? 'checked' : '')?> />
<span>매수 선택</span>
</label>
</div>
</div>

<?php
  }
?>

</div>

<button class="buy-btn">매수하기</button>

</form>


<div class="footer-note">표시 항목: 종목코드 · 종목명 · 최종점수 · 등락율 · 이유</div>
</div>

<script>
const dateFilter = document.getElementById('dateFilter');
const btnSearch = document.getElementById('btnSearch');
const sampleDate = document.getElementById('sampleDate');

const today = "<?=$yyyymmdd?>";
dateFilter.value = today;
sampleDate.textContent = `기준일: ${today}`;

function applyDateOnly() {
const selected = dateFilter.value || today;
sampleDate.textContent = `기준일: ${selected}`;
}

btnSearch.addEventListener('click', applyDateOnly);
dateFilter.addEventListener('change', applyDateOnly);
</script>
<script>
(function () {
var toggle = document.getElementById('todayTradeToggle');
var statusText = document.getElementById('todayTradeStatusText');
var slider = document.getElementById('todayTradeSlider');
var knob = document.getElementById('todayTradeKnob');

function renderTradeToggle() {
if (toggle.checked) {
statusText.textContent = '거래 진행';
statusText.style.color = '#21c87a';
slider.style.background = '#21c87a';
knob.style.transform = 'translateX(24px)';
} else {
statusText.textContent = '거래 보류';
statusText.style.color = '#ff6b7d';
slider.style.background = '#4b556b';
knob.style.transform = 'translateX(0)';
}
}

function renderTradeToggle1() {
if (toggle.checked) {
// statusText.textContent = '거래 진행';
// statusText.style.color = '#21c87a';
// slider.style.background = '#21c87a';
// knob.style.transform = 'translateX(24px)';
$("#set_trigger").val('on');
} else {
// statusText.textContent = '거래 보류';
// statusText.style.color = '#ff6b7d';
// slider.style.background = '#4b556b';
// knob.style.transform = 'translateX(0)';
$("#set_trigger").val('off');
}
$("#fstockmanager").attr("action","/stock/trigger").submit();
}

toggle.addEventListener('change', renderTradeToggle1);
renderTradeToggle();
})();

function fbiglucky_submit() {
  var isSeasonChk = $("input:checkbox[name='buyTicker[]']").is(":checked");

  if(!isSeasonChk){
      alert("매수할 종목을 선택해주세요.");
      return false;
  }
  return true;
}
</script>
<script>
const signal = {
signal_light: '<?=$result1->signal_light?>',
total_score: <?=$result1->total_score?>,
ewy_score: <?=$result1->ewy_score?>,
sox_score: <?=$result1->sox_score?>,
nasdaq_score: <?=$result1->nasdaq_score?>,
wti_score: <?=$result1->wti_score?>,
usdkrw_score: <?=$result1->usdkrw_score?>,
us10y_score: <?=$result1->us10y_score?>
};
renderSignalBox(signal);

function renderSignalBox(signal) {
const box = document.getElementById('signalBox');
if (!signal || !signal.signal_light) {
box.style.display = 'none';
return;
}

const light = String(signal.signal_light).toUpperCase();
const badge = document.getElementById('signalLightBadge');
const total = document.getElementById('signalTotalScore');
const summary = document.getElementById('signalSummary');

box.className = 'signal-box';
if (light === 'RED') box.classList.add('signal-red');
else if (light === 'GREEN') box.classList.add('signal-green');
else box.classList.add('signal-yellow');
const label = light === 'RED' ? '빨간불' : light === 'GREEN' ? '초록불' : '노란불';

badge.textContent = label;
total.textContent = `총점 ${Number(signal.total_score ?? 0).toFixed(1)}`;
summary.textContent = `시장 신호등: ${label} / 외부요인 종합 점수 기준`;

document.getElementById('sigEwy').textContent = Number(signal.ewy_score ?? 0).toFixed(1);
document.getElementById('sigSox').textContent = Number(signal.sox_score ?? 0).toFixed(1);
document.getElementById('sigNasdaq').textContent = Number(signal.nasdaq_score ?? 0).toFixed(1);
document.getElementById('sigWti').textContent = Number(signal.wti_score ?? 0).toFixed(1);
document.getElementById('sigUsdkrw').textContent = Number(signal.usdkrw_score ?? 0).toFixed(1);
document.getElementById('sigUs10y').textContent = Number(signal.us10y_score ?? 0).toFixed(1);

box.style.display = 'block';
}
</script>
</body>
</html>
