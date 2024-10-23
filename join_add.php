<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "";
$h_menu = '7';
$h_func = "back_confirm()";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

$_SESSION['mt_pass'] = $_GET['mt_pass'];
// 파라미터 저장
$mt_pass = isset($_SESSION['mt_pass']) ? $_SESSION['mt_pass'] : '';
$mt_gender = isset($_SESSION['mt_gender']) ? $_SESSION['mt_gender'] : '';
$pick_year = isset($_SESSION['pick_year']) ? $_SESSION['pick_year'] : '';
$pick_month = isset($_SESSION['pick_month']) ? $_SESSION['pick_month'] : '';
$pick_day = isset($_SESSION['pick_day']) ? $_SESSION['pick_day'] : '';
$mt_name = isset($_SESSION['mt_name']) ? $_SESSION['mt_name'] : '';
$mt_email = isset($_SESSION['mt_email']) ? $_SESSION['mt_email'] : '';

if ($_SESSION['_mt_idx'] == '') {
    gotourl('./logout');
}
?>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<!-- <script src="https://polyfill.io/v3/polyfill.min.js"></script> -->
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
        width: 33%;
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
        <p class="tit_h1 wh_pre line_h1_3"><?= $translations['txt_enter_member_info'] ?></p>
        <form method="post" name="frm_form" id="frm_form" action="./join_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="join_add_info" />
            <input type="hidden" id="mt_idx" name="mt_idx" value="<?= $_SESSION['_mt_idx'] ?>">
            <input type="hidden" id="pick_year" name="pick_year" value="">
            <input type="hidden" id="pick_month" name="pick_month" value="">
            <input type="hidden" id="pick_day" name="pick_day" value="">
            <div class="mt-5">
                <div class="ip_wr mt_25" id="mt_name_text">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5 class=""><?= $translations['txt_name'] ?></h5>
                        <p class="text_num fs_12 fc_gray_600">(<span id="mt_name_cnt">0</span>/30)</p>
                    </div>
                    <input type="text" class="form-control txt-cnt" name="mt_name" id="mt_name" maxlength="30" data-length-id="mt_name_cnt" placeholder="<?= $translations['txt_enter_name'] ?>" value="<?= isset($_POST['mt_name']) ? $_POST['mt_name'] : (isset($mt_name) ? $mt_name : '') ?>">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> <?= $translations['txt_confirmed'] ?></div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= $translations['txt_check_id'] ?></div>
                </div>
                <div class="ip_wr mt_25" id="mt_birth_text">
                    <div class="ip_tit">
                        <h5 class=""><?= $translations['txt_birth_date'] ?></h5>
                    </div>
                    <div class="spin_wrap" id="cal_time_box">
                        <div class="spin_result">
                            <p class="fs_14 fw_700 text_gray cal_time_box"><i class="xi-calendar mr-2"></i><span id="stime_txt"></span></p>
                        </div>
                        <div class="row mt-3 px-0 mx-0">
                            <div class="picker" id="stime_picker">
                                <div class="vizor"></div>
                                <div class="swiper-container select_years">
                                    <div class="swiper-wrapper" id="resultyearList">
                                        <div class="swiper-slide" data-yy="<?= $pick_year ?>"><?= $pick_year ?></div>
                                    </div>
                                </div>
                                <div class="swiper-container select_months">
                                    <div class="swiper-wrapper" id="resultmonthList">
                                        <div class="swiper-slide" data-mm="<?= $pick_month ?>"><?= $pick_month ?></div>
                                    </div>
                                </div>
                                <div class="swiper-container select_days">
                                    <div class="swiper-wrapper" id="resultdayList">
                                        <div class="swiper-slide" data-dd="<?= $pick_day ?>"><?= $pick_day ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        $(document).ready(function() {
                            // 엔터 키 입력 시 폼 제출 방지
                            $('#mt_name').on('keydown', function(event) {
                                if (event.key === 'Enter') {
                                    event.preventDefault(); // 기본 동작 방지
                                }
                            });

                            btn_class_active();
                            f_open_time();
                        });

                        // 클릭한 버튼 타입에 따라 결과 리스트를 업데이트
                        function select_type() {
                            var resultListYear = [];
                            var resultListMonth = [];
                            var resultListDay = [];

                            // 오늘의 년도 가져오기
                            var today = new Date();
                            var currentYear = today.getFullYear();
                            var beforeYear = (currentYear - 100);
                            document.getElementById('resultyearList').innerHTML = "";
                            document.getElementById('resultmonthList').innerHTML = "";
                            document.getElementById('resultdayList').innerHTML = "";

                            for (var i = beforeYear; i <= currentYear; i++) {
                                resultListYear.push('<div class="swiper-slide" data-yy="' + i + '">' + i + '</div>');
                            }
                            for (var i = 1; i <= 12; i++) {
                                var formattedMonth = String(i).padStart(2, '0'); // 1자리 숫자인 경우 0을 추가
                                resultListMonth.push('<div class="swiper-slide" data-mm="' + formattedMonth + '">' + formattedMonth + '</div>');
                            }
                            for (var i = 1; i <= 31; i++) {
                                var formattedDay = String(i).padStart(2, '0'); // 1자리 숫자인 경우 0을 추가
                                resultListDay.push('<div class="swiper-slide" data-dd="' + formattedDay + '">' + formattedDay + '</div>');
                            }

                            // 결과 리스트 업데이트
                            document.getElementById('resultyearList').innerHTML = resultListYear.join('');
                            document.getElementById('resultmonthList').innerHTML = resultListMonth.join('');
                            document.getElementById('resultdayList').innerHTML = resultListDay.join('');
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
                            if (window.syears) {
                                window.syears.destroy();
                                window.syears = null;
                            }

                            if (window.smonths) {
                                window.smonths.destroy();
                                window.smonths = null;
                            }

                            if (window.sdays) {
                                window.sdays.destroy();
                                window.sdays = null;
                            }
                        }

                        function f_open_time() {
                            // 이미 생성된 Swiper 인스턴스 제거
                            destroySwiperInstances();
                            select_type();
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
                                window.syears = new Swiper(
                                    ".swiper-container.select_years",
                                    Object.assign({}, defaults)
                                );

                                window.smonths = new Swiper(
                                    ".swiper-container.select_months",
                                    Object.assign({}, defaults)
                                );

                                window.sdays = new Swiper(
                                    ".swiper-container.select_days",
                                    Object.assign({}, defaults)
                                );

                                var today = new Date();
                                var currentYear = today.getFullYear();

                                var syear = <?= $pick_year ? $pick_year : 1990; ?>;
                                var smonth = <?= $pick_month ? $pick_month : 1; ?>;
                                var sday = <?= $pick_day ? $pick_day : 1; ?>;

                                console.log(syear, smonth, sday);

                                syears.on("init", function() {
                                    var syears_initialSlide;
                                    $.each(syears.slides, function(index, value) {
                                        if (syear == value.dataset.yy) {
                                            syears_initialSlide = value.dataset.swiperSlideIndex;
                                        }
                                    });
                                    syears.slideToLoop(syears_initialSlide, 500, $('#swipe_init').val('Y'));
                                });
                                syears.init();
                                smonths.on("init", function() {
                                    var smonths_initialSlide;
                                    $.each(smonths.slides, function(index, value) {
                                        if (smonth == value.dataset.mm) {
                                            smonths_initialSlide = value.dataset.swiperSlideIndex;
                                        }
                                    });
                                    smonths.slideToLoop(smonths_initialSlide, 500, $('#swipe_init').val('Y'));
                                });
                                smonths.init();
                                sdays.on("init", function() {
                                    var sdays_initialSlide;
                                    $.each(sdays.slides, function(index, value) {
                                        if (sday == value.dataset.dd) {
                                            sdays_initialSlide = value.dataset.swiperSlideIndex;
                                        }
                                    });
                                    sdays.slideToLoop(sdays_initialSlide, 500, $('#swipe_init').val('Y'));
                                });
                                sdays.init();
                                syears.on("transitionEnd", function() {
                                    setTimeout(() => {
                                        var yy_data1 = $('.select_years .swiper-slide-active').data("yy");
                                        var mm_data1 = $('.select_months .swiper-slide-active').data("mm");
                                        var dd_data1 = $('.select_days .swiper-slide-active').data("dd");
                                        var rtn = get_hh_mm_txt(yy_data1, mm_data1, dd_data1);
                                    }, 0);
                                });
                                smonths.on("transitionEnd", function() {
                                    setTimeout(() => {
                                        var yy_data2 = $('.select_years .swiper-slide-active').data("yy");
                                        var mm_data2 = $('.select_months .swiper-slide-active').data("mm");
                                        var dd_data2 = $('.select_days .swiper-slide-active').data("dd");
                                        var rtn = get_hh_mm_txt(yy_data2, mm_data2, dd_data2);
                                    }, 0);
                                });
                                sdays.on("transitionEnd", function() {
                                    setTimeout(() => {
                                        var yy_data3 = $('.select_years .swiper-slide-active').data("yy");
                                        var mm_data3 = $('.select_months .swiper-slide-active').data("mm");
                                        var dd_data3 = $('.select_days .swiper-slide-active').data("dd");
                                        var rtn = get_hh_mm_txt(yy_data3, mm_data3, dd_data3);
                                    }, 0);
                                });

                                syears.on("touchStart", function() {
                                    // console.log('touchStart');
                                    body_scroll_lock()
                                });
                                syears.on("touchEnd", function() {
                                    // console.log('touchStart');
                                    body_scroll_visible()
                                });
                                smonths.on("touchStart", function() {
                                    // console.log('touchStart');
                                    body_scroll_lock()
                                });
                                smonths.on("touchEnd", function() {
                                    // console.log('touchStart');
                                    body_scroll_visible()
                                });
                                sdays.on("touchStart", function() {
                                    // console.log('touchStart');
                                    body_scroll_lock()
                                });
                                sdays.on("touchEnd", function() {
                                    // console.log('touchStart');
                                    body_scroll_visible()
                                });
                            }, 0);
                        }

                        function get_hh_mm_txt(yy_data, mm_data, dd_date) {
                            $('#stime_txt').html(yy_data + '-' + mm_data + '-' + dd_date);
                            $('#pick_year').val(yy_data);
                            $('#pick_month').val(mm_data);
                            $('#pick_day').val(dd_date);
                        }
                    </script>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> <?= $translations['txt_confirmed'] ?></div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> <?= $translations['txt_check_id'] ?></div>
                </div>
                <div class="ip_wr mt_25" id="mt_gender_text">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5><?= $translations['txt_gender'] ?></h5>
                    </div>
                    <div class="checks_wr row pt-3">
                        <div class="checks col-6 mr-0">
                            <label>
                                <input type="radio" name="mt_gender" id="mt_gender" value="1" <?= $mt_gender && $mt_gender == '1' ? 'checked' : '' ?>>
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p ">
                                    <p class="text_dynamic fs_16"><?= $translations['txt_male'] ?></p>
                                </div>
                            </label>
                        </div>
                        <div class="checks col-6 mr-0">
                            <label>
                                <input type="radio" name="mt_gender" id="mt_gender2" value="2" <?= $mt_gender && $mt_gender == '2' ? 'checked' : '' ?>>
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p ">
                                    <p class="text_dynamic fs_16"><?= $translations['txt_female'] ?></p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="b_botton">
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="validateAndSubmit(event)"><?= $translations['txt_input_complete'] ?></button>
                </button>
            </div>
        </form>
    </div>
</div>
<!-- 뒤로가기 클릭 시 -->
<div class="modal fade" id="back_confirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center"><?= $translations['txt_leave_signup_confirm'] ?></p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" onclick="$.ajax({url: './join_update', type: 'POST', data: {act: 'join_delete'}, success: function() {location.replace('./join_entry?phoneNumber=<?= $_GET['phoneNumber'] ?>&mt_email=<?= $_GET['mt_email'] ?>&mt_idx=<?= $_SESSION['_mt_idx'] ?>&mt_gender=<?= $mt_gender ?>&pick_year=<?= $pick_year ?>&pick_month=<?= $pick_month ?>&pick_day=<?= $pick_day ?>&mt_name=<?= $mt_name ?>');}})"><?= $translations['txt_yes'] ?></button>
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" data-dismiss="modal" aria-label="Close"><?= $translations['txt_no'] ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(document).on("keyup", "input.txt-cnt", function() {
            var cnt_id = $(this).data('length-id');
            $('#' + cnt_id).text($(this).val().length);
        });
    });


    function validateDate(year, month, day) {
        var date = new Date(year, month - 1, day);
        return date.getFullYear() == year && date.getMonth() == month - 1 && date.getDate() == day;
    }

    function back_confirm() {
        $('#back_confirm').modal('show');
    }

    function validateAndSubmit(event) {
        event.preventDefault(); // 폼 기본 제출 동작 방지

        var mt_gender = $('input[name=mt_gender]:checked').val();
        var mt_name = $('#mt_name').val();
        var year = parseInt($('#pick_year').val());
        var month = parseInt($('#pick_month').val());
        var day = parseInt($('#pick_day').val());

        if (!validateDate(year, month, day)) {
            jalert("<?= $translations['txt_enter_valid_birth_date'] ?>");
            saveFormState();
            return false;
        }

        if (!mt_name || !mt_gender) {
            jalert(!mt_name ? "<?= $translations['txt_enter_name'] ?>" : "<?= $translations['txt_enter_gender'] ?>");
            saveFormState();
            return false;
        }
        // AJAX 호출을 통해 데이터 전송
        $.ajax({
            url: './join_update.php',
            type: 'POST',
            data: {
                act: 'join_add_info',
                mt_gender: mt_gender,
                pick_year: year,
                pick_month: month,
                pick_day: day,
                mt_name: mt_name
            },
            success: function(response) {
                // 성공 시 join_agree.php로 이동
                window.location.href = './join_agree.php';
            },
            error: function(xhr, status, error) {
                // 오류 처리
                jalert("오류가 발생했습니다. 다시 시도해 주세요.");
            }
        });
    }

    function saveFormState() {
        localStorage.setItem('mt_name', $('#mt_name').val());
        localStorage.setItem('mt_gender', $('input[name=mt_gender]:checked').val());
        localStorage.setItem('pick_year', $('#pick_year').val());
        localStorage.setItem('pick_month', $('#pick_month').val());
        localStorage.setItem('pick_day', $('#pick_day').val());
    }

    $(document).ready(function() {
        // 페이지 로드 시 저장된 상태 복원
        $('#mt_name').val(localStorage.getItem('mt_name') || '');
        $('input[name=mt_gender][value="' + (localStorage.getItem('mt_gender') || '') + '"]').prop('checked', true);
        $('#pick_year').val(localStorage.getItem('pick_year') || '');
        $('#pick_month').val(localStorage.getItem('pick_month') || '');
        $('#pick_day').val(localStorage.getItem('pick_day') || '');

        // 상태 복원 후 localStorage 클리어
        localStorage.clear();
    });
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>