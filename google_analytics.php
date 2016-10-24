<?php
define('GOOGLE_ANALYTICS_CODE_CUSTOM','');
function sendGoogleAnalytics($postData='',$pageTitle='',$uid='',$analyticsCode=_GOOGLE_ANALYTICS_CODE_CUSTOM){
    $requestURL = 'https://www.google-analytics.com/collect';
    $params = array();
    $params[] .= sprintf('v=1');
    $params[] .= sprintf('tid=%s',$analyticsCode);                                                                 //구글 UID
    if($_COOKIE['PHPSESSID'])
        $params[] .= sprintf('cid=%s',rawurlencode($_COOKIE['PHPSESSID']));                                        //클라이언트 id(php_self 값으로 대체)
    else
        $params[] .= sprintf('cid=%s',str_shuffle('abcdefghijklmlopqrstuvwxyz123456789-'));                        //클라이언트 id(php_self 값으로 대체)
    if($uid)
        $params[] .= sprintf('uid=%s',rawurlencode($uid));                                                         //사용자id
    $params[] .= sprintf('t=pageview');
    if($_SERVER['REMOTE_ADDR'])
        $params[] .= sprintf('uip=%s',$_SERVER['REMOTE_ADDR']);                                                    //사용자ip
    if($_SERVER['HTTP_USER_AGENT'])
        $params[] .= sprintf('ua=%s',rawurlencode($_SERVER['HTTP_USER_AGENT']));                                   //사용자 agent
    if($_SERVER['HTTP_ACCEPT_LANGUAGE'])
        $params[] .= sprintf('ul=%s',rawurlencode(@current(explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']))));       //사용자 언어
    $params[] .= sprintf('ds=web');                                                                                //컨텐츠 소스
    if($_SERVER['HTTP_HOST'])
        $params[] .= sprintf('dh=%s',rawurlencode($_SERVER['HTTP_HOST']));                                         //서버 호스트
    if($postData)
        $params[] .= sprintf('dp=%s',rawurlencode($postData));                                                     //타켓페이지
    else
        $params[] .= sprintf('dp=%s',rawurlencode($_SERVER['REQUEST_URI']));
    if($_SERVER['REQUEST_URI'])
        $params[] .= sprintf('dl=%s',rawurlencode(sprintf('%s%s%s',($_SERVER['SERVER_PORT']=='443'?'https://':'http://'),$_SERVER['HTTP_HOST'],$_SERVER['REQUEST_URI'])));
    if($pageTitle)
        $params[] .= sprintf('dt=%s',rawurlencode($pageTitle));                                                    //페이지제목
    if($_SERVER['HTTP_REFERER'])
        $params[] .= sprintf('dr=%s',rawurlencode($_SERVER['HTTP_REFERER']));                                      //레퍼럴
    $params = implode('&',$params);
    //cURL 전송
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,sprintf('%s?%s',$requestURL,$params));
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
