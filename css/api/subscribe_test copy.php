<?php
include $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

// JSON 키 파일의 경로
$keyFilePath = $_SERVER['DOCUMENT_ROOT'] . '/com-dmonster-smap-firebase-adminsdk-2zx5p-2610556cf5.json'; // 서비스 계정의 JSON 키 파일 경로를 설정합니다.

define("JsonPath", $keyFilePath);
define("AndroidPublisher", "https://www.googleapis.com/auth/androidpublisher");
define("DEBURG", true);

if (DEBURG) {
    $arrData['product_id'] = "smap_sub";
    $arrData['purchase_token'] = "ngmlihiaeomeeeaebidndkkk.AO-J1OxXvVjz3KTgsTNv8dkYSLdgtTLKEU4dvxMtJrf2_rWC9DP9OWOy8_DocXyW191_1f2gzCHCMCQXSLI0cnhXCqP76nSyeg";
    $arrData['package_name'] = "com.dmonster.smap";
    $arrData['mode'] = 4;
    verifyReceipt($arrData);
    return;
}
$json = json_decode(file_get_contents("php://input"));
$myData = base64_decode($json->message->data);

switch ($myData) {
    case strpos($myData, "testNotification"):
        $fp = fopen("log.txt", "w");
        fwrite($fp, $myData);
        exit();
        break;
    case strpos($myData, "OneTimeProductNotification"):
        parsePurchaseInfo($myData);
        break;
    case strpos($myData, "SubscriptionNotification"):
        parseSubScriptionInfo($myData);
        break;
}

function parsePurchaseInfo($myData) // 일회용 상품일 때
{
    /***
     * {
     * "version": string
     * "notificationType": int
     * "purchaseToken": string
     * "subscriptionId": string
     * }
     */
}

function parseSubScriptionInfo($myData) // 정기결제(구독) 상품일때
{
    /***
     *{
     *  "version":"1.0",
     *  "packageName":"com.some.thing",
     *  "eventTimeMillis":"1503349566168",
     *  "subscriptionNotification":
     *  {
     *      "version":"1.0",
     *      "notificationType":4,
     *      "purchaseToken":"PURCHASE_TOKEN",
     *      "subscriptionId":"my.sku"
     *  }
     *}
     */
}

function verifyReceipt(array $arrData) // $mode 1. 일반상품, 2. 구독결제, 3. 취소정보 얻어옴, 4 수동으로 지움
{
    /** 구글서버에 접속하여 인증작업 진행
     * 정상적인 인증일 때 True 반환, 비정상적일 때 False 반환
     */
    $product_id = "";
    $purchase_token = "";
    $package_name = "";
    $mode = 0;

    if (DEBURG) {
        $product_id = "smap_sub";
        $purchase_token = "ngmlihiaeomeeeaebidndkkk.AO-J1OxXvVjz3KTgsTNv8dkYSLdgtTLKEU4dvxMtJrf2_rWC9DP9OWOy8_DocXyW191_1f2gzCHCMCQXSLI0cnhXCqP76nSyeg";
        $package_name = "com.dmonster.smap";
        $mode = 4;
    } else {
        $product_id = $arrData['product_id'];
        $purchase_token = $arrData['purchase_token'];
        $package_name = $arrData['package_name'];
        $mode = $arrData['mode'];
    }

    $verify = new BillingVerify($product_id, $purchase_token, $package_name);
    $str = null;
    $arr_result = array();
    // printr($verify);
    switch ($mode) {
        case 0:
            error_log("Billing Mode Value 0");
            break;
        case 1: //일반결제 정보 얻어옴
            $arr_result = $verify->getProductInfo();
            $str = json_encode($arr_result);
            break;
        case 2: //구독정보 얻어옴
            $arr_result = $verify->getSubscribeInfo();
            $str = json_encode($arr_result);
            printr($str);
            break;
        case 3: //취소정보 얻어옴(구독결제)
            try {
                $arr_result['res_code'] = 1;
                $arr_result['res_msg'] = 'ok';
                $result = $verify->getCancelInfo();
            } catch (Exception $e) {
                $arr_result['res_code'] = 2;
                $arr_result['res_msg'] = 'error';
            }
            $str = json_encode($arr_result);
            break;
        case 4: //구독정보 얻어옴
            $arr_result = $verify->test();
            $str = json_encode($arr_result);
            printr($str);
            break;      
    }
}

class BillingVerify
{
    public $mProduct_id;
    public $mPurchase_token;
    public $mPackage_name;

    public $mClient;
    public $mService;

    public function __construct($product_id, $purchase_token, $package_name)
    {
        $this->mProduct_id = $product_id;
        $this->mPurchase_token = $purchase_token;
        $this->mPackage_name = $package_name;
        $this->init();
    }


    public function init()
    {
        $this->mClient = new Google_Client();
        $this->mClient->setAuthConfig(JsonPath);
        $this->mClient->addScope(AndroidPublisher);
        $this->mClient->refreshTokenWithAssertion();
        // $token = $this->mClient->getAccessToken();
        // $accessToken = $token['access_token'];
        // $this->mPurchase_token = $accessToken;
        $this->mService = new Google_Service_AndroidPublisher($this->mClient);
    }

    public function getProductInfo()
    {
        $this->paramEmptyCheck();
        $result = $this->mService->purchases_products->get($this->mPackage_name, $this->mProduct_id, $this->mPurchase_token);
        $arr = array();
        $arr['kind']                    = $result->getKind();
        $arr['purchaseTimeMillis']      = $result->getPurchaseTimeMillis();
        $arr['purchaseState']           = $result->getPurchaseState();
        $arr['consumptionState']        = $result->getConsumptionState();
        $arr['developerPayload']        = $result->getDeveloperPayload();
        $arr['orderId']                 = $result->getOrderId();
        $arr['purchaseType']            = $result->getPurchaseType();
        $arr['acknowledgementState']    = $result->getAcknowledgementState();
        return $arr;
    }

    public function getSubscribeInfo()
    {
        $this->paramEmptyCheck();
        $result = $this->mService->purchases_subscriptions->get($this->mPackage_name, $this->mProduct_id, $this->mPurchase_token);
        $arr = array();
        $arr['kind']                        = $result->kind;
        $arr['startTimeMillis']             = $result->startTimeMillis;
        $arr['expiryTimeMillis']            = $result->expiryTimeMillis;
        $arr['autoResumeTimeMillis']        = $result->autoResumeTimeMillis;
        $arr['autoRenewing']                = $result->autoRenewing;
        $arr['priceCurrencyCode']           = $result->priceCurrencyCode;
        $arr['priceAmountMicros']           = $result->priceAmountMicros;
        $arr['introductoryPriceInfo']       = $result->getIntroductoryPriceInfo();
        $arr['countryCode']                 = $result->countryCode;
        $arr['developerPayload']            = $result->developerPayload;
        $arr['paymentState']                = $result->getPaymentState();
        $arr['cancelReason']                = $result->cancelReason;
        $arr['userCancellationTimeMillis']  = $result->userCancellationTimeMillis;
        $arr['cancelSurveyResult']          = $result->getCancelSurveyResult();
        $arr['orderId']                     = $result->orderId;
        $arr['linkedPurchaseToken']         = $result->linkedPurchaseToken;
        $arr['purchaseType']                = $result->purchaseType; // 0. 테스트, 1. 프로모션 코드를 사용하여 구매
        $arr['priceChange']                 = $result->getPriceChange();
        $arr['profileName']                 = $result->profileName;
        $arr['emailAddress']                = $result->emailAddress; // 구글 이메일
        $arr['givenName']                   = $result->givenName;
        $arr['familyName']                  = $result->familyName;
        $arr['profileId']                   = $result->profileId;
        $arr['acknowledgementState']        = $result->acknowledgementState;
        $arr['promotionType']               = $result->promotionType;
        $arr['promotionCode']               = $result->promotionCode;
        return $arr;
    }

    public function getCancelInfo()
    {
        $arr = array();
        $this->paramEmptyCheck();
        $result = $this->mService->purchases_subscriptions->cancel($this->mPackage_name, $this->mProduct_id, $this->mPurchase_token);
        return $result;
    }

    function test()
    {
        $result = $this->mService->purchases_subscriptions->get($this->mPackage_name, $this->mProduct_id, $this->mPurchase_token);
        print_r($result);
    }

    function paramEmptyCheck()
    {
        if (empty($this->mService)) {
            $this->errorResponse("1101", "no paramater", "service not bound");
        } else if (empty($this->mProduct_id)) {
            $this->errorResponse("1101", "no paramater", "empty mProduct_id");
        } else if (empty($this->mPurchase_token)) {
            $this->errorResponse("1101", "no paramater", "empty mPurchase_token");
        } else if (empty($this->mPackage_name)) {
            $this->errorResponse("1101", "no paramater", "empty mPackage_name");
        }
        exit;
    }

    function errorResponse($error_code, $error_message, $detail_message)
    {
        $arr = array();
        $arr['error_code'] = $error_code;
        $arr['error_message'] = $error_message;
        $arr['detail_message'] = $detail_message;
        echo json_encode($arr);
    }
}

?>