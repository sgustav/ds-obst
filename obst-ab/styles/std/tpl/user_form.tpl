<form action="index.php?page=user&amp;action=dologin" method="post">
    <input type="hidden" name="redirect" value="{$redirect}" />
    
    {if !$obst.online}
    <p><span class="warning">Hinweis: </span>OBST ist momentan deaktiviert. Nur Administratoren können sich einloggen!</p>
    {/if}
	<table border="0">
		<tr>
			<td>Benutzername</td>
			<td><input type="text" name="user" value="" /></td>
		</tr>
		<tr>
			<td>Passwort</td>
			<td><input type="password" name="pass" value="" /></td>
		</tr>
	</table>
	<br />
	
	<input type="submit" name="login" value="Login" />
</form>

<p>
    <a href="index.php?page=user&amp;action=register">&gt; Registrieren</a>
</p>