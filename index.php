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

            foreach ($bjjj->message as $k=>$v) {
                echo $k . $v . PHP_EOL.PHP_EOL  ;
            }
        }
    } catch (Exception $e) {
        $message =  $e->getMessage() . " the end!!!!";
        echo $message.PHP_EOL;
    }
}
