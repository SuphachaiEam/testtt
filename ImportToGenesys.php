<?php

DEFINE ('GENESYS_MOBILE', 'Mobile');
DEFINE ('GENESYS_HOME','Home Phone');
DEFINE ('GENESYS_OFFICE','Business With Extension');
DEFINE ('CRM_MOBILE','mobilephone');
DEFINE ('CRM_HOME','home_phone');
DEFINE ('CRM_OFFICE','off_phone');

function create_result_file(){
	global $result_filename;

	if(empty($result_filename)) $result_filename = 'genesysimporter-'.time().".tmp";

	//file_put_contents('cache/import/'.$result_filename, '<html><table><tr><td></td><td></td></tr>');

	$myfile = fopen("cache/import/{$result_filename}", "w");
	$txt = "<html><table>
<tr><td>Request ID : </td><td>'<%requestid%></td></tr>
<tr><td>Import to : </td><td>'<%calllists%></td></tr>
<tr><td>CRM Records count : </td><td>'<%record_count%></td></tr>
<tr><td>CRM Chains count : </td><td>'<%chain_count%></td></tr>
<tr><td>CRM blank chains : </td><td>'<%blank_chain_count%></td></tr>
";
	fwrite($myfile, $txt);
	fclose($myfile);
}

function append_result_file($txt){
	if(empty($txt)) return;
	
	global $result_filename;
	
	$myfile = fopen("cache/import/{$result_filename}", "a");
	fwrite($myfile, $txt);
	fclose($myfile);
}

function mappingCalllist($v, $k) { 
	$v = str_replace('|', '#', $v);
	$v = str_replace('=', ':', $v);
	return empty($v)?'':$k . '=' . $v; 
}

function replaceExtensionNumber($fullnumber){
	/* Ex. 0024846878,12345
	 * Adjust telephone number with extension number.
	 * Ex1. 0812345678,00000  ->  0812345678
	 * Ex2. 0024685646,04864  ->  0024685646,4864
	 * Ex3. 0000000000,00000  ->  ""
	 */
	
	/*
	 * test empty number
	 * ^(0*), => test for 00000...n,
	 * V.1 -> ^(0*),
	 * V.2 -> ^[0-9]{3}(0*),
	 */
	if(preg_match('/^[0-9]{3}(0*),/', $fullnumber)) return '';

	/*
	 * test RegEx
	 * ,[^0-9]*$ => replace ,- (or not number) to blank none comma
	* ,(0*)$ => replace ,00000000..........n to blank none comma
	* ,(0+)  => replace ,00456 to ,456
	*/

	$fullnumber = preg_replace('/,[^0-9]*$/', '', $fullnumber);
	$fullnumber = preg_replace('/,(0*)$/', '', $fullnumber);
	$fullnumber = preg_replace('/,(0+)/', ',', $fullnumber);
	return $fullnumber;
}

function getGenesysRecords($row, $chain_id, &$chain_n){
	
	$row_raw = $row;
	
	$input = array(
// 			'record_id'=>'1',
// 			'contact_info'=>'5125',
// 			'contact_info_type'=>'Home Phone',
// 			'record_type'=>'General',
// 			'record_status'=>'Retrieved',
// 			'call_result'=>'Unknown Call Result',
// 			'attempt'=>'0',
// 			'dial_sched_time'=>'',
// 			'call_time'=>'2/11/2009 4:45:43 PM',
// 			'daily_from'=>'8:00:00 AM',
// 			'daily_till'=>'6:00:00 PM',
// 			'tz_dbid'=>'VST',
// 			'campaign_id'=>'121',
// 			'agent_id'=>'',
// 			'chain_id'=>'8',
// 			'chain_n'=>'0',
// 			'group_id'=>'118',
			'app_id'=>'',
			'treatments'=>'',
			'media_ref'=>'',
			'email_subject'=>'',
			'email_template_id'=>'',
// 			'switch_id'=>'101',
			
			/*'age'=>'',
			'birth'=>'',
			'gender'=>'',
			'reference_code'=>'',
			'cardno'=>'',
			'income'=>'',
			'province'=>'',
			'cardtype'=>'',
			'expiry_month'=>'',
			'note3'=>'',
			'note4'=>'',
			'note5'=>'',
			'prefix_th'=>'',
			'name_th'=>'',
			'surname_th'=>'',
			'prefix_en'=>'',
			'name_en'=>'',
			'surname_en'=>'',
			'note1_c'=>'',
			'note5_c'=>'',
			'brand_c'=>'',
			'exp1_c'=>'',
			'brand1_c'=>'',
			'model1_c'=>'',
			'cc1_c'=>'',
			'y1_c'=>'',
			'leadtype'=>'',
			'crm_rec_id'=>'',
			'crm_product_type'=>'',
			'crm_wave_name'=>'',
			'imported_date'=>'1/1/2015'*/
	);
	
	$contact_info = array(GENESYS_MOBILE=>replaceExtensionNumber($row[CRM_MOBILE]),
						GENESYS_OFFICE=>replaceExtensionNumber($row[CRM_OFFICE]),
						GENESYS_HOME=>replaceExtensionNumber($row[CRM_HOME]));
	
	unset($row[CRM_MOBILE]);
	unset($row[CRM_OFFICE]);
	unset($row[CRM_HOME]);
	
	//Swap between office phone number and home phone number when detect comma in home phone.
	if(strpos($contact_info[GENESYS_HOME], ",")){
		$tmp = $contact_info[GENESYS_HOME];
		$contact_info[GENESYS_HOME] = $contact_info[GENESYS_OFFICE];
		$contact_info[GENESYS_OFFICE] = $tmp;
	}
	
	$input = array_merge($input, $row);
	
	$output = '';
	$chain_n=0;
	foreach($contact_info as $info_type=>$number){
		$number_trimed = trim($number);
		if(empty($number_trimed)) continue;
		$input['contact_info'] = $number;
		$input['contact_info_type'] = $info_type;
		$input['chain_n'] = ++$chain_n;
		$input['chain_id'] = $chain_id; 
		$output .= implode('|', array_filter(array_map("mappingCalllist", $input, array_keys($input))))."\r\n";
	}
	
	if(empty($chain_n)){
		append_result_file("<tr> <td></td><td>{$row_raw['crm_rec_id']}</td><td>{$row_raw['name_th']}</td><td>{$row_raw['surname_th']}</td> </tr>");
	}
		
	return $output;
}

require_once('modules/Otg_OutboundTarget/Otg_OutboundTarget.php');
require_once('modules/Otg_OutboundLead/Otg_OutboundLead.php');
ini_set('memory_limit','1024M');

$result_filename="";
global $result_filename, $sugar_config;;

$genesys_config = $sugar_config['genesys'];

create_result_file();

$export_where = $_SESSION['export_where'];
$mass = $_REQUEST['mass'];

$db = & PearDatabase::getInstance();

try{

	echo '<h3>Import To Genesys<h3>';
	
	
	// print_r($export_where);
	// echo '--------------------------------------------------------------';
	// print_r($mass);
	
	
	$where_clause = '';
	
	if (is_array($mass)) {
		$where_clause  = "WHERE deleted = 0 AND ";
		$where_clause .= "id IN ('" . implode("', '", $mass) . "')";
	} else {
		$where_clause = $export_where;
	}
	
	$query  = "SELECT 
				otg_outboundtarget.age,
				otg_outboundtarget.dealer_prem as birth,
				otg_outboundtarget.gender,
				otg_outboundtarget.reference_code,
				otg_outboundtarget.car_make as cardno,
				otg_outboundtarget.income,
				otg_outboundtarget.province,
				otg_outboundtarget_cstm.descriptionl_c as cardtype,
				otg_outboundtarget.expiry_month,
				otg_outboundtarget.series as note3,
				otg_outboundtarget.authorized_prem as note4,
				otg_outboundtarget.dealer_prem_cmi as note5,
				otg_outboundtarget.cc as prefix_th,
				otg_outboundtarget.name as name_th,
				otg_outboundtarget.surname as surname_th,
				otg_outboundtarget.dealer as prefix_en,
				otg_outboundtarget.model as name_en,
				otg_outboundtarget.extension as surname_en,
				otg_outboundtarget.series as leadtype,
				otg_outboundtarget.id as crm_rec_id,
				otg_outboundtarget.wave_name as crm_wave_name,
				'citipa' as crm_product_type,
				
				otg_outboundtarget.mobilephone+','+otg_outboundtarget_cstm.m_phnext_c as mobilephone,
				otg_outboundtarget.off_phone+','+otg_outboundtarget_cstm.b_phnext_c as off_phone,
				otg_outboundtarget.home_phone+','+otg_outboundtarget_cstm.h_phnext_c as home_phone ";
	$query .= " from otg_outboundtarget,otg_outboundtarget_cstm  ";
	$query .= $where_clause." and otg_outboundtarget_cstm.id_c = otg_outboundtarget.id";
	
	//echo $query;
	
	$result = $db->query($query);
	
	$row_count=$db->getRowCount($result);
	// exit();
	
	if($row_count<1) throw new Exception('Not found record'); 
	
	$get_chain_id_seq_url = $genesys_config['get_chain_id_seq_url'];
	if(empty($get_chain_id_seq_url)) throw new Exception('Can\'t connect chain id seqence generator'); 
	$str=file_get_contents($get_chain_id_seq_url.$row_count);
	if(!is_numeric($str)) throw new Exception('Chain id seqence is not a number'); 
	$sequence = intval($str);
		
	// 	print_r($sequence);
	// 	exit();
	$filename = 'calllist_citipa_'.$sequence.'.rsl';
	$strFileName = "cache/import/".$filename;
	$objFopen = fopen($strFileName, 'w');
	
	$blank_chain_count=0;
	$chain_count = 0;
	$record_count = 0;
	
	while( ($row = $db->fetchByAssoc($result))) {
		//echo '<pre>'; print_r($row); echo '</pre>';
		fwrite($objFopen, getGenesysRecords($row, $sequence++, $chain_n));
		if(empty($chain_n)){ $blank_chain_count++; }
		else{ $chain_count++; }
		$record_count+=$chain_n;
	}
	
	if(!$objFopen) throw new Exception('File can not write');
		
	fclose($objFopen);
	$filesize = filesize($strFileName);
	
?>
<h4>Create file completed</h4>
<div style="">
	<form name="" method="post" action="index.php">
		<table>
			<tr>
				<td>File name : </td>
				<td>
					<input type="text" name="filename" value="<?=$filename?>" />
					<input type="hidden" name="module" value="Otg_OutboundTarget" />
					<input type="hidden" name="action" value="StartGenesysImport" />
					<input type="hidden" name="filename_old" value="<?=$filename?>" />
					<input type="hidden" name="filesize" value="<?=$filesize?>" />
					<input type="hidden" name="chain_count" value="<?=$chain_count?>" />
					<input type="hidden" name="blank_chain_count" value="<?=$blank_chain_count?>" />
					<input type="hidden" name="record_count" value="<?=$record_count?>" />
					<input type="hidden" name="result_filename" value="<?=$result_filename?>" />
				</td>
			</tr>
			<tr>
				<td>Genesys Calllist : </td>
				<td>
					<select id="calllists" name="calllists"></select>
				</td>
			</tr>
			<tr>
				<td>isAppend : </td>
				<td>
					<input type="checkbox" name="isappend" value="1" checked="checked" />
				</td>
			</tr>
			<tr>
				<td>Auto Commit after validated : </td>
				<td>
					<input type="checkbox" name="isautocommit" value="1" />
				</td>
			</tr>
			<tr>
				<td></td>
				<td><button>Start Genesys Import</button></td>
			</tr>
		</table>
	</form>
</div>
<script type="text/javascript">
	function adjust_Calllists(){
		var params = {wsparam:{}};
		jQuery.post("?module=Otg_OutboundTarget&action=ws_Genesys_GetCalllists&to_pdf=1",params, null, 'json')
		.done(function(data){
// 			console.log(data);
			jQuery('#calllists').find('option').remove();
// 			console.log(data.GetGenesysCallingListResult.string);
			jQuery.each(data.GetGenesysCallingListResult.string, function(){
// 				console.log(this);
				jQuery('#calllists').append('<option value="'+this+'">'+this+'</option>');
			});
		})
		.error(function(e){
			alert("Ajax Error : adjust_Calllists()");
		});
	}
	
	adjust_Calllists();
</script>
<?php
	
}catch(Exception $e){
	echo '<div style="color:red;">Error : '.$e.'</div>';
}
?>