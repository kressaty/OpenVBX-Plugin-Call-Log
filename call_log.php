<?php

	$ci = & get_instance();

	$ci->load->helper('format_helper');
	require_once(APPPATH . 'libraries/twilio.php');
	
	$ci->twilio = new TwilioRestClient($ci->twilio_sid,
										 $ci->twilio_token,
										 $ci->twilio_endpoint);
	
	
	if ($ci->uri->segment(3) != null)
	{
		$page = $ci->uri->segment(3);
	}
	else
	{
		$page = "0";
	}
	
	$log_url = "Accounts/{$this->twilio_sid}/Calls";
	$log_method = "GET";
	$log_params = array('page' => $page, 'num' => '25');
	$log = $ci->twilio->request($log_url, $log_method, $log_params);
	
	$log_xml = $log->ResponseXml;
	
	function status_text($status)
	{
	
		if($status == 0)
		{
			return "Not Yet Dialed";
		}
		elseif($status == 1)
		{
			return "In Progress";
		}
		elseif($status == 2)
		{
			return "Completed";
		}
		elseif($status == 3)
		{
			return "Busy";
		}
		elseif($status == 4)
		{
			return "App Error";
		}
		elseif($status == 5)
		{
			return "No Answer";
		}
		else
		{
			return "Status Unknown";
		}
	}
	
?>

<div class="vbx-plugin">
	
	<h3>Complete Call Log</h3>
	<table>
		<thead>
			<tr>
				<th>Number</th>
				<th>Start Time</th>
				<!--<th>End Time</th>-->
				<th>Duration</th>
				<th>Called</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($log_xml->Calls->Call as $call): ?>
			<tr>
				<td><?php echo format_phone($call->Caller);?></td>
				<td><?php echo date('D, M j Y g:i a', strtotime($call->StartTime));?> GMT</td>
				<!--<td><?php echo date('D, M j Y g:i a', strtotime($call->EndTime));?> GMT</td>-->
				<td><?php echo $call->Duration;?> sec</td>
				<td><?php echo format_phone($call->Called);?></td>
				<td><?php echo status_text($call->Status);?></td>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>

<div class="log_pagination" style="float: right;">

	<?php if($ci->uri->segment(3) != "0" AND $ci->uri->segment(3) != ""):?>
	<a href="<?php echo base_url();?>p/call_log/<?php echo ($ci->uri->segment(3) - 1);?>">Previous</a>
	<?php endif?>
	

	<?php if($log_xml->Calls['numpages']-1 > $ci->uri->segment(3)):?>
	<a href="<?php echo base_url();?>p/call_log/<?php echo ($ci->uri->segment(3) + 1);?>">Next</a>
	<?php endif?>
	
</div>
<br />
</div>