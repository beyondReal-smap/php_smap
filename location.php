<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '4';
$h_menu = '5';
$$location_page = '1';
$_SUB_HEAD_TITLE = translate("내장소", $userLang);
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/b_menu.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert(translate('로그인이 필요합니다.', $userLang), './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert(translate('다른기기에서 로그인 시도 하였습니다.\n 다시 로그인 부탁드립니다.', $userLang), './logout');
    }
}

if ($_GET['sdate'] == '') {
    $_GET['sdate'] = date('Y-m-d');
}

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

    /* 로딩 화면 스타일 */
    #map-loading {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .dots-spinner {
        display: flex;
        gap: 10px;
    }

    .dot {
        width: 8px;
        height: 8px;
        background-color: #0046FE;
        border-radius: 50%;
        animation: dot-bounce 1s infinite ease-in-out;
    }

    .dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes dot-bounce {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.5);
        }
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
            <!-- 로딩 화면 추가 -->
            <div id="map-loading" style="display: none;">
                <div class="dots-spinner">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>
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
                                    <p class=" fc_gray_700"><span class="pr-3"><img src="./img/ico_search.png" width="14px" alt="검색" /></span><?= translate('지번, 도로명, 건물명으로 검색', $userLang) ?></p>
                                </div>
                            </li>
                            <li class="d-flex">
                                <div class="name flex-fill">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fs_12 fw_600 text-primary"><?= translate('선택한 위치', $userLang) ?></span>
                                        <!-- <a class="fc_gray_900 fs_12 fw_500" href="javascript:f_modal_map_search();">주소검색하기 <i class="xi-angle-right-min"></i></a> -->
                                    </div>
                                    <div class="fs_14 fw_600 fc_gray_600 text_dynamic mt-2 line_h1_3" id="location_add" name="location_add"><?= translate('위치를 선택해주세요', $userLang) ?></div>
                                </div>
                            </li>
                            <!-- .loc_nickname_wp에 .on추가하면 나타탑니다. -->
                            <li class="mt-3 loc_nickname_wp">
                                <div class="name d-flex flex-fill flex-column">
                                    <label class="fs_12 fw_600 text-primary"><?= translate('별칭', $userLang) ?></label>
                                    <input class="fs_14 fw_600 fc_gray_600 form-control text_dynamic mt-1 line_h1_3 loc_nickname" name="slt_title" id="slt_title" value="" placeholder="<?= translate('별칭을 입력해주세요', $userLang) ?>" style="word-break: break-all;">
                                </div>
                            </li>
                        </ul>
                    </div>
                    <!-- .myplace_btn에 .on추가하면 버튼이 나타납니다. -->
                    <div class="d-flex align-items-center myplace_btn_wr w-100 mx-0 my-0">
                        <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0 myplace_btn flt_close"><?= translate('닫기', $userLang) ?></button>
                        <!-- <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0 myplace_btn">내장소 저장 횟수 초과</button> -->
                        <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0 myplace_btn" onclick="location_add()"><?= translate('내장소 등록', $userLang) ?></button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- G-2 위치 페이지 [시작]-->
    <?php
    if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) {
        // $translateY = 0;
        $translateY = 82;
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
            <input type="hidden" name="sgt_idx" id="sgt_idx" value="<?= $sgdt_row['sgt_idx'] ?>" />
            <input type="hidden" name="sgdt_idx" id="sgdt_idx" value="<?= $sgdt_row['sgdt_idx'] ?>" />
            <input type="hidden" name="mt_idx" id="mt_idx" value="<?= $sgdt_row['mt_idx'] ?>" />
            <!-- 프로필 tab_scroll scroll_bar_x -->
            <?php if ($sgt_cnt > 0 || $sgdt_leader_cnt > 0) { ?>
                <div class="grp_wrap">
                    <!-- 그룹원 자리 -->
                </div>
            <?php } ?>
            <div id="location_map_list_box" class="px_16">
                <!-- 내장소 리스트 자리 -->
            </div>
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
                    <p class="modal-title line1_text fs_20 fw_700"><?= translate('주소검색', $userLang); ?></p>
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
    let map;
    let selectMarker;
    let markers = []; // 마커들을 저장할 배열
    var profileMarkers = [];
    // 그룹원별 슬라이드 컨테이너를 저장할 객체
    const groupMemberSlides = {};
    let googleMapsLoaded = false;
    let googleMapsLoadPromise = null;
    let geocoder;
    let optBottomSelect;
    let bottomSheetHeight;
    let mapContainer = document.getElementById("map");
    let mapHeight = mapContainer.getBoundingClientRect().height;
    let verticalCenterOffset;
    let currentLat;
    let currentLng;
    let optBottom = document.querySelector(".opt_bottom");
    const loadingElement = document.getElementById('map-loading');
</script>
<?php
// 한국어 사용자를 위한 네이버 지도 API 스크립트
if ($userLang == 'ko') {
?>
    <script>
        map = new naver.maps.Map("map", {
            center: new naver.maps.LatLng(<?= $_SESSION['_mt_lat'] ?>, <?= $_SESSION['_mt_long'] ?>),
            zoom: 16,
            mapTypeControl: false
        });
    </script>
<?php
    // 한국어 이외의 사용자를 위한 구글 지도 API 스크립트
} else {
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
        async function initMap(st_lat, st_lng) {
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

                // 커서 설정
                map.setOptions({
                    cursor: "pointer"
                });
            }

            const mapOptions = {
                center: {
                    lat: parseFloat(st_lat),
                    lng: parseFloat(st_lng)
                },
                zoom: 15,
                mapTypeControl: false,
                mapId: "4afcc0d726ad31e5",
                fullscreenControl: false,
                disableDoubleClickZoom: true,
                clickableIcons: false, // 장소 아이콘 클릭 비활성화
                language: '<?= $userLang ?>'
            };

            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // 추가 옵션 설정
            map.setOptions({
                disableDefaultUI: true, // 기본 UI 비활성화
                gestureHandling: 'greedy' // 스크롤 동작 변경
            });

            geocoder = new google.maps.Geocoder();

            map.addListener("click", function(e) {
                if (selectMarker) {
                    selectMarker.setMap(null);
                }
                searchGoogleCoordinateToAddress(e.latLng);
            });


            // 지도를 50픽셀 위로 이동
            map.panBy(0, -50);

            console.log("Map initialized successfully");
            return map;
        }

        function searchGoogleCoordinateToAddress(latLng) {
            geocoder.geocode({
                location: latLng
            }, (results, status) => {
                if (status === "OK") {
                    if (results.length > 0) {
                        // 마지막 배열의 formatted_address에서 제거할 문자열 추출
                        const stringToRemove = results[results.length - 1].formatted_address;

                        // 각 배열의 formatted_address에서 문자열 제거
                        const updatedResults = results.map(result => {
                            result.formatted_address = result.formatted_address.replace(stringToRemove, '').trim();
                            return result;
                        });

                        // 업데이트된 results 배열 사용
                        console.log(updatedResults);

                        if (results[0]) {
                            const address = results[0].formatted_address;

                            $(".flt_map_pin_wrap").addClass("on");
                            $(".loc_nickname_wp").addClass("on");
                            $('#location_add').html(address);
                            $('#sst_location_add').val(address);
                            $('#sst_location_lat').val(latLng.lat());
                            $('#sst_location_long').val(latLng.lng());
                            $('#slt_title').val('');

                            $('#map_info_box').removeClass('d-none-temp');

                            if (selectMarker) {
                                selectMarker.setMap(null);
                            }
                            selectMarker = new google.maps.Marker({
                                position: latLng,
                                map: map
                            });

                            // Display additional address information
                            let htmlAddresses = [];
                            results.forEach((result, i) => {
                                htmlAddresses.push(`${i + 1}. ${result.formatted_address}`);
                            });
                            htmlAddresses.push(`[GPS] 위도: ${latLng.lat()}, 경도: ${latLng.lng()}`);

                            // You can use htmlAddresses to display the information as needed
                            console.log(htmlAddresses.join('\n'));
                        } else {
                            window.alert("No results found");
                        }
                    } else {
                        window.alert("No results found");
                    }
                } else {
                    window.alert("Geocoder failed due to: " + status);
                }
            });
        }

        // 페이지 로드 시 Google Maps 초기화
        window.addEventListener('load', () => {
            loadGoogleMapsScript().then(() => {
                initMap().catch(error => console.error("Error initializing map:", error));
            }).catch(error => console.error("Error loading Google Maps API:", error));
        });

        window.addEventListener('resize', function() {
            if (map) {
                google.maps.event.trigger(map, 'resize');
            }
        });
    </script>
<?php } ?>
<script>
    $(document).ready(function() {
        createGroupMember(<?= $sgdt_row['sgdt_idx'] ?>);
        createLocationList(<?= $sgdt_row['sgt_idx'] ?>, <?= $sgdt_row['sgdt_idx'] ?>, <?= $sgdt_row['mt_idx'] ?>);
        f_get_box_list2();
        f_get_box_list();
        setTimeout(() => {
            location_map(<?= $sgdt_row['sgdt_idx'] ?>);
            calcScreenOffset();
        }, 100);

    });

    function renderMemberList(data) {
        const grpWrap = $('.grp_wrap');
        grpWrap.empty(); // 기존 내용 삭제

        // 전체 HTML 구조 생성
        const html = `
        <div class="border bg-white rounded-lg px_16 py_16">
            <p class="fs_16 fw_600 mb-3">${'<?= translate('그룹원', $userLang) ?>'}</p>
            <style>
                @keyframes loading {
                    0% {
                        transform: rotate(0deg);
                    }

                    100% {
                        transform: rotate(360deg);
                    }
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
                        ${generateMemberItems(data)}
                    </div>
                </div>
            </div>
        </div>
    `;
        grpWrap.html(html);

        // Swiper 다시 초기화
        mem_swiper = new Swiper(".mem_swiper", {
            slidesPerView: 'auto',
            spaceBetween: 12,
        });
    }

    // 멤버 아이템 생성 함수
    function generateMemberItems(data) {
        let html = '';

        // 본인 정보 추가
        html += `
        <div class="swiper-slide checks mem_box">
            <label>
                <input type="radio" name="rd2" checked onclick="mem_schedule(${data.my_info.sgt_idx}, ${data.my_info.sgdt_idx});">
                <div class="prd_img mx-auto">
                    <div class="rect_square rounded_14">
                        <img src="${data.my_info.profile_image}" onerror="this.src='<?= $ct_no_profile_img_url ?>'" />
                    </div>
                </div>
                <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">${data.my_info.nickname}</p>
            </label>
        </div>
    `;

        // 그룹 멤버 정보 추가
        data.group_members.forEach(member => {
            html += `
            <div class="swiper-slide checks mem_box">
                <label>
                    <input type="radio" name="rd2" onclick="mem_schedule(${member.sgt_idx}, ${member.sgdt_idx});">
                    <div class="prd_img mx-auto"> 
                        <div class="rect_square rounded_14">
                            <img src="${member.profile_image}" alt="프로필이미지" onerror="this.src='<?= $ct_no_profile_img_url ?>'" />
                        </div>
                    </div>
                    <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">${member.nickname}</p>
                </label>
            </div>
        `;
        });

        // 그룹원추가 버튼 추가
        html += `
        <div class="swiper-slide mem_box add_mem_box" ${data.sgt_cnt > 0 ? 'onclick="location.href=\'./group\'"' : 'style="visibility: hidden;"'}>
            <button class="btn mem_add">
                <i class="xi-plus-min fs_20"></i>
            </button>
            <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic" style="word-break: break-all; line-height:0.7;">
                ${'<?= translate(' 그룹원 ', $userLang) ?>'}<br>
                ${'<?= translate(' 추가 ', $userLang) ?>'}
            </p>
        </div>
    `;

        return html;
    }

    function renderLocationList(data) {
        const locationMapListBox = $('#location_map_list_box'); // location_map_list_box 요소 선택
        locationMapListBox.empty(); // 기존 내용 삭제

        // 장소 추가 버튼 HTML
        let html = `
        <div class="trace_box trace_add_place swiper-slide" onclick="map_info_box_show()" style="height: 135px;">
            <div class="trace_box_txt_box text-center" style="height: 91.5px;">
                <p class="fs_13 fw_400 text_dynamic line_h1_4 text-center" style="line-height: 0.7;">${'<?= translate('내장소를', $userLang) ?>'}<br>
                    ${'<?= translate('추가해보세요!', $userLang) ?>'}
                </p>
                <button type="button" class="btn trace_addbtn"></button>
            </div>
        </div>
    `;

        // 내 장소 목록 HTML 생성
        data.slt_list.forEach(slt_row => {
            // slt_row['slt_add']에서 첫 번째 단어를 제거
            let parts = slt_row.slt_add.split(' ');
            parts.shift();
            const slt_add = parts.join(' ');

            html += `
            <div class="trace_box swiper-slide" style="height: 135px;">
                <div class="trace_box_txt_box" onclick="map_panto('${slt_row.slt_lat}','${slt_row.slt_long}')" style="height: 63.5px;">
                    <p class="mr-2">
                        <span class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line1_text line_h1_4 mb-2" style="display: inline-block; max-width: 6em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${slt_row.slt_title}</span>
                    </p>
                    <p class="line2_text fs_13 fw_400 text_dynamic line_h1_4">${slt_add}</p>
                </div>
                <div class="trace_box_btn_box" style="height: 28px;">
                    <button type="button" class="btn trace_armbtn ${slt_row.slt_enter_alarm === 'Y' ? 'on' : ''}" onclick="f_location_alarm_modal('${slt_row.slt_idx}','${slt_row.slt_enter_alarm}')"></button>
                    <button type="button" class="btn trace_binbtn" onclick="f_del_location_modal('${slt_row.slt_idx}')" style="margin-left: 3px;"></button>
                </div>
            </div>
        `;
        });

        // 추천 장소 목록 HTML 생성
        if (data.sgt_cnt > 0 || data.sgdt_leader_cnt > 0) {
            data.rlt_list.forEach(rlt_row => {
                html += `
                <div class="trace_box trace_frt_place swiper-slide">
                    <div class="trace_box_txt_box" onclick="map_panto_recomand('${rlt_row.rlt_lat}','${rlt_row.rlt_long}','${rlt_row.rlt_add1 + rlt_row.rlt_add2}','${rlt_row.rlt_title}')">
                        <p class="mr-2">
                            <span class="fs_13 fc_d58c19 rounded_04 bg_fbf3e8 px_06 py_02 line1_text line_h1_4 mb-2">추천장소</span>
                        </p>
                        <p class="line2_text fs_13 fw_400 text_dynamic line_h1_4">${rlt_row.rlt_add1 + rlt_row.rlt_add2}</p>
                    </div>
                    <div class="d-flex align-items-center trace_box_btn_box">
                        <button type="button" class="btn trace_frtplace_btn" onclick="map_panto_recomand('${rlt_row.rlt_lat}','${rlt_row.rlt_long}','${rlt_row.rlt_add1 + rlt_row.rlt_add2}','${rlt_row.rlt_title}')"></button>
                    </div>
                </div>
            `;
            });
        }

        // locationPointListWrap을 location_map_list_box 아래에 추가
        locationMapListBox.html(`
            <div class="border bg-white rounded-lg px_16 py_16 pb_3">
                <p class="fs_16 fw_600 mb-3">${'<?= translate('리스트', $userLang) ?>'}</p>
                <div class="swiper locSwiper location_point_list_wrap pb-0">
                    <div class="swiper-wrapper lo_grid_wrap">
                        ${html} 
                    </div>
                </div>
            </div>
        `);
        // Swiper 다시 초기화
        loc_swiper = new Swiper(".locSwiper", {
            slidesPerView: 'auto',
            spaceBetween: 10,
        });
    }

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
        createLocationList(sgdt_idx, sgdt_idx, '');
    }
    // 내장소 추가 팝업 띄우기
    function map_info_box_show() {
        $(".flt_map_pin_wrap").addClass("on");
        $(".loc_nickname_wp").addClass("on");
        $('#map_info_box').removeClass('d-none-temp');
    }

    function initNaverMap(my_profile, st_lat, st_lng, markerData) {
        var mapContainer = document.getElementById("map");
        optbottom = document.querySelector('.opt_bottom');
        var bottomSheetHeight = optBottom ? optBottom.getBoundingClientRect().height : 0;
        var mapHeight = mapContainer.getBoundingClientRect().height;
        var verticalCenterOffset = (mapHeight - bottomSheetHeight) / 2 / 2;

        clearAllMapElements();

        currentLat = parseFloat(st_lat);
        currentLng = parseFloat(st_lng);
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
        profileMarkers.push(profileMarker);

        map.setZoom(16); // 줌 레벨을 16으로 초기화

        // 내장소 마커 추가
        for (let i = 1; i <= markerData.count; i++) {
            const locationLat = parseFloat(markerData['markerLat_' + i]);
            const locationLng = parseFloat(markerData['markerLong_' + i]);
            const locationTitle = markerData['markerTitle_' + i];

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
                            transform: translate(-50%, -15px); 
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

            var markerOptions = {
                position: new naver.maps.LatLng(locationLat, locationLng),
                map: map,
                icon: {
                    content: pointWrapDiv,
                    size: new naver.maps.Size(61, 61),
                    origin: new naver.maps.Point(0, 0),
                    anchor: new naver.maps.Point(30, 30)
                }
            };

            var marker = new naver.maps.Marker(markerOptions);
            markers.push(marker); // markers 배열에 마커 추가
        }

        naver.maps.Event.addListener(map, 'click', function(e) {
            if (selectMarker) {
                selectMarker.setMap(null);
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
                    if (selectMarker) {
                        selectMarker.setMap(null);
                    }
                    console.log(htmlAddresses.join('\n'));
                    selectMarker = new naver.maps.Marker({
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

        // 지도 로딩 완료 후 로딩 화면 숨기기
        hideMapLoading();
    }

    async function initGoogleMap(my_profile, st_lat, st_lng, markerData) {
        await loadGoogleMapsScript();

        // sgdt_idx에 해당하는 멤버의 위치 정보를 사용하여 지도 중심 설정
        if (!map) {
            await initMap(st_lat, st_lng);
        } else {
            map.setCenter({
                lat: parseFloat(st_lat),
                lng: parseFloat(st_lng)
            });
        }
        console.log("Google Map initialized with custom data");

        currentLat = parseFloat(st_lat);
        currentLng = parseFloat(st_lng);

        clearAllMapElements();

        // 옵션 바 높이 고려하여 지도 중심 조정 (필요한 경우)
        // var bottomSheetHeight = optBottom ? optBottom.getBoundingClientRect().height : 0;
        // var mapHeight = mapContainer.getBoundingClientRect().height;
        // var verticalCenterOffset = (mapHeight - bottomSheetHeight) / 2 / 2;

        // 본인 프로필 마커 추가 (AdvancedMarkerElement 사용)
        addGoogleProfileMarker(st_lat, st_lng, my_profile);

        map.setZoom(15); // 줌 레벨 16으로 초기화

        // 내 장소 마커 추가
        for (let i = 1; i <= markerData.count; i++) {
            const locationLat = parseFloat(markerData['markerLat_' + i]);
            const locationLng = parseFloat(markerData['markerLong_' + i]);
            const locationTitle = markerData['markerTitle_' + i];

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
                            transform: translate(-50%, -15px); 
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
                zIndex: 1
            });

            markers.push(locationMarker);
        }
        // 지도 로딩 완료 후 로딩 화면 숨기기
        hideMapLoading();
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

    function clearAllMapElements() {
        clearMapElements(profileMarkers);
        clearMapElements(markers);
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

    // 주소 검색 함수 (Geocoding API 사용)
    function searchCoordinateToAddress(latlng) {
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            location: latlng
        }, (results, status) => {
            if (status === "OK" && results[0]) {
                const address = results[0].formatted_address;

                $(".flt_map_pin_wrap").addClass("on");
                $(".loc_nickname_wp").addClass("on");
                $('#location_add').html(address);
                $('#sst_location_add').val(address);
                $('#sst_location_lat').val(latlng.lat());
                $('#sst_location_long').val(latlng.lng());
                $('#slt_title').val('');

                $('#map_info_box').removeClass('d-none-temp');
                if (selectMarker) {
                    selectMarker.setMap(null);
                }
                selectMarker = new google.maps.Marker({
                    position: latlng,
                    map: map
                });
            } else {
                alert("Geocode was not successful for the following reason: " + status);
            }
        });
    }

    function createGroupMember(sgdt_idx) {
        // sessionStorage에서 데이터를 먼저 확인
        let cachedData = sessionStorage.getItem('groupMemberData_' + sgdt_idx);
        if (cachedData) {
            // 캐싱된 데이터가 있으면 사용
            let response = JSON.parse(cachedData);
            if (response.result === 'success') {
                renderMemberList(response.data);
                return; // 함수 종료
            }
        }
        var form_data = new FormData();
        form_data.append("act", "group_member_list");
        form_data.append("group_sgdt_idx", sgdt_idx);

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
            success: function(response) {
                if (response.result === 'success') {
                    // sessionStorage에 데이터 저장
                    sessionStorage.setItem('groupMemberData_' + sgdt_idx, JSON.stringify(response));
                    renderMemberList(response.data);
                } else {
                    alert(response.message);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }

    function createLocationList(sgt_idx, sgdt_idx, mt_idx) {
        var form_data = new FormData();
        form_data.append("act", "location_map_list");
        form_data.append("sgdt_idx", sgdt_idx);
        form_data.append("sgt_idx", sgt_idx);
        form_data.append("mt_idx", mt_idx);

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
            success: function(response) {
                if (response.result === 'success') {
                    renderLocationList(response.data);
                } else {
                    alert(response.message);
                }
                hideMapLoading(); // success 후 무조건 실행
            },
            error: function(err) {
                console.log(err);
                hideMapLoading(); // error 후 무조건 실행
            }
        });
    }

    function location_map(sgdt_idx) {
        showMapLoading();
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

                    if ('ko' === '<?= $userLang ?>') {
                        initNaverMap(my_profile, st_lat, st_lng, data);
                    } else if ('ko' !== '<?= $userLang ?>') {
                        initGoogleMap(my_profile, st_lat, st_lng, data);
                    }

                    map_panto(st_lat, st_lng)
                } else {
                    console.log(err);
                }
            },
            error: function(err) {
                console.log(err);
            },
            complete: function() {
                hideMapLoading(); // AJAX 요청 완료 후 무조건 실행 (success 또는 error 후에도 실행)
            }
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
                    createLocationList($('#sgt_idx').val(), $('#sgdt_idx').val(), '');
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
                    createLocationList($('#sgt_idx').val(), $('#sgdt_idx').val(), '');
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

        optbottom = document.querySelector('.opt_bottom');
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

    // 마커 클릭 이벤트 (infoBox 표시)
    $('.point_wrap').click(function() {
        $(this).find('.infobox').addClass('on');
        $('.point_wrap').not(this).find('.infobox').removeClass('on');
    });

    // 지도 마커클릭시 상세내역 보여짐
    $('.point_wrap').click(function() {
        $('.point_wrap').click(function() {
            $(this).find('.infobox').addClass('on');
            $('.point_wrap').not(this).find('.infobox').removeClass('on');
        });
    });

    // 로딩 화면을 보이게 하는 함수
    function showMapLoading(center = true) {
        const spinnerDots = document.querySelectorAll('.dot'); // 모든 .dot 요소 선택

        // 랜덤 색상 적용
        const randomColor = generateSpinnerColor();
        spinnerDots.forEach(dot => {
            dot.style.backgroundColor = randomColor;
        });

        if (optBottom) {
            var transformY = optBottom.style.transform;
            if (transformY == 'translateY(0px)') {
                // 화면 중앙에서 180px 위로 이동
                // loadingElement.style.top = 'calc(50% - 180px)';
                loadingElement.style.transform = 'translate(0, -25%)';
            }
        }
        loadingElement.style.display = 'flex'; // 로딩바 표시
    }

    // 로딩 화면을 숨기는 함수
    function hideMapLoading() {
        document.getElementById("map-loading").style.display = 'none';
    }

    function generateSpinnerColor() {
        const colorSets = [
            '#FF0000', // 빨간색
            '#FFA500', // 주황색
            '#0000FF', // 파란색
            '#000080', // 남색
            '#800080', // 보라색
        ];

        const randomIndex = Math.floor(Math.random() * colorSets.length);
        return colorSets[randomIndex];
    }

    function map_panto(lat, lng) {
        currentLat = parseFloat(lat);
        currentLng = parseFloat(lng);
        var mapContainer = document.getElementById("map");
        optbottom = document.querySelector('.opt_bottom');
        var bottomSheetHeight = optBottom ? optBottom.getBoundingClientRect().height : 0;
        var mapHeight = mapContainer.getBoundingClientRect().height;
        var verticalCenterOffset = (mapHeight - bottomSheetHeight) / 2 / 2;

        if ('ko' === '<?= $userLang ?>') { // 네이버 지도
            map.setCenter(new naver.maps.LatLng(lat, lng));

            if (optBottom) {
                var transformY = optBottom.style.transform;
                if (transformY == 'translateY(0px)') {
                    map.panBy(new naver.maps.Point(0, verticalCenterOffset));
                }
                map.panBy(new naver.maps.Point(0, verticalCenterOffset));
            }

            // 해당 좌표에 있는 마커를 찾습니다.
            var clickedMarker = findMarkerByPosition(lat, lng);

            // 찾은 마커를 클릭했을 때의 동작을 시뮬레이트합니다.
            if (clickedMarker) {
                naver.maps.Event.trigger(clickedMarker, 'click');
            }

            if (selectMarker) {
                selectMarker.setMap(null);
            }

            selectMarker = new naver.maps.Marker({
                position: new naver.maps.LatLng(lat, lng),
                map: map
            });

        } else if (typeof google !== 'undefined') { // 구글 지도
            map.setCenter({
                lat: parseFloat(lat),
                lng: parseFloat(lng)
            });

            google.maps.event.addListenerOnce(map, 'idle', function() {
                if (optBottom) {
                    var transformY = optBottom.style.transform;
                    if (transformY == 'translateY(0px)') {
                        map.panBy(0, 180);
                    }
                }
            });

            // 구글 지도 마커 클릭 시뮬레이션
            const clickedMarker = findMarkerByPosition(lat, lng);
            if (clickedMarker) {
                google.maps.event.trigger(clickedMarker, 'click');
            }

            // 기존 마커 제거
            if (selectMarker) {
                selectMarker.setMap(null);
            }

            // 새로운 마커 생성
            const {
                AdvancedMarkerElement
            } = google.maps.marker;
            selectMarker = new AdvancedMarkerElement({
                position: {
                    lat: parseFloat(lat),
                    lng: parseFloat(lng)
                },
                map: map,
                // content 속성에 마커에 표시할 내용을 추가해야 합니다. 
                // 예시: content: '<div>마커 내용</div>'
            });
        }

        // 나머지 공통 로직
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
        if ('ko' === '<?= $userLang ?>') {
            map.setCenter(new naver.maps.LatLng(lat, lng));

            optbottom = document.querySelector('.opt_bottom');
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
            if (selectMarker) {
                selectMarker.setMap(null);
            }

            selectMarker = new naver.maps.Marker({
                position: new naver.maps.LatLng(lat, lng),
                map: map
            });
        } else {
            // 지도 중심 이동
            map.setCenter(new google.maps.LatLng(lat, lng));

            // opt_bottom 요소 체크 및 지도 이동
            optbottom = document.querySelector('.opt_bottom');
            if (optBottom) {
                var transformY = optBottom.style.transform;
                if (transformY == 'translateY(0px)') {
                    map.panBy(0, -180); // 위로 180 픽셀 이동 (Google Maps에서는 음수 값 사용)
                }
            }

            // 해당 좌표에 있는 마커를 찾습니다.
            var clickedMarker = findMarkerByPosition(lat, lng);

            // 찾은 마커를 클릭했을 때의 동작을 시뮬레이트합니다.
            if (clickedMarker) {
                google.maps.event.trigger(clickedMarker, 'click');
            }

            // 기존 선택 마커 제거
            if (selectMarker) {
                selectMarker.setMap(null);
            }

            // 새 마커 생성
            selectMarker = new google.maps.Marker({
                position: new google.maps.LatLng(lat, lng),
                map: map
            });
        }
    }

    // 마커를 위치로 찾는 함수
    function findMarkerByPosition(lat, lng) {
        for (var i = 0; i < markers.length; i++) {
            var markerPosition = markers[i].getPosition();
            if (markerPosition.lat() === lat && markerPosition.lng() === lng) {
                return markers[i];
            }
        }
        return null;
    }

    function map_panto_recomand(lat, lng, addr, title) {
        map.setCenter(new naver.maps.LatLng(lat, lng));

        optbottom = document.querySelector('.opt_bottom');
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
        if (selectMarker) {
            selectMarker.setMap(null);
        }

        selectMarker = new naver.maps.Marker({
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
        var tolerance = 0.000001;

        for (var i = 0; i < markers.length; i++) {
            var marker = markers[i];

            if ('ko' === '<?= $userLang ?>') { // 네이버 지도
                var position = marker.getPosition(); // 네이버 지도는 getPosition() 사용
                if (Math.abs(position._lat - lat) < tolerance && Math.abs(position._lng - lng) < tolerance) {
                    return marker;
                }
            } else if (typeof google !== 'undefined') { // 구글 지도
                var position = marker.position; // 구글 지도는 position 속성 사용
                if (Math.abs(position.Fg - lat) < tolerance && Math.abs(position.Hg - lng) < tolerance) {
                    return marker;
                }
            }
        }
        return null;
    }

    function calcScreenOffset() {
        optBottomSelect = document.querySelector('.opt_bottom');
        bottomSheetHeight = optBottomSelect ? optBottomSelect.getBoundingClientRect().height : 0;
        verticalCenterOffset = (mapHeight - bottomSheetHeight) / 2;
    }

    // MutationObserver 설정
    let previousTransformY = optBottom.style.transform; // 이전 transformY 값 저장
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.attributeName === 'style' && optBottom.style.transform !== previousTransformY) {
                previousTransformY = optBottom.style.transform;

                if (previousTransformY === 'translateY(0px)') {
                    panMapDown();
                } else if (isPannedDown) {
                    panMapUp();
                }
            }
        });
    });

    function panMapDown() {
        originalCenter = map.getCenter();
        let newLat = 'ko' === '<?= $userLang ?>' ? (currentLat || originalCenter.lat()) - (300 / 111000) * 1.05 : (currentLat || originalCenter.lat()) - (300 / 111000) * 1.8;
        let newCenter = 'ko' === '<?= $userLang ?>' ? new naver.maps.LatLng(newLat, currentLng || originalCenter.lng()) : new google.maps.LatLng(newLat, currentLng || originalCenter.lng());

        if ('ko' === '<?= $userLang ?>') {
            map.panTo(newCenter, {
                duration: 700,
                easing: 'easeOutCubic'
            });
        } else {
            // map.setOptions({
            //     animation: null
            // });
            // map.setCenter(newCenter);

            // 애니메이션 시간 설정 (밀리초 단위)
            const duration = 700; // 0.7초

            map.setOptions({
                animation: google.maps.Animation.BOUNCE
            });
            map.panTo(newCenter);

            // 애니메이션 시간 이후 애니메이션 옵션 초기화
            setTimeout(() => {
                map.setOptions({
                    animation: null
                });
            }, duration);
        }

        isPannedDown = true;
    }

    function panMapUp() {
        let targetLatLng = currentLat ? ('ko' === '<?= $userLang ?>' ? new naver.maps.LatLng(currentLat, currentLng) : new google.maps.LatLng(currentLat, currentLng)) : originalCenter;

        if ('ko' === '<?= $userLang ?>') {
            map.panTo(targetLatLng, {
                duration: 700,
                easing: 'easeOutCubic',
                onComplete: function() {
                    isPannedDown = false;
                    originalCenter = null;
                }
            });
        } else {
            if (targetLatLng) {
                // map.setOptions({
                //     animation: null
                // });
                // map.setCenter(targetLatLng);
                // 애니메이션 시간 설정 (밀리초 단위)
                const duration = 700; // 0.7초

                map.setOptions({
                    animation: google.maps.Animation.BOUNCE
                });
                map.panTo(targetLatLng);

                // 애니메이션 시간 이후 애니메이션 옵션 초기화
                setTimeout(() => {
                    map.setOptions({
                        animation: null
                    });
                }, duration);



                isPannedDown = false;
                originalCenter = null;
            }
        }
    }

    // 감시 시작
    observer.observe(optBottom, {
        attributes: true,
        attributeFilter: ['style']
    });

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

    // // 커서 설정
    // map.setOptions({
    //     cursor: "pointer"
    // });

    // // 지도 중심 설정
    // map.setCenter({
    //     lat: parseFloat(st_lat),
    //     lng: parseFloat(st_lng)
    // });

    // // 지도 클릭 이벤트 리스너 (주소 검색)
    // map.addListener("click", (e) => {
    //     if (selectMarker) {
    //         selectMarker.setMap(null);
    //     }
    //     searchCoordinateToAddress(e.latLng);
    // });
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>