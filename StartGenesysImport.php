<?php

require_once('modules/Otg_OutboundTarget/Otg_OutboundTarget.php');

global $sugar_config;

$db = & PearDatabase::getInstance();

// print_r($sugar_config); exit();

try{
	echo '<h3>Genesys Import Status</h3>';
	
	$directory = "cache/import/";
	
	$filename = $_POST['filename'];
	$filename_old = $_POST['filename_old'];
	$filesize = $_POST['filesize'];
	$calllists = $_POST['calllists'];
	$isappend = $_POST['isappend'];
	$isautocommit = $_POST['isautocommit'];

	$chain_count = $_POST['chain_count'];
	$blank_chain_count = $_POST['blank_chain_count'];
	$record_count = $_POST['record_count'];
	$result_filename = $_POST['result_filename'];
	
	
	if($filename!=$filename_old){
		//TODO: change file name.
		if(!rename($directory.$filename_old, $directory.$filename)){
			throw new Exception('Can not rename file');
		}
	}
	
	//TODO: FTP transfer
	$genesys_config = $sugar_config['genesys'];
	$file = $directory.$filename;
	$remote_file = $filename;
	
	// set up basic connection
	$conn_id = ftp_connect($genesys_config['ftp_host']);
	
	// login with username and password
	$login_result = ftp_login($conn_id, $genesys_config['ftp_user'], base64_decode($genesys_config['ftp_password']));
	
	// turn passive mode on
	ftp_pasv($conn_id, true);

	// upload a file
	//ftp_put($conn_id, $remote_file, $file, FTP_ASCII);
	if (!ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
		throw new Exception("There was a problem while uploading $file\n");
	}
	
	// close the connection
	ftp_close($conn_id);
	
	//TODO: Unlink file on local
	unlink($directory.$filename);
	
	//TODO: call webservice WSDL for start import?>
<style type="text/css">
div.loading-invisible { /*make invisible*/
                        display: none;
                        z-index: 0;
}

div.loading-visible { /*make visible*/
                      display: block;
                      /*position it 200px down the screen*/
                      position:absolute;
                      top: 40%;
                      left: 42.5%;
                      width: 15%;
                      height: auto;
                      text-align: center;
                      /*in supporting browsers, make it
                      a little transparent*/
                      background: #666666;
                      filter: alpha(Opacity=90);
                      -moz-opacity: 0.90;
                      opacity: 0.90;
                      z-index: 110002;
}
div.loading-unblock { /*make unblock*/
                        display: none;
                        z-index: 0;
}
div.loading-block { /*make block*/
                      display: block;
                      /*position it 200px down the screen*/
                      position: absolute;                      
                      top: 0;
                      left: 0;
                      width: 100%;
                      height: 100%;
                      text-align: center;
                      /*in supporting browsers, make it
                      a little transparent*/
                      background: white;
                      filter: alpha(Opacity=60);
                      -moz-opacity: 0.6;
                      opacity: 0.6;
                      z-index: 110001;
}
</style>
<div id="content" style="padding: 10px 0 0 0;">
	<table>
		<tr>
			<td>Import to : </td>
			<td><?=$_POST['calllists']?></td>
		</tr>
		<tr>
			<td>CRM Records count : </td>
			<td><?=$_POST['record_count']?></td>
		</tr>
		<tr>
			<td>CRM Chains count : </td>
			<td><?=$_POST['chain_count']?></td>
		</tr>
		<tr>
			<td>Status : </td>
			<td><span id="validate_status"></span></td>
		</tr>
		<tr>
			<td>Code : </td>
			<td><span id="validate_code"></span></td>
		</tr>
		<tr>
			<td>Description : </td>
			<td><span id="validate_desc"></span></td>
		</tr>
		<tr>
			<td>Valid records : </td>
			<td><span id="validate_validcount"></span></td>
		</tr>
		<tr>
			<td valign="top">Invalid records : </td>
			<td>
				<div id="validate_invalidcount"></div>
				<table id="validate_invalidrecord" border="1"></table>
			</td>
		</tr>
		<tr>
			<td>Download import result : </td>
			<td><span id="download_import_result"></span></td>
		</tr>
	</table>
</div>
<div>
<form name="commitfrm" action="">
	<input type="hidden" id="vRequestID" name="vRequestID" value="" />
	<input type="button" id="btnCommit" value="Commit" disabled="disabled" onclick="commitImport(this.form.vRequestID.value);" />
</form>
</div>



<div id="block-page" class="loading-unblock"><div id="block-page_hdn"></div></div>
<div id="div_loading" class="loading-invisible">
    <table border="0" style="margin-left: auto;margin-right: auto;">
        <tr>
            <td align="center" valign="middle">
            	<!-- <img src="include/images/loading1.gif" style="border: 0;padding:5px;" alt="" /><br /> -->
            	<img src="include/images/loading2.gif" style="border: 0;padding:5px;" alt="" />
            </td>
        </tr>
        <tr>
            <td align="center" valign="middle">
                <font size="2" color="#FFFFFF"><b><span class="message">Loading<br /></span></b></font>
            </td>
        </tr>
    </table>
</div>
<script type="text/javascript">

var genesys_filename = '<?=$filename?>';
var genesys_filesize = '<?=$filesize?>';
var genesys_calllists = '<?=$calllists?>';
var genesys_isappend = '<?=(empty($isappend)?false:true)?>';
var genesys_isautocommit = <?=(empty($isautocommit)?'false':'true')?>;
	
function g_setLoading() {
//	document.getElementsByName("div_loading").className = "loading-visible";
    document.getElementById("div_loading").className = "loading-visible";
}

function g_unsetLoading() {
    document.getElementById("div_loading").className = "loading-invisible";
    //document.getElementById('block-page').style.display='none';
}

function g_setBlock() {
	var D = document;
	jQuery("#block-page").css("height",Math.max(
	        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
	        Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
	        Math.max(D.body.clientHeight, D.documentElement.clientHeight)
	    ));
	jQuery("#block-page").css("width",Math.max(
	        Math.max(D.body.scrollWidth, D.documentElement.scrollWidth),
	        Math.max(D.body.offsetWidth, D.documentElement.offsetWidth),
	        Math.max(D.body.clientWidth, D.documentElement.clientWidth)
	    ));
    D.getElementById("block-page").className = "loading-block";
}

function g_unsetBlock() {
    document.getElementById("block-page").className = "loading-unblock";
}
function loading(){
	g_setLoading();
	g_setBlock();
}
function unloading(){
	g_unsetLoading();
	g_unsetBlock();
	loadingMessage(); // Clear message.
}
function fLoadCommin(i_divLoading){
	g_divLoading = i_divLoading;
//	if(jQuery("#f_"+i_divLoading).html()){
//		return;
//	}
	var v_divPopup = "";
	v_divPopup += "<div id='"+i_divLoading+"_loading' class='loading-invisible'>"+jQuery("#div_loading").html()+"</div>";
	v_divPopup += "<div id='"+i_divLoading+"block-page' class='loading-unblock'>"+jQuery("#block-page").html()+"</div>";
	jQuery("#f_"+i_divLoading).html(v_divPopup);	
}
function fLoading() {
//	return;
	if(!g_divLoading){
		return;
	}
	var D = document;
	jQuery(g_divLoading+"#block-page").css("height",Math.max(
	        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
	        Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
	        Math.max(D.body.clientHeight, D.documentElement.clientHeight)
	    ));
	jQuery(g_divLoading+"#block-page").css("width",Math.max(
	        Math.max(D.body.scrollWidth, D.documentElement.scrollWidth),
	        Math.max(D.body.offsetWidth, D.documentElement.offsetWidth),
	        Math.max(D.body.clientWidth, D.documentElement.clientWidth)
	    ));
    D.getElementById(g_divLoading+"block-page").className = "loading-block";
    
    document.getElementById(g_divLoading+"_loading").className = "loading-visible";
    
}

function fUnloading() {
//	return;
	if(!g_divLoading){
		return;
	}
    document.getElementById(g_divLoading+"_loading").className = "loading-invisible";
    document.getElementById(g_divLoading+"block-page").className = "loading-unblock";
    //document.getElementById('block-page').style.display='none';
}

function loadingMessage(msg) {
	if(msg!=undefined){
		jQuery('div#div_loading').find('span.message').html(msg);
	}else{
		jQuery('div#div_loading').find('span.message').html('Loading<br />');
	}
}

var ws_timeout = 30000;

//#Step 3#
function commitImport(vRequestID){
	loading();
	var params = {wsparam:{vRequestID:vRequestID}};
	jQuery.post("?module=Otg_OutboundTarget&action=ws_Genesys_CommitUpload&to_pdf=1",params, null, 'html')
	.done(function(text_data){

		var data;

		try{
			data = jQuery.parseJSON(text_data);

			var result = data.CommitUploadCallListResult;
			if(result.ImportStatus==1){
				if(confirm('Import '+result.ImportRecordsCnt+' records successfully\n\nGo back to Outbound Target?')){
					window.location="?module=Otg_OutboundTarget&action=index";
				}
				//window.location="?module=Otg_OutboundTarget&action=index";
				jQuery('#btnCommit').attr('disabled', true);
				unloading();
			}else{
				alert('Error : '+result.ImportErrorCode+' : '+result.ImportErrorDesc);
				unloading();
			}
			
		}catch(e){
			var container = jQuery('<div></div>').css('display','none');
			container.html('commitImport('+e.message+') -> ' + text_data);

			jQuery('body').append(container);
			
			alert('Error : commitImport('+e.message+')');
			unloading();
		}
		
	})
	.error(function(e){
		alert("Ajax Error : commitImport('"+vRequestID+"')");
		unloading();
	});
}

//#Step 2#
function checkImportStatus(vRequestID){
	loading();
	jQuery('#div_loading .message').append(' .');
	var params = {wsparam:{vRequestID:vRequestID}, detail:{result_filename:'<?=$result_filename?>'}};
	jQuery.post("?module=Otg_OutboundTarget&action=ws_Genesys_CheckImportStatus&to_pdf=1",params, null, 'html')
	.done(function(text_data){

		var data;

		try{
			data = jQuery.parseJSON(text_data);

			var result = data.GetOCSCallListStatusResult;

			switch(result.ValidatoionStatus){
			case 0 :
				setTimeout(function(){ checkImportStatus(vRequestID); }, ws_timeout);
				break;
			case 1 :
				//alert('Import Completed');
				jQuery('#validate_status').html('Validate success');
				jQuery('#validate_code').html('-');
				jQuery('#validate_desc').html('-');
				jQuery('#validate_validcount').html(result.ValidRecordsCnt);
				jQuery('#validate_invalidcount').html(result.InvalidRecordCnt);
				jQuery('#download_import_result').html('<a href="'+data.newFileName+'" target="_blank">Result file</a>');

				//Display invalidate record
				jQuery('#validate_invalidrecord').find('tr, th').remove();
				if(!jQuery.isEmptyObject(result.InvalidRecordList) && result.InvalidRecordList.InvalidRecord.length>0){
					jQuery('#validate_invalidrecord').append('<tr><th>Record ID</th><th>Error Code</th></tr>');
					jQuery.each(result.InvalidRecordList.InvalidRecord, function(){
						jQuery('#validate_invalidrecord').append('<tr><td><a href="?module=Otg_OutboundTarget&action=DetailView&record='+this.RecordID+'" target="_blank">'+this.RecordID+'</a></td><td>'+this.ErrorCode+'</td></tr>');
					});
					jQuery('#validate_invalidrecord').append('<tr><td colspan="2" style="color:red;">*** The "Commit" button will import with out invalid records.</td></tr>');
				}

				//Enable commit button.
				jQuery('#btnCommit').removeAttr('disabled');
				
				unloading();

				//Auto commit after validated.
				if(genesys_isautocommit){
					jQuery('#btnCommit').click();
				}
				break;
			default :
				jQuery('#validate_status').html('Validate failed');
				jQuery('#validate_code').html(result.ValidationErrorCode);
				jQuery('#validate_desc').html(result.ValidationErrorDesc);
				jQuery('#validate_validcount').html('-');
				jQuery('#validate_invalidcount').html('-');
				unloading();
			}
			
		}catch(e){
			var container = jQuery('<div></div>').css('display','none');
			container.html('checkImportStatus('+e.message+') -> ' + text_data);

			jQuery('body').append(container);
			
// 			alert('Error : checkImportStatus('+e.message+')');
			setTimeout(function(){ checkImportStatus(vRequestID); }, ws_timeout);
// 			unloading();
		}

	})
	.error(function(e){
// 		alert("Ajax Error : checkImportStatus('"+vRequestID+"')");
		setTimeout(function(){ checkImportStatus(vRequestID); }, ws_timeout);
// 		unloading();
	});
}

// #Step 1#
function startImport(){
	loading();
// 	return;
	var params = {wsparam:{'_CampaignName':genesys_calllists, '_FileName':genesys_filename, '_FileSize':genesys_filesize, '_IsAppend':genesys_isappend},
				detail:{result_filename:'<?=$result_filename?>', 
					calllists:'<?=$calllists?>', 
					record_count:'<?=$record_count?>', 
					chain_count:'<?=$chain_count?>', 
					blank_chain_count:'<?=$blank_chain_count?>'}
				};
	jQuery.post("?module=Otg_OutboundTarget&action=ws_Genesys_StartImport&to_pdf=1",params, null, 'json')
	.done(function(data){
// 		console.log(data);
// 		alert("DataLoaded:"+data);
// 		unloading();
		var result = data.UploadCallingListResult;

		if(result.ErrorCode!=undefined && jQuery.trim(result.ErrorCode)!=''){
			alert('Error : '+result.ErrorCode+' : '+result.ErrorMessage);
			unloading();
		}else{
// 			alert(result.RequestID);
			jQuery('#vRequestID').val(result.RequestID);
			setTimeout(function(){ checkImportStatus(result.RequestID); }, ws_timeout);
		}
		
	})
	.error(function(e){
		alert("Ajax Error : startImport()");
		unloading();
	});
}

startImport();

</script>
	
<?php
}catch (Exception $e){
	echo '<div style="color:red;">Error : '.$e.'</div>';
}



/*$result = $db->query('');

$row_count=$db->getRowCount($result);

if($row_count>0){
	
	while( ($row = $db->fetchByAssoc($result))) {
		
	}

}else{
	echo "Not found record.";
}*/
?>