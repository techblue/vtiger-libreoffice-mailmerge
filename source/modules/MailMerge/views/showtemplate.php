<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('include/database/PearDatabase.php');
require_once('config.php');

class MailMerge_showtemplate_View extends Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
        global $adb;
        $srcModule = $request->get('src_module');
        $viewer = $this->getViewer($request);
        $db = PearDatabase::getInstance();
        $rs = $adb->pquery("select * from vtiger_wordtemplates");
        $result = $adb->pquery("select templateid,filename,description,module from vtiger_wordtemplates");
        $i = 0;
        if (!$result) {
            die('Could not get data: ' . mysql_error());
        }
        $record = array();
        while ($row = $adb->fetch_row($result)) {

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


        $viewer->view('ListWordTemplates.tpl', $request->getModule());
    }

}
