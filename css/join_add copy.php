<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "";
$_GET['hd_num'] = '2';
$h_menu = '2';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    gotourl('./logout');
}
?>


<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3">회원 정보를 입력해 주세요</p>
        <form method="post" name="frm_form" id="frm_form" action="./join_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="join_add_info" />
            <input type="hidden" id="mt_idx" name="mt_idx" value="<?= $_SESSION['_mt_idx'] ?>">
            <div class="mt-5">
                <div class="ip_wr mt_25" id="mt_name_text">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5 class="">이름</h5>
                        <p class="text_num fs_12 fc_gray_600">(<span id="mt_name_cnt">0</span>/30)</p>
                    </div>
                    <input type="text" class="form-control txt-cnt" name="mt_name" id="mt_name" maxlength="30" data-length-id="mt_name_cnt" placeholder="이름을 입력해주세요">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                </div>
                <div class="ip_wr mt_25" id="mt_birth_text">
                    <div class="ip_tit">
                        <h5 class="">생년월일</h5>
                    </div>
                    <input type="date" class="form-control" name="mt_birth" id="mt_birth" maxlength="10" placeholder="생년월일 8자리를 입력해주세요.">
                    <div class=" form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                </div>
                <div class="ip_wr mt_25" id="mt_gender_text">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5>성별</h5>
                    </div>
                    <select class="form-control custom-select" id="mt_gender" name="mt_gender">
                        <option selected>성별</option>
                        <option value="1">남자</option>
                        <option value="2">여자</option>
                    </select>
                </div>
            </div>
            <div class="b_botton">
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block">입력했어요!</button>
                <!-- <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='join_agree.php'">입력했어요!</button> -->
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(document).on("keyup", "input.txt-cnt", function() {
            var cnt_id = $(this).data('length-id');
            $('#' + cnt_id).text($(this).val().length);
        });
    });

    $("#frm_form").validate({
        submitHandler: function() {
            $('#splinner_modal').modal('toggle');

            return true;
        },
        rules: {
            mt_name: {
                required: true,
            },
            mt_birth: {
                required: true,
            },
        },
        messages: {
            mt_name: {
                required: "이름을 입력해주세요.",
            },
            mt_birth: {
                required: "생년월일을 선택해주세요.",
            },
        },
        errorPlacement: function(error, element) {
            $(element)
                .closest("form")
                .find("span[for='" + element.attr("id") + "']")
                .append(error);
        },
    });
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>