<?php
class amoQuery
{
    public $result;
    public $user = array();
    public $domain;
    
    public function __construct($domain, $user)
    {
        $this->domain = $domain;
        $this->user = $user;
        $this->amoRequest(new Request((Request::AUTH), $this));
    }
    
    public function amoRequest(Request $requestOption, $decodeOption = false)
    {
        $link='https://'.$this->domain.'.amocrm.ru/private/api/'.$requestOption->getURL();

        echo ('link: <br>');
        print_r($link);
        echo ('<br>');
        
        echo ('request: <br>');
        print_r($requestOption);
        echo ('<br>');

        $curl=curl_init();
        curl_setopt($curl,CURLOPT_URL,$link);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
        curl_setopt($curl,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt');
        curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt');

        if ($requestOption->post) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestOption->getParams())); //УТОЧНИТЬ
        }

        $result = curl_exec($curl);
        $info = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $output = htmlspecialchars_decode($result);
        
        curl_close($curl);
        $this->checkResponse($info);

        $this->result = json_decode($result, $decodeOption);

        $this->result = isset($this->result->response) ? $this->result->response : false;
        $this->last_insert_id = ($requestOption->post && isset($this->result->{$requestOption->type}->{$requestOption->action}[0]->id))? $this->result->{$requestOption->type}->{$requestOption->action}[0]->id : false;
        return $this;
    }
    
    public function checkResponse($code)
    {
        $code=(int)$code;
        $errors=array(
            301=>'Moved permanently',
            400=>'Bad request',
            401=>'Unauthorized',
            403=>'Forbidden',
            404=>'Not found',
            500=>'Internal server error',
            502=>'Bad gateway',
            503=>'Service unavailable'
        );
        try
        {
            if($code!=200 && $code!=204)
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
        }
        catch(Exception $E)
        {
            die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
        }        
    }
}


class Request
{
    const AUTH = 1;
    const INFO = 2;
    const GET = 3;
    const SET = 4;

    public $post;
    public $url;
    public $type;
    public $action;
    public $params;
    public $key_name;

    private $if_modified_since;
    private $object;
    private $act;

    public function __construct($request_type = null, $params = null, $object = null)
    {
        $this->post = false;      
        $this->params = $params;       
        $this->object = $object;
        $this->act = $request_type;

        switch ($request_type) {
            case Request::AUTH:
                $this->createAuthRequest();
                break;
            case Request::INFO:
                $this->createInfoRequest();
                break;
            case Request::GET:
                $this->createGetRequest();
                break;
            case Request::SET:
                $this->createPostRequest();
                break;
        }
    }

    public function getAct()
    {
        return $this->act;
    }
    
    public function getURL()
    {
        return $this->url;
    }
    
    public function getParams()
    {
        return $this->params;
    }
    
    public function setIfModifiedSince($if_modified_since)
    {
        $this->if_modified_since = $if_modified_since;
    }

    public function getIfModifiedSince()
    {
        return empty($this->if_modified_since) ? false : $this->if_modified_since;
    }

    private function createAuthRequest()
    {
        $this->post = true;
        $this->url = 'auth.php?type=json';
        $this->params = [
            'USER_LOGIN' => $this->params->user['USER_LOGIN'],
            'USER_HASH' => $this->params->user['USER_HASH']
        ];

    }

    private function createInfoRequest()
    {
        $this->url = 'v2/json/accounts/current';
    }

    private function createGetRequest()
    {
        $this->url = 'v2/json/' . $this->object[0] . '/' . $this->object[1];
        $this->url .= (count($this->params) ? '?' . http_build_query($this->params) : '');

    }

    private function createPostRequest()
    {
        if (!is_array($this->params)) {
            $this->params = [$this->params];
        }
        
        $key_name = $this->params[0]->key_name;
        $url_name = $this->params[0]->url_name;
        $id = $this->params[0]->id;

        $action = (isset($id)) ? 'update' : 'add';
        $params = [];
        $params['request'][$key_name][$action] = $this->params;

        $this->post = true;
        $this->type = $key_name;
        $this->action = $action;
        $this->url = 'v2/json/' . $url_name . '/set';
        $this->params = $params;
    }
}

class Note
{
    public $element_id;
    public $element_type;
    public $note_type;
    public $text;
    const DEAL_CREATED = 1;            // Сделка создана
    const CONTACT_CREATED = 2;         // Контакт создан
    const DEAL_STATUS_CHANGED = 3;     // Статус сделки изменен
    const COMMON = 4;                  // Обычное примечание
    const ATTACHEMENT = 5;             // Файл
    const CALL = 6;                    // Звонок приходящий от айфон приложений
    const MAIL_MESSAGE = 7;            // Письмо
    const MAIL_MESSAGE_ATTACHMENT = 8; // Письмо с файлом
    const CALL_IN = 10;                // Входящий звонок
    const CALL_OUT = 11;               // Исходящий звонок
    const COMPANY_CREATED = 12;        // Компания создана
    const TASK_RESULT = 13;            // Результат по задаче
    const SMS_IN = 102;                // Входящее смс
    const SMS_OUT = 103;               // Исходящее смс
    const TYPE_CONTACT = 1;            // Привязка к контакту
    const TYPE_LEAD = 2;               // Привязка к сделке
    
    public function __construct()
    {
        $this->key_name = 'notes';
        $this->url_name = $this->key_name;
    }
    public function setElementId($value)
    {
        $this->element_id = $value;
        return $this;
    }
    public function setElementType($value)
    {
        $this->element_type = $value;
        return $this;
    }
    public function setNoteType($value)
    {
        $this->note_type = $value;
        return $this;
    }
    public function setText($value)
    {
        $this->text = $value;
        return $this;
    }
}

class Task
{
    public $element_id;
    public $element_type;
    public $task_type;
    public $responsible_user_id;
    public $complete_till;
    public $text;
    const CALL = 1;
    const MEETING = 2;
    const LETTER = 3;
    const TYPE_CONTACT = 1; // Првязка к контакту
    const TYPE_LEAD = 2; // Привязка к сделке
    public function __construct()
    {
        $this->key_name = 'tasks';
        $this->url_name = $this->key_name;
    }
    public function setElementId($value)
    {
        $this->element_id = $value;
        return $this;
    }
    public function setElementType($value)
    {
        $this->element_type = $value;
        return $this;
    }
    public function setTaskType($value)
    {
        $this->task_type = $value;
        return $this;
    }
    public function setResponsibleUserId($value)
    {
        $this->responsible_user_id = $value;
        return $this;
    }
    public function setCompleteTill($value)
    {
        $this->complete_till = $value;
        return $this;
    }
    public function setText($value)
    {
        $this->text = $value;
        return $this;
    }
}

class Pipeline
{
    public $key_name;
    public $url_name;  
    public $name;
    public $responsible_user_id;
    public $tags;
    public $custom_fields;
    public $id;
    public $tags_array;
    public $last_modified;

    public function __construct()
    {
        $this->key_name = 'pipelines';
        $this->url_name = $this->key_name;
    }
   
    public function setName($value)
    {
        $this->name = $value;
    }

    public function setResponsibleUserId($value)
    {
        $this->responsible_user_id = $value;
    }

    public function setTags($value)
    {
        if (!is_array($value)) {
                $value = [$value];
        }

        $this->tags_array = array_merge($this->tags_array, $value);
        $this->tags = implode(',', $this->tags_array);
    }

    public function setCustomField($name, $value, $enum = false)
    {
        $field = [
                'id' => $name,
                'values' => []
        ];

        $field_value = [];
        $field_value['value'] = $value;

        if ($enum) {
            $field_value['enum'] = $enum;
        }

        $field['values'][] = $field_value;

        $this->custom_fields[] = $field;
    }
}

class Lead
{
    public $key_name;
    public $url_name;
    public $name;
    public $status_id;
    public $responsible_user_id;
    public $tags_array;
    public $custom_fields;
    public $idRequest;
    public $contactID;
    public $pipeline_id;
    public $id;
    public $last_modified;


    public function __construct()
    {
        $this->key_name = 'leads';
        $this->url_name = $this->key_name;
        $this->custom_fields = [];
        $this->tags_array = [];
    }
    
    public function setContactID($value)
    {
        $this->contactID = $value;
    }
    
    public function setPipelineID($value)
    {
        $this->pipeline_id = $value;
    }


    public function setName($value)
    {
        $this->name = $value;
    }

    public function setStatusId($value)
    {
        $this->status_id = $value;
    }

    public function setResponsibleUserId($value)
    {
        $this->responsible_user_id = $value;
    }

    public function setPrice($value)
    {
        $this->price = $value;
    }

    public function setTags($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $this->tags_array = array_merge($this->tags_array, $value);
        $this->tags = implode(',', $this->tags_array);
    }

    public function setCustomField($name, $value, $enum = false)
    {
        $field = [
            'id' => $name,
            'values' => []
        ];

        $field_value = [];
        $field_value['value'] = $value;

        if ($enum) {
            $field_value['enum'] = $enum;
        }

        $field['values'][] = $field_value;

        $this->custom_fields[] = $field;
    }
}

class ContactLeadsUpdate
{
    public $key_name;
    public $url_name;  
    public $last_modified;
    public $linked_leads_id;
    public $id;
    

    public function __construct()
    {
        $this->key_name = 'contacts';
        $this->url_name = $this->key_name;
        $this->linked_leads_id = [];
    }
   
    public function setLinkedLeadsId($value)
    {
        if (!is_array($value)) {
                $value = [$value];
        }
        $this->linked_leads_id = array_merge($this->linked_leads_id, $value);
    }
}

class Contact
{
    public $key_name;
    public $url_name;  
    public $name;
    public $responsible_user_id;
    public $tags;
    public $custom_fields;
    public $id;
    public $tags_array;
    public $last_modified;
    public $linked_leads_id;    
    

    public function __construct()
    {
        $this->key_name = 'contacts';
        $this->url_name = $this->key_name;
        $this->linked_leads_id = [];
        $this->custom_fields = [];
        $this->tags_array = [];
    }
   
    public function setName($value)
    {
        $this->name = $value;
    }

    public function setResponsibleUserId($value)
    {
        $this->responsible_user_id = $value;
    }

    public function setLinkedLeadsId($value)
    {
        if (!is_array($value)) {
                $value = [$value];
        }
        $this->linked_leads_id = array_merge($this->linked_leads_id, $value);
    }
    
    
    public function setTags($value)
    {
        if (!is_array($value)) {
                $value = [$value];
        }

        $this->tags_array = array_merge($this->tags_array, $value);
        $this->tags = implode(',', $this->tags_array);
    }

    public function setCustomField($name, $value, $enum = false)
    {
        $field = [
                'id' => $name,
                'values' => []
        ];

        $field_value = [];
        $field_value['value'] = $value;

        if ($enum) {
            $field_value['enum'] = $enum;
        }

        $field['values'][] = $field_value;

        $this->custom_fields[] = $field;
    }
}