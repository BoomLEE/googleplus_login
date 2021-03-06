<?php
ini_set("display_errors",1);
error_reporting(E_ALL);
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ .'/vendor/google/apiclient/src');

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

const CLIENT_ID = '####################';
const CLIENT_SECRET = '#####################';
const APPLICATION_NAME = "w****c***r";
const TOP_PAGE_URL = "https://new.w****-****r.jp/login/gplus_test/gplus-quickstart-php/index.php";
//https://console.developers.google.com->承認済みのリダイレクト URIと一致させないと権限エラーが発生する。

$client = new Google_Client();
$client->setApplicationName(APPLICATION_NAME);
$client->setClientId(CLIENT_ID);
$client->setClientSecret(CLIENT_SECRET);
$client->setRedirectUri(TOP_PAGE_URL);
$client->setScopes(array(
        'https://www.googleapis.com/auth/plus.me', 
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile',
    ));
    
session_start();
if (isset($_GET['code'])) {
    // https://developers.google.com/api-client-library/php/auth/web-app?hl=ja
    // 認証
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    $token = json_decode($client->getAccessToken());
    header('Location: ' . TOP_PAGE_URL);
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token'] !="") {
    $client->setAccessToken($_SESSION['access_token']);
    $oauth2 = new \Google_Service_Oauth2($client);
    $userInfo = $oauth2->userinfo->get();
    
} else {
    $authUrl = $client->createAuthUrl();
    
}

//ログアウトするときにはaccess_tokenをunsetする
if (isset($_REQUEST['logout'])) {
  unset($_SESSION['access_token']);
  header('Location: ' . TOP_PAGE_URL);
}

if (isset($authUrl)) {
  echo "<a class='login' href='" . $authUrl . "'>Connect Me!</a>";
} else {
  echo <<<END
    login ok
    <a class='logout' href='?logout'>Logout</a>
END;
if (isset($userInfo)) {
    echo $userInfo->id;
    echo $userInfo->familyName;
    echo $userInfo->givenName;
    echo $userInfo->gender;
    echo $userInfo->email;
    echo $userInfo->picture;

}
}
exit;
