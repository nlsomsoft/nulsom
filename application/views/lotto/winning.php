<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title></title>
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

<?php if (isset($winning_array)) { ?>
<p></p>
<p></p>

<div style="text-align:center;margin-top:50px;"><span style="font-size:20px;"><?=$divide?>회 당첨번호</span> <span style="font-size:15px;">추첨일(<?=$drawing_date?>)</span></div>
<div style="padding-top:50px;text-align:center;">
    <img src="/images/lotto/<?=$winning_array[0]?>.png" width="10%" height="10%"/>
    <img src="/images/lotto/<?=$winning_array[1]?>.png" width="10%" height="10%"/>
    <img src="/images/lotto/<?=$winning_array[2]?>.png" width="10%" height="10%"/>
    <img src="/images/lotto/<?=$winning_array[3]?>.png" width="10%" height="10%"/>
    <img src="/images/lotto/<?=$winning_array[4]?>.png" width="10%" height="10%"/>
    <img src="/images/lotto/<?=$winning_array[5]?>.png" width="10%" height="10%"/>
    <img src="/images/lotto/ico_add.png" />
    <img src="/images/lotto/<?=$bonus_array[0]?>.png" width="10%" height="10%"/>
</div>

<p></p>
<p></p>


<br />
<br />
<table style="border: 1px solid; margin: auto; text-align: center;">
  <tr style="height:40px;background-color:#f4f4f4;">
    <td width="16%">순위</td>
    <td>추천번호</td>
    <td width="16%">비고</td>
  </tr>
<?php
    foreach ($winning as $row) {
      $num_array = array();
      $num_array = explode(',', $row->num);
?>
  <tr>
    <td><?=$row->grade?></td>
    <td>
      <span>
        <img src="/images/lotto/<?=$num_array[0]?>.png" width="11%" height="11%"/>
        <img src="/images/lotto/<?=$num_array[1]?>.png" width="11%" height="11%"/>
        <img src="/images/lotto/<?=$num_array[2]?>.png" width="11%" height="11%"/>
        <img src="/images/lotto/<?=$num_array[3]?>.png" width="11%" height="11%"/>
        <img src="/images/lotto/<?=$num_array[4]?>.png" width="11%" height="11%"/>
        <img src="/images/lotto/<?=$num_array[5]?>.png" width="11%" height="11%"/>
      </span>
    </td>
    <td>
    <?php if ($row->type == '1') { ?>
        <img src="/images/lotto/hot.png" width="40%" height="40%"/>
    <?php } ?>
    </td>
  </tr>
<?php
  }
?>
</table>
<br />

<?php } ?>

</body>
</html>