<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once "debug.php";
require_once "config.php";
require_once "PHPExcel.php";
require_once "myAmo.php";
require_once "amocrm.php";
// $excel = PHPExcel_IOFactory::load('upld/ex.xlsx');
// foreach($excel ->getWorksheetIterator() as $worksheet) {
// 	$lists[] = $worksheet->toArray();
// }

$contacts;
// if(!empty($lists)){
// 	foreach($lists as $list){
// 	 	foreach($list as $row){
// 	   		$contacts[] = 	[
// 	   							'name' => $row['0'],
// 	   							'email' => $row['1'],
// 	   							'phone' => $row['2'],
// 	   						];
// 	 	}
// 	}

	$Amo = new myAmo;
	$Amo->domain = DOMAIN;
	$Amo->USER_LOGIN = USER_LOGIN;
	$Amo->USER_HASH = USER_HASH;
	$current_acc = $Amo->AmoMethod('private/api/v2/json/accounts/current');	
	//$current_acc = $Amo->AmoMethod('private/api/v2/json/contacts/list');	
	// foreach($current_acc['response']['account']['custom_fields']['leads'] as $curlead){
	// 	if($curlead['name'] == 'Источник сделки'){
	// 		$leadFrom = $curlead['id'];
	// 	}
	// 	if($curlead['name'] == 'Продукт'){
	// 		$leadProduct = $curlead['id'];
	// 	}
	// }

	// $similiar = $Amo->AmoMethod('private/api/v2/json/leads/list');
	// debug($similiar);
	    $note = new Note();
        $note->setElementId('1620699');
        $note->setElementType(NOTE::TYPE_LEAD);
        $note->setText('text');
	class Main{
	    private $AMQ;
	    private $subdomain;
	    private $user_login;
	    private $user_hash;
	    
	    public function __construct(){
	        require_once dirname(__FILE__).'/amocrm/amocrm.php';
	        $this->user_login = USER_LOGIN;
	        $this->user_hash  = API_KEY;
	        $this->subdomain = AMOCRM_DOMAIN;
	        $this->user = array(
	            'USER_LOGIN'=>$this->user_login, 
	            'USER_HASH'=>$this->user_hash 
	        );
	        $this->AMQ = new amoQuery($this->subdomain, $this->user);
	    }        

	$Amo->AmoAddTasksToLista('1620699', 2, 'Выявить интерес');
	$added_task = $Amo->AmoAddAllTasks();
	debug($added_task); 
	debug($current_acc); 
	// print_r($similiar);
	// echo $leadProduct;
	// echo $leadFrom;

	// debug($current_acc);
// 	$response_ar = parse_ini_file("response.ini", true);
// 	$response_count = $response_ar['response']['count'];

// 	$response = $current_acc['response']['account']['users'][$response_count]['id'];
// 	$r = $response_count + 1;
// 	if(isset($current_acc['response']['account']['users'][$r]['id'])){
// 		$response_ar['response']['count'] = $response_count + 1;
// 	}else{
// 		$response_ar['response']['count'] = 0;
// 	}

// 	$str = "[response]\r\n";
// 	$str .="count = ".$response_ar['response']['count']."\r\n";
// 	file_put_contents('response.ini', $str);



// 	foreach($current_acc['response']['account']['custom_fields']['contacts'] as $curCon){
// 		if($curCon['name'] == 'Телефон'){
// 			$phone = $curCon['id'];
// 		}
// 		if($curCon['name'] == 'Email'){
// 			$mail = $curCon['id'];
// 		}
// 	}
// 	foreach($current_acc['response']['account']['task_types'] as $tt){
// 		if($tt['name'] == 'Выявить интерес'){
// 			$task_type = $tt['id'];
// 		}
// 	}
// 	if(!$task_type){
// 		$task_type = 1;
// 	}

// 	foreach($contacts as $contact){
// 		// $Amo->AmoAddleadsToList('Сделка из Экселя', $response);
// 		// $added_lead = $Amo->AmoAddAllleads();
// 		// sleep(1);
// 		// $Amo->AmoAddContactsToList($contact['name'], $added_lead['response']['leads']['add'][0]['id'], $phone, $contact['phone'], $mail, $contact['email'], $response);
// 		// $added_contact = $Amo->AmoAddAllContacts();
// 		// sleep(1);
// 		// // print_r($added_lead['response']['leads']['add'][0]['id']);
// 		// $Amo->AmoAddTasksToLista($added_lead['response']['leads']['add'][0]['id'], 1, 'Выявить интерес');
// 		// $added_task = $Amo->AmoAddAllTasks();
// 	}





// 	debug($current_acc);
// 	// print_r($current_acc);
// }
