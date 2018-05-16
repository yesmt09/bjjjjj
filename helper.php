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
            'Accept: */*',
            'X-Requested-With: XMLHttpRequest',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: zh-CN,en-US;q=0.8',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Origin: ' . $this->hosts,
            'Connection: keep-alive',
            'User-Agent: Mozilla/5.0 (Linux; Android 4.4.2; vivo Xplay3S Build/KVT49L) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/30.0.0.0 Mobile Safari/537.36',
            'Cookie: JSESSIONID=D5A1D688EA23F70E86DA1E94875C65CC; UM_distinctid=16057e6bec981-09139ef21-3064d5f-38400-16057e6becb1d; CNZZDATA1260761932=1446634612-1513302424-%7C1526445942'
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
