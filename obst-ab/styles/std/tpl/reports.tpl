{include file="header.tpl" tribe=$tribe}

<div id="sub_navi">
    <ul>
        <li><a href="index.php?page=reports">Letzte 10...</a></li>
        <li><a href="index.php?page=reports&amp;action=all">Alle Berichte</a></li>
        <li><a href="index.php?page=reports&amp;action=parse">Bericht einlesen</a></li>
        <li><a href="index.php?page=reports&amp;action=search">Suche</a></li>
    </ul>
</div>

<div class="with_navi" id="content">
    {include file=$content}
</div>

{include file="footer.tpl"}