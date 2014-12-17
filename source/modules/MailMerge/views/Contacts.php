<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class MailMerge_Contacts_View extends Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
        global $adb;
        $srcModule = $request->get('src_module');
        $viewer = $this->getViewer($request);
        $db = PearDatabase::getInstance();


        $rec_limit = 20;
        /* Get total number of records */
        $search = $_REQUEST['search'];
        if (isset($_REQUEST['search'])) {
           $sql = "SELECT count(*) FROM vtiger_contactdetails inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid 
				inner join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid 
				inner join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid 
				inner join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid 
				inner join vtiger_customerdetails on vtiger_contactdetails.contactid = vtiger_customerdetails.customerid 
				left join vtiger_contactdetails as contactdetailsContacts on contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
				left join vtiger_account as accountContacts on accountContacts.accountid = vtiger_contactdetails.accountid 
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups 
					ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
				left join vtiger_crmentity as crmentityAccounts on crmentityAccounts.crmid=vtiger_account.accountid
				left join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid
				left join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid
				left join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid
				left join vtiger_account as accountAccounts on accountAccounts.accountid = vtiger_account.parentid
				left join vtiger_users as usersAccounts on usersAccounts.id = crmentityAccounts.smownerid 
				LEFT JOIN vtiger_groups as groupsAccounts
					ON groupsAccounts.groupid = vtiger_crmentity.smownerid
				where vtiger_crmentity.deleted=0 and (crmentityAccounts.deleted is NULL or crmentityAccounts.deleted <> 1) and (upper(vtiger_contactdetails.firstname) like '$search%') ";
        } else {
            $sql = "SELECT count(*) FROM vtiger_contactdetails inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid 
				inner join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid 
				inner join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid 
				inner join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid 
				inner join vtiger_customerdetails on vtiger_contactdetails.contactid = vtiger_customerdetails.customerid 
				left join vtiger_contactdetails as contactdetailsContacts on contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
				left join vtiger_account as accountContacts on accountContacts.accountid = vtiger_contactdetails.accountid 
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups 
					ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
				left join vtiger_crmentity as crmentityAccounts on crmentityAccounts.crmid=vtiger_account.accountid
				left join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid
				left join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid
				left join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid
				left join vtiger_account as accountAccounts on accountAccounts.accountid = vtiger_account.parentid
				left join vtiger_users as usersAccounts on usersAccounts.id = crmentityAccounts.smownerid 
				LEFT JOIN vtiger_groups as groupsAccounts
					ON groupsAccounts.groupid = vtiger_crmentity.smownerid
				where vtiger_crmentity.deleted=0 and (crmentityAccounts.deleted is NULL or crmentityAccounts.deleted <> 1) ";
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
            $sql = "SELECT vtiger_contactdetails.contactid,vtiger_contactdetails.contact_no,vtiger_contactdetails.firstname,vtiger_contactdetails.lastname,vtiger_contactdetails.email,vtiger_contactdetails.phone,vtiger_contactdetails.title,vtiger_contactdetails.mobile FROM vtiger_contactdetails inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid 
				inner join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid 
				inner join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid 
				inner join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid 
				inner join vtiger_customerdetails on vtiger_contactdetails.contactid = vtiger_customerdetails.customerid 
				left join vtiger_contactdetails as contactdetailsContacts on contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
				left join vtiger_account as accountContacts on accountContacts.accountid = vtiger_contactdetails.accountid 
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups 
					ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
				left join vtiger_crmentity as crmentityAccounts on crmentityAccounts.crmid=vtiger_account.accountid
				left join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid
				left join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid
				left join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid
				left join vtiger_account as accountAccounts on accountAccounts.accountid = vtiger_account.parentid
				left join vtiger_users as usersAccounts on usersAccounts.id = crmentityAccounts.smownerid 
				LEFT JOIN vtiger_groups as groupsAccounts
					ON groupsAccounts.groupid = vtiger_crmentity.smownerid
				where vtiger_crmentity.deleted=0 and (crmentityAccounts.deleted is NULL or crmentityAccounts.deleted <> 1) and (upper(vtiger_contactdetails.firstname) like '$search%') ".
       "LIMIT $offset, $rec_limit";
        } else {
           $sql = "SELECT vtiger_contactdetails.contactid,vtiger_contactdetails.contact_no,vtiger_contactdetails.firstname,vtiger_contactdetails.lastname,vtiger_contactdetails.email,vtiger_contactdetails.phone,vtiger_contactdetails.title,vtiger_contactdetails.mobile FROM vtiger_contactdetails inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid 
				inner join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid 
				inner join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid 
				inner join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid 
				inner join vtiger_customerdetails on vtiger_contactdetails.contactid = vtiger_customerdetails.customerid 
				left join vtiger_contactdetails as contactdetailsContacts on contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
				left join vtiger_account as accountContacts on accountContacts.accountid = vtiger_contactdetails.accountid 
				left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups 
					ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				left join vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
				left join vtiger_crmentity as crmentityAccounts on crmentityAccounts.crmid=vtiger_account.accountid
				left join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid
				left join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid
				left join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid
				left join vtiger_account as accountAccounts on accountAccounts.accountid = vtiger_account.parentid
				left join vtiger_users as usersAccounts on usersAccounts.id = crmentityAccounts.smownerid 
				LEFT JOIN vtiger_groups as groupsAccounts
					ON groupsAccounts.groupid = vtiger_crmentity.smownerid
				where vtiger_crmentity.deleted=0 and (crmentityAccounts.deleted is NULL or crmentityAccounts.deleted <> 1) ".
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
//$result=  mysql_query("select contactid,contact_no,firstname,lastname,email,phone,title,mobile from vtiger_contactdetails limit ".$start.",".$length);
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
        while ($row = $db->fetch_row($rs)) {
            $templates[$i] = $row;
            $i++;
        }

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
        $viewer->view('mergeContactContent.tpl', $request->getModule());
    }

}
