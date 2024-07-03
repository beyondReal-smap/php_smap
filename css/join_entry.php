<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "";
$_GET['hd_num'] = '';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>


<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3 test_dynamic">SMAP과 함께라면,
            소중한 사람들과의 거리가
            가까워 집니다</p>
        <p class="fc_gray_600 test_dynamic mt-3">위치 기반 일정 관리로 더 빠르고 정확하게 소통해요.</p>
        <form action="" onkeypress="return event.keyCode != 13;">
            <div class="mt-5">
                <div class="ip_wr mt-5">
                    <div class="ip_tit">
                        <h5 class="">휴대전화번호</h5>
                    </div>
                    <input type="tel" class="form-control" placeholder="010-0000-0000" id="phoneNumber" name="phoneNumber" maxlength="13" oninput="restrictInput(this);formatPhoneNumber(this);">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 휴대전화번호를 다시 확인해주세요</div>
                </div>
            </div>
            <div class="mt-5">
                <!--<button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='join_verify.php'">입력했어요!</button>-->
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="check_hp()">입력했어요!</button>
            </div>
        </form>
    </div>
</div>

<script>
    //휴대전화번호 입력 확인
    var phoneCheck = false;

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


    function check_hp() {
        let phoneNumber = $("#phoneNumber").val();

        // 휴대폰 번호가 010으로 시작하고, 총 11자리인지 확인
        var regex = /^(010)-?(\d{4})-?(\d{4})$/;
        if (regex.test(phoneNumber)) {
            $(".ip_wr").addClass("ip_valid");
            $(".ip_wr").removeClass("ip_invalid");
            phoneCheck = true;
        } else {
            $(".ip_wr").addClass("ip_invalid");
            $(".ip_wr").removeClass("ip_valid");
            phoneCheck = false;
        }
        if (phoneCheck === true) {
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

    }
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>