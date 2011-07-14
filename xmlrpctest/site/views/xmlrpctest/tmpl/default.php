<?php

/**
 * @author Antonio Duran
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package xmlrpctest
 */


//$params = array ( 'param1' => 'This is param1', 'param2' => 'This is param2');
$params = array ( 'This is param1', 'This is param2');

$request_method = 'example.test';
$request_params = xmlrpc_encode( $params );


$postdata = http_build_query(
    array(
        'params' => $request_params,
    )
);

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);

$context  = stream_context_create($opts);


//$response = file_get_contents("http://localhost/j16/index.php?option=com_xmlrpctest&task=$request_method&format=xmlrpc", false, $context);
$response = file_get_contents(JURI::base()."index.php?option=com_xmlrpctest&task=$request_method&format=xmlrpc", false, $context);
$params = xmlrpc_decode($response);

echo "Method name: $request_method";
echo "<P>Params:";
print_r ($params);

?>
