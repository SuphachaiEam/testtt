<?php
// ini_set("soap.wsdl_cache_enabled", 0);
ini_set('soap.wsdl_cache_ttl', 1);
require_once('modules/Calls/Call.php');
require_once('modules/Otg_OutboundTarget/Otg_OutboundTarget.php');

class Otg_OutboundTargetController extends SugarController {
	
	static public $ws_genesys_url = "http://10.141.26.52/WSMSIGOCSFileMgr/svcCRMFileManager.asmx?WSDL";
	
	function action_CallsListview() {
		$this->view = 'CallsListview';
	}	
	
	
	//TODO: example : http://localhost/CitiPA/index.php?module=Otg_OutboundTarget&action=ws_Genesys_GetCampaigns&to_pdf=1
	function action_Ws_Genesys_StartImport() {
		$this->view = 'ajax';

		$wsparam = $_POST['wsparam'];
		$detail = $_POST['detail'];
		
		$client = new SoapClient(self::$ws_genesys_url);
		$result = $client->UploadCallingList($wsparam);
		
		$this->setval_result_file( $detail['result_filename'], 
				$result->UploadCallingListResult->RequestID, 
				$detail['calllists'], 
				$detail['record_count'], 
				$detail['chain_count'], 
				$detail['blank_chain_count']);
		
		$this->view_object_map['message'] = json_encode($result);
	}
	
	function action_Ws_Genesys_CheckImportStatus() {
		$this->view = 'ajax';
		//GetOCSCallListStatus
		
		$wsparam = $_POST['wsparam'];
		$detail = $_POST['detail'];
		
		$client = new SoapClient(self::$ws_genesys_url);
		$result = $client->GetOCSCallListStatus($wsparam);
		$status_result = $result->GetOCSCallListStatusResult;
		
		if(!empty($result->GetOCSCallListStatusResult->ValidatoionStatus) && $status_result->ValidatoionStatus==1){
			//Validate complete.
			//TODO: Append import result from Genesys to report.
			
			$txt = "<tr><td>status : </td><td>Valid</td></tr>
					<tr><td>Error code : </td><td>'{$status_result->ValidationErrorCode}</td></tr>
					<tr><td>Error description : </td><td>'{$status_result->ValidationErrorDesc}</td></tr>
					<tr><td>Genesys valid records : </td><td>'{$status_result->ValidRecordsCnt}</td></tr>
					<tr><td>Genesys invalid records : </td><td>'{$status_result->InvalidRecordCnt}</td></tr>
					";
			
			$this->append_result_file($detail['result_filename'], $txt);
			
			if(!empty($status_result->InvalidRecordList->InvalidRecord)){
				foreach($status_result->InvalidRecordList->InvalidRecord as $record){
					$this->append_result_file($detail['result_filename'], "<tr> <td></td><td>{$record->RecordID}</td><td>{$record->ErrorCode}</td> </tr>");
				}
			}
			
			$new_file_name = "import_result_".time().'_'.$status_result->SessionID.'.xls';
			
			@rename("cache/import/".$detail['result_filename'], 
					"cache/import/$new_file_name");
			
// 			print_r($status_result);
			$result->newFileName="cache/import/$new_file_name";
		}
		
		$this->view_object_map['message'] = json_encode($result);
	}
	
	function action_Ws_Genesys_CommitUpload() {
		$this->view = 'ajax';
		//GetOCSCallListStatus
		
		$wsparam = $_POST['wsparam'];
		
		$client = new SoapClient(self::$ws_genesys_url);
		$result = $client->CommitUploadCallList($wsparam);
		
		$this->view_object_map['message'] = json_encode($result);
	}
	
	function action_Ws_Genesys_GetCalllists() {
		$this->view = 'ajax';
		
		$wsparam = $_POST['wsparam'];
		if(empty($wsparam)) $wsparam = array();
		
		$client = new SoapClient(self::$ws_genesys_url);
		$result = $client->GetGenesysCallingList($wsparam);
		
		$this->view_object_map['message'] = json_encode($result);
	}
	
	private function setval_result_file($result_filename, $requestid, $calllists, $record_count, $chain_count, $blank_chain_count){
		//read the entire string
		$str=file_get_contents("cache/import/$result_filename");
		
		//replace something in the file string - this is a VERY simple example
		$str=str_replace("<%requestid%>", $requestid, $str);
		$str=str_replace("<%calllists%>", $calllists, $str);
		$str=str_replace("<%record_count%>", $record_count, $str);
		$str=str_replace("<%chain_count%>", $chain_count, $str);
		$str=str_replace("<%blank_chain_count%>", $blank_chain_count, $str);
		
		//write the entire string
		file_put_contents("cache/import/$result_filename", $str);
// 		file_put_contents("cache/import/import_result_".time().'_'.$requestid.'.xls', $str);
// 		unlink("cache/import/$result_filename");
	}
	
	private function append_result_file($result_filename, $txt){
		if(empty($txt)) return;
	
		$myfile = fopen("cache/import/{$result_filename}", "a");
		fwrite($myfile, $txt);
		fclose($myfile);
	}
}
?>