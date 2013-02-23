<h3>Suche</h3>

<form action="index.php?page=reports&amp;action=search" method="post">
    <input type="hidden" name="filled" value="1" />
    
    <table class="simple_border fullwidth">
        <tr>
            <th class="heading"><input type="checkbox" name="search_general" value="yes"{if $search.general!='no'} checked="checked"{/if} />Allgemeine Parameter</th>
        </tr>
        <td>
            <table class="section">
                <tr>
                    <th>Angreifer</th>
                    <td><input type="text" name="attacker_nick" value="{$search.attacker_nick}" />
                    <select onchange="if(this.value == '---') document.forms[0].elements['attacker_nick'].value = ''; else document.forms[0].elements['attacker_nick'].value = this.value;">
                        <option>---</option>
                        {foreach from=$usernames item=user}
                        <option value="{$user.name}"{if $user.name == $search.attacker_nick} selected="selected"{/if}>{$user.name}</option>
                        {/foreach}
                    </select>
                    </td>
                </tr>
                    <th>Verteidiger</th>
                    <td><input type="text" name="defender_nick" value="{$search.defender_nick}" />
                    <select onchange="if(this.value == '---') document.forms[0].elements['defender_nick'].value = ''; else document.forms[0].elements['defender_nick'].value = this.value;">
                        <option>---</option>
                        {foreach from=$usernames item=user}
                        <option value="{$user.name}"{if $user.name == $search.defender_nick} selected="selected"{/if}>{$user.name}</option>
                        {/foreach}
                    </select>
                    </td>
                </tr>
                <tr>
                    <th>Angreifer Koordinaten<br />
                    <div class="description_small">Koordinaten ohne Klammern angeben!</div></th>
                    <td><input type="text" name="attacker_coords" value="{$search.attacker_coords}" /></td>
                </tr>
                    <th>Verteidiger Koordinaten<br />
                    <div class="description_small">Koordinaten ohne Klammern angeben!</div></th>
                    <td><input type="text" name="defender_coords" value="{$search.defender_coords}" /></td>
                </tr>
                </tr>
                    <th>Gruppe<br />
                    <div class="description_small">Um nur in einer bestimmten Gruppe von Berichten zu suchen!</div></th>
                    <td>
                        <select name="group_id">
                            <option value="-1">-egal-</option>
                            {foreach from=$report_groups item=group}
                            <option value="{$group.id}"{if $search.group_id == $group.id} selected="selected"{/if}>{$group.name}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="center">
            <input type="submit" value="Suchen" /> <input type="reset" value="Zurücksetzen" />
        </td>
    </tr>
    </table>
</form>

{if isset($reports)}
<br />
{include file='reports_list.tpl'}
{/if}