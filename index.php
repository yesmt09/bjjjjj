<?php
header('Content-Type:text/html;charset=utf-8');
require_once './vendor/autoload.php';
$users = require_once('./config/user.php');
foreach ( $users as $userid=>$carInfo) {
    try {
        $bjjj = new \Bjjjj\Bjjj($userid);
        $cards = $bjjj->getCardList();
        foreach ( $cards as $car) {
            $need = true;
            if(!empty($car['carapplyarr'])) {
                foreach ($car['carapplyarr'] as $info) {
            		$bjjj->setCarInfo($carInfo[$car['licenseno']], $info);
                    if ($info['status'] != 1 || !$bjjj->checkCardTime($info)) {
                        $inBjjjj = $info['enterbjend'];
                    } else {
                        $need = false;
                        $message = ' 无需申请进京证';
                        break;
                    }
                }
            } else {
                $inBjjjj = date('Y-m-d');
            }
            if($need) {
                $message = '需要申请进京证';
                $bjjj->getCarType();
                $i = 1;
                while (true) {
                    try {
                        $bjjj->checkService();
                        $bjjj->submitPaper($inBjjjj);
                        break;
                    } catch (Exception $e) {
                        echo $e->getMessage() . " 继续检测" .PHP_EOL;
                        sleep(mt_rand(20,100));
                        if($i == 100) {
                            break;
                        }
                    }
                }
            }
            $bjjj->pushMessage($message);
        }
    } catch (Exception $e) {
        $message =  $e->getMessage() . " the end!!!!";
        echo $message;
    }
}
foreach ($bjjj->message as $k=>$v) {
    echo $k . $v . PHP_EOL.PHP_EOL  ;
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
 
