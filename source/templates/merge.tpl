{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
<form action="index.php" method="post" enctype="multipart/form-data" id="merge">

    <br/>{if $COUNT eq '0'}
    <a href="index.php?module=CreateMergeTemplate&view=createTemplates">Create MergeTemplate</a>
    {else}
        <b>Select template to merge: </b><select name="document">
            {foreach item=RECORD from=$RECORDS}
                <option value="{$RECORD['templateid']}">{$RECORD['filename']}</option>
            {/foreach}
        </select><br/>   

        <input type="hidden" name="module" value="{$MODULE}">
        <input type="hidden" name="view" value="Merge">
        <br/>
        <button title="Save" accesskey="S" type="button" onclick="document.getElementById('merge').submit();" class="btn btn-primary" id="merged">Merge</button>

    </form>

    <script>
        //$('#merged').bind(click);
        $('#merged').click(function() {
            var values = [];
            $('.listViewEntriesCheckBox:checked').each(function() {
                values.push($(this).val());//alert($(this).val());
            });
            if (values.length == 0) {
                alert('select an entity');
                return false;
            }
        });



    </script>{/if}
