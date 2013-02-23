<select name="group">
    {if !isset($must_select_group)}<option value="-1">keine</option>{/if}
    {foreach from=$report_groups item=group}
    <option value="{$group.id}">{$group.name}</option>
    {/foreach}
</select>