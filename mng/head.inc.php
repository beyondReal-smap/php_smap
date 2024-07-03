<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

$_APP_TITLE = APP_TITLE;
$_OG_IMAGE = OG_IMAGE;
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <meta name="Generator" content="<?=APP_AUTHOR?>" />
    <meta name="Author" content="<?=APP_AUTHOR?>" />
    <meta name="Keywords" content="<?=KEYWORDS?>" />
    <meta name="Description" content="<?=DESCRIPTION?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="apple-mobile-web-app-title" content="<?=$_APP_TITLE?>" />
    <meta content="telephone=no" name="format-detection" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta property="og:image" content="<?=$_OG_IMAGE?>" />
    <meta property="og:image:width" content="151" />
    <meta property="og:image:height" content="79" />
    <meta property="og:title" content="<?=$_APP_TITLE?>" />
    <meta property="og:description" content="<?=DESCRIPTION?>" />
    <meta property="og:url" content="<?=APP_DOMAIN.$_SERVER['REQUEST_URI']?>" />
    <link rel="apple-touch-icon" sizes="180x180" href="<?=CDN_HTTP?>/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?=CDN_HTTP?>/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=CDN_HTTP?>/img/favicon-16x16.png">
    <link rel="manifest" href="<?=CDN_HTTP?>/img/site.webmanifest">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <title><?=$_APP_TITLE?></title>

    <!-- base css&js -->
    <link rel="stylesheet" type="text/css" href="<?=CDN_HTTP?>/css/base_mng.css" />
    <script type="text/javascript" src="<?=CDN_HTTP?>/js/base_mng.js"></script>

    <!-- icons -->
    <link rel="stylesheet" href="//cdn.materialdesignicons.com/4.7.95/css/materialdesignicons.min.css">

    <!-- fonts -->
    <link rel="stylesheet" as="style" crossorigin href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.8/dist/web/static/pretendard.css" />

    <!-- jquery.validate & jalert -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
    <script src="<?=CDN_HTTP?>/js/jalert.js?v=<?=$v_txt?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script type="text/javascript">
    <!--
    $.extend($.validator.messages, {
        required: "필수 항목입니다.",
        remote: "항목을 수정하세요.",
        email: "유효하지 않은 E-Mail주소입니다.",
        url: "유효하지 않은 URL입니다.",
        date: "올바른 날짜를 입력하세요.",
        dateISO: "올바른 날짜(ISO)를 입력하세요.",
        number: "유효한 숫자가 아닙니다.",
        digits: "숫자만 입력 가능합니다.",
        creditcard: "신용카드 번호가 바르지 않습니다.",
        equalTo: "같은 값을 다시 입력하세요.",
        extension: "올바른 확장자가 아닙니다.",
        maxlength: $.validator.format("{0}자를 넘을 수 없습니다. "),
        minlength: $.validator.format("{0}자 이상 입력하세요."),
        rangelength: $.validator.format("문자 길이가 {0} 에서 {1} 사이의 값을 입력하세요."),
        range: $.validator.format("{0} 에서 {1} 사이의 값을 입력하세요."),
        max: $.validator.format("{0} 이하의 값을 입력하세요."),
        min: $.validator.format("{0} 이상의 값을 입력하세요."),
    });

    $.validator.setDefaults({
        onkeyup: false,
        onclick: false,
        onfocusout: false,
        showErrors: function(errorMap, errorList) {
            if (this.numberOfInvalids()) { // 에러가 있으면
                $.alert({
                    title: '',
                    content: errorList[0].message,
                    buttons: {
                        confirm: {
                            text: "확인",
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

    <link rel="stylesheet" type="text/css" href="<?=CDN_HTTP?>/lib/datepicker/jquery.datetimepicker.min.css" />
    <script src="<?=CDN_HTTP?>/lib/datepicker/jquery.datetimepicker.full.min.js"></script>
    <script type="text/javascript">
    <!--
    jQuery.datetimepicker.setLocale('ko');
    //
    -->
    </script>

    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" integrity="sha512-d9xgZrVZpmmQlfonhQUvTR7lMPtO7NkZMkA0ABN3PHCbKA5nqylQ/yWlFAyY6hYgdF1Qh6nYiuADWwKB4C2WSw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link rel="stylesheet" type="text/css" href="<?=CDN_HTTP?>/css/default_mng.css?v=<?=$v_txt?>" />
    <script type="text/javascript" src="<?=CDN_HTTP?>/js/default_mng.js?v=<?=$v_txt?>"></script>

    <!-- 달력 -->
    <link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css" />
    <script src="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.js"></script>
</head>

<body>