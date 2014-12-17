<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
 
class MailMerge_mergeModule_View extends Vtiger_Index_View {
	
	public function process(Vtiger_Request $request) {
		
		$viewer = $this->getViewer($request);
		
		 $viewer->view('mergeModule.tpl', $request->getModule());
	}
}
