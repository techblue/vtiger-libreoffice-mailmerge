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

class MailMerge_getList_View extends Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
// Fix For: http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/2107
        $db = PearDatabase::getInstance();
        if ($_REQUEST['name'] == 'Leads') {
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
                    //$field_label[$x] = $field_label[$x].",USER_FIRSTNAME,USER_LASTNAME,USER_USERNAME,USER_SECONDARYEMAIL,USER_TITLE,USER_OFFICEPHONE,USER_DEPARTMENT,USER_MOBILE,USER_OTHERPHONE,USER_FAX,USER_EMAIL,USER_HOMEPHONE,USER_OTHEREMAIL,USER_PRIMARYADDRESS,USER_CITY,USER_STATE,USER_POSTALCODE,USER_COUNTRY";
                }
                echo '<table class="table table-bordered listViewEntriesTable"><thead>
        <tr class="" style="font-size: small"><td><b>' . $field_label[$x] . "</b></td></tr></thead></table>";
            }
// Ordena etiquetas más grandes primero para que no se sutituyan subcadenas en el documento
// Por ejemplo, pongo LEAD_TIPOVIVIENDA delante de LEAD_TIPO, para que no se sustituya la subcadena LEAD_TIPO
        } else if ($_REQUEST['name'] == 'Accounts') {
            global $current_user;
            require('user_privileges/user_privileges_' . $current_user->id . '.php');
            if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || $module == "Users" || $module == "Emails") {
                // $query1="select vtiger_tab.name,vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldlabel from vtiger_field inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid where vtiger_field.tabid in (6) and vtiger_field.block <> 75 and vtiger_field.presence in (0,2) order by vtiger_field.tablename";
                $query1 = "select vtiger_tab.name,vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldlabel from vtiger_field inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid where vtiger_field.tabid in (6) and vtiger_field.uitype <> 61 and block <> 75 and block <> 30 and vtiger_field.presence in (0,2) and vtiger_field.tablename <> 'vtiger_campaignrelstatus' order by vtiger_field.tablename";
                $params1 = array();
            } else {
                $profileList = getCurrentUserProfileList();
                $query1 = "select vtiger_tab.name,vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldlabel from vtiger_field inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid in (6) and vtiger_field.uitype <> 61 and vtiger_field.block <> 75 AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ") and vtiger_field.presence in (0,2) and vtiger_field.tablename <> 'vtiger_campaignrelstatus' GROUP BY vtiger_field.fieldid order by vtiger_field.tablename";
                $params1 = array($profileList);
                //Postgres 8 fixes
                if ($db->dbType == "pgsql")
                    $query1 = fixPostgresQuery($query1, $log, 0);
            }

            $result = $db->pquery($query1, $params1);
            $y = $db->num_rows($result);
            $userNameSql = getSqlForNameInDisplayFormat(array('first_name' =>
                'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
            $contactUserNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'usersContacts.first_name', 'last_name' =>
                'usersContacts.last_name'), 'Users');

            for ($x = 0; $x < $y; $x++) {
                $tablename = $db->query_result($result, $x, "tablename");
                $columnname = $db->query_result($result, $x, "columnname");
                $modulename = $db->query_result($result, $x, "name");

                if ($tablename == "crmentity") {
                    if ($modulename == "Contacts") {
                        $tablename = "vtiger_crmentityContacts";
                    }
                }
                $querycolumns[$x] = $tablename . "." . $columnname;
                if ($columnname == "smownerid") {
                    if ($modulename == "Accounts") {
                        $querycolumns[$x] = "case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as userjoinname,vtiger_users.first_name,vtiger_users.last_name,vtiger_users.user_name,vtiger_users.secondaryemail,vtiger_users.title,vtiger_users.phone_work,vtiger_users.department,vtiger_users.phone_mobile,vtiger_users.phone_other,vtiger_users.phone_fax,vtiger_users.email1,vtiger_users.phone_home,vtiger_users.email2,vtiger_users.address_street,vtiger_users.address_city,vtiger_users.address_state,vtiger_users.address_postalcode,vtiger_users.address_country";
                    }
                    if ($modulename == "Contacts") {
                        $querycolumns[$x] = "case when (usersContacts.user_name not like '') then $contactUserNameSql else groupsContacts.groupname end as userjoincname";
                    }
                }
                if ($columnname == "parentid") {
                    $querycolumns[$x] = "vtiger_accountAccount.accountname";
                }
                if ($columnname == "accountid") {
                    $querycolumns[$x] = "vtiger_accountContacts.accountname";
                }
                if ($columnname == "reportsto") {
                    $querycolumns[$x] = "vtiger_contactdetailsContacts.lastname";
                }

                if ($modulename == "Accounts") {
                    $field_label[$x] = "ACCOUNT_" . strtoupper(str_replace(" ", "", $db->query_result($result, $x, "fieldlabel")));
                    if ($columnname == "smownerid") {
                        //$field_label[$x] = $field_label[$x].",USER_FIRSTNAME,USER_LASTNAME,USER_USERNAME,USER_SECONDARYEMAIL,USER_TITLE,USER_OFFICEPHONE,USER_DEPARTMENT,USER_MOBILE,USER_OTHERPHONE,USER_FAX,USER_EMAIL,USER_HOMEPHONE,USER_OTHEREMAIL,USER_PRIMARYADDRESS,USER_CITY,USER_STATE,USER_POSTALCODE,USER_COUNTRY";
                    }
                }

                if ($modulename == "Contacts") {
                    $field_label[$x] = "CONTACT_" . strtoupper(str_replace(" ", "", $db->query_result($result, $x, "fieldlabel")));
                }
                echo '<table class="table table-bordered listViewEntriesTable"><thead>
        <tr class="" style="font-size: small"><td><b>' . $field_label[$x] . "</b></td></tr></thead></table>";
            }
        } elseif ($_REQUEST['name'] == 'Contacts') {

            global $current_user;
            require('user_privileges/user_privileges_' . $current_user->id . '.php');
            if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || $module == "Users" || $module == "Emails") {
                $query1 = "select vtiger_tab.name,vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldlabel from vtiger_field inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid where vtiger_field.tabid in (4,6) and vtiger_field.block <> 75 order by vtiger_field.tablename and vtiger_field.presence in (0,2)";
                $params1 = array();
            } else {
                $profileList = getCurrentUserProfileList();
                $query1 = "select vtiger_tab.name,vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldlabel from vtiger_field inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid in (4,6) and vtiger_field.block <> 75 AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ") and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid order by vtiger_field.tablename";
                $params1 = array($profileList);
                //Postgres 8 fixes
                if ($db->dbType == "pgsql")
                    $query1 = fixPostgresQuery($query1, $log, 0);
            }
            $result = $db->pquery($query1, $params1);
            $y = $db->num_rows($result);
            $userNameSql = getSqlForNameInDisplayFormat(array('first_name' =>
                'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
            $accountUserNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'usersAccounts.first_name', 'last_name' =>
                'usersAccounts.last_name'), 'Users');

            for ($x = 0; $x < $y; $x++) {
                $tablename = $db->query_result($result, $x, "tablename");
                $columnname = $db->query_result($result, $x, "columnname");
                $modulename = $db->query_result($result, $x, "name");

                if ($tablename == "crmentity") {
                    if ($modulename == "Accounts") {
                        $tablename = "crmentityAccounts";
                    }
                }
                $querycolumns[$x] = $tablename . "." . $columnname;
                if ($columnname == "smownerid") {
                    if ($modulename == "Accounts") {
                        $querycolumns[$x] = "case when (usersAccounts.user_name not like '') then $accountUserNameSql else groupsAccounts.groupname end as username";
                    }
                    if ($modulename == "Contacts") {
                        $querycolumns[$x] = "case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as usercname,vtiger_users.first_name,vtiger_users.last_name,vtiger_users.user_name,vtiger_users.secondaryemail,vtiger_users.title,vtiger_users.phone_work,vtiger_users.department,vtiger_users.phone_mobile,vtiger_users.phone_other,vtiger_users.phone_fax,vtiger_users.email1,vtiger_users.phone_home,vtiger_users.email2,vtiger_users.address_street,vtiger_users.address_city,vtiger_users.address_state,vtiger_users.address_postalcode,vtiger_users.address_country";
                    }
                }
                if ($columnname == "parentid") {
                    $querycolumns[$x] = "accountAccounts.accountname";
                }
                if ($columnname == "accountid") {
                    $querycolumns[$x] = "accountContacts.accountname";
                }
                if ($columnname == "reportsto") {
                    $querycolumns[$x] = "contactdetailsContacts.lastname";
                }


                if ($modulename == "Accounts") {
                    $field_label[$x] = "ACCOUNT_" . strtoupper(str_replace(" ", "", $db->query_result($result, $x, "fieldlabel")));
                }

                if ($modulename == "Contacts") {
                    $field_label[$x] = "CONTACT_" . strtoupper(str_replace(" ", "", $db->query_result($result, $x, "fieldlabel")));
                    if ($columnname == "smownerid") {
                        //$field_label[$x] = $field_label[$x].",USER_FIRSTNAME,USER_LASTNAME,USER_USERNAME,USER_SECONDARYEMAIL,USER_TITLE,USER_OFFICEPHONE,USER_DEPARTMENT,USER_MOBILE,USER_OTHERPHONE,USER_FAX,USER_EMAIL,USER_HOMEPHONE,USER_OTHEREMAIL,USER_PRIMARYADDRESS,USER_CITY,USER_STATE,USER_POSTALCODE,USER_COUNTRY";
                    }
                    echo '<table class="table table-bordered listViewEntriesTable"><thead>
        <tr class="" style="font-size: small"><td><b>' . $field_label[$x] . "</b></td></tr></thead></table>";
                }
            }
        } elseif ($_REQUEST['name'] == 'HelpDesk') {

            global $current_user;
            require('user_privileges/user_privileges_' . $current_user->id . '.php');
            if ($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || $module == "Users" || $module == "Emails") {
                $query1 = "select vtiger_tab.name,vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldlabel from vtiger_field inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid where vtiger_field.tabid in (13,4,6) and vtiger_field.uitype <> 61 and block <> 75 and block <> 30 and vtiger_field.presence in (0,2) order by vtiger_field.tablename";
                $params1 = array();
            } else {
                $profileList = getCurrentUserProfileList();
                $query1 = "select vtiger_tab.name,vtiger_field.tablename,vtiger_field.columnname,vtiger_field.fieldlabel from vtiger_field inner join vtiger_tab on vtiger_tab.tabid = vtiger_field.tabid INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid in (13,4,6) and vtiger_field.uitype <> 61 and block <> 75 and block <> 30 AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN (" . generateQuestionMarks($profileList) . ") and vtiger_field.presence in (0,2) GROUP BY vtiger_field.fieldid order by vtiger_field.tablename";
                $params1 = array($profileList);
                //Postgres 8 fixes
                if ($db->dbType == "pgsql")
                    $query1 = fixPostgresQuery($query1, $log, 0);
            }
            $result = $db->pquery($query1, $params1);
            $y = $db->num_rows($result);
            $userNameSql = getSqlForNameInDisplayFormat(array('first_name' =>
                'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
            $contactUserNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'usersContacts.first_name', 'last_name' =>
                'usersContacts.last_name'), 'Users');
            $accountUserNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'usersAccounts.first_name', 'last_name' =>
                'usersAccounts.last_name'), 'Users');

            for ($x = 0; $x < $y; $x++) {
                $tablename = $db->query_result($result, $x, "tablename");
                $columnname = $db->query_result($result, $x, "columnname");
                $modulename = $db->query_result($result, $x, "name");

                $column_name = $tablename . "." . $columnname;

                if ($columnname == "parent_id") {
                    $column_name = "case crmentityRelHelpDesk.setype when 'Accounts' then accountRelHelpDesk.accountname when 'Contacts' then concat(contactdetailsRelHelpDesk.firstname,' ',contactdetailsRelHelpDesk.lastname) End";
                }
                if ($columnname == "product_id") {
                    $column_name = "productsRel.productname";
                }
                if ($tablename == "vtiger_crmentity") {
                    if ($modulename == "Contacts") {
                        $tablename = "crmentityContacts";
                        $column_name = $tablename . "." . $columnname;
                    }
                    if ($modulename == "Accounts") {
                        $tablename = "crmentityAccounts";
                        $column_name = $tablename . "." . $columnname;
                    }
                }

                if ($columnname == "smownerid") {
                    if ($modulename == "Accounts") {
                        $column_name = "case when (usersAccounts.user_name not like '') then $accountUserNameSql else groupsAccounts.groupname end as username";
                    }
                    if ($modulename == "Contacts") {
                        $column_name = "case when (usersContacts.user_name not like '') then $contactUserNameSql else groupsContacts.groupname end as username";
                    }
                    if ($modulename == "HelpDesk") {
                        $column_name = "case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as userhelpname,vtiger_users.first_name,vtiger_users.last_name,vtiger_users.user_name,vtiger_users.secondaryemail,vtiger_users.title,vtiger_users.phone_work,vtiger_users.department,vtiger_users.phone_mobile,vtiger_users.phone_other,vtiger_users.phone_fax,vtiger_users.email1,vtiger_users.phone_home,vtiger_users.email2,vtiger_users.address_street,vtiger_users.address_city,vtiger_users.address_state,vtiger_users.address_postalcode,vtiger_users.address_country";
                    }
                }
                if ($columnname == "parentid") {
                    $column_name = "accountAccount.accountname";
                }
                if ($columnname == "accountid") {
                    $column_name = "accountContacts.accountname";
                }
                if ($columnname == "reportsto") {
                    $column_name = "contactdetailsContacts.lastname";
                }

                $querycolumns[$x] = $column_name;

                if ($modulename == "Accounts") {
                    $field_label[$x] = "ACCOUNT_" . strtoupper(str_replace(" ", "", $db->query_result($result, $x, "fieldlabel")));
                }
                if ($modulename == "Contacts") {
                    $field_label[$x] = "CONTACT_" . strtoupper(str_replace(" ", "", $db->query_result($result, $x, "fieldlabel")));
                }
                if ($modulename == "HelpDesk") {
                    $field_label[$x] = "TICKET_" . strtoupper(str_replace(" ", "", $db->query_result($result, $x, "fieldlabel")));
                    if ($columnname == "smownerid") {
                        //$field_label[$x] = $field_label[$x].",USER_FIRSTNAME,USER_LASTNAME,USER_USERNAME,USER_SECONDARYEMAIL,USER_TITLE,USER_OFFICEPHONE,USER_DEPARTMENT,USER_MOBILE,USER_OTHERPHONE,USER_FAX,USER_EMAIL,USER_HOMEPHONE,USER_OTHEREMAIL,USER_PRIMARYADDRESS,USER_CITY,USER_STATE,USER_POSTALCODE,USER_COUNTRY";
                    }
                    echo '<table class="table table-bordered listViewEntriesTable"><thead>
        <tr class="" style="font-size: small"><td><b>' . $field_label[$x] . "</b></td></tr></thead></table>";
                }
            }
        }
    }

}
