{* gibt alle Fehlermeldungen aus *}
{if count($errors)>0}
	<p>Folgende Fehler sind aufgetreten:</p>
	<ul style="color: #DD2121;">
	{foreach from=$errors item=msg}
		<li>{$msg}</li>
	{/foreach}
	</ul>
{/if}