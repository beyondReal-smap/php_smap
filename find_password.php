<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '';
$h_menu = '2';
$_SUB_HEAD_TITLE = "";
include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";
?>
<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3"><?= $translations['txt_forgot_password'] ?></p>
        <form action="">
            <div class="mt-5">
                <div class="ip_wr">
                    <div class="ip_tit">
                        <h5><?= $translations['txt_password'] ?></h5>
                    </div>
                    <input type="password" class="form-control" placeholder="<?= $translations['txt_enter_password'] ?>">
                    <div class="form_arm_text fs_12 fc_gray_600 mt-3 px-4 line_h1_2"><?= $translations['txt_password_requirements'] ?></div>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> <?= $translations['txt_password_confirmed'] ?></div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= $translations['txt_check_password'] ?></div>
                </div>
                <div class="ip_wr mt_25 ip_invalid">
                    <div class="ip_tit">
                        <h5><?= $translations['txt_password'] ?> <?= $translations['txt_confirm'] ?></h5>
                    </div>
                    <input type="password" class="form-control" placeholder="<?= $translations['txt_enter_password_again'] ?>">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> <?= $translations['txt_password_confirmed'] ?></div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= $translations['txt_check_password'] ?></div>
                </div>
            </div>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block " onclick="location.href='form_add_info'"><?= $translations['txt_input_complete'] ?></button>
            </div>
        </form>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>