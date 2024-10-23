<?php

use Kedniko\FCM\FCM;

require 'vendor/autoload.php';

define("PROJECT_ID", 'com-dmonster-smap');
define("AUTH_KEY_CONTENT_FILE_NM", 'com-dmonster-smap-firebase-adminsdk-2zx5p-83d04ed4fc.json');

class SendFcm
{
    public function send($body)
    {
        $authKeyContent = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.AUTH_KEY_CONTENT_FILE_NM), true);
        $bearerToken = FCM::getBearerToken($authKeyContent);

        $rtn2 = FCM::send($bearerToken, PROJECT_ID, $body);

        $rtn = date("Y-m-d H:i:s");

        return $rtn;
    }
}

$send_fcm = new SendFcm();
