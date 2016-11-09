<?php
define('GOOGLE_ANALYTICS_CODE_CUSTOM','');

class UUID{ //출처: http://php.net/manual/en/function.uniqid.php (작성자: Andrew Moore)
  public static function RFC4122_v3($namespace, $name) {
    if(!self::is_valid($namespace)) return false;
    // Get hexadecimal components of namespace
    $nhex = str_replace(array('-','{','}'), '', $namespace);
    // Binary Value
    $nstr = '';
    // Convert Namespace UUID to bits
    for($i = 0; $i < strlen($nhex); $i+=2) {
      $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
    }
    // Calculate hash value
    $hash = md5($nstr . $name);
    return sprintf('%08s-%04s-%04x-%04x-%12s',
      // 32 bits for "time_low"
      substr($hash, 0, 8),
      // 16 bits for "time_mid"
      substr($hash, 8, 4),
      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 3
      (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,
      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
      // 48 bits for "node"
      substr($hash, 20, 12)
    );
  }
  public static function RFC4122_v4() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      // 32 bits for "time_low"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),
      // 16 bits for "time_mid"
      mt_rand(0, 0xffff),
      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand(0, 0x0fff) | 0x4000,
      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand(0, 0x3fff) | 0x8000,
      // 48 bits for "node"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
  }
  public static function RFC4122_v5($namespace, $name) {
    if(!self::is_valid($namespace)) return false;
    // Get hexadecimal components of namespace
    $nhex = str_replace(array('-','{','}'), '', $namespace);
    // Binary Value
    $nstr = '';
    // Convert Namespace UUID to bits
    for($i = 0; $i < strlen($nhex); $i+=2) {
      $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
    }
    // Calculate hash value
    $hash = sha1($nstr . $name);
    return sprintf('%08s-%04s-%04x-%04x-%12s',
      // 32 bits for "time_low"
      substr($hash, 0, 8),
      // 16 bits for "time_mid"
      substr($hash, 8, 4),
      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 5
      (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
      // 48 bits for "node"
      substr($hash, 20, 12)
    );
  }
  public static function is_valid($uuid) {
    return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
                      '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
  }
}
function sendGoogleAnalytics($postData='',$pageTitle='',$uid='',$analyticsCode=_GOOGLE_ANALYTICS_CODE_CUSTOM){
    $requestURL = 'https://www.google-analytics.com/collect';
    $params = array();
    $params[] .= sprintf('v=1');
    $params[] .= sprintf('tid=%s',$analyticsCode);                                                                 //구글 UID
    $params[] .= sprintf('cid=%s',(isset($_COOKIE['_ga'])?explode('.',$_COOKIE["_ga"],'3')[2]:UUID::RFC4122_v4()));
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
