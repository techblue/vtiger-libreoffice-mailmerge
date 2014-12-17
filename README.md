Vtiger-LibreOffice-MailMerge
===================

**Description**
----------------

Vtiger Mail Merge Module can be used to do mail merge in Vtiger CRM system. 

Requisites
----------

1. VTiger 6.0
1. VTiger 6.1
3. Libre Office (For editing documents only)

-------------

## Installation on Vtiger 6.0.0##

 1. Login to VTiger CRM with admin credentials.
 2. Go to CRM Settings
 3. Under Studio click `Module Manager`
 4. Click `Install from File` option
 5. Select zip file vtiger-mail-merge.zip provided in the plugin folder.
 6. Click Import and install module.
 
## Installation on Vtiger 6.1.0##
 1. Login to VTiger CRM with admin credentials.
 2. Go to CRM Settings
 3. Under Studio click `Module Manager`
 4. Click `Install from zip` option
 5. Select zip file vtiger-mail-merge.zip provided in the plugin folder.
 6. Click Import and install module.

StackEdit stores your documents in your browser, which means all your documents are automatically saved locally and are accessible **offline!**

Usage
=====

Create a template file using  LibreOffice. As placeholders for the fields in vtiger CRM use the name of the entity followed by the name of the field without any spaces and in capital letters, for example:  ACCOUNT_ACCOUNTNAME will be replaced with the real account name being merged.
OR 
you can get the list of fields to be merged in 'Module Merge Field List' section by clicking the link 'Module Merge Field List' ,and on the next page by selecting module you will get merge field list
for a particular module.

These templates follow the same rules as Vtiger CRM normal mail templates and can only have one record per template. 

An example template oo-test-template.odt is also included in the archive. The template uses labels as an example. For example:

Test some Accounts module merge fields

accounts_type=ACCOUNT_TYPE
accounts_phone=ACCOUNT_PHONE
accounts_email=ACCOUNT_EMAIL
accounts_siccode=ACCOUNT_SICCODE
accounts_accountname=ACCOUNT_ACCOUNTNAME
accounts_billingaddress=ACCOUNT_BILLINGADDRESS
accounts_billingcode=ACCOUNT_BILLINGCODE
accounts_billingstate=ACCOUNT_BILLINGSTATE
accounts_billingpobox=ACCOUNT_BILLINGPOBOX
accounts_billingcity=ACCOUNT_BILLINGCITY
accounts_billingcountry=ACCOUNT_BILLINGCOUNTRY
accounts_shippingaddress=ACCOUNT_SHIPPINGADDRESS
accounts_shippingcode=ACCOUNT_SHIPPINGCODE
accounts_shippingstate=ACCOUNT_SHIPPINGSTATE
accounts_shippingpobox=ACCOUNT_SHIPPINGPOBOX
accounts_shippingcity=ACCOUNT_SHIPPINGCITY
accounts_shippingcountry=ACCOUNT_SHIPPINGCOUNTRY

The supported modules are Leads, HelpDesk, Accounts, Contacts.

 - Click on Merge  Modules
 - Select “merge {moduleName} Module” link you want to merge and after that  on next page, select one or more items (e.g. one or more Contacts), choose the correct template in the 'Select template to Mail Merge:' combobox and click the 'Merge' button.
 - After Merging, it will produce the merged document and it will automatically latched with document module, and it will also latched with particular record of module  and will show up in document section of that record.
