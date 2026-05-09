<!doctype html>
<html lang="ko">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title>인증번호 입력</title>
<style>
:root {
--bg: #0b1020;
--panel: #121b2f;
--line: #243554;
--text: #e6efff;
--muted: #8ea3c9;
--accent: #4da3ff;
--accent-2: #2f81f7;
}

* {
box-sizing: border-box;
}

html, body {
margin: 0;
padding: 0;
width: 100%;
min-height: 100%;
background: linear-gradient(180deg, #0b1020 0%, #10182d 100%);
font-family: -apple-system, BlinkMacSystemFont, "Apple SD Gothic Neo", "Noto Sans KR", sans-serif;
color: var(--text);
}

body {
display: flex;
align-items: center;
justify-content: center;
padding: 20px 16px;
}

.wrap {
width: 100%;
max-width: 420px;
}

.card {
background: var(--panel);
border: 1px solid var(--line);
border-radius: 18px;
padding: 18px 16px 16px;
box-shadow: 0 14px 34px rgba(0, 0, 0, 0.24);
}

.input {
width: 100%;
height: 52px;
border: 1px solid var(--line);
border-radius: 12px;
background: #17233d;
color: var(--text);
padding: 0 14px;
font-size: 16px;
outline: none;
margin-bottom: 12px;
}

.input::placeholder {
color: var(--muted);
}

.input:focus {
border-color: var(--accent);
box-shadow: 0 0 0 3px rgba(77, 163, 255, 0.16);
}

.btn {
width: 100%;
height: 52px;
border: 0;
border-radius: 12px;
background: linear-gradient(135deg, var(--accent-2), #5ba4ff);
color: #fff;
font-size: 16px;
font-weight: 800;
cursor: pointer;
}

.btn:active {
transform: translateY(1px);
}
</style>
<script src="https://code.jquery.com/jquery-latest.min.js"></script>
</head>
<body>
<div class="wrap">
<div class="card" style="margin-top:200px;">

<?php
$attributes = array(
    'id' => 'fstock',
    'method' => 'post',
);
echo form_open('stock/login', $attributes);
?>

<input
type="text"
name="auth_num"
id="auth_num"
class="input"
placeholder="인증번호"
inputmode="numeric"
autocomplete="one-time-code"
/>
<button type="submit" class="btn" onclick="doRecommoned();">인증하기</button>
</form>
</div>
</div>

<script type="text/javascript">
  function doRecommoned() {
    if ($("#auth_num").val().trim() == '') {
      alert("인증번호를 입력하세요.");
      return;
    }
    $("#fstock").attr("action","/stock/login").submit();
  }
</script>

</body>
</html>


