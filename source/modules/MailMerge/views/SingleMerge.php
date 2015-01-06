

<?php

class MailMerge_SingleMerge_View extends Vtiger_Index_View {
	
	public function process(Vtiger_Request $request) {
		// global $adb;
        
        $db = PearDatabase::getInstance();
		$viewer = $this->getViewer($request);
                $record = $_REQUEST['record'];
                  $rs = $db->pquery("select templateid,filename from vtiger_wordtemplates where module=?", array($_REQUEST['m']));

                  if($_REQUEST['m']=='Leads'){
                      $mergeview='createMergeLead';
                      
                  }elseif ($_REQUEST['m']=='Accounts') {
                      $mergeview='createMergeAccount';
            
        }elseif ($_REQUEST['m']=='HelpDesk') {
            $mergeview='createMergeHelpDesk';
            
        }elseif ($_REQUEST['m']=='Contacts') {
            $mergeview='createMergeContact';
            
        }
        $i = 0;
        while ($row = $db->fetchByAssoc($rs)) {
            $templates[$i] = $row;
            $i++;
        }
       // print_r($templates);
       // echo $i;
        // print_r($record);
        $viewer->assign('TEMPLATES', $templates);
        $viewer->assign('COUN', $i);
         $viewer->assign('RECOR', $record);
         $viewer->assign('MERGEVIEW', $mergeview);
        //}
        //if ($rec_count <= $rec_limit) {
           // $viewer->assign('LIMIT', 0);
        //}
//echo $row["firstname"];
        //$alphabets = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        //$viewer->assign('RECORDS', $record);
        //$viewer->assign('SRCMODULE', $srcModule);
       // $viewer->assign('COUNT', count($record));
       // $viewer->assign('NEXT', $page);
       // $viewer->assign('PREV', $last);
       // $viewer->assign('ALPHABETS', $alphabets);
      //  $viewer->assign('SEARCH', $search);
      //  $viewer->view('mergeAccountContent.tpl', $request->getModule());
		
		 $viewer->view('singlemergemodule.tpl', $request->getModule());
	}
}
