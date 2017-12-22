<?php
namespace Bjjjj;

class Enter
{
    private $appkey = '';
    private $deviceid = '';
    private $platform = '';
    private $appsource = '';
    private $imei = '';
    private $imsi = '';
    private $gpslat = '';
    private $gpslon = '';
    private $cartypecode = '';
    private $vehicletype = '';
    // 获取车辆进京证状态
    private $page_entercarlist = '/enterbj/platform/enterbj/entercarlist';
    private $page_index = '/enterbj/jsp/enterbj/index.html';
    private $page_addcartype = '/enterbj/platform/enterbj/addcartype';
    private $page_applyBjMessage = '/enterbj/platform/enterbj/applyBjMessage';
    private $page_loadotherdrivers = '/enterbj-img/platform/enterbj/loadotherdrivers';
    private $page_submitpaper = '/enterbj-img/platform/enterbj/submitpaper_03';
    private $page_curtime = '/enterbj/platform/enterbj/curtime_03';
    private $page_toVehicleType = '/enterbj/platform/enterbj/toVehicleType';

    private $userid;
    private $licenseno;
    private $token;
    private $envGrade = 3;

    public function __construct($userid)
    {
        $this->userid = $userid;
        $this->token = $this->getToken();
    }

    public function setEnterCar ($car)
    {
        $this->licenseno = $car;
    }

    public function getToken()
    {
        $timestamp = strval(time()-1) . '000';
        $parajson = [
            'userid' => $this->userid,
            'appkey' => $this->appkey,
            'deviceid' => $this->deviceid,
            'timestamp' => $timestamp
        ];

        ksort($parajson);
        $tmp = '';
        foreach ($parajson as $k=>$v) {
            $tmp .=$k .$v;
        }
        $tmp = $tmp . $timestamp;
        return md5($tmp);
    }

    public function entercarlist($timestamp, $sign)
    {
        $form = array(
            'userid'=>$this->userid,
            'appkey'=> $this->appkey,
            'deviceid'=> $this->deviceid,
            'timestamp'=>$timestamp,
            'token'=>$this->token,
            'sign'=>$sign,
            'platform'=>$this->platform,
            'appsource'=> $this->appsource,
            'vehicletype' => 03,
        );
        $helper = new helper();
        return $helper->curl_post(http_build_query($form), $this->page_entercarlist, $this->page_index);
    }

    public function getCarType($applyId,$carId) {
        // form提交
        $form = array(
            'applyid'=>$applyId,
            'carid'=>$carId,
            'userid'=>$this->userid,
            'gpslon'=> $this->gpslon,
            'gpslat'=>$this->gpslat,
            'imei'=>$this->imei,
            'imsi'=>$this->imsi,
            'licenseno'=>$this->licenseno,
            'appsource'=>$this->appsource,
            'hiddentime'=>'time'
        );
        $helper = new helper();
        return $helper->curl_post(http_build_query($form), $this->page_addcartype, $this->page_index);
    }

    public function checkService() {
        // form提交
        $form = array(
            'userid'=>$this->userid,
        );
        $helper = new helper();
        return $helper->curl_post(http_build_query($form), $this->page_curtime, $this->page_toVehicleType);
    }

    public function applyBjMessage($applyid, $carid) {
        // form提交
        $form = array(
            'appsource'=>$this->appsource,
            'hiddentime'=>'',
            'applyid'=>$applyid,
            'userid'=>$this->userid,
            'applystatus'=>'',
            'carid'=>$carid,
            'gpslon'=> $this->gpslon,
            'gpslat'=> $this->gpslat,
            'imei'=>$this->imei,
            'imsi'=> $this->imsi,
            'licenseno'=>$this->licenseno,
            'envGrade'=>$this->envGrade
        );
        $helper = new helper();
        return $helper->curl_post(http_build_query($form),$this->page_applyBjMessage, $this->page_index);
    }

    public function submitPaper($inBjTime, $cardInfo, $carmodel, $carregtime) {
        $engineno = $cardInfo['engineno'];
        $carid = $cardInfo['carid'];
        $envGrade = $this->envGrade;
        $drivername = $cardInfo['drivername'];
        $driverlicenseno = $cardInfo['driverlicenseno'];

        //获取图片
        $drivingphoto = $cardInfo['xing'];
        $carphoto = $cardInfo['che'];
        $driverphoto = $cardInfo['jia'];
        $personphoto = $cardInfo['shen'];
        //进京的区
        $inbjentrancecode1 = '';
        //进京的高速
        $inbjentrancecode = '';
        $inbjduration = '7';

        $logid = time();
        $imageId = $inbjentrancecode . $inbjduration . $inBjTime . $this->userid . $engineno. $this->cartypecode. $driverlicenseno. $carid;
        file_put_contents('./config/await/'. $this->userid, json_encode([
            'userid' => $imageId,
            'logid' => $logid
        ]));

        sleep(2);
        $filename = './config/sign/'. $logid;
        if (!file_exists($filename)) {
            throw new \Exception('签名文件未找到');
        }

        //unlink($filename);

        $result = json_decode(file_get_contents($filename),true);

        $form = array(
            'appsource'=>$this->appsource,
            'hiddentime'=>date('Y-m-d H:m:s'),
            'inbjentrancecode1'=>$inbjentrancecode1,
            'inbjentrancecode'=>$inbjentrancecode,
            'inbjduration'=>$inbjduration,
            'inbjtime'=>$inBjTime,
            'appkey'=>'',
            'deviceid'=>'',
            'token'=>'',
            'timestamp'=> $result['timestamp'],
            'userid'=>$this->userid,
            'licenseno'=>$this->licenseno,
            'engineno'=>$engineno,                  //发送机号
            'cartypecode'=>$this->cartypecode,
            'vehicletype'=>$this->vehicletype,
            'drivername'=>$drivername,              //驾驶员姓名
            'driverlicenseno'=>$driverlicenseno,    //驾驶人身份
            'drivingphoto'=>'',             //行驶证正面
            'carphoto'=>'',                  //车辆照片
            'driverphoto'=>'',             //驾驶证照片
            'personphoto'=>'',           //用户照片
            'gpslon'=> $this->gpslon,
            'gpslat'=> $this->gpslat,
            'phoneno'=> '',
            'imei'=> $this->imei,
            'imsi'=> $this->imsi,
            'carid' => $carid,                     //车辆信息
            'carmodel' => $carmodel,                //车类型
            'carregtime'=>$carregtime,              //注册时间
            'envGrade'=>$envGrade,
            'imageId'=> $imageId . $result['timestamp'],
            'code'=> '',
            'sign' => $result['sign'],
            'platform' => $this->platform,
        );

        $header = [
            CURLOPT_POSTFIELDS, [
                'drivingphoto'=> new \CURLFile(realpath($drivingphoto)),             //行驶证正面
                'carphoto'=>new \CURLFile(realpath($carphoto)),          //车辆照片
                'driverphoto'=>new \CURLFile(realpath($driverphoto)),             //驾驶证照片
                'personphoto'=>new \CURLFile(realpath($personphoto)),           //用户照片
            ]
        ];

        $helper = new helper();
        return $helper->curl_post(http_build_query($form), $this->page_submitpaper, $this->page_loadotherdrivers, $header);
    }
}