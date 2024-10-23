<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "";
$_GET['hd_num'] = '';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>
<style>
    html {
        height: 100%;
        overflow-y: unset !important;
    }

    .sub_pg {
        background-color: #EDEEF0;
        position: fixed;
        top: 0;
        left: 50%;
        width: 100%;
        height: 100% !important;
        min-height: 100% !important;
        max-width: 50rem;
        transform: translateX(-50%);
        padding-bottom: 1rem;
        padding-top: 4rem;
    }

    .sub_pg_in {
        width: 100%;
        height: 100%;
        position: relative;
    }
</style>
<div class="container sub_pg">
    <div class="sub_pg_in">
        <!-- Swiper -->
        <div class="swiper onbding_swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide sw_slide_1">
                    <p class="fs_22 fw_700 line_h1_4 text_dynamic pb-3 pl-3"><?=$translations['txt_check_schedule_location']?></p>
                    <div class="onbd_in_img">
                        <img src="./img/onbd_img1.png" alt="Your Image Description" style="width: 280%; height: auto;">
                    </div>
                </div>
                <div class="swiper-slide sw_slide_2">
                    <p class="fs_22 fw_700 line_h1_4 text_dynamic pb-3 pl-3"><?=$translations['txt_create_group_manage_members']?></p>
                    <div class="onbd_in_img">
                        <img src="./img/onbd_img2.png" alt="Your Image Description" style="width: 280%; height: auto;">
                    </div>
                </div>
                <div class="swiper-slide sw_slide_3">
                    <p class="fs_22 fw_700 line_h1_4 text_dynamic pb-3 pl-3"><?=$translations['txt_manage_schedule_by_member']?></p>
                    <div class="onbd_in_img">
                        <img src="./img/onbd_img3.png" alt="Your Image Description" style="width: 280%; height: auto;">
                    </div>
                </div>
                <div class="swiper-slide sw_slide_4">
                    <p class="fs_22 fw_700 line_h1_4 text_dynamic pb-3 pl-3"><?=$translations['txt_check_realtime_visitors']?></p>
                    <div class="onbd_in_img">
                        <img src="./img/onbd_img4.png" alt="Your Image Description" style="width: 280%; height: auto;">
                    </div>
                </div>
                <div class="swiper-slide sw_slide_5">
                    <p class="fs_22 fw_700 line_h1_4 text_dynamic pb-3 pl-3"><?=$translations['txt_check_daily_log']?></p>
                    <div class="onbd_in_img">
                        <img src="./img/onbd_img5.png" alt="Your Image Description" style="width: 280%; height: auto;">
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"><?=$translations['txt_next']?></div>
        </div>
    </div>
</div>

<script>
    var onbding_swiper = new Swiper('.onbding_swiper', {
        pagination: {
            el: ".swiper-pagination",
        },
        navigation: {
            nextEl: ".onbding_swiper .swiper-button-next",
        },

        on: {
            slideChange: function() {
                var nextButton = document.querySelector('.swiper-button-next');

                if (nextButton.classList.contains('swiper-button-disabled')) {
                    nextButton.textContent = '<?=$translations['txt_start']?>';
                    nextButton.addEventListener('click', swiperNextClick);
                } else {
                    nextButton.textContent = '<?=$translations['txt_next']?>';
                    nextButton.removeEventListener('click', swiperNextClick);
                }
            }
        }
    });

    function swiperNextClick() {
        window.location.href = './';
    }
</script>