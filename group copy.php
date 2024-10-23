<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '2';
$h_menu = '5';
$_SUB_HEAD_TITLE = "그룹";
include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";

if($_SESSION['_mt_idx'] == '') {
    alert($translations['txt_login_required'], './login', '');
}

//오너인 그룹수
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgt_show', 'Y');
$row = $DB->getone('smap_group_t', 'count(*) as cnt');
$sgt_cnt = $row['cnt'];

//초대된 그룹수
// $DB->where('mt_idx', $_SESSION['_mt_idx']);
// $DB->where('sgdt_show', 'Y');
// $row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
// $sgdt_cnt = $row['cnt'];

$sgdt_cnt = 0;

//나의 걸음수
$row = get_member_location_log_t_info();
$my_working_cnt = $row['mt_health_work'];
?>
<style>
.top_btn_wr.b_on.active {
    bottom: 14rem
}
</style>
<div class="container sub_pg bg_main">
    <div class="mt_20">
        <?php
if($sgt_cnt < 1 && $sgdt_cnt < 1) {
    ?>
        <!-- 내용 없을 때 박스 -->
        <div class="border rounded-lg px_16 py_16 none_box mb-3">
            <div class="text-center">
                <p class="fs_14 text_gray text_dynamic">그룹을 생성해주세요!</p>
                <button type="button" class="btn btn-secondary btn-sm fc_primary pl_14 pr_11 mt_12 mx-auto" onclick="location.href='group_create'">그룹 생성 하러가기<i class="xi-angle-right-min ml_19"></i></button>
            </div>
        </div>
        <?php
} else {
    ?>
        <div class="fixed_top bg_main">
            <div class="py_20 px_16">
                <div class="group_mem bg_main d-flex align-items-center justify-content-between">
                    <div class="w_fit">
                        <a href="#" class="d-flex align-items-center">
                            <div class="prd_img flex-shrink-0 mr_12 mine">
                                <div class="rect_square rounded_14">
                                    <img src="<?=$_SESSION['_mt_file1']?>" onerror="this.src='<?=$ct_no_profile_img_url?>'" alt="프로필이미지" />
                                </div>
                            </div>
                            <div>
                                <p class="fs_14 fw_500 text_dynamic mr-2">나</p>
                                <div class="d-flex align-items-center flex-wrap">
                                    <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1"></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <p class="fs_14 fw_500 text_gray"><?=number_format($my_working_cnt)?> <span>걸음</span></p>
                </div>
            </div>
            <div class="bar_fluid"></div>
        </div>

        <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
            <input type="hidden" name="act" id="act" value="list" />
            <input type="hidden" name="obj_list" id="obj_list" value="group_list_box" />
            <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
            <input type="hidden" name="obj_uri" id="obj_uri" value="./group_update" />
            <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
            <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
            <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
        </form>

        <script>
        $(document).ready(function() {
            f_get_box_list();
        });
        </script>

        <div id="group_list_box"></div>
        <?php
}
?>
    </div>
    <?php
if($sgt_cnt > 0) {
    ?>
    <button type="button" class="btn w-100 floating_btn rounded" onclick="location.href='group_create'"><i class="xi-plus-min mr-3"></i> 그룹 추가하기</button>
    <?php
}
?>
</div>

<!-- E-13 멤버 초대 -->
<div class="modal btn_sheeet_wrap fade" id="link_modal" tabindex="-1">
    <div class="modal-dialog btm_sheet">
        <div class="modal-content">
            <div class="modal-header border-0">
                <div class="d-inline-block w-100 text-right">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?=CDN_HTTP?>/img/modal_close.png" width="24px"></button>
                </div>
                <p class="fs_18 fw_700 text_dynamic line_h1_2">초대장은 어떻게 보낼까요?</p>
            </div>
            <div class="modal-body">
                <ul>
                    <li>
                        <a href="#" class="d-flex align-items-center justify-content-between py_07">
                            <div class="d-flex align-items-center">
                                <img src="<?=CDN_HTTP?>/img/ico_kakao.png" alt="카카오톡 열기" width="40px" class="mr_12" />
                                <p class="fs_15 fw_500 gray_900">카카오톡 열기</p>
                            </div>
                            <i class="xi-angle-right-min fs_15 text_gray"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="d-flex align-items-center justify-content-between py_07">
                            <div class="d-flex align-items-center">
                                <img src="<?=CDN_HTTP?>/img/ico_link.png" alt="초대 링크 복사" width="40px" class="mr_12" />
                                <p class="fs_15 fw_500 gray_900">초대 링크 복사</p>
                            </div>
                            <i class="xi-angle-right-min fs_15 text_gray"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="d-flex align-items-center justify-content-between py_07">
                            <div class="d-flex align-items-center">
                                <img src="<?=CDN_HTTP?>/img/ico_address.png" alt="연락처 열기" width="40px" class="mr_12" />
                                <p class="fs_15 fw_500 gray_900">연락처 열기</p>
                            </div>
                            <i class="xi-angle-right-min fs_15 text_gray"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>