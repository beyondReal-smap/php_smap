<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '2';
$_SUB_HEAD_TITLE = translate("추천인입력", $userLang); // "추천인입력" 번역
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
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
?>

<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3 text_dynamic"><?= translate("함께할수록 좋아요.", $userLang); ?>
        <?= translate("추천인 전화번호를 입력하면", $userLang); ?>
        <?= translate("나와 추천인 모두 1개월 무료", $userLang); ?></p> 
        <!-- "함께할수록 좋아요.\n추천인 전화번호를 입력하면\n나와 추천인 모두 1개월 무료" 번역 (줄바꿈 유지) -->
        <p class="fs_12 fc_gray_600 mt-3 line_h1_2"><?= translate('가입하신 모든 분들께 한 번의 특별한 혜택을 드려요!', $userLang); ?></p> 
        <!-- "가입하신 모든 분들께 한 번의 특별한 혜택을 드려요!" 번역 -->
        <form action="">
            <input type="hidden" name="act" id="act" value="recommend_input" />
            <input type="hidden" name="mt_idx" id="mt_idx" value="<?=$_SESSION['_mt_idx']?>" />
            <div class="mt-5">
                <div class="ip_wr mt_hp_msg" id="mt_hp_text">
                    <div class="ip_tit">
                        <h5 class=""><?= translate('추천인 코드', $userLang); ?></h5> <!-- "추천인 코드" 번역 -->
                    </div>
                    <input type="tel" class="form-control" placeholder="<?= translate('추천인코드를 입력해주세요.', $userLang); ?>" id="mt_hp" name="mt_hp" maxlength="13" oninput="restrictInput(this);formatPhoneNumber(this);" >
                    <!-- "추천인코드를 입력해주세요." 번역 -->
                    <p class="fs_12 fc_gray_600 mt-3 px-4 line_h1_2 text_dynamic"><?= translate('추천인 코드는 추천한 분의', $userLang) ?><span class="text-text fw_700"><?= translate('전화번호', $userLang) ?></span><?= translate('를 입력하시면 됩니다.', $userLang); ?></p>
                    <!-- "추천인 코드는 추천한 분의 <span class="text-text fw_700">전화번호</span>를 입력하시면 됩니다." 번역 -->
                    <!-- <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div> -->
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= translate('전화번호 형식에 맞게 입력해주세요.', $userLang); ?></div> 
                    <!-- "전화번호 형식에 맞게 입력해주세요." 번역 -->
                </div>
            </div>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block"  onclick="check_recommend()"><?= translate('입력했어요!', $userLang) ?></button> <!-- "입력했어요!" 번역 -->
            </div>
        </form>
    </div>
</div>
<script>
    //휴대전화번호 입력 확인
    var phoneCheck = false;
    function formatPhoneNumber(input) {
        // 입력된 내용에서 숫자만 남기고 모든 문자 제거
        var phoneNumber = input.value.replace(/\D/g, '');

        // 전화번호 형식에 맞게 "-" 추가
        if (phoneNumber.length > 3 && phoneNumber.length <= 7) {
            phoneNumber = phoneNumber.replace(/(\d{3})(\d{1,4})/, '$1-$2');
        } else if (phoneNumber.length > 7) {
            phoneNumber = phoneNumber.replace(/(\d{3})(\d{4})(\d{1,4})/, '$1-$2-$3');
        }

        // 최대 길이 제한
        if (phoneNumber.length > 13) {
            phoneNumber = phoneNumber.substring(0, 13);
        }

        // 형식이 적용된 전화번호로 변경
        input.value = phoneNumber;
    }

    function restrictInput(element) {
        // 숫자와 하이픈만 허용
        element.value = element.value.replace(/[^0-9-]/g, '');
        // 중복된 하이픈 제거
        element.value = element.value.replace(/-{2,}/g, '-');
    }
    function check_recommend() {
        // 비밀번호 입력값 가져오기
        let mt_idx = $("#mt_idx").val();
        let mt_hp = $("#mt_hp").val();

        if (mt_hp.length < 13) {
            $(".mt_hp_msg").addClass("ip_invalid");
            $(".mt_hp_msg").removeClass("ip_valid");
            $("#mt_hp").focus();
            return false;
        } else {
            $(".mt_hp_msg").addClass("ip_valid");
            $(".mt_hp_msg").removeClass("ip_invalid");
        }
        $.ajax({
            url: "./recommend_update",
            type: "POST",
            data: {
                act: "recommend_input",
                mt_idx: mt_idx,
                mt_hp: mt_hp,
            },
            dataType: "json",
            success: function(d, s) {
                console.log(d);
                if (d.result == "ok") {
                    jalert_url('<?= translate('추천인 입력이 완료되었습니다.', $userLang); ?>','./setting'); // "추천인 입력이 완료되었습니다." 번역
                } else if (d.result == "use") {
                    jalert('<?= translate('이미 추천인입력을 사용하였습니다.', $userLang); ?>'); // "이미 추천인입력을 사용하였습니다." 번역
                } else if (d.result == "none") {
                    jalert('<?= translate('해당되는 추천인을 찾을 수 없습니다.', $userLang); ?>'); // "해당되는 추천인을 찾을 수 없습니다." 번역
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