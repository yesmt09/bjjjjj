<?php
namespace Bjjjj;

class helper
{
    public $header;
    public $hosts;
    public $domain;

    public function __construct()
    {
        $this->hosts = 'https://enterbj.zhongchebaolian.com';
        $this->domain = 'enterbj.zhongchebaolian.com';
        $this->header = [
            'Host: ' . $this->domain,
            'Accept: application/json, text/javascript, */*; q=0.01',
            'X-Requested-With: XMLHttpRequest',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: zh-cn',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Origin: ' . $this->hosts,
            'Connection: keep-alive',
            'User-Agent: Mozilla/5.0 (Linux; Android 4.4.2; vivo Xplay3S Build/KVT49L) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/30.0.0.0 Mobile Safari/537.36',
            'Cookie:JSESSIONID=2DC6D07B3F9894CADE2483B94A8AF543;CNZZDATA1260761932=1576525013-1503353120-https%253A%252F%252Fenterbj.zhongchebaolian.com%252F%7C1512521350; UM_distinctid=15dfeacbf2a32a-0b6188833cb4d98-2c590766-4a640-15dfeacbf2b722'
        ];
    }

    public function curl_get( $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        $result = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return array($status_code, $result);
    }

    public function curl_post( $data, $url, $referer = null, array $opt = array(), $skip = false)
    {
        $ch = curl_init();
        if($skip == true) {
            echo $url;
            echo $data;
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            curl_setopt($ch, CURLOPT_URL, $this->hosts . $url);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        foreach ($opt as $k=>$v) {
            curl_setopt($ch, $k, $v);
        }
        if ($referer != null) {
            curl_setopt($ch, CURLOPT_REFERER, $this->hosts . $referer);
        }
        $result = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array($status_code, $result);
    }
}
