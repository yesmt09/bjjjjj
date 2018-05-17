<?php
namespace Bjjjj;

use Exception;

class Bjjj
{

    private $sign = null;
    private $timestamp = null;
    private $userId = null;
    public $message = [];
    private $setp = 1;
    private $car = 'init';
    public $enter = null;
    private $applyid = '';
    private $carid = '';
    private $carmodel;
    private $carregtime;
    private $logid;
    private $carInfo;

    public function __construct($userId)
    {
        $this->userId = $userId;
        $this->logid = time();

        $this->pushImage();

        $this->getParams();
        if ($this->sign == false || $this->timestamp == false) {
            throw new Exception('params err');
        }
        $this->enter = new Enter($this->userId);
    }

    public function pushImage() {
        file_put_contents('./config/await/'.$this->userId,json_encode([
            'userid'=>$this->userId,
            'logid' => $this->logid
        ]));
        sleep(2);
    }

    public function getParams()
    {
        $signFileName = './config/sign/'.$this->logid;
        if(!file_exists($signFileName)) {
            return '';
        }
        $fp = file($signFileName);
        $params = json_decode($fp[count($fp)-1], true);
        $this->sign = $params['sign'];
        $this->timestamp = $params['timestamp'];
        unlink($signFileName);
    }

    public function setCarInfo ($carinfo , $car) {
        $this->carInfo = $carinfo;
        $this->enter->setEnterCar($car['licenseno']);
        $this->car = $car['licenseno'];
        $this->applyid = $car['applyid'];
        $this->carid = $car['carid'];
    }
    public function getCardList () {
        $info = $this->getCarInCache();
        if($info !== false) {
            $this->pushMessage('使用缓存车辆信息');
            return $info;
        } else {
            $result = $this->enter->entercarlist($this->timestamp, $this->sign, $this->userId);
            if($result[0] == 200) {
                $enterCardList = json_decode($result[1],true);
                if($enterCardList['rescode'] == 200) {
                    $this->pushMessage($enterCardList['resdes']);
                    if(!$this->pushCache($enterCardList['datalist'])) {
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

    public function getCarType() {
        $result = $this->enter->getCarType($this->applyid,$this->carid);
        if($result[0] == 200) {
            $dom = \Sunra\PhpSimple\HtmlDomParser::str_get_html($result[1]);
            $form = $dom->find('form[id=submitForm]', 0);
            $this->carmodel = $form->find('input[id=carmodel]', 0)->value;
            $this->carregtime = $form->find('input[id=carregtime]', 0)->value;
            $dom->clear();
        } else {
            throw new Exception('获取车辆型号失败');
        }
    }

    public function checkService() {
        $result = $this->enter->checkService();
        if($result[0] != 200) {
            throw new Exception('当前无法办理进京证');
        } else {
            $this->pushMessage('当前可以办理进京证');
        }
    }

    public function submitPaper ($applyTime){
        $result = $this->enter->submitPaper( date('Y-m-d',strtotime($applyTime)+86400),$this->carInfo, $this->carmodel, $this->carregtime);
        if($result[0] == 200) {
            $code = json_decode($result[1],true);
            if ($code['rescode'] == 200) {
                $this->pushMessage('申请成功');
                unlink('./config/cache/' . $this->userId);
                return true;
            } else {
                $this->pushMessage($code['resdes']);
                return false;
            }
        } else {
            return false;
        }
    }

    public function checkCardTime(array $cardInfo) {
        if(empty($cardInfo)){
            return true;
        } else {
            if ((time() - strtotime($cardInfo['enterbjend']))<=0) {
                return true;
            }
            return false;
        }
    }

    public function pushMessage ($txt) {
        $this->message[$this->car] = isset($this->message[$this->car])?$this->message[$this->car]:'';
        $this->message[$this->car] .= PHP_EOL . $this->setp . '. ' . $txt;
        $this->setp++;
    }

    public function pushCache($info) {
        $data = [
            date('Y-m-d')=>$info
        ];
        return file_put_contents('./config/cache/'.$this->userId, json_encode($data));
    }

    public function getCarInCache() {
        $name = './config/cache/'.$this->userId;
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