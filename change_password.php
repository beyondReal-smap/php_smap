<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '6';
$h_url = './setting_list';

$_SUB_HEAD_TITLE = $translations['txt_change_password']; 
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert($translations['txt_login_required'], './login', ''); 
}
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
        <p class="tit_h1 wh_pre line_h1_3"><?=$translations['txt_change_password'] ?></p>
        <form method="post" name="frm_form" id="frm_form" action="./setting_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="change_password" />
            <div class="mt-5">
                <div class="ip_wr">
                    <div class="ip_tit">
                        <h5><?=$translations['txt_password'] ?></h5> 
                    </div>
                    <input type="password" name="mt_pass" id="mt_pass" class="form-control" maxlength="20" placeholder="<?=$translations['txt_enter_password'] ?>" onkeyup="f_isValid()"> 
                    <div class="form_arm_text fs_12 fc_gray_600 mt-3 px-4 line_h1_2"><?=$translations['txt_password_requirements'] ?></div> 
                </div>
                <div class="ip_wr mt_25 ip_invalid">
                    <div class="ip_tit">
                        <h5><?=$translations['txt_check_phone_number'] ?></h5> 
                    </div>
                    <input type="password" name="mt_pass_confirm" id="mt_pass_confirm" class="form-control" maxlength="20" placeholder="<?=$translations['txt_check_phone_number'] ?>" onkeyup="f_isValid()"> 
                </div>
            </div>
            <div class="b_botton">
                <button type="submit" class="btn rounded btn-primary btn-lg btn-block" disabled><?=$translations['txt_done'] ?></button> 
            </div>
            <div id="layoutViewport"></div>
        </form>
        <script>
            function f_isValid() {
                let passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@#$!%*?&])[A-Za-z\d@#$!%*?&]{9,}$/;
                let isValidPassword = passwordRegex.test($('#mt_pass').val());
                let isValidPassword2 = passwordRegex.test($('#mt_pass_confirm').val());
                if (isValidPassword && isValidPassword2) {
                    $('#frm_form button[type="submit"]').prop('disabled', false);
                } else {
                    $('#frm_form button[type="submit"]').prop('disabled', true);
                }
            }

            $(document).ready(function() {
                f_isValid();

                $('#mt_pass_confirm').on('change', function() {
                    f_isValid();
                });
            });

            $.validator.addMethod("regex", function(value, element, regexpr) {
                return regexpr.test(value);
            }, "<?=$translations['txt_password_requirements'] ?>"); 

            $("#frm_form").validate({
                submitHandler: function() {
                    // $('#splinner_modal').modal('toggle');

                    return true;
                },
                rules: {
                    mt_pass: {
                        required: true,
                        minlength: 9,
                        regex: /^(?=[a-zA-Z0-9!@#$^]*$)(?!.*[^a-zA-Z0-9!@#$^])/i
                    },
                    mt_pass_confirm: {
                        required: true,
                        equalTo: mt_pass
                    },
                },
                messages: {
                    mt_pass: {
                        required: "<?=$translations['txt_enter_password'] ?>", 
                        minlength: "<?=$translations['txt_password_requirements'] ?>", 
                    },
                    mt_pass_confirm: {
                        required: "<?=$translations['txt_check_phone_number'] ?>", 
                        equalTo: "<?=$translations['txt_password_mismatch'] ?>" 
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
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>