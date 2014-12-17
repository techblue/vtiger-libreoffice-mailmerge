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
//require_once('include/utils/MergeUtils.php');
//global $adb;
//  $srcModule = $request->get('src_module');
//$viewer = $this->getViewer($request);
$db = PearDatabase::getInstance();
$rs = $db->pquery("select * from vtiger_wordtemplates");
if ($row = $db->fetch_row($rs)) {
    $record = 1;
}
$merge = $_REQUEST['selected_id'];
//echo $mass_merge;
//echo $request->get('check_list');
//echo $_REQUEST['check_list'];
$c = count($merge);
//echo $c;
$deleted = 0;
if ($c == 1) {
    $single_record = $merge[0];
    $result = $db->pquery("delete from vtiger_wordtemplates where templateid in($single_record)");
    $deleted = 1;
} else {
    $count = 0;
    foreach ($_REQUEST['selected_id'] as $selected) {
        $count++;
        if (is_numeric($selected)) {
            $mass_merge.= $selected;
            if ($count != $c) {
                $mass_merge.=",";
            }
        }
    }
    $result = $db->pquery("delete from vtiger_wordtemplates where templateid in($mass_merge)");
    $deleted = 1;
//echo $mass_merge;
}
//$error=0;
if ($c == 0 && $record == 1) {
    $error = '&error=1';
}


//echo $row["firstname"];

header('Location:index.php?module=MailMerge&view=showtemplate' . $error);

//$viewer->view('ListViewContents.tpl', $request->getModule());
	
