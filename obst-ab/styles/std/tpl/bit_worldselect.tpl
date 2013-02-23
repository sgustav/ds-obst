<select name="world">
    {section name=world loop=$obst_user_worlds step=-1}
    <option value="{$obst_user_worlds[world]}">{$obst_user_worlds[world]}</option>
    {/section}
</select>