<table class="simple_border">
    <tr>
        <th>Gruppe:</th>
        <td>{$report.group}</td>
    </tr>
    <tr>
        <th>Welt:</th>
        <td>{if $report.world != 0}{$report.world}{else}-{/if}</td>
    </tr>
    <tr>
        <th>Eingelesen von:</th>
        <td>{$report.poster}</td>
    </tr>
    <tr>
        <th>Eingelesen am:</th>
        <td>{$report.realtime|date_format:"%d.%m.%Y, %H:%M"}</td>
    </tr>
</table>
<a href="index.php?page=reports&amp;action=delete&amp;delete={$report.id}" onclick="return checkConfirm('Wirklich löschen?');">Diesen Bericht löschen</a>
<br />

<form action="index.php?page=reports&amp;action=regroup&amp;regroup={$report.id}" method="post">
    Diesen Bericht in die Gruppe
    
    <select name="group">
        <option value="-1">keine</option>
        {foreach from=$report_groups item=group}
        <option value="{$group.id}"{if $report.group_id == $group.id} selected="selected"{/if}>{$group.name}</option>
        {/foreach}
    </select>
    
    einordnen <input type="submit" value="OK" />
</form>

<form action="index.php?page=reports&amp;action=setworld&amp;id={$report.id}" method="post">
    Diesen Bericht der Welt
    
    <select name="world">
        {section name=world loop=$obst.worlds step=-1}
        <option value="{$obst.worlds[world]}"{if $report.world == $obst.worlds[world]} selected="selected"{/if}>{$obst.worlds[world]}</option>
        {/section}
    </select>
    
    zuordnen <input type="submit" value="OK" />
</form>

<br />

<div id="report_box">

{include file="dsbericht.tpl" report=$report}

</div>

<br />

<div id="report_comments_box">

<a id="comments"></a>

{section name=rows loop=$comments_pages step=1}
    {if $comments_pages[rows] != $comments_page}
        <a href="index.php?page=reports&amp;action=view&amp;id={$report.id}&amp;comments_page={$comments_pages[rows]}">[{$comments_pages[rows]}]</a>
    {else}
        <b>[{$comments_pages[rows]}]</b>
    {/if}
{/section}
    
<table class="simple_border fullwidth" id="report_comments_table">
    <tr>
        <th>Kommentare</th>
    </tr>
    <tr>
        <td>
            {if count($comments) > 0}
                {foreach from=$comments item=comment}
                <table class="simple_border report_comment fullwidth">
                    <tr>
                        <td>
                            <b>{$comment.user_name}</b><br />
                            {$comment.time|date_format:"%d.%m.%Y, %H:%M"}
                            {if $is_admin}
                            <br /><a class="small" href="index.php?page=reports&amp;action=delete_comment&amp;delete={$comment.id}&amp;report_id={$report.id}">Löschen</a>
                            {/if}
                        </td>
                        <td style="width: 80%;">
                            {$comment.text}
                        </td>
                    </tr>
                </table>
                {/foreach}
            {else}
            <i>Keine Kommentare bisher.</i>
            {/if}
        </td>
    </tr>
    <tr>
        <td>
            <h3>Kommentar verfassen</h3>
            <form action="index.php?page=reports&amp;action=comment&amp;report_id={$report.id}" method="post">
                <textarea name="comment" id="report_comment_textbox"></textarea>
                <br />
                <input type="submit" value="Abschicken" />
            </form>
        </td>
    </tr>
</table>

</div>


<br />