<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
header('Content-Type: application/json'); // Only necessary if the request asks for it. The content will be send anyway. POSTMAN use this value to preset the viewer used to show the data

session_start();

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

if(isset($_GET['PrintRequestObject'])) {

    $request = json_decode(file_get_contents("php://input")); 
    // echo(($request));

    $RequestObject = new RequestObject();
    $RequestObject->Pack($request);

    $url = 'http://aju.ro/my/api/index.php';
    $data = array('select' => 'plain', 'list' => 'connections', 'fetch' => 'objects', 'startTime' => '1890.15');
    $data = array(
        'select' => 'plain', 
        'list' => 'records', 
        'database' => 'Northwind (MsSQL)', 
        'connection' => 3, 
        'schema' => 'dbo',  
        'table' => 'Customers', 
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
    
    echo($result);
	// If you want to see the post body
    // echo json_encode($RequestObject, JSON_PRETTY_PRINT);
}

?>