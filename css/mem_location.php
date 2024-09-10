<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '1';
$h_menu = '1';
include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";
?>
<style>
html {
    height: 100%;
}

.h_menu {
    background-color: #fff !important;
}

.sub_pg {
    height: calc(100% - 4.8rem) !important;
    min-height: calc(100% - 4.8rem) !important;
    overflow: hidden;
    padding-top: 4.8rem;
}
</style>
<div class="container sub_pg  px-0 py-0">
    <div class="map_wrap_h">
        <div class="map_wrap_h_div">
            <div class="map_rt">
                <div class="map_rt_round">
                    <div class="map_rt_img">
                        <div class="rect_square rounded-pill">
                            <img src="<?=CDN_HTTP?>/img/sample01.png" alt="이미지" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="map_ab">
                <div class="point point1">
                    <img src="<?=CDN_HTTP?>/img/map_point.png" width="28px" alt="이미지" />
                    <div class="infobox w_fit rounded-sm bg-white px_08 py_08">
                        <!-- 필요하시다면 on 추가되면 상세내역 보여집니다.-->
                        <p class="fs_8 text_dynaimic">오후 6:00 ~ 오후 8:30</p>
                        <p class="fs_12 fw_500 text_dynaimic mt-2">미술학원</p>
                    </div>
                </div>
                <div class="point point2">
                    <img src="<?=CDN_HTTP?>/img/map_point.png" width="28px" alt="이미지" />
                    <div class="infobox w_fit rounded-sm bg-white px_08 py_08">
                        <p class="fs_8 text_dynaimic">오후 6:00 ~ 오후 8:30</p>
                        <p class="fs_12 fw_500 text_dynaimic mt-2">미술학원</p>
                    </div>
                </div>
                <div class="point point3">
                    <img src="<?=CDN_HTTP?>/img/map_point.png" width="28px" alt="이미지" />
                    <div class="infobox w_fit rounded-sm bg-white px_08 py_08">
                        <p class="fs_8 text_dynaimic">오후 6:00 ~ 오후 8:30</p>
                        <p class="fs_12 fw_500 text_dynaimic mt-2">미술학원</p>
                    </div>
                </div>
                <div class="point point4">
                    <img src="<?=CDN_HTTP?>/img/mark_departure.png" width="30px" alt="출발" />
                </div>
                <div class="point point5">
                    <img src="<?=CDN_HTTP?>/img/mark_arrival.png" width="30px" alt="도착" />
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- D-8 멤버 위치 -->
<div class="opt_bottom opt_bottom_1" id="bottomSheet">
    <div class="">
        <div class="top_bar_wrap text-center pt_08">
            <img src="<?=CDN_HTTP?>/img/top_bar.png" class="top_bar" width="34px" alt="탑바" />
            <img src="<?=CDN_HTTP?>/img/btn_bl_arrow.png" class="top_down mx-auto" width="12px" alt="탑다운" />
        </div>
        <!--프로필-->
        <div class="mem_wrap">
            <div class="d-flex tab_scroll scroll_bar_x">
                <div class="checks mem_box w_fit mr_12">
                    <label>
                        <input type="radio" name="rd2" checked>
                        <div class="prd_img mx-auto">
                            <div class="rect_square rounded_14">
                                <img src="<?=CDN_HTTP?>/img/sample01.png" alt="이미지" />
                            </div>
                        </div>
                        <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic">최지우 지우</p>
                    </label>
                </div>
                <div class="checks mem_box w_fit mr_12">
                    <label>
                        <input type="radio" name="rd2">
                        <div class="prd_img mx-auto on_arm">
                            <!-- 알림왔을 때 on_arm 추가 -->
                            <div class="rect_square rounded_14">
                                <img src="<?=CDN_HTTP?>/img/sample01.png" alt="이미지" />
                            </div>
                        </div>
                        <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic">지우지우지우</p>
                    </label>
                </div>
                <div class="checks mem_box w_fit mr_12">
                    <label>
                        <input type="radio" name="rd2">
                        <div class="prd_img mx-auto">
                            <div class="rect_square rounded_14">
                                <img src="<?=CDN_HTTP?>/img/sample01.png" alt="이미지" />
                            </div>
                        </div>
                        <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">최지우지우</p>
                    </label>
                </div>
                <div class="checks mem_box w_fit mr_12">
                    <label>
                        <input type="radio" name="rd2">
                        <div class="prd_img mx-auto">
                            <div class="rect_square rounded_14">
                                <img src="<?=CDN_HTTP?>/img/sample01.png" alt="이미지" />
                            </div>
                        </div>
                        <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">우주최강 루틴강자 호호</p>
                    </label>
                </div>
                <div class="checks mem_box w_fit mr_12">
                    <label>
                        <input type="radio" name="rd2">
                        <div class="prd_img mx-auto">
                            <div class="rect_square rounded_14">
                                <img src="<?=CDN_HTTP?>/img/sample01.png" alt="이미지" />
                            </div>
                        </div>
                        <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">최지우 지우</p>
                    </label>
                </div>
                <div class="checks mem_box w_fit mr_12">
                    <label>
                        <input type="radio" name="rd2">
                        <div class="prd_img mx-auto">
                            <div class="rect_square rounded_14">
                                <img src="<?=CDN_HTTP?>/img/sample01.png" alt="이미지" />
                            </div>
                        </div>
                        <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">최지우 지우</p>
                    </label>
                </div>
                <div class="mem_box w_fit mr_12" data-toggle="modal" data-target="#schedule_member">
                    <button class="btn mem_add mx-auto">
                        <i class="xi-plus-min fs_20"></i>
                    </button>
                    <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic"><?= translate('그룹원 추가', $userLang) ?></p>
                </div>
                <div class="mem_box w_fit mr_12" data-toggle="modal" data-target="#link_modal">
                    <button class="btn mem_add mx-auto">
                        <i class="xi-plus-min fs_20"></i>
                    </button>
                    <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic"><?= translate('그룹원 추가', $userLang) ?></p>
                </div>
            </div>
        </div>
        <!--장소이름-->
        <div class="location_wrap">
            <div class="d-flex tab_scroll scroll_bar_x">
                <div class="checks location_wrap_btn">
                    <label>
                        <input type="radio" name="rd1" disabled>
                        <div class="ic_box border btn-gray_200 text_gray rounded_08 py-3 px_12">
                            <div class="chk_p">
                                <p class="text_dynamic">수학학원</p>
                            </div>
                        </div>

                    </label>
                </div>
                <div class="checks location_wrap_btn">
                    <label>
                        <input type="radio" name="rd1" checked>
                        <div class="ic_box border btn-gray_200 text_gray rounded_08 py-3 px_12">
                            <div class="chk_p">
                                <p class="text_dynamic">미술학원</p>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="checks location_wrap_btn">
                    <label>
                        <input type="radio" name="rd1">
                        <div class="ic_box border btn-gray_200 text_gray rounded_08 py-3 px_12">
                            <div class="chk_p ">
                                <p class="text_dynamic">메이플베어 영어학원</p>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="checks location_wrap_btn">
                    <label>
                        <input type="radio" name="rd1">
                        <div class="ic_box border btn-gray_200 text_gray rounded_08 py-3 px_12">
                            <div class="chk_p ">
                                <p class="text_dynamic">울랄라 피아노학원</p>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="checks location_wrap_btn">
                    <label>
                        <input type="radio" name="rd1">
                        <div class="ic_box border btn-gray_200 text_gray rounded_08 py-3 px_12">
                            <div class="chk_p ">
                                <p class="text_dynamic">귀가</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <!-- 루트 -->
    <div class="container h-100 tab_scroll_y scoll_bar_y">
        <div class="route_wrap h-100">
            <div class="border-bottom pb_24">
                <p class="fs_32 fw_800 mb-3">34<span class="fs_18 fw_700">분</span></p>
                <p class="fs_12 text_light_gray">20:38 ~ 21:12<span> | </span><span>1,500원</span></p>
                <p class="w-100 bg_gray_300 text-center text_light_gray rounded-pill fs_12 fw_700 py-2 mt_20">34<span class="fs_10">분</span></p>
            </div>
            <div class="route_item_box ">
                <div class="">
                    <div class="route_item_row d-flex align-items-center start_point">
                        <p class="item_time fs_11 text_light_gray py-3 text-left">20:38</p>
                        <div class="item_img">
                            <img src="<?=CDN_HTTP?>/img/mark_departure.png" width="30px" alt="출발" />
                        </div>
                        <p class="fs_16 fw_600 pl_06 text_dynamic line_h1_3">서울특별시 영등포구 문래동서울특별시 영등포구 문래동</p>
                    </div>
                    <div class="route_item_row d-flex align-items-center py-3">
                        <p class="item_time fs_11 fc_gray_600 fw_500 pl-2 text-center">34분</p>
                        <div class="item_img"></div>
                        <p class="fs_12 text_gray pl_06">도보 749m</p>
                    </div>
                    <div class="route_item_row d-flex align-items-center py-3">
                        <p class="item_time fs_11 fc_gray_600 fw_500 pl-2 text-center">34분</p>
                        <div class="item_img"></div>
                        <p class="fs_12 text_gray pl_06">도보 749m</p>
                    </div>
                    <div class="route_item_row d-flex align-items-center py-3 ">
                        <p class="item_time fs_11 fc_gray_600 fw_500 pl-2 text-center">34분</p>
                        <div class="item_img"></div>
                        <p class="fs_12 text_gray pl_06">도보 749m</p>
                    </div>
                    <div class="route_item_row d-flex align-items-center py-3 ">
                        <p class="item_time fs_11 fc_gray_600 fw_500 pl-2 text-center">34분</p>
                        <div class="item_img"></div>
                        <p class="fs_12 text_gray pl_06">도보 749m</p>
                    </div>
                    <div class="route_item_row d-flex align-items-center py-3 ">
                        <p class="item_time fs_11 fc_gray_600 fw_500 pl-2 text-center">34분</p>
                        <div class="item_img"></div>
                        <p class="fs_12 text_gray pl_06">도보 749m</p>
                    </div>
                    <div class="route_item_row d-flex align-items-center py-3 ">
                        <p class="item_time fs_11 fc_gray_600 fw_500 pl-2 text-center">34분</p>
                        <div class="item_img"></div>
                        <p class="fs_12 text_gray pl_06">도보 749m</p>
                    </div>
                    <div class="route_item_row d-flex align-items-center py-3 ">
                        <p class="item_time fs_11 fc_gray_600 fw_500 pl-2 text-center">34분</p>
                        <div class="item_img"></div>
                        <p class="fs_12 text_gray pl_06">도보 749m</p>
                    </div>
                    <div class="route_item_row d-flex align-items-center py-3 ">
                        <p class="item_time fs_11 fc_gray_600 fw_500 pl-2 text-center">34분</p>
                        <div class="item_img"></div>
                        <p class="fs_12 text_gray pl_06">도보 749m</p>
                    </div>
                    <div class="route_item_row d-flex align-items-center py-3 ">
                        <p class="item_time fs_11 fc_gray_600 fw_500 pl-2 text-center">34분</p>
                        <div class="item_img"></div>
                        <p class="fs_12 text_gray pl_06">도보 749m</p>
                    </div>
                    <div class="route_item_row d-flex align-items-center py-3 end_point">
                        <p class="item_time fs_11 text_light_gray text-left">20:38</p>
                        <div class="item_img">
                            <img src="<?=CDN_HTTP?>/img/mark_arrival.png" width="30px" alt="도착" />
                        </div>
                        <p class="fs_16 fw_600 pl_06 text_dynamic line_h1_3">한화손해보험 본사한화손해보험 본사한화손해보험 본사</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
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


<script>
// 지도 마커클릭시 상세내역 보여짐
$('.point').click(function() {
    $('.point').click(function() {
        $(this).find('.infobox').addClass('on');
        $('.point').not(this).find('.infobox').removeClass('on');
    });
});

// 바텀시트 업다운
$('.opt_bottom .top_bar_wrap').click(function() {
    $('.opt_bottom').toggleClass('on');
});
</script>
<?php
include $_SERVER['DOCUMENT_ROOT']."/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>