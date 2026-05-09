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
    <li><a href="/lotto/main">로또번호추천</a></li>
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
  <div>포함할 번호 : <input type="text" name="include_num" value="<?=$include_num?>" oninput="this.value = this.value.replace(/[^0-9,]/g, '').replace(/(\..*)\./g, '$1');" /> ex)10,11</div>
  <div>배제할 번호 : <input type="text" name="exclude_num" value="<?=$exclude_num?>" oninput="this.value = this.value.replace(/[^0-9,]/g, '').replace(/(\..*)\./g, '$1');" /> ex)10,11</div>
  <div style="padding-top:50px;text-align:center;">
    <span style="padding-top:30px;margin-right:10px;"><input type="button" id="signup-button1" class="sowsms-inp-submit" value="추천 (순한맛)" style="height:60px; width:150px; background-color: #337ab7; border-color: #2e6da4; color: #fff;font-size:20px;" onclick="doRecommoned('M');" /></span>
    <span style="padding-top:30px;"><input type="button" id="signup-button2" class="sowsms-inp-submit" value="추천 (매운맛)" style="height:60px; width:150px; background-color: #337ab7; border-color: #2e6da4; color: #fff;font-size:20px;" onclick="doRecommoned('H');" /></span>
  </div>
</div>

<?php if (isset($total_recommend)) { ?>
<p></p>
<p></p>
<div style="padding-top:50px;text-align:center;">
  <?php
      $ii = 0;
      foreach($total_recommend as $lotto_array) {
        $i = 0;

        foreach($lotto_array as $val) {
          if ($i == 0) {
            if (($ii%2)) {
              echo "<div style='border:1px; border-style:dotted;padding-top:5px;padding-bottom:5px;'>";
            } else {
              echo "<div style='border:1px; border-style:dotted;padding-top:5px;padding-bottom:5px;background-color:#e8f5fd'>";
            }
          }
  ?>
    <img src="/images/lotto/<?=$val?>.png" width="11%" height="11%"/>
  <?php
        $i ++;
        if ($i == 6) {
          echo "</div>";
          $i = 0;
        }
      }
      $ii ++;
    }
  ?>
</div>
<?php } ?>
</form>

<script type="text/javascript">
  function doRecommoned(parm) {
    if (parm == 'M') $("#flotto").attr("action","/lotto/roll").submit();
    else if (parm == 'H') $("#flotto").attr("action","/lotto/hot_roll").submit();
  }
</script>

</body>
</html>