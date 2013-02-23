<div style="max-width: 500px;">

    <table class="vis simple_border" style="width: 100%;">
        <tr>
            <th width="140">Betreff</th>
            <th width="300">
                <img src="graphic/dots/red.png" title="" alt="" />
                <span id="label">
                    <span id="labelText">{$report.attacker_nick} greift {$report.defender_village} an</span>
                </span>
            </th>
        </tr>
        
        <tr>
            <td>Gesendet</td>
            <td>{$report.time|date_format:"%d.%m.%Y %H:%M"}</td>
        </tr>
        
        <tr>
            <td colspan="2" valign="top" height="160" style="border: solid 1px black; padding: 5px;">
        
                <h3 style="margin-top: 0px; text-decoration: none;">{if $report.winner == 1}Der Angreifer hat gewonnen{else}Der Verteidiger hat gewonnen{/if}</h3>
        
                <h4>Glück (aus Sicht des Angreifers)</h4>
        
                <table>
                    <tr>
                        {* {math equation="x + y" x=$height y=$width} *}
                                        
                        {if $report.luck <= 0}<td><b>{$report.luck}%</b></td>{/if}
                                        
                        <td><img src="{if $report.luck <= 0}http://dsgfx.bmaker.net/rabe.png{else}http://dsgfx.bmaker.net/rabe_grau.png{/if}" alt="Pech" /></td>
                        
                        <td>
                            <table style="border: 1px solid black;" cellspacing="0" cellpadding="0">
                                <tr>
                                    {if $report.luck <> 0}
                                    <td width="{if $report.luck <= 0}{math equation="(25 + luck) * 2" luck = $report.luck_i}{else}48{/if}" height="12"></td>
                                    <td width="{if $report.luck <= 0}{math equation="-luck * 2" luck=$report.luck_i}{else}0{/if}" style="background-image:url(http://dsgfx.bmaker.net/balken_pech.png);"></td>
                                    <td width="2" style="background-color:rgb(0, 0, 0)"></td>
                                    <td width="{if $report.luck > 0}{math equation="(luck) * 2" luck = $report.luck_i}{else}0{/if}" style="background-image:url(http://dsgfx.bmaker.net/balken_glueck.png);"></td>
                                    <td width="{if $report.luck > 0}{math equation="(25 - luck) * 2" luck = $report.luck_i}{else}48{/if}" height="12"></td>
                                    {else}
                                    <td width="48" height="12"></td>
                                    <td width="0" style="background-image:url(http://dsgfx.bmaker.net/balken_pech.png);"></td>
                                    <td width="2" style="background-color:rgb(0, 0, 0)"></td>
                                    <td width="0" style="background-image:url(http://dsgfx.bmaker.net/balken_glueck.png);"></td>
                                    <td width="48" height="12"></td>
                                    {/if}
                                </tr>
                            </table>
                        </td>
        
                        <td><img src="{if $report.luck > 0}http://dsgfx.bmaker.net/klee.png{else}http://dsgfx.bmaker.net/klee_grau.png{/if}" alt="Glück" /></td>
                                        
                        {if $report.luck > 0}<td><b>{$report.luck}%</b></td>{/if}
                    </tr>
                </table>
        
                <table>
                    <tr>
                        <td><h4 style="margin-top: 0px;">Moral: {$report.moral}%</h4></td>
                    </tr>
                </table>
        
                <br />
        
                <table width="100%" style="border: 1px solid #DED3B9">
                    <tr>
                        <th width="100">Angreifer:</th>
                        <th></th>
                    </tr>
                    <tr>
                        <td>Dorf:</td>
                        <td><span class="ds_link">{$report.attacker_village} ({$report.attacker_coords}){if $report.attacker_continent != -1} K{$report.attacker_continent}{/if}</span></td>
                    </tr>
                    <tr>
                        <td colspan="2">
        
                        <table class="vis">
                            <tr class="center">
                                <td></td>
                                {foreach from=$units item=unit}
                                <td width="35"><img src="http://dsgfx.bmaker.net/unit_{$unit->iname}.png" title="{$unit->name}" alt="" /></td>
                                {/foreach}
                            </tr>
        
                            <tr class="center">
                                <td>Anzahl:</td>
                                {foreach from=$report.units_att item=unit}
                                <td{if $unit == 0} class="hidden"{/if}>  {$unit}</td>
                                {/foreach}
                            </tr>
        
                            <tr class="center">
                                <td>Verluste:</td>
                                {foreach from=$report.units_attl item=unit}
                                <td{if $unit == 0} class="hidden"{/if}>  {$unit}</td>
                                {/foreach}
                            </tr>
                        </table>
                        
                        </td>
                    </tr>
                </table>
        
                <br />
        
                <table width="100%" style="border: 1px solid #DED3B9">
                    <tr>
                        <th width="100">Verteidiger:</th>
                        <th><span class="ds_link">{$report.defender_nick}</span></th>
                    </tr>
                    <tr>
                        <td>Dorf:</td>
                        <td><span class="ds_link">{$report.defender_village} ({$report.defender_coords}){if $report.defender_continent != -1} K{$report.defender_continent}{/if}</span></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table class="vis">
                                <tr class="center">
                                    <td></td>
                                    {foreach from=$units item=unit}
                                    <td width="35"><img src="http://dsgfx.bmaker.net/unit_{$unit->iname}.png" title="{$unit->name}" alt="" /></td>
                                    {/foreach}
                                </tr>
            
                                <tr class="center">
                                    <td>Anzahl:</td>
                                    {foreach from=$report.units_deff item=unit}
                                    <td{if $unit == 0} class="hidden"{/if}>  {$unit}</td>
                                    {/foreach}
                                </tr>
            
                                <tr class="center">
                                    <td>Verluste:</td>
                                    {foreach from=$report.units_deffl item=unit}
                                    <td{if $unit == 0} class="hidden"{/if}>  {$unit}</td>
                                    {/foreach}
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

            <br /><br />
        
            {if $report.spied or $report.buildings or $report.spied_troops_out}
            <h4>Spionage</h4>
            <table style="border: 1px solid #DED3B9">
                <tr>
                        <th>Erspähte Rohstoffe:</th>
                        <td>
                                {if $report.spied}
                                <img src="http://dsgfx.bmaker.net/holz.png" title="Holz" alt="" />{$report.spied_wood}
                                <img src="http://dsgfx.bmaker.net/lehm.png" title="Lehm" alt="" />{$report.spied_loam}
                                <img src="http://dsgfx.bmaker.net/eisen.png" title="Eisen" alt="" />{$report.spied_iron}
                                {else}
                                <i>keine</i>
                                {/if}
                        </td>
                </tr>
                
        
                {if $report.buildings}
                <tr>
                    <th>Gebäude:</th>
                    <td>
                            Hauptgebäude <b>(Stufe {$report.buildings_main})</b><br />
                            {if $report.buildings_barracks > 0}Kaserne <b>(Stufe {$report.buildings_barracks})</b><br />{/if}
                            {if $report.buildings_stable > 0}Stall <b>(Stufe {$report.buildings_stable})</b><br />{/if}
                            {if $report.buildings_garage > 0}Werkstatt <b>(Stufe {$report.buildings_garage})</b><br />{/if}
            
                            {if $report.buildings_snob > 0}Adelshof <b>(Stufe {$report.buildings_snob})</b><br />{/if}
            
                            {if $report.buildings_smith > 0}Schmiede <b>(Stufe {$report.buildings_smith})</b><br />{/if}
                            Versammlungsplatz <b>(Stufe {$report.buildings_place})</b><br />
                            {if $report.buildings_statue > 0}Statue <b>(Stufe {$report.buildings_statue})</b><br />{/if}
                            {if $report.buildings_market > 0}Marktplatz <b>(Stufe {$report.buildings_market})</b><br />{/if}
            
                            {if $report.buildings_wood > 0}Holzfäller <b>(Stufe {$report.buildings_wood})</b><br />{/if}
                            {if $report.buildings_stone > 0}Lehmgrube <b>(Stufe {$report.buildings_stone})</b><br />{/if}
            
                            {if $report.buildings_iron > 0}Eisenmine <b>(Stufe {$report.buildings_iron})</b><br />{/if}
                            Bauernhof <b>(Stufe {$report.buildings_farm})</b><br />
            
                            Speicher <b>(Stufe {$report.buildings_storage})</b><br />
                            Versteck <b>(Stufe {$report.buildings_hide})</b><br />
                            {if $report.buildings_wall > 0}Wall <b>(Stufe {$report.buildings_wall})</b><br />{/if}
                    </td>
                </tr>
                {/if}
            </table>
                
            {if $report.spied_troops_out}
            <table>
                <tr>
                        <th>Einheiten außerhalb:</th>
                </tr>
                <tr>
                        <td>
                                <table>
                                    <tr>
                                        {foreach from=$units item=unit}
                                        <th width="35"><img src="http://dsgfx.bmaker.net/unit_{$unit->iname}.png" title="{$unit->name}" alt="" /></th>
                                        {/foreach}
                                    </tr>
                                    <tr>
                                        {foreach from=$report.units_spied item=unit}
                                        <td{if $unit == 0} class="hidden"{/if}>  {$unit}</td>
                                        {/foreach}
                                    </tr>
                                </table>
                        </td>
                </tr>
            </table>
            {/if}
        
            <br />
            <br />
            {/if}
        
            {if $report.troops_out}
            <h4>Truppen des Verteidigers, die unterwegs waren</h4>
            <table>
                <tr>
                    {foreach from=$units item=unit}
                    <th width="35"><img src="http://dsgfx.bmaker.net/unit_{$unit->iname}.png" title="{$unit->name}" alt="" /></th>
                    {/foreach}
                </tr>
                <tr>
                    {foreach from=$report.units_out item=unit}
                    <td{if $unit == 0} class="hidden"{/if}>  {$unit}</td>
                    {/foreach}
                </tr>
            </table>
            {/if}
        
            {if $report.booty or $report.wall or $report.catapult or $report.mood}
            <table width="100%" class="simple_border">
                {if $report.booty}
                <tr>
                    <th width="100">Beute:</th>
                    <td width="220">
                        <img src="http://dsgfx.bmaker.net/holz.png" alt="Holz" /> {$report.booty_wood}
                        <img src="http://dsgfx.bmaker.net/lehm.png" alt="Lehm" /> {$report.booty_loam}
                        <img src="http://dsgfx.bmaker.net/eisen.png" alt="Eisen" /> {$report.booty_iron}
                    </td>
                    <td>
                        {$report.booty_all}/{$report.booty_max}
                    </td>
                </tr>
                {/if}
            
                {if $report.wall}
                <tr>
                    <th>Schaden durch Rammen:</th>
                    <td>Wall beschädigt von Level <b>{$report.wall_before}</b> auf Level <b>{$report.wall_after}</b></td>
                </tr>
                {/if}
            
                {if $report.catapult}
                <tr>
                    <th>Schaden durch Katapultbeschuss:</th>
                    <td>{$report.catapult_building} beschädigt von Level <b>{$report.catapult_before}</b> auf Level <b>{$report.catapult_after}</b></td>
                </tr>
                {/if}
            
                {if $report.mood}
                <tr>
                    <th>Veränderung der Zustimmung:</th>
                    <td>Zustimmung gesenkt von <b>{$report.mood_before}</b> auf <b>{$report.mood_after}</b></td>
                </tr>
                {/if}
            </table>
            {/if}
        
        </td>
        </tr>
        
    </table>

</div>