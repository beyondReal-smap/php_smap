<?php
$title = "일정";
$b_menu = "3";
$_GET['hd_num'] = '5';
include_once("./inc/head.php");
?>

<div class="container sub_pg bg_main px-0">

    <div class="sch_wrap_top">
        <div class="fixed_top sch_cld_wrap bg-white pt-3 border-bottom">
            <div class="cld_head_wr ">
                <div class="add_cal_tit">
                    <div class="sel_month d-inline-flex">
                        <img class="mr-2" src="./img/sel_month.png" alt="월 선택 아이콘" style="width:1.6rem; ">
                        <p class="fs_15 fw_600">2023년 09월</p>
                    </div>
                    <div class="d-flex">
                        <button type="button" class="btn h-auto px-1 pl-3 mr-3"><i class="xi-angle-left-min"></i></button>
                        <button type="button" class="btn h-auto px-1 pr-3"><i class="xi-angle-right-min"></i></button>
                    </div>
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
        <div class="sch_wrap">
            <div class="px_16 pt_22 scroll_bar_y">
                <!-- 내용 없을 때 박스 -->
                <div class="border rounded-lg px_16 py_16 none_box mb_25">
                    <div class="text-center">
                        <p class="fs_14 text_gray text_dynamic">일정을 생성해주세요!</p>
                        <button type="button" class="btn btn-secondary btn-sm fc_primary pl_14 pr_11 mt_12 mx-auto" onclick="location.href='schedule_form.php'">일정 생성 하러가기<i class="xi-angle-right-min ml_19"></i></button>
                    </div>
                </div>
                <div>
                    <div class="mb_25">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="w_fit">
                                <a href="#" class="d-flex align-items-center">
                                    <div class="prd_img flex-shrink-0 mr_12">
                                        <div class="rect_square rounded_14">
                                            <img src="./img/sample01.png" alt="이미지"/>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="fs_14 fw_500 text_dynamic mr-2">나</p>
                                        <div class="d-flex align-items-center flex-wrap">
                                            <p class="fs_12 fw_400 text_dynamic fc_green line_h1_2 mt-1 mr-2"></p>
                                            <p class="fs_12 fw_400 text_dynamic fc_green line_h1_2 mt-1"></p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <a href="schedule_form.php" class="fs_13 fc_navy"><i class="xi-plus-min"></i>일정 추가하기</a>
                        </div>
                        <div class="mt-3">
                            <div class="border rounded-lg pl_20 pr_16 py_08 bg-white">
                                <div class="py_08 list_routine">
                                    <a href="schedule_form.php">
                                        <div class="border-bottom pb_16 ml-3">
                                            <div class="d-flex aling-items-center justify-content-between">
                                                <p class="fs_13 fw_500 text_gray text_dynamic position-relative slash4 text-left">하루종일</p>
                                                <i class="xi-angle-right-min fs_15 text_gray"></i>
                                            </div>
                                            <div class="mt-3">
                                                <div class="">
                                                    <p class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line1_text line_h1_4 text_dynamic w_fit">회사 회사 회사에 출근 회사! 회사 회사 회사에 출근 회사! 회사 회사 회사에 출근 회사!</p>
                                                </div>
                                                <p class="text_dynamic fs_13 text_light_gray line2_text line_h1_4 mt-2 text-left">서울 영등포구 여의대로56서울 영등포구 여의대로56서울 영등포구 여의대로56</p>
                                                <p class="text_dynamic fs_13 fw_600 fc_navy mt-3 line_h1_4 text-left">물건 발송 일정 확인, 프로젝트 회의 잡기, 프로젝트, 물건 발송 일정 확인, 프로젝트 회의 잡기, 프로젝트</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="py_08 list_routine">
                                    <a href="schedule_form.php">
                                        <div class="border-bottom pb_16 ml-3">
                                            <div class="d-flex aling-items-center justify-content-between">
                                                <p class="fs_13 fw_500 text_gray text_dynamic position-relative slash4 text-left">오전 8:40 ~ 오후 6:20</p>
                                                <i class="xi-angle-right-min fs_15 text_gray"></i>
                                            </div>
                                            <div class="mt-3">
                                                <div class="">
                                                    <p class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line1_text line_h1_4 text_dynamic w_fit">회사</p>
                                                </div>
                                                <p class="text_dynamic fs_13 text_light_gray line2_text line_h1_4 mt-2 text-left">서울 영등포구 여의대로56</p>
                                                <p class="text_dynamic fs_13 fw_600 fc_navy mt-3 line_h1_4 text-left">물건 발송 일정 확인, 프로젝트 회의 잡기, 프로젝트</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb_25">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="w_fit">
                                <a href="#" class="d-flex align-items-center">
                                    <div class="prd_img flex-shrink-0 mr_12">
                                        <div class="rect_square rounded_14">
                                            <img src="./img/sample01.png" alt="이미지"/>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="fs_14 fw_500 text_dynamic mr-2">다연</p>
                                        <div class="d-flex align-items-center flex-wrap">
                                            <p class="fs_12 fw_400 text_dynamic fc_green line_h1_2 mt-1 mr-2"></p>
                                            <p class="fs_12 fw_400 text_dynamic fc_green line_h1_2 mt-1"></p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <a href="schedule_form.php" class="fs_13 fc_navy"><i class="xi-plus-min"></i>일정 추가하기</a>
                        </div>
                        <div class="mt-3">
                            <div class="border rounded-lg pl_20 pr_16 py_08 bg-white">
                                <div class="py_08 list_routine">
                                    <a href="schedule_form.php">
                                        <div class="border-bottom pb_16 ml-3">
                                            <div class="d-flex aling-items-center justify-content-between">
                                                <p class="fs_13 fw_500 text_gray text_dynamic position-relative slash4 text-left">오전 8시 40분 ~ 오후 1시 20분</p>
                                                <i class="xi-angle-right-min fs_15 text_gray"></i>
                                            </div>
                                            <div class="mt-3">
                                                <div class="">
                                                    <p class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line1_text line_h1_4 text_dynamic w_fit">학교</p>
                                                </div>
                                                <p class="text_dynamic fs_13 text_light_gray line2_text line_h1_4 mt-2 text-left">서울특별시 강남구 테헤란로 123</p>
                                                <p class="text_dynamic fs_13 fw_600 fc_navy mt-3 line_h1_4 text-left">색연필 / 오늘 받아쓰기 잘 봐!</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="py_08 list_routine">
                                    <a href="schedule_form.php">
                                        <div class="border-bottom pb_16 ml-3">
                                            <div class="d-flex aling-items-center justify-content-between">
                                                <p class="fs_13 fw_500 text_gray text_dynamic position-relative slash4 text-left">오후 2시 ~ 오후 4시 20분</p>
                                                <i class="xi-angle-right-min fs_15 text_gray"></i>
                                            </div>
                                            <div class="mt-3">
                                                <div class="">
                                                    <p class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line1_text line_h1_4 text_dynamic w_fit">피아노학원</p>
                                                </div>
                                                <p class="text_dynamic fs_13 text_light_gray line2_text line_h1_4 mt-2 text-left">서울 영등포구 선유로11길</p>
                                                <p class="text_dynamic fs_13 fw_600 fc_navy mt-3 line_h1_4 text-left">어린이 바이엘</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="button" class="btn w-100 floating_btn rounded b_botton_2" onclick="location.href='schedule_form.php'"><i class="xi-plus-min mr-3"></i> 일정 추가하기</button>

</div>

<script>

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
include_once("./inc/tail.php");
?>

