<h3>Benutzer "{$user.name}"</h3>

{if $saved}<p>Die Änderungen wurden gespeichert.</p>{/if}

<form action="index.php?page=admin&amp;action=user_rights&amp;userid={$user.id}" method="post">
    <input type="hidden" name="filled" value="1" />
    <table class="simple_border fullwidth">
        <tr>
            <th class="heading">Allgemein</th>
        </tr>
        <tr>
            <td>
                <table class="section simple_border fullwidth">
                    <tr>
                        <th>Userlevel<br />
                        <div class="description_small">Das Userlevel des Benutzers. Ein Administrator kann z.B. nur Benutzer bearbeiten,
                        die ein geringeres Userlevel haben, als er selbst.</div></th>
                        <td><input type="text" name="user_level" value="{$user.user_level}" /></td>
                    </tr>
                    <tr>
                        <th>Administrator<br />
                        <div class="description_small">Ob der Benutzer Zugriff auf den Administrationsbereich hat!</div></th>
                        <td><input type="checkbox" name="admin" value="1"{if $user.admin} checked="checked"{/if} /></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th class="heading">Verwaltungs Berechtigungen</th>
        </tr>
        <tr>
            <td>
                <table class="section simple_border fullwidth">
                    <tr>
                        <th>Benutzerverwaltung<br />
                        <div class="description_small">Hat der Benutzer Zugriff auf die Benutzerverwaltung (im Allgemeinen)</div></th>
                        <td><input type="checkbox" name="can_users" value="1"{if $user.can_users} checked="checked"{/if} /></td>
                    </tr>
                    <tr>
                        <th>Benutzer editieren<br />
                        <div class="description_small">Kann der Benutzer andere Benutzer editieren</div></th>
                        <td><input type="checkbox" name="can_users_edit" value="1"{if $user.can_users_edit} checked="checked"{/if} /></td>
                    </tr>
                    <tr>
                        <th>Rechteverwaltung<br />
                        <div class="description_small">Ob der Benutzer die <i>Rechte</i> anderer Benutzer bearbeiten kann</div></th>
                        <td><input type="checkbox" name="can_users_rights" value="1"{if $user.can_users_rights} checked="checked"{/if} /></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th class="heading">Sonstige besondere Rechte</th>
        </tr>
        <tr>
            <td>
                <table class="section simple_border fullwidth">
                    <tr>
                        <th>Massenbearbeitung von Berichten<br />
                            <div class="description_small">
                            Der Benutzer kann die Massenbearbeitung nutzen!<br />
                            <b>Hinweis:</b>Diese Einstellung wirkt sich nicht auf Administratoren aus. Administratoren verfügen unabhängig von dieser Einstellung immer über das Recht der Massenbearbeitung von Berichten!
                            </div>
                        </th>
                        <td>
                            <input type="checkbox" id="can_reports_mass_edit" name="can_reports_mass_edit" value="1"{if $user.can_reports_mass_edit} checked="checked"{/if} /><label for="can_reports_mass_edit">Allgemein</label>
                            <br />
                            &nbsp;&nbsp;<input type="checkbox" id="can_reports_mass_edit_regroup" name="can_reports_mass_edit_regroup" value="1"{if $user.can_reports_mass_edit_regroup} checked="checked"{/if} /><label for="can_reports_mass_edit_regroup">Gruppierung</label>
                            <br />
                            &nbsp;&nbsp;<input type="checkbox" id="can_reports_mass_edit_setworld" name="can_reports_mass_edit_setworld" value="1"{if $user.can_reports_mass_edit_setworld} checked="checked"{/if} /><label for="can_reports_mass_edit_setworld">Weltenzuordnung</label>
                            <br />
                            &nbsp;&nbsp;<input type="checkbox" id="can_reports_mass_edit_delete" name="can_reports_mass_edit_delete" value="1"{if $user.can_reports_mass_edit_delete} checked="checked"{/if} /><label for="can_reports_mass_edit_delete">Löschen</label>
                        </td>
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