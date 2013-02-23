<h3>Berichtegruppen</h3>

{if count($report_groups) > 0}
    {foreach from=$report_groups item=group}
    {$group.name}, <span class="small"><a onclick="return confirm('Diese Gruppe wirklich löschen?');" href="index.php?page=admin&amp;action=report_groups_delete&amp;delete={$group.id}">Löschen</a></span>
    <br />
    {/foreach}
    
    <br />
    <form action="index.php?page=admin&amp;action=report_groups_rename" method="post">
        Umbenennen einer Gruppe:
        {include file='bit_groupselect.tpl' must_select_group=1}
        Neuer Name: <input type="text" name="new_name" value="" /><input type="submit" value="OK" />
    </form>
{else}
<i>Keine Gruppen vorhanden.</i>
{/if}

<p>Beim Löschen einer Gruppe wird die Gruppenzugehörigkeit der zugehörigen Berichte zurückgesetzt.</p>

<h3>Hinzufügen einer Berichtegruppe</h3>

<form action="index.php?page=admin&amp;action=report_groups_add" method="post">
    Name: <input type="text" name="group_name" />
    <br />
    <input type="submit" value="Hinzufügen" />
</form>