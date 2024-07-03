<?php
$b_menu = "1";
$_GET['hd_num'] = '1';
include_once("./inc/head.php");
?>
<style>
    .head_01{ background-color: #FBFBFF;}
</style>

<div class="idx_pg bg_main">
    <div class="container bg_main px-0">
        <div class="mt-3 px_16">
            <!--D-6 멤버 스케줄 미참석 팝업 임시로 넣어놓았습니다.-->
            <div class="border rounded-lg px_16 py_16 bg-white top_weather" data-toggle="modal" data-target="#push_modal">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <!-- 로딩아닐때 -->
                    <div class="date_weather d-flex align-items-center flex-wrap">
                        <div class="d-flex align-items-center fs_14 fw_600 text_dynamic mr-1 mb_08">2023.09.06(수)<span class="ml-1"><img src="./img/weather_8.png" width="18px" alt="날씨"/></span></div>
                        <div class="d-flex align-items-center mb_08 mr-3">
                            <p class="ml-1 fs_11 fw_600 text-text fw_500 mr-2"><span class="fs_11 text_light_gray mr-1">최저</span>2°C</p>
                            <p class="ml-1 fs_11 fw_600 fc_red fw_500"><span class="fs_11 text_light_gray mr-1">최고</span>2°C</p>
                        </div>
                    </div>
                    <!-- 로딩할때 사용 -->
                    <!-- <div class="date_weather d-flex align-items-center flex-wrap">
                        <div class="d-flex align-items-center fs_14 fw_600 text_dynamic mr-1 mb_08">2023.09.06(수)<span class="loader loader_sm mr-2 ml-2"></span></div>
                    </div> -->
                    <div class="d-flex align-items-center mb_08 p_address">
                        <p class="fs_12 text_light_gray fw_500 text_dynamic">여의도동 ·</p>
                        <p class="fs_12 text_light_gray fw_500 text_dynamic">서울시</p>
                    </div>
                </div>
                <p class="fs_12 text_gray text_dynamic p_content line_h1_2">오늘은 구름이 조금 있지만 햇살이 보일 거예요. <span class="ml-1"><img src="./img/weather_1.png" width="18px" alt="날씨"/></p>
                <!-- <p class="fs_12 text_gray text_dynamic p_content line_h1_2">오늘은 구름이 많이 끼어 있어요. <span class="ml-1"><img src="./img/weather_2.png" width="18px" alt="날씨"/></p>
                <p class="fs_12 text_gray text_dynamic p_content line_h1_2">오늘은 안개가 많이 끼었어요. 시야가 흐릴 수 있으니 조심하세요. <span class="ml-1"><img src="./img/weather_3.png" width="18px" alt="날씨"/></p>
                <p class="fs_12 text_gray text_dynamic p_content line_h1_2">오늘은 비와 눈이 섞여 내릴 거예요. 따뜻한 옷과 우산을 챙기세요. <span class="ml-1"><img src="./img/weather_4.png" width="18px" alt="날씨"/></p>
                <p class="fs_12 text_gray text_dynamic p_content line_h1_2">오늘은 눈이 내릴 예정이에요. 미끄럼에 주의하세요. <span class="ml-1"><img src="./img/weather_5.png" width="18px" alt="날씨"/></p>
                <p class="fs_12 text_gray text_dynamic p_content line_h1_2">오늘은 천둥번개가 치는 날입니다. 가능하다면 실내에서 지내세요. <span class="ml-1"><img src="./img/weather_6.png" width="18px" alt="날씨"/></p>
                <p class="fs_12 text_gray text_dynamic p_content line_h1_2">오늘은 하늘이 맑아요. 기분 좋은 하루 보내세요. <span class="ml-1"><img src="./img/weather_7.png" width="18px" alt="날씨"/></p>
                <p class="fs_12 text_gray text_dynamic p_content line_h1_2">오늘은 비가 내릴 예정이에요. 우산을 챙기세요. <span class="ml-1"><img src="./img/weather_8.png" width="18px" alt="날씨"/></p> -->
                <!-- 로딩할때 사용 -->
                <div class="">
                    <!-- <span class="loader loader_sm mr-2 mt-2 p_content mb-3"></span> --> <!-- 혹시 필요시 사용 -->
                    <!-- <p class="fs_12 text_light_gray text_dynamic p_content line_h1_2">잠시만 기다려주세요! 기상 데이터를 가져오는 중입니다.!</p> -->
                </div>
            </div>
            <div class="mt_25">
                <p class="tit_h2">위치</p>
                <div class="mt-4">
                    <div class="mb_25">
                        <div class="d-flex align-items-center">
                            <div class="w_fit">
                                <a href="mem_location.php" class="d-flex align-items-center">
                                    <div class="prd_img flex-shrink-0 mr_12">
                                        <div class="rect_square rounded_14">
                                            <img src="./img/sample01.png" alt="이미지"/>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="fs_14 fw_500 text_dynamic mr-2">김은정 @ㅇ0ㅇ</p>
                                        <div class="d-flex align-items-center flex-wrap">
                                            <p class="fs_12 fw_400 text_dynamic fc_green line_h1_2 mt-1 mr-2">이동중 ·</p>
                                            <p class="fs_12 fw_400 text_dynamic fc_green line_h1_2 mt-1">3km/h</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="bg-white d-flex align-items-start justify-content-between border rounded-lg py_16 px-2">
                                <div class="text-center w_50 pr-2 border-right">
                                    <p class="fs_14 fw_500">현재위치</p>
                                    <p class="fs_12 fc_gray_600 fw_500 line1_text text_dynamic mt_08">서울시 영등포구 문래로 1234-5 64</p>
                                    <div class="fc_primary rounded-pill bg_secondary mx-auto text-center px_08 py_03 text_dynamic w_fit h_fit_im d-flex flex-wrap align-items-center justify-content-center mt_08 re_time_txt">
                                        <p class="fs_12 pr_03 ">도착함 ·</p>
                                        <p class="fs_12">오후 03:20</p>
                                    </div>
                                </div>
                                <div class="text-center w_50 pl-2">
                                    <p class="fs_14 fw_500 text_gray ">다음 위치</p>
                                    <p class="fs_12 text_light_gray line1_text text_dynamic mt_08">서울시 영등포구 문래로 1234-5 64</p>
                                    <div class="text_gray rounded-pill bg_efefef mx-auto text-center px_08 py_03 text_dynamic w_fit h_fit_im d-flex flex-wrap align-items-center justify-content-center mt_08 re_time_txt">
                                        <p class="fs_11 pr_03">오후 04:20</p>
                                        <p class="fs_11">예정</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb_25">
                        <div class="d-flex align-items-center">
                            <div class="w_fit">
                                <a href="mem_location.php" class="d-flex align-items-center">
                                    <div class="prd_img flex-shrink-0 mr_12">
                                        <div class="rect_square rounded_14">
                                            <img src="./img/sample01.png" alt="이미지"/>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="fs_14 fw_500 text_dynamic mr-2">김은정</p>
                                        <div class="d-flex align-items-center flex-wrap">
                                            <p class="fs_12 fw_400 text_dynamic text_light_gray line_h1_2 mt-1 mr-2">집 27분 -</p>
                                            <p class="fs_12 fw_400 text_dynamic text_light_gray line_h1_2 mt-1">체류중</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="bg-white d-flex align-items-start justify-content-between border rounded-lg py_16 px-2">
                                <div class="text-center  w_50 pr-2 border-right">
                                    <p class="fs_14 fw_500">현재위치</p>
                                    <p class="fs_12 fc_gray_600 fw_500 line1_text text_dynamic mt_08">서울시 영등포구 문래로 1234-5 64</p>
                                    <div class="fc_primary rounded-pill bg_secondary mx-auto text-center px_08 py_03 text_dynamic w_fit h_fit_im d-flex flex-wrap align-items-center justify-content-center mt_08 re_time_txt">
                                        <p class="fs_12 pr_03 ">도착예정 ·</p>
                                        <p class="fs_12">오후 03:20</p>
                                    </div>
                                </div>
                                <div class="text-center  w_50 pl-2">
                                    <p class="fs_14 fw_500 text_gray ">다음 위치</p>
                                    <p class="fs_12 text_light_gray line1_text text_dynamic mt_08">-</p>
                                    <!-- <div class="text_gray rounded-pill bg_efefef mx-auto text-center px_08 py_03 text_dynamic w_fit h_fit_im d-flex flex-wrap align-items-center justify-content-center mt_08 re_time_txt">
                                        <p class="fs_11 pr_03">오후 04:20</p>
                                        <p class="fs_11">예정</p>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
                <!-- 내용 없을 때 박스 -->
                <div class="mt-4 pb-4">
                    <div class="border rounded-lg px_16 py_16 none_box mb_25">
                        <div class="text-center">
                            <p class="fs_14 text_gray text_dynamic">그룹을 생성해주세요!</p>
                            <button type="button" class="btn btn-secondary btn-sm fc_primary pl_14 pr_11 mt_12 mx-auto" onclick="location.href='group_create.php'">그룹 생성 하러가기<i class="xi-angle-right-min ml_19"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bar"></div>
    <div class="container bg_main_sub px-0 pb_100 main_sch">
        <div class="pt_20 px_16">
            <p class="tit_h2 mb-4">일정</p>
            <div class="mb_25">
                <div class="d-flex align-items-center">
                    <div class="w_fit">
                        <a href="mem_location.php" class="d-flex align-items-center">
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
                </div>
                <div class="mt-3">
                    <div class="border rounded-lg pl_20 pr_16 py_08 bg-white">
                        <div class="py_08 list_routine">
                            <a href="schedule_form.php">
                                <div class="border-bottom pb_16 ml-3">
                                    <div class="d-flex aling-items-center justify-content-between">
                                        <p class="fs_13 fw_500 text_gray text_dynamic position-relative slash4 text-left">오전 8:40 ~ 오후 6:20</p>
                                        <i class="xi-angle-right-min fs_15 text_gray"></i>
                                    </div>
                                    <div class="mt-3">
                                        <div class="">
                                            <p class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line1_text line_h1_4 text_dynamic w_fit">회사 회사 회사에 출근 회사! 회사 회사 회사에 출근 회사! 회사 회사 회사에 출근 회사!</p>
                                        </div>
                                        <p class="text_dynamic fs_13 text_light_gray line2_text line_h1_4 mt-2 text-left">서울 영등포구 여의대로56서울 영등포구 여의대로56서울 영등포구 여의대로56</p>
                                        <p class="text_dynamic fs_13 fw_600 fc_navy mt-3 line_h1_4 text-left">물건 발송 일정 확인, 프로젝트 회의 잡기, 프로젝트 〈응♥〉 힣♪』</p>
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
                <div class="d-flex align-items-center">
                    <div class="w_fit">
                        <a href="mem_location.php" class="d-flex align-items-center">
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
                </div>
                <div class="mt-3">
                    <!-- 내용 없을 때 박스 -->
                    <div class="border rounded-lg px_16 py_16 none_box mb_25">
                        <div class="text-center">
                            <p class="fs_14 text_gray text_dynamic">등록된 일정이 없습니다.</p>
                            <button type="button" class="btn btn-secondary btn-sm fc_primary pl_14 pr_11 mt_12 mx-auto" onclick="location.href='schedule_form.php'">일정 등록하러 가기<i class="xi-angle-right-min ml_19"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 클릭시 일정(.main_sch) 부분이 위로 올라가게 해주세요! -->
    <button type="button" class="btn w-100 floating_btn floating_btn_w100 rounded b_botton_2">일정  <i class="xi-angle-down-min ml-3"></i></button>
</div>



<!--D-6 멤버 스케줄 미참석 팝업-->
<div class="modal fade" id="push_modal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body px_16 pt_44_im pb_26_im">
                <div class="prd_img mx-auto d-flex">
                    <div class="rect_square rounded_14 flex-shrink-0">
                        <img src="./img/sample01.png" alt="이미지"/>
                    </div>
                </div>
                <p class="fs_16 fw_700 wh_pre mt-4 line_h1_3 text-center">다연님이 '피아노 학원'일정에
                도착하지 않았습니다.</p>
            </div>
            <div class="modal-footer px_16 py-0 bg-white">
                <button type="button" class="btn btn-block btn-md btn-primary mx-0 my-0 rounded-sm open_contact_modal_btn">전화하기</button>
                <div class="w-100 d-flex align-items-center">
                    <button type="button" class="btn btn-lg w-50 fw_400 fs_14 text_gray mx-0 mr-0 mb-3">알림 해제하기</button>
                    <button type="button" class="btn btn-lg w-50 fw_400 fs_14 text-primary mx-0 mr-0 mb-3">알림 유지하기</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!--D-7 연락처 목록-->
<div class="modal ad_ad fade second_modal " id="contact_modal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header flex-column px-0 py-0">
                <div class="w-100 pt_20 px_20 d-flex align-items-center justify-content-between ">
                    <div class="tit_h2">연락처</div>
                    <div></div>
                    <button type="button" class="btn h-auto p-0" data-dismiss="modal" aria-label="Close"><i class="xi-close fs_20"></i></button>
                </div>
                <div class="w-100 px_20 py_20">
                    <div class="w_fit">
                        <div class="d-flex align-items-center">
                            <div class="prd_img_50 mx-auto d-flex flex-shrink-0">
                                <div class="rect_square rounded_14 flex-shrink-0">
                                    <img src="./img/sample01.png" alt="이미지"/>
                                </div>
                            </div>
                            <div class="pl-3">
                                <p class="fs_14 fw_500 text_dynamic mr-2 mb-2">최지우최지우최지우최지우최지우</p>
                                <a href="tel:010-2222-333" class="fs_13 text_gray text_dynamic line_h1_2"><u>010-2222-3333</u></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body px-0 py-0 ">
                <div class="">
                    <div id="accordion" class="accordion_1">
                        <div class="card aco_list border-0" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            <div class="card-header border-bottom bg_f8faff" id="headingOne">
                                <div class="d-flex justify-content-between align-items-center pl_16 pr_20 ">
                                    <p class="fs_16 fw_700 d-flex align-items-center text_dynamic"><span class="mr_04"><img src="./img/ico_tel.png" width="20px" alt="전화"/></span>학교</p>
                                    <button class="btn btn-link position-relative h_fit_im aco_btn"></button>
                                </div>
                            </div>
                            <!-- 오픈할때 .collapse 클래스에 .show 추가-->
                            <div id="collapseOne" class="collapse " aria-labelledby="headingOne" data-parent="#accordion">
                                <div class="card-body m-0">
                                    <ul>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">학교 학교 학교돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">돌봄선생님돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray  text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card aco_list border-0" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <div class="card-header border-bottom bg_f8faff" id="headingTwo">
                                <div class="d-flex justify-content-between align-items-center pl_16 pr_20 ">
                                    <p class="fs_16 fw_700 d-flex align-items-center text_dynamic"><span class="mr_04"><img src="./img/ico_tel.png" width="20px" alt="전화"/></span>수학학원</p>
                                    <button class="btn btn-link position-relative h_fit_im aco_btn"></button>
                                </div>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                                <div class="card-body m-0">
                                    <ul>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">학교 학교 학교돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">돌봄선생님돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray  text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card aco_list border-0" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <div class="card-header border-bottom bg_f8faff" id="headingThree">
                                <div class="d-flex justify-content-between align-items-center pl_16 pr_20">
                                    <p class="fs_16 fw_700 d-flex align-items-center text_dynamic"><span class="mr_04"><img src="./img/ico_tel.png" width="20px" alt="전화"/></span>학교</p>
                                    <button class="btn btn-link position-relative h_fit_im aco_btn"></button>
                                </div>
                            </div>
                            <!-- 오픈할때 .collapse 클래스에 .show 추가-->
                            <div id="collapseThree" class="collapse " aria-labelledby="headingThree" data-parent="#accordion">
                                <div class="card-body m-0">
                                    <ul>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">학교 학교 학교돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">돌봄선생님돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray  text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card aco_list border-0" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            <div class="card-header border-bottom bg_f8faff" id="headingFour">
                                <div class="d-flex justify-content-between align-items-center pl_16 pr_20">
                                    <p class="fs_16 fw_700 d-flex align-items-center text_dynamic"><span class="mr_04"><img src="./img/ico_tel.png" width="20px" alt="전화"/></span>수학학원</p>
                                    <button class="btn btn-link position-relative h_fit_im aco_btn"></button>
                                </div>
                            </div>
                            <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
                                <div class="card-body m-0">
                                    <ul>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">학교 학교 학교돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">돌봄선생님돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray  text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card aco_list border-0" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            <div class="card-header border-bottom bg_f8faff" id="headingFive">
                                <div class="d-flex justify-content-between align-items-center pl_16 pr_20">
                                    <p class="fs_16 fw_700 d-flex align-items-center text_dynamic"><span class="mr_04"><img src="./img/ico_tel.png" width="20px" alt="전화"/></span>수학학원</p>
                                    <button class="btn btn-link position-relative h_fit_im aco_btn"></button>
                                </div>
                            </div>
                            <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordion">
                                <div class="card-body m-0">
                                    <ul>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">학교 학교 학교돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">돌봄선생님돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray  text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card aco_list border-0" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                            <div class="card-header border-bottom bg_f8faff" id="headingSix">
                                <div class="d-flex justify-content-between align-items-center pl_16 pr_20 ">
                                    <p class="fs_16 fw_700 d-flex align-items-center text_dynamic"><span class="mr_04"><img src="./img/ico_tel.png" width="20px" alt="전화"/></span>수학학원</p>
                                    <button class="btn btn-link position-relative h_fit_im aco_btn"></button>
                                </div>
                            </div>
                            <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#accordion">
                                <div class="card-body m-0">
                                    <ul>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">학교 학교 학교돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">돌봄선생님돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray  text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card aco_list border-0" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                            <div class="card-header border-bottom bg_f8faff" id="headingSeven">
                                <div class="d-flex justify-content-between align-items-center pl_16 pr_20">
                                    <p class="fs_16 fw_700 d-flex align-items-center text_dynamic"><span class="mr_04"><img src="./img/ico_tel.png" width="20px" alt="전화"/></span>수학학원</p>
                                    <button class="btn btn-link position-relative h_fit_im aco_btn"></button>
                                </div>
                            </div>
                            <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordion">
                                <div class="card-body m-0">
                                    <ul>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">학교 학교 학교돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                        <li class="border-bottom">
                                            <a href="tel:010-222-333" class="d-flex align-items-center justify-content-between pl_40 pr_30 py_18">
                                                <p class="fs_15 fw_600 text_gray text_dynamic mr-2">돌봄선생님돌봄선생님</p>
                                                <u class="fs_13 fw_400 text_gray  text-right flex-shrink-0">010-2222-3333</u>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    include_once("./inc/b_menu.php");
    include_once("./inc/tail.php");
?>


