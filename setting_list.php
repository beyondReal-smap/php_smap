<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '6';
$h_url = './setting';
$_SUB_HEAD_TITLE = "계정설정";
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
<div class="container sub_pg px-0 bg_main">
    <div>
        <form action="">
            <div class="pt-5 pb_20 px_16 bg-white">
                <?php
                //프로필 사진 등록/수정
                include $_SERVER['DOCUMENT_ROOT'] . "/profile.inc.php";
                ?>
                <div class="mb-3 mr-2">
                    <div class="d-flex align-items-center justify-content-center flex-wrap ">
                        <a class="fs_15 fw_600 line_h1_3 text-center text_dynamic mt-2 mr-2"><?= $_SESSION['_mt_nickname'] ?></a>
                        <p class="fs_13 fc_mian_sec fw_600 line_h1_3 text-center text_dynamic mt-2"><?= $_SESSION['_mt_name'] ?></p>
                    </div>
                    <p class="fs_14 text_light_gray fw_500 text-center text_dynamic mt-2"><?= format_phone($_SESSION['_mt_id']) ?></p>
                </div>
            </div>
        </form>
        <div class=" py_16">
            <div class="px_16">
                <div class="border rounded-lg py_16 bg-white mb-3">
                    <a href="./setting_modify" class="d-flex align-items-center justify-content-between px_16 py_16">
                        <p class="fs_16 fw_600">기본 정보 수정</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./current_password" class="d-flex align-items-center justify-content-between px_16 py_16">
                        <p class="fs_16 fw_600">비밀번호 변경</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a class="d-flex align-items-center justify-content-between cursor_pointer px_16 py_16" data-toggle="modal" data-target="#logout_modal">
                        <p class="fs_16 fw_600">로그아웃</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                    <a href="./withdraw" class="d-flex align-items-center justify-content-between px_16 py_16">
                        <p class="fs_16 text_gray fw_600">회원탈퇴</p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- H-5 로그아웃 -->
<div class="modal fade" id="logout_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">로그아웃 하시겠습니까?</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="location.href='./logout'">로그아웃</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>