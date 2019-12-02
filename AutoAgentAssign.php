<?php

function setParameter($url, $name, $value){
	$patterns = '/<%'.$name.'%>/';
	
	return preg_replace($patterns, $value, $url);
}

require_once('modules/Otg_Agent/Otg_Agent.php');

global $current_user;

$db = & PearDatabase::getInstance();

echo 'Auto Agent Assign';

// echo '<pre>'; print_r($current_user); echo '<pre>'; exit();

$forward_url = 'index.php?module=Calls&action=CreateCallHistory&record=<%record_id%>&return_module=Otg_OutboundTarget&return_action=DetailView&return_id=<%record_id%>&field_phone=<%telephone_no%>';

try{
	$params = explode('@', $_REQUEST['record']);
	$record_id = $params[0];
	$telephone_no = $params[1];

	//TODO: Not allow success status lead
	$obtid_trim = trim($record_id);
	
	$sqlFindOBT = "select id from otg_outboundtarget where id='{$obtid_trim}' and status='Success' and deleted=0";
	$rsFindOBT = $db->query($sqlFindOBT);
	
	$sqlFindAPP = "select * from otg_motorappform where outboundtarget_id='{$obtid_trim}' and motor_app_status in ('agent_complete', 'qc_complete') and deleted=0";
	$rsFindAPP = $db->query($sqlFindAPP);
	
	if(mssql_num_rows($rsFindOBT)>0 || mssql_num_rows($rsFindAPP)>0){ echo '<h3 style="color:red;">Not allow save call result to "Success Customer". !!!<br /> Please close this tab.</h3>';exit(0); }
	
	
	
	$agent_assigned_id = $current_user->id;
	
	//$obt_sql = "select * from otg_outboundtarget where deleted=0 and id=''";
	
	
	$agent = new Otg_Agent ();
	$agent->retrieve_by_string_fields ( array (
			'user_id_c' => $agent_assigned_id
	) );
	
	
	//TODO: 1. insert log
	$insert_log_sql = "INSERT INTO otg_agent_otg_outboundtarget_log (date_modified,users_id,otg_outboundtarget_id,reference_code,action_by,users_id_old) VALUES
		('" . gmdate ( $GLOBALS ['timedate']->get_db_date_time_format (), strtotime ( 'now' ) ) . "','" . $agent_assigned_id . "','".$record_id."',NULL,'" . $agent_assigned_id . "',NULL)";
	$db->query($insert_log_sql);
	
	//TODO: 2. update Otg_OutboundTarget
	// "update otg_outboundtarget set assigned_user_id='', agent_name='' where id=''"
	$update_obt_sql = "update otg_outboundtarget set assigned_user_id='{$agent_assigned_id}', agent_name='{$agent->name}' where id='{$record_id}'";
	$db->query($update_obt_sql);
	
	//TODO: 3. update Otg_MotorAppForm
	//			If exist in motorappform then update otg_motorappform
	// ""
	
	$forward_url = setParameter($forward_url, 'record_id', $record_id);
	$forward_url = setParameter($forward_url, 'telephone_no', $telephone_no);
	
// 	echo $forward_url;
	
	header('Location: '.$forward_url);
	exit;
	
}catch(Exception $e){
	echo '<div style="color:red;">Error : '.$e.'</div>';
}



?>