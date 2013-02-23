<h3>Benutzer "{$user.name}"</h3>

{if $saved}<p>Die Änderungen wurden gespeichert.</p>{/if}

<form action="index.php?page=admin&amp;action=user_edit&amp;userid={$user.id}" method="post">
    <input type="hidden" name="filled" value="1" />
    <table class="simple_border fullwidth">
        <tr>
            <th class="heading">Allgemein</th>
        </tr>
        <tr>
            <td>
                <table class="section simple_border fullwidth">
                    <tr>
                        <th>Name<br />
                        <div class="description_small">Der Name des Benutzers</div></th>
                        <td><input type="text" name="name" value="{$user.name}" /></td>
                    </tr>
                    <tr>
                        <th>EMail<br />
                        <div class="description_small">Die EMail-Addresse des Benutzers</div></th>
                        <td><input type="text" name="email" value="{$user.email}" /></td>
                    </tr>
                    <tr>
                        <th>Neues Passwort<br />
                        <div class="description_small">Wenn ein neues Passwort gesetzt werden soll, dann muss dieses hier eingetragen werden!</div></th>
                        <td><input type="password" name="pass_new" value="" /></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th class="heading">Berechtigungen</th>
        </tr>
        <tr>
            <td>
                <table class="section simple_border fullwidth">
                    <tr>
                        <th>Welten<br />
                        <div class="description_small">Alle Welten, auf die der User Zugriff hat, mit Kommas voneinander getrennt<br />
                        Diese Beschränkung gilt nicht für Administratoren!<br />
                        Wird dieses Feld leer gelassen, so hat der Benutzer Zugriff auf Berichte aller Welten</div></th>
                        <td><input type="text" name="worlds" value="{$user.worlds}" /></td>
                    </tr>
                    <tr>
                        <th>Berichte ansehen<br />
                        <div class="description_small">Ob der Benutzer Berichte ansehen kann</div></th>
                        <td><input type="checkbox" name="can_reports_view" value="1"{if $user.can_reports_view} checked="checked"{/if} /></td>
                    </tr>
                    <tr>
                        <th>Berichte einlesen<br />
                        <div class="description_small">Ob der Benutzer Berichte einlesen kann</div></th>
                        <td><input type="checkbox" name="can_reports_parse" value="1"{if $user.can_reports_parse} checked="checked"{/if} /></td>
                    </tr>
                    <tr>
                        <th>Berichte kommentieren<br />
                        <div class="description_small">Ob der Benutzer Berichte kommentieren kann</div></th>
                        <td><input type="checkbox" name="can_reports_comment" value="1"{if $user.can_reports_comment} checked="checked"{/if} /></td>
                    </tr>
                    <tr>
                        <th>Berichtkommentare löschen<br />
                        <div class="description_small">Ob der Benutzer Kommentare von Berichten löschen kann</div></th>
                        <td><input type="checkbox" name="can_reports_comments_delete" value="1"{if $user.can_reports_comments_delete} checked="checked"{/if} /></td>
                    </tr>
                    <tr>
                        <th>Berichte löschen<br />
                        <div class="description_small">Ob der Benutzer Berichte löschen kann</div></th>
                        <td><input type="checkbox" name="can_reports_delete" value="1"{if $user.can_reports_delete} checked="checked"{/if} /></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th class="heading">Sonstiges</th>
        </tr>
        <tr>
            <td>
                <table class="section simple_border fullwidth">
                    <tr>
                        <th>Aktiviert<br />
                        <div class="description_small">Hiermit kann der Account des Benutzers deaktiviert werden.<br />
                        Der Benutzer kann sich dann nicht mehr einloggen.<br />
                        <b>Wichtig:</b><br />Diese Einstellung wirkt sich nicht auf Administratoren aus!</div></th>
                        <td><input type="checkbox" name="activated" value="1"{if $user.activated} checked="checked"{/if} /></td>
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