{if !$no_page_select}
<p>
    Seite:
    {section name=rows loop=$pages step=1}
        {if $pages[rows] != $page}
            <a href="index.php?page=reports&amp;action=all&amp;p={$pages[rows]}">[{$pages[rows]}]</a>
        {else}
            <b>[{$pages[rows]}]</b>
        {/if}
    {/section}
</p>
{/if}

<form action="index.php?page=reports&amp;action=mass_edit" method="post">
    {if count($reports) > 0}
    <table class="simple_border" style="width: 100%;">
        <tr>
            <th title="Wann der Bericht eingelesen wurde..." width="100">Eingelesen am</th>
            <th title="Wann der Angriff stattgefunden hat..." width="100">Datum</th>
            <th title="Betreff">Betreff</th>
            <th>Zusammenfassung</th>
            <th title="Welt">Welt</th>
            <th title="Gruppe des Berichts">Gruppe</th>
            <th>Angreifer</th>
            <th>Verteidiger</th>
            {if $obst_user->can('reports_mass_edit')}<th>Ausw.</th>{/if}
        </tr>
    {foreach from=$reports item=report}
        <tr>
            <td>{$report.realtime|date_format:"%d.%m.%Y, %H:%M"}</td>
            <td>{$report.time|date_format:"%d.%m.%Y, %H:%M"}</td>
            <td><img src="styles/std/graphic/dots/{$report.dot}.png" title="" alt="" class=""> {if $report.lastcomment > $obst_user->getVal('lastlogin')}! {/if}<a href="index.php?page=reports&amp;action=view&amp;id={$report.id}">{$report.attacker_nick} greift {$report.defender_village} an</a></td>
            <td>{$report.sumary}</td>
            <td>{if $report.world != 0}{$report.world}{else}-{/if}</td>
            <td>{$report.group}</td>
            <td>{$report.attacker_nick}</td>
            <td>{$report.defender_nick}</td>
            {if $obst_user->can('reports_mass_edit')}<td><input type="checkbox" name="select_{$report.id}" /></td>{/if}
        </tr>
    {/foreach}
    </table>
    {else}
    <p style="font-style: italic;">Es entsprechen keine Berichte deinen Angaben.</p>
    {/if}
    {if $is_admin or $obst_user->can('reports_mass_edit')}
    <p>
        Alle markierten Berichte...<br />
        {if $is_admin or $obst_user->can('reports_mass_edit_regroup')}
            <input type="radio" name="mass_edit_action" value="regroup" checked="checked" />in die Gruppe {include file='bit_groupselect.tpl'} einordnen<br />
        {/if}
        {if $is_admin or $obst_user->can('reports_mass_edit_setworld')}
            <input type="radio" name="mass_edit_action" value="setworld" />der Welt {include file='bit_worldselect.tpl'} zuordnen<br />
        {/if}
        {if $is_admin or $obst_user->can('reports_mass_edit_delete')}
            <input type="radio" name="mass_edit_action" value="delete" />löschen <input type="checkbox" name="delete_sure" value="yes" />sicher!<br />
        {/if}
        <input type="submit" value="OK" />
    </p>
    {/if}
</form>
