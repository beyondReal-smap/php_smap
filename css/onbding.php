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
                    <p class="fs_22 fw_700 line_h1_4 text_dynamic pb-3 pl-3">자녀의 일정과 실시간 위치를
                        지도에서 간편하게 확인해보세요!</p>
                    <div class="onbd_in_img"></div>
                </div>
                <div class="swiper-slide sw_slide_2">
                    <p class="fs_22 fw_700 line_h1_4 text_dynamic pb-3 pl-3">그룹을 만들어
                        그룹원을 관리할 수 있어요!</p>
                    <div class="onbd_in_img"></div>
                </div>
                <div class="swiper-slide sw_slide_3">
                    <p class="fs_22 fw_700 line_h1_4 text_dynamic pb-3 pl-3">일정을 그룹원 별로
                        쉽게 관리해보세요.</p>
                    <div class="onbd_in_img"></div>
                </div>
                <div class="swiper-slide sw_slide_4">
                    <p class="fs_22 fw_700 line_h1_4 text_dynamic pb-3 pl-3">내장소에 누가왔는지
                        실시간으로 확인할 수 있어요!</p>
                    <div class="onbd_in_img"></div>
                </div>
                <div class="swiper-slide sw_slide_5">
                    <p class="fs_22 fw_700 line_h1_4 text_dynamic pb-3 pl-3">그날의 일정과 위치를
                        로그를 통해 확인해보세요!</p>
                    <div class="onbd_in_img"></div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next">다음</div>
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
                    nextButton.textContent = '시작하기';
                    nextButton.addEventListener('click', swiperNextClick);
                } else {
                    nextButton.textContent = '다음';
                    nextButton.removeEventListener('click', swiperNextClick);
                }
            }
        }
    });

    function swiperNextClick() {
        window.location.href = './';
    }
</script>