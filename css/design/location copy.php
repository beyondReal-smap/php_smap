<?php
$title = "위치";
$b_menu = "4";
$_GET['hd_num'] = '5';
include_once("./inc/head.php");
?>

<style>
    html{
        height: 100%;
    }
    .h_menu{
        background-color: #fff !important;
    }
    .sub_pg {
        height: calc(100% - 4.8rem) !important;
        min-height: calc(100% - 4.8rem) !important;
        overflow: hidden;
        padding-top: 4.8rem;
    }
</style>
<div class="container sub_pg px-0 py-0 h-100">
    <div class="map_wrap_h_sm">
        <div class="map_wrap_h_div">
            <div class="map_rt">
                <div class="map_rt_round">
                    <div class="map_rt_img">
                        <div class="rect_square rounded-pill">
                            <img src="./img/sample01.png" alt="이미지"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="map_ab">
                <div class="point point1">
                    <img src="./img/map_point.png" width="28px" alt="이미지"/>
                    <div class="infobox rounded-sm bg-white px_08 py_08"> <!-- 임시 on 추가되면 상세내역 보여집니다.-->
                        <p class="fs_8 text_dynamic">오후 6:00 ~ 오후 8:30</p>
                        <p class="fs_12 fw_500 text_dynamic line_h1_2 mt-2">미술학원</p>
                    </div>
                </div>
                <div class="point point2">
                    <img src="./img/map_point.png" width="28px" alt="이미지"/>
                    <div class="infobox rounded-sm bg-white px_08 py_08">
                        <p class="fs_8 text_dynamic">오후 6:00 ~ 오후 8:30</p>
                        <p class="fs_12 fw_500 text_dynamic line_h1_2 mt-2">미술학원미술 원미술 원미술</p>
                    </div>
                </div>
                <div class="point point3">
                    <img src="./img/map_point.png" width="28px" alt="이미지"/>
                    <div class="infobox rounded-sm bg-white px_08 py_08">
                        <p class="fs_8 text_dynamic">오후 6:00 ~ 오후 8:30</p>
                        <p class="fs_12 fw_500 text_dynamic line_h1_2 mt-2">미술학원</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- G-2 위치 페이지 -->
    <div class="opt_bottom opt_bottom_2">
        <div class="top_bar_wrap text-center pt_08">
            <img src="./img/top_bar.png" class="top_bar" width="34px" alt="탑바"/>
            <img src="./img/btn_bl_arrow.png" class="top_down mx-auto" width="12px" alt="탑업"/>
        </div>
        <div class="scroll_bar_y pb_100 scroll_bar_none">
            <!--프로필-->
            <div class="mem_wrap">
                <div class="d-flex tab_scroll scroll_bar_x">
                    <div class="checks mem_box w_fit mr_12 ">
                        <label>
                            <input type="radio" name="rd2" checked>
                            <div class="prd_img mx-auto">
                                <div class="rect_square rounded_14">
                                    <img src="./img/sample01.png" alt="이미지"/>
                                </div>
                            </div>
                            <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">최지우 지우</p>
                        </label>
                    </div>
                    <div class="checks mem_box w_fit mr_12">
                        <label>
                            <input type="radio" name="rd2" >
                                <div class="prd_img mx-auto on_arm"> <!-- 알림왔을 때 on_arm 추가 -->
                                    <div class="rect_square rounded_14">
                                        <img src="./img/sample01.png" alt="이미지"/>
                                    </div>
                                </div>
                                <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">최지우 A⃝ #⃞</p>
                        </label>
                    </div>
                    <div class="checks mem_box w_fit mr_12">
                        <label>
                            <input type="radio" name="rd2">
                            <div class="prd_img mx-auto">
                                <div class="rect_square rounded_14">
                                    <img src="./img/sample01.png" alt="이미지"/>
                                </div>
                            </div>
                            <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">지우지우지우</p>
                        </label>
                    </div>
                    <div class="checks mem_box w_fit mr_12">
                        <label>
                            <input type="radio" name="rd2">
                            <div class="prd_img mx-auto">
                                <div class="rect_square rounded_14">
                                    <img src="./img/sample01.png" alt="이미지"/>
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
                                    <img src="./img/sample01.png" alt="이미지"/>
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
                                    <img src="./img/sample01.png" alt="이미지"/>
                                </div>
                            </div>
                            <p class="fs_12 fw_400 text-center mt-2 line_h1_2 line2_text text_dynamic">최지우</p>
                        </label>
                    </div>
                    <div class="mem_box w_fit mr_12"  data-toggle="modal" data-target="#schedule_member">
                        <button class="btn mem_add mx-auto">
                            <i class="xi-plus-min fs_20"></i>
                        </button>
                        <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic">그룹원 추가</p>
                    </div>
                    <div class="mem_box w_fit mr_12"  data-toggle="modal" data-target="#link_modal">
                        <button class="btn mem_add mx-auto">
                            <i class="xi-plus-min fs_20"></i>
                        </button>
                        <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic">그룹원 추가</p>
                    </div>
                </div>
            </div>
            <!-- 달력 -->
            <div class="sch_cld_wrap bg-white pt-3 border-bottom">
                <div class="cld_head_wr">
                    <div class="sel_month d-inline-flex pl_16">
                        <select class="form-none custom-select text-text re_de">
                            <option selected value="1">2023년 09월</option>
                            <option value="2">2023년 10월</option>
                            <option value="3">2023년 11월</option>
                            <option value="4">2023년 12월</option>
                        </select>
                    </div>
                    <div class="cld_head fs_12">
                        <ul>
                            <li class="sun">일</li>
                            <li>월</li>
                            <li>화</li>
                            <li>수</li>
                            <li>목</li>
                            <li>금</li>
                            <li class="sat">토</li>
                        </ul>
                    </div>
                </div>
                <div class="cld_date_wrap">
                    <form>
                        <div class="date_conent">
                            <div class="cld_content">
                                <div class="cld_body fs_15 fw_500">
                                    <ul>
                                        <li><div class="lastday"><span>31</span></div></li>
                                        <li><div class="lastday"><span>1</span></div></li>
                                        <li><div class="lastday"><span>2</span></div></li>
                                        <li><div class="lastday"><span>3</span></div></li>
                                        <li><div class="today"><span>4</span></div></li>
                                        <li><div class=""><span>5</span></div></li>
                                        <li><div class=" sat"><span>6</span></div></li>
                                        <li><div class="sun"><span>7</span></div></li>
                                        <li><div class=""><span>8</span></div></li>
                                        <li><div class=""><span>9</span></div></li>
                                        <li><div class="schdl"><span>10</span></div></li>
                                        <li><div class="schdl"><span>11</span></div></li>
                                        <li><div class="schdl"><span>12</span></div></li>
                                        <li><div class="sat schdl"><span>13</span></div></li>
                                        <li><div class="sun"><span>14</span></div></li>
                                        <li><div class=""><span>15</span></div></li>
                                        <li><div class=""><span>16</span></div></li>
                                        <li><div class=""><span>17</span></div></li>
                                        <li><div class=""><span>18</span></div></li>
                                        <li><div class=""><span>19</span></div></li>
                                        <li><div class="sat"><span>20</span></div></li>
                                        <li><div class="sun"><span>21</span></div></li>
                                        <li><div class=""><span>22</span></div></li>
                                        <li><div class=""><span>23</span></div></li>
                                        <li><div class="active act_ing act_start"><span>24</span></div></li>
                                        <li><div class="act_ing"><span>25</span></div></li>
                                        <li><div class="act_ing"><span>26</span></div></li>
                                        <li><div class="sat act_ing"><span>27</span></div></li>
                                        <li><div class="sun act_ing"><span>28</span></div></li>
                                        <li><div class="act_ing"><span>29</span></div></li>
                                        <li><div class="act_ing"><span>30</span></div></li>
                                        <li><div class="active act_ing act_end"><span>31</span></div></li>
                                        <li><div class="sun lastday"><span></span></div></li>
                                        <li><div class="sun lastday"><span></span></div></li>
                                        <li><div class="sun lastday"><span></span></div></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="down_wrap text-center pt_08 pb-3">
                    <img src="./img/btn_bl_arrow.png" class="top_down mx-auto" width="12px" alt="탑다운"/>
                </div>
            </div>
            <div class="sch_summary h-0 row mx-0 align-items-start bg_main py_20 border-bottom">
                <div class="sch_summary_div px-2">
                    <p class="fs_13 text_gray text-center">일정개수</p>
                    <p class="fs_16 fw_700 fc_mian_sec text-center text_dynamic mt_07">3<span>개</span></p>
                </div>
                <div class="sch_summary_div border-left border-right px-2">
                    <p class="fs_13 text_gray text-center">이동거리</p>
                    <p class="fs_16 fw_700 fc_mian_sec text-center text_dynamic mt_07">3565.42<span>km</span></p>
                </div>
                <div class="sch_summary_div px-2">
                    <p class="fs_13 text_gray text-center">이동시간</p>
                    <p class="fs_16 fw_700 fc_mian_sec text-center text_dynamic mt_07">5622<span>분</span></p>
                </div>
            </div>
            <div class="pt_20 px_16">
                <div class="border rounded-lg px_16 py_16 d-flex align-items-center justify-content-between mb-3">
                    <div class="mr-2">
                        <p class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line_h1_4 w_fit mb-2">집</p>
                        <p class="line1_text fs_13 fw_700 text_dynamic line_h1_4 py_02">서울 영등포구 선유로11길 서울 영등포구 선유로11길 서울 영등포구 선유로11길</p>
                        <p class="fs_11 fc_gray_700">체류시간 <span class="fc_mian_sec fs_13 fw_600 pl-2">4시간 23분</span></p>
                    </div>
                    <!-- 오너/리더일 경우 -->
                    <div class="d-flex align-items-center trace_box_btn_box">
                        <button type="button" class="btn text_gray fs_20 px-2 mr-2 trace_armbtn on" data-toggle="modal" data-target="#arm_setting_modal"></button>
                        <button type="button" class="btn text_gray fs_20 h_fit_im px-2" data-toggle="modal" data-target="#location_delete_modal"><i class="xi-close fs_15 fw_800"></i></button>
                    </div>
                </div>
                <div class="border rounded-lg px_16 py_16 d-flex align-items-center justify-content-between mb-3">
                    <div class="mr-2">
                        <p class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line_h1_4 w_fit mb-2">피아노학원</p>
                        <p class="line1_text fs_13 fw_700 text_dynamic line_h1_4 py_02">서울 영등포구 선유로11길 서울 영등포구 선유로11길 서울 영등포구 선유로11길 서울 영등포구 선유로11길</p>
                        <p class="fs_11 fc_gray_700">체류시간 <span class="fc_mian_sec fs_13 fw_600 pl-2">4시간 23분</span></p>
                    </div>
                </div>
                <div class="border rounded-lg px_16 py_16 d-flex align-items-center justify-content-between mb-3">
                    <div class="mr-2">
                        <p class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line_h1_4 w_fit mb-2">피아노학원</p>
                        <p class="line1_text fs_13 fw_700 text_dynamic line_h1_4 py_02">서울 영등포구 선유로11길</p>
                        <p class="fs_11 fc_gray_700">체류시간 <span class="fc_mian_sec fs_13 fw_600 pl-2">4시간 23분</span></p>
                    </div>
                </div>
            </div>
            <div class="bg_main px_20 py_20">
                <p class="fs_16 fw_600 pt-2 pb-3">추천장소</p>
                <div class="border_orange rounded-lg px_16 py_16 d-flex align-items-center justify-content-between mb-3">
                    <div class="mr-2">
                        <p class="fs_13 fc_orange rounded_04 bg_fff5ea text-center px_06 py_02 text_dynamic line1_text line_h1_4 w_fit mb-2">추천장소</p>
                        <p class="line1_text fs_13 fw_700 text_dynamic line_h1_4 py_02">서울 영등포구 선유로11길 서울 영등포구 선유로11길 서울 영등포구 선유로11길</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn text_gray fs_20 h_fit_im px-2"><i class="xi-plus fc_orange"></i></button>
                    </div>
                </div>
                <div class="border_orange rounded-lg px_16 py_16 d-flex align-items-center justify-content-between mb-3">
                    <div class="mr-2">
                        <p class="fs_13 fc_orange rou nded_04 bg_fff5ea text-center px_06 py_02 text_dynamic line1_text line_h1_4 w_fit mb-2">추천장소</p>
                        <p class="line1_text fs_13 fw_700 text_dynamic line_h1_4 py_02">서울 영등포구 선유로11길</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn text_gray fs_20 h_fit_im px-2"><i class="xi-plus fc_orange"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ml-3 location_point_list_wrap tab_scroll scroll_bar_x pr_16 pb-0">
    <div class="mr-3 sdfs w-100">
        
    </div>
    <!-- <div class="sdfs">
        <div class="trace_box d-flex align-items-center justify-content-between border rounded_12 bg-white px_16 py_16">
            <div class="mr-3 trace_box_txt_box">
                <div class="mr-2">
                    <p class="fs_13 fc_orange rounded_04 bg_fff5ea text-center px_06 py_02 text_dynamic line1_text line_h1_4 w_fit mb-2">추천장소</p>
                </div>
                <p class="line1_text fs_13 fw_700 text_dynamic line_h1_4">서울 영등포구 선유로11길</p>
            </div>
            <div class="d-flex align-items-center">
                <button type="button" class="btn text_gray fs_20 h_fit_im px-2"><i class="xi-plus"></i></button>
            </div>
        </div>
        <div class="trace_box d-flex align-items-center justify-content-between border rounded_12 bg-white px_16 py_16">
            <div class="mr-3 trace_box_txt_box">
                <div class="mr-2">
                    <p class="fs_13 fc_orange rounded_04 bg_fff5ea text-center px_06 py_02 text_dynamic line1_text line_h1_4 w_fit mb-2">추천장소</p>
                </div>
                <p class="line1_text fs_13 fw_700 text_dynamic line_h1_4">서울 영등포구 선유로11길 서울 영등포구 선유로11길</p>
            </div>
            <div class="d-flex align-items-center">
                <button type="button" class="btn text_gray fs_20 h_fit_im px-2"><i class="xi-plus"></i></button>
            </div>
        </div>
    </div> -->
</div>

<button type="button" class="btn w-100 floating_btn rounded" onclick="location.href='location_form.php'"><i class="xi-plus-min mr-3"></i> 위치 추가하기</button>


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
                <p class="fs_14 fw_400 text_gray mt-3 text_dynamic text-center">위치와 관련된 일정에 대한 알림을 설정합니다.</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0">알림설정하기</button>
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
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0">삭제하기</button>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="./img/modal_close.png" width="24px"></button>
                </div>
                <p class="fs_18 fw_700 text_dynamic line_h1_2">초대장은 어떻게 보낼까요?</p>
            </div>
            <div class="modal-body">
                <ul>
                    <li>
                        <a href="#" class="d-flex align-items-center justify-content-between py_07">
                            <div class="d-flex align-items-center">   
                                <img src="./img/ico_kakao.png" alt="카카오톡 열기" width="40px" class="mr_12"/>
                                <p class="fs_15 fw_500 gray_900">카카오톡 열기</p>
                            </div>
                            <i class="xi-angle-right-min fs_15 text_gray"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="d-flex align-items-center justify-content-between py_07">
                            <div class="d-flex align-items-center">   
                                <img src="./img/ico_link.png" alt="초대 링크 복사" width="40px" class="mr_12"/>
                                <p class="fs_15 fw_500 gray_900">초대 링크 복사</p>
                            </div>
                            <i class="xi-angle-right-min fs_15 text_gray"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="d-flex align-items-center justify-content-between py_07">
                            <div class="d-flex align-items-center">   
                                <img src="./img/ico_address.png" alt="연락처 열기" width="40px" class="mr_12"/>
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
    $('.point').click(function () {
         $('.point').click(function () {
            $(this).find('.infobox').addClass('on');
            $('.point').not(this).find('.infobox').removeClass('on');
        });
    });

    
    // 바텀시트 업다운
    $('.opt_bottom .top_bar_wrap').click(function() {
        $('.opt_bottom').toggleClass('on');
    });

    // 바텀시트 업다운
    $('.down_wrap').click(function() {
        var cldDateWrap = $('.sch_cld_wrap .cld_date_wrap');

        // .on 클래스를 토글
        cldDateWrap.toggleClass('on');

        // .on 클래스의 유무에 따라 이미지 파일 이름 변경
        var imgSrc = cldDateWrap.hasClass('on') ? 'btn_tl_arrow.png' : 'btn_bl_arrow.png';
        $('.down_wrap img.top_down').attr('src', './img/' + imgSrc);

        // CSS 스타일 추가
        if (cldDateWrap.hasClass('on')) {
            $('.sch_wrap').css('padding-top', '34.7rem');
        } else {
            $('.sch_wrap').css('padding-top', '15.3rem');
        }
    });
</script>



<?php 
include_once("./inc/b_menu.php");
include_once("./inc/modal.php");

?>