<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '6';
$h_url = './setting';

$_SUB_HEAD_TITLE = $translations['txt_map_select_two'];
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert($translations['txt_login_required'], './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert($translations['txt_login_attempt_other_device'], './logout');
    }
}
$mt_info = get_member_t_info();
?>
<div class="container sub_pg">
    <div class="mt-5">
        <form action="" class="">
                <div>
                    <div class="checks ava_checks">
                        <label>
                            <input type="radio" name="rd1" <?= $mt_info['mt_map'] == 'N' ? 'checked' : '' ?> onclick="updateMap('N')">
                            <div class="w-100 chk_p rounded ava_class ava_class_b mt-0 d-flex align-items-center">
                                <p class="fs_18 fw_700 text_dynamic map-title">네이버</p>
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <p class="fs_14 fw_500 map-description">대한민국에서는 네이버 지도가 더 나은 경험을 선사해요.</p>
                                    <div class="ava_class_btn ava_class_btn_b select-btn" data-type="N">선택</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="plan_info_wrap mt-3">
                    <div class="checks ava_checks">
                        <label class="map-option">
                            <input type="radio" name="rd1" <?= $mt_info['mt_map'] != 'N' ? 'checked' : '' ?> onclick="updateMap('Y')">
                            <div class="w-100 chk_p rounded ava_class ava_class_b mt-0 d-flex align-items-center">
                                <p class="fs_18 fw_700 text_dynamic map-title">구글</p>
                                <div class="d-flex align-items-center justify-content-between w-100">
                                    <p class="fs_14 fw_500 map-description">구글 지도를 선택하면 전 세계 어디서나 사용할 수 있어요.</p>
                                    <div class="ava_class_btn ava_class_btn_b select-btn" data-type="Y">선택</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
        </form>
    </div>
</div>

<style>
    .map-option {
        display: block;
        width: 100%;
        margin-bottom: 20px;
    }
    .map-title {
        margin-bottom: 0;
        width: 100px; /* 너비를 충분히 확보 */
    }
    .map-description {
        width: 65%;
        padding-right: 20px;
        margin-bottom: 0;
        line-height: 1.5;
    }
    .ava_class_b {
        padding: 20px;
    }
    .select-btn {
        width: 80px;
        text-align: center;
    }
</style>
<script>
    function updateMap(mapType) {
        var form_data = new FormData();
        form_data.append("act", "update_map");
        form_data.append("mt_map", mapType);

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
                    jalert('지도 설정이 업데이트되었습니다.');
                } else {
                    jalert('지도 설정 업데이트에 실패했습니다.');
                }
            },
            error: function(err) {
                console.log(err);
                jalert('오류가 발생했습니다.');
            },
        });
    }
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>