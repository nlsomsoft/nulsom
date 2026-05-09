
<?php if ($this->session->flashdata('notice')) { ?>
<script type="text/javascript">
$(document).ready(function(){
    alert("<?=$this->session->flashdata('notice')?>");
});
</script>
<?php } ?>
<footer id="ft">
    <p>
        Copyright &copy; <?=DOMAIN?>. All rights reserved.<br>
    </p>
</footer>
</html>