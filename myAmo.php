<?php

class myAmo{

	public $domain;
	public $USER_LOGIN;
	public $USER_HASH;
	public $contacts = [];
    public $contactsUpdate = [];
    public $leads = [];
    public $tasks = [];
    public $notes = [];
    public $searchCont = [];

    public function AmoMethod($method, $params = '')
    {
    	$user_config = 	[
    						'USER_LOGIN' => $this->USER_LOGIN,
    						'USER_HASH' => $this->USER_HASH
    					];
        if($params){
    	   $params = array_merge($user_config, $params);	
        }else{
           $params = $user_config;
        }			
        $url_query = 'https://'.$this->domain.'.amocrm.ru/'.$method.'?'.http_build_query($params);
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url_query);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        $query = curl_exec($curl_handle);
        $query = json_decode($query,true);  
        return $query;  
    }
    public function AmoMethoda($method, $params = '')
    {
        $user_config =  [
                            'USER_LOGIN' => $this->USER_LOGIN,
                            'USER_HASH' => $this->USER_HASH
                        ];
        if($params){
           $params = array_merge($user_config, $params);    
        }else{
           $params = $user_config;
        }           
        $url_query = 'https://'.$this->domain.'.amocrm.ru/'.$method.'?'.http_build_query($params);
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url_query);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        $query = curl_exec($curl_handle);
        $query = json_decode($query,true);  

        return $query;  
    }

    public function AmoAddContactsToList($name,$leadId, $phoneID,$phoneVAL,$emailID,$emailVAL, $nominated='')
    {
		$this->contacts['request']['contacts']['add'][] = array(
													    'name'=>$name, #Имя контакта
                                                        'linked_leads_id' => $leadId,
                                                        'responsible_user_id' => $nominated,
                                                        'custom_fields'=>array(
                                                          array(
                                                            'id'=>$phoneID, 
                                                            'values'=>array(
                                                              array(
                                                                'value'=>$phoneVAL,
                                                                'enum'=>'WORK'
                                                              ),
                                                            )
                                                          ),array(
                                                            'id' => $emailID,
                                                            'values' =>array(
                                                                array(
                                                                    'value' => $emailVAL,
                                                                    'enum' => 'WORK'
                                                                    ),
                                                                ) 
                                                            ),                                                        
                                                        )
													 ); 

    }   
    public function AmoAddAllContacts()
    {
    	$params = $this->contacts;
    	$method = 'private/api/v2/json/contacts/set';
    	$user_config = 	[
    						'USER_LOGIN' => $this->USER_LOGIN,
    						'USER_HASH' => $this->USER_HASH
    					];

    	$params = array_merge($user_config, $params);				
        $url_query = 'https://'.$this->domain.'.amocrm.ru/'.$method.'?'.http_build_query($user_config);
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url_query);
        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST,'POST');
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS,json_encode($params));
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        $query = curl_exec($curl_handle);
        $query = json_decode($query,true); 
         //print_r($query);
        $this->contacts = '';
        return $query;    	
    }
   
   
    public function AmoAddleadsToList($name,$otv,$fromId,$fromval,$productId,$productval,$enum,$enumprod,$tags = '')
    {
        $this->leads['request']['leads']['add'][] = array(
                                                        'name'=> $name,
                                                        // 'status_id' => $status_id,
                                                        'responsible_user_id' => $otv,
                                                        'tags' => $tags,
                                                        'custom_fields'=>array(
                                                          array(
                                                            'id'=>$fromId, 
                                                            'values'=>array(
                                                              array(
                                                                'enum' => $enum,
                                                                'value'=>$fromval,
                                                              ),
                                                            )
                                                          ),array(
                                                            'id' => $productId,
                                                            'values' =>array(
                                                                array(
                                                                    'value' => $productval,
                                                                    'enum' => $enumprod
                                                                    ),
                                                                ) 
                                                            ),                                                        
                                                        )                                                        
                                                    );                                                                                                      
    }
    public function AmoUpdateContactsToList($updateId,$updateLead)
    {
        $cur_date = date("y-m-d H:i:s");
        $cur_date = strtotime($cur_date);
        $this->contacts['request']['contacts']['update'][] =   array(
                                                        'id'=>$updateId, 
                                                        'last_modified' => $cur_date,
                                                        'linked_leads_id' => $updateLead
                                                     ); 

    }     

    public function AmoAddAllLeads()
    {
        $params = $this->leads;
        // print_r($params);

        $method = 'private/api/v2/json/leads/set';
        $user_config =  [
                            'USER_LOGIN' => $this->USER_LOGIN,
                            'USER_HASH' => $this->USER_HASH
                        ];

        $params = array_merge($user_config, $params);               
        $url_query = 'https://'.$this->domain.'.amocrm.ru/'.$method.'?'.http_build_query($user_config);
        // echo $url_query;
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url_query);
        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST,'POST');
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS,json_encode($params));
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        $query = curl_exec($curl_handle);
        $query = json_decode($query,true); 
        // print_r($query);
        $this->leads = '';
        return $query;      
    }  
    public function AmoAddTasksToList($leadId,$type,$comment,$response=NULL)
    {
        // $this->tasks['request']['tasks']['add'][] = array(
        //                                             'element_id' => $leadId,
        //                                             'responsible_user_id' => $response,
        //                                             'element_type' => '2',
        //                                             'task_type' => $type,
        //                                             'text' => $comment,
        //                                             'complete_till' => date("H:i")                                                  
        //                                         );  
        $this->tasks['request']['tasks']['add'][] = array(
                                                    'element_id' => $leadId,
                                                    'element_type' => '2',
                                                    'responsible_user_id' => $response,                                                   
                                                    'task_type' => '3',
                                                    'text' => 'text',
                                                    'complete_till' => date("H:i")                                                  
                                                );         


    }
    public function AmoAddTasksToLista($leadId,$type,$comment,$response=NULL)
    {
        $this->tasks['request']['tasks']['add']=array(
            array(
              #Привязываем к контакту
                'element_id'=>$leadId,
                'element_type'=>2,
                'responsible_user_id' => $response,               
                'task_type'=>$type,
                'text'=>$comment,
                'complete_till'=>'23:59'
            ),
          
        );

/*        $this->tasks['request']['tasks']['add'][] = array(
                                                    "element_id" =>  1761905,
                                                    "element_type" => 2,
                                                    "task_type" => 4,
                                                    "text" => "first",
                                                    "complete_till" => time() + 1000,                                                 
                                                    "responsible_user_id" => "1931620"
                                                );  */

    }    
    public function AmoAddAllTasks()
    {
        $params = $this->tasks;

        $user_config =  [
                            'USER_LOGIN' => $this->USER_LOGIN,
                            'USER_HASH' => $this->USER_HASH
                        ];
        $link='https://'.$this->domain.'.amocrm.ru/private/api/v2/json/tasks/set'.'?'.http_build_query($user_config);
        
        $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
        #Устанавливаем необходимые опции для сеанса cURL
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
        curl_setopt($curl,CURLOPT_URL,$link);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($params));
        curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
        curl_setopt($curl,CURLOPT_HEADER,false);
        curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
         
        $query = curl_exec($curl);
        $query = json_decode($query,true); 
        // print_r($query);
        $this->tasks = '';
        return $query;       
        //$this->CheckCurlResponse($code);

/*        echo 'params: ';
             print_r($params);

        $method = 'private/api/v2/json/tasks/set';
        $user_config =  [
                            'USER_LOGIN' => $this->USER_LOGIN,
                            'USER_HASH' => $this->USER_HASH
                        ];

        $params = array_merge($user_config, $params);               
        $url_query = 'https://'.$this->domain.'.amocrm.ru/'.$method.'?'.http_build_query($user_config);
         echo $url_query;
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url_query);
        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST,'POST');
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS,json_encode($params));
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        $query = curl_exec($curl_handle);
        $query = json_decode($query,true); 
        // print_r($query);
        $this->tasks = '';
        return $query;       */
    }  

    public function AmoAddNotesToList($contact_id,$comment,$note_type = '1',$element_type= "2")
    {
        $this->notes['request']['notes']['add'][] = array(
                                                    'element_id'=> $contact_id,
                                                    'element_type' => $element_type,
                                                    'note_type' => $note_type,
                                                    'text' => $comment                                                                                                  
                                                );  
    }
    public function AmoAddAllNotes()
    {
        $params = $this->notes;
        $method = 'private/api/v2/json/notes/set';
        $user_config =  [
                            'USER_LOGIN' => $this->USER_LOGIN,
                            'USER_HASH' => $this->USER_HASH
                        ];

        $params = array_merge($user_config, $params);               
        $url_query = 'https://'.$this->domain.'.amocrm.ru/'.$method.'?'.http_build_query($user_config);
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url_query);
        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST,'POST');
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS,json_encode($params));
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        $query = curl_exec($curl_handle);
        $query = json_decode($query,true); 
        // print_r($query);
        $this->notes = '';
        return $query;          
    }      

    
}  

