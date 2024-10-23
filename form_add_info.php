<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '';
$h_menu = '7';
$h_func = "f_back_chk('form_add_info');";
$_SUB_HEAD_TITLE = "";

if($_SESSION['_mt_idx'] == '') {
    gotourl('./logout');
}

include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";
?>
<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3"><?= $translations['txt_enter_additional_info'] ?></p>
        <form method="post" name="frm_form" id="frm_form" action="./form_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="form_add_info" />
            <div class="mt-5">
                <?php
                    //프로필 사진 등록/수정
                    include $_SERVER['DOCUMENT_ROOT']."/profile.inc.php";
?>
                <div class="ip_wr mt_25 ip_valid">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5 class=""><?= $translations['txt_name'] ?> <b class="text-danger">*</b></h5>
                        <p class="text_num fs_12 fc_gray_600">(<span id="mt_name_cnt">0</span>/30)</p>
                    </div>
                    <input type="text" class="form-control txt-cnt" name="mt_name" id="mt_name" maxlength="30" data-length-id="mt_name_cnt" placeholder="<?= $translations['txt_enter_name'] ?>">
                </div>
                <div class="ip_wr mt_25 ip_invalid">
                    <div class="ip_tit">
                        <h5 class=""><?= $translations['txt_phone_number'] ?></h5>
                    </div>
                    <input type="number" class="form-control" name="mt_hp" id="mt_hp" numberOnly minlength="2" maxlength="20" placeholder="<?= $translations['txt_enter_phone_number'] ?>">
                </div>
                <div class="ip_wr mt_25">
                    <div class="ip_tit">
                        <h5 class=""><?= $translations['txt_date_of_birth'] ?></h5>
                    </div>
                    <input type="date" class="form-control" name="mt_birth" id="mt_birth" maxlength="10" placeholder="<?= $translations['txt_enter_date_of_birth'] ?>">
                </div>
                <div class="ip_wr mt_25">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5><?= $translations['txt_gender'] ?></h5>
                    </div>
                    <select class="form-control custom-select" name="mt_gender" id="mt_gender">
                        <option value=""><?= $translations['txt_please_select'] ?></option>
                        <option value="1"><?= $translations['txt_male'] ?></option>
                        <option value="2"><?= $translations['txt_female'] ?></option>
                    </select>
                </div>
                <div class="ip_wr mt_25">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5 class=""><?= $translations['txt_nickname'] ?> <b class="text-danger">*</b></h5>
                        <p class="text_num fs_12 fc_gray_600">(<span id="mt_nickname_cnt">0</span>/12)</p>
                    </div>
                    <input type="text" class="form-control txt-cnt" name="mt_nickname" id="mt_nickname" minlength="2" maxlength="12" data-length-id="mt_nickname_cnt" placeholder="<?= $translations['txt_enter_nickname'] ?>">
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <div>
                            <div class="form_arm_text fs_13 fw_600 fc_gray_600 px-4 line_h1_2"><?= $translations['txt_nickname_input_rule'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom_btn_flex_end_wrap" style="height: 120px;">
                <div class="bottom_btn_flex_end_box">
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block"><?= $translations['txt_input_complete'] ?></button>
                </div>
            </div>
        </form>
        <script>
        $(document).ready(function() {
            $(document).on("keyup", "input.txt-cnt", function() {
                var cnt_id = $(this).data('length-id');
                $('#' + cnt_id).text($(this).val().length);
            });
        });

        $.validator.addMethod("mt_nick_chk", function(value, element) {
            var rtn = false;

            $.ajax({
                url: './form_update',
                data: {
                    act: 'chk_mt_nick',
                    mt_nickname: value
                },
                type: 'POST',
                async: false,
                success: function(args) {
                    args = $.trim(args);
                    rtn = (args === 'true');
                }
            });

            return rtn;
        });

        $("#frm_form").validate({
            submitHandler: function() {
                // $('#splinner_modal').modal('toggle');

                return true;
            },
            rules: {
                mt_name: {
                    required: true,
                },
                mt_nickname: {
                    required: true,
                    mt_nick_chk: true,
                },
            },
            messages: {
                mt_name: {
                    required: "<?= $translations['txt_enter_name_message'] ?>",
                },
                mt_nickname: {
                    required: "<?= $translations['txt_enter_nickname_message'] ?>",
                    mt_nick_chk: "<?= $translations['txt_duplicate_nickname_message'] ?>",
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
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>