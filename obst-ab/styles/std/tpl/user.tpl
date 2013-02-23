{include file="header.tpl"}

{if !$nosubnavi}
<div id="sub_navi">
    <ul>
        <li><a href="index.php?page=user&amp;action=options">Optionen</a></li>
    </ul>
</div>
{/if}

<div {if !$nosubnavi}class="with_navi" {/if}id="content">
{include file=$content}
</div>

{include file="footer.tpl"}