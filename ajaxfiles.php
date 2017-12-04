<?php 

require_once "debug.php";
require_once "config.php";
require_once "PHPExcel.php";
require_once "myAmo.php";
$err_arr;
if ($_FILES['xlsfile']['error'] == 0) {
        $tmp_name = $_FILES['xlsfile']["tmp_name"];
        $name = $_FILES["xlsfile"]["name"];	
        move_uploaded_file($tmp_name, 'upld/'.$name);

		$excel = PHPExcel_IOFactory::load('upld/'.$name);

		foreach($excel ->getWorksheetIterator() as $worksheet) {
			$lists[] = $worksheet->toArray();
		}
		$Amo = new myAmo;
		$Amo->domain = DOMAIN;
		$Amo->USER_LOGIN = USER_LOGIN;
		$Amo->USER_HASH = USER_HASH;
		$current_acc = $Amo->AmoMethod('private/api/v2/json/accounts/current');	
		$contacts;		
		if(!empty($lists)){
			foreach($lists as $list){
			 	foreach($list as $row){
			 		if(!is_null($row['0'])){
						// $response_ar = parse_ini_file("response.ini", true);

						// $response_count = $response_ar['response']['count'];
						// $find = 'N';
						// $r = $response_count + 1;			
						// while($find != 'Y'){				
						// 	if(isset($current_acc['response']['account']['users'][$r]['id'])){
						// 		if($current_acc['response']['account']['users'][$r]['group_id'] == 0){
						// 			$r = $r;
						// 			$find = 'Y';
						// 		}else{
						// 			$r = $r + 1;
						// 		}
						// 	}else{
						// 		$r = 0;
						// 	}				
						// }
						// $str = "[response]\r\n";
						// $str .="count = ".$r."\r\n";
						// file_put_contents('response.ini', $str);
						// $response = $current_acc['response']['account']['users'][$response_count]['id'];			 		
					 		if($row['0'] == ''){
					 			$row['0'] == '-';
					 		}
					 		if($row['1'] == ''){
					 			$row['1'] == '-';
					 		}
					 		if($row['2'] == ''){
					 			$row['2'] == '-';
					 		}
					 		if($row['3'] == ''){
					 			$row['3'] == '-';
					 		}
					 		if($row['4'] == ''){
					 			$row['4'] == '-';
					 		}
					 		if($row['5'] == ''){
					 			$row['5'] == '-';
					 		}	
					 		if($row['6'] == ''){
					 			$row['6'] == '-';
					 		}					 				 					 					 					 					 		
					   		$contacts[] = 	[
					   							'name' => $row['0'],
					   							'email' => $row['1'],
					   							'phone' => $row['2'],
					   							'product' => $row['3'],
					   							'name_lead' => $row['4'],
					   							'fromlead' => $row['5'],
					   							// 'response' => $response,
					   							'comment' => $row['6'],
					   						];
					}	   						
			 	}
			}









			foreach($current_acc['response']['account']['custom_fields']['contacts'] as $curCon){
				if($curCon['name'] == 'Телефон'){
					$phone = $curCon['id'];
				}
				if($curCon['name'] == 'Email'){
					$mail = $curCon['id'];
				}
			}
			foreach($current_acc['response']['account']['custom_fields']['leads'] as $curlead){
				if($curlead['name'] == 'Источник сделки'){
					$leadFrom = $curlead['id'];
				}
				if($curlead['name'] == 'Продукты Школа'){
					$leadProduct = $curlead['id'];
				}
			}			
		   
				// $all_contacts = takeAll($Amo);
				sleep(1);
				$dublicates;
				unset($contacts[0]);
				$enum = '';
			
			$uniquer_contact=[];
			foreach($contacts as $contact){
				foreach($current_acc['response']['account']['custom_fields']['leads'][3]['enums'] as $key => $val){
					if($val == $contact['fromlead']){
						$enum = $key;
					}
				}
				foreach($current_acc['response']['account']['custom_fields']['leads'][0]['enums'] as $key => $val){
					if($val == $contact['product']){
						$enumprod = $key;
					}
				}	
				$contact['product'];						
				$key = '';
				$double = 'N';
				$double_phone = 'N';
				$dub_ids = '';
				$dub_ids_phone = '';
				$dub_check = dublicate($Amo,$contact['email']);
				$dub_check_phone = dublicate($Amo,$contact['phone']);
				if(!empty($dub_check)){
					foreach($dub_check['response']['contacts'] as $dub_ch){
						foreach($dub_ch['custom_fields'] as $dub_custom){
							if($dub_custom['name'] == 'Email'){
								if(!empty($dub_custom)){
									foreach($dub_custom['values'] as $mail){
										if($mail['value'] == $contact['email']){
											$double = 'Y';
										}
									}
								}
							}
						}
						$dub_ch['dubNot'] = $double;
						if($dub_ch['dubNot'] == 'Y'){
							$dub_ids = $dub_ch['id'];
						}
					}
				}
				if(!empty($dub_check_phone)){
					foreach($dub_check_phone['response']['contacts'] as $dub_ch){
						foreach($dub_ch['custom_fields'] as $dub_custom){
							if($dub_custom['name'] == 'Телефон'){
								if(!empty($dub_custom)){
									foreach($dub_custom['values'] as $mail){
										if($mail['value'] == $contact['phone']){
											$double_phone = 'Y';
										}
									}
								}
							}
						}
						$dub_ch['dubNot'] = $double_phone;
						if($dub_ch['dubNot'] == 'Y'){
							$dub_ids_phone = $dub_ch['id'];
						}
					}
				}				
				if($contact['name_lead'] != ''){
					if($dub_ids != ''){
						$ar2 = 	[
							'id' => $dub_ids,
						];
						$temp_cont = $Amo->AmoMethod('private/api/v2/json/contacts/list', $ar2);
						
						sleep(1);					
						$Amo->AmoAddleadsToList($contact['name_lead'], $temp_cont['response']['contacts'][0]['responsible_user_id'], $leadFrom, $contact['fromlead'], $leadProduct, $contact['product'],$enum,$enumprod);
						$added_lead = $Amo->AmoAddAllleads();
						$temp_leads_links = '';
						sleep(1);						
						$ar = 	[
							'contacts_link' => $dub_ids,
						];
						$temp_links = $Amo->AmoMethod('private/api/v2/json/contacts/links', $ar);
						sleep(1);
						if(!empty($temp_links['response']['links'])){	
							foreach($temp_links['response']['links'] as $links){
								$temp_leads_links[] = $links['lead_id'];
							}
						}
						$temp_leads_links[] = $added_lead['response']['leads']['add'][0]['id'];		 					
						$Amo->AmoUpdateContactsToList($dub_ids,$temp_leads_links);
						$added_contact = $Amo->AmoAddAllContacts();
						sleep(1);
						$Amo->AmoAddTasksToLista($added_lead['response']['leads']['add'][0]['id'], TASK_TYPE, $contact['comment'],$temp_cont['response']['contacts'][0]['responsible_user_id']);
						$added_task = $Amo->AmoAddAllTasks();
						sleep(1);						
					}elseif($dub_ids_phone != ''){
						$ar2 = 	[
							'id' => $dub_ids_phone,
						];
						$temp_cont = $Amo->AmoMethod('private/api/v2/json/contacts/list', $ar2);	
						sleep(1);						
						$Amo->AmoAddleadsToList($contact['name_lead'], $temp_cont['response']['contacts'][0]['responsible_user_id'], $leadFrom, $contact['fromlead'], $leadProduct, $contact['product'],$enum,$enumprod);
						$added_lead = $Amo->AmoAddAllleads();
						$temp_leads_links = '';
						sleep(1);						
						$ar = 	[
							'contacts_link' => $dub_ids_phone,
						];
						$temp_links = $Amo->AmoMethod('private/api/v2/json/contacts/links', $ar);
						sleep(1);
						if(!empty($temp_links['response']['links'])){	
							foreach($temp_links['response']['links'] as $links){
								$temp_leads_links[] = $links['lead_id'];
							}
						}
						$temp_leads_links[] = $added_lead['response']['leads']['add'][0]['id'];							
						$Amo->AmoUpdateContactsToList($dub_ids_phone,$temp_leads_links);
						$added_contact = $Amo->AmoAddAllContacts();
						sleep(1);
						$Amo->AmoAddTasksToLista($added_lead['response']['leads']['add'][0]['id'], TASK_TYPE, $contact['comment'],$temp_cont['response']['contacts'][0]['responsible_user_id']);
						$added_task = $Amo->AmoAddAllTasks();
						sleep(1);							
					}else{
						$response_ar = parse_ini_file("response.ini", true);

						$response_count = $response_ar['response']['count'];
						$find = 'N';
						$r = $response_count + 1;			
						while($find != 'Y'){				
							if(isset($current_acc['response']['account']['users'][$r]['id'])){
								if($current_acc['response']['account']['users'][$r]['group_id'] == 0){
									$r = $r;
									$find = 'Y';
								}else{
									$r = $r + 1;
								}
							}else{
								$r = 0;
							}				
						}
						$str = "[response]\r\n";
						$str .="count = ".$r."\r\n";
						file_put_contents('response.ini', $str);
						$response = $current_acc['response']['account']['users'][$response_count]['id'];						
						$Amo->AmoAddleadsToList($contact['name_lead'], $response, $leadFrom, $contact['fromlead'], $leadProduct, $contact['product'],$enum,$enumprod);
						$added_lead = $Amo->AmoAddAllleads();
						$temp_leads_links = '';
						sleep(1);
						$Amo->AmoAddContactsToList($contact['name'], $added_lead['response']['leads']['add'][0]['id'], '310642', $contact['phone'],'310644', $contact['email'], $response);
						$added_contact = $Amo->AmoAddAllContacts();
						sleep(1);
						$Amo->AmoAddTasksToLista($added_lead['response']['leads']['add'][0]['id'], TASK_TYPE, $contact['comment'],$response);
						$added_task = $Amo->AmoAddAllTasks();
						sleep(1);													
					}
					// if($added_lead['response']['leads']['update'][0]['id'] == ''){
					// 	$Amo->AmoAddTasksToLista($added_lead['response']['leads']['add'][0]['id'], TASK_TYPE, $contact['comment'],$response);
					// }else{
					// 	$Amo->AmoAddTasksToLista($added_lead['response']['leads']['update'][0]['id'], TASK_TYPE, $contact['comment'],$temp_cont['response']['contacts'][0]['responsible_user_id']);
					// }
					// $added_task = $Amo->AmoAddAllTasks();
					// sleep(1);
					$i = 0;

					$uniquer_contact['name'][] = $contact['name'];	
					$uniquer_contact['phone'][] = $contact['phone'];	
					$uniquer_contact['email'][] = $contact['email'];		

					foreach ($all_contacts['response']['contacts'] as $c){
						$c['name'] = checkString($c['name']);
						$contact['name'] = checkString($contact['name']);

						if($c['name'] == $contact['name']){
							$dublicates['name'][] = 	[
															'name' => $contact['name']
														];
						}
						foreach($c['custom_fields'] as $cust){
							if($cust['name'] == 'Телефон'){
								if($cust['values'][0]['value'] == $contact['phone']){
									$dublicates['phone'][] = 	[
																	'phone' => $contact['phone']
																];								
								}
							}							
							if($cust['name'] == 'Email'){
								$cust['values'][0]['value'] = checkString($cust['values'][0]['value']);
								$contact['email'] = checkString($contact['email']);
								if($cust['values'][0]['value'] == $contact['email']){
									$dublicates['email'][] = 	[
																	'email' => $contact['email']
																];								
								}
							}
						}
						$i = $i + 1;

					}					
				}else{
					break;
				}
			}

			$uniquer_contact['name'] = array_unique($uniquer_contact['name']);	
			$uniquer_contact['phone'] = array_unique($uniquer_contact['phone']);	
			$uniquer_contact['email'] = array_unique($uniquer_contact['email']);	
			foreach($uniquer_contact['name'] as $un){
				$arr = dublicate($Amo,$un);	
				if(!empty($arr['response']['contacts'])){
					$temp = [];
					foreach($arr['response']['contacts'] as $arr){
						$temp[] = $arr;
					}
					$dublicates['name'][] = $temp;
				}


			}
			foreach($uniquer_contact['phone'] as $un){
				$arr = dublicate($Amo,$un);	
				if(!empty($arr['response']['contacts'])){
					foreach($arr['response']['contacts'] as $arr){
						$dublicates['phone'][] = $un;
					}
				}

			}	
			foreach($uniquer_contact['email'] as $un){
				$arr = dublicate($Amo,$un);	
				if(!empty($arr['response']['contacts'])){
					foreach($arr['response']['contacts'] as $arr){
						$dublicates['email'][] = $un;
					}
				}

			}							


			unlink('upld/'.$name);
			$lastname == '';
			if(!empty($dublicates)){
				require_once 'similiar.php';
			}else{
				echo '<h2>Дубликатов нет</h2>';
			}
		}else{
			echo 'error';
		}	

}else{
	echo 'error';
}






