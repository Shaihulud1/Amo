<?php
function debug($arr){
	?><pre>
	<?print_r ($arr);?>
	</pre><?	
}
function takeAll($Amo){

	$offset = 0;
	$end = 0;
	$all_contacts = [];
	$ar = 	[
				'limit_rows' => '500',
				'limit_offset' => $offset,
			];
	while($end!=1){
		$temp_contacts = $Amo->AmoMethod('private/api/v2/json/contacts/list',$ar);
		$all_contacts = array_merge($all_contacts, $temp_contacts);
		if(count($temp_contacts) != '500'){
			$end = 1;
		}else{
			$offset = $offset + 500;
			sleep(1);
		}
	}
	return $all_contacts;


}
function dublicate($Amo,$query){


	$ar = 	[
				'query' => $query,
			];

	$temp_contacts = $Amo->AmoMethod('private/api/v2/json/contacts/list',$ar);
	sleep(1);

	return $temp_contacts;


}

function checkString($inputData)
{
    $inputData = strip_tags($inputData);
    $inputData = htmlspecialchars($inputData);
    $inputData = trim($inputData);
    return $inputData;
}