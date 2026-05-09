<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title></title>
  <script src="https://code.jquery.com/jquery-latest.min.js"></script>
</head>

<style>
  * {
    margin:0;
    padding:0;
  }
  body {
    font:17px 'Nanum Gothic'.sans-serif;
  }
    a {
    text-decoration:none;
    color:#fff;
  }
  #menu1 {
    width:100%;
    height:50px;
    ouline: 1px dotted red;
  }
  #menu1 ul li {
    float:left;
    width:50%;
    height:100%;
    line-height:50px;
    text-align:center;
    background:#232F3E;
    list-style:none;
    list-style-type: none;
  }
  #menu1 ul li a {
    display:block;
  }
  #menu1 ul li a:hover {
    background:#37475A;
    color:#fff;
  }
</style>

<body>
<div id="menu1">
  <ul>
    <li><a href="/lotto">로또번호추천</a></li>
    <li><a href="/lotto/winning">로또당첨번호</a></li>
  </ul>
</div>

<?php
$attributes = array(
    'id' => 'flotto',
    'method' => 'post',
);
echo form_open('lotto/roll', $attributes);
?>
<div style="padding-top:50px;text-align:center;">
  <div><input type="text" name="auth_num" id="auth_num" value="" placeholder="   인 증 번 호" style="padding-left:5px;font-size:20px;height:50px;width:200px;letter-spacing:5px" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');" /></div>
  <div style="padding-top:50px;text-align:center;">
    <span style="padding-top:30px;"><input type="button" id="signup-button1" class="sowsms-inp-submit" value="인증하기" style="height:60px; width:150px; background-color: #337ab7; border-color: #2e6da4; color: #fff;font-size:20px;" onclick="doRecommoned('M');" /></span>
  </div>
</div>


</form>

<script type="text/javascript">
  function doRecommoned(parm) {
    if ($("#auth_num").val().trim() == '') {
      alert("인증번호를 입력하세요.");
      return;
    }
    $("#flotto").attr("action","/lotto/login").submit();
  }
</script>

</body>
</html>