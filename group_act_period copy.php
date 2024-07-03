<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "그룹 활동 기한 설정";
$h_menu = '2';
$_SUB_HEAD_TITLE = "그룹 활동 기한 설정";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
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
        position: relative;
        overflow: hidden;
        margin: 1rem auto;
        padding: 0 30px;
        color: #252525;
        /* border: 1px solid red; */
        width: 23rem;
    }

    .swiper-container {
        width: 80px;
        height: 200px;
        float: left;
    }

    .swiper-slide {
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
        height: 70px !important;
        padding-top: 20px;
        padding-bottom: 20px;
    }


    .swiper-slide-active {
        opacity: 1;
    }

    .vizor {
        border-top: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
        height: 50px;
        position: absolute;
        top: 50%;
        left: 1rem;
        right: 1rem;
        transform: translateY(-50%);
        font-size: 2rem;
        line-height: 60px;
    }

    .vizor:before,
    .vizor:after {
        content: ':';
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
            <p class="tit_h3">그룹 활동 기한</p>
            <div class="checks_wr pt-3">
                <div class="checks pt-2">
                    <label>
                        <input type="checkbox" name="chk1" id="chk1" checked onchange="toggleSpinner()">
                        <span class=" ic_box"><i class="xi-check-min"></i></span>
                        <div class="chk_p ">
                            <p class="text_dynamic fs_14 fw_700">기한 없음</p>
                        </div>
                    </label>
                </div>
            </div>
            <div class="spin_wrap" id="cal_time_box">
                <div class="spin_result">
                    <p class="fs_14 fw_700 text_gray cal_time_box"><i class="xi-calendar mr-2"></i>7시간</p>
                </div>
                <div class="row mt-3 px-0 mx-0">
                    <div class="col px-0">
                        <ul class="spin_ul">
                            <li><button type=" button" class="btn h-auto w-auto">29</button></li>
                            <!-- 디자인상 색상이 더 어우둔 그레이는 .spin_center 클래스 추가시켜주시면 됩니다.!-->
                            <li class="spin_center"><button type="button" class="btn h-auto w-auto">30</button></li>
                            <li><button type="button" class="btn h-auto w-auto">1</button></li>
                        </ul>
                    </div>
                    <div class="col px-0">
                        <ul class="spin_ul">
                            <li><button type="button" class="btn h-auto w-auto">시간</button></li>
                            <!-- 디자인상 색상이 더 어우둔 그레이는 .spin_center 클래스 추가시켜주시면 됩니다.!-->
                            <li class="spin_center"><button type="button" class="btn h-auto w-auto">일</button></li>
                            <li><button type="button" class="btn h-auto w-auto">분</button></li>
                        </ul>
                    </div>
                </div>
            </div>



            <div class="line_ip mt_25" id="cal_time_box">
                <div class="row align-items-center">
                    <div class="col text-center">
                        <button type="button" id="btn_stime" class="btn btn-light btn-c cal_time_box"><span id="stime_txt"><?= $time_now_st ?></span></button>
                    </div>
                    <div class="col-1 d-flex justify-content-center align-items-center">
                        <i class="xi-long-arrow-right fs_20" style="color: gray"></i>
                    </div>
                    <div class="col text-center">
                        <button type="button" id="btn_etime" class="btn btn-light btn-c cal_time_box"><span id="etime_txt"><?= $time_now_et ?></span></button>
                    </div>
                </div>
            </div>
            <div class="picker d-none-temp" id="stime_picker">
                <div class="vizor"></div>
                <div class="swiper-container stime_hours">
                    <div class="swiper-wrapper">
                        <?php
                        $w = 0;
                        for ($q = 0; $q < 24; $q++) {
                            if ($w < 10) {
                                $w = "0" . $w;
                            }
                        ?>
                            <div class="swiper-slide" data-hh="<?= $w ?>"><?= $w ?></div>
                        <?php
                            $w++;
                        }
                        ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <div class="swiper-container stime_minutes">
                    <div class="swiper-wrapper">
                        <?php
                        $w = 0;
                        for ($q = 0; $q < 12; $q++) {
                            if ($w < 10) {
                                $w = "0" . $w;
                            }
                        ?>
                            <div class="swiper-slide" data-mm="<?= $w ?>"><?= $w ?></div>
                        <?php
                            $w += 5;
                        }
                        ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>

            <div class="picker d-none-temp" id="etime_picker">
                <div class="vizor"></div>
                <div class="swiper-container etime_hours">
                    <div class="swiper-wrapper">
                        <?php
                        $w = 0;
                        for ($q = 0; $q < 24; $q++) {
                            if ($w < 10) {
                                $w = "0" . $w;
                            }
                        ?>
                            <div class="swiper-slide" data-hh="<?= $w ?>"><?= $w ?></div>
                        <?php
                            $w++;
                        }
                        ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                <div class="swiper-container etime_minutes">
                    <div class="swiper-wrapper">
                        <?php
                        $w = 0;
                        for ($q = 0; $q < 12; $q++) {
                            if ($w < 10) {
                                $w = "0" . $w;
                            }
                        ?>
                            <div class="swiper-slide" data-mm="<?= $w ?>"><?= $w ?></div>
                        <?php
                            $w += 5;
                        }
                        ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>

            <script>
                $(document).ready(function() {

                });


                function get_time_format(hh_data, mm_data) {
                    if (hh_data > 12) {
                        var hh_t = '오후';
                        hh_data = hh_data - 12;
                        hh_data = get_pad(hh_data);
                    } else {
                        if (hh_data == 12 && mm_data == 0) {
                            var hh_t = '정오';
                        } else if (hh_data == 0 && mm_data == 0) {
                            var hh_t = '자정';
                        } else {
                            var hh_t = '오전';
                        }
                    }

                    // return hh_t + ' ' + get_pad(hh_data) + ':' + mm_data;
                    return hh_t + ' ' + hh_data + ':' + mm_data;
                }

                const btn_stime_b = document.getElementById("btn_stime");
                const btn_etime_b = document.getElementById("btn_etime");

                btn_stime_b.addEventListener('click', (e) => {
                    this.blur();
                    e.preventDefault();
                    btn_class_active();
                    setTimeout(() => {
                        $('#btn_stime').addClass('btn_active');
                    }, 100);
                    f_sopen_time('stime');

                    // ttcc();
                });
                btn_etime_b.addEventListener('click', (e) => {
                    this.blur();
                    e.preventDefault();
                    btn_class_active();
                    setTimeout(() => {
                        $('#btn_etime').addClass('btn_active');
                    }, 100);
                    f_eopen_time('etime');

                    // ttcc();
                });

                function body_scroll_lock() {
                    document.getElementsByTagName('body')[0].style.overflow = 'hidden';
                }

                function body_scroll_visible() {
                    document.getElementsByTagName('body')[0].style.overflow = 'visible';
                }

                function ttcc() {
                    // $('#pick_time').val('etime');
                    $('#datetimes').trigger("click");
                    $('#datetimes').trigger("click");
                }

                function chg_ddt(d) {
                    var cday = new Date(d);

                    return cday.getDate() + "/" + (cday.getMonth() + 1) + "/" + cday.getFullYear();
                }

                function f_day_click(dct) {
                    $('.c_id').removeClass('active');
                    setTimeout(() => {
                        $('#calendar_' + dct).addClass('active');
                    }, 100);
                    var pdt = $('#pick_time').val();

                    var dwt = dateFormat_week(dct);

                    if (pdt == 'stime') {
                        // $('#sdate_txt').html(dwt);
                        // $('#edate_txt').html(dwt);
                        $('#pick_sdate').val(dct);
                        // $('#pick_edate').val(dct);
                    } else {
                        $('#pick_edate').val(dct);

                        // var date1 = new Date($('#pick_sdate').val());
                        // var date2 = new Date(dct);
                        // var dctg = chg_ddt(dct);

                        // if (date1 <= date2) {
                        //     $('#pick_edate').val(dct);
                        //     $('#edate_txt').html(dwt);
                        // } else {
                        //     $('#pick_sdate').val(dct);
                        //     $('#pick_edate').val(dct);
                        //     $('#sdate_txt').html(dwt);
                        //     $('#edate_txt').html(dwt);
                        // }
                    }

                    setTimeout(() => {
                        datetime_chk();
                    }, 100);
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

                function f_open_cal(t) {
                    $('#pick_time').val(t);
                    $('#schedule_calandar_box_header').show();
                    f_calendar_init();
                }

                function Unix_timestamp(t) {
                    var date = new Date(t * 1000);
                    var year = date.getFullYear();
                    var month = "0" + (date.getMonth() + 1);
                    var day = "0" + date.getDate();
                    var hour = "0" + date.getHours();
                    var minute = "0" + date.getMinutes();
                    var second = "0" + date.getSeconds();
                    return year + "-" + month.substr(-2) + "-" + day.substr(-2) + " " + hour.substr(-2) + ":" + minute.substr(-2) + ":" + second.substr(-2);
                }

                function f_sopen_time(t) {
                    $('#pick_time').val(t);
                    $('#' + t + '_picker').show();

                    setTimeout(() => {
                        var dStartD, dEndD;

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
                            breakpointsBase: 'container ',
                            init: false,
                        };

                        dStartD = new Date($('#sst_sdate').val());
                        dEndD = new Date($('#sst_edate').val());

                        // console.log("dStartD " + dStartD);

                        var shours = new Swiper(
                            ".swiper-container.stime_hours",
                            Object.assign({}, defaults)
                        );

                        var sminutes = new Swiper(
                            ".swiper-container.stime_minutes",
                            Object.assign({}, defaults)
                        );

                        var sdhh = dStartD.getHours();
                        var sdmm = dStartD.getMinutes();

                        shours.on("init", function() {
                            var shours_initialSlide;

                            $.each(shours.slides, function(index, value) {
                                if (sdhh == value.dataset.hh) {
                                    shours_initialSlide = value.dataset.swiperSlideIndex;
                                }
                            });

                            shours.slideToLoop(shours_initialSlide, 500, $('#swipe_init').val('Y'));
                        });

                        shours.init();

                        sminutes.on("init", function() {
                            var smin_initialSlide;

                            $.each(sminutes.slides, function(index, value) {
                                if (sdmm == value.dataset.mm) {
                                    smin_initialSlide = value.dataset.swiperSlideIndex;
                                }
                            });

                            sminutes.slideToLoop(smin_initialSlide, 500, $('#swipe_init').val('Y'));
                        });

                        sminutes.init();

                        shours.on("transitionEnd", function() {
                            setTimeout(() => {
                                var hh_data1 = $('.stime_hours .swiper-slide-active').data("hh");
                                var mm_data1 = $('.stime_minutes .swiper-slide-active').data("mm");

                                var rtn = sget_hh_mm_txt(hh_data1, mm_data1);
                            }, 0);
                        });

                        sminutes.on("transitionEnd", function() {
                            setTimeout(() => {
                                var hh_data2 = $('.stime_hours .swiper-slide-active').data("hh");
                                var mm_data2 = $('.stime_minutes .swiper-slide-active').data("mm");

                                var rtn = sget_hh_mm_txt(hh_data2, mm_data2);
                            }, 0);
                        });

                        shours.on("touchStart", function() {
                            // console.log('touchStart');
                            body_scroll_lock()
                        });

                        shours.on("touchEnd", function() {
                            // console.log('touchStart');
                            body_scroll_visible()
                        });

                        sminutes.on("touchStart", function() {
                            // console.log('touchStart');
                            body_scroll_lock()
                        });

                        sminutes.on("touchEnd", function() {
                            // console.log('touchStart');
                            body_scroll_visible()
                        });
                    }, 0);
                }

                function f_eopen_time(t) {
                    $('#pick_time').val(t);
                    $('#' + t + '_picker').show();

                    setTimeout(() => {
                        var dStartD, dEndD;

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
                            breakpointsBase: 'container ',
                            init: false,
                        };

                        dStartD = new Date($('#sst_sdate').val());
                        dEndD = new Date($('#sst_edate').val());

                        // console.log("dStartD " + dStartD);

                        var ehours = new Swiper(
                            ".swiper-container.etime_hours",
                            Object.assign({}, defaults)
                        );

                        var eminutes = new Swiper(
                            ".swiper-container.etime_minutes",
                            Object.assign({}, defaults)
                        );

                        var edhh = dEndD.getHours();
                        var edmm = dEndD.getMinutes();

                        ehours.on("init", function() {
                            var ehours_initialSlide;

                            $.each(ehours.slides, function(index, value) {
                                if (edhh == value.dataset.hh) {
                                    ehours_initialSlide = value.dataset.swiperSlideIndex;
                                }
                            });

                            ehours.slideToLoop(ehours_initialSlide, 500, $('#swipe_init').val('Y'));
                        });

                        ehours.init();

                        eminutes.on("init", function() {
                            var emin_initialSlide;

                            $.each(eminutes.slides, function(index, value) {
                                if (edmm == value.dataset.mm) {
                                    emin_initialSlide = value.dataset.swiperSlideIndex;
                                }
                            });

                            eminutes.slideToLoop(emin_initialSlide, 500, $('#swipe_init').val('Y'));
                        });

                        eminutes.init();

                        ehours.on("transitionEnd", function() {
                            setTimeout(() => {
                                var hh_data3 = $('.etime_hours .swiper-slide-active').data("hh");
                                var mm_data3 = $('.etime_minutes .swiper-slide-active').data("mm");

                                var rtn = eget_hh_mm_txt(hh_data3, mm_data3);
                            }, 0);
                        });

                        eminutes.on("transitionEnd", function() {
                            setTimeout(() => {
                                var hh_data4 = $('.etime_hours .swiper-slide-active').data("hh");
                                var mm_data4 = $('.etime_minutes .swiper-slide-active').data("mm");

                                var rtn = eget_hh_mm_txt(hh_data4, mm_data4);
                            }, 0);
                        });

                        ehours.on("touchStart", function() {
                            // console.log('touchStart');
                            body_scroll_lock()
                        });

                        ehours.on("touchEnd", function() {
                            // console.log('touchStart');
                            body_scroll_visible()
                        });

                        eminutes.on("touchStart", function() {
                            // console.log('touchStart');
                            body_scroll_lock()
                        });

                        eminutes.on("touchEnd", function() {
                            // console.log('touchStart');
                            body_scroll_visible()
                        });
                    }, 0);
                }

                function sget_hh_mm_txt(hh_data, mm_data) {
                    if ($('#swipe_init').val() == 'Y') {
                        var rtn = get_time_format(hh_data, mm_data);

                        $('#pick_stime').val(hh_data + ':' + mm_data + ':00');
                        // $('#stime_txt').html(rtn);

                        var rtn2 = datetime_chk();

                        return rtn2;
                    }
                }

                function eget_hh_mm_txt(hh_data, mm_data) {
                    if ($('#swipe_init').val() == 'Y') {
                        var rtn = get_time_format(hh_data, mm_data);

                        $('#pick_etime').val(hh_data + ':' + mm_data + ':00');
                        // $('#etime_txt').html(rtn);

                        var rtn2 = datetime_chk();

                        return rtn2;
                    }
                }

                function get_hh_mm_txt(hh_data, mm_data) {
                    var pick_time_tt = $('#pick_time').val();

                    var rtn = get_time_format(hh_data, mm_data);

                    $('#pick_' + pick_time_tt).val(hh_data + ':' + mm_data + ':00');
                    $('#' + pick_time_tt + '_txt').html(rtn);

                    var rtn2 = datetime_chk();

                    return rtn2;
                }

                function set_date_time(sd, ed, st, et) {
                    console.log("sd st " + sd + " " + st);
                    console.log("ed et " + ed + " " + et);

                    var sdhtml = dateFormat_week(sd);
                    $('#sdate_txt').html(sdhtml);

                    var edhtml = dateFormat_week(ed);
                    $('#edate_txt').html(edhtml);

                    var stsp = st.split(":");
                    var sthtml = get_time_format(stsp[0], stsp[1]);
                    $('#stime_txt').html(sthtml);

                    // console.log("sthtml" + sthtml);

                    var etsp = et.split(":");
                    var ethtml = get_time_format(etsp[0], etsp[1]);
                    $('#etime_txt').html(ethtml);

                    // console.log("ethtml" + ethtml);

                    $('#sst_sdate').val(sd + ' ' + st);
                    $('#sst_edate').val(ed + ' ' + et);

                    $('#pick_sdate').val(sd);
                    $('#pick_edate').val(ed);
                    $('#pick_stime').val(st);
                    $('#pick_etime').val(et);
                }

                function datetime_chk() {
                    var sd = $('#pick_sdate').val();
                    var ed = $('#pick_edate').val();
                    var st = $('#pick_stime').val();
                    var et = $('#pick_etime').val();
                    var pd = $('#pick_date').val();
                    var pt = $('#pick_time').val();

                    if (sd && ed && st && et) {
                        var csdt = new Date(sd + ' ' + st);
                        var cedt = new Date(ed + ' ' + et);

                        console.log("csdt : " + csdt);
                        console.log("cedt : " + cedt);
                        console.log(csdt > cedt);

                        if (csdt == cedt) { //시작 == 마감
                            var usd = (csdt.getTime() / 1000);
                            var usc = Unix_timestamp(usd + 3600);
                            var syd = new Date(usc);
                            set_date_time(sd, ed, st, et);
                        } else {
                            if (csdt < cedt) { //시작 < 마감
                                set_date_time(sd, ed, st, et);
                            } else { //시작 > 마감
                                console.log("pd pt " + pd + " " + pt);
                                if (pt == 'stime') { //시작 설정시
                                    var usd = (csdt.getTime() / 1000);
                                    var usc = Unix_timestamp(usd + 3600);
                                    var syd = new Date(usc);
                                    console.log(ed + et);
                                    set_date_time(sd, ed, st, et);
                                } else { //마감 설정시
                                    var ued = (cedt.getTime() / 1000);
                                    var uec = Unix_timestamp(ued - 3600);
                                    var eyd = new Date(uec);
                                    set_date_time(sd, ed, st, et);
                                }
                            }
                        }
                    } else {
                        $('#sst_sdate').val('');
                        $('#sst_edate').val('');

                        return 'edate_chg';
                    }
                }
            </script>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block " onclick="location.href='./group'">입력했어요!</button>
            </div>
        </form>
    </div>
</div>
<script>
    function toggleSpinner() {
        var checkbox = document.getElementById('chk1');
        var spinner = document.getElementById('cal_time_box');

        // 체크 상태에 따라 스피너 보이기/숨기기
        if (checkbox.checked) {
            spinner.style.display = 'none';
        } else {
            spinner.style.display = 'block';
        }
    }
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>