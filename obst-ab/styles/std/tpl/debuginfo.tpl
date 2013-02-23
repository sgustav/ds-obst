{* gibt alle Debugmeldungen aus *}
{if $show_debuginfo}
    {if count($debuginfo)>0}
    	<p>Debuginformationen:</p>
    	<ul style="color: #21AA21;">
    	{foreach from=$debuginfo item=msg}
    		<li>{$msg}</li>
    	{/foreach}
    	</ul>
    {/if}
{/if}