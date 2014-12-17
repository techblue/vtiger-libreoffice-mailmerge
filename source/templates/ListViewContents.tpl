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
<table border="0" cellspacing="0" cellpadding="5" width="100%" class="display dataTable">
    <tbody><tr>
            <td width="50" rowspan="2" valign="top"><img src="modules/MailMerge/images/mailmarge.gif" alt="Settings" width="48" height="48" border="0" title="Settings"></td>
            <td class="heading2" valign="bottom"><b><a href="index.php?module=Settings&amp;action=index&amp;parenttab=Settings"></a> Mail Merge Templates </b></td>
        </tr>
        <tr>
            <td valign="top" class="small">Manage Mail Merge templates used in CRM modules</td>
        </tr>
    </tbody></table>

<form action="index.php" id="upload" method="post" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="3000000">
    <input type="hidden" name="view" value="savewordtemplate">
    <input type="hidden" name="module" value="MailMerge">
    <table border="0" cellspacing="0" cellpadding="10" width="100%">
        <tbody><tr>
                <td>

                    <table border="0" cellspacing="0" cellpadding="5" width="100%" class="tableHeading">
                        <tbody><tr>
                                <td class="big"><strong>New Template</strong><br><br></td>

                            </tr>
                        </tbody></table>

                    <table border="0" class="table table-bordered listViewEntriesTable" cellspacing="0" cellpadding="5" width="100%">
                        <tbody><tr valign="top">
                                <td nowrap="" class="cellLabel small"><font color="red">*</font><strong> Template File</strong></td>
                                <td class="cellText small"><strong>
                                        <input type="file" name="binFile" class="small">
                                        <input type="hidden" name="binFile_hidden" value="">
                                    </strong></td>
                            </tr>
                            <tr>
                                <td valign="top" class="small cellLabel"><strong>Description</strong></td>
                                <td class="cellText small" valign="top"><textarea name="txtDescription" class="small" style="width:90%;height:50px"></textarea></td>
                            </tr>
                            <tr>
                                <td valign="top" class="small cellLabel"><strong>Module</strong></td>
                                <td class="cellText small" valign="top">
                                    <select name="target_module" size="1" class="small" tabindex="3">
                                        <option value="Leads">Leads</option>
                                        <option value="Accounts">Organizations</option>
                                        <option value="Contacts">Contacts</option>
                                        <option value="HelpDesk">Trouble Tickets</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody></table>
                    <table border="0" cellspacing="0" cellpadding="5" width="100%">
                        <tbody><tr>
                                <td class="small" nowrap="" align="right"><a href="#top">[Scroll to Top]</a></td>
                            </tr>
                        </tbody></table>
                </td>
            </tr>
        </tbody></table>
    <td class="small" align="right">
        <button title="Save" accesskey="S" type="button" onclick="document.getElementById('upload').submit();" tabindex="4" name="button" style="background-color: #428bca" class="btn btn-primary">save</button>&nbsp;
        &nbsp;<a  href="index.php?module=MailMerge&view=showtemplate" name="button"  class="btn btn-info">Cancel</a>
    </td>

</form><br/>
{if $ERROR eq '1'}
    <script>alert('File type not supported');</script> 
{/if}
{if $ERROR eq '2'}
    <script>alert('Please choose a file');</script>
{/if}
