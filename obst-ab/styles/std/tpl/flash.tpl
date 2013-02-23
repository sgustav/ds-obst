{include file="header.tpl" tribe=$tribe nonavi=1}

<p>{$message}</p>

<script language="javascript" type="text/javascript">

	setTimeout('location.href="{$redirect}"', 1000);

</script>

{include file="footer.tpl"}