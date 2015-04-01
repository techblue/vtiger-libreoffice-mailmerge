<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class MailMerge_Accounts_View extends Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
        global $adb;
        $srcModule = $request->get('src_module');
        $viewer = $this->getViewer($request);
        $db = PearDatabase::getInstance();
      


        $rec_limit = 20;
        /* Get total number of records */
        $search = $_REQUEST['search'];
          if($_REQUEST['submit']=='Search'){
            $search='%'.$_REQUEST['search'];
        }

        
        if (isset($_REQUEST['search'])) {
            $sql = "SELECT DISTINCT count(*) FROM vtiger_account inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid 
				inner join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid 
				inner join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid 
				inner join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid 
				left join vtiger_account as vtiger_accountAccount on vtiger_accountAccount.accountid = vtiger_account.parentid
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups 
					ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_contactdetails on vtiger_contactdetails.accountid=vtiger_account.accountid
				left join vtiger_crmentity as vtiger_crmentityContacts on vtiger_crmentityContacts.crmid = vtiger_contactdetails.contactid 
				left join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid 
				left join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid 
				left join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid 
				left join vtiger_customerdetails on vtiger_contactdetails.contactid = vtiger_customerdetails.customerid 
				left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
				left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid 
				left join vtiger_users as usersContacts on usersContacts.id = vtiger_crmentityContacts.smownerid
				LEFT JOIN vtiger_groups as groupsContacts
					ON groupsContacts.groupid = vtiger_crmentity.smownerid
				where vtiger_crmentity.deleted=0 and (vtiger_crmentityContacts.deleted=0 || vtiger_crmentityContacts.deleted is null) and (upper(vtiger_account.accountname) like '$search%') ";
        } else {
           $sql = "SELECT DISTINCT count(*) FROM vtiger_account inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid 
				inner join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid 
				inner join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid 
				inner join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid 
				left join vtiger_account as vtiger_accountAccount on vtiger_accountAccount.accountid = vtiger_account.parentid
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups 
					ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_contactdetails on vtiger_contactdetails.accountid=vtiger_account.accountid
				left join vtiger_crmentity as vtiger_crmentityContacts on vtiger_crmentityContacts.crmid = vtiger_contactdetails.contactid 
				left join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid 
				left join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid 
				left join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid 
				left join vtiger_customerdetails on vtiger_contactdetails.contactid = vtiger_customerdetails.customerid 
				left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
				left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid 
				left join vtiger_users as usersContacts on usersContacts.id = vtiger_crmentityContacts.smownerid
				LEFT JOIN vtiger_groups as groupsContacts
					ON groupsContacts.groupid = vtiger_crmentity.smownerid
				where vtiger_crmentity.deleted=0 and (vtiger_crmentityContacts.deleted=0 || vtiger_crmentityContacts.deleted is null) ";
        }
        $retval = $db->pquery($sql);
        if (!$retval) {
            die('Could not get data: ' . mysql_error());
        }
        $row = $db->fetch_array($retval, MYSQL_NUM);
        $rec_count = $row[0];
        $count = intval($rec_count / $rec_limit);
        if ($rec_count % $rec_limit == 0) {
            //$count=$count+1;
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
           $sql = "SELECT DISTINCT vtiger_account.accountid,vtiger_account.accountname,vtiger_account.industry,vtiger_account.phone,vtiger_account.website FROM vtiger_account inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid 
				inner join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid 
				inner join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid 
				inner join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid 
				left join vtiger_account as vtiger_accountAccount on vtiger_accountAccount.accountid = vtiger_account.parentid
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups 
					ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_contactdetails on vtiger_contactdetails.accountid=vtiger_account.accountid
				left join vtiger_crmentity as vtiger_crmentityContacts on vtiger_crmentityContacts.crmid = vtiger_contactdetails.contactid 
				left join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid 
				left join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid 
				left join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid 
				left join vtiger_customerdetails on vtiger_contactdetails.contactid = vtiger_customerdetails.customerid 
				left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
				left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid 
				left join vtiger_users as usersContacts on usersContacts.id = vtiger_crmentityContacts.smownerid
				LEFT JOIN vtiger_groups as groupsContacts
					ON groupsContacts.groupid = vtiger_crmentity.smownerid
				where vtiger_crmentity.deleted=0 and (vtiger_crmentityContacts.deleted=0 || vtiger_crmentityContacts.deleted is null) and (upper(vtiger_account.accountname) like '$search%') ".
       "LIMIT $offset, $rec_limit";
        } else {
           $sql = "SELECT DISTINCT vtiger_account.accountid,vtiger_account.accountname,vtiger_account.industry,vtiger_account.phone,vtiger_account.website FROM vtiger_account inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid 
				inner join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid 
				inner join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid 
				inner join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid 
				left join vtiger_account as vtiger_accountAccount on vtiger_accountAccount.accountid = vtiger_account.parentid
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups 
					ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_contactdetails on vtiger_contactdetails.accountid=vtiger_account.accountid
				left join vtiger_crmentity as vtiger_crmentityContacts on vtiger_crmentityContacts.crmid = vtiger_contactdetails.contactid 
				left join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid 
				left join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid 
				left join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid 
				left join vtiger_customerdetails on vtiger_contactdetails.contactid = vtiger_customerdetails.customerid 
				left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
				left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid 
				left join vtiger_users as usersContacts on usersContacts.id = vtiger_crmentityContacts.smownerid
				LEFT JOIN vtiger_groups as groupsContacts
					ON groupsContacts.groupid = vtiger_crmentity.smownerid
				where vtiger_crmentity.deleted=0 and (vtiger_crmentityContacts.deleted=0 || vtiger_crmentityContacts.deleted is null) ".
       "LIMIT $offset, $rec_limit";
        }

        $result = $db->pquery($sql);


//if( $page > 0 )
//{

        $last = $page - 2;
        if ($last < 0) {
            $last = -1; //$page=0;
        }

        $rs = $db->pquery("select * from vtiger_contactdetails");
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
        // print_r($record);
        $viewer->assign('TEMPLATES', $templates);
        $viewer->assign('COUN', $i);
        //}
        if ($rec_count <= $rec_limit) {
            $viewer->assign('LIMIT', 0);
        }
//echo $row["firstname"];
        $alphabets = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $viewer->assign('RECORDS', $record);
        $viewer->assign('SRCMODULE', $srcModule);
        $viewer->assign('COUNT', count($record));
        $viewer->assign('NEXT', $page);
        $viewer->assign('PREV', $last);
        $viewer->assign('ALPHABETS', $alphabets);
        $viewer->assign('SEARCH', $search);
        $viewer->view('mergeAccountContent.tpl', $request->getModule());
    }

}
