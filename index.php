<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
header('Content-Type: application/json'); // Only necessary if the request asks for it. The content will be send anyway. POSTMAN use this value to preset the viewer used to show the data

session_start();
$_SESSION['uas_logging_file'] = 'C:/Logs/api.json';
$_SESSION['uas_write_log'] = 100;

// $postdata = file_get_contents("php://input");
// echo '---'.$postdata.'---';

/*
echo ('===================================<br>RawData:<br><pre>');
print_r(file_get_contents("php://input"));
echo ('</pre><br>===================================<br>FormData:<br><pre>');
print_r($_POST);
echo('</pre>');
*/

// http://localhost:86/my/api/RequestObject.php?PrintRequestObject=1

/*
* Request Object structure
*/
class RequestObject implements JsonSerializable {

    // Resource Identifier
    public $ResourceName;

    // Source Identifier (Might be a specific database or file known to the api)
    public $PrefferedSource;

    // Result data
    public $ProcessedFields;

    // Fields requested. by default all fields are requested
    public $BuildOptions = Array();
    
    // Where conditions to apply
    public $WhereConditions;
    
    // Relations
    public $Relations;

    public function __construct() {
        //$this->sql = $processed_sql_statement;
    }

    public function Pack($json)
    {
        // $json = '{ "e": "Customers", "s": "2", "f": [ { "n": "Age", "i": -1, "o": 1, "x": 1 }, { "n": "Age", "i": -1, "o": 1, "x": 1 } ], "o": { "i" : ["ForeignId", "LongText"] }, "w": { "c": 0, "v": 10, "a": [], "o": [ ], "w": 20 }, "r": null }';
        $data = json_decode($json);

        $this->ResourceName = $data->e;
        $this->PrefferedSource = $data->s;
        $this->ProcessedFields = $data->f;
        $this->BuildOptions = $data->o;
        $this->WhereConditions = $data->w;
        $this->Relations = null;
    }

    public function jsonSerialize() {
        $this->content =
        [
            'e' => $this->ResourceName,
            's' => $this->PrefferedSource,
            'f' => $this->ProcessedFields,
            'o' => $this->BuildOptions,
            'w' => $this->WhereConditions,
            'r' => $this->Relations
        ];
        return $this->content;
    }
}



class APIManager {

    public function MakeAPIRequestType2($RequestObject){
        if(isset($_SESSION['uas_write_log']) && $_SESSION['uas_write_log'] > 0) file_put_contents($_SESSION['uas_logging_file'], ", " . 
        json_encode(Array(
            "id"=>$_SESSION['uas_logging_id'], 
            "fp"=>__FILE__, 
            "ln"=>__LINE__, 
            "ns"=>__NAMESPACE__,
            "class"=>__CLASS__,  
            "function"=>(__FUNCTION__ <> "" ? __FUNCTION__ : __METHOD__),  
            "variables"=>get_defined_vars(),
            "stack"=>debug_backtrace(FALSE, 1),
            "timestamp"=>time()
        )) . PHP_EOL, FILE_APPEND | LOCK_EX);  $_SESSION['uas_logging_id'] += 1; // TO DO: Do not forget to remove this line !
        
        $url = 'http://localhost:86/my/Users/Interactions.php?user_id=9';
        $data = array('select' => 'plain', 'list' => 'connections', 'fetch' => 'objects', 'startTime' => '1890.15');
        $data = array(
            'select' => 'plain', 
            'list' => 'records', 
            'database' => 'Northwind (MsSQL)', 
            'connection' => 3, 
            'schema' => explode('.', $RequestObject->ResourceName)[0],  
            'table' => explode('.', $RequestObject->ResourceName)[1], 
            'fetch' => 'objects', 
            'startTime' => '1890.15'
        );
            
        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) { /* Handle error */ }
        
        return $result;
        // echo json_encode($RequestObject, JSON_PRETTY_PRINT);
    }
    
    public function MakeAPIRequestType1($RequestObject){
        if(isset($_SESSION['uas_write_log']) && $_SESSION['uas_write_log'] > 0) file_put_contents($_SESSION['uas_logging_file'], ", " . 
        json_encode(Array(
            "id"=>$_SESSION['uas_logging_id'], 
            "fp"=>__FILE__, 
            "ln"=>__LINE__, 
            "ns"=>__NAMESPACE__,
            "class"=>__CLASS__,  
            "function"=>(__FUNCTION__ <> "" ? __FUNCTION__ : __METHOD__),  
            "variables"=>get_defined_vars(),
            "stack"=>debug_backtrace(FALSE, 1),
            "timestamp"=>time()
        )) . PHP_EOL, FILE_APPEND | LOCK_EX);  $_SESSION['uas_logging_id'] += 1; // TO DO: Do not forget to remove this line !
        
        $url = 'http://localhost:86/my/api/index.php';
        // $data = array('select' => 'plain', 'list' => 'connections', 'fetch' => 'objects', 'startTime' => '1890.15');
        // echo explode('.', $RequestObject->ResourceName)[0];
        // echo explode('.', $RequestObject->ResourceName)[1];
        $data = array(
            'select' => 'plain', 
            'list' => 'records', 
            'database' => 'Northwind (MsSQL)', 
            'connection' => 3,   
            'schema' => explode('.', $RequestObject->ResourceName)[0],  
            'table' => explode('.', $RequestObject->ResourceName)[1], 
            'fetch' => 'objects', 
            'startTime' => '1890.15'
        );
            
        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) { /* Handle error */ }
        
        return $result;
        // echo json_encode($RequestObject, JSON_PRETTY_PRINT);
    }

    public function InitializeRequest()
    {
        if(isset($_SESSION['uas_write_log']) && $_SESSION['uas_write_log'] > 0) file_put_contents($_SESSION['uas_logging_file'], ", " . 
        json_encode(Array(
            "id"=>$_SESSION['uas_logging_id'], 
            "fp"=>__FILE__, 
            "ln"=>__LINE__, 
            "ns"=>__NAMESPACE__,
            "class"=>__CLASS__,  
            "function"=>(__FUNCTION__ <> "" ? __FUNCTION__ : __METHOD__),  
            "variables"=>get_defined_vars(),
            "stack"=>debug_backtrace(FALSE, 1),
            "timestamp"=>time()
        )) . PHP_EOL, FILE_APPEND | LOCK_EX);  $_SESSION['uas_logging_id'] += 1; // TO DO: Do not forget to remove this line !
        
        $request = file_get_contents("php://input"); 
        // echo(($request));
        $RequestObject = new RequestObject();
        $RequestObject->Pack($request);
    
    
        if($RequestObject->ResourceName == 'Skills')
        {
            $response = $this->MakeAPIRequestType2($RequestObject);
        }
        else
        {
            $response = $this->MakeAPIRequestType1($RequestObject);
        }
        $original_response = json_decode($response); 
        if($original_response == null)
        {
            echo($response);
        }
        // The response from underlaying API
        $original_response->request = json_decode($request);
        $original_response->after_mapping_timestamp = new DateTime();
        echo json_encode($original_response, JSON_PRETTY_PRINT);
        exit();
    }
}

if(isset($_GET['PrintRequestObject'])) {
    $APIManager = new APIManager();
    $APIManager->InitializeRequest();
}

?>
