<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '2';

$_SUB_HEAD_TITLE = $translations['txt_referrer_input']; 
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
if ($userLang == 'ko') {
    $inputType = 'tel';
    $placeholder = $translations['txt_enter_referral_code'];
    $inputName = 'mt_hp';
    $inputId = 'mt_hp';
    $maxLength = '13';
    $onInput = 'restrictInput(this);formatPhoneNumber(this);';
} else {
    $inputType = 'email';
    $placeholder = $translations['txt_enter_referral_code'];
    $inputName = 'mt_email';
    $inputId = 'mt_email';
    $maxLength = '';
    $onInput = ''; 
}
?>

<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3 text_dynamic" style="line-height: 0.7;"><?=$translations['txt_better_together'] ?><br>
        <?=$translations['txt_enter_referrer_phone'] ?><br>
        <?=$translations['txt_1_month_free_both'] ?></p> 
        <p class="fs_12 fc_gray_600 mt-3 line_h1_2"><?=$translations['txt_special_benefit'] ?></p>
        <form action="">
            <input type="hidden" name="act" id="act" value="recommend_input" />
            <input type="hidden" name="mt_idx" id="mt_idx" value="<?=$_SESSION['_mt_idx']?>" />
            <div class="mt-5">
                <div class="ip_wr mt_hp_msg" id="mt_hp_text">
                    <div class="ip_tit">
                        <h5 class=""><?=$translations['txt_referral_code'] ?></h5> 
                    </div>
                    <input type="<?=$inputType?>" class="form-control" placeholder="<?=$placeholder?>" id="<?=$inputId?>" name="<?=$inputName?>" maxlength="<?=$maxLength?>" oninput="<?=$onInput?>">
                    <?=$translations['txt_referral_code_explanation'] ?>
                    <?php if ($userLang == 'ko') { ?>
                        <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?=$translations['txt_correct_phone_format'] ?></div>
                    <?php } else { ?>
                        <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?=$translations['txt_correct_mail_format'] ?></div>
                    <?php } ?>
                </div>
            </div>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block"  onclick="check_recommend()"><?=$translations['txt_enter'] ?></button>
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
        let mt_input = $("#<?=$inputId?>").val();

        if ('<?= $userLang ?>' == 'ko') {
            if (mt_input.length < 13) {
                $(".mt_hp_msg").addClass("ip_invalid");
                $(".mt_hp_msg").removeClass("ip_valid");
                $("#<?=$inputId?>").focus();
                return false;
            } else {
                $(".mt_hp_msg").addClass("ip_valid");
                $(".mt_hp_msg").removeClass("ip_invalid");
            }
        } else {
            if (validateEmail(mt_input)) {
                $(".mt_hp_msg").addClass("ip_invalid");
                $(".mt_hp_msg").removeClass("ip_valid");
                $("#<?=$inputId?>").focus();
                return false;
            } else {
                $(".mt_hp_msg").addClass("ip_valid");
            }
        }
        
        $.ajax({
            url: "./recommend_update",
            type: "POST",
            data: {
                act: "recommend_input",
                mt_idx: mt_idx,
                <?=$inputName?>: mt_input,
            },
            dataType: "json",
            success: function(d, s) {
                console.log(d);
                if (d.result == "ok") {
                    jalert_url('<?=$translations['txt_referrer_entry_done'] ?>','./setting');
                } else if (d.result == "use") {
                    jalert('<?=$translations['txt_referrer_input_used'] ?>');
                } else if (d.result == "none") {
                    jalert('<?=$translations['txt_referrer_not_found'] ?>'); 
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
    
    function validateEmail(input) {
        // 이메일 형식 검증
        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (emailPattern.test(input.value)) {
            return true;
        } else {
            return false;
        }
    }
</script>