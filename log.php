<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '5';
$h_menu = '5';
$_SUB_HEAD_TITLE = translate("로그", $userLang); // "로그" 번역
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/b_menu.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert(translate('로그인이 필요합니다.', $userLang), './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        // alert('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', './logout');
    }
}

if ($_GET['sdate'] == '') {
    $_GET['sdate'] = date('Y-m-d');
}
$sdate = date('Y-m-d');
$tt = strtotime($sdate);

$numDay = date('d', $tt);
$numMonth = date('m', $tt);
$numMonth2 = date('n', $tt);
// 숫자가 1자리일 경우 앞에 0을 붙여주는 로직 추가
$numMonth2 = str_pad($numMonth2, 2, '0', STR_PAD_LEFT);
$numYear = date('Y', $tt);
$prevMonth = date('Y-m-01', strtotime($sdate . " -" . $dayOfWeek . "days"));
$nextMonth = date('Y-m-01', strtotime($sdate . " +" . $dayOfWeek . "days"));
$calendar_date_title = $numYear . "." . " " . $numMonth2;
$now_month_year = $numYear . "-" . $numMonth;

$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('mt_show', 'Y');
$mem_row = $DB->getone('member_t');
if ($mem_row['mt_level'] == 5) {
    // 오늘날짜로부터 14일 전까지 표시
    $log_start_date = date('Y-m-d', strtotime($sdate . '-14 days'));
    $log_end_date = date('Y-m-d');
} else {
    // 오늘날짜로부터 하루 전까지 표시
    $log_start_date = date('Y-m-d', strtotime($sdate . '-2 day'));
    $log_end_date = date('Y-m-d');
}

if (!$_GET['sgdt_mt_idx']) {
    $_GET['sgdt_mt_idx'] = $_SESSION['_mt_idx'];
}
$row_slmt['sgdt_mt_idx'] = $_GET['sgdt_mt_idx'];

$mt_location_info = get_member_location_log_t_info($row_slmt['sgdt_mt_idx']);
$mt_info = get_member_t_info($row_slmt['sgdt_mt_idx']);

$m_mt_lat = $mt_location_info['mlt_lat'];
$m_mt_long = $mt_location_info['mlt_long'];
$mt_file1_url = get_image_url($mt_info['mt_file1']);
$sgt_cnt = f_get_owner_cnt($_SESSION['_mt_idx']); //오너인 그룹수
$sgdt_leader_cnt = f_get_leader_cnt($_SESSION['_mt_idx']); //리더인 그룹수
$sgdt_cnt = f_group_invite_cnt($_SESSION['_mt_idx']); //초대된 그룹수
$sgt_row = f_group_info($_SESSION['_mt_idx']); // 그룹생성여부


$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgt_idx', $sgt_row['sgt_idx']);
$DB->where('sgdt_show', 'Y');
$DB->where('sgdt_owner_chk', 'Y');
$sgdt_row = $DB->getone('smap_group_detail_t');

if (!$sgdt_row['sgdt_idx']) {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_exit', 'N');
    $DB->where('sgdt_owner_chk', 'N');
    $sgdt_row = $DB->getone('smap_group_detail_t');
}

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
    .sch_cld_wrap {
        padding: 1rem 0;
    }

    #wrap {
        height: 100vh;
        min-height: 100vh;
    }

    .log_wrap {
        position: fixed;
        top: 0;
        left: 50%;
        width: 100%;
        height: 100%;
        max-width: 50rem;
        transform: translateX(-50%);
        height: 100% !important;
        min-height: 100% !important;
    }

    .log_pg_div {
        position: relative;
        top: 0;
        left: 0;
        bottom: 0;
        width: 100%;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
</style>
<div class="container sub_pg log_wrap px-0">
    <!-- 달력 -->
    <section class="sch_cld_wrap bg-white ">
        <input type="hidden" name="week_calendar" id="week_calendar" value="Y" />
        <input type="hidden" name="lsdate" id="lsdate" value="<?= $log_start_date ?>" />
        <input type="hidden" name="ledate" id="ledate" value="<?= $log_end_date ?>" />
        <input type="hidden" name="csdate" id="csdate" value="<?= $sdate ?>" />
        <input type="hidden" name="nmy" id="nmy" value="<?= $now_month_year ?>" />
        <input type="hidden" name="sgdt_mt_idx" id="sgdt_mt_idx" value="<?= $row_slmt['sgdt_mt_idx'] ?>" />
        <input type="hidden" name="sgdt_idx" id="sgdt_idx" value="<?= $sgdt_row['sgdt_idx'] ?>" />
        <input type="hidden" name="event_start_date" id="event_start_date" value="<?= $_GET['sdate'] ?>" />
        <input type="hidden" name="map_mt_lat" id="map_mt_lat" value="<?= $m_mt_lat ?>" />
        <input type="hidden" name="map_mt_long" id="map_mt_long" value="<?= $m_mt_long ?>" />
        <input type="hidden" name="map_mt_file1" id="map_mt_file1" value="<?= $mt_file1_url ?>" />
        <input type="hidden" name="sst_idx" id="sst_idx" value="" />
        <div class="cld_head_wr">
            <div class="add_cal_tit">
                <button type="button" class="btn h-auto swiper-button-prev"><i class="xi-angle-left-min"></i></button>
                <div class="sel_month d-inline-flex flex-grow-1 text-centerf">
                    <a href="javascript:;" onclick="f_calendar_log_init('today');"><img class="mr-2" src="<?= CDN_HTTP ?>/img/sel_month.png" alt="<?= translate("월 선택 아이콘", $userLang) ?>" style="width:1.6rem; "></a>
                    <p class="fs_15 fw_600" id="calendar_date_title"><?= $calendar_date_title ?></p>
                </div>
                <button type="button" class="btn h-auto swiper-button-next"><i class="xi-angle-right-min"></i></button>
            </div>
            <!-- <div class="cld_head fs_12">
                <ul>
                    <li class="sun">일</li>
                    <li>월</li>
                    <li>화</li>
                    <li>수</li>
                    <li>목</li>
                    <li>금</li>
                    <li class="sat">토</li>
                </ul>
            </div> -->
        </div>
        <div id="schedule_calandar_box" class="cld_date_wrap"></div>
        <!-- <div id="location_info_box"></div> -->
    </section>
    <!-- 지도 -->
    <section class="log_map_wrap" id="map">
    </section>
    <!-- 로그 -->
    <?
    if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) {
        // $translateY = 70;
        $translateY = 60;
    } else {
        // $translateY = 54;
        $translateY = 70;
    }
    ?>
    <? if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) {
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $row_sgdt = $DB->getone('smap_group_detail_t', 'GROUP_CONCAT(sgt_idx) as gc_sgt_idx');

        unset($list_sgt);
        $DB->where("sgt_idx in (" . $row_sgdt['gc_sgt_idx'] . ")");
        $DB->where('sgt_show', 'Y');
        $DB->orderBy("sgt_udate", "desc");
        $DB->orderBy("sgt_idx", "asc");
        $list_sgt = $DB->get('smap_group_t');
    ?>
        <section class="opt_bottom" style="transform: translateY(<?= $translateY ?>%);">
            <div class="top_bar_wrap text-center pt_08">
                <img src="./img/top_bar.png" class="top_bar" width="34px" alt="탑바" />
                <img src="./img/btn_tl_arrow.png" class="top_down mx-auto" width="12px" alt="탑업" />
            </div>
            <div>
                <div class="px_16 mb-3">
                    <div class="border bg-white rounded-lg px_16 py_16">
                        <!-- 위치조정 슬라이드 -->
                        <div class="border-bottom loc_rog_adj pb-4">
                            <p class="fs_16 fw_600"><?= translate('이동경로 따라가기', $userLang); ?></p>
                            <div class="pt-4">
                                <input type="range" class="custom-range" id="timeSlider" min='1' max='1' value='1'>
                            </div>
                        </div>
                        <div>
                            <div style="padding-top: 1.6rem;">
                                <p class="fs_16 fw_600 mb-3"><?= translate('그룹원', $userLang); ?></p>
                                <!--프로필 tab_scroll scroll_bar_x-->
                                <!-- <div class="" id="location_member_box"></div> -->
                                <div class="mem_wrap swiper mem_swiper">
                                    <div class="swiper-wrapper d-flex ">
                                        <div class="swiper-slide checks mem_box">
                                            <label>
                                                <input type="radio" name="member_r1" id="member_r1_<?= $_SESSION['_mt_idx'] ?>" value="<?= $_SESSION['_mt_idx'] ?>" checked />
                                                <div class="prd_img mx-auto" onclick="f_profile_click('<?= $_SESSION['_mt_idx'] ?>','<?= $sgdt_row['sgdt_idx'] ?>');">
                                                    <!-- 알림왔을 때 on_arm 추가 -->
                                                    <div class="rect_square rounded_14">
                                                        <img src="<?= $_SESSION['_mt_file1'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="이미지" />
                                                    </div>
                                                </div>
                                                <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic" onclick="f_profile_click('<?= $_SESSION['_mt_idx'] ?>','<?= $sgdt_row['sgdt_idx'] ?>');"><?= $_SESSION['_mt_nickname'] ? $_SESSION['_mt_nickname'] : $_SESSION['_mt_name'] ?></p>
                                            </label>
                                        </div>
                                        <?php
                                        if ($list_sgt) {
                                            foreach ($list_sgt as $row_sgt) {
                                                $member_cnt_t = get_group_member_cnt($row_sgt['sgt_idx']);
                                                unset($list_sgdt);
                                                $list_sgdt = get_sgdt_member_list($row_sgt['sgt_idx']);
                                                $invite_cnt = get_group_invite_cnt($row_sgt['sgt_idx']);
                                                if ($invite_cnt || $list_sgdt['data']) {
                                                    if ($list_sgdt['data']) {
                                                        foreach ($list_sgdt['data'] as $key => $val) {
                                        ?>
                                                            <div class="swiper-slide checks mem_box">
                                                                <label>
                                                                    <input type="radio" name="member_r1" id="member_r1_<?= $val['mt_idx'] ?>" value="<?= $val['mt_idx'] ?>" />
                                                                    <div class="prd_img mx-auto" onclick="f_profile_click('<?= $val['mt_idx'] ?>','<?= $val['sgdt_idx'] ?>');">
                                                                        <!-- 알림왔을 때 on_arm 추가 -->
                                                                        <div class="rect_square rounded_14">
                                                                            <img src="<?= $val['mt_file1_url'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="이미지" />
                                                                        </div>
                                                                    </div>
                                                                    <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic" onclick="f_profile_click('<?= $val['mt_idx'] ?>','<?= $val['sgdt_idx'] ?>');"><?= $val['mt_nickname'] ? $val['mt_nickname'] : $val['mt_name'] ?></p>
                                                                </label>
                                                            </div>
                                        <?
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                        <!-- 그룹원 추가 -->
                                        <?php if ($sgt_cnt > 0) { ?>
                                            <div class="swiper-slide mem_box add_mem_box" onclick="location.href='./group'">
                                                <button class="btn mem_add">
                                                    <i class="xi-plus-min fs_20"></i>
                                                </button>
                                                <p class="fs_12 fw_400 text-center mt-2 line_h1_4 text_dynamic" ><?= translate('그룹원추가', $userLang) ?></p>
                                            </div>
                                        <?php } else { ?>
                                            <div class="swiper-slide mem_box add_mem_box" style="visibility: hidden;">
                                                <button class="btn mem_add">
                                                    <i class="xi-plus-min fs_20"></i>
                                                </button>
                                                <p class="fs_12 fw_400 text-center mt-2 line_h1_4 text_dynamic" ><?= translate('그룹원추가', $userLang) ?></p>
                                            </div>
                                        <?php } ?>

                                    </div>
                                </div>
                                <script>
                                    //프로필 슬라이더
                                    let mem_swiper = new Swiper(".mem_swiper", {
                                        slidesPerView: 'auto',
                                        spaceBetween: 12,
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="grp_wrap">
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
                            <input type="hidden" name="group_sgdt_idx" id="group_sgdt_idx" value="<?= $sgdt_row['sgdt_idx'] ?>" />
                        </form>
                        <div id="group_member_list_box"></div>
                    </div>
                </div> -->
                <div class="mt-2 mb-3 px_16">
                    <!-- 위치기록 요약 -->
                    <div class="border bg-white rounded-lg px_16 py_16">
                        <p class="fs_16 fw_600 mt-2"><?= translate('위치기록 요약', $userLang); ?></p>
                        <ul class="loc_rog_ul d-flex align-item-center justify-content-between py-4" id="location_log_box">
                            <li class="text-center border-right flex-fill loc_rog_ul_l11">
                                <p class="fs_13 fw_400 text_gray line_h1_3 text_dynamic"><?= translate('일정개수', $userLang); ?></p>
                                <p class="fs_16 fw_600 mt-2 line_h1_3 text_dynamic">0<span> <?= translate('개', $userLang); ?></span></p>
                            </li>
                            <li class="text-center border-right flex-fill loc_rog_ul_l12">
                                <p class="fs_13 fw_400 text_gray line_h1_3 text_dynamic"><?= translate('이동거리', $userLang); ?></p>
                                <p class="fs_16 fw_600 mt-2 line_h1_3 text_dynamic">0m</p>
                            </li>
                            <li class="text-center border-right flex-fill loc_rog_ul_l13">
                                <p class="fs_13 fw_400 text_gray line_h1_3 text_dynamic"><?= translate('이동시간', $userLang); ?></p>
                                <p class="fs_16 fw_600 mt-2 line_h1_3 text_dynamic">0 <?= translate('분', $userLang); ?></p>
                            </li>
                            <li class="text-center flex-fill loc_rog_ul_l14">
                                <p class="fs_13 fw_400 text_gray line_h1_3 text_dynamic"><?= translate('걸음수', $userLang); ?></p>
                                <p class="fs_16 fw_600 mt-2 line_h1_3 text_dynamic">0 <?= translate('걸음', $userLang); ?></p>
                            </li>
                        </ul>
                    </div>
                </div>
            <? } else { ?>
                <section class="opt_bottom" style="transform: translateY(<?= $translateY ?>%);">
                    <div class="top_bar_wrap text-center pt_08">
                        <img src="./img/top_bar.png" class="top_bar" width="34px" alt="탑바" />
                        <img src="./img/btn_tl_arrow.png" class="top_down mx-auto" width="12px" alt="탑업" />
                    </div>
                    <div>
                        <div class="px_16 mb-3">
                            <div class="border bg-white rounded-lg px_16 py_16">
                                <!-- 위치조정 슬라이드 -->
                                <div class="loc_rog_adj pb-4">
                                    <p class="fs_16 fw_600"><?= translate('이동경로 따라가기', $userLang); ?></p>
                                    <div class="pt-4">
                                        <input type="range" class="custom-range" id="timeSlider" min='1' max='1' value='1'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <? } ?>
            </div>
        </section>
</div>
<!-- 토스트 Toast 토스트 넣어두었습니다. 필요하시면 사용하심됩니다.! 사용할 버튼에 id="ToastBtn" 넣으면 사용가능! -->
<div id="Toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-body">
        <p><i class="xi-check-circle mr-2"></i><?= translate('위치가 등록되었습니다.', $userLang); ?></p>
        <!-- <p><i class="xi-error mr-2"></i>에러메시지</p> -->
    </div>
</div>
<!-- H-1 그룹없음 / 무료플랜 플러팅 -->
<? if ($sgt_cnt < 1 && $sgdt_cnt < 1) { ?>
    <div class="floating_wrap on">
        <div class="flt_inner">
            <div class="flt_head">
                <p class="line_h1_2"><span class="text_dynamic flt_badge"><?= translate('그룹만들기', $userLang); ?></span></p>
            </div>
            <div class="flt_body pb-5 pt-3">
                <p class="text_dynamic line_h1_3 fs_17 fw_700"><?= translate('아직 그룹을 만들지 않으셨네요.', $userLang); ?></p>
                <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500"><?= translate('그룹원의 이동경로를
                    로그 메뉴에서 확인할 수 있습니다.
                    그룹을 만들고 이 기능을 사용해 볼까요?', $userLang); ?>
                </p>
            </div>
            <div class="flt_footer">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_create'">다음</button>
            </div>
        </div>
    </div>
<? } ?>
<!-- <? if ($sgt_cnt == 1 && $expt_cnt < 1) { ?>
    <div class="floating_wrap on">
        <div class="flt_inner">
            <div class="flt_head">
                <p class="line_h1_2"><span class="text_dynamic flt_badge">그룹원 초대하기</span></p>
            </div>
            <div class="flt_body pb-5 pt-3">
                <p class="text_dynamic line_h1_3 fs_17 fw_700">이동 경로로 알아보는 
                    <span class="text-primary">그룹원</span>의 하루!
                </p>
                <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500">SMAP-로그에서 제공하는 이동경로 조회기능을 통해 
                    그룹원의 하루를 재구성해 볼 수 있어요.</p>
            </div>
            <div class="flt_footer">
                <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" onclick="location.href='./group_info?sgt_idx=<?= $row_sgt['sgt_idx'] ?>'">초대하러 가기</button>
            </div>
        </div>
    </div>
<? } ?> -->

<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        const adMessages = [
            "로그를 불러오는 중...\n🎬 광고 시청으로 SMAP을 응원해 주세요!",
            "로그 데이터 확인을 위해 광고 시청이 필요합니다.\n🙏 여러분의 협조에 감사드립니다."
        ];

        // 무작위로 광고 메시지 선택
        const randomAdMessage = adMessages[Math.floor(Math.random() * adMessages.length)];

        // 선택된 메시지를 모달에 삽입
        document.getElementById('adMessage').innerText = randomAdMessage;
    });
</script> -->
<!-- D-6 광고표시 후 로그 표출 : 3의배수 카운트 중 나오는 모달창  -->
<!-- <div class="modal fade" id="showAd_modal" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <input type="hidden" name="pedestrian_path_modal_sgdt_idx" id="pedestrian_path_modal_sgdt_idx" value="" />
            <input type="hidden" name="path_day_count" id="path_day_count" value="" />
            <div class="modal-body text-center pb-4">
                <img src="./img/loud_speaker.png" width="48px" class="pt-3" alt="최적의경로" />
                <p id="adMessage" class="fs_16 text_dynamic fw_700 line_h1_3 mt-4"></p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close" onclick="history.back()">취소하기</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="showAd(<?= $ad_data ?>)">광고보기</button>
                </div>
            </div>
        </div>
    </div>
</div> -->
<script>
    let optimalPath; // 최적 경로를 표시할 변수입니다.
    let drawInfoArr = [];
    let scheduleMarkerCoordinates = [];
    let scheduleStatus = [];
    let startX, startY, endX, endY; // 출발지와 도착지 좌표 변수 초기화
    let pathCount;
    // 버튼 엘리먼트 찾기
    let showPathButton = document.getElementById('showPathButton');
    let showPathAdButton = document.getElementById('showPathAdButton'); //광고실행버튼
    let map;
    let centerLat, centerLng;
    // 전역 상태 객체
    const state = {
        pathData: null,
        walkingData: null,
        isDataLoaded: false
    };
    // 그룹원별 슬라이드 컨테이너를 저장할 객체
    const groupMemberSlides = {};
    let googleMapsLoaded = false;
    let googleMapsLoadPromise = null;
    let polylinePath = [];
    let resultdrawArr = [];
    let locationMarker;
    let currentSelectedDate = '<?= $_GET['sdate'] ?>' || new Date().toISOString().split('T')[0];
    let sgdtMtIdx = $('#sgdt_mt_idx').val(); // 초기 sgdt_mt_idx 값 저장
    let sgdtIdx = $('#sgdt_idx').val(); // 초기 sgdt_idx 값 저장
    let mapInitialized = false; // 지도 초기화 여부를 나타내는 변수 추가
    let markers = [];
    let polylines = [];
    let profileMarkers = [];
    let scheduleMarkers = [];
    let logMarkers = [];
    let optBottom = document.querySelector(".opt_bottom");
    let isPannedDown = false;
    let originalCenter = null; // 초기 중심 좌표 저장
    let currentLat;
    let currentLng;
    const timeSlider = document.getElementById('timeSlider');
</script>
<?php
if ($userLang === 'ko') {
    // 네이버 지도 스크립트
?>
    <script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?= NCPCLIENTID ?>"></script>
    <script>
        map = new naver.maps.Map("map", {
            center: new naver.maps.LatLng(<?= $_SESSION['_mt_lat'] ?>, <?= $_SESSION['_mt_long'] ?>),
            zoom: 16,
            mapTypeControl: false
        });

        function initNaverMap(markerData, sgdt_idx) {
            map.setCenter(new naver.maps.LatLng(markerData.my_lat, markerData.mt_long));
            clearAllMapElements();

            if (markerData && markerData.log_markers && markerData.log_markers.length > 0) {
                if (markerData.log_chk === "Y") {
                    let polylinePath = [];
                    let gradient = createGradient(markerData.log_markers.length);
                    let stayCount = 1;

                    markerData.log_markers.forEach((marker, index) => {
                        const logmarkerLat = parseFloat(marker.latitude);
                        const logmarkerLng = parseFloat(marker.longitude);

                        polylinePath.push(new naver.maps.LatLng(logmarkerLat, logmarkerLng));

                        const markerContent = document.createElement('div');
                        markerContent.className = 'point_wrap point5';
                        markerContent.setAttribute('data-rangeindex', index + 1);

                        const stayMarker = document.createElement('div');
                        stayMarker.className = 'stay_marker d-none';
                        const stayMarkerInner = document.createElement('div');
                        stayMarkerInner.className = 'stay_marker_inner';
                        stayMarker.appendChild(stayMarkerInner);
                        markerContent.appendChild(stayMarker);

                        const infoBox = document.createElement('div');
                        if (marker.type === 'stay') {
                            infoBox.className = 'infobox rounded-sm bg-white px_08 py_08 d-none';
                            infoBox.innerHTML = `
                                <p class="fs_12 fw_900 text_dynamic">${marker.time}</p>
                                <p class="fs_10 fw_600 text_dynamic text-primary line_h1_2 mt-2">${marker.stayTime}</p>
                                <p class="fs_10 fw_400 line1_text line_h1_2 mt-2">${marker.address}</p>
                            `;
                            stayCount++;
                        } else {
                            infoBox.className = 'infobox infobox_2 rounded-sm px_08 py_08 d-none';
                            infoBox.style.backgroundColor = '#413F4A';
                            infoBox.style.color = '#E6F3FF';
                            infoBox.innerHTML = `<p class="fs_12 fw_800 text_dynamic">${marker.time}</p>`;
                        }

                        // infoBox 스타일 변경
                        infoBox.style.position = 'absolute'; // infoBox를 마커 내에서 절대 위치로 설정
                        infoBox.style.zIndex = '3'; // 다른 마커 요소보다 높은 z-index 값 설정
                        infoBox.classList.add('d-none');
                        markerContent.appendChild(infoBox);

                        if (marker.type === 'stay') {
                            const button = document.createElement('button');
                            button.type = 'button';
                            button.className = 'btn log_point point_stay';
                            button.style.position = 'relative'; // button을 기준으로 자식 요소의 위치를 지정

                            const pointInner = document.createElement('span');
                            pointInner.className = 'point_inner';

                            const pointTxt = document.createElement('span');
                            pointTxt.className = 'point_txt';
                            pointTxt.textContent = stayCount - 1;

                            pointInner.appendChild(pointTxt);
                            button.appendChild(pointInner);

                            markerContent.appendChild(button);
                        }

                        const newMarker = new naver.maps.Marker({
                            map: map,
                            position: new naver.maps.LatLng(logmarkerLat, logmarkerLng),
                            icon: {
                                content: markerContent,
                                size: new naver.maps.Size(48, 48), // 아이콘 크기 조정
                                anchor: new naver.maps.Point(10, 10) // 앵커 포인트 조정
                            },
                            zIndex: 1,
                        });

                        logMarkers.push(newMarker);
                    });

                    // 폴리라인 경로 생성
                    for (let i = 0; i < polylinePath.length - 1; i++) {
                        const polyline = new naver.maps.Polyline({
                            path: [polylinePath[i], polylinePath[i + 1]],
                            strokeColor: gradient[i],
                            strokeOpacity: 0.5,
                            strokeWeight: 5,
                            map: map
                        });
                        polylines.push(polyline);
                    }

                    function updateMarkerVisibility(sliderValue) {
                        const marker = markerData.log_markers[sliderValue - 1];
                        if (!marker) return;

                        map.setCenter(new naver.maps.LatLng(parseFloat(marker.latitude), parseFloat(marker.longitude)));

                        currentLat = parseFloat(marker.latitude);
                        currentLng = parseFloat(marker.longitude);

                        logMarkers.forEach((mapMarker, index) => {
                            const content = mapMarker.icon.content;
                            const infoBox = content.querySelector('.infobox');
                            const stayMarker = content.querySelector('.stay_marker');
                            const button = content.querySelector('.btn.log_point');

                            // 현재 선택된 마커의 stay_marker와 infoBox만 표시
                            if (index === sliderValue - 1) {
                                stayMarker?.classList.remove('d-none');
                                if (infoBox) {
                                    infoBox.classList.remove('d-none');
                                    infoBox.style.display = 'block';
                                }
                            } else {
                                stayMarker?.classList.add('d-none');
                                if (infoBox) {
                                    infoBox.classList.add('d-none');
                                    infoBox.style.display = 'none';
                                }
                            }

                            // stay 버튼은 항상 표시
                            if (button) {
                                button.classList.remove('d-none');
                            }
                        });
                    }

                    let prevOptBottomState = null; // opt_bottom의 이전 상태를 저장할 변수

                    if (timeSlider) {
                        timeSlider.max = markerData.log_markers.length;
                        timeSlider.value = 1;
                        updateMarkerVisibility(1);

                        timeSlider.addEventListener('input', function() {
                            // map.setOptions({
                            //     animation: null
                            // }); // 애니메이션 비활성화
                            const sliderValue = parseInt(this.value);
                            updateMarkerVisibility(sliderValue);

                            // opt_bottom이 올라가 있을때
                            if (optBottom.style.transform === 'translateY(0px)') {
                                map.panBy(new naver.maps.Point(0, 180));
                            }
                        });
                    }
                }
            }

            // 스케줄 마커 추가
            if (markerData.schedule_chk === 'Y') {
                for (let i = 1; i <= markerData.count; i++) {
                    const markerLat = parseFloat(markerData['markerLat_' + i]);
                    const markerLng = parseFloat(markerData['markerLong_' + i]);
                    const markerTitle = markerData['markerTitle_' + i];

                    // 랜덤 색상 생성
                    const randomColor = generateRandomColor();

                    // DOM 노드 생성
                    const pointWrapDiv = document.createElement('div');
                    pointWrapDiv.className = 'point_wrap point1';

                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn point point_sch';

                    const spanInner = document.createElement('span');
                    spanInner.className = 'point_inner';

                    const image = document.createElement('img');
                    image.src = './img/sch_alarm.png';
                    image.alt = 'Desired Image';
                    image.className = 'btn point point_ing';
                    image.style.width = '24px';
                    image.style.height = '24px';

                    const infoboxDiv = document.createElement('div');
                    infoboxDiv.className = 'infobox1 rounded_04 px_08 py_03 on';

                    const titleSpan = document.createElement('span');
                    titleSpan.className = 'fs_12 fw_800 text_dynamic line_h1_2 mt-2';
                    titleSpan.textContent = markerTitle;

                    // 스타일 DOM 노드 생성
                    const style = document.createElement('style');
                    style.textContent = `
                        .infobox1 {
                            position: absolute !important;
                            left: 50%; 
                            top: 100%; 
                            transform: translate(-50%, -80%); 
                            background-color: #413F4A;
                            padding: 0.3rem 0.8rem; 
                            border-radius: 0.4rem;
                            z-index: 1;
                            white-space: nowrap; 
                        }

                        .infobox1 span {
                            color: ${randomColor};
                            font-size: 14px !important;
                            white-space: nowrap !important;
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                        }
                        `;

                    // DOM 노드 연결
                    spanInner.appendChild(image);
                    button.appendChild(spanInner);
                    infoboxDiv.appendChild(titleSpan);
                    pointWrapDiv.appendChild(style); // 스타일 노드 추가
                    pointWrapDiv.appendChild(button);
                    pointWrapDiv.appendChild(infoboxDiv);

                    // 네이버 지도 마커 생성
                    const scheduleMarker = new naver.maps.Marker({
                        map: map,
                        position: new naver.maps.LatLng(markerLat, markerLng),
                        icon: {
                            content: pointWrapDiv,
                            size: new naver.maps.Size(48, 48), // 아이콘 크기 조정
                            anchor: new naver.maps.Point(24, 24) // 앵커 포인트 조정
                        },
                        zIndex: 1
                    });

                    scheduleMarkers.push(scheduleMarker);
                }
            }

            // 내 장소 마커 추가
            if (markerData.location_chk === 'Y') {
                for (let i = 1; i <= markerData.location_count; i++) {
                    const locationLat = parseFloat(markerData['locationmarkerLat_' + i]);
                    const locationLng = parseFloat(markerData['locationmarkerLong_' + i]);
                    const locationTitle = markerData['locationmarkerTitle_' + i];

                    // 랜덤 색상 생성
                    const randomColor = generateRandomColor();

                    // DOM 노드 생성
                    const pointWrapDiv = document.createElement('div');
                    pointWrapDiv.className = 'point_wrap point1';

                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn point point_myplc';

                    const spanInner = document.createElement('span');
                    spanInner.className = 'point_inner';

                    const image = document.createElement('img');
                    image.src = './img/loc_alarm.png';
                    image.alt = 'Desired Image';
                    image.className = 'btn point point_ing';
                    image.style.width = '24px';
                    image.style.height = '24px';

                    const infoboxDiv = document.createElement('div');
                    infoboxDiv.className = 'infobox2 rounded_04 px_08 py_03 on';

                    const titleSpan = document.createElement('span');
                    titleSpan.className = 'fs_12 fw_800 text_dynamic line_h1_2 mt-2';
                    titleSpan.textContent = locationTitle;

                    // 스타일 DOM 노드 생성
                    const style = document.createElement('style');
                    style.textContent = `
                        .infobox2 {
                            position: absolute;
                            left: 50%; 
                            top: 100%; 
                            transform: translate(-50%, 40%); 
                            background-color: #413F4A;
                            padding: 0.3rem 0.8rem; 
                            border-radius: 0.4rem;
                            z-index: 1;
                            white-space: nowrap; 
                        }

                        .infobox2 span {
                            color: ${randomColor}; 
                            font-size: 14px !important;
                            white-space: nowrap !important;
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                        }
                        `;

                    // DOM 노드 연결
                    spanInner.appendChild(image);
                    button.appendChild(spanInner);
                    infoboxDiv.appendChild(titleSpan);
                    pointWrapDiv.appendChild(style); // 스타일 노드 추가
                    pointWrapDiv.appendChild(button);
                    pointWrapDiv.appendChild(infoboxDiv);

                    // 네이버 지도 마커 생성
                    const locationMarker = new naver.maps.Marker({
                        map: map,
                        position: new naver.maps.LatLng(locationLat, locationLng),
                        icon: {
                            content: pointWrapDiv,
                            size: new naver.maps.Size(48, 48), // 아이콘 크기 조정
                            anchor: new naver.maps.Point(24, 24) // 앵커 포인트 조정
                        },
                        zIndex: 1
                    });

                    // 마커 배열에 추가 (필요하다면)
                    markers.push(locationMarker);
                }
            }

            // 랜덤 색상 생성
            const randomColor = generateRandomColor();

            // DOM 노드 생성
            const pointWrapDiv = document.createElement('div');
            pointWrapDiv.className = 'point_wrap point1';

            const mapUserDiv = document.createElement('div');
            mapUserDiv.className = 'map_user';
            pointWrapDiv.appendChild(mapUserDiv);

            const mapRtImgDiv = document.createElement('div');
            mapRtImgDiv.className = 'map_rt_img rounded_14';
            mapUserDiv.appendChild(mapRtImgDiv);

            const rectSquareDiv = document.createElement('div');
            rectSquareDiv.className = 'rect_square';
            mapRtImgDiv.appendChild(rectSquareDiv);

            const image = document.createElement('img');
            image.src = markerData.my_profile;
            image.alt = '프로필 이미지';
            image.onerror = function() {
                this.src = '<?= $ct_no_img_url ?>';
            };
            rectSquareDiv.appendChild(image);

            // 스타일 DOM 노드 생성 (필요시 추가 스타일 적용)
            // const style = document.createElement('style');
            // style.textContent = `
            // .point_wrap { /* 추가적인 스타일 */ }
            // .map_user { /* 추가적인 스타일 */ }
            // /* ... */
            // `;
            // pointWrapDiv.appendChild(style);

            // 네이버 지도 마커 생성
            const profileMarker = new naver.maps.Marker({
                map: map,
                position: new naver.maps.LatLng(markerData.my_lat, markerData.mt_long),
                icon: {
                    content: pointWrapDiv,
                    size: new naver.maps.Size(44, 44),
                    anchor: new naver.maps.Point(22, 22)
                },
                zIndex: 2
            });

            // 마커 배열에 추가
            profileMarkers.push(profileMarker);
        }

        function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
            const R = 6371; // 지구의 반지름 (km)
            const dLat = deg2rad(lat2 - lat1);
            const dLon = deg2rad(lon2 - lon1);
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const d = R * c; // 두 지점 사이의 거리 (km)
            return d;
        }

        function deg2rad(deg) {
            return deg * (Math.PI / 180);
        }
    </script>
<?php
} else {
    // 구글 지도 스크립트
?>
    <script>
        // Google Maps API 로드 함수
        function loadGoogleMapsScript() {
            if (googleMapsLoadPromise) {
                return googleMapsLoadPromise;
            }

            googleMapsLoadPromise = new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyBkWlND5fvW4tmxaj11y24XNs_LQfplwpw&libraries=places,geometry,marker&v=weekly`;
                script.async = true;
                script.defer = true;
                script.onload = () => {
                    googleMapsLoaded = true;
                    resolve();
                };
                script.onerror = reject;
                document.head.appendChild(script);
            });

            return googleMapsLoadPromise;
        }

        // 지도 초기화 함수
        async function initMap(st_lat = 35.12806700000000, st_lng = 136.90676000000000) {
            if (mapInitialized) { // 이미 초기화되었다면 함수 종료
                return;
            }

            if (!googleMapsLoaded) {
                console.log("Waiting for Google Maps API to load...");
                await loadGoogleMapsScript();
            }

            if (map) {
                map.setCenter({
                    lat: parseFloat(st_lat),
                    lng: parseFloat(st_lng)
                });
                return map;
            }

            const mapOptions = {
                center: {
                    lat: parseFloat(st_lat),
                    lng: parseFloat(st_lng)
                },
                zoom: 16,
                mapTypeControl: false,
                mapId: "e40062e414aad354",
                fullscreenControl: false,
                disableDoubleClickZoom: true,
                clickableIcons: false, // 장소 아이콘 클릭 비활성화
                language: '<?= $userLang ?>',
                animatedZoom: false // 애니메이션 줌 비활성화
            };

            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // 추가 옵션 설정
            map.setOptions({
                disableDefaultUI: true, // 기본 UI 비활성화
                gestureHandling: 'greedy' // 스크롤 동작 변경
            });

            mapInitialized = true; // 지도 초기화 완료 표시
            console.log("Map initialized successfully");

            // 지도가 완전히 로딩된 후 이벤트 리스너 등록 (한 번만 등록되도록 수정)
            google.maps.event.addListenerOnce(map, 'idle', () => {
                if (state.isDataLoaded) {
                    drawPathOnMap();
                }
            });

            return map;
        }

        async function initGoogleMap(markerData, sgdt_idx) {
            try {
                await loadGoogleMapsScript();

                if (!map) {
                    await initMap(markerData.my_lat, markerData.mt_long);
                } else {
                    map.setCenter({
                        lat: parseFloat(markerData.my_lat),
                        lng: parseFloat(markerData.mt_long)
                    });
                }
                console.log("Google Map initialized with custom data");

                clearAllMapElements();

                if (markerData && markerData.log_markers && markerData.log_markers.length > 0) {
                    if (markerData.log_chk === "Y") {
                        let polylinePath = [];
                        let gradient = createGradient(markerData.log_markers.length);
                        let stayCount = 1;

                        markerData.log_markers.forEach((marker, index) => {
                            const logmarkerLat = parseFloat(marker.latitude);
                            const logmarkerLng = parseFloat(marker.longitude);
                            const {
                                AdvancedMarkerElement
                            } = google.maps.marker;

                            polylinePath.push(new google.maps.LatLng(logmarkerLat, logmarkerLng));

                            const markerContent = document.createElement('div');
                            markerContent.className = 'point_wrap point5';
                            markerContent.setAttribute('data-rangeindex', index + 1);

                            const stayMarker = document.createElement('div');
                            stayMarker.className = 'stay_marker d-none';
                            const stayMarkerInner = document.createElement('div');
                            stayMarkerInner.className = 'stay_marker_inner';
                            stayMarker.appendChild(stayMarkerInner);
                            markerContent.appendChild(stayMarker);

                            const infoBox = document.createElement('div');
                            if (marker.type === 'stay') {
                                infoBox.className = 'infobox rounded-sm bg-white px_08 py_08 d-none';
                                infoBox.innerHTML = `
                                <p class="fs_12 fw_900 text_dynamic">${marker.time}</p>
                                <p class="fs_10 fw_600 text_dynamic text-primary line_h1_2 mt-2">${marker.stayTime}</p>
                                <p class="fs_10 fw_400 line1_text line_h1_2 mt-2">${marker.address}</p>
                            `;
                                stayCount++;
                            } else {
                                infoBox.className = 'infobox infobox_2 rounded-sm px_08 py_08 d-none';
                                infoBox.style.backgroundColor = '#413F4A';
                                infoBox.style.color = '#E6F3FF';
                                infoBox.innerHTML = `<p class="fs_12 fw_800 text_dynamic">${marker.time}</p>`;
                            }

                            // infoBox 스타일 변경
                            infoBox.style.position = 'absolute'; // infoBox를 마커 내에서 절대 위치로 설정
                            infoBox.style.zIndex = '3'; // 다른 마커 요소보다 높은 z-index 값 설정
                            infoBox.classList.add('d-none');
                            markerContent.appendChild(infoBox);

                            if (marker.type === 'stay') {
                                const button = document.createElement('button');
                                button.type = 'button';
                                button.className = 'btn log_point point_stay';
                                button.style.position = 'relative'; // button을 기준으로 자식 요소의 위치를 지정

                                const pointInner = document.createElement('span');
                                pointInner.className = 'point_inner';

                                const pointTxt = document.createElement('span');
                                pointTxt.className = 'point_txt';
                                pointTxt.textContent = stayCount - 1;

                                pointInner.appendChild(pointTxt);
                                button.appendChild(pointInner);

                                markerContent.appendChild(button);
                            }

                            const newMarker = new AdvancedMarkerElement({
                                map: map,
                                position: {
                                    lat: logmarkerLat,
                                    lng: logmarkerLng
                                },
                                content: markerContent,
                                zIndex: 99, // 다른 마커 요소보다 낮은 z-index 값 설정 (필요시 조절)
                            });

                            logMarkers.push(newMarker);
                        });

                        // 경로 생성 (polyline) - 항상 표시
                        for (let i = 0; i < polylinePath.length - 1; i++) {
                            const polyline = new google.maps.Polyline({
                                path: [polylinePath[i], polylinePath[i + 1]],
                                strokeColor: gradient[i],
                                strokeOpacity: 0.5,
                                strokeWeight: 5,
                                map: map
                            });
                            polylines.push(polyline);
                        }

                        function updateMarkerVisibility(sliderValue) {
                            const marker = markerData.log_markers[sliderValue - 1];
                            if (!marker) return;

                            map.setOptions({
                                center: {
                                    lat: parseFloat(marker.latitude),
                                    lng: parseFloat(marker.longitude)
                                }
                            });

                            currentLat = marker.latitude;
                            currentLng = marker.longitude;


                            logMarkers.forEach((mapMarker, index) => {
                                const content = mapMarker.content;
                                const infoBox = content.querySelector('.infobox');
                                const stayMarker = content.querySelector('.stay_marker');
                                const button = content.querySelector('.btn.log_point');

                                // 현재 선택된 마커의 stay_marker와 infoBox만 표시
                                if (index === sliderValue - 1) {
                                    stayMarker?.classList.remove('d-none');
                                    if (infoBox) {
                                        infoBox.classList.remove('d-none');
                                        infoBox.style.display = 'block';
                                    }
                                } else {
                                    stayMarker?.classList.add('d-none');
                                    if (infoBox) {
                                        infoBox.classList.add('d-none');
                                        infoBox.style.display = 'none';
                                    }
                                }

                                // stay 버튼은 항상 표시
                                if (button) {
                                    button.classList.remove('d-none');
                                }
                            });
                        }

                        if (timeSlider) {
                            timeSlider.max = markerData.log_markers.length;
                            timeSlider.value = 1;
                            updateMarkerVisibility(1);

                            timeSlider.addEventListener('input', function() {
                                const sliderValue = parseInt(this.value);
                                updateMarkerVisibility(sliderValue);

                                // opt_bottom이 올라가 있고, 아직 panBy가 실행되지 않았을 때만 실행
                                if (optBottom.style.transform === 'translateY(0px)') {
                                    const currentCenter = map.getCenter();
                                    map.setOptions({
                                        center: {
                                            lat: currentCenter.lat() - (300 / 111000) * 1.5,
                                            lng: currentCenter.lng()
                                        }
                                    });
                                }
                            });
                        }
                    }
                }

                // 스케줄 마커 추가
                if (markerData.schedule_chk === 'Y') {
                    for (let i = 1; i <= markerData.count; i++) {
                        const markerLat = parseFloat(markerData['markerLat_' + i]);
                        const markerLng = parseFloat(markerData['markerLong_' + i]);
                        const markerTitle = markerData['markerTitle_' + i];

                        // 랜덤 색상 생성
                        const randomColor = generateRandomColor();

                        // DOM 노드 생성
                        const pointWrapDiv = document.createElement('div');
                        pointWrapDiv.className = 'point_wrap point1';

                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'btn point point_sch';

                        const spanInner = document.createElement('span');
                        spanInner.className = 'point_inner';

                        const image = document.createElement('img');
                        image.src = './img/sch_alarm.png';
                        image.alt = 'Desired Image';
                        image.className = 'btn point point_ing';
                        image.style.width = '24px';
                        image.style.height = '24px';

                        const infoboxDiv = document.createElement('div');
                        infoboxDiv.className = 'infobox1 rounded_04 px_08 py_03 on';

                        const titleSpan = document.createElement('span');
                        titleSpan.className = 'fs_12 fw_800 text_dynamic line_h1_2 mt-2';
                        titleSpan.textContent = markerTitle;

                        // 스타일 DOM 노드 생성
                        const style = document.createElement('style');
                        style.textContent = `
                        .infobox1 {
                            position: absolute !important;
                            left: 50%;
                            top: 100%; 
                            transform: translate(-50%, -80%); 
                            background-color: #413F4A;
                            padding: 0.3rem 0.8rem; 
                            border-radius: 0.4rem;
                            z-index: 5;
                            white-space: nowrap; 
                        }

                        .infobox1 span {
                            color: ${randomColor};
                            font-size: 14px !important;
                            white-space: nowrap !important;
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                        }
                        `;

                        // DOM 노드 연결
                        spanInner.appendChild(image);
                        button.appendChild(spanInner);
                        infoboxDiv.appendChild(titleSpan);
                        pointWrapDiv.appendChild(style);
                        pointWrapDiv.appendChild(button);
                        pointWrapDiv.appendChild(infoboxDiv);

                        // Google Maps 마커 생성 (AdvancedMarkerElement 사용)
                        const {
                            AdvancedMarkerElement
                        } = google.maps.marker;
                        const scheduleMarker = new AdvancedMarkerElement({
                            map: map,
                            position: {
                                lat: markerLat,
                                lng: markerLng
                            },
                            content: pointWrapDiv,
                            zIndex: 1
                        });

                        scheduleMarkers.push(scheduleMarker);
                    }
                }

                // 내 장소 마커 추가
                if (markerData.location_chk === 'Y') {
                    for (let i = 1; i <= markerData.location_count; i++) {
                        const locationLat = parseFloat(markerData['locationmarkerLat_' + i]);
                        const locationLng = parseFloat(markerData['locationmarkerLong_' + i]);
                        const locationTitle = markerData['locationmarkerTitle_' + i];

                        // 랜덤 색상 생성
                        const randomColor = generateRandomColor();

                        // DOM 노드 생성
                        const pointWrapDiv = document.createElement('div');
                        pointWrapDiv.className = 'point_wrap point1';

                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'btn point point_myplc';

                        const spanInner = document.createElement('span');
                        spanInner.className = 'point_inner';

                        const image = document.createElement('img');
                        image.src = './img/loc_alarm.png';
                        image.alt = 'Desired Image';
                        image.className = 'btn point point_ing';
                        image.style.width = '24px';
                        image.style.height = '24px';

                        const infoboxDiv = document.createElement('div');
                        infoboxDiv.className = 'infobox2 rounded_04 px_08 py_03 on';

                        const titleSpan = document.createElement('span');
                        titleSpan.className = 'fs_12 fw_800 text_dynamic line_h1_2 mt-2';
                        titleSpan.textContent = locationTitle;

                        // 스타일 DOM 노드 생성
                        const style = document.createElement('style');
                        style.textContent = `
                        .infobox2 {
                            position: absolute;
                            left: 50%; 
                            top: 100%; 
                            transform: translate(-50%, 40%); 
                            background-color: #413F4A;
                            padding: 0.3rem 0.8rem; 
                            border-radius: 0.4rem;
                            z-index: 5;
                            white-space: nowrap; 
                        }

                        .infobox2 span {
                            color: ${randomColor}; 
                            font-size: 14px !important;
                            white-space: nowrap !important;
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                        }
                        `;

                        // DOM 노드 연결
                        spanInner.appendChild(image);
                        button.appendChild(spanInner);
                        infoboxDiv.appendChild(titleSpan);
                        pointWrapDiv.appendChild(style); // 스타일 노드 추가
                        pointWrapDiv.appendChild(button);
                        pointWrapDiv.appendChild(infoboxDiv);

                        // Google Maps 마커 생성 (AdvancedMarkerElement 사용)
                        const {
                            AdvancedMarkerElement
                        } = google.maps.marker;
                        const locationMarker = new AdvancedMarkerElement({
                            map: map,
                            position: {
                                lat: locationLat,
                                lng: locationLng
                            },
                            content: pointWrapDiv,
                            zIndex: 2
                        });

                        markers.push(locationMarker);
                    }
                }

                // 프로필 마커
                addGoogleProfileMarker(markerData.my_lat, markerData.mt_long, markerData.my_profile);
            } catch (error) {
                console.error("Error in initGoogleMap:", error);
            }
        }

        function addGoogleProfileMarker(lat, lng, imageUrl) {
            // Check if AdvancedMarkerElement is available
            if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
                const {
                    AdvancedMarkerElement
                } = google.maps.marker;

                // Rest of your code remains the same
                const content = document.createElement('div');
                content.className = 'point_wrap';
                const mapUserDiv = document.createElement('div');
                mapUserDiv.className = 'map_user';
                content.appendChild(mapUserDiv);
                const mapRtImgDiv = document.createElement('div');
                mapRtImgDiv.className = 'map_rt_img rounded_14';
                mapUserDiv.appendChild(mapRtImgDiv);
                const rectSquareDiv = document.createElement('div');
                rectSquareDiv.className = 'rect_square';
                mapRtImgDiv.appendChild(rectSquareDiv);
                const img = document.createElement('img');
                img.src = imageUrl;
                img.alt = '이미지';
                img.onerror = function() {
                    this.src = 'https://app.smap.site/img/no_image.png';
                };
                rectSquareDiv.appendChild(img);

                const profileMarker = new AdvancedMarkerElement({
                    map: map,
                    position: {
                        lat: parseFloat(lat),
                        lng: parseFloat(lng)
                    },
                    content: content,
                    zIndex: 2,
                });
                profileMarkers.push(profileMarker);
            } else {
                // Fallback to standard Marker if AdvancedMarkerElement is not available
                const profileMarker = new google.maps.Marker({
                    map: map,
                    position: {
                        lat: parseFloat(lat),
                        lng: parseFloat(lng)
                    },
                    icon: {
                        url: imageUrl,
                        scaledSize: new google.maps.Size(40, 40),
                    },
                    zIndex: 2,
                });
                profileMarkers.push(profileMarker);
            }
        }



        function createLocationMarkerContent(title, lat, lng) {
            // 랜덤 색상 생성
            const randomColor = generateRandomColor();

            // DOM 노드 생성
            const pointWrapDiv = document.createElement('div');
            pointWrapDiv.className = 'point_wrap point1';

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn point point_myplc';

            const spanInner = document.createElement('span');
            spanInner.className = 'point_inner';

            const image = document.createElement('img');
            image.src = './img/loc_alarm.png';
            image.alt = 'Desired Image';
            image.className = 'btn point point_ing';
            image.style.width = '24px';
            image.style.height = '24px';

            const infoboxDiv = document.createElement('div');
            infoboxDiv.className = 'infobox2 rounded_04 px_08 py_03 on';

            const titleSpan = document.createElement('span');
            titleSpan.className = 'fs_12 fw_800 text_dynamic line_h1_2 mt-2';
            titleSpan.textContent = title;

            // 스타일 DOM 노드 생성
            const style = document.createElement('style');
            style.textContent = `
                .infobox2 {
                    position: absolute;
                    left: 50%; 
                    top: 100%; 
                    transform: translate(-50%, 40%); 
                    background-color: #413F4A;
                    padding: 0.3rem 0.8rem; 
                    border-radius: 0.4rem;
                    z-index: 1;
                    white-space: nowrap; 
                }
                
                .infobox2 span {
                    color: ${randomColor};
                    font-size: 14px !important;
                    white-space: nowrap !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                }
            `;

            // DOM 노드 연결
            spanInner.appendChild(image);
            button.appendChild(spanInner);
            infoboxDiv.appendChild(titleSpan);
            pointWrapDiv.appendChild(button);
            pointWrapDiv.appendChild(infoboxDiv);
            pointWrapDiv.appendChild(style); // 스타일 노드 추가

            // 마커 생성 및 반환
            const {
                AdvancedMarkerElement
            } = google.maps.marker;
            const marker = new AdvancedMarkerElement({
                map: map,
                position: {
                    lat: parseFloat(lat),
                    lng: parseFloat(lng)
                },
                content: pointWrapDiv,
                zIndex: 1,
            });

            return marker;
        }

        function addGoogleProfileMarker(lat, lng, imageUrl) {
            // Check if AdvancedMarkerElement is available
            if (google.maps.marker && google.maps.marker.AdvancedMarkerElement) {
                const {
                    AdvancedMarkerElement
                } = google.maps.marker;

                // Rest of your code remains the same
                const content = document.createElement('div');
                content.className = 'point_wrap';
                const mapUserDiv = document.createElement('div');
                mapUserDiv.className = 'map_user';
                content.appendChild(mapUserDiv);
                const mapRtImgDiv = document.createElement('div');
                mapRtImgDiv.className = 'map_rt_img rounded_14';
                mapUserDiv.appendChild(mapRtImgDiv);
                const rectSquareDiv = document.createElement('div');
                rectSquareDiv.className = 'rect_square';
                mapRtImgDiv.appendChild(rectSquareDiv);
                const img = document.createElement('img');
                img.src = imageUrl;
                img.alt = '이미지';
                img.onerror = function() {
                    this.src = 'https://app.smap.site/img/no_image.png';
                };
                rectSquareDiv.appendChild(img);

                const profileMarker = new AdvancedMarkerElement({
                    map: map,
                    position: {
                        lat: parseFloat(lat),
                        lng: parseFloat(lng)
                    },
                    content: content,
                    zIndex: 2,
                });
                profileMarkers.push(profileMarker);
            } else {
                // Fallback to standard Marker if AdvancedMarkerElement is not available
                const profileMarker = new google.maps.Marker({
                    map: map,
                    position: {
                        lat: parseFloat(lat),
                        lng: parseFloat(lng)
                    },
                    icon: {
                        url: imageUrl,
                        scaledSize: new google.maps.Size(40, 40),
                    },
                    zIndex: 2,
                });
                profileMarkers.push(profileMarker);
            }
        }
    </script>
<?php
}
?>
<script>
    $(document).ready(function() {
        // 함수 호출
        f_calendar_log_init('today'); // 달력 스케줄
        f_get_log_location(sgdtMtIdx); // 위치 기록 요약
        initMapAndData(); // 지도 및 데이터 초기화

        // initMapAndData 함수를 사용하여 중복 실행을 방지하고 초기화 로직을 하나로 통합
        async function initMapAndData() {
            try {
                await new Promise(resolve => setTimeout(resolve, 300)); // 300ms 지연
                highlightSelectedDate();
                checkAdCount(); // 광고 표시 여부 확인 및 처리
            } catch (error) {
                console.error("초기화 중 오류 발생:", error);
            }
        }
    });

    async function initializeMapAndMarkers(data, sgdt_idx) {
        if ('ko' === '<?= $userLang ?>') {
            await initNaverMap(data, sgdt_idx);
        } else if ('ko' !== '<?= $userLang ?>') {
            await initGoogleMap(data, sgdt_idx);
        } else {
            throw new Error('지도 API를 초기화할 수 없습니다.');
        }
    }

    function createGradient(steps) {
        const rainbow = [
            '#FF0000', // 빨간색
            '#FFA500', // 주황색
            '#FFFF00', // 노란색
            '#00FF00', // 초록색
            '#0000FF', // 파란색
            '#000080', // 남색
            '#800080', // 보라색
        ];

        // steps가 1일 경우 첫 번째 색상만 반환
        if (steps === 1) {
            return [rainbow[0]];
        }

        const gradientColors = [];
        for (let i = 0; i < steps; i++) {
            const rainbowIndex = Math.floor((i / steps) * (rainbow.length - 1));
            const ratio = (i / steps) * (rainbow.length - 1) - rainbowIndex;

            const color1 = rainbow[rainbowIndex];
            const color2 = rainbow[Math.min(rainbowIndex + 1, rainbow.length - 1)];

            const r = Math.round(parseInt(color1.slice(1, 3), 16) * (1 - ratio) + parseInt(color2.slice(1, 3), 16) * ratio);
            const g = Math.round(parseInt(color1.slice(3, 5), 16) * (1 - ratio) + parseInt(color2.slice(3, 5), 16) * ratio);
            const b = Math.round(parseInt(color1.slice(5, 7), 16) * (1 - ratio) + parseInt(color2.slice(5, 7), 16) * ratio);

            gradientColors.push(`#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`);
        }

        return gradientColors;
    }

    function clearAllMapElements() {
        clearMapElements(profileMarkers);
        clearMapElements(scheduleMarkers);
        clearMapElements(markers);
        clearMapElements(logMarkers); // logMarkers도 clear
        clearPolylines();
    }

    function clearMapElements(elements) {
        if (elements && elements.length > 0) {
            elements.forEach(element => {
                if (element.setMap) {
                    element.setMap(null); // 지도에서 요소 제거
                }
            });
            elements.splice(0, elements.length); // 배열 요소 완전히 제거
        }
    }

    function clearPolylines() {
        if (polylines && polylines.length > 0) {
            polylines.forEach(polyline => {
                if (polyline.setMap) {
                    polyline.setMap(null); // 지도에서 폴리라인 제거
                }
            });
            polylines.splice(0, polylines.length); // 배열 요소 완전히 제거
        }
    }

    function generateRandomColor() {
        const colorSets = [
            '#E6F2FF', // 연한 파란색
            '#D6E6FF', // 연한 라벤더
            '#E5F1FF', // 연한 하늘색
            '#F0F8FF', // 연한 앨리스 블루
            '#E0FFFF', // 연한 민트색
            '#E0F0FF', // 밝은 연한 파란색
            '#E0E6FF', // 밝은 연한 라벤더
            '#E0F0FF', // 밝은 연한 하늘색
            '#E6F0FF', // 밝은 연한 앨리스 블루
            '#E6FFFF' // 밝은 연한 민트색
        ];

        const randomIndex = Math.floor(Math.random() * colorSets.length);
        return colorSets[randomIndex];
    }

    // 두 색상 사이의 중간 색상 계산 함수
    function interpolateColor(color1, color2, ratio) {
        const hex = (number) => {
            const hexStr = number.toString(16);
            return hexStr.length === 1 ? '0' + hexStr : hexStr;
        };

        const r = Math.ceil(parseInt(color1.substring(1, 3), 16) * (1 - ratio) + parseInt(color2.substring(1, 3), 16) * ratio);
        const g = Math.ceil(parseInt(color1.substring(3, 5), 16) * (1 - ratio) + parseInt(color2.substring(3, 5), 16) * ratio);
        const b = Math.ceil(parseInt(color1.substring(5, 7), 16) * (1 - ratio) + parseInt(color2.substring(5, 7), 16) * ratio);

        return '#' + hex(r) + hex(g) + hex(b);
    }

    function updateMemberLocationInfo() {
        return new Promise((resolve, reject) => {
            let form_data = new FormData();
            form_data.append("act", "get_line");
            form_data.append("sgdt_mt_idx", $('#sgdt_mt_idx').val());
            form_data.append("sgdt_idx", $('#sgdt_idx').val());
            form_data.append("event_start_date", currentSelectedDate);
            let ad_data = fetchAdDisplayStatus();

            // 로딩 인디케이터 표시
            showLoadingIndicator();

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
                        // 데이터 처리를 비동기적으로 수행
                        setTimeout(() => {
                            initializeMapAndMarkers(data, $('#sgdt_idx').val());
                            highlightSelectedDate();
                            updateTimeSlider(data.log_count);
                            resolve(data);
                        }, 0);
                    } else {
                        console.log('No data received');
                        reject('No data received');
                    }
                },
                error: function(err) {
                    console.log(err);
                    jalert('타임아웃');
                    reject(err);
                },
                complete: function() {
                    // AJAX 요청 완료 후 로딩 인디케이터 숨기기
                    hideLoadingIndicator();
                }
            });
        });
    }

    function highlightSelectedDate() {
        $('.c_id').removeClass('active');
        $('#calendar_' + currentSelectedDate).addClass('active');
        $('#event_start_date').val(currentSelectedDate);
    }

    function updateTimeSlider(logCount) {
        timeSlider.max = logCount;
        timeSlider.value = 0;
    }

    // 로딩 인디케이터 표시/숨기기 함수
    function showLoadingIndicator() {
        // 로딩 UI 요소를 표시하는 로직
        $('#loadingIndicator').show();
    }

    function hideLoadingIndicator() {
        // 로딩 UI 요소를 숨기는 로직
        $('#loadingIndicator').hide();
    }

    async function f_profile_click(i, sgdt_idx) {
        $('#sgdt_mt_idx').val(i);
        $('#sgdt_idx').val(sgdt_idx);

        // f_calendar_log_init('today');
        f_get_log_location(i);

        // 로딩 인디케이터 표시
        showLoadingIndicator();

        // updateMemberLocationInfo를 비동기적으로 실행
        updateMemberLocationInfo()
            .then(data => {
                // 업데이트된 위치 정보로 지도 이동
                map_panto(data.my_lat, data.mt_long);
            })
            .catch(error => {
                console.error("AJAX 오류:", error);
                showErrorMessage("위치 정보를 업데이트하는 데 문제가 발생했습니다.");
            })
            .finally(() => {
                // 로딩 인디케이터 숨기기
                hideLoadingIndicator();
            });
    }

    function f_day_click(sdate) {
        if (sdate === currentSelectedDate) return; // 이미 선택된 날짜면 아무 것도 하지 않음

        currentSelectedDate = sdate;

        if (typeof(history.pushState) != "undefined") {
            let url = './log?sdate=' + sdate;
            history.pushState(null, '', url);
        } else {
            location.href = url;
        }

        $('#event_start_date').val(sdate);
        $('#schedule-title').text(get_date_t(sdate));

        highlightSelectedDate();
        f_get_log_location($('#sgdt_mt_idx').val());
        updateMemberLocationInfo()
            .then(data => {
                // AJAX 요청 성공 시 data 사용
                map_panto(data.my_lat, data.mt_long);
                // ... data를 활용한 추가 작업 ...
            })
            .catch(error => {
                // AJAX 요청 실패 시 에러 처리
                console.error("AJAX 오류:", error);
            });
    }

    function f_get_log_location(i, s = "") {
        // $('#splinner_modal').modal('toggle');
        let form_data = new FormData();
        form_data.append("act", "location_log");
        if (s) {
            form_data.append("event_start_date", s);
        } else {
            form_data.append("event_start_date", $('#event_start_date').val());
        }

        if (i) {
            form_data.append("mt_idx", i);
            $('#sgdt_mt_idx').val(i);
        }

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
                if (data) {
                    $('#location_log_box').html(data);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }

    function checkAdCount() {
        let ad_data = fetchAdDisplayStatus();
        console.log('log.php - ad_alert : ' + ad_data.ad_alert + ' ad_show : ' + ad_data.ad_show + ' ad_count : ' + ad_data.ad_count);

        try {
            if (ad_data.ad_show == 'Y') {
                requestAdDisplay(ad_data)
                    .then(() => {
                        console.log("Ad shown successfully");
                    })
                    .catch((error) => {
                        console.error("Error in requestAdDisplay:", error);
                    })
                    .finally(() => {
                        updateAdDisplayCount(ad_data);
                        gtag('event', 'log_ad', {
                            'event_category': 'show_log',
                            'event_label': 'show',
                            'user_id': '<?= $_SESSION['_mt_idx'] ?>',
                            'platform': isAndroidDevice() ? 'Android' : (isiOSDevice() ? 'iOS' : 'Unknown')
                        });
                        setTimeout(() => {
                            updateMemberLocationInfo();
                        }, 1000); // 광고 표시 시도 후 1초 뒤에 지도 로드
                    });
            } else {
                updateAdDisplayCount(ad_data);
                updateMemberLocationInfo();
            }
        } catch (err) {
            console.log("Error in checkAdCount: " + err);
            updateAdDisplayCount(ad_data);
            updateMemberLocationInfo();
        }
    }

    function fetchAdDisplayStatus() {
        <?php
        unset($arr_data);
        $arr_data = array(
            "ad_alert" => "N",
            "ad_show" => "N",
            "ad_count" => "0"
        );

        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $mem_row = $DB->getone('member_t');

        // 무료회원인지 확인하고 광고체크하기
        if ((
                $mem_row['mt_level'] == '2'
                // && ($_SESSION['_mt_idx'] == 286 || $_SESSION['_mt_idx'] == 275 || $_SESSION['_mt_idx'] == 281 )
            )
            || $_SESSION['_mt_idx'] == 281
        ) {
            // 무료회원일 경우 광고 카운트 확인하기
            $ad_row = get_ad_log_check($_SESSION['_mt_idx']);
            $ad_count = $ad_row['log_count']; // 현재 광고 수
            $ad_check = $ad_count % 5;

            if ($ad_check == 4) { // 클릭이 5번째일 때
                $arr_data['ad_alert'] = 'Y';
                $arr_data['ad_show'] = 'Y';
            } else {
                $arr_data['ad_alert'] = 'N';
                $arr_data['ad_show'] = 'N';
            }

            $arr_data['ad_count'] = $ad_count;
        }

        $ad_data = json_encode($arr_data);
        ?>
        // console.log(<?= $_SESSION['_mt_idx'] ?>);
        // console.log(<?= $mem_row['mt_level'] ?>);
        // console.log("ad_count :  <?= $ad_count ?>");
        // console.log(<?= $ad_data ?>);
        return <?= $ad_data ?>;
    }

    function requestAdDisplay(data) {
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                try {
                    let message = {
                        type: "showAd",
                    };
                    if (!isAndroidDevice() && !isiOSDevice()) {
                        console.log("Showing desktop ad");
                    } else if (isAndroidDevice()) {
                        window.smapAndroid.showAd();
                        console.log("Showing Android ad");
                    } else if (isiOSDevice()) {
                        window.webkit.messageHandlers.smapIos.postMessage(message);
                        console.log("Showing iOS ad");
                    }
                    console.log("Ad display attempted");
                    resolve();
                } catch (error) {
                    console.error("Error showing ad:", error);

                    // 에러 로그를 서버에 저장
                    saveErrorLog(error);

                    reject(error);
                }
            }, 800); // 0.8초 지연 후 광고 표시 시도
        });
    }

    function saveErrorLog(error) {
        let logData = {
            mt_idx: <?= $_SESSION['_mt_idx'] ?>,
            error_message: error.message,
            error_stack: error.stack,
            user_agent: navigator.userAgent,
            platform: isAndroidDevice() ? 'Android' : (isiOSDevice() ? 'iOS' : 'Unknown'),
            timestamp: new Date().toISOString()
        };

        $.ajax({
            url: "./save_ad_error_log.php",
            type: "POST",
            data: JSON.stringify(logData),
            contentType: "application/json",
            success: function(response) {
                console.log("Error log saved successfully:", response);
            },
            error: function(xhr, status, error) {
                console.error("Failed to save error log:", error);
            }
        });
    }

    function updateAdDisplayCount(data) {
        let form_data = new FormData();
        form_data.append("act", "show_ad_log");
        // form_data.append("log_count", data.ad_count);
        form_data.append("mt_idx", <?= $_SESSION['_mt_idx'] ?>);

        $.ajax({
            url: "./show_ad_log_update",
            enctype: "multipart/form-data",
            data: form_data,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 5000
        });
    }
    //손으로 바텀시트 움직이기
    document.addEventListener("DOMContentLoaded", function() {
        // console.log('bottom');
        let startY = 0;
        let isDragging;

        if (optBottom) {
            optBottom.addEventListener("touchstart", function(event) {
                startY = event.touches[0].clientY; // 터치 시작 좌표 저장
            });
            optBottom.addEventListener("touchmove", function(event) {
                let currentY = event.touches[0].clientY; // 현재 터치 좌표
                let deltaY = currentY - startY; // 터치 움직임의 차이 계산

                // 움직임이 일정 값 이상이면 보이거나 숨김
                if (Math.abs(deltaY) > 50) {
                    let isVisible = deltaY < 0; // deltaY가 음수면 보이게, 양수면 숨기게
                    let newTransformValue = isVisible ? "translateY(0)" : "translateY(42.5%)";
                    optBottom.style.transform = newTransformValue;
                }
            });


            optBottom.addEventListener('mousedown', function(event) {
                startY = event.clientY; // 클릭 시작 좌표 저장
                isDragging = true;
            });

            document.addEventListener('mousemove', function(event) {
                if (isDragging) {
                    let currentY = event.clientY; // 현재 마우스 좌표
                    let deltaY = currentY - startY; // 움직임의 차이 계산

                    // 움직임이 일정 값 이상이면 보이거나 숨김
                    if (Math.abs(deltaY) > 50) {
                        let isVisible = deltaY < 0; // deltaY가 음수면 보이게, 양수면 숨기게
                        let newTransformValue = isVisible ? 'translateY(0)' : 'translateY(42.5%)';
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

    async function map_panto(lat, lng) {
        if (optBottom) {
            const transformY = optBottom.style.transform;
            if (transformY === 'translateY(0px)') {
                if (typeof naver !== 'undefined' && map instanceof naver.maps.Map) {
                    map.setCenter(new naver.maps.LatLng(lat, lng));
                    map.panBy(new naver.maps.Point(0, 180));
                } else if (typeof google !== 'undefined' && map instanceof google.maps.Map) {
                    // 애니메이션 없이 중심 변경
                    map.setOptions({
                        animation: null
                    }); // 애니메이션 비활성화
                    map.setCenter({
                        lat: parseFloat(lat),
                        lng: parseFloat(lng)
                    });

                    // translateY(0px)일 때 한 번만 panBy 실행
                    if (isPannedDown) {
                        google.maps.event.addListenerOnce(map, 'idle', function() {
                            map.panBy(0, 180);
                            isPannedDown = true; // panBy 실행 여부 업데이트
                        });
                    }
                }
            } else if (isPannedDown) {
                // translateY(0px)에서 다른 값으로 변경되었을 때 원래대로 복귀
                if (typeof google !== 'undefined' && map instanceof google.maps.Map && originalCenter) {
                    map.setOptions({
                        animation: null
                    }); // 애니메이션 비활성화
                    map.setCenter(originalCenter);
                    isPannedDown = false;
                    originalCenter = null;
                }
            }
        }
    }

    // MutationObserver 설정
    let previousTransformY = optBottom.style.transform; // 이전 transformY 값 저장
    let isPanning = false; // 패닝 중인지 확인하는 플래그
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.attributeName === 'style' && optBottom.style.transform !== previousTransformY) {
                previousTransformY = optBottom.style.transform;
                if (previousTransformY === 'translateY(0px)') {
                    if (!isPanning) panMapDown();
                } else {
                    if (!isPanning) panMapUp();
                }
            }
        });
    });

    function panMapDown() {
        isPanning = true;
        originalCenter = map.getCenter();
        let newLat = 'ko' === '<?= $userLang ?>' ? (currentLat || originalCenter.lat()) - (300 / 111000) * 1.05 : (currentLat || originalCenter.lat()) - (300 / 111000) * 1.5;
        let newCenter = 'ko' === '<?= $userLang ?>' ? new naver.maps.LatLng(newLat, currentLng || originalCenter.lng()) : new google.maps.LatLng(newLat, currentLng || originalCenter.lng());
        if ('ko' === '<?= $userLang ?>') {
            map.panTo(newCenter, {
                duration: 700,
                easing: 'easeOutCubic',
                complete: function() {
                    isPanning = false;
                    isPannedDown = true;
                }
            });
        } else {
            map.setOptions({
                animation: null
            });
            map.setCenter(newCenter);
            isPanning = false;
            isPannedDown = true;
        }
    }

    function panMapUp() {
        if (isPannedDown) {
            isPanning = true;
            let targetLatLng = currentLat ? ('ko' === '<?= $userLang ?>' ? new naver.maps.LatLng(currentLat, currentLng) : new google.maps.LatLng(currentLat, currentLng)) : originalCenter;
            if ('ko' === '<?= $userLang ?>') {
                map.panTo(targetLatLng, {
                    duration: 700,
                    easing: 'easeOutCubic',
                    complete: function() {
                        isPanning = false;
                        isPannedDown = false;
                        originalCenter = null;
                    }
                });
            } else {
                if (originalCenter) {
                    map.setOptions({
                        animation: null
                    });
                    map.setCenter(targetLatLng);
                    isPanning = false;
                    isPannedDown = false;
                    originalCenter = null;
                }
            }
        }
    }

    // 감시 시작
    observer.observe(optBottom, {
        attributes: true,
        attributeFilter: ['style']
    });

    //손으로 바텀시트 움직이기
    document.addEventListener('DOMContentLoaded', function() {
        let startY = 0;
        let isDragging;

        if (optBottom) {
            optBottom.addEventListener('touchstart', function(event) {
                startY = event.touches[0].clientY; // 터치 시작 좌표 저장
            });

            optBottom.addEventListener('touchmove', function(event) {
                let currentY = event.touches[0].clientY; // 현재 터치 좌표
                let deltaY = currentY - startY; // 터치 움직임의 차이 계산

                // 움직임이 일정 값 이상이면 보이거나 숨김
                if (Math.abs(deltaY) > 50) {
                    let isVisible = deltaY < 0; // deltaY가 음수면 보이게, 양수면 숨기게
                    let newTransformValue = isVisible ? 'translateY(0)' : 'translateY(<?= $translateY ?>%)';
                    optBottom.style.transform = newTransformValue;
                }
            });

            optBottom.addEventListener('mousedown', function(event) {
                startY = event.clientY; // 클릭 시작 좌표 저장
                isDragging = true;
            });

            document.addEventListener('mousemove', function(event) {
                if (isDragging) {
                    let currentY = event.clientY; // 현재 마우스 좌표
                    let deltaY = currentY - startY; // 움직임의 차이 계산

                    // 움직임이 일정 값 이상이면 보이거나 숨김
                    if (Math.abs(deltaY) > 50) {
                        let isVisible = deltaY < 0; // deltaY가 음수면 보이게, 양수면 숨기게
                        let newTransformValue = isVisible ? 'translateY(0)' : 'translateY(<?= $translateY ?>%)';
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
    // 실시간 마커 이동
    function marker_reload(sgdt_idx) {
        let form_data = new FormData();
        form_data.append("act", "marker_reload");
        form_data.append("sgdt_idx", sgdt_idx);
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
            dataType: 'json',
            success: function(data) {
                if (data) {
                    let my_profile = data.my_profile;
                    let st_lat = data.my_lat;
                    let st_lng = data.mt_long;

                    initNaverMap(my_profile, st_lat, st_lng, data);
                } else {
                    console.log(err);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }

    function isAndroidDevice() {
        if (/Android/i.test(navigator.userAgent) && typeof window.smapAndroid !== 'undefined') {
            console.log('Android!!');
        }
        return /Android/i.test(navigator.userAgent) && typeof window.smapAndroid !== 'undefined';
    }

    function isiOSDevice() {
        if (/iPhone|iPad|iPod/i.test(navigator.userAgent) && window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.smapIos) {
            console.log('iOS!!');
        }
        return /iPhone|iPad|iPod/i.test(navigator.userAgent) && window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.smapIos;
    }

    // setInterval(() => {
    //     let sgdt_idx = $('#sgdt_idx').val();
    //     marker_reload(sgdt_idx);
    // }, 30000);
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>