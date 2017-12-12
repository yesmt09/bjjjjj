<?php
header('Content-Type:text/html;charset=utf-8');
require_once 'Enter.php';
require_once 'helper.php';
require_once './vendor/autoload.php';


class Bjjj
{

    private $token = null;
    private $sign = null;
    private $timestamp = null;
    private $userId = null;
    public $message = [];
    private $setp = 1;
    private $car = 'init';
    public $enter = null;
    private $user = [];

    public function __construct()
    {
        $this->user = require_once('./config/user.php');
        $this->getParams();
        $this->userId = $this->user['userid'];
        if ($this->token == false || $this->sign == false || $this->timestamp == false) {
            throw new Exception('params err');
        }
        $this->enter = new Enter($this->userId, $this->user['licenseno']);
    }

    public function setCar($car) {
        $this->car = $car;
    }

    public function getCardList () {
        $info = $this->getCardInfo();
        if($info !== false) {
            $this->pushMessage('使用缓存车辆信息');
            return $info;
        } else {
            $result = $this->enter->entercarlist($this->timestamp, $this->token, $this->sign, $this->userId);
            if($result[0] == 200) {
                $enterCardList = json_decode($result[1],true);
                if($enterCardList['rescode'] == 200) {
                    $this->pushMessage($enterCardList['resdes']);
                    if(!$this->pushCardInfo($enterCardList['datalist'])) {
                        $this->pushMessage('汽车列表塞入缓存文件失败');
                    } else {
                        $this->pushMessage('汽车列表塞入缓存文件成功');
                    }
                    return $enterCardList['datalist'];
                } else {

                }

                $this->pushMessage($enterCardList['resdes']);
            } else {
                throw new Exception('获取车列表失败');
            }
            return [];
        }
    }

    public function submitPaper (){
        $this->enter->submitPaper( );
    }

    public function checkCardTime(array $cardInfo) {
        if(empty($cardInfo)){
            return true;
        } else {
            if ((time() - strtotime($cardInfo['enterbjend']))>1) {
                return true;
            }
            return false;
        }
    }

    public function getParams()
    {
        $fp = file('./config/getParams.json');
        $params = json_decode($fp[count($fp)-1], true);
        $this->token = $params['token'];
        $this->sign = $params['sign'];
        $this->timestamp = $params['timestamp'];
    }

    public function pushMessage ($txt) {
        $this->message[$this->car] .= PHP_EOL . $this->setp . '. ' . $txt;
        $this->setp++;
    }

    public function pushCardInfo($info) {
        $data = [
            date('Y-m-d')=>$info
        ];
        return file_put_contents('./config/'.$this->userId . '.json', json_encode($data));
    }

    public function getCardInfo() {
        $name = './config/'.$this->userId . '.json';
        if(file_exists($name) && !empty(file_get_contents($name))) {
            $data = json_decode(file_get_contents( $name ),true);
            if (!isset($data[date('Y-m-d')])) {
                return false;
            } else {
                return $data[date('Y-m-d')];
            }
        } else {
            return false;
        }
    }
}

try {
    $bjjj = new Bjjj();
    $cardInfo = $bjjj->getCardList();
    foreach ( $cardInfo as $k=>$v) {
        $bjjj->setCar($v['licenseno']);
        $message = ' 需要申请进京证';
        foreach ($v['carapplyarr'] as $car => $info) {
            $status = $info['status'];
            $carid = $info['carid'];
            $applyid = $info['applyid'];
            $licenseno = $info['licenseno'];
            if ($status != 1 || !$bjjj->checkCardTime($info)) {
                $message = ' 需要申请进京证';
                //$bjjj->submitPaper($applyid, $carid);
            } else {
                $message = ' 无需申请进京证';
                break;
            }
        }
        $bjjj->pushMessage($message);
    }
    foreach ($bjjj->message as $k=>$v) {
        echo $k . $v . PHP_EOL.PHP_EOL  ;
    }
} catch (Exception $e) {
    echo $e->getMessage() . " the end!!!!";
}
