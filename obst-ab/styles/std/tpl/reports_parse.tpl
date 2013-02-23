<p>Auf dieser Seite kannst du einen Bericht einlesen. Danach steht er dem ganzen Stamm zur Verfügung.</p>

<form action="index.php?page=reports&amp;action=parse" method="post" name="parse">
	<input type="hidden" name="filled" value="1" />

	{include file="errors.tpl" error=$error}
	{include file="debuginfo.tpl" debuginfo=$debuginfo}

	<p>Bitte füge den Bericht hier ein:</p>

	<textarea name="report" cols="70" rows="15">{$report}</textarea>
	
        <p>Beachte, dass du den Bericht ab "Betreff" kopieren solltest. Alternativ kannst du auch STRG-A drücken und die gesamte Seite hier einfügen.</p>
        <br />
        
        Gruppe des Berichts:
        {include file='bit_groupselect.tpl'}
        <br />
        
        Welt:
        {include file='bit_worldselect.tpl'}
        <br />
        
	<input type="submit" value="Einlesen" />
</form>