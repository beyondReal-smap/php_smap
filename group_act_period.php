<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "그룹 활동 기한 설정";
$h_menu = '2';
$_SUB_HEAD_TITLE = "그룹 활동 기한 설정";
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script type="text/javascript" src="https://cdn.polyfill.io/v2/polyfill.min.js"></script>
<style>
    .btn_active {
        color: #fff !important;
        background-color: #343a40 !important;
        border-color: #343a40 !important;
    }

    .btn-light:focus,
    .btn-light.focus,
    .btn-light:hover {
        color: #212529;
        background-color: #f8f9fa;
        border-color: #f8f9fa;
    }


    .btn:focus,
    .btn:active {
        outline: none !important;
        box-shadow: none;
    }


    .picker {
        /* 이렇게 붙여주세요. */
        position: relative;
        overflow: hidden;
        margin: 1rem auto;
        padding: 0;
        color: #252525;
        width: 100%;
    }

    .picker .swiper-container {
        /* 수정 */
        width: 50%;
        height: 135px;
        float: left;
    }

    .picker .swiper-slide-active {
        /* 추가 */
        /* background-color: #EBEBEC; */
        opacity: 1 !important;
    }

    .picker .swiper-slide {
        /* 수정 */
        text-align: center;
        font-size: 2rem;
        /* Center slide text vertically */
        display: flex;
        justify-content: center;
        align-items: center;
        user-select: none;
        opacity: 0.25;
        transition: opacity 0.3s ease;
        cursor: default;
        font-weight: bold;
        -webkit-tap-highlight-color: transparent;
        /* border: 1px solid red; */
        height: 45px !important;
    }

    .vizor {
        background-color: #EBEBEC;
        width: 100%;
        height: 45px;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2rem;
        line-height: 60px;
    }

    .vizor:before,
    .vizor:after {
        display: inline-block;
        line-height: inherit;
        height: 100%;
        position: absolute;
        top: -5px;
        transform: translateX(-50%);
        left: 100px;
    }
</style>
<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 text_dynamic line_h1_3">그룹 활동 기한이 끝나면
            자동으로 그룹에서 나오게 되어,
            위치정보와 일정을 공유할 수 없어요.
        </p>
        <p class="fs_12 fc_gray_600 mt-3 line_h1_2">결정하실 때 주의 깊게 생각해주세요.</p>
        <form action="" class="mt-5">
            <input type="hidden" id="sgdt_idx" name="sgdt_idx" value="<?= $_GET['sgdt_idx'] ?>">
            <input type="hidden" id="sgdt_group_chk" name="sgdt_group_chk" value="Y">
            <input type="hidden" id="pick_type" name="pick_type" value="">
            <input type="hidden" id="pick_result" name="pick_result" value="">
            <p class="tit_h3">그룹 활동 기한</p>
            <div class="checks_wr pt-3">
                <div class="checks pt-2">
                    <label>
                        <input type="checkbox" name="sgdt_group_checkbox" id="sgdt_group_checkbox" checked onchange="toggleSpinner()">
                        <span class=" ic_box"><i class="xi-check-min"></i></span>
                        <div class="chk_p ">
                            <p class="text_dynamic fs_14 fw_700">기한 없음</p>
                        </div>
                    </label>
                </div>
            </div>
            <div class="spin_wrap" id="cal_time_box" style="display: none;">
                <div class="spin_result">
                    <p class="fs_14 fw_700 text_gray cal_time_box"><i class="xi-calendar mr-2"></i><span id="stime_txt"></span></p>
                </div>
                <div class="row mt-3 px-0 mx-0">
                    <div class="picker" id="stime_picker">
                        <div class="vizor"></div>
                        <div class="swiper-container select_times">
                            <div class="swiper-wrapper" id="resultTypeList">
                            </div>
                            <div class=""></div>
                        </div>
                        <div class="swiper-container select_type">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide" data-mm="day">일</div>
                                <div class="swiper-slide" data-mm="minute">분</div>
                                <div class="swiper-slide" data-mm="hour">시간</div>
                                <div class="swiper-slide" data-mm="other_day">일</div>
                                <div class="swiper-slide" data-mm="other_minute">분</div>
                                <div class="swiper-slide" data-mm="other_hour">시간</div>
                            </div>
                            <div class=""></div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function toggleSpinner() {
                    var checkbox = document.getElementById('sgdt_group_checkbox');
                    var spinner = document.getElementById('cal_time_box');

                    // 체크 상태에 따라 스피너 보이기/숨기기
                    if (checkbox.checked) {
                        spinner.style.display = 'none';
                        $('#sgdt_group_chk').val('Y');
                        $('#pick_type').val('');
                        $('#pick_result').val('');
                    } else {
                        spinner.style.display = 'block';
                        $('#sgdt_group_chk').val('N');
                        btn_class_active();
                        f_open_time('day');
                    }
                }
                // 클릭한 버튼 타입에 따라 결과 리스트를 업데이트
                function select_type(type) {
                    var resultList = [];
                    document.getElementById('resultTypeList').innerHTML = "";
                    if (type === 'hour') {
                        for (var i = 1; i < 24; i++) {
                            resultList.push('<div class="swiper-slide" data-hh="' + i + '">' + i + '</div>');
                        }
                    } else if (type === 'day') {
                        for (var i = 1; i < 31; i++) {
                            resultList.push('<div class="swiper-slide" data-hh="' + i + '">' + i + '</div>');
                        }
                    } else if (type === 'minute') {
                        for (var i = 5; i < 60; i++) {
                            resultList.push('<div class="swiper-slide" data-hh="' + i + '">' + i + '</div>');
                        }
                    }
                    // 결과 리스트 업데이트
                    document.getElementById('resultTypeList').innerHTML = resultList.join('');
                }

                function body_scroll_lock() {
                    document.getElementsByTagName('body')[0].style.overflow = 'hidden';
                }

                function body_scroll_visible() {
                    document.getElementsByTagName('body')[0].style.overflow = 'visible';
                }

                function btn_class_active() {
                    $('.btn-c').addClass('btn-light');
                    $('.btn-c').removeClass('btn-danger');
                    $('.btn-c').removeClass('active');
                    $('.btn-c').removeClass('btn_active');
                    $('#schedule_calandar_box_header').hide();
                    $("#schedule_calandar_box").html('');
                    $('.picker').hide();
                }
                // Swiper 인스턴스 제거 함수
                function destroySwiperInstances() {
                    if (window.stimes) {
                        window.stimes.destroy();
                        window.stimes = null;
                    }

                    if (window.stypes) {
                        window.stypes.destroy();
                        window.stypes = null;
                    }
                }

                function f_open_time(t) {
                    // 이미 생성된 Swiper 인스턴스 제거
                    destroySwiperInstances();
                    select_type(t);
                    $('#stime_picker').show();

                    setTimeout(() => {
                        var defaults = {
                            pagination: {
                                el: ".swiper-pagination",
                                clickable: false
                            },
                            slidesPerView: "auto",
                            freeMode: {
                                enabled: true,
                                sticky: true,
                                momentumBounce: true,
                                momentum: true,
                                minimumVelocity: 0.05,
                                momentumBounceRatio: 0.05
                            },
                            loop: true,
                            direction: "vertical",
                            centeredSlides: true,
                            centeredSlidesBounds: true,
                            speed: 600,
                            breakpointsBase: 'container',
                            init: false,
                        };

                        // Swiper 인스턴스 새로 생성
                        window.stimes = new Swiper(
                            ".swiper-container.select_times",
                            Object.assign({}, defaults)
                        );

                        window.stypes = new Swiper(
                            ".swiper-container.select_type",
                            Object.assign({}, defaults)
                        );

                        var sday = 30;
                        var shour = 1;
                        var sminute = 5;

                        stimes.on("init", function() {
                            var stimes_initialSlide;
                            $.each(stimes.slides, function(index, value) {
                                //if (sdhh == value.dataset.hh) {
                                if (t == 'day') {
                                    if (sday == value.dataset.hh) {
                                        stimes_initialSlide = value.dataset.swiperSlideIndex;
                                    }
                                } else if (t == 'minute') {
                                    if (sminute == value.dataset.hh) {
                                        stimes_initialSlide = value.dataset.swiperSlideIndex;
                                    }
                                } else {
                                    if (shour == value.dataset.hh) {
                                        stimes_initialSlide = value.dataset.swiperSlideIndex;
                                    }
                                }
                                // }
                            });
                            stimes.slideToLoop(stimes_initialSlide, 500, $('#swipe_init').val('Y'));
                        });
                        stimes.init();
                        stypes.on("init", function() {
                            var stype_initialSlide;
                            $.each(stypes.slides, function(index, value) {
                                if (t == value.dataset.mm) {
                                    stype_initialSlide = value.dataset.swiperSlideIndex;
                                }
                            });
                            stypes.slideToLoop(stype_initialSlide, 500, $('#swipe_init').val('Y'));
                        });
                        stypes.init();
                        stimes.on("transitionEnd", function() {
                            setTimeout(() => {
                                var hh_data1 = $('.select_times .swiper-slide-active').data("hh");
                                var mm_data1 = $('.select_type .swiper-slide-active').data("mm");
                                var rtn = get_hh_mm_txt(hh_data1, mm_data1);
                            }, 0);
                        });
                        stypes.on("transitionEnd", function() {
                            setTimeout(() => {
                                var hh_data2 = $('.select_times .swiper-slide-active').data("hh");
                                var mm_data2 = $('.select_type .swiper-slide-active').data("mm");
                                var rtn = get_hh_mm_txt(hh_data2, mm_data2);
                            }, 0);
                        });

                        stimes.on("touchStart", function() {
                            // console.log('touchStart');
                            body_scroll_lock()
                        });

                        stimes.on("touchEnd", function() {
                            // console.log('touchStart');
                            body_scroll_visible()
                        });

                        stypes.on("touchStart", function() {
                            // console.log('touchStart');
                            body_scroll_lock()
                        });

                        stypes.on("touchEnd", function() {
                            //console.log('touchEnd');
                            body_scroll_visible();
                            var mm_data3 = $('.select_type .swiper-slide-active').data("mm");
                            if (mm_data3 == 'other_day') {
                                f_open_time('day');
                            } else if (mm_data3 == 'other_hour') {
                                f_open_time('hour');
                            } else if (mm_data3 == 'other_minute') {
                                f_open_time('minute');
                            } else {
                                f_open_time(mm_data3);
                            }
                        });
                    }, 0);
                }

                function get_hh_mm_txt(hh_data, mm_data) {
                    var stype;
                    if (mm_data == 'day' || mm_data == 'other_day') {
                        stype = '일';
                        group_type = "day";
                    } else if (mm_data == 'minute' || mm_data == 'other_minute') {
                        stype = '분';
                        group_type = "minute";
                    } else {
                        stype = '시간';
                        group_type = "hour";
                    }
                    if (typeof hh_data === 'undefined') {} else {
                        $('#stime_txt').html(hh_data + stype);
                        $('#pick_type').val(group_type);
                        $('#pick_result').val(hh_data);
                    }
                }
            </script>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block " onclick="group_period_chk()"><?= translate('입력했어요!', $userLang) ?></button>
            </div>
        </form>
    </div>
</div>
<script>
    function group_period_chk() {
        var pick_type = $('#pick_type').val();
        var pick_result = parseInt($('#pick_result').val(), 10); // pick_result를 정수로 파싱
        var sgdt_group_chk = $('#sgdt_group_chk').val();
        var sgdt_adate;
        if (sgdt_group_chk === 'N') {
            // 현재 날짜를 가져옴
            var currentDate = new Date();
            // pick_type에 따라서 시간 계산
            if (pick_type === 'day') {
                timestamp = currentDate.setDate(currentDate.getDate() + pick_result);
            } else if (pick_type === 'minute') {
                timestamp = currentDate.setMinutes(currentDate.getMinutes() + pick_result);
            } else if (pick_type === 'hour') {
                timestamp = currentDate.setHours(currentDate.getHours() + pick_result);
            }
            var date = new Date(timestamp);
            var year = date.getFullYear();
            var month = (date.getMonth() + 1).toString().padStart(2, '0'); // 월은 0부터 시작하므로 1을 더하고 두 자리로 맞춤
            var day = date.getDate().toString().padStart(2, '0');
            var hours = date.getHours().toString().padStart(2, '0');
            var minutes = date.getMinutes().toString().padStart(2, '0');
            var seconds = date.getSeconds().toString().padStart(2, '0');

            var sgdt_adate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        }

        var form_data = new FormData();
        form_data.append("act", "group_activity_period_detail");
        form_data.append("sgdt_idx", $('#sgdt_idx').val());
        form_data.append("sgdt_group_chk", $('#sgdt_group_chk').val());
        form_data.append("sgdt_adate", sgdt_adate);

        $.ajax({
            url: "./group_update",
            enctype: "multipart/form-data",
            data: form_data,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 5000,
            success: function(data) {
                console.log(data);
                if (data == 'Y') {
                    document.location.href = './group';
                }
            },
            error: function(err) {
                console.log(err);
            },
        });

        return false;
    }
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>