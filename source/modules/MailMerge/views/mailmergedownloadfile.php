<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
ob_start();
require_once('config.php');
require_once('include/database/PearDatabase.php');
require_once('modules/MailMerge/include/utils/MergeUtils.php');
global $fileId, $default_charset;
class MailMerge_mailmergedownloadfile_View extends Vtiger_Index_View {

	public function process(Vtiger_Request $request) {
$templateid = $_REQUEST['record'];
$randomfilename = "vt_" . str_replace(array("."," "), "", microtime());


$sql = "select filename,data,filesize,filetype from vtiger_wordtemplates where templateid=?";
$db = PearDatabase::getInstance();
$result = $db->pquery($sql, array($templateid));
$temparray = $db->fetch_array($result);
$fileType=$temparray['filetype'];
$fileContent = $temparray['data'];
$filename=html_entity_decode($temparray['filename'], ENT_QUOTES, $default_charset);
$extension=GetFileExtension($filename);
$wordtemplatedownloadpath ="test/wordtemplatedownload/templates/";
if(!is_dir($wordtemplatedownloadpath)){
mkdir($wordtemplatedownloadpath,0777);
}
  foreach (glob("$wordtemplatedownloadpath*") as $delefile) 
    {
        
        unlink($delefile);
     
      
    }
$filesize=$temparray['filesize'];



$handle = fopen($wordtemplatedownloadpath .$filename,"wb");
fwrite($handle,base64_decode($fileContent),$filesize);
fclose($handle);


header('location:'.$wordtemplatedownloadpath.$filename);


        }
}
?>
