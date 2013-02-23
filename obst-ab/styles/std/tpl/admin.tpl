{include file="header.tpl" tribe=$tribe nonavi=1}

<div id="sub_navi">
    <ul>
        <li><a href="index.php?page=admin&amp;action=users_list">Benutzerverwaltung</a></li>
        <li><a href="index.php?page=admin&amp;action=report_groups">Berichtegruppen</a></li>
    </ul>
</div>

<div id="content" class="with_navi">
    {include file=$content}
</div>

{include file="footer.tpl" hide_admin_link=1}