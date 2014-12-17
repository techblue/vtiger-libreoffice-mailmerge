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

class MailMerge_Leads_View extends Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
        global $adb;
        $srcModule = $request->get('src_module');
        $viewer = $this->getViewer($request);
        $db = PearDatabase::getInstance();

        $rec_limit = 20;
        /* Get total number of records */
        $search = $_REQUEST['search'];
        if (isset($_REQUEST['search'])) {
            $sql = "SELECT count(*) FROM vtiger_leaddetails inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_leaddetails.leadid 
  inner join vtiger_leadsubdetails on vtiger_leadsubdetails.leadsubscriptionid=vtiger_leaddetails.leadid 
  inner join vtiger_leadaddress on vtiger_leadaddress.leadaddressid=vtiger_leadsubdetails.leadsubscriptionid 
  inner join vtiger_leadscf on vtiger_leaddetails.leadid = vtiger_leadscf.leadid 
  left join vtiger_campaignleadrel on vtiger_leaddetails.leadid = vtiger_campaignleadrel.leadid
  left join vtiger_campaignrelstatus on vtiger_campaignrelstatus.campaignrelstatusid = vtiger_campaignleadrel.campaignrelstatusid
  LEFT JOIN vtiger_groups
  	ON vtiger_groups.groupid = vtiger_crmentity.smownerid
  left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
  where vtiger_crmentity.deleted=0 and (upper(vtiger_leaddetails.firstname) like '$search%')";
            // $page=$_REQUEST['go']-1;
        } else {
            $sql = "SELECT count(*) FROM vtiger_leaddetails inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_leaddetails.leadid 
  inner join vtiger_leadsubdetails on vtiger_leadsubdetails.leadsubscriptionid=vtiger_leaddetails.leadid 
  inner join vtiger_leadaddress on vtiger_leadaddress.leadaddressid=vtiger_leadsubdetails.leadsubscriptionid 
  inner join vtiger_leadscf on vtiger_leaddetails.leadid = vtiger_leadscf.leadid 
  left join vtiger_campaignleadrel on vtiger_leaddetails.leadid = vtiger_campaignleadrel.leadid
  left join vtiger_campaignrelstatus on vtiger_campaignrelstatus.campaignrelstatusid = vtiger_campaignleadrel.campaignrelstatusid
  LEFT JOIN vtiger_groups
  	ON vtiger_groups.groupid = vtiger_crmentity.smownerid
  left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
  where vtiger_crmentity.deleted=0 ";
        }
        $retval = $db->pquery($sql);
        if (!$retval) {
            die('Could not get data: ' . mysql_error());
        }
        $row = $db->fetch_array($retval, MYSQL_NUM);
        $rec_count = $row[0];
        $count = intval($rec_count / $rec_limit);
        if ($rec_count % $rec_limit != 0) {
            $count = $count + 1;
        }
        if ($count != 0) {
            $start = 1;
        }
        if (isset($_REQUEST['page'])) {
            $page = $_REQUEST['page'] + 1;
            $offset = $rec_limit * $page;
        } else {
            $page = 0;
            $offset = 0;
        }
        if ($count == $page + 2) {
            //$page=$page-1;
        }

        $left_rec = $rec_count - ($page * $rec_limit); //echo $left_rec;
        if ($left_rec == 0 || $left_rec < 0) {
            $page = $_REQUEST['page'];
            $offset = $rec_limit * $page;
        }
        if (isset($_REQUEST['search'])) {
               $sql = "SELECT vtiger_leaddetails.leadid,vtiger_leaddetails.company,vtiger_leaddetails.firstname,vtiger_leaddetails.lastname,vtiger_leaddetails.email,vtiger_leaddetails.designation FROM vtiger_leaddetails inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_leaddetails.leadid 
  inner join vtiger_leadsubdetails on vtiger_leadsubdetails.leadsubscriptionid=vtiger_leaddetails.leadid 
  inner join vtiger_leadaddress on vtiger_leadaddress.leadaddressid=vtiger_leadsubdetails.leadsubscriptionid 
  inner join vtiger_leadscf on vtiger_leaddetails.leadid = vtiger_leadscf.leadid 
  left join vtiger_campaignleadrel on vtiger_leaddetails.leadid = vtiger_campaignleadrel.leadid
  left join vtiger_campaignrelstatus on vtiger_campaignrelstatus.campaignrelstatusid = vtiger_campaignleadrel.campaignrelstatusid
  LEFT JOIN vtiger_groups
  	ON vtiger_groups.groupid = vtiger_crmentity.smownerid
  left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
  where vtiger_crmentity.deleted=0 and (upper(vtiger_leaddetails.firstname) like '$search%') LIMIT $offset, $rec_limit";
        } else {
            $sql = "SELECT vtiger_leaddetails.leadid,vtiger_leaddetails.company,vtiger_leaddetails.firstname,vtiger_leaddetails.lastname,vtiger_leaddetails.email,vtiger_leaddetails.designation FROM vtiger_leaddetails inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_leaddetails.leadid 
  inner join vtiger_leadsubdetails on vtiger_leadsubdetails.leadsubscriptionid=vtiger_leaddetails.leadid 
  inner join vtiger_leadaddress on vtiger_leadaddress.leadaddressid=vtiger_leadsubdetails.leadsubscriptionid 
  inner join vtiger_leadscf on vtiger_leaddetails.leadid = vtiger_leadscf.leadid 
  left join vtiger_campaignleadrel on vtiger_leaddetails.leadid = vtiger_campaignleadrel.leadid
  left join vtiger_campaignrelstatus on vtiger_campaignrelstatus.campaignrelstatusid = vtiger_campaignleadrel.campaignrelstatusid
  LEFT JOIN vtiger_groups
  	ON vtiger_groups.groupid = vtiger_crmentity.smownerid
  left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
  where vtiger_crmentity.deleted=0 ".
       "LIMIT $offset, $rec_limit";
        }
        $result = $db->pquery($sql);


//if( $page > 0 )
//{

        $last = $page - 2;
        if ($last < 0) {
            $last = -1; //$page=0;
        }

//$rs  = $db->pquery("select * from vtiger_contactdetails");
//$result=  mysql_query("select leadid,company,firstname,lastname,email,designation from vtiger_leaddetails limit ".$start.",".$length);
        $i = 0;
        $record = array();




        while ($row = $db->fetchByAssoc($result)) {

            $record[$i] = $row;
            $i++;
        }
        $error = $request->get('error');
//echo $error;
        if (isset($error)) {
            $viewer->assign('ERROR', $error);
        }

        // if($moduleName=='Leads' || $moduleName=='Accounts' || $moduleName=='Contacts' || $moduleName=='HelpDesk'){
        // $db = PearDatabase::getInstance();
        $rs = $db->pquery("select templateid,filename from vtiger_wordtemplates where module=?", array($_REQUEST['view']));

        $i = 0;
        while ($row = $db->fetchByAssoc($rs)) {
            $templates[$i] = $row;
            $i++;
        }

        $viewer->assign('TEMPLATES', $templates);
        $viewer->assign('COUN', $i);
        //}
        $alphabets = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
//echo $row["firstname"];
        if ($rec_count <= $rec_limit) {
            $viewer->assign('LIMIT', 0);
        }
        $viewer->assign('RECORDS', $record);
        $viewer->assign('SRCMODULE', $srcModule);
        $viewer->assign('COUNT', count($record));
        $viewer->assign('NEXT', $page);
        $viewer->assign('PREV', $last);
        $viewer->assign('TOTALPAGES', $count);
        $viewer->assign('START', $start);
        $viewer->assign('ALPHABETS', $alphabets);
        $viewer->assign('SEARCH', $search);
        $viewer->view('mergeLeadContent.tpl', $request->getModule());
    }

}
