<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class MailMerge_createTemplates_View extends Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
        global $adb;
        $srcModule = $request->get('src_module');
        $viewer = $this->getViewer($request);
        $db = PearDatabase::getInstance();
        $rs = $db->pquery("select * from vtiger_contactdetails");
        $result = $db->pquery("select contactid,contact_no,firstname,lastname,email,phone,title,mobile from vtiger_contactdetails");
        $i = 0;
        $record = array();
        while ($row = $db->fetch_row($result)) {

            $record[$i] = $row;
            $i++;
        }
        $error = $request->get('error');
//echo $error;
        if (isset($error)) {
            $viewer->assign('ERROR', $error);
        }

//echo $row["firstname"];

        $viewer->assign('RECORDS', $record);
        $viewer->assign('SRCMODULE', $srcModule);
        $viewer->assign('COUNT', count($record));


        $viewer->view('ListViewContents.tpl', $request->getModule());
    }

}
