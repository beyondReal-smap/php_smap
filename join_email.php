<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "";
$h_menu = '7';
// $h_func = "location.replace('./join_verify?phoneNumber=" . $_GET['phoneNumber'] . "')";
$h_func = "back_confirm()";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>
<style>
    .hidden {
        display: none !important;
    }

    #wrap {
        min-height: 100vh;
        height: 100vh;
        position: relative;
        overflow-y: auto;
    }

    #layoutViewport {
        position: fixed;
        width: 100%;
        height: 100%;
        visibility: hidden;
        background: #FAF2CE;
    }
</style>
<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3 text_dynamic"><?= $translations['txt_enter_email_address'] ?></p>
        <!-- <p class="fs_12 fc_gray_600 mt-3 line_h1_2">이메일 주소를 입력해주세요.</p> -->
        <form action="" onkeypress="return event.keyCode != 13;">
            <input type="hidden" name="HTTP_REFERER" id="HTTP_REFERER" value="<?= $_SERVER["HTTP_REFERER"] ?>">
            <input type="hidden" name="mt_hp" id="mt_hp" value="<?= $_GET['phoneNumber'] ?>">
            <input type="hidden" name="mt_id_chk" id="mt_id_chk" value="0">
            <div class="mt-5">
                <div class="ip_wr">
                    <div class="ip_tit">
                        <h5 class=""><?= $translations['txt_email'] ?></h5>
                    </div>
                    <input type="email" class="form-control" placeholder="example@domain.com" id="mt_email" name="mt_email" maxlength="100" value="<?= $_GET['mtEmail'] ?>">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> <?= $translations['txt_confirmed'] ?></div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= $translations['txt_enter_email_address'] ?></div>
                    <div class="form-text ip_invalid2"><i class="xi-error-o"></i> <?= $translations['txt_email_already_in_use'] ?></div>
                </div>
            </div>
            <div class="b_botton">
                <!-- 이메일 중복일 경우 data-toggle="modal" data-target="#dpl_email" 버튼태기에 넣어주세요 -->
                <!--<button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='join_psd.php'"><?= $translations['txt_input_complete'] ?></button>-->
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" id="mt_id_chk_button" onclick="check_id()"><?= $translations['txt_input_complete'] ?></button>
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block d-none" id="next_button" onclick="next_page()"><?= $translations['txt_input_complete'] ?></button>
            </div>
            <div id="layoutViewport"></div>
        </form>
    </div>
</div>

<!-- 존재하는 메일주소 -->
<div class="modal fade" id="dpl_email" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center"><?= $translations['txt_email_already_in_use'] ?></p>
            </div>
            <div class="modal-footer px-0 py-0">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" data-dismiss="modal" aria-label="Close"><?= $translations['txt_confirm'] ?></button>
            </div>
        </div>
    </div>
</div>
<!-- 뒤로가기 클릭 시 -->
<div class="modal fade" id="back_confirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center"><?= $translations['txt_leave_without_completing'] ?></p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" onclick="location.replace('./join_entry')"><?= $translations['txt_yes'] ?></button>
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" data-dismiss="modal" aria-label="Close"><?= $translations['txt_no'] ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function check_id() {
        let mt_email = $("#mt_email").val();
        let mt_hp = $("#mt_hp").val();

        // 이메일 형식 체크
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        let isValidEmail = emailRegex.test(mt_email);

        // 이메일 형식이 유효한 경우
        if (isValidEmail) {
            $.ajax({
                url: 'join_update', // 서버 URL 설정
                type: 'POST',
                data: {
                    act: 'chk_mt_id',
                    mt_email: mt_email
                }, // 서버에 전송할 데이터
                success: function(response) {
                    // 서버에서 전송한 JSON 응답 파싱
                    let isDuplicate = JSON.parse(response);
                    console.log(isDuplicate);
                    if (isDuplicate) {
                        // 중복된 이메일이 없는 경우 여기에 추가 로직을 작성할 수 있습니다.
                        $("#mt_id_chk").val("1");
                        $(".ip_wr").addClass("ip_valid");
                        $(".ip_wr").removeClass("ip_invalid");
                        $(".ip_wr").removeClass("ip_invalid2");
                        // 성공일 때 버튼 표출
                        $("#mt_id_chk_button").addClass("d-none");
                        $("#next_button").removeClass("d-none");

                        //바로이동
                        window.location.replace('./join_psd?phoneNumber=' + mt_hp + '&mtEmail=' + mt_email);
                    } else {
                        // 중복된 이메일이 존재하는 경우 모달 표시
                        $('#dpl_email').modal('show');
                        $("#mt_id_chk").val("0");
                        $(".ip_wr").addClass("ip_invalid2");
                        $(".ip_wr").removeClass("ip_invalid");
                        $(".ip_wr").removeClass("ip_valid");

                        // 버튼 표출
                        $("#next_button").addClass("d-none");
                        $("#mt_id_chk_button").removeClass("d-none");
                    }
                },
                error: function(error) {
                    console.error('서버 통신 오류:', error);
                }
            });
        } else {
            // 이메일 형식이 유효하지 않은 경우 메시지 표시
            $(".ip_wr").addClass("ip_invalid");
            $(".ip_wr").removeClass("ip_valid");
            $(".ip_wr").removeClass("ip_invalid2");
            $("#mt_id_chk").val("0");
            // 버튼 표출
            $("#next_button").addClass("d-none");
            $("#mt_id_chk_button").removeClass("d-none");
        }
    }

    function next_page() {
        var mt_hp = $("#mt_hp").val();
        var mt_email = $("#mt_email").val();
        var mt_id_chk = $("#mt_id_chk").val();
        if (mt_id_chk == '1' && mt_hp && mt_email) {
            window.location.replace('./join_psd?phoneNumber=' + mt_hp + '&mtEmail=' + mt_email);
        }
    }

    function back_confirm() {

        // 중복된 이메일이 존재하는 경우 모달 표시
        $('#back_confirm').modal('show');
    }
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>