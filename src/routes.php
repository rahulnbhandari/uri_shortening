<?php
// Routes

//use Fleshgrinder\Validator;

//use utils\url_shortening;

$app->post('/url', function ()  use ($app) {
	$data = file_get_contents("php://input");	
	$data = json_decode($data,true);
    
  
    try{
         $url = new URL($data['url']);
         $url->validate($data['url']);
         $uuid = UUID_GEN::random_uuid(11);
         $dao = new Dao("reverse_url_mapper");
         $url_record['uuid'] = $uuid;
         $url_record['timstamp'] = date("Y-m-d\TH:i:s\Z");
         $url_record['url'] = $data['url']; 
         $result = $dao->put($url_record);
         if($result) {
            $message['url'] = $_SERVER["SERVER_NAME"]."/".$result['uuid'];
        } else {
            $this->response()->status(400);
            $message['error'] = "Something went wrong";
            $message = json_encode($message);
        }
         
         return $message;
    } catch(InvalidArgumentException $e) {
        $message['error'] = $e->getmessage();
        $message = json_encode($message);
        $this->response()->status(400);
        return $message;

    }
   
   // require("../lib/dao.php");
    //$url = new Dao("reverse_url_mapper");
   // $url->validate($data['url']);
    
});

$app->get('/{request}', function ($request) {
    $path = $request->getUri()->getPath();
    $path = substr($path, 1);
    $dao = new Dao("reverse_url_mapper");
    $result = $dao->get( $path,"uuid");
    if($result['url']) {
         header("Location: ".$result['url']);
         $this->logger->info("Slim-url-shortening '/' route ".$result['url']);
         return
    } else {
         $this->response()->status(400);
    }
    $this->logger->info("Slim-url-shortening '/' route ");
    return;
    
    
});

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-url-shortening '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});



