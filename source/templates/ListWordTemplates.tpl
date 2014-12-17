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
<td class="small settingsSelectedUI" valign="top" align="left">

    <!-- DISPLAY -->
    <table border="0" cellspacing="0" cellpadding="5" width="100%" class="table table-bordered listViewEntriesTable">
        <form name="massdelete" id="delete" method="POST" action="index.php">
            <input name="module" type="hidden" value="MailMerge">
            <input name="view" type="hidden" value="deleteTemplate">
            <tbody><tr>
                    <td width="50" rowspan="2" valign="top"><img src="modules/MailMerge/images/mailmarge.gif" alt="Settings" width="48" height="48" border="0" title="Settings"></td>
                    <td class="heading2" valign="bottom"><b><a href="index.php?module=Settings&amp;action=index&amp;parenttab=Settings"></a> Mail Merge Templates </b></td>
                </tr>
                <tr>
                    <td valign="top" class="small">Manage Mail Merge templates used in CRM modules</td>
                </tr>
                <tr>
                    <td align="right"></td>
                </tr>
            </tbody></table>
    <br/><br/>
    &nbsp;&nbsp;
    <table border="0" cellspacing="0" cellpadding="5" width="100%" class="listTableTopButtons">
        <tbody><tr>
                <td class="small"><a href="index.php?module=MailMerge&view=mergeModule" style="width:190px;" class="btn btn-info" >Merge Modules</a>&nbsp;&nbsp;</td>
                <td class="small" align="right"><a href="index.php?module=MailMerge&view=mergeFieldList" style="width:190px;" class="btn btn-success" >Module Merge Field List</a>&nbsp;&nbsp;</td>
            </tr>
        </tbody></table>
    &nbsp;&nbsp;
    <br/>
    <table border="0" cellspacing="0" cellpadding="10" width="100%" class="">
        <tbody><tr>
                <td>

                    <table border="0" cellspacing="0" cellpadding="5" width="100%" class="table table-bordered listViewEntriesTable">
                        <tbody><tr>
                                <td class="big"><strong>Mail Merge Templates</strong></td>
                                <td class="small" align="right">&nbsp;
                                </td>
                            </tr>
                        </tbody></table>

                    <table border="0" cellspacing="0" cellpadding="5" width="100%" class="listTableTopButtons">
                        <tbody><tr>
                                <td class="small"><button onclick="" class="btn btn-danger" id="remove">Delete</button></td>
                                <td class="small" align="right"><a href="index.php?module=MailMerge&view=createTemplates" style="background-color: #428bca" class="btn btn-primary" >Add Template</a>&nbsp;&nbsp;</td>
                            </tr>
                        </tbody></table>

                    <table border="0" cellspacing="0" cellpadding="5" width="100%" class="table table-bordered listViewEntriesTable">
                        <tbody><tr class="listViewEntries">
                                <td width="2%" class="colHeader small">#</td>
                                <td width="3%" class="colHeader small"><input type="checkbox" name="check_listall" id="selecctall" value="" /></td>
                                <td width="20%" class="colHeader small"><b>Template File</b></td>
                                <td width="50%" class="colHeader small"><b>Description</b></td>
                                <td width="15%" class="colHeader small"><b>Module</b></td>
                                <td width="15%" class="colHeader small"><b>Tools</b></td>
                                {assign var=COUNTER value=1}
                            </tr>{foreach item=RECORD from=$RECORDS}
                            <tr class="listViewEntries">
                                <td class="listTableRow small" valign="top">{$COUNTER++}</td>
                                <td class="listTableRow small" valign="top"><input type="checkbox" class="listViewEntriesCheckBox" name="selected_id[]" value="{$RECORD['templateid']}"></td>
                                <td class="listTableRow small" valign="top">{$RECORD['filename']}</td>
                                <td class="listTableRow small" valign="top">{$RECORD['description']}&nbsp;</td>
                                <td class="listTableRow small" valign="top">{$RECORD['module']}</td>
                                <td class="listTableRow small" valign="top"><a href="index.php?module=MailMerge&view=mailmergedownloadfile&record={$RECORD['templateid']}" style="color:#0070BA;">Download</a></td>
                            </tr>



                            {/foreach}


                            </tbody></table>
                        <table border="0" cellspacing="0" cellpadding="5" width="100%">
                            <tbody><tr>
                                    <td class="small" nowrap="" align="right"><a href="#top">[Scroll to Top]</a></td>
                                </tr>
                            </tbody></table>
                    </td>
                </tr>
            </tbody></table>



    </td></form>
    {if $ERROR eq '1'}
    <script>alert('Please select at least one template');</script> 
{/if}

<script>$(document).ready(function() {

        $('#selecctall').click(function(event) {  //on click 
            if ($('#'))
                if (this.checked) { // check select status
                    $('.listViewEntriesCheckBox').each(function() { //loop through each checkbox
                        this.checked = true;  //select all checkboxes with class "listViewEntriesCheckBox"               
                    });
                } else {
                    $('.listViewEntriesCheckBox').each(function() { //loop through each checkbox
                        this.checked = false; //deselect all checkboxes with class "listViewEntriesCheckBox"                       
                    });
                }
        });
        $('#remove').click(function() {  //on click 
            if ($('.listViewEntriesCheckBox').length == 0) {
                alert('There is no template ');
                return false;
            }
            var values = [];
            $('.listViewEntriesCheckBox:checked').each(function() {
                values.push($(this).val());//alert($(this).val());
            });
            if (values.length == 0) {
                alert('select a template');
                return false;
            } else {
                if (confirm('Are you sure you want to delete ' + values.length + ' template?')) {
                    document.getElementById('delete').submit();
                } else {
                    $('.listViewEntriesCheckBox').each(function() { //loop through each checkbox
                        this.checked = false; //deselect all checkboxes with class "listViewEntriesCheckBox" 

                    });
                    $('#selecctall').each(function() { //loop through each checkbox
                        this.checked = false; //deselect all checkboxes with class "listViewEntriesCheckBox" 

                    });
                    return false;
                }
            }
        });

    });

</script>
