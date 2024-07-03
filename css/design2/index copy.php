<?php
$b_menu = "1";
$_GET['hd_num'] = '1';
include_once("./inc/head.php");
?>
<style>
    html {
        height: 100%;
        overflow-y: unset !important;
    }

    .head_01 {
        background-color: #FBFBFF;
    }

    .idx_pg {
        position: fixed;
        top: 0;
        left: 50%;
        width: 100%;
        height: 100%;
        max-width: 50rem;
        width: 100%;
        transform: translateX(-50%);
    }

    .opt_bottom {
        transition: transform 0.1s ease;
        -webkit-overflow-scrolling: touch;
        overflow-y: auto;
        max-height: 80vh;
        /* 최대 높이 설정 */
    }
</style>

<div class="container-fluid idx_pg bg_main px-0 py-0 h-100">
    <div class="idx_pg_div">
        <section class="main_top">
            <div>
                <div class="px_16 py-3 bg-white top_weather">
                    <div class="d-flex align-items-center p_address">
                        <p class="fs_12 text_light_gray fw_500 text_dynamic">여의도동 ·</p>
                        <p class="fs_12 text_light_gray fw_500 text_dynamic">서울시</p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div class="date_weather d-flex align-items-center flex-wrap">
                            <div class="fs_14 fw_600 text_dynamic mr-1 mt_08">2023.09.06(수)</div>
                            <div class="d-flex align-items-center mt_08 mr-3">
                                <p><img src="./img/weather_8.png" width="18px" alt="날씨" /></p>
                                <p class="ml-1 fs_11 fw_600 text-text fw_500 mr-2"><span class="fs_11 text_light_gray mr-1">강수확률</span>30%</p>
                                <p class="ml-1 fs_11 fw_600 text-text fw_500 mr-2"><span class="fs_11 text_light_gray mr-1">최저</span>2°C</p>
                                <p class="ml-1 fs_11 fw_600 fc_red fw_500"><span class="fs_11 text_light_gray mr-1">최고</span>2°C</p>
                                <!-- 로딩할때 사용해주세요 -->
                                <!-- <div class="loader loader_sm mr-2 ml-2"></div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- 지도 wrap -->
        <section class="idx_map_wrap">
            <div class="map_inner">
                <div class="banner">
                    <div class="banner_inner">
                        <!-- Swiper -->
                        <div class="swiper mySwiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="bner_txt">
                                        <p class="text-primary fs_13 mr-2"><i class="xi-info"></i></p>
                                        <p class="text_dynamic fs_12 fw_500 line_h1_3">그룹을 생성하고 일정을 입력하면 모든 준비가 완료됩니다.!그룹을 생성하고 일정을 입력하면 모든 준비가 완료됩니다.!</p>
                                    </div>
                                    <div class="">
                                        <div class="rect_bner">
                                            <img src="./img/banner_sp.png" alt="배너이미지" />
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="bner_txt">
                                        <p class="text-primary fs_13 mr-2"><i class="xi-info"></i></p>
                                        <p class="text_dynamic fs_12 fw_500 line_h1_3">그룹을 생성하고 일정을 입력하면 모든 준비가 완료됩니다.!</p>
                                    </div>
                                    <div class="">
                                        <div class="rect_bner">
                                            <img src="./img/banner_sp.png" alt="배너이미지" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
                <!-- 사용자 마커 -->
                <div class="point_wrap point5">
                    <div class="map_user">
                        <div class="map_rt_img rounded_14">
                            <div class="rect_square">
                                <img src="./img/no_image.png" alt="이미지" />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 일정 마커 -->
                <div class="point_wrap point1">
                    <button type="button" class="btn point point_done">
                        <span class="point_inner">
                            <span class="point_txt">1</span>
                        </span>
                    </button>
                    <div class="infobox rounded-sm bg-white px_08 py_08">
                        <p class="fs_8 text_dynamic">오후 6:00 ~ 오후 8:30</p>
                        <p class="fs_12 fw_500 text_dynamic line_h1_2 mt-2">미술학원</p>
                    </div>
                </div>
                <div class="point_wrap point2">
                    <button type="button" class="btn point point_ing">
                        <span class="point_inner">
                            <span class="point_txt">2</span>
                        </span>
                    </button>
                    <div class="infobox rounded-sm bg-white px_08 py_08">
                        <p class="fs_8 text_dynamic">오후 6:00 ~ 오후 8:30</p>
                        <p class="fs_12 fw_500 text_dynamic line_h1_2 mt-2">미술학원</p>
                    </div>
                </div>
                <div class="point_wrap point3">
                    <button type="button" class="btn point point_gonna">
                        <span class="point_inner">
                            <span class="point_txt">3</span>
                        </span>
                    </button>
                    <div class="infobox rounded-sm bg-white px_08 py_08">
                        <p class="fs_8 text_dynamic">오후 6:00 ~ 오후 8:30</p>
                        <p class="fs_12 fw_500 text_dynamic line_h1_2 mt-2">미술학원</p>
                    </div>
                </div>

                <!-- 마커 완료/ 진행중/ 예정에 따른 클래스명 입니다. *.point1, .point2, .point3 이거는 퍼블때 보기위함이라 없어도됩니다.**
                    .point.point_done : 완료
                    .point.point_ing : 진행중
                    .point.point_gonna : 예정
                -->
            </div>
        </section>
        <!-- G-2 위치 페이지 -->
        <div class="opt_bottom">
            <div class="top_bar_wrap text-center pt_08">
                <img src="./img/top_bar.png" class="top_bar" width="34px" alt="탑바" />
                <img src="./img/btn_tl_arrow.png" class="top_down mx-auto" width="12px" alt="탑업" />
            </div>
            <div class="">
                <!--프로필-->
                <div class="mem_wrap">
                    <div class="d-flex tab_scroll scroll_bar_x">
                        <div class="checks mem_box w_fit mr_12 ">
                            <label>
                                <input type="radio" name="rd2" checked>
                                <div class="prd_img mx-auto">
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지" />
                                    </div>
                                </div>
                                <!-- 처음은 사용자 본인이 나옵니다. -->
                                <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">나</p>
                            </label>
                        </div>
                        <div class="checks mem_box w_fit mr_12">
                            <label>
                                <input type="radio" name="rd2">
                                <div class="prd_img mx-auto on_arm"> <!-- 알림왔을 때 on_arm 추가 -->
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지" />
                                    </div>
                                </div>
                                <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">최지유</p>
                            </label>
                        </div>
                        <div class="checks mem_box w_fit mr_12">
                            <label>
                                <input type="radio" name="rd2">
                                <div class="prd_img mx-auto">
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지" />
                                    </div>
                                </div>
                                <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">지우지우지우</p>
                            </label>
                        </div>
                        <div class="mem_box w_fit mr_12" data-toggle="modal" data-target="#link_modal">
                            <button class="btn mem_add mx-auto">
                                <i class="xi-plus-min fs_20"></i>
                            </button>
                            <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic">그룹원 추가</p>
                        </div>
                    </div>
                </div>
                <!-- 일정리스트 -->
                <div class="task_wrap">
                    <div class="border bg-white rounded-lg mb-3">
                        <div class="task_header px_16 py_16 border-bottom">
                            <div class="task_header_tit">
                                <p class="fs_14 fw_700 line_h1_2 mr-3">현재 위치</p>
                                <div class="d-flex align-items-center justify-content-end">
                                    <p class="fc_green fs_11 mr-3"><span class="mr-2">이동중</span> 3km/h</p>
                                    <p class="d-flex fc_bf8639 fs_11"><span class="d-flex align-items-center flex-shrink-0 mr-2"><img src="./img/battery.png" width="13px" class="battery_img" alt="베터리시용량"></span> 91%</p>
                                </div>
                            </div>
                            <p class="fs_12 fw_500 text_light_gray text_dynamic line_h1_3 mt-2">서울 영등포구 선유로 11길</p>
                        </div>
                        <div class="task_body px_16 py_16">
                            <div class="task_body_tit">
                                <p class="fs_14 fw_700 line_h1_2">일정<span class="text_light_gray fs_12 ml-2">(8개)</span></p>
                                <button type="button" class="btn fs_11 fw_500 h-auto w-auto text-primary">최적경로 표시하기<i class="xi-angle-right-min fs_12"></i></button>
                            </div>
                            <div class="task_body_cont">
                                <div class="">
                                    <!-- Swiper -->
                                    <div class="swiper task_slide">
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide task_point_box">
                                                <div class="task point_done">
                                                    <span class="point_inner">
                                                        <span class="point_txt">1</span>
                                                    </span>
                                                </div>
                                                <p class="text_lightgray fs_10 mt-1 status_txt">완료</p>
                                            </div>
                                            <div class="swiper-slide optimal_box">
                                                <p class="fs_23 fw_700 optimal_time">2<span class="fs_14">분</span></p>
                                                <p class="fs_12 text_light_gray optimal_tance">164m</p>
                                            </div>
                                            <div class="swiper-slide task_point_box">
                                                <div class="task point_ing">
                                                    <span class="point_inner">
                                                        <span class="point_txt">2</span>
                                                    </span>
                                                </div>
                                                <p class="text_lightgray fs_10 mt-1 status_txt">진행중</p>
                                            </div>
                                            <div class="swiper-slide optimal_box">
                                            </div>
                                            <div class="swiper-slide task_point_box">
                                                <div class="task point_gonna">
                                                    <span class="point_inner">
                                                        <span class="point_txt">3</span>
                                                    </span>
                                                </div>
                                                <p class="text_lightgray fs_10 mt-1 status_txt"></p>
                                            </div>
                                            <div class="swiper-slide optimal_box"></div>
                                            <div class="swiper-slide task_point_box">
                                                <div class="task point_gonna">
                                                    <span class="point_inner">
                                                        <span class="point_txt">4</span>
                                                    </span>
                                                </div>
                                                <p class="text_lightgray fs_10 mt-1 status_txt"></p>
                                            </div>
                                            <div class="swiper-slide optimal_box"></div>
                                            <div class="swiper-slide task_point_box">
                                                <div class="task point_gonna">
                                                    <span class="point_inner">
                                                        <span class="point_txt">5</span>
                                                    </span>
                                                </div>
                                                <p class="text_lightgray fs_10 mt-1 status_txt"></p>
                                            </div>
                                            <div class="swiper-slide optimal_box"></div>
                                            <div class="swiper-slide task_point_box">
                                                <div class="task point_gonna">
                                                    <span class="point_inner">
                                                        <span class="point_txt">6</span>
                                                    </span>
                                                </div>
                                                <p class="text_lightgray fs_10 mt-1 status_txt"></p>
                                            </div>
                                            <div class="swiper-slide optimal_box"></div>
                                            <div class="swiper-slide task_point_box">
                                                <div class="task point_gonna">
                                                    <span class="point_inner">
                                                        <span class="point_txt">7</span>
                                                    </span>
                                                </div>
                                                <p class="text_lightgray fs_10 mt-1 status_txt"></p>
                                            </div>
                                            <div class="swiper-slide optimal_box"></div>
                                            <div class="swiper-slide task_point_box">
                                                <div class="task point_gonna">
                                                    <span class="point_inner">
                                                        <span class="point_txt">8</span>
                                                    </span>
                                                </div>
                                                <p class="text_lightgray fs_10 mt-1 status_txt"></p>
                                            </div>
                                        </div>
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>
                                <!--선택한 그룹원의 금일 등록된 일정이 없을 경우 - 일정 추가 버튼 표시되고 클릭시 일정추가 페이지로 이동-->
                                <div class="pt-4">
                                    <button type="button" class="btn w-100 rounded add_sch_btn" onclick="location.href='.php'"><i class="xi-plus-min mr-3"></i> 일정을 추가해보세요!</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 초대링크로 가입하셨나요? 플러팅 -->
<!-- <div class="floating_wrap on">
    <div class="flt_inner">
        <div class="flt_head">
            <div></div>
            <button type="button" class="btn p-0 h-auto w-auto flex-shrink-0 flt_close"><img src="./img/ico_close_ligray.png" width="24px" alt="닫기"/></button>
        </div>
        <div class="flt_body pb-5 d-flex align-items-start justify-content-between" style="margin-top: -1rem;">
            <div>
                <p class="fc_3d72ff fs_14 fw_700 text-primary">환영합니다.</p>
                <p class="text_dynamic line_h1_3 fs_17 fw_700 mt-2">친구에게 받은
                    <span class="text-primary">초대링크</span>로 가입하셨나요?
                </p>
                <p class="text_dynamic line_h1_3 text_gray fs_14 mt-3 fw_500">그룹원을 추가하면 실시간 위치 조회를 할 수 있어요.</p>
            </div>
            <img src="./img/send_img.png" class="flt_img_send" width="66px" alt="초대링크"/>
        </div>
        <div class="flt_footer flt_footer_b">
            <div class="d-flex align-items-center w-100 mx-0 my-0">
                <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0 flt_close" data-dismiss="modal" aria-label="Close">아니요</button>
                <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="location.href='invitation_code.php'">네</button>
            </div>
        </div>
    </div>
</div> -->


<!-- 그룹만들기 플러팅 -->
<!-- <div class="floating_wrap on">
    <div class="flt_inner">
        <div class="flt_head">
            <p class="line_h1_2 flt_badge"><span class="text_dynamic">그룹만들기</span></p>
            <button type="button" class="btn p-0 h-auto w-auto flex-shrink-0 flt_close"><img src="./img/ico_close_ligray.png" width="24px" alt="닫기"/></button>
        </div>
        <div class="flt_body pb-5 pt-3">
            <p class="text_dynamic line_h1_3 fs_17 fw_700">친구들과 함께할
                <span class="text=primary">나만의 그룹을</span> 만들어 볼까요?
            </p>
            <p class="text_dynamic line_h1_3 text_gray fs_14 mt-2 fw_500">그룹원을 추가하면 실시간 위치 조회를 할 수 있어요.</p>
        </div>
        <div class="flt_footer">
            <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0" data-dismiss="modal" aria-label="Close">다음</button>
        </div>
    </div>
</div> -->

<script>
    // 일정 슬라이드
    var swiper = new Swiper(".task_slide", {
        slidesPerView: 7,
        pagination: {
            el: ".task_slide .swiper-pagination",
            clickable: true,
        },
    });
    /*
    document.addEventListener('DOMContentLoaded', function() {
        var topBarWrap = document.querySelector('.opt_bottom');
        var startY;
        console.log(topBarWrap);
        if (topBarWrap) {
            topBarWrap.addEventListener('touchstart', function(event) {
                startY = event.touches[0].clientY;
                console.log(startY);
            });
            topBarWrap.addEventListener('touchmove', function(event) {
                var deltaY = event.touches[0].clientY - startY;
                console.log(deltaY);
            });
        } else {
            console.error("요소를 찾을 수 없습니다.");
        }
    });
    */
    document.addEventListener('DOMContentLoaded', function() {
        var startY = 0;
        var startTranslateY = 0;
        var deltaY = 0;
        var newTransformValue;
        var isVisible = false;

        var optBottom = document.querySelector('.opt_bottom');
        var topBar = document.querySelector('.top_bar_wrap');
        var memWrap = document.querySelector('.mem_wrap');

        if (optBottom && topBar) {
            topBar.addEventListener('touchstart', function(event) {
                startY = event.touches[0].clientY; // 터치 시작 좌표 저장
                startTranslateY = parseFloat(getComputedStyle(optBottom).transform.split(',')[5]); // 현재 translateY 값 저장
            });

            topBar.addEventListener('touchmove', function(event) {
                event.preventDefault(); // 페이지 리로드 이벤트 중단
                var currentY = event.touches[0].clientY; // 현재 터치 좌표
                deltaY = currentY - startY; // 터치 움직임의 차이 계산

                // 계산된 translateY 값을 기준으로 터치 이동 거리만큼 더함
                var translateY = startTranslateY + deltaY;
                // 최소값 0, 최대값 289로 제한
                translateY = Math.max(0, Math.min(289, translateY));

                newTransformValue = 'translateY(' + translateY + 'px)';
                optBottom.style.transform = newTransformValue;

                // 움직임이 일정 값 이상이면 보이거나 숨김
                if (Math.abs(deltaY) > 50) {
                    isVisible = deltaY < 0; // deltaY가 음수면 보이게, 양수면 숨기게
                    newTransformValue = isVisible ? 'translateY(0)' : 'translateY(70%)';
                    //optBottom.style.transform = newTransformValue;
                }
            });
            topBar.addEventListener('touchend', function(event) {
                var currentTransformValue = newTransformValue;
                optBottom.style.transform = currentTransformValue;
            });
        }
        if (optBottom && memWrap) {
            memWrap.addEventListener('touchstart', function(event) {
                startY = event.touches[0].clientY; // 터치 시작 좌표 저장
                startTranslateY = parseFloat(getComputedStyle(optBottom).transform.split(',')[5]); // 현재 translateY 값 저장

                // 값이 NaN인 경우 0으로 설정
                if (isNaN(startTranslateY)) {
                    startTranslateY = 0;
                }
            });

            memWrap.addEventListener('touchmove', function(event) {
                event.preventDefault(); // 페이지 리로드 이벤트 중단
                var currentY = event.touches[0].clientY; // 현재 터치 좌표
                deltaY = currentY - startY; // 터치 움직임의 차이 계산

                // 계산된 translateY 값을 기준으로 터치 이동 거리만큼 더함
                var translateY = startTranslateY + deltaY;
                // 최소값 0, 최대값 289로 제한
                translateY = Math.max(0, Math.min(289, translateY));

                newTransformValue = 'translateY(' + translateY + 'px)';
                optBottom.style.transform = newTransformValue;

                // 움직임이 일정 값 이상이면 보이거나 숨김
                if (Math.abs(deltaY) > 50) {
                    isVisible = deltaY < 0; // deltaY가 음수면 보이게, 양수면 숨기게
                    newTransformValue = isVisible ? 'translateY(0)' : 'translateY(70%)';
                    //optBottom.style.transform = newTransformValue;
                }
            });
            memWrap.addEventListener('touchend', function(event) {
                var currentTransformValue = newTransformValue;
                optBottom.style.transform = currentTransformValue;
            });
        }
    });
    /*
    // 바텀시트 업다운
    $('.opt_bottom .top_bar_wrap').click(function() {
        $('.opt_bottom').toggleClass('on');
    });
    */

    // 지도 마커클릭시 상세내역 보여짐
    $('.point_wrap').click(function() {
        $('.point_wrap').click(function() {
            $(this).find('.infobox').addClass('on');
            $('.point_wrap').not(this).find('.infobox').removeClass('on');
        });
    });

    // 문서 전체를 클릭했을 때 마커 상세내역 사라짐
    $(document).click(function(event) {
        if (!$(event.target).closest('.point_wrap, .infobox').length) {
            $('.point_wrap .infobox').removeClass('on');
        }
    });

    //스와이퍼
    var swiper = new Swiper(".mySwiper", {
        //     autoplay: {
        //         delay: 2500,
        //         disableOnInteraction: false,
        //   },
        pagination: {
            el: ".swiper-pagination",
            type: "fraction",
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
</script>

<?php
include_once("./inc/b_menu.php");
include_once("./inc/tail.php");
?>