<?php
class Enter
{
    public $appkey = 'kkk';
    public $deviceid = 'ddd';
    public $platform = 02;
    public $appsource = 'bjjj';
    public $imei = '205bb2ce-ea10-3503-bbfa-f7a66aebdcce';
    public $imsi = '460021200248431';
    public $gpslat = '39.982963';
    public $gpslon = '116.357566';
    // 获取车辆进京证状态
    public $page_entercarlist = '/enterbj/platform/enterbj/entercarlist';
    public $page_index = '/enterbj/jsp/enterbj/index.html';
    public $page_addcartype = '/enterbj/platform/enterbj/addcartype';
    public $page_applyBjMessage = '/enterbj/platform/enterbj/applyBjMessage';
    public $page_loadotherdrivers = '/enterbj-img/platform/enterbj/loadotherdrivers';
    public $page_submitpaper = '/enterbj-img/platform/enterbj/submitpaper_03';
    public $page_curtime = '/enterbj/platform/enterbj/curtime_03';
    public $page_toVehicleType = '/enterbj/platform/enterbj/toVehicleType';

    public $userid;
    public $licenseno;

    public function __construct($userid, $licenseno)
    {
        $this->userid = $userid;
        $this->licenseno = $licenseno;
    }

    public function entercarlist($timestamp, $token, $sign)
    {
        $form = array(
            'userid'=>$this->userid,
            'appkey'=> $this->appkey,
            'deviceid'=> $this->deviceid,
            'timestamp'=>$timestamp,
            'token'=>$token,
            'sign'=>$sign,
            'platform'=>$this->platform,
            'appsource'=> $this->appsource,
            'vehicletype' => 03,
        );
        $helper = new helper();
        return $helper->curl_post(http_build_query($form), $this->page_entercarlist, $this->page_index);
    }

    public function cardType($applyId,$carId) {
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

    public function applyBjMessage($applyid, $carid, $envGrade) {
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
            'envGrade'=>$envGrade
        );
        $helper = new helper();
        return $helper->curl_post(http_build_query($form),$this->page_applyBjMessage, $this->page_index);
    }

    public function submitPaper($inBjTime, $sign) {
        $engineno = $cardInfo['engineno'];
        $cartypecode = $cardInfo['cartypecode'];
        $vehicletype = $cardInfo['vehicletype'];
        $carid = $cardInfo['carid'];
        $carmodel = $cardInfo['carmodel'];
        $carregtime = $cardInfo['carregtime'];
        $envGrade = $cardInfo['envGrade'];


        //获取图片
        $person_info = require_once './config/photo.php';
        $drivingphoto = $person_info['drivingphoto'];
        $carphoto = $person_info['carphoto'];
        $drivername = $person_info['drivername'];
        $driverlicenseno = $person_info['driverlicenseno'];
        $driverphoto = $person_info['driverphoto'];
        $personphoto = $person_info['personphoto'];
        //进京的区
        $inbjentrancecode1 = '09';
        //进京的高速
        $inbjentrancecode = '03';
        $inbjduration = '7';

        $imageId = $inbjentrancecode.$inbjduration.$inBjTime.$this->userid.$engineno.$cartypecode.$driverlicenseno.$carid.$hiddentime;

        $form = array(
            'appsource'=>$this->appsource,
            'hiddentime'=>'',
            'inbjentrancecode1'=>$inbjentrancecode1,
            'inbjentrancecode'=>$inbjentrancecode,
            'inbjduration'=>$inbjduration,
            'inbjtime'=>$inBjTime,
            'appkey'=>'',
            'deviceid'=>'',
            'token'=>'',
            'timestamp'=>$hiddentime,
            'userid'=>$this->userid,
            'licenseno'=>$this->licenseno,
            'engineno'=>$engineno,
            'cartypecode'=>02,
            'vehicletype'=>03,
            'drivingphoto'=>$drivingphoto,
            'carphoto'=>$carphoto,
            'drivername'=>$drivername,  //驾驶员名字
            'driverlicenseno'=>$driverlicenseno,    //驾驶人身份着
            'driverphoto'=>$driverphoto,
            'personphoto'=>$personphoto,
            'gpslon'=> $this->gpslon,
            'gpslat'=> $this->gpslat,
            'phoneno'=> $phoneno,
            'imei'=> $this->imei,
            'imsi'=> $this->imsi,
            'carid' => $carid,
            'carmodel' => $carmodel,
            'carregtime'=>$carregtime,
            'envGrade'=>$envGrade,
            'imageId'=>$imageId,
            'code'=> '',
            'sign' => $sign,
            'platform' => $this->platform,
        );
        $helper = new helper();
        return $helper->curl_post(http_build_query($form), $this->page_submitpaper, $this->page_loadotherdrivers);
    }
}