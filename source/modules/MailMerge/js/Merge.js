/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

jQuery.Class('HelpDesk', {
	viewSelected: function() {
		//var listView    = Vtiger_List_Js.getInstance();
		//var nothingSelected = listView.checkListRecordSelected();
		//if (nothingSelected) {
			//alert('Nothing selected!');
		//} else {
			//var selectedIds = listView.readSelectedIds(false);
			//var excludedIds = listView.readExcludedIds(false);
                        var recordId=$('#recordId').val();
                        //alert(recordId);
			//if (selectedIds.length) {
				window.open("index.php?module=MailMerge&view=SingleMerge&m=HelpDesk&record="+recordId);
			//}
		//}
	}
}, {});

jQuery.Class('Leads', {
	viewSelected: function() {
		//var listView    = Vtiger_List_Js.getInstance();
		//var nothingSelected = listView.checkListRecordSelected();
		//if (nothingSelected) {
			//alert('Nothing selected!');
		//} else {
			//var selectedIds = listView.readSelectedIds(false);
			//var excludedIds = listView.readExcludedIds(false);
                        var recordId=$('#recordId').val();
                       // alert(recordId);
			//if (selectedIds.length) {
				window.open("index.php?module=MailMerge&view=SingleMerge&m=Leads&record="+recordId);
			//}
		//}
	}
}, {});

jQuery.Class('Contacts', {
	viewSelected: function() {
		//var listView    = Vtiger_List_Js.getInstance();
		//var nothingSelected = listView.checkListRecordSelected();
		//if (nothingSelected) {
			//alert('Nothing selected!');
		//} else {
			//var selectedIds = listView.readSelectedIds(false);
			//var excludedIds = listView.readExcludedIds(false);
                        var recordId=$('#recordId').val();
                       // alert(recordId);
			//if (selectedIds.length) {
				window.open("index.php?module=MailMerge&view=SingleMerge&m=Contacts&record="+recordId);
			//}
		//}
	}
}, {});

jQuery.Class('Accounts', {
	viewSelected: function() {
		//var listView    = Vtiger_List_Js.getInstance();
		//var nothingSelected = listView.checkListRecordSelected();
		//if (nothingSelected) {
			//alert('Nothing selected!');
		//} else {
			//var selectedIds = listView.readSelectedIds(false);
			//var excludedIds = listView.readExcludedIds(false);
                        var recordId=$('#recordId').val();
                        //alert(recordId);
			//if (selectedIds.length) {
				window.open("index.php?module=MailMerge&view=SingleMerge&m=Accounts&record="+recordId);
			//}
		//}
	}
}, {});