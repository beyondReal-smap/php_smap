<?php
//세션값이 있는지 확인
if ($_SUB_HEAD_TITLE) {
    $_APP_TITLE = APP_TITLE . ' - ' . $_SUB_HEAD_TITLE;
} else {
    $_APP_TITLE = APP_TITLE;
}

if ($_SUB_HEAD_IMAGE) {
    $_OG_IMAGE = $_SUB_HEAD_IMAGE;
} else {
    $_OG_IMAGE = OG_IMAGE . '?v=' . $v_txt;
}

// www 있으면 www 제거하기
$base_URL = "";
if (!preg_match('/www/', $_SERVER['SERVER_NAME']) == true) {
    // www 없을때
} else {
    // www 있을때
    $base_URL = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
    $base_URL .= ($_SERVER['SERVER_PORT'] != '80') ? $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] : str_replace("www.", "", $_SERVER['HTTP_HOST']) . $_SERVER['REQUEST_URI'];

    header('Location: ' . $base_URL);
}


// 우리 앱에 접근했으면 로그인 일시를 업데이트해준다.
// member_t의 mt_adate를 현재시각으로 업데이트
$arr_query = array(
    'mt_adate' => date('Y-m-d H:i:s'),
);
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->update('member_t', $arr_query);

$userLangHead = getUserLang() ? getUserLang() : substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
$trans = [
    'ko' => [
        'key_required' => '필수 항목입니다.',
        'key_remote' => '항목을 수정하세요.',
        'key_email' => '유효하지 않은 E-Mail주소입니다.',
        'key_url' => '유효하지 않은 URL입니다.',
        'key_date' => '올바른 날짜를 입력하세요.',
        'key_dateISO' => '올바른 날짜(ISO)를 입력하세요.',
        'key_number' => '유효한 숫자가 아닙니다.',
        'key_digits' => '숫자만 입력 가능합니다.',
        'key_creditcard' => '신용카드 번호가 바르지 않습니다.',
        'key_equalTo' => '같은 값을 다시 입력하세요.',
        'key_extension' => '올바른 확장자가 아닙니다.',
        'key_maxlength' => '{0}자를 넘을 수 없습니다.',
        'key_minlength' => '{0}자 이상 입력하세요.',
        'key_rangelength' => '문자 길이가 {0} 에서 {1} 사이의 값을 입력하세요.',
        'key_range' => '{0} 에서 {1} 사이의 값을 입력하세요.',
        'key_max' => '{0} 이하의 값을 입력하세요.',
        'key_min' => '{0} 이상의 값을 입력하세요.',
        'key_confirm' => '확인',
        'key_cancel' => '취소',
        'key_attention' => '주의',
        'key_move_main' => '메인으로 이동하시겠습니까? 추가정보는 설정에서 입력가능합니다.',
        'key_add_info' => '추가정보는 설정에서 입력가능합니다.',
        'key_set_info' => '설정',
    ],
    'vi' => [
        'key_required' => 'Trường bắt buộc.',
        'key_remote' => 'Vui lòng sửa trường này.',
        'key_email' => 'Vui lòng nhập địa chỉ email hợp lệ.',
        'key_url' => 'Vui lòng nhập URL hợp lệ.',
        'key_date' => 'Vui lòng nhập ngày hợp lệ.',
        'key_dateISO' => 'Vui lòng nhập ngày hợp lệ (ISO).',
        'key_number' => 'Vui lòng nhập số hợp lệ.',
        'key_digits' => 'Vui lòng chỉ nhập số.',
        'key_creditcard' => 'Vui lòng nhập số thẻ tín dụng hợp lệ.',
        'key_equalTo' => 'Vui lòng nhập lại giá trị giống nhau.',
        'key_extension' => 'Vui lòng nhập giá trị có phần mở rộng hợp lệ.',
        'key_maxlength' => 'Vui lòng nhập không quá {0} ký tự.',
        'key_minlength' => 'Vui lòng nhập ít nhất {0} ký tự.',
        'key_rangelength' => 'Vui lòng nhập giá trị có độ dài từ {0} đến {1} ký tự.',
        'key_range' => 'Vui lòng nhập giá trị từ {0} đến {1}.',
        'key_max' => 'Vui lòng nhập giá trị nhỏ hơn hoặc bằng {0}.',
        'key_min' => 'Vui lòng nhập giá trị lớn hơn hoặc bằng {0}.',
        'key_confirm' => 'Xác nhận',
        'key_cancel' => 'Hủy',
        'key_attention' => 'Chú ý',
        'key_move_main' => 'Bạn có muốn chuyển về trang chủ không? Thông tin bổ sung có thể được nhập trong cài đặt.',
        'key_add_info' => 'Thông tin bổ sung có thể được nhập trong cài đặt.',
        'key_set_info' => 'Cài đặt',
    ],
    'th' => [
        'key_required' => 'จำเป็นต้องกรอกข้อมูล',
        'key_remote' => 'โปรดแก้ไขรายการนี้',
        'key_email' => 'ที่อยู่อีเมลไม่ถูกต้อง',
        'key_url' => 'URL ไม่ถูกต้อง',
        'key_date' => 'โปรดป้อนวันที่ที่ถูกต้อง',
        'key_dateISO' => 'โปรดป้อนวันที่ที่ถูกต้อง (ISO)',
        'key_number' => 'ไม่ใช่ตัวเลขที่ถูกต้อง',
        'key_digits' => 'โปรดป้อนเฉพาะตัวเลข',
        'key_creditcard' => 'หมายเลขบัตรเครดิตไม่ถูกต้อง',
        'key_equalTo' => 'โปรดป้อนค่าเดียวกันอีกครั้ง',
        'key_extension' => 'นามสกุลไฟล์ไม่ถูกต้อง',
        'key_maxlength' => 'ไม่สามารถเกิน {0} ตัวอักษร',
        'key_minlength' => 'โปรดป้อนอย่างน้อย {0} ตัวอักษร',
        'key_rangelength' => 'โปรดป้อนค่าที่มีความยาวระหว่าง {0} ถึง {1} ตัวอักษร',
        'key_range' => 'โปรดป้อนค่าระหว่าง {0} ถึง {1}',
        'key_max' => 'โปรดป้อนค่าน้อยกว่าหรือเท่ากับ {0}',
        'key_min' => 'โปรดป้อนค่ามากกว่าหรือเท่ากับ {0}',
        'key_confirm' => 'ยืนยัน',
        'key_cancel' => 'ยกเลิก',
        'key_attention' => 'สำคัญ',
        'key_move_main' => 'คุณต้องการย้ายไปยังหน้าหลักหรือไม่? ข้อมูลเพิ่มเติมสามารถป้อนได้ในการตั้งค่า',
        'key_add_info' => 'ข้อมูลเพิ่มเติมสามารถป้อนได้ในการตั้งค่า',
        'key_set_info' => 'ตั้งค่า',
    ],
    'ja' => [
        'key_required' => '必須項目です。',
        'key_remote' => '項目を修正してください。',
        'key_email' => '有効なEメールアドレスではありません。',
        'key_url' => '有効なURLではありません。',
        'key_date' => '正しい日付を入力してください。',
        'key_dateISO' => '正しい日付(ISO)を入力してください。',
        'key_number' => '有効な数字ではありません。',
        'key_digits' => '数字のみ入力可能です。',
        'key_creditcard' => 'クレジットカード番号が正しくありません。',
        'key_equalTo' => '同じ値をもう一度入力してください。',
        'key_extension' => '有効な拡張子ではありません。',
        'key_maxlength' => '{0}文字を超えることはできません。',
        'key_minlength' => '{0}文字以上入力してください。',
        'key_rangelength' => '文字の長さが{0}から{1}の間である値を入力してください。',
        'key_range' => '{0}から{1}の間の値を入力してください。',
        'key_max' => '{0}以下の値を入力してください。',
        'key_min' => '{0}以上の値を入力してください。',
        'key_confirm' => '確認',
        'key_cancel' => 'キャンセル',
        'key_attention' => '注意',
        'key_move_main' => 'メインに移動しますか？ 追加情報は設定で入力できます。',
        'key_add_info' => '追加情報は設定で入力できます。',
        'key_set_info' => '設定',
    ],
    'id' => [
        'key_required' => 'Diperlukan.',
        'key_remote' => 'Silakan perbaiki bidang ini.',
        'key_email' => 'Silakan masukkan alamat email yang valid.',
        'key_url' => 'Silakan masukkan URL yang valid.',
        'key_date' => 'Silakan masukkan tanggal yang valid.',
        'key_dateISO' => 'Silakan masukkan tanggal yang valid (ISO).',
        'key_number' => 'Silakan masukkan angka yang valid.',
        'key_digits' => 'Silakan masukkan hanya angka.',
        'key_creditcard' => 'Silakan masukkan nomor kartu kredit yang valid.',
        'key_equalTo' => 'Silakan masukkan nilai yang sama lagi.',
        'key_extension' => 'Silakan masukkan nilai dengan ekstensi yang valid.',
        'key_maxlength' => 'Silakan masukkan tidak lebih dari {0} karakter.',
        'key_minlength' => 'Silakan masukkan setidaknya {0} karakter.',
        'key_rangelength' => 'Silakan masukkan nilai antara {0} dan {1} karakter panjang.',
        'key_range' => 'Silakan masukkan nilai antara {0} dan {1}.',
        'key_max' => 'Silakan masukkan nilai kurang dari atau sama dengan {0}.',
        'key_min' => 'Silakan masukkan nilai lebih dari atau sama dengan {0}.',
        'key_confirm' => 'Konfirmasi',
        'key_cancel' => 'Batal',
        'key_attention' => 'Perhatian',
        'key_move_main' => 'Apakah Anda ingin pindah ke halaman utama? Informasi tambahan dapat diinput di pengaturan.',
        'key_add_info' => 'Informasi tambahan dapat diinput di pengaturan.',
        'key_set_info' => 'Pengaturan',
    ],
    'hi' => [
        'key_required' => 'आवश्यक है।',
        'key_remote' => 'कृपया इस क्षेत्र को ठीक करें।',
        'key_email' => 'कृपया एक मान्य ईमेल पता दर्ज करें।',
        'key_url' => 'कृपया एक मान्य URL दर्ज करें।',
        'key_date' => 'कृपया एक मान्य तिथि दर्ज करें।',
        'key_dateISO' => 'कृपया एक मान्य तिथि (ISO) दर्ज करें।',
        'key_number' => 'कृपया एक मान्य संख्या दर्ज करें।',
        'key_digits' => 'कृपया केवल अंक दर्ज करें।',
        'key_creditcard' => 'कृपया एक मान्य क्रेडिट कार्ड नंबर दर्ज करें।',
        'key_equalTo' => 'कृपया वही मान फिर से दर्ज करें।',
        'key_extension' => 'कृपया एक मान्य एक्सटेंशन के साथ मान दर्ज करें।',
        'key_maxlength' => 'कृपया {0} वर्णों से अधिक न दर्ज करें।',
        'key_minlength' => 'कृपया कम से कम {0} वर्ण दर्ज करें।',
        'key_rangelength' => 'कृपया {0} और {1} वर्णों के बीच का मान दर्ज करें।',
        'key_range' => 'कृपया {0} और {1} के बीच का मान दर्ज करें।',
        'key_max' => 'कृपया {0} से कम या उसके बराबर मान दर्ज करें।',
        'key_min' => 'कृपया {0} से अधिक या उसके बराबर मान दर्ज करें।',
        'key_confirm' => 'अनुमोदन',
        'key_cancel' => 'अनुमोदन',
        'key_attention' => 'अनुमोदन',
        'key_move_main' => 'क्या आप मुख्य पृष्ठ पर जाना चाहते हैं? अतिरिक्त जानकारी को सेटिंग में दर्ज किया जा सकता है।',
        'key_add_info' => 'अतिरिक्त जानकारी को सेटिंग में दर्ज किया जा सकता है।',
        'key_set_info' => 'सेटिंग',
    ],
    'es' => [
        'key_required' => 'Este campo es obligatorio.',
        'key_remote' => 'Por favor, corrija este campo.',
        'key_email' => 'Por favor, introduce una dirección de correo electrónico válida.',
        'key_url' => 'Por favor, introduce una URL válida.',
        'key_date' => 'Por favor, introduce una fecha válida.',
        'key_dateISO' => 'Por favor, introduce una fecha válida (ISO).',
        'key_number' => 'Por favor, introduce un número válido.',
        'key_digits' => 'Por favor, introduce solo dígitos.',
        'key_creditcard' => 'Por favor, introduce un número de tarjeta de crédito válido.',
        'key_equalTo' => 'Por favor, introduce el mismo valor de nuevo.',
        'key_extension' => 'Por favor, introduce una extensión válida.',
        'key_maxlength' => 'Por favor, no introduzcas más de {0} caracteres.',
        'key_minlength' => 'Por favor, introduce al menos {0} caracteres.',
        'key_rangelength' => 'Por favor, introduce un valor entre {0} y {1} caracteres de largo.',
        'key_range' => 'Por favor, introduce un valor entre {0} y {1}.',
        'key_max' => 'Por favor, introduce un valor menor o igual a {0}.',
        'key_min' => 'Por favor, introduce un valor mayor o igual a {0}.',
        'key_confirm' => 'Confirmar',
        'key_cancel' => 'Cancelar',
        'key_attention' => 'Atención',
        'key_move_main' => '¿Desea ir a la página principal? La información adicional se puede ingresar en la configuración.',
        'key_add_info' => 'La información adicional se puede ingresar en la configuración.',
        'key_set_info' => 'Configuración',
    ],
    'en' => [
        'key_required' => 'This field is required.',
        'key_remote' => 'Please fix this field.',
        'key_email' => 'Please enter a valid email address.',
        'key_url' => 'Please enter a valid URL.',
        'key_date' => 'Please enter a valid date.',
        'key_dateISO' => 'Please enter a valid date (ISO).',
        'key_number' => 'Please enter a valid number.',
        'key_digits' => 'Please enter only digits.',
        'key_creditcard' => 'Please enter a valid credit card number.',
        'key_equalTo' => 'Please enter the same value again.',
        'key_extension' => 'Please enter a valid extension.',
        'key_maxlength' => 'Please enter no more than {0} characters.',
        'key_minlength' => 'Please enter at least {0} characters.',
        'key_rangelength' => 'Please enter a value between {0} and {1} characters long.',
        'key_range' => 'Please enter a value between {0} and {1}.',
        'key_max' => 'Please enter a value less than or equal to {0}.',
        'key_min' => 'Please enter a value greater than or equal to {0}.',
        'key_confirm' => 'Confirm',
        'key_cancel' => 'Cancel',
        'key_attention' => 'Attention',
        'key_move_main' => 'Do you want to go to the main page? Additional information can be entered in the settings.',
        'key_add_info' => 'Additional information can be entered in the settings.',
        'key_set_info' => 'Settings',
    ],
];
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <meta name="Generator" content="<?= APP_AUTHOR ?>" />
    <meta name="Author" content="<?= APP_AUTHOR ?>" />
    <meta name="Keywords" content="<?= KEYWORDS ?>" />
    <meta name="Description" content="<?= DESCRIPTION ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="apple-mobile-web-app-title" content="<?= $_APP_TITLE ?>" />
    <meta content="telephone=no" name="format-detection" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta property="og:image" content="<?= $_OG_IMAGE ?>" />
    <meta property="og:image:width" content="151" />
    <meta property="og:image:height" content="79" />
    <meta property="og:title" content="<?= $_APP_TITLE ?>" />
    <meta property="og:description" content="<?= DESCRIPTION ?>" />
    <meta property="og:url" content="<?= APP_DOMAIN . $_SERVER['REQUEST_URI'] ?>" />
    <link rel="apple-touch-icon" sizes="144x144" href="<?= CDN_HTTP ?>/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= CDN_HTTP ?>/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= CDN_HTTP ?>/img/favicon-16x16.png">
    <link rel="manifest" href="<?= CDN_HTTP ?>/img/site.webmanifest">
    <link rel="mask-icon" href="<?= CDN_HTTP ?>/img/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <title><?= $_APP_TITLE ?></title>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-X8X49QPT01"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-X8X49QPT01');
        // console.log(<?= $_SESSION['_mt_idx'] ?>);
    </script>

    <!-- 제이쿼리 -->
    <script src="<?= CDN_HTTP ?>/js/jquery.min.js"></script>

    <!--부트스트랩-->
    <link rel="stylesheet" href="<?= CDN_HTTP ?>/css/boot_custom.css">
    <script src="<?= CDN_HTTP ?>/js/bootstrap.bundle.min.js"></script>

    <!-- 로티 -->
    <script src="<?= CDN_HTTP ?>/js/lottie-player.js"></script>

    <!-- xe아이콘 -->
    <link rel="stylesheet" href="<?= CDN_HTTP ?>/css/xeicon.min.css">

    <!-- ie css 변수적용 -->
    <script src="<?= CDN_HTTP ?>/js/ie11CustomProperties.min.js"></script>

    <!-- 폰트-->
    <!-- <link href="https://cdn.jsdelivr.net/gh/sun-typeface/SUITE/fonts/variable/woff2/SUITE-Variable.css" rel="stylesheet"> -->
    <!-- <link href="https://fastly.jsdelivr.net/gh/sun-typeface/SUITE/fonts/variable/woff2/SUITE-Variable.css" rel="stylesheet"> -->
    <link href="<?= CDN_HTTP ?>/lib/SUITE-2.0.3/fonts/variable/woff2/SUITE-Variable.css?v=<?= $v_txt ?>" rel="stylesheet">

    <!-- JS -->
    <script src="<?= CDN_HTTP ?>/js/custom.js?v=<?= $v_txt ?>"></script>

    <!-- swiper -->
    <script src="<?= CDN_HTTP ?>/js/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="<?= CDN_HTTP ?>/css/swiper-bundle.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= CDN_HTTP ?>/css/custom.css?v=<?= $v_txt ?>">

    <!-- DEV -->
    <link rel="stylesheet" href="<?= CDN_HTTP ?>/css/default_dev.css?v=<?= $v_txt ?>">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
    <script src="<?= CDN_HTTP ?>/js/jalert.js?v=<?= $v_txt ?>"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script> -->
    <!-- <script src="https://fastly.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script> -->
    <script src="<?= CDN_HTTP ?>/lib/jquery-validation-1.20.0/dist/jquery.validate.min.js"></script>
    <script type="text/javascript">
        <!--
        $.extend($.validator.messages, {
            required: "<?= $trans[$userLangHead]['key_required'] ?>", // 필수 항목입니다.
            remote: "<?= $trans[$userLangHead]['key_remote'] ?>", // 항목을 수정하세요.
            email: "<?= $trans[$userLangHead]['key_email'] ?>", // 유효하지 않은 E-Mail주소입니다.
            url: "<?= $trans[$userLangHead]['key_url'] ?>", // 유효하지 않은 URL입니다.
            date: "<?= $trans[$userLangHead]['key_date'] ?>", // 올바른 날짜를 입력하세요.
            dateISO: "<?= $trans[$userLangHead]['key_dateISO'] ?>", // 올바른 날짜(ISO)를 입력하세요.
            number: "<?= $trans[$userLangHead]['key_number'] ?>", // 유효한 숫자가 아닙니다.
            digits: "<?= $trans[$userLangHead]['key_digits'] ?>", // 숫자만 입력 가능합니다.
            creditcard: "<?= $trans[$userLangHead]['key_creditcard'] ?>", // 신용카드 번호가 바르지 않습니다.
            equalTo: "<?= $trans[$userLangHead]['key_equalTo'] ?>", // 같은 값을 다시 입력하세요.
            extension: "<?= $trans[$userLangHead]['key_extension'] ?>", // 올바른 확장자가 아닙니다.
            maxlength: $.validator.format("<?= $trans[$userLangHead]['key_maxlength'] ?>"), // {0}자를 넘을 수 없습니다.
            minlength: $.validator.format("<?= $trans[$userLangHead]['key_minlength'] ?>"), // {0}자 이상 입력하세요.
            rangelength: $.validator.format("<?= $trans[$userLangHead]['key_rangelength'] ?>"), // 문자 길이가 {0} 에서 {1} 사이의 값을 입력하세요.
            range: $.validator.format("<?= $trans[$userLangHead]['key_range'] ?>"), // {0} 에서 {1} 사이의 값을 입력하세요.
            max: $.validator.format("<?= $trans[$userLangHead]['key_max'] ?>"), // {0} 이하의 값을 입력하세요.
            min: $.validator.format("<?= $trans[$userLangHead]['key_min'] ?>"), // {0} 이상의 값을 입력하세요.
        });

        $.validator.setDefaults({
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            showErrors: function(errorMap, errorList) {
                if (this.numberOfInvalids()) { // 에러가 있으면
                    $.alert({
                        title: '',
                        type: 'blue',
                        typeAnimated: true,
                        content: errorList[0].message,
                        buttons: {
                            confirm: {
                                btnClass: 'btn-default btn-lg btn-block',
                                text: "<?= $trans[$userLangHead]['key_confirm'] ?>",
                                action: function() {
                                    errorList[0].element.focus()
                                },
                            },
                        },
                    });
                }
            }
        });
        //
        -->
    </script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js"></script>
    <script type="text/javascript">
        <!--
        moment.locale('ko');
        //
        -->
    </script>

    <script src="<?= CDN_HTTP ?>/js/default_dev.js?v=<?= $v_txt ?>"></script>

    <script>
        function isAndroidDevice() {
            return /Android/i.test(navigator.userAgent) && typeof window.smapAndroid !== 'undefined';
        }

        function isiOSDevice() {
            return /iPhone|iPad|iPod/i.test(navigator.userAgent) && window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.smapIos;
        }
        <?php
        //안드로이드 웹뷰로 페이지명을 전달해서 홈(index)면 뒤로가기2번으로 종료가 작동하도록 합니다.
        $page_nm = get_page_nm();
        ?>
        var isKeyboardOpen = false;
        var isScrolling = false; // 스크롤이 발생 중인지 여부를 나타내는 전역 변수
        $(document).ready(function() {
            // window.smapIos.pageType('<?= $page_nm ?>');
            var message = {
                "type": "pageType",
                "param": "<?= $page_nm ?>"
            };
            if (isAndroidDevice()) {
                window.smapAndroid.pageType('<?= $page_nm ?>');
                // 입력폼 작성시 키패드 감지하여 하단메뉴를 노출/미노출
                if ('visualViewport' in window) {
                    const VIEWPORT_VS_CLIENT_HEIGHT_RATIO = 0.75;
                    window.visualViewport.addEventListener('resize', function(event) {
                        if ((event.target.height * event.target.scale) / window.screen.height < VIEWPORT_VS_CLIENT_HEIGHT_RATIO) {
                            // 키패드가 열렸을 때 실행할 코드
                            $('.b_menu').hide(); // 하단 메뉴 숨기기
                            $('.opt_bottom').hide(); // 하단 메뉴 숨기기
                        } else {
                            // 키패드가 닫혔을 때 실행할 코드
                            $('.b_menu').show(); // 하단 메뉴 표시하기
                            $('.opt_bottom').show(); // 하단 메뉴 숨기기
                        }
                    });
                }
            } else if (isiOSDevice()) {
                window.webkit.messageHandlers.smapIos.postMessage(message);
            }
            // 스크롤 이벤트 핸들러 등록
            window.addEventListener('scroll', function() {
                // 현재 보이는 영역의 크기와 위치를 가져옵니다.
                var visibleHeight = window.visualViewport.height;
                var scrollTop = window.visualViewport.pageTop;

                // 만약 스크롤이 가상 영역을 벗어난 경우에는 스크롤을 막습니다.
                if (window.scrollY + visibleHeight > document.body.offsetHeight - 2) {
                    // 스크롤이 가상 영역을 벗어나면 스크롤 위치를 조정하여 가상 영역을 벗어나지 않도록 합니다.
                    window.scrollTo(0, document.body.offsetHeight - visualViewport.height - 1);
                }
            });
        });

        if (isiOSDevice()) {
            // 키패드 열림 이벤트를 감지
            const VIEWPORT_VS_CLIENT_HEIGHT_RATIO = 0.75;
            var viewport = window.visualViewport;

            // 이벤트 리스너를 추가하기 전에 기존의 리스너를 제거합니다.
            viewport.removeEventListener('resize', onViewportResize);
            viewport.addEventListener('resize', onViewportResize);

            function onViewportResize(event) {
                if ((event.target.height * event.target.scale) / window.screen.height < VIEWPORT_VS_CLIENT_HEIGHT_RATIO) {
                    isKeyboardOpen = true;
                    isScrolling = true;
                    // 키보드가 열렸을 때만 스크롤 이벤트 리스너를 추가합니다.
                    window.visualViewport.removeEventListener('scroll', handleWindowScroll);
                    window.visualViewport.addEventListener('scroll', handleWindowScroll);
                } else {
                    isKeyboardOpen = false;
                    isScrolling = false;
                }
                viewportHandler();
            }
        }

        function viewportHandler() {
            <?php if ($b_menu == '4') { ?>
                // 키패드가 열릴 때 스크롤을 막음
                preventScrollWhenKeypadOpen();
                if (isKeyboardOpen) {
                    // 키보드가 열렸을 때 실행할 코드
                    $('.b_menu').hide(); // 하단 메뉴 숨기기
                    $('.opt_bottom').hide(); // 하단 메뉴 숨기기
                } else {
                    // 키보드가 닫혔을 때 실행할 코드
                    $('.b_menu').show(); // 하단 메뉴 표시하기
                    $('.opt_bottom').show(); // 하단 메뉴 숨기기24
                }
            <?php } else { ?>
                document.addEventListener('DOMContentLoaded', function() {
                    preventScrollWhenKeypadOpen();
                    var b_botton = document.querySelector('.b_botton');
                    var layoutViewport = document.getElementById('layoutViewport');
                    if (!b_botton) {
                        console.error("Element with class 'b_botton' not found.");
                        return;
                    }

                    if (isKeyboardOpen) {
                        // When the keyboard is open
                        $('.b_menu').hide(); // Hide bottom menu
                        $('.opt_bottom').hide(); // Hide bottom options
                        // Calculate bar position
                        var offsetY = viewport.height - layoutViewport.getBoundingClientRect().height + viewport.offsetTop;
                        // Adjust bar position
                        b_botton.style.transform = 'translateX(-50%) translateY(' + offsetY + 'px)';
                        b_botton.style.transition = 'all 0.2s ease-in-out';
                    } else {
                        // When the keyboard is closed
                        $('.b_menu').show(); // Show bottom menu
                        $('.opt_bottom').show(); // Show bottom options
                        // Move b_botton element to original position
                        b_botton.style.transform = 'translateX(-50%)';
                        b_botton.style.position = 'fixed'; // Reset to original position
                        b_botton.style.bottom = '0';
                    }
                });
            <?php } ?>

        }
        // scroll event
        function handleWindowScroll() {
            if (isScrolling) {
                var b_botton = document.querySelector('.b_botton');
                var layoutViewport = document.getElementById('layoutViewport');
                var offsetY = viewport.height - layoutViewport.getBoundingClientRect().height + viewport.offsetTop;

                // 👇 scroll 변화에 따라 viewport div 이동
                b_botton.style.transform = 'translateX(-50%) translateY(' + offsetY + 'px)';
                b_botton.style.transition = 'all 0.2s ease-in-out';
            }
        }
        // iOS에서 키보드가 열릴 때 스크롤을 막는 함수
        function preventScrollWhenKeypadOpen() {
            // 터치 이벤트에서 스크롤을 막는 함수
            function preventDefaultTouch(event) {
                event.preventDefault();
            }

            function onFocusIn() {
                // 터치 이벤트가 발생할 때 스크롤을 막음
                window.addEventListener('touchmove', preventDefaultTouch, {
                    passive: false
                });
            }

            function onFocusOut() {
                // 터치 이벤트에서 스크롤 막는 것을 제거
                window.removeEventListener('touchmove', preventDefaultTouch);
            }
            if (isiOSDevice()) {
                // iOS에서는 visualViewport를 사용하여 키보드 열림/닫힘 감지
                const VIEWPORT_VS_CLIENT_HEIGHT_RATIO = 0.75;
                var viewport = window.visualViewport;

                function onViewportResize(event) {
                    if ((event.target.height * event.target.scale) / window.screen.height < VIEWPORT_VS_CLIENT_HEIGHT_RATIO) {
                        onFocusIn();
                    } else {
                        onFocusOut();
                    }
                }

                viewport.addEventListener('resize', onViewportResize);
            }
        }

        //안드로이드>웹뷰 스크립트 실행
        function backPress() {
            history.back();
        }

        function f_back_chk(v) {
            if (v == 'form_add_info') {
                $.confirm({
                    type: "blue",
                    typeAnimated: true,
                    title: "<?= $trans[$userLangHead]['key_attention'] ?>",
                    content: "<?= $trans[$userLangHead]['key_move_main'] ?>",
                    buttons: {
                        confirm: {
                            text: "<?= $trans[$userLangHead]['key_confirm'] ?>",
                            action: function() {
                                location.href = './';
                            },
                        },
                        cancel: {
                            btnClass: "btn-outline-default",
                            text: "<?= $trans[$userLangHead]['key_cancel'] ?>",
                            action: function() {
                                close();
                            },
                        },
                    },
                });
            }

            return false;
        }
    </script>

    <?php
    // $chk_admin 변수가 필요한 경우 주석 해제
    if ($chk_admin) {
        // 안드로이드 웹뷰로 페이지명을 전달해서 홈(index)면 뒤로가기 2번으로 종료가 작동하도록 합니다.
        if ($chk_mobile) {
    ?>
            <script src="<?= CDN_HTTP ?>/lib/fakeloader/fakeloader.min.js?v=<?= $v_txt ?>"></script>
            <link rel="stylesheet" href="<?= CDN_HTTP ?>/lib/fakeloader/fakeloader.css?v=<?= $v_txt ?>">
            <script type="text/javascript">
                $(document).ready(function() {
                    setTimeout(() => {
                        window.FakeLoader.init({
                            auto_hide: true
                        });
                    }, 100);
                });

                document.getElementById("fakeloader-overlay").addEventListener("click", function(event) {
                    event.stopPropagation(); // 이벤트 전파 중지
                });
            </script>
    <?php
        } // if ($chk_admin) 닫는 주석 추가
    }
    ?>

</head>

<?php

if ($chk_mobile) { // 모바일일 경우
    if ($_SESSION['_mt_token_id'] != '') { // 토큰값이 있다면
        setcookie('_mt_token_id', $_SESSION['_mt_token_id']);
    } else { // 토큰값이 없다면
        if ($_COOKIE['_mt_token_id']) { // 쿠키값이 있을 경우 세션에 넣어주기
            $_SESSION['_mt_token_id'] = $_COOKIE['_mt_token_id'];
        } else { //쿠키값이 없다면 로그아웃 처리시키기
            unset($arr_query);
            $arr_query = array(
                "mt_token_id" => 'sessin_null',
                "mt_token_id_cookie" => 'cokkie_null',
                "mt_lat" => $_SESSION['_mt_lat'],
                "mt_long" => $_SESSION['_mt_long'],
                "event_url" => $_SERVER['PHP_SELF'],
                "referer_url" => $_SERVER['HTTP_REFERER'],
                "now_url" => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                "agent" => $_SERVER['HTTP_USER_AGENT'],
                "ip" => $_SERVER['REMOTE_ADDR'],
                "auth_chk" => $_SESSION['_auth_chk'],
                "wdate" => $DB->now(),
            );
            $DB->insert('page_log_t', $arr_query);
?>
            <script>
                // 세션정보가 없다면 토큰값 위치정보 값 새로 받아서 auth로 보내기
                var message1 = {
                    "type": "session_refresh",
                    "param": "<?= $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : "" ?>",
                };
                if (isAndroidDevice()) {
                    window.smapAndroid.session_refresh('<?= $_SERVER['HTTP_REFERER'] ?>');
                } else if (isiOSDevice()) {
                    window.webkit.messageHandlers.smapIos.postMessage(message1);
                }
            </script>
        <?php
            // alert('앱토큰값이 없습니다', './logout');
        }
    }
}

//읽지 않은 알림이 있는지?
if ($_SESSION['_mt_idx']) {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('plt_read_chk', 'N');
    $DB->where('plt_show', 'Y');
    $row_alarm = $DB->getone('push_log_t', 'count(*) as cnt');

    if ($row_alarm['cnt'] > 0) {
        $alarm_t = ' on';
    } else {
        $alarm_t = '';
    }
    if ($_SESSION['_mt_level'] == '5' && $sub_title != "plan") { //유료회원일때 구독취소및 갱신 확인
        ?>
        <script>
            /* 인앱결제 체크 -> 크론으로 대체(1시간마다 체크)
            var message2 = {
                "type": "purchaseCheck",
            };
            if (isAndroidDevice()) {
                window.smapAndroid.purchaseCheck();
            } else if (isiOSDevice()) {
                window.webkit.messageHandlers.smapIos.postMessage(message2);
            }
            */
        </script>
<?php
        member_plan_check($_SESSION['_mt_idx']);
    }
    // 유료회원 마감되었는지 확인 -> 크론으로 대체(1시간마다 체크)
    /*
    $current_date = date("Y-m-d H:i:s");
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('mt_level', 5);
    $plan_End_row = $DB->getone('member_t');
    if ($plan_End_row['mt_idx'] && $plan_End_row['mt_level'] == 5 && $current_date > $plan_End_row['mt_plan_date']) {
        unset($arr_query);
        $arr_query = array(
            'mt_level' => '2'
        );
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $DB->update('member_t', $arr_query);
    }
    */
    // 그룹원 초대코드 만료 확인
    group_invite_del($_SESSION['_mt_idx']);
    member_location_history_delete();
    coupon_end_check();
}
?>
<body id="wrap">
    <?php if ($h_menu == '1') { ?>
        <!-- head_01 -->
        <div class="h_menu head_01">
            <div class="logo_wr"><a class="logo" href="<?= CDN_HTTP ?>/"><img src="<?= CDN_HTTP ?>/img/logo.png" alt="<?=$trans['txt_go_to_home'] ?>"></a></div>
            <div class="mr-5 h_tit">
                <p class="fs_18 fw_600 mr_24"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div class="d-flex">
                <a href="./setting" class="mr-3" onclick="sendEvent('click_setting', 'engagement', 'setting');"><img src="<?= CDN_HTTP ?>/img/ico_set.png" width="24px" alt="<?=$trans['txt_settings'] ?>" /></a>
                <a href="./alarm_list" class="arm_btn <?= $alarm_t ?>" onclick="sendEvent('click_alarm', 'engagement', 'alarm');"><img src="<?= CDN_HTTP ?>/img/ico_arm.png" width="24px" alt="<?=$trans['txt_alarm'] ?>" /></a>
            </div><!-- on 추가되면 활성화-->
        </div>
    <?php } elseif ($h_menu == '2') { ?>
        <!-- head_02 -->
        <div class="h_menu head_02">
            <div><button type="button" class="btn hd_btn px-0 py-0" onclick="history.back();"><img src="<?= CDN_HTTP ?>/img/top_back_b.png" width="24px" alt="<?=$trans['txt_back'] ?>" /></button></div>
            <div class="mr-4 h_tit">
                <p class="fs_16 fw_700"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div></div>
        </div>
    <?php } elseif ($h_menu == '3') { ?>
        <!-- head_03 -->
        <div class="h_menu head_03">
            <div><button type="button" class="btn hd_btn px-0 py-0" onclick="location.href='<?= $h_url ?>'"><img src="<?= CDN_HTTP ?>/img/top_back_b.png" width="24px" alt="<?=$trans['txt_back'] ?>" /></button></div>
            <div class="h_tit">
                <p class="fs_17 fw_700"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div><button type="button" class="btn hd_btn px-0 py-0 fs_14 fw_400 text-primary" onclick="location.href='./inquiry_form'"><?=$trans['txt_contact_us_button'] ?></button></div>
        </div>
    <?php } elseif ($h_menu == '4') { ?>
        <!-- head_07 -->
        <div class="h_menu head_04">
            <div class="h_tit fs_22 fw_700">
                <p class="fs_17 fw_700 mr_24"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div></div>
            <div><a href="alarm_list" class="arm_btn<?= $alarm_t ?>" onclick="sendEvent('click_alarm', 'engagement', 'alarm');"><img src="<?= CDN_HTTP ?>/img/ico_arm.png" width="24px" alt="<?=$trans['txt_alarm'] ?>" /></a></div><!-- on 추가되면 활성화-->
        </div>
    <?php } elseif ($h_menu == '5') { ?>
        <!-- head_08 -->
        <div class="h_menu head_05 bg_main">
            <div class="h_tit ">
                <p class="fs_22 fw_700 mr_24"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div></div>
            <div></div>
        </div>
    <?php } elseif ($h_menu == '6') { ?>
        <!-- head_02 -->
        <div class="h_menu head_02">
            <div><button type="button" class="btn hd_btn px-0 py-0" onclick="location.href='<?= $h_url ?>'"><img src="<?= CDN_HTTP ?>/img/top_back_b.png" width="24px" alt="<?=$trans['txt_back'] ?>" /></button></div>
            <div class="mr-4 h_tit">
                <p class="fs_16 fw_700"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div></div>
        </div>
    <?php } elseif ($h_menu == '7') { ?>
        <!-- head_02 -->
        <div class="h_menu head_02">
            <div><button type="button" class="btn hd_btn px-0 py-0" onclick="<?= $h_func ?>"><img src="<?= CDN_HTTP ?>/img/top_back_b.png" width="24px" alt="<?=$trans['txt_back'] ?>" /></button></div>
            <div class="mr-4 h_tit">
                <p class="fs_16 fw_700"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div></div>
        </div>
    <?php } elseif ($h_menu == '8') { ?>
        <!-- <div class="h_menu head_03">
            <div><button type="button" class="btn hd_btn px-0 py-0" onclick="history.back();"><img src="./img/top_back_b.png" width="24px" alt="뒤로" /></button></div>
            <div class="h_tit">
                <p class="fs_17 fw_700"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div><button type="button" class="btn hd_btn px-0 py-0 fs_14 fw_400 text-primary" onclick="location.href='./plan_info'">플랜</button></div>
        </div> -->
        <div class="h_menu head_07">
            <div class="h_tit">
                <p class="fs_22 fw_700 mr_24"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
            <div></div>
            <div><button type="button" class="btn hd_btn px-0 py-0 fs_14 fw_400 text-primary" onclick="location.href='./plan_information'"><?=$trans['txt_plan'] ?></button></div>
        </div>
    <?php } elseif ($h_menu == '9') { ?>
        <!-- head_07 -->
        <div class="h_menu head_04">
            <div class="h_tit fs_22 fw_700">
                <p class="fs_17 fw_700 mr_24"><?= $_SUB_HEAD_TITLE ?></p>
            </div>
        </div>
    <?php } ?>

    <script>
        function isAndroid() {
            return navigator.userAgent.match(/Android/i);
        }

        function isiOS() {
            return navigator.userAgent.match(/iPhone|iPad|iPod|Mac|Apple/i);
        }

        function sendEvent(eventName, eventCategory, eventLabel) {
            gtag('event', eventName, {
                'event_category': eventCategory,
                'event_label': eventLabel,
                'user_id': '<?= $_SESSION['_mt_idx'] ?>',
                'platform': isAndroidDevice() ? 'Android' : (isiOSDevice() ? 'iOS' : 'Unknown')
            });
        }
    </script>
</body>