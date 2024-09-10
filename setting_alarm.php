<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '';
$h_menu = '6';
$h_url = './setting';
$_SUB_HEAD_TITLE = translate("알림설정", $userLang); // "알림설정" 번역
include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert(translate('로그인이 필요합니다.', $userLang), './login', ''); // "로그인이 필요합니다." 번역
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert(translate('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', $userLang), './logout'); // "다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다." 번역
    }
}

$mt_info = get_member_t_info();
?>
<div class="container sub_pg px-0">
    <div class="bg_gray py_20 px_16">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="fs_18 fw_700"><?= translate('전체알림 설정', $userLang) ?></h5> <!-- "전체알림 설정" 번역 -->
            <div class="">
                <div class="custom-switch ml-auto">
                    <input type="hidden" name="mt_push_chg" id="mt_push_chg" value="<?=$mt_info['mt_push1']?>" />
                    <input type="checkbox" class="custom-control-input" name="mt_push1" id="mt_push1" value="Y" onclick="f_mt_push_chg();" />
                    <label class="custom-control-label" for="mt_push1"></label>
                </div>
            </div>
        </div>
    </div>
    <script>
    $(document).ready(function() {
        var mt_push_chg = $('#mt_push_chg').val();

        if (mt_push_chg == 'Y') {
            $('#mt_push1').attr('checked', true);
        } else {
            $('#mt_push1').attr('checked', false);
        }
    });

    function f_mt_push_chg() {
        var form_data = new FormData();
        form_data.append("act", "mt_push_chg");
        form_data.append("mt_push_chg", $('#mt_push_chg').val());

        $.ajax({
            url: "./setting_update",
            enctype: "multipart/form-data",
            data: form_data,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 5000,
            success: function(data) {
                if (data == 'Y') {
                    $('#mt_push1').attr('checked', true);
                } else {
                    $('#mt_push1').attr('checked', false);
                }
                $('#mt_push_chg').val(data);
            },
            error: function(err) {
                console.log(err);
            },
        });
    }
    </script>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>