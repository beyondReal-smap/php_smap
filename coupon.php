<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '2';
$_SUB_HEAD_TITLE = "쿠폰입력";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
if ($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', './logout');
    }
}
?>


<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3 text_dynamic">제공받으신 쿠폰번호를
            입력해주세요.
        </p>
        <form action="" class="">
            <input type="hidden" name="act" id="act" value="coupon_input" />
            <input type="hidden" name="mt_idx" id="mt_idx" value="<?= $_SESSION['_mt_idx'] ?>" />
            <div class="mt-5">
                <div class="ip_wr ct_code_msg" id="ct_code_text">
                    <div class="ip_tit">
                        <h5 class="">쿠폰번호</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="쿠폰번호를 입력해주세요." id="ct_code" name="ct_code" maxlength="8">
                    <p class="fs_12 fc_gray_600 mt-3 px-4 line_h1_2 text_dynamic">쿠폰번호는 8자리 문자, 숫자 조합입니다.</p>
                    <!-- <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div> -->
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 쿠폰코드에 맞게 입력해주세요.</div>
                </div>
            </div>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="check_coupon()"><?= translate('입력했어요!', $userLang) ?></button>
            </div>
        </form>
    </div>
</div>

<script>
    function check_coupon() {
        // 비밀번호 입력값 가져오기
        let mt_idx = $("#mt_idx").val();
        let ct_code = $("#ct_code").val();

        if (ct_code.length < 8) {
            $(".ct_code_msg").addClass("ip_invalid");
            $(".ct_code_msg").removeClass("ip_valid");
            $("#ct_code").focus();
            return false;
        } else {
            $(".ct_code_msg").addClass("ip_valid");
            $(".ct_code_msg").removeClass("ip_invalid");
        }
        $.ajax({
            url: "./coupon_update",
            type: "POST",
            data: {
                act: "coupon_input",
                mt_idx: mt_idx,
                ct_code: ct_code,
            },
            dataType: "json",
            success: function(d, s) {
                console.log(d);
                if (d.result == "ok") {
                    jalert_url('쿠폰적용이 완료되었습니다.', './setting');
                } else if (d.result == "use") {
                    jalert('이미 사용한 쿠폰코드입니다.');
                } else if (d.result == "end") {
                    jalert('만료된 쿠폰코드입니다.');
                } else if (d.result == "none") {
                    jalert('해당되는 쿠폰을 찾을 수 없습니다.');
                }
            },
            error: function(d) {
                console.log("error:" + d);
            },
        });
    }

    $("#mt_hp").filter(".lower").on("keyup", function() {
        $(this).val($(this).val().toLowerCase());
    });
</script>