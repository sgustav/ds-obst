{if !$onlybody}
{include file='header.tpl'}
{/if}

{include file="errors.tpl" errors=$errors}
{include file="debuginfo.tpl" debuginfo=$debuginfo}

<p><a href="javascript:history.back()">&gt; Zurück</a></p>

{if !$onlybody}
{include file='footer.tpl'}
{/if}