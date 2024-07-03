<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '4';
$h_menu = '5';
$$location_page = '1';
$_SUB_HEAD_TITLE = "내장소";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/b_menu.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert('다른기기에서 로그인 시도 하였습니다.\n 다시 로그인 부탁드립니다.', './logout');
    }
}

if ($_GET['sdate'] == '') {
    $_GET['sdate'] = date('Y-m-d');
}

// $DB->where('mt_idx', $_SESSION['_mt_idx']);
// $DB->orderBy("slmt_idx", "desc");
// $row_slmt = $DB->getone('smap_location_member_t');

// if ($_GET['sgdt_mt_idx']) {
//     $row_slmt['sgdt_mt_idx'] = $_GET['sgdt_mt_idx'];
// }

// $mt_location_info = get_member_location_log_t_info($row_slmt['sgdt_mt_idx']);
// $mt_info = get_member_t_info($row_slmt['sgdt_mt_idx']);

// $m_mt_lat = $mt_location_info['mlt_lat'];
// $m_mt_long = $mt_location_info['mlt_long'];
// $mt_file1_url = get_image_url($mt_info['mt_file1']);

$sgt_cnt = f_get_owner_cnt($_SESSION['_mt_idx']); //오너인 그룹수
$sgdt_leader_cnt = f_get_leader_cnt($_SESSION['_mt_idx']); //리더인 그룹수
$sgdt_cnt = f_group_invite_cnt($_SESSION['_mt_idx']); //초대된 그룹수
$sgt_row = f_group_info($_SESSION['_mt_idx']); // 그룹생성여부

$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgt_idx', $sgt_row['sgt_idx']);
$DB->where('sgdt_show', 'Y');
$DB->where('sgdt_exit', 'N');
$DB->where('sgdt_owner_chk', 'Y');
$DB->where('sgdt_discharge', 'N');
$sgdt_row = $DB->getone('smap_group_detail_t');
if (!$sgdt_row['sgdt_idx']) {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_exit', 'N');
    $DB->where('sgdt_owner_chk', 'N');
    $DB->where('sgdt_discharge', 'N');
    $sgdt_row = $DB->getone('smap_group_detail_t');
}
$member_info_row = get_member_t_info($_SESSION['_mt_idx']);

//오너제외한 그룹원 수
$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgt_show', 'Y');
$row_sgt = $DB->getone('smap_group_t', 'sgt_idx');

$DB->where('sgt_idx', $row_sgt['sgt_idx']);
$DB->where('sgdt_owner_chk', 'N');
$DB->where('sgdt_show', 'Y');
$DB->where('sgdt_discharge', 'N');
$DB->where('sgdt_exit', 'N');
$row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
$expt_cnt = $row['cnt'];

?>
<style>
    html {
        height: 100%;
        overflow-y: unset !important;
    }

    .sub_pg {
        position: fixed;
        top: 0;
        left: 50%;
        width: 100% !important;
        height: 100% !important;
        min-height: 100%;
        max-width: 50rem;
        transform: translateX(-50%);
    }

    #wrap {
        min-height: 100vh;
        height: 100vh;
        position: relative;
        overflow-y: auto;
    }
</style>
<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?= NCPCLIENTID ?>&submodules=geocoder&callback=CALLBACK_FUNCTION"></script>
<div class="container sub_pg px-0 py-0">
    <section class="pg_map_wrap">
        <input type="hidden" name="sst_location_title" id="sst_location_title" value="" />
        <input type="hidden" name="sst_location_add" id="sst_location_add" value="" />
        <input type="hidden" name="sst_location_lat" id="sst_location_lat" value="" />
        <input type="hidden" name="sst_location_long" id="sst_location_long" value="" />
        <input type="hidden" id="slt_idx" name="slt_idx" value="">
        <div class="pg_map_inner" id="map">
        </div>
        <div class="pg_map_inner" id="map_info_box">
            <div class="flt_map_wrap">
                <!-- 배너-->
                <div class="banner locationpg_banner">
                    <div class="banner_inner">
                        <!-- Swiper -->
                        <div class="swiper banSwiper">
                            <div class="swiper-wrapper">
                                <?
                                $DB->where('bt_type', 2);
                                $DB->where('bt_show', 'Y');
                                $DB->orderby('bt_rank', 'asc');
                                $banner_list = $DB->get('banner_t');
                                if ($banner_list) {
                                    foreach ($banner_list as $bt_row) {
                                ?>
                                        <div class="swiper-slide">
                                            <div class="bner_txt">
                                                <p class="text-primary fs_13 mr-2"><i class="xi-info"></i></p>
                                                <p class="text_dynamic fs_12 fw_500 line_h1_3"><?= $bt_row['bt_title'] ?></p>
                                            </div>
                                            <div class="">
                                                <div class="rect_bner">
                                                    <img src="<?= $ct_img_url . '/' . $bt_row['bt_file'] ?>" alt="배너이미지" onerror="this.src='<?= $ct_no_img_url ?>'" />
                                                </div>
                                            </div>
                                        </div> <?
                                            }
                                        }
                                                ?>
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
                <div class="flt_map_pin_wrap bg-white rounded_10 ">
                    <div class="pin_cont pt_20 px_16 pb_16">
                        <ul>
                            <li>
                                <div class="address_btn" onclick="f_modal_map_search();">
                                    <p class=" fc_gray_700"><span class="pr-3"><img src="./img/ico_search.png" width="14px" alt="검색" /></span> 지번, 도로명, 건물명으로 검색</p>
                                </div>
                            </li>
                            <li class="d-flex">
                                <div class="name flex-fill">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fs_12 fw_600 text-primary">선택한 위치</span>
                                        <!-- <a class="fc_gray_900 fs_12 fw_500" href="javascript:f_modal_map_search();">주소검색하기 <i class="xi-angle-right-min"></i></a> -->
                                    </div>
                                    <div class="fs_14 fw_600 fc_gray_600 text_dynamic mt-2 line_h1_3" id="location_add" name="location_add">위치를 선택해주세요</div>
                                </div>
                            </li>
                            <!-- .loc_nickname_wp에 .on추가하면 나타탑니다. -->
                            <li class="mt-3 loc_nickname_wp">
                                <div class="name d-flex flex-fill flex-column">
                                    <labe class="fs_12 fw_600 text-primary">별칭</labe>
                                    <input class="fs_14 fw_600 fc_gray_600 form-control text_dynamic mt-1 line_h1_3 loc_nickname" name="slt_title" id="slt_title" value="" placeholder="별칭을 입력해주세요" style="word-break: break-all;">
                                </div>
                            </li>
                        </ul>
                    </div>
                    <!-- .myplace_btn에 .on추가하면 버튼이 나타납니다. -->
                    <div class="d-flex align-items-center myplace_btn_wr w-100 mx-0 my-0">
                        <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0 myplace_btn flt_close">닫기</button>
                        <!-- <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0 myplace_btn">내장소 저장 횟수 초과</button> -->
                        <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0 myplace_btn" onclick="location_add()">내장소 등록</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- G-2 위치 페이지 [시작]-->
    <?php
    if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) {
        // $translateY = 0;
        $translateY = 24;
    } else {
        $translateY = 0;
    }
    ?>
    <section class="opt_bottom" style="transform: translateY(<?= $translateY ?>%);">
        <div class="top_bar_wrap text-center pt_08">
            <?php if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) { ?>
                <img src="./img/top_bar.png" class="top_bar" width="34px" alt="탑바" />
                <img src="./img/btn_tl_arrow.png" class="top_down mx-auto" width="12px" alt="탑업" />
            <?php } ?>
        </div>
        <div>
            <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
                <input type="hidden" name="act" id="act" value="location_map_list" />
                <input type="hidden" name="obj_list" id="obj_list" value="location_map_list_box" />
                <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                <input type="hidden" name="obj_uri" id="obj_uri" value="./location_update" />
                <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                <input type="hidden" name="sgt_idx" id="sgt_idx" value="<?= $sgdt_row['sgt_idx'] ?>" />
                <input type="hidden" name="sgdt_idx" id="sgdt_idx" value="<?= $sgdt_row['sgdt_idx'] ?>" />
                <input type="hidden" name="mt_idx" id="mt_idx" value="<?= $sgdt_row['mt_idx'] ?>" />
            </form>
            <div id="location_map_list_box">
                <div class="px_16" style="margin-bottom: 12px;">
                    <div class="border bg-white rounded-lg px_16 py_16">
                        <p class="fs_16 fw_600 mb-3">리스트</p>
                        <div class="swiper locSwiper location_point_list_wrap pb-0">
                            <div class="swiper-wrapper lo_grid_wrap">
                                <div class="trace_box trace_add_place swiper-slide mr-3" style="height: 135px;">
                                    <div class="trace_box_txt_box text-center" style="height: 91.5px;">
                                        <p class="fs_13 fw_400 text_dynamic line_h1_4 text-center"></p>
                                    </div>
                                </div>
                                <?php if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) { ?>
                                    <div class="trace_box swiper-slide mr-3" style="height: 135px;">
                                        <div class="trace_box_txt_box" style="height: 91.5px;">
                                            <!-- <p class="mr-2">
                                                <span class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line1_text line_h1_4 mb-2">내장소 불러오는 중..</span>
                                            </p> 
                                            <p class="line2_text fs_13 fw_400 text_dynamic line_h1_4">내장소 정보 불러오는 중..</p> -->
                                        </div>
                                        <div class="trace_box_btn_box">
                                        </div>
                                    </div>
                                    <div class="trace_box trace_frt_place swiper-slide mr-3">
                                        <div class="trace_box_txt_box" style="height: 91.5px;">
                                            <!-- <p class="mr-2">
                                                <span class="fs_13 fc_d58c19 rounded_04 bg_fbf3e8 px_06 py_02 line1_text line_h1_4 mb-2">추천장소 불러오는 중..</span>
                                            </p>
                                            <p class="line2_text fs_13 fw_400 text_dynamic line_h1_4">추천장소 정보 불러오는 중..</p> -->
                                        </div>
                                        <div class="d-flex align-items-center trace_box_btn_box">
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 프로필 tab_scroll scroll_bar_x -->
            <?php if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) { ?>
                <div class="grp_wrap">
                    <div class="border bg-white rounded-lg px_16 py_16">
                        <p class="fs_16 fw_600 mb-3">그룹원</p>
                        <form method="post" name="frm_group_list" id="frm_group_list" onsubmit="return false;">
                            <input type="hidden" name="act" id="act" value="group_member_list" />
                            <input type="hidden" name="obj_list2" id="obj_list2" value="group_member_list_box" />
                            <input type="hidden" name="obj_frm2" id="obj_frm2" value="frm_group_list" />
                            <input type="hidden" name="obj_uri2" id="obj_uri2" value="./location_update" />
                            <input type="hidden" name="obj_pg2" id="obj_pg2" value="1" />
                            <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                            <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
                            <input type="hidden" name="group_sgt_idx" id="group_sgt_idx" value="<?= $sgdt_row['sgt_idx'] ?>" />
                            <input type="hidden" name="group_sgdt_idx" id="group_sgdt_idx" value="<?= $sgdt_row['sgdt_idx'] ?>" />
                        </form>
                        <style>
                            @keyframes loading {
                                0% { transform: rotate(0deg); }
                                100% { transform: rotate(360deg); }
                            }

                            .loading-animation {
                                width: 40px;
                                height: 40px;
                                border-radius: 50%;
                                border: 4px solid #f3f3f3;
                                border-top: 4px solid #3498db;
                                animation: loading 1s infinite linear;
                            }
                        </style>

                        <div id="group_member_list_box">
                            <div class="mem_wrap mem_swiper">
                                <div class="swiper-wrapper d-flex">
                                    <!-- 로딩 애니메이션 추가 -->
                                    <div id="loading-placeholder" class="d-flex align-items-center justify-content-center" style="width: 100%; height: 81px;">
                                        <div class="loading-animation"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>

    <script>
        // Swiper 초기화
        var mem_swiper = new Swiper('.mem_swiper', {
            slidesPerView: 'auto',
            spaceBetween: 12,
        });

        // 페이지 로드 시 숨겨진 요소 로딩 후 표시
        document.addEventListener("DOMContentLoaded", function() {
            // PHP에서 생성된 HTML을 삽입
            <?php
            if ($list_sgt) {
                foreach ($list_sgt as $row_sgt) {
                    $member_cnt_t = get_group_member_cnt($row_sgt['sgt_idx']);
                    unset($list_sgdt);
                    $list_sgdt = get_sgdt_member_lists($row_sgt['sgt_idx']);
                    $invite_cnt = get_group_invite_cnt($row_sgt['sgt_idx']);
                    if ($invite_cnt || $list_sgdt['data']) {
                        if ($list_sgdt['data']) {
                            foreach ($list_sgdt['data'] as $key => $val) {
            ?>
                                var slideHtml = `
                                    <div class="swiper-slide checks mem_box">
                                        <label>
                                            <input type="radio" name="rd2" <?= $val['sgdt_owner_chk'] == 'Y' ? 'checked' : '' ?> onclick="mem_schedule(<?= $val['sgdt_idx'] ?>);">
                                            <div class="prd_img mx-auto">
                                                <div class="rect_square rounded_14">
                                                    <img src="<?= $val['mt_file1_url'] ?>" alt="프로필이미지" onerror="this.src='<?= $ct_no_profile_img_url ?>'" />
                                                </div>
                                            </div>
                                            <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic"><?= $val['mt_nickname'] ? $val['mt_nickname'] : $val['mt_name'] ?></p>
                                        </label>
                                    </div>
                                `;
                                document.querySelector('.swiper-wrapper').insertAdjacentHTML('beforeend', slideHtml);
            <?php
                            }
                        }
                    }
                }
            }
            ?>
        });
    </script>
</div>
<? if ($sgt_cnt < 1 && $sgdt_cnt < 1) { ?>
    <div class="floating_wrap on">
        <div class="flt_inner">
            <div class="flt_head">
                <p class="line_h1_2"><span class="text_dynamic flt_badge">그룹만들기</span></p>
            </div>
            <div class="flt_body pb-5 d-flex align-items-start justify-content-between">
                <div>
                    <p class="text_dynamic line_h1_3 fs_17 fw_700 mt-3"><span class="text-primary">'내 장소'</span>는 여러분만의
                        맞춤 지도입니다.</p>
                    <p class="text_dynamic line_h1_3 text_gray fs_14 mt-3 fw_500">장소를 추가하면 그룹원이 해당 장소에 
                        도착하거나 떠날 때 실시간 알림으로 알려드립니다. 
                        지금 바로 그룹을 만들고 이 기능을 사용해 볼까요? 
                    </p>
                </div>
            </div>
            <div class="flt_footer">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_create'">다음</button>
            </div>
        </div>
    </div>
<? } ?>
<? if ($sgt_cnt == 1 && $expt_cnt < 1) { ?>
    <div class="floating_wrap on">
        <div class="flt_inner">
            <div class="flt_head">
                <p class="line_h1_2"><span class="text_dynamic flt_badge">그룹원 초대하기</span></p>
            </div>
            <div class="flt_body pb-5 pt-3">
                <p class="text_dynamic line_h1_3 fs_17 fw_700"><span class="text-primary">그룹원</span>들의 안전을 지키는 
                스마트한 방법!
                </p>
                <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500">SMAP-내장소에서는 그룹원들이 
                    자주 가는 장소를 등록할 수 있어요. 
                    그룹원이 해당 장소에 들어가거나 나갈 때 
                    그룹 오너에게 푸시알림을 보내드려요.</p>
            </div>
            <div class="flt_footer">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_info?sgt_idx=<?= $row_sgt['sgt_idx'] ?>'">초대하러 가기</button>
            </div>
        </div>
    </div>
<? } ?>
<!-- 토스트 Toast 토스트 넣어두었습니다. 필요하시면 사용하심됩니다.! 사용할 버튼에 id="ToastBtn" 넣으면 사용가능! -->
<div id="Toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-body">
        <p><i class="xi-check-circle mr-2"></i>위치가 등록되었습니다.</p> <!-- 성공메시지 -->
        <!-- <p><i class="xi-error mr-2"></i>에러메시지</p> -->
    </div>
</div>
<!-- G-5 알림 설정 -->
<div class="modal fade" id="arm_setting_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">위치 알림을 설정합니다.</p>
                <p class="fs_14 fw_400 text_gray mt-3 text_dynamic text-center">그룹원이 들어가거나 나올 때마다 알림을 받을 수 있어요.</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="f_alarm_location();">알림설정하기</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- G-5 알림 해제 -->
<div class="modal fade" id="arm_setting_no_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">위치 알림을 해제합니다.</p>
                <p class="fs_14 fw_400 text_gray mt-3 text_dynamic text-center">더 이상 해당 장소에 대한 알림을 받지 않게되요</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="f_alarm_location();">알림해제하기</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- G-5 위치 삭제 -->
<div class="modal fade" id="location_delete_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">위치를 삭제합니다.</p>
                <p class="fs_14 fw_400 text_gray mt-3 text_dynamic text-center">위치 삭제 시 연관된 일정도 전체 삭제됩니다.</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="f_delete_location();">삭제하기</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 주소검색부분 -->
<div class="modal fade" id="map_search" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content" id="map_search_content">
            <form method="post" name="frm_map_search" id="frm_map_search">
                <div class="modal-header">
                    <p class="modal-title line1_text fs_20 fw_700">주소 검색</p>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?= CDN_HTTP ?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y">
                    <iframe id="mapSearchFrame" frameborder="0" width="100%" height="500px"></iframe>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- 내장소 2개 입력했을 때 뜨는 모달창  -->
<div class="modal fade" id="showSub_modal" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <input type="hidden" name="pedestrian_path_modal_sgdt_idx" id="pedestrian_path_modal_sgdt_idx" value="" />
            <input type="hidden" name="path_day_count" id="path_day_count" value="" />
            <div class="modal-body text-center pb-4">
                <img src="./img/location_pin.png" width="48px" class="pt-3" alt="내장소 등록 제한" />
                <p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4">무료 회원은 4곳까지만 등록 가능합니다. 
                    Plus 구독하면 무제한 등록 가능해요.
                </p>
            </div>
            <style>
                .btn-text-large {
                font-size: 18px;
                font-weight: bold;
                display: block;
                }

                .btn-text-small {
                font-size: 12px;
                display: block;
                }
            </style>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close" onclick="location.href='./location'">
                    <span class="btn-text-large">괜찮아요</span>
                    <!-- <span class="btn-text-small">구독하지 않습니다</span> -->
                    </button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="location.href='./plan_information'">
                    <span class="btn-text-large">구독하기</span>
                    <!-- <span class="btn-text-small">구독합니다</span> -->
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var map = new naver.maps.Map("map", {
        center: new naver.maps.LatLng(<?= $_SESSION['_mt_lat'] ?>, <?= $_SESSION['_mt_long'] ?>),
        zoom: 16,
        mapTypeControl: false
    });
    var selectmarker;
    var markers = []; // 마커들을 저장할 배열

    function f_modal_map_search() {
        // location.href='./schedule_loc';
        var scheduleSearchURL = './schedule_loc';
        $('#schedule_map').modal('hide');
        setTimeout(() => {
            $('#map_search').modal('show');
            // iframe에 arm_setting 페이지 로드
            $('#mapSearchFrame').attr('src', scheduleSearchURL);
        }, 100);
    }
    // 모달을 닫는 함수
    function closelocationSearchModal() {
        $('#map_search').modal('hide');
    }
    // 주소값 받아오기
    function onlocationSearchComplete(data) {
        $('#sst_location_title').val(data.slt_title);
        $('#sst_location_add').val(data.sst_location_add);
        $('#sst_location_lat').val(data.sst_location_lat);
        $('#sst_location_long').val(data.sst_location_long);
        $('#slt_title').val(data.slt_title);
        $('#location_add').html(data.sst_location_add);

        map_panto_location(data.sst_location_lat, data.sst_location_long);

        closelocationSearchModal();
    }
    $(document).ready(function() {
        f_get_box_list2();
        f_get_box_list();
        setTimeout(() => {
            location_map(<?= $sgdt_row['sgdt_idx'] ?>);
        }, 100);

    });

    var message = {
        "type": "pagetype",
        "param": "index"
    };
    setInterval(() => {
        if (isAndroidDevice()) {
            window.smapAndroid.pagetype('index');
        } else if (isiOSDevice()) {
            window.webkit.messageHandlers.smapIos.postMessage(message);
        }
    }, 100000);

    //멤버아이콘 클릭시
    function mem_schedule(sgt_idx, sgdt_idx) {
        $('#sgt_idx').val(sgt_idx);
        $('#sgdt_idx').val(sgdt_idx);
        f_get_box_list();
        location_map(sgdt_idx);
    }
    // 내장소 추가 팝업 띄우기
    function map_info_box_show() {
        $(".flt_map_pin_wrap").addClass("on");
        $(".loc_nickname_wp").addClass("on");
        $('#map_info_box').removeClass('d-none-temp');
    }

    function initializeMap(my_profile, st_lat, st_lng, markerData) {
        var mapContainer = document.getElementById("map");
        var optBottom = document.querySelector('.opt_bottom');

        map = new naver.maps.Map(mapContainer, {
            center: new naver.maps.LatLng(st_lat, st_lng),
            zoom: 16,
            mapTypeControl: false
        });

        var bottomSheetHeight = optBottom ? optBottom.getBoundingClientRect().height : 0;
        var mapHeight = mapContainer.getBoundingClientRect().height;
        var verticalCenterOffset = (mapHeight - bottomSheetHeight) / 2 / 2;

        // 본인 프로필 마커 추가
        var profileMarkerOptions = {
            position: new naver.maps.LatLng(st_lat, st_lng),
            map: map,
            icon: {
                content: '<div class="point_wrap"><div class="map_user"><div class="map_rt_img rounded_14"><div class="rect_square"><img src="' + my_profile + '" alt="이미지" onerror="this.src=\'<?= $ct_no_img_url ?>\'"/></div></div></div></div>',
                size: new naver.maps.Size(44, 44),
                origin: new naver.maps.Point(0, 0),
                anchor: new naver.maps.Point(22, 22)
            },
            zIndex: 2
        };

        var profileMarker = new naver.maps.Marker(profileMarkerOptions);
        map.setCenter(new naver.maps.LatLng(st_lat, st_lng));
        map.panBy(new naver.maps.Point(0, verticalCenterOffset));

        // 스케줄 마커 추가
        if (markerData.schedule_chk === 'Y') {
            var positions = [];
            for (var i = 1; i <= markerData.count; i++) {
                var markerOptions = {
                    position: new naver.maps.LatLng(markerData['markerLat_' + i], markerData['markerLong_' + i]),
                    map: map,
                    icon: {
                        content: markerData['markerContent_' + i],
                        size: new naver.maps.Size(61, 61),
                        origin: new naver.maps.Point(0, 0),
                        anchor: new naver.maps.Point(30, 30)
                    }
                };

                var marker = new naver.maps.Marker(markerOptions);
                markers.push(marker); // markers 배열에 마커 추가
                positions.push(marker.getPosition());
            }
        }

        naver.maps.Event.addListener(map, 'click', function(e) {
            if (selectmarker) {
                selectmarker.setMap(null);
            }
            searchCoordinateToAddress(e.coord);
        });

        function initGeocoder() {
            map.addListener("click", function(e) {
                searchCoordinateToAddress(e.coord);
            });
            return false;
        }

        function searchCoordinateToAddress(latlng) {
            naver.maps.Service.reverseGeocode({
                    coords: latlng,
                    orders: [naver.maps.Service.OrderType.ADDR, naver.maps.Service.OrderType.ROAD_ADDR].join(","),
                },
                function(status, response) {
                    if (status === naver.maps.Service.Status.ERROR) {
                        return alert("Something Wrong!");
                    }

                    var items = response.v2.results,
                        address = "",
                        htmlAddresses = [];

                    for (var i = 0, ii = items.length, item, addrType; i < ii; i++) {
                        item = items[i];
                        address = makeAddress(item) || "";
                        addrType = item.name == "roadaddr" ? "[도로명 주소]" : "[지번 주소]";
                        htmlAddresses.push(i + 1 + ". " + addrType + " " + address);
                    }

                    if (latlng._lat && latlng._lng) {
                        htmlAddresses.push("[GPS] 위도:" + latlng._lat + ", 경도: " + latlng._lng);
                    }

                    $(".flt_map_pin_wrap").addClass("on");
                    $(".loc_nickname_wp").addClass("on");
                    $('#location_add').html(address);
                    $('#sst_location_add').val(address);
                    $('#sst_location_lat').val(latlng._lat);
                    $('#sst_location_long').val(latlng._lng);
                    $('#slt_title').val('');

                    $('#map_info_box').removeClass('d-none-temp');
                    if (selectmarker) {
                        selectmarker.setMap(null);
                    }
                    selectmarker = new naver.maps.Marker({
                        position: new naver.maps.LatLng(latlng._lat, latlng._lng),
                        map: map
                    });
                }
            );
        }

        function makeAddress(item) {
            if (!item) return;

            var name = item.name,
                region = item.region,
                land = item.land,
                isRoadAddress = name === "roadaddr";

            var sido = hasArea(region.area1) ? region.area1.name : "",
                sigugun = hasArea(region.area2) ? region.area2.name : "",
                dongmyun = hasArea(region.area3) ? region.area3.name : "",
                ri = hasArea(region.area4) ? region.area4.name : "",
                rest = "";

            if (land) {
                if (hasData(land.number1)) {
                    if (hasData(land.type) && land.type === "2") rest += "산";
                    rest += land.number1;
                    if (hasData(land.number2)) rest += "-" + land.number2;
                }

                if (isRoadAddress) {
                    if (checkLastString(dongmyun, "면")) ri = land.name;
                    else dongmyun = land.name, ri = "";
                    if (hasAddition(land.addition0)) rest += " " + land.addition0.value;
                }
            }

            return [sido, sigugun, dongmyun, ri, rest].join(" ");
        }

        function hasArea(area) {
            return !!(area && area.name && area.name !== "");
        }

        function hasData(data) {
            return !!(data && data !== "");
        }

        function checkLastString(word, lastString) {
            return new RegExp(lastString + "$").test(word);
        }

        function hasAddition(addition) {
            return !!(addition && addition.value);
        }

        $('.point_wrap').click(function() {
            $(this).find('.infobox').addClass('on');
            $('.point_wrap').not(this).find('.infobox').removeClass('on');
        });

        map.setCursor('pointer');
    }


    function location_map(sgdt_idx) {
        var form_data = new FormData();
        form_data.append("act", "my_location_list");
        form_data.append("sgdt_idx", sgdt_idx);
        form_data.append("event_start_date", '<?= $s_date ?>');

        $.ajax({
            url: "./location_update",
            enctype: "multipart/form-data",
            data: form_data,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 10000,
            dataType: 'json',
            success: function(data) {
                if (data) {
                    var my_profile = data.my_profile;
                    var st_lat = data.my_lat;
                    var st_lng = data.mt_long;

                    // 이미지가 들어있는 부모 요소를 찾습니다.
                    var parentElement = document.getElementById('map');

                    // 부모 요소의 자식 요소로 있는 모든 이미지를 제거합니다.
                    while (parentElement.firstChild) {
                        parentElement.removeChild(parentElement.firstChild);
                    }
                    initializeMap(my_profile, st_lat, st_lng, data);
                } else {
                    console.log(err);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }

    function f_del_location_modal(i) {
        $('#slt_idx').val(i);
        $('#location_delete_modal').modal('show');
    }

    function f_location_alarm_modal(i, alarm_chk) {
        $('#slt_idx').val(i);
        if (alarm_chk == 'Y') {
            $('#arm_setting_no_modal').modal('show');

        } else {
            $('#arm_setting_modal').modal('show');
        }
    }

    function f_delete_location() {
        $('#location_delete_modal').modal('hide');

        var slt_idx = $('#slt_idx').val();

        var form_data = new FormData();
        form_data.append("act", "location_delete");
        form_data.append("slt_idx", slt_idx);

        $.ajax({
            url: "./location_update",
            enctype: "multipart/form-data",
            data: form_data,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 5000,
            success: function(data) {
                if (data == 'Y') {
                    var sgdt_idx = $('#sgdt_idx').val();
                    f_get_box_list();
                    location_map(sgdt_idx);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }

    function f_alarm_location() {
        $('#arm_setting_modal').modal('hide');
        $('#arm_setting_no_modal').modal('hide');

        var slt_idx = $('#slt_idx').val();

        var form_data = new FormData();
        form_data.append("act", "location_alarm_chk");
        form_data.append("slt_idx", slt_idx);

        $.ajax({
            url: "./location_update",
            enctype: "multipart/form-data",
            data: form_data,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 5000,
            success: function(data) {
                if (data == 'Y') {
                    var sgdt_idx = $('#sgdt_idx').val();
                    f_get_box_list();
                    location_map(sgdt_idx);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }

    function location_add() {

        var sgt_idx = $('#sgt_idx').val();
        var sgdt_idx = $('#sgdt_idx').val();
        var slt_title = $('#slt_title').val();
        var slt_add = $('#sst_location_add').val();
        var slt_lat = $('#sst_location_lat').val();
        var slt_long = $('#sst_location_long').val();

        if (!slt_lat || !slt_long) {
            jalert('위치를 선택해주세요.');
            return false;
        }

        if (!slt_title) {
            jalert('별칭을 입력해주세요.');
            return false;
        }

        var form_data = new FormData();
        form_data.append("act", "location_add");
        form_data.append("sgt_idx", sgt_idx);
        form_data.append("sgdt_idx", sgdt_idx);
        form_data.append("slt_title", slt_title);
        form_data.append("slt_add", slt_add);
        form_data.append("slt_lat", slt_lat);
        form_data.append("slt_long", slt_long);

        $.ajax({
            url: "./location_update",
            enctype: "multipart/form-data",
            data: form_data,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 5000,
            success: function(data) {
                if (data == 'Y') {
                    var sgdt_idx = $('#sgdt_idx').val();
                    f_get_box_list();
                    location_map(sgdt_idx);
                    $('#map_info_box').addClass('d-none-temp');
                    $('#slt_title').val('');
                } else if (data == 'E') {
                    // jalert("내장소는 최대 2개까지 등록 가능합니다.");
                    $('#showSub_modal').modal('show');
                    $('#map_info_box').addClass('d-none-temp');
                    $('#slt_title').val('');
                } else {
                    jalert("내장소 등록에 실패했습니다.");
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }

    //손으로 바텀시트 움직이기
    document.addEventListener('DOMContentLoaded', function() {
        var startY = 0;
        var isDragging;

        var optBottom = document.querySelector('.opt_bottom');
        if (optBottom) {
            optBottom.addEventListener('touchstart', function(event) {
                startY = event.touches[0].clientY; // 터치 시작 좌표 저장
            });

            optBottom.addEventListener('touchmove', function(event) {
                var currentY = event.touches[0].clientY; // 현재 터치 좌표
                var deltaY = currentY - startY; // 터치 움직임의 차이 계산

                // 움직임이 일정 값 이상이면 보이거나 숨김
                if (Math.abs(deltaY) > 50) {
                    var isVisible = deltaY < 0; // deltaY가 음수면 보이게, 양수면 숨기게
                    var newTransformValue = isVisible ? 'translateY(0)' : 'translateY(<?= $translateY ?>%)';
                    optBottom.style.transform = newTransformValue;
                }
            });


            optBottom.addEventListener('mousedown', function(event) {
                startY = event.clientY; // 클릭 시작 좌표 저장
                isDragging = true;
            });

            document.addEventListener('mousemove', function(event) {
                if (isDragging) {
                    var currentY = event.clientY; // 현재 마우스 좌표
                    var deltaY = currentY - startY; // 움직임의 차이 계산

                    // 움직임이 일정 값 이상이면 보이거나 숨김
                    if (Math.abs(deltaY) > 50) {
                        var isVisible = deltaY < 0; // deltaY가 음수면 보이게, 양수면 숨기게
                        var newTransformValue = isVisible ? 'translateY(0)' : 'translateY(<?= $translateY ?>%)';
                        optBottom.style.transform = newTransformValue;
                    }
                }
            });

            document.addEventListener('mouseup', function() {
                isDragging = false;
            });

        } else {
            console.error("요소를 찾을 수 없습니다.");
        }
    });

    //배너 슬라이더
    var ban_swiper = new Swiper(".banSwiper", {
        //     autoplay: {
        //         delay: 2500,
        //         disableOnInteraction: false,
        //   },
        autoHeight: true,
        pagination: {
            el: ".banSwiper .swiper-pagination",
            type: "fraction",
        },
        navigation: {
            nextEl: ".banSwiper .swiper-button-next",
            prevEl: ".banSwiper .swiper-button-prev",
        },
    });
    // 지도 마커클릭시 상세내역 보여짐
    $('.point_wrap').click(function() {
        $('.point_wrap').click(function() {
            $(this).find('.infobox').addClass('on');
            $('.point_wrap').not(this).find('.infobox').removeClass('on');
        });
    });

    // 문서 전체를 클릭했을 때 마커 상세내역 사라짐
    // $(document).click(function(event) {
    //     if (!$(event.target).closest('.point_wrap, .infobox').length) {
    //         $('.point_wrap .infobox').removeClass('on');
    //     }
    // });

    function map_panto(lat, lng) {
        var mapContainer = document.getElementById("map");
        var optBottom = document.querySelector('.opt_bottom');
        var bottomSheetHeight = optBottom ? optBottom.getBoundingClientRect().height : 0;
        var mapHeight = mapContainer.getBoundingClientRect().height;
        var verticalCenterOffset = (mapHeight - bottomSheetHeight) / 2 / 2;

        map.setCenter(new naver.maps.LatLng(lat, lng));

        var optBottom = document.querySelector('.opt_bottom');
        if (optBottom) {
            var transformY = optBottom.style.transform;
            if (transformY == 'translateY(0px)') {
                map.panBy(new naver.maps.Point(0, verticalCenterOffset + 40)); // 위로 180 픽셀 이동
            }
            map.panBy(new naver.maps.Point(0, verticalCenterOffset)); 
        }

        // 해당 좌표에 있는 마커를 찾습니다.
        var clickedMarker = findMarkerByPosition(lat, lng);

        // 찾은 마커를 클릭했을 때의 동작을 시뮬레이트합니다.
        if (clickedMarker) {
            naver.maps.Event.trigger(clickedMarker, 'click');
        }
        if (selectmarker) {
            selectmarker.setMap(null);
        }

        selectmarker = new naver.maps.Marker({
            position: new naver.maps.LatLng(lat, lng),
            map: map
        });

        $(".flt_map_pin_wrap").removeClass("on");
        $(".loc_nickname_wp").removeClass("on");

        $('#location_add').html('');

        $('#sst_location_add').val('');
        $('#sst_location_lat').val('');
        $('#sst_location_long').val('');
        $('#slt_title').val('');
        $('#map_info_box').addClass('d-none-temp');
    }

    function map_panto_location(lat, lng) {
        map.setCenter(new naver.maps.LatLng(lat, lng));

        var optBottom = document.querySelector('.opt_bottom');
        if (optBottom) {
            var transformY = optBottom.style.transform;
            if (transformY == 'translateY(0px)') {
                map.panBy(new naver.maps.Point(0, 180)); // 위로 180 픽셀 이동
            }
        }

        // 해당 좌표에 있는 마커를 찾습니다.
        var clickedMarker = findMarkerByPosition(lat, lng);

        // 찾은 마커를 클릭했을 때의 동작을 시뮬레이트합니다.
        if (clickedMarker) {
            naver.maps.Event.trigger(clickedMarker, 'click');
        }
        if (selectmarker) {
            selectmarker.setMap(null);
        }

        selectmarker = new naver.maps.Marker({
            position: new naver.maps.LatLng(lat, lng),
            map: map
        });
    }

    function map_panto_recomand(lat, lng, addr, title) {
        map.setCenter(new naver.maps.LatLng(lat, lng));

        var optBottom = document.querySelector('.opt_bottom');
        if (optBottom) {
            var transformY = optBottom.style.transform;
            if (transformY == 'translateY(0px)') {
                map.panBy(new naver.maps.Point(0, 180)); // 위로 180 픽셀 이동
            }
        }
        // 해당 좌표에 있는 마커를 찾습니다.
        var clickedMarker = findMarkerByPosition(lat, lng);

        // 찾은 마커를 클릭했을 때의 동작을 시뮬레이트합니다.
        if (clickedMarker) {
            naver.maps.Event.trigger(clickedMarker, 'click');
        }
        if (selectmarker) {
            selectmarker.setMap(null);
        }

        selectmarker = new naver.maps.Marker({
            position: new naver.maps.LatLng(lat, lng),
            map: map
        });


        $(".flt_map_pin_wrap").addClass("on");
        $(".loc_nickname_wp").addClass("on");

        $('#location_add').html(addr);

        $('#sst_location_add').val(addr);
        $('#sst_location_lat').val(lat);
        $('#sst_location_long').val(lng);
        $('#slt_title').val(title);
        $('#map_info_box').removeClass('d-none-temp');
    }

    function findMarkerByPosition(lat, lng) {
        var tolerance = 0.000001; // 허용 오차 범위 설정 (마커들의 위치와의 오차 범위에 따라 조절)

        // 마커들을 저장하는 배열이 markers 배열이라고 가정하겠습니다.
        for (var i = 0; i < markers.length; i++) {
            var marker = markers[i];
            var position = marker.getPosition();
            // 마커의 위치와 주어진 위치의 위도, 경도 값의 차이가 허용 오차 범위 이내인지 확인합니다.
            if (Math.abs(position.lat() - lat) < tolerance && Math.abs(position.lng() - lng) < tolerance) {
                return marker; // 일치하는 마커를 반환합니다.
            }
        }
        return null; // 일치하는 마커가 없을 경우 null을 반환합니다.
    }

    $("#frm_schedule_map").validate({
        submitHandler: function() {
            var f = document.frm_schedule_map;

            if ($('#sst_location_add').val() == '') {
                jalert('위치를 선택해주세요.');
                return false;
            }

            $('#slt_idx_t').val($('#sst_location_add').val());
            $('#schedule_map').modal('hide');

            return false;
        },
        rules: {
            sst_location_add: {
                required: true,
            },
        },
        messages: {
            sst_location_add: {
                required: "위치를 선택해주세요.",
            },
        },
        errorPlacement: function(error, element) {
            $(element)
                .closest("form")
                .find("span[for='" + element.attr("id") + "']")
                .append(error);
        },
    });

    $(".flt_close").click(function() {
        $(".floating_wrap").removeClass("on");
        $(".flt_map_pin_wrap").removeClass("on");
    });
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>