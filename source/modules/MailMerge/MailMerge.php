<?php

class MailMerge {
    public function vtlib_handler($moduleName, $eventType) {
		if ($eventType == 'module.postinstall') {
			$this->_registerLinks($moduleName);
		} else if ($eventType == 'module.enabled') {
			$this->_registerLinks($moduleName);
		} else if ($eventType == 'module.disabled') {
			$this->_deregisterLinks($moduleName);
		}else {
			$this->_registerLinks($moduleName);
		}
                	
	}

	protected function _registerLinks($moduleName) {
		$thisModuleInstance = Vtiger_Module::getInstance($moduleName);
		if ($thisModuleInstance) {
			$thisModuleInstance->addLink("HEADERSCRIPT", "MailMerge", "modules/MailMerge/js/Merge.js");
			
			$leadsModuleInstance = Vtiger_Module::getInstance('Leads');
			$leadsModuleInstance->addLink("DETAILVIEWBASIC", "Merge", 'javascript:Leads.viewSelected();');
			
                        $leadsModuleInstance = Vtiger_Module::getInstance('HelpDesk');
			$leadsModuleInstance->addLink("DETAILVIEWBASIC", "Merge", 'javascript:HelpDesk.viewSelected();');
			
			$contactsModuleInstance = Vtiger_Module::getInstance('Contacts');
			$contactsModuleInstance->addLink("DETAILVIEWBASIC", "Merge", 'javascript:Contacts.viewSelected();');
                        
                        $contactsModuleInstance = Vtiger_Module::getInstance('Accounts');
			$contactsModuleInstance->addLink("DETAILVIEWBASIC", "Merge", 'javascript:Accounts.viewSelected();');
		}
		}
	

	protected function _deregisterLinks($moduleName) {
		$thisModuleInstance = Vtiger_Module::getInstance($moduleName);
		if ($thisModuleInstance) {
			$thisModuleInstance->deleteLink("HEADERSCRIPT", "MailMerge", "modules/MailMerge/js/Merge.js");
			
			$leadsModuleInstance = Vtiger_Module::getInstance('Leads');
			$leadsModuleInstance->deleteLink("DETAILVIEWBASIC", "Merge");
                        
                        $leadsModuleInstance = Vtiger_Module::getInstance('HelpDesk');
			$leadsModuleInstance->deleteLink("DETAILVIEWBASIC", "Merge");

			$contactsModuleInstance = Vtiger_Module::getInstance('Contacts');
			$contactsModuleInstance->deleteLink("DETAILVIEWBASIC", "Merge");
                        
                        $contactsModuleInstance = Vtiger_Module::getInstance('Accounts');
			$contactsModuleInstance->deleteLink("DETAILVIEWBASIC", "Merge");
		}
	}


}
