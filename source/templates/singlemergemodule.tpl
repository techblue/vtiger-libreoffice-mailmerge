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
<br>
{if $COUN eq '0'}
              
                <a href="index.php?module=MailMerge&view=createTemplates" style="background-color: #428bca" class="btn btn-primary">Create MergeTemplate</a>
                {else}
                    <form action="index.php" id='merge'>
                    &nbsp;&nbsp;<b>Select template to merge this record: &nbsp;&nbsp;</b><select name="document">

                        {foreach item=RECORD from=$TEMPLATES}
                            <option value="{$RECORD['templateid']}">{$RECORD['filename']}</option>
                        {/foreach}

                    </select>&nbsp;&nbsp;&nbsp;&nbsp;

                    
                    <b>Latch with Document: </b>&nbsp;&nbsp;
                    <input type="checkbox" name="latch_doc[]" class="" value="yes" checked="checked" />
                      

                    <br/>   
                    <input type="hidden" name="check_list[]" value="{$RECOR}">

                    <input type="hidden" name="module" value="MailMerge">
                    <input type="hidden" name="view" value="{$MERGEVIEW}">
                    <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="document.getElementById('merge').submit();" class="btn btn-primary" style="background-color: #428bca" id="merged">Merge</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </form>
                {/if}
                    <script>$(document).ready(function() {
                            //$('#merged').bind(click);
                            $('#merged').click(function() {
                                var values = [];
                                $('.listViewEntriesCheckBox:checked').each(function() {
                                    values.push($(this).val());//alert($(this).val());
                                });
                               // if (values.length == 0) {
                                  //  alert('select an entity');
                                   // return false;
                                //} else {
                                $('#merged').hide();
                                    $('.btn-info').hide();
                                    return true;
                                //}
                            });


                            $('#selecctall').click(function(event) {  //on click 
                                if (this.checked) { // check select status
                                    $('.listViewEntriesCheckBox').each(function() { //loop through each checkbox
                                        this.checked = true;  //select all checkboxes with class "checkbox1"               
                                    });
                                } else {
                                    $('.listViewEntriesCheckBox').each(function() { //loop through each checkbox
                                        this.checked = false; //deselect all checkboxes with class "checkbox1"                       
                                    });
                                }
                            });

                        });

                    </script>
