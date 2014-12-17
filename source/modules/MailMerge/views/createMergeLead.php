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
require_once('modules/MailMerge/include/utils/MergeUtils.php');
global $app_strings;
global $default_charset;

class MailMerge_createMergeLead_View extends Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
// Fix For: http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/2107
        $randomfilename = "vt_" . str_replace(array(".", " "), "", microtime());

        $templateid = $_REQUEST['document'];
        if (count($_REQUEST['check_list']) == 0) {
            echo "<html>
<body>
<script>

alert('select an entity');
	

</script>";
            //die("Select an entity");
            header('location: index.php?module=Leads&view=List&error=1');
        }
//get the particular file from db and store it in the local hard disk.
//store the path to the location where the file is stored and pass it  as parameter to the method 
        $sql = "select filename,data,filesize from vtiger_wordtemplates where templateid=?";
        $db = PearDatabase::getInstance();
        $result = $db->pquery($sql, array($templateid));
        $temparray = $db->fetch_array($result);

        $fileContent = $temparray['data'];
        $filename = html_entity_decode($temparray['filename'], ENT_QUOTES, $default_charset);
       $originalFileName=$filename;
        $extension = GetFileExtension($filename);
// Fix For: http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/2107
        $filename = $randomfilename . "_mmrg.$extension";

        $filesize = $temparray['filesize'];
        $wordtemplatedownloadpath = "test/wordtemplatedownload/";


        $handle = fopen($wordtemplatedownloadpath . $filename, "wb");
        fwrite($handle, base64_decode($fileContent), $filesize);
        fclose($handle);

        if (GetFileExtension($filename) == "doc") {
            echo "<html>
<body>
<script>
if (document.layers)
{
	document.write(\"This feature requires IE 5.5 or higher for Windows on Microsoft Windows 2000, Windows NT4 SP6, Windows XP.\");
	document.write(\"<br><br>Click <a href='#' onclick='window.history.back();'>here</a> to return to the previous page\");
}	
else if (document.layers || (!document.all && document.getElementById))
{
	document.write(\"This feature requires IE 5.5 or higher for Windows on Microsoft Windows 2000, Windows NT4 SP6, Windows XP.\");
	document.write(\"<br><br>Click <a href='#' onclick='window.history.back();'>here</a> to return to the previous page\");	
}
else if(document.all)
{
	document.write(\"<br><br>Click <a href='#' onclick='window.history.back();'>here</a> to return to the previous page\");
	document.write(\"<OBJECT Name='vtigerCRM' codebase='modules/Settings/vtigerCRM.CAB#version=1,5,0,0' id='objMMPage' classid='clsid:0FC436C2-2E62-46EF-A3FB-E68E94705126' width=0 height=0></object>\");
}
</script>";
        }

//for mass merge
        $merge = $_REQUEST['check_list'];
//echo $mass_merge;
//echo $request->get('check_list');
//echo $_REQUEST['check_list'];
        $c = count($merge);
//echo $c;
        if ($c == 1) {
            $single_record = $merge[0];
        } else {
            $count = 0;
            foreach ($_REQUEST['check_list'] as $selected) {
                $count++;
                if (is_numeric($selected)) {
                    $mass_merge.= $selected;
                    if ($count != $c) {
                        $mass_merge.=";";
                    }
                }
            }
//echo $mass_merge;
        }
//$mass_merge='33;34;35;36';
        if ($mass_merge != "") {
            $mass_merge = explode(";", $mass_merge);
            //array_pop($mass_merge);
            $temp_mass_merge = $mass_merge;
            if (array_pop($temp_mass_merge) == "")
                array_pop($mass_merge);
            //$mass_merge = implode(",",$mass_merge);
        }else if ($single_record != "") {
            $mass_merge = $single_record;
        } else {
            die("Record Id is not found");
        }

//<<<<<<<<<<<<<<<<header for csv and select columns for query>>>>>>>>>>>>>>>>>>>>>>>>

        global $current_user;
        require('user_privileges/user_privileges_' . $current_user->id . '.php');
        if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || $module == "Users" || $module == "Emails") {
            $query1 = "select tablename,columnname,fieldlabel from vtiger_field where tabid=7 and vtiger_field.presence in (0,2) order by tablename";
            $params1 = array();
        } else {
            $profileList = getCurrentUserProfileList();
            $query1 = "select vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldlabel from vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid in (7) AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ") and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid order by vtiger_field.tablename";
            $params1 = array($profileList);
            //Postgres 8 fixes
            if ($db->dbType == "pgsql")
                $query1 = fixPostgresQuery($query1, $log, 0);
        }

        $result = $db->pquery($query1, $params1);
        $y = $db->num_rows($result);
        $userNameSql = getSqlForNameInDisplayFormat(array('first_name' =>
            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

        for ($x = 0; $x < $y; $x++) {
            $tablename = $db->query_result($result, $x, "tablename");
            $columnname = $db->query_result($result, $x, "columnname");
            $querycolumns[$x] = $tablename . "." . $columnname;
            if ($columnname == "smownerid") {
                $querycolumns[$x] = "case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as username,vtiger_users.first_name,vtiger_users.last_name,vtiger_users.user_name,vtiger_users.secondaryemail,vtiger_users.title,vtiger_users.phone_work,vtiger_users.department,vtiger_users.phone_mobile,vtiger_users.phone_other,vtiger_users.phone_fax,vtiger_users.email1,vtiger_users.phone_home,vtiger_users.email2,vtiger_users.address_street,vtiger_users.address_city,vtiger_users.address_state,vtiger_users.address_postalcode,vtiger_users.address_country";
            }
            $field_label[$x] = "LEAD_" . strtoupper(str_replace(" ", "", $db->query_result($result, $x, "fieldlabel")));
            if ($columnname == "smownerid") {
                $field_label[$x] = $field_label[$x] . ",USER_FIRSTNAME,USER_LASTNAME,USER_USERNAME,USER_SECONDARYEMAIL,USER_TITLE,USER_OFFICEPHONE,USER_DEPARTMENT,USER_MOBILE,USER_OTHERPHONE,USER_FAX,USER_EMAIL,USER_HOMEPHONE,USER_OTHEREMAIL,USER_PRIMARYADDRESS,USER_CITY,USER_STATE,USER_POSTALCODE,USER_COUNTRY";
            }
        }
// Ordena etiquetas más grandes primero para que no se sutituyan subcadenas en el documento
// Por ejemplo, pongo LEAD_TIPOVIVIENDA delante de LEAD_TIPO, para que no se sustituya la subcadena LEAD_TIPO
        $labels_length = $field_label;

        function strlength($label, $clave) {
            global $labels_length;
            $labels_length[$clave] = strlen($label);
        }

        array_walk($labels_length, 'strlength');
        array_multisort($labels_length, $field_label, $querycolumns);
        $field_label = array_reverse($field_label);
        $querycolumns = array_reverse($querycolumns);
        $labels_length = array_reverse($labels_length);
        $csvheader = implode(",", $field_label);
//<<<<<<<<<<<<<<<<End>>>>>>>>>>>>>>>>>>>>>>>>

        if (count($querycolumns) > 0) {
            $selectcolumns = implode($querycolumns, ",");

            $query = "select " . $selectcolumns . " from vtiger_leaddetails 
  inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_leaddetails.leadid 
  inner join vtiger_leadsubdetails on vtiger_leadsubdetails.leadsubscriptionid=vtiger_leaddetails.leadid 
  inner join vtiger_leadaddress on vtiger_leadaddress.leadaddressid=vtiger_leadsubdetails.leadsubscriptionid 
  inner join vtiger_leadscf on vtiger_leaddetails.leadid = vtiger_leadscf.leadid 
  left join vtiger_campaignleadrel on vtiger_leaddetails.leadid = vtiger_campaignleadrel.leadid
  left join vtiger_campaignrelstatus on vtiger_campaignrelstatus.campaignrelstatusid = vtiger_campaignleadrel.campaignrelstatusid
  LEFT JOIN vtiger_groups
  	ON vtiger_groups.groupid = vtiger_crmentity.smownerid
  left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
  where vtiger_crmentity.deleted=0 and vtiger_leaddetails.leadid in (" . generateQuestionMarks($mass_merge) . ")";

             $mergevalue=array();
            $actual_values=array();
            
            $result = $db->pquery($query, array($mass_merge));
            $avail_pick_arr = getAccessPickListValues('Leads');
            while ($columnValues = $db->fetch_array($result)) {
                $y = $db->num_fields($result);
                for ($x = 0; $x < $y; $x++) {
                    $value = $columnValues[$x];
                    foreach ($columnValues as $key => $val) {
                        if ($val == $value && $value != '') {
                            if (array_key_exists($key, $avail_pick_arr)) {
                                if (!in_array($val, $avail_pick_arr[$key])) {
                                    $value = "Not Accessible";
                                }
                            }
                        }
                    }
                    //<<<<<<<<<<<<<<< For Blank Fields >>>>>>>>>>>>>>>>>>>>>>>>>>>>
                    if (trim($value) == "--None--" || trim($value) == "--none--") {
                        $value = "";
                    }
                    //<<<<<<<<<<<<<<< End >>>>>>>>>>>>>>>>>>>>>>>>>>>>
                    $actual_values[$x] = $value;
                    $actual_values[$x] = str_replace('"', " ", $actual_values[$x]);
                    //if value contains any line feed or carriage return replace the value with ".value."
                    if (preg_match("/(\r?\n)/", $actual_values[$x])) {
                        // <<< pag 21-Sep-2011 >>>
                        // Replacement see: php.net/manual/en/function.str-replace.php
                        // $str     = "Line 1\nLine 2\rLine 3\r\nLine 4\n";
                        $order = array("\r\n", "\n", "\r"); // order of replacement matters
                        $replace = '!!'; // you choose your appropriate delimiters 
                        // They'll be replaced by an OO/LO macro once the resulting document has been downloaded
                        // We now processes \r\n's first so they aren't converted twice.
                        // $newstr = str_replace($order, $replace, $str);
                        $actual_values[$x] = str_replace($order, $replace, $actual_values[$x]);
                        // <<< pag 21-Sep-2011 END >>>
                        // not needed ??? // $actual_values[$x] = '"'.$actual_values[$x].'"';
                    }
                    $actual_values[$x] = decode_html(str_replace(",", " ", $actual_values[$x]));
                }
                $mergevalue[] = implode(",",$actual_values);
            }
            $csvdata = implode("###",$mergevalue);
        } else {
            die("No vtiger_fields to do Merge");
        }
        echo "<br><br><br>";
        $extension = GetFileExtension($filename);
        if ($extension == "doc") {
            // Fix for: http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/2107
            $datafilename = $randomfilename . "_data.csv";

            $handle = fopen($wordtemplatedownloadpath . $datafilename, "wb");
            fwrite($handle, $csvheader . "\r\n");
            fwrite($handle, str_replace("###", "\r\n", $csvdata));
            fclose($handle);
        } else if ($extension == "odt") {
            //delete old .odt files in the wordtemplatedownload directory
              $randomfilename=$randomfilename.rand(111,999);
    $path=$wordtemplatedownloadpath;
        $wordtemplatedownloadpath.=$randomfilename.'/';
        mkdir($wordtemplatedownloadpath);
    //delete old .odt files in the wordtemplatedownload directory
   foreach (glob("$path*") as $delefile) 
    {
        if(GetFileExtension($delefile)!='zip'){
        unlink($delefile);
        }
      
    }
     /*foreach (glob("$path*") as $delefile) 
    {
       
        if($delefile!=$path.$randomfilename && GetFileExtension($delefile)!='zip' ){
            
            foreach (glob("$delefile/*") as $delefile1) 
    {
        unlink($delefile1);
      
    }
            
          //  echo $delefile;
            @rmdir($delefile);
        }
    }*/
            
            
            //foreach (glob("$wordtemplatedownloadpath/*.odt") as $delefile) {
            //    unlink($delefile);
            //}
            if (!is_array($mass_merge))
                $mass_merge = array($mass_merge);
            foreach ($mass_merge as $idx => $entityid) {
                
                     $rs = $db->pquery("select * from vtiger_leaddetails where leadid=?", array($entityid));

       // $i = 0;
        if ($row = $db->fetch_row($rs)) {
            $fname=$row['firstname'];
            $lname=$row['lastname'];
        }
        
                
                $temp_dir = entpack($filename, $wordtemplatedownloadpath, $fileContent);
                $concontent = file_get_contents($wordtemplatedownloadpath . $temp_dir . '/content.xml');
                unlink($wordtemplatedownloadpath . $temp_dir . '/content.xml');
                $new_filecontent = crmmerge($csvheader, $concontent, $idx, 'htmlspecialchars', $csvdata);
                $stycontent = file_get_contents($wordtemplatedownloadpath . $temp_dir . '/styles.xml');
                unlink($wordtemplatedownloadpath . $temp_dir . '/styles.xml');
                $new_filestyle = crmmerge($csvheader, $stycontent, $idx, 'htmlspecialchars',$csvdata);
                packen($entityid . $filename, $wordtemplatedownloadpath, $temp_dir, $new_filecontent, $new_filestyle);

                //Send Document to the Browser 
                //header("Content-Type: $mimetype");
                //header("Content-Disposition: attachment; filename=$filename");
                //echo file_get_contents($wordtemplatedownloadpath .$filename);
                //readfile($root_directory .$wordtemplatedownloadpath .$filename);
                //latching merged documents with vtiger documents module
          $rs1 = $db->pquery("select max(crmid) as id from vtiger_crmentity");
            if ($row = $db->fetch_row($rs1)) {
            $crmid=$row['id'];
            $crmid=$crmid+1;
        }
        $userid=$current_user->id;
       // echo $crmid;
            $rs2 = $db->pquery("insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime) VALUES('$crmid','$userid','$userid','Documents',NOW(),NOW())");
           // echo $rs2;
           
         $filetype='application/vnd.oasis.opendocument.text';
        $zipfilename=$entityid.$filename;
            
                        $rs10 = $db->pquery("select notesid,note_no from vtiger_notes order by notesid desc");
            if ($row = $db->fetch_row($rs10)) {
            $note_no=$row['note_no'];
            if(substr($note_no, 0,3)=='DOC'){
            $note_no= str_replace('DOC', '', $note_no);
            $note_no=$note_no+1;
            }else{
                $note_no=$row['notesid'];
            }
        }
           
        $note_no='DOC'.($note_no);
        $title=$originalFileName.'_'.$entityid;
        $filesize=  filesize($wordtemplatedownloadpath.$entityid.$filename);
            $rs7 = $db->pquery("insert into vtiger_notes (notesid,note_no,title,filename,notecontent,filetype,filelocationtype,filedownloadcount,filestatus,filesize) VALUES('$crmid','$note_no','$title','$zipfilename','','$filetype','I','0','1','$filesize')");
        
            $rs8 = $db->pquery("insert into vtiger_notescf (notesid) VALUES ('$crmid') ");
            
            $crmid1=$crmid+1;
             $rs2 = $db->pquery("insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime) VALUES('$crmid1','$userid','$userid','Documents Attachment',NOW(),NOW())");
            $rs3 = $db->pquery("update vtiger_crmentity_seq SET id='$crmid1' ");
            
            $rs4 = $db->pquery("select MAX(attachmentsid) as attachmentsid from vtiger_attachments");
            if ($row = $db->fetch_row($rs4)) {
            $attachmentsid=$row['attachmentsid'];
            $attachmentsid=$attachmentsid+1;
        }
     // echo  filetype($path . $randomfilename . '.zip');
        //echo $attachmentsid;
     
        
            $rs5 = $db->pquery("insert into vtiger_attachments (attachmentsid,name,type,path) VALUES('$crmid1','$zipfilename','$filetype','$wordtemplatedownloadpath')");

            
            $rs9 = $db->pquery("insert into vtiger_seattachmentsrel (crmid,attachmentsid) VALUES('$crmid','$crmid1')");
          
            $rs10 = $db->pquery("insert into vtiger_senotesrel (crmid,notesid) VALUES('$entityid','$crmid')");
            
            $newfilepath=$wordtemplatedownloadpath. $crmid1.'_'.$entityid . $filename;
            rename($wordtemplatedownloadpath.$entityid.$filename,$newfilepath );
            
                
                
                
                

                echo "&nbsp;&nbsp;<font size=+1><b><a href=$newfilepath class='btn btn-info' style='width:400px;'>" . 'Download merged document (' .$fname.' '.$lname.')' . "</a></b></font><br>";
                remove_dir($wordtemplatedownloadpath . $temp_dir);
            }
            $zip = new ZipArchive;
            unlink($wordtemplatedownloadpath . $filename);
            $zip->open($path . $randomfilename . '.zip', ZipArchive::CREATE);
            foreach (glob($wordtemplatedownloadpath . "*.odt") as $file) {
                $zip->addFile($file, $file);
            }
            $zip->close();
            
            //latching merged documents with vtiger documents module
          /*$rs1 = $db->pquery("select id from vtiger_crmentity_seq");
            if ($row = $db->fetch_row($rs1)) {
            $crmid=$row['id'];
            $crmid=$crmid+1;
        }
        $userid=$current_user->id;
       // echo $crmid;
            $rs2 = $db->pquery("insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime) VALUES('$crmid','$userid','$userid','Documents',NOW(),NOW())");
           // echo $rs2;
            $rs3 = $db->pquery("update vtiger_crmentity_seq SET id='$crmid' ");
         
            $rs4 = $db->pquery("select MAX(attachmentsid) as attachmentsid from vtiger_attachments");
            if ($row = $db->fetch_row($rs4)) {
            $attachmentsid=$row['attachmentsid'];
            $attachmentsid=$attachmentsid+1;
        }
     // echo  filetype($path . $randomfilename . '.zip');
        //echo $attachmentsid;
     
        $filetype=filetype($path . $randomfilename . '.zip');
        $zipfilename=$randomfilename . '.zip';
            $rs5 = $db->pquery("insert into vtiger_attachments (attachmentsid,name,type,path) VALUES('$attachmentsid','$zipfilename','$filetype','$path')");

           
        $note_no='DOC'.($crmid);
        $title=$randomfilename;
        $filesize=  filesize($path . $randomfilename . '.zip');
            $rs7 = $db->pquery("insert into vtiger_notes (notesid,note_no,title,filename,notecontent,filetype,filelocationtype,filedownloadcount,filestatus,filesize) VALUES('$crmid','$note_no','$title','$zipfilename','','$filetype','I','0','1','$filesize')");
        
            $rs8 = $db->pquery("insert into vtiger_notescf (notesid) VALUES ('$crmid') ");
            
            $rs9 = $db->pquery("insert into vtiger_seattachmentsrel (crmid,attachmentsid) VALUES('$crmid','$attachmentsid')");
           
            rename($path . $randomfilename . '.zip', $path . $attachmentsid.'_' . $randomfilename . '.zip');
            */
            echo "<br/>&nbsp;&nbsp;<font size=+1><b><a class='btn btn-info' style='width:400px;' id='mergeTemplate' href=$path$randomfilename.zip>Download As A Zip</a></b></font><br>";
        
            
            
        } else if ($extension == "rtf") {
          
                      $randomfilename=$randomfilename.rand(111,999);
    $path=$wordtemplatedownloadpath;
        $wordtemplatedownloadpath.=$randomfilename.'/';
        mkdir($wordtemplatedownloadpath);
    //delete old .odt files in the wordtemplatedownload directory
   foreach (glob("$path*") as $delefile) 
    {
        if(GetFileExtension($delefile)!='zip'){
        unlink($delefile);
        }
       
      
    }
/*foreach (glob("$path*") as $delefile) 
    {
       
        if($delefile!=$path.$randomfilename ){
            
            foreach (glob("$delefile/*") as $delefile1) 
    {
        unlink($delefile1);
      
    }
            
          //  echo $delefile;
            @rmdir($delefile);
        }
    }*/
            
            $filecontent = base64_decode($fileContent);
            if (!is_array($mass_merge))
                $mass_merge = array($mass_merge);
            foreach ($mass_merge as $idx => $entityid) {
                
                            $rs = $db->pquery("select * from vtiger_leaddetails where leadid=?", array($entityid));

       // $i = 0;
        if ($row = $db->fetch_row($rs)) {
            $fname=$row['firstname'];
            $lname=$row['lastname'];
        }
                
                $handle = fopen($wordtemplatedownloadpath . $entityid . $filename, "wb");
                $new_filecontent = crmmerge($csvheader, $filecontent, $idx, 'utf8Unicode', $csvdata);
                fwrite($handle, $new_filecontent);
                fclose($handle);
                
                
                //latching merged documents with vtiger documents module
          $rs1 = $db->pquery("select max(crmid) as id from vtiger_crmentity");
            if ($row = $db->fetch_row($rs1)) {
            $crmid=$row['id'];
            $crmid=$crmid+1;
        }
        $userid=$current_user->id;
       // echo $crmid;
            $rs2 = $db->pquery("insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime) VALUES('$crmid','$userid','$userid','Documents',NOW(),NOW())");
           // echo $rs2;
           
         $filetype=  mime_content_type($wordtemplatedownloadpath.$entityid.$filename);
        $zipfilename=$entityid.$filename;
            
                        $rs10 = $db->pquery("select notesid,note_no from vtiger_notes order by notesid desc");
            if ($row = $db->fetch_row($rs10)) {
            $note_no=$row['note_no'];
            if(substr($note_no, 0,3)=='DOC'){
            $note_no= str_replace('DOC', '', $note_no);
            $note_no=$note_no+1;
            }else{
                $note_no=$row['notesid'];
            }
        }
           
        $note_no='DOC'.($note_no);
        $title=$originalFileName.'_'.$entityid;
        $filesize=  filesize($wordtemplatedownloadpath.$entityid.$filename);
            $rs7 = $db->pquery("insert into vtiger_notes (notesid,note_no,title,filename,notecontent,filetype,filelocationtype,filedownloadcount,filestatus,filesize) VALUES('$crmid','$note_no','$title','$zipfilename','','$filetype','I','0','1','$filesize')");
        
            $rs8 = $db->pquery("insert into vtiger_notescf (notesid) VALUES ('$crmid') ");
            
            $crmid1=$crmid+1;
             $rs2 = $db->pquery("insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime) VALUES('$crmid1','$userid','$userid','Documents Attachment',NOW(),NOW())");
            $rs3 = $db->pquery("update vtiger_crmentity_seq SET id='$crmid1' ");
            
            $rs4 = $db->pquery("select MAX(attachmentsid) as attachmentsid from vtiger_attachments");
            if ($row = $db->fetch_row($rs4)) {
            $attachmentsid=$row['attachmentsid'];
            $attachmentsid=$attachmentsid+1;
        }
     // echo  filetype($path . $randomfilename . '.zip');
        //echo $attachmentsid;
     
        
            $rs5 = $db->pquery("insert into vtiger_attachments (attachmentsid,name,type,path) VALUES('$crmid1','$zipfilename','$filetype','$wordtemplatedownloadpath')");

            
            $rs9 = $db->pquery("insert into vtiger_seattachmentsrel (crmid,attachmentsid) VALUES('$crmid','$crmid1')");
          
            $rs10 = $db->pquery("insert into vtiger_senotesrel (crmid,notesid) VALUES('$entityid','$crmid')");
            
            $newfilepath=$wordtemplatedownloadpath. $crmid1.'_'.$entityid . $filename;
            rename($wordtemplatedownloadpath.$entityid.$filename,$newfilepath );
            

            echo "&nbsp;&nbsp;<font size=+1><b><a href=$newfilepath class='btn btn-info' style='width:400px;'>" . 'Download merged document (' .$fname.' '.$lname.')' . "</a></b></font><br>";

            }
             $zip = new ZipArchive;
            unlink($wordtemplatedownloadpath . $filename);
            $zip->open($path . $randomfilename . '.zip', ZipArchive::CREATE);
            foreach (glob($wordtemplatedownloadpath . "*.rtf") as $file) {
                $zip->addFile($file, $file);
            }
            $zip->close();

            echo "<br/>&nbsp;&nbsp;<font size=+1><b><a class='btn btn-info' style='width:400px;' id='mergeTemplate' href=$path$randomfilename.zip>Download As A Zip</a></b></font><br>";
        
            
        } else {
            die("unknown file format");
        }
    }

}
?>

<script>
    if (window.ActiveXObject) {
        try
        {
            ovtigerVM = eval("new ActiveXObject('vtigerCRM.ActiveX');");
            if (ovtigerVM)
            {
                var filename = "<?php echo $filename ?>";
                if (filename != "")
                {
                    if (objMMPage.bDLTempDoc("<?php echo $site_URL ?>/test/wordtemplatedownload/<?php echo $filename ?>", "MMTemplate.doc"))
                                        {
                                            try
                                            {
                                                if (objMMPage.Init())
                                                {
                                                    objMMPage.vLTemplateDoc();
                                                    objMMPage.bBulkHDSrc("<?php echo $site_URL; ?>/test/wordtemplatedownload/<?php echo $datafilename ?>");
                                                                                    objMMPage.vBulkOpenDoc();
                                                                                    objMMPage.UnInit()
                                                                                    window.history.back();
                                                                                }
                                                                            } catch (errorObject)
                                                                            {
                                                                                document.write("Error while processing mail merge operation");
                                                                            }
                                                                        } else
                                                                        {
                                                                            document.write("Cannot get template document");
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            catch (e) {
                                                                document.write("Requires to download ActiveX Control from vtigerCRM. Please, ensure that you have administration privilage");
                                                            }
                                                        }
</script>
</body>
</html>
