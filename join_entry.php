<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "";
$_GET['hd_num'] = '';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
// $_SESSION['mt_email'] = '';
$_SESSION['mt_pass'] = '';
$_SESSION['mt_gender'] = '';
$_SESSION['pick_year'] = '';
$_SESSION['pick_month'] = '';
$_SESSION['pick_day'] = '';
$_SESSION['mt_name'] = '';

$mt_pass = isset($_SESSION['mt_pass']) ? $_SESSION['mt_pass'] : '';
$mt_gender = isset($_SESSION['mt_gender']) ? $_SESSION['mt_gender'] : '';
$pick_year = isset($_SESSION['pick_year']) ? $_SESSION['pick_year'] : '';
$pick_month = isset($_SESSION['pick_month']) ? $_SESSION['pick_month'] : '';
$pick_day = isset($_SESSION['pick_day']) ? $_SESSION['pick_day'] : '';
$mt_name = isset($_SESSION['mt_name']) ? $_SESSION['mt_name'] : '';
$mt_email = isset($_SESSION['mt_email']) ? $_SESSION['mt_email'] : '';
?>

<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3 test_dynamic" style="line-height: 1.2;"><?= $translations['txt_smap_together_closer'] ?><br></p>
        <p class="fc_gray_600 test_dynamic mt-3"><?= $translations['txt_communicate_faster_accurately'] ?></p>
        <form action="" onkeypress="return event.keyCode != 13;">
            <div class="mt-5">
                <div class="ip_wr mt-5">
                    <div class="ip_tit">
                        <h5 class="">
                            <?php if ($userLang == 'ko'): ?>
                                <?= $translations['txt_phone_number'] ?>
                            <?php else: ?>
                                <?= $translations['txt_email_address'] ?>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <?php if ($userLang == 'ko'): ?>
                        <input type="tel" class="form-control" placeholder="<?= $translations['txt_phone_placeholder'] ?>" id="phoneNumber" name="phoneNumber" maxlength="13" oninput="restrictInput(this);formatPhoneNumber(this);">
                    <?php else: ?>
                        <input type="email" class="form-control" placeholder="example@domain.com" id="email" name="email" value="<?= $mt_email ?>">
                    <?php endif; ?>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> <?= $translations['txt_confirmed'] ?></p>
                    </div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i>
                        <?php if ($userLang == 'ko'): ?>
                            <?= $translations['txt_check_phone_number'] ?>
                        <?php else: ?>
                            <?= $translations['txt_check_email_address'] ?>
                        <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="checkInput()"><?= $translations['txt_input_complete'] ?></button>
            </div>
        </form>
    </div>
</div>

<script>
    // 입력 확인
    var inputCheck = false;

    function restrictInput(element) {
        // 숫자와 하이픈만 허용
        element.value = element.value.replace(/[^0-9-]/g, '');

        // 중복된 하이픈 제거
        element.value = element.value.replace(/-{2,}/g, '-');
    }

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

    function checkInput() {
        <?php if ($userLang == 'ko'): ?>
            let phoneNumber = $("#phoneNumber").val();

            // 휴대폰 번호가 010으로 시작하고, 총 11자인지 확인
            var regex = /^(010)-?(\d{4})-?(\d{4})$/;
            if (regex.test(phoneNumber)) {
                $(".ip_wr").addClass("ip_valid");
                $(".ip_wr").removeClass("ip_invalid");
                inputCheck = true;
            } else {
                $(".ip_wr").addClass("ip_invalid");
                $(".ip_wr").removeClass("ip_valid");
                inputCheck = false;
            }
            if (inputCheck === true) {
                $.ajax({
                    url: "./join_update",
                    type: "POST",
                    data: {
                        act: "check_hp",
                        mt_hp: phoneNumber,
                    },
                    dataType: "json",
                    success: function(d, s) {
                        console.log(d);
                        if (d.result == "login") {
                            window.location.replace('./login?phoneNumber=' + phoneNumber);
                        } else {
                            window.location.replace('./join_verify?phoneNumber=' + d.mt_hp);
                        }
                    },
                });
            }
        <?php else: ?>
            let email = $("#email").val();
            var regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; // 이메일 유효성 검사 정규식
            if (regex.test(email)) {
                $(".ip_wr").addClass("ip_valid");
                $(".ip_wr").removeClass("ip_invalid");
                inputCheck = true;
            } else {
                $(".ip_wr").addClass("ip_invalid");
                $(".ip_wr").removeClass("ip_valid");
                inputCheck = false;
            }
            if (inputCheck === true) {
                $.ajax({
                    url: "./join_update",
                    type: "POST",
                    data: {
                        act: "check_email", // 이메일 확인 액션
                        mt_email: email,
                    },
                    dataType: "json",
                    success: function(d, s) {
                        console.log(d);
                        if (d.result == "login") {
                            window.location.replace('./login?email=' + email);
                        } else {
                            window.location.replace('./join_psd?mt_email=' + d.mt_email + '&mt_gender=' + '<?= $mt_gender ?>' + '&pick_year=' + '<?= $pick_year ?>' + '&pick_month=' + '<?= $pick_month ?>' + '&pick_day=' + '<?= $pick_day ?>' + '&mt_name=' + '<?= $mt_name ?>');
                        }
                    },
                });
            }
        <?php endif; ?>

    }
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>