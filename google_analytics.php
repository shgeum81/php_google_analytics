<?php
define('GOOGLE_ANALYTICS_CODE_CUSTOM','');
function sendGoogleAnalytics($analyticsCode=_GOOGLE_ANALYTICS_CODE,$getParams=array()){
    $requestURL = 'https://www.google-analytics.com/collect';
    $params = array(
        'v' => '1',
        't'=>'pageview',
        'ds'=>'web',
        'de'=>'UTF-8',
        'tid'=>$analyticsCode,    
        'cid'=>(isset($_COOKIE['_ga'])?@end(@explode('.',$_COOKIE["_ga"],'3')):sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',mt_rand(0, 0xffff), mt_rand(0, 0xffff),mt_rand(0, 0xffff),mt_rand(0, 0x0fff)|0x4000,mt_rand(0, 0x3fff)|0x8000,mt_rand(0, 0xffff),mt_rand(0, 0xffff),mt_rand(0, 0xffff))),
        'uid'=>$_COOKIE['_ga_uid'],
        'ip'=>$_SERVER['REMOTE_ADDR'],
        'ua'=>$_SERVER['HTTP_USER_AGENT'],
        'ul'=>@current(explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE'])),
        'dl'=>sprintf('%s%s%s',($_SERVER['SERVER_PORT']=='443'?'https://':'http://'),$_SERVER['HTTP_HOST'],$_SERVER['REQUEST_URI']),
        'dh'=>$_SERVER['HTTP_HOST'],
        'dp'=>$_SERVER['REQUEST_URI'],
        'dr'=>$_SERVER['HTTP_REFERER'],
        'z'=>mt_rand(),
    );
    if($getParams){
        $getParams = @array_filter($getParams,function($value){return $value!== '';});
        $params = @array_merge($params,$getParams);
    }
    print_r($params);
    unset($requestParams);
    foreach((array)$params as $key=>$value){
        if(!$value = trim($value))
            continue;
        if(isset($requestParams))
           $requestParams .= '&'; 
        $requestParams .= sprintf('%s=%s',$key,rawurlencode($value));
    unset($key,$value);
    }
    //cURL 전송
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,sprintf('%s?%s',$requestURL,$requestParams));
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPGET, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_NOBODY, TRUE);
    curl_exec($ch);
    curl_close ($ch);
}

?>
