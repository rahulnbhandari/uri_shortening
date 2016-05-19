<?php
// Routes

//use Fleshgrinder\Validator;

//use utils\url_shortening;

$app->post('/url', function ($request,$response,$args)  use ($app) {
    $body = $request->getBody();
    if($body) {
        $data = $body->getContents();
    }
	$data = json_decode($data,true);
    $uri = $request->getUri();
    $host = $uri->getHost();
    $prot = $uri->getScheme();
    
  
    try {
         $url = new URL($data['url']);
         $url->validate($data['url']);
       
         $clientip = CommonUtil::GetClientIpAddress();
      
        
         
         $url_record['url'] = $data['url'];
         $url_record['clientip'] = $clientip;


         $result = CommonUtil::addUrlMap($url_record);
         if($result &&  $result[0]['uuid']) {
            //print_r($result);

            $message['url'] = $prot.'://'.$host."/".$result[0]['uuid'];
            $response = $response->withJson($message, 201);
        } else {
            $message['error'] = "Something went wrong";
            $response = $response->withJson($message, 400);
        }
       
    } catch(InvalidArgumentException $e) {
        $message['error'] = $e->getmessage();
        $response = $response->withJson($message, 400);
    } 

    return $response;
   
   // require("../lib/dao.php");
    //$url = new Dao("reverse_url_mapper");
   // $url->validate($data['url']);
    
});

$app->get('/{request}', function ($request,$response) {
    $path = $request->getUri()->getPath();
    $path = substr($path, 1);
    $dao = new Dao(Entities::URL_TABLE);

    $result = $dao->get( $path,"uuid");

    if($result['url']) {
         //print_r($result);
         $response = $response->withHeader('cache-control', 'max-age=25900');
         $clientip = CommonUtil::GetClientIpAddress();
         $this->logger->info("Slim-url-shortening '/' route $path ".$result['url']." clientip $clientip");
         return $response->withStatus(302)->withHeader('Location', $result['url']);
         
    } else {
         $message = "<h1>Not Found</h1>
<p>The requested URL /$path was not found on this server.</p>";
         $body = $response->getBody();
         $body->write($message);
         $response = $response->withStatus(404);
         $response = $response->withBody($body);
    }
    $this->logger->info("Slim-url-shortening '/' route ");
    return $response;
    
    
});

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-url-shortening '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});



