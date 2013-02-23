<h3>Benutzerübersicht</h3>

{if count($users) > 0}
<table class="simple_border">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>EMail</th>
        <th>Userlevel</th>
        <th>Aktiviert</th>
        <th>Aktionen</th>
    </tr>
{foreach from=$users item=user}
    <tr>
        <td>{$user.id}</td>
        <td>{$user.name}</td>
        <td>{$user.email}</td>
        <td>{$user.user_level}</td>
        <td{if !$user.activated} class="marked"{/if}>{if $user.activated}Ja{else}<span class="warning">Nein</span>{/if}</td>
        <td><a href="index.php?page=admin&amp;action=user_edit&amp;userid={$user.id}">Bearbeiten</a> / 
        <a href="index.php?page=admin&amp;action=user_rights&amp;userid={$user.id}">Verwaltungsrechte</a> /
        <a href="index.php?page=admin&amp;action=user_delete&amp;userid={$user.id}" onclick="return confirm('Willst du diesen Benutzer WIRKLICH löschen?')">Löschen</a></td>
    </tr>
{/foreach}
</table>
{else}
<p style="font-style: italic;">Es gibt keine Benutzer.</p>
{/if}