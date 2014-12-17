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
<br/><br/>&nbsp;&nbsp;&nbsp;<b>
    <font size="2">Select Module To Get Merge Field List</font>
</b>&nbsp;&nbsp;&nbsp;
<select name="fieldList" id="list_select_id">
    <option value="">Select Module</option>
    <option value="Leads">Leads</option>
    <option value="Accounts">Organizations</option>
    <option value="Contacts">Contacts</option>
    <option value="HelpDesk">Trouble Tickets</option>

</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a  href="index.php?module=MailMerge&view=showtemplate" name="button" style="margin-top:-10px;"  class="btn btn-info">Cancel</a>
<div id="list"></div>
<script>
    $(document).ready(function($) {


        $('#list_select_id').change(function(e) {
            //Grab the chosen value on first select list change
            var selectvalue = $(this).val();
            //alert(selectvalue);
            //Display 'loading' status in the target select list
            // $('#'+list_target_id).html('<option value="">Loading...</option>');

            if (selectvalue == "") {
                $('#list').html('<table class="table table-bordered listViewEntriesTable"><thead><tr class="" style="font-size: small"><td>No Module Selected</td></tr></thead></table>');

                //Display initial prompt in target select if blank value selected
                //$('#'+list_target_id).html(initial_target_html);
            } else {
                //Make AJAX request, using the selected value as the GET
                jQuery.ajax({
                    type: "GET",
                    url: "index.php?module=MailMerge&view=getList&name=" + selectvalue,
                    // data:dataString,
                    success: function(responseData) {
                        //alert(responseData); //Enable for Debugging
                        $('#list').html(responseData);
                    }
                });
            }
        });
    });

</script>
