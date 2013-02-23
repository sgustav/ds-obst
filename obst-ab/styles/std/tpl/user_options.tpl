{if $saved}<p>Die Änderungen wurden gespeichert.{if $password_changed} <b>Wichtig:</b> Dein Passwort wurde ebenfalls geändert!{/if}</p>{/if}

<form action="index.php?page=user&amp;action=options" method="post">
    <input type="hidden" name="filled" value="1" />
    <table class="user_edit_main simple_border fullwidth">
        <tr>
            <th class="heading">Allgemein</th>
        </tr>
        <tr>
            <td>
                <table class="section simple_border fullwidth">
                    <tr>
                        <th>EMail<br />
                        <div class="description_small">Deine EMail-Addresse</div></th>
                        <td><input type="text" name="email" value="{$user.email}" /></td>
                    </tr>
                    <tr>
                        <th>Neues Passwort<br />
                        <div class="description_small">Wenn ein neues Passwort gesetzt werden soll, dann muss dieses hier eingetragen werden!</div></th>
                        <td><input type="password" name="pass_new" value="" /></td>
                    </tr>
                    <tr>
                        <th>Neues Passwort bestätigen<br />
                        <div class="description_small">siehe oben</div></th>
                        <td><input type="password" name="pass_new_confirm" value="" /></td>
                    </tr>
                    <tr>
                        <th>Altes Passwort<br />
                        <div class="description_small">Um dein Passwort zu ändern musst du hier dein altes Passwort eingeben!</div></th>
                        <td><input type="password" name="pass_old" value="" /></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="center">
            <td>
                <input type="submit" value="Speichern" />
                <input type="reset" value="Zurücksetzen" />
            </td>
        </tr>
    </table>
</form>