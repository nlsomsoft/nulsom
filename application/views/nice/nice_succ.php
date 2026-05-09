<html>
<head>
    <title>NICE평가정보 - CheckPlus 본인인증 테스트</title>
</head>
<body>
    <center>
    <p><p><p><p>
    <?=$ret_msg?><br>
    </center>
</body>
</html>

<?php if ($errcode == '0') { ?>
<script type="text/javascript">
    window.onload=function()
    {
        try
        {
            opener.location.replace("/signup/join_form");
            window.close();
        }
        catch(e)
        {
            alert(e); // 정상적인 부모창의 iframe 를 못찾은 경우임
        }
    }
</script>
<?php } ?>