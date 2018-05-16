<?php
header('Content-Type:text/html;charset=utf-8');
require_once './vendor/autoload.php';


$i = 0;
$l = 0;
$tip = false;
while(true) {
    // form提交
    $form = array(
        'userid'=>'4ED87357128F4016B0F402EB479169A0',
    );
    $helper = new \Bjjjj\helper();
    $result = $helper->curl_post(http_build_query($form), '/enterbj/platform/enterbj/curtime_03', '/enterbj/platform/enterbj/toVehicleType');
    if($result[0] != 200) {
        echo $l++;
        $i = 0;
	if($tip) {
		$message = '✖️进京证无无无法办理了!!!  ';
		setTip($message);
		$tip = false;
	}
        echo " 无法办理 " . date('H:i:s').PHP_EOL;
        sleep(mt_rand(300,600));
    } else {
	$tip = true;
	$i++;
	if($i==7){
		$i = 1;
	}
        $message = '☑️可以办理进京证了!!!  ';
	setTip($message);
	echo $i;
        echo " 可以办理进京证了 " . date('H:i:s').PHP_EOL;
        sleep(60*(15*$i));
    }
};

function setTip ($message){
        /*$webHook = 'https://oapi.dingtalk.com/robot/send?access_token=6d64be75402dbee628defc846aa22163449f6caf60a252ff41b71871075b2080';
        request_by_curl($webHook, json_encode([
            'msgtype' => 'text',
            'text' => [
                'content' => $message
            ],
            "at" => [
		"atMobiles" => [
		]
                //"isAtAll" => true
            ]
        ]));
*/
}

function request_by_curl($remote_server, $post_string) {  
    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_URL, $remote_server);
    curl_setopt($ch, CURLOPT_POST, 1); 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=utf-8'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
    // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0); 
    // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $data = curl_exec($ch);
    curl_close($ch);  
               
    return $data;  
}  
 
