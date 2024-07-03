<?php
$title = "폼 스타일";
$_GET['hd_num'] = '1';
$b_menu = '';
include_once("./inc/head.php");
?>

<div class="container sub_pg">
    <div class="pt_40">
        <div class="position-relative">

        <div class="map_wrap_h_sm">
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
                    <div class="point point4">
                        <img src="./img/mark_departure.png" width="30px" alt="출발"/>
                    </div>
                    <div class="point point5">
                        <img src="./img/mark_arrival.png" width="30px" alt="도착"/></div>
                    </div>
            </div>

            <!-- 개인 루트 지도 들어가는 영역 -->
            <div class="map_indivi">
                <p class="map_num text-white mx-1 my-1">1</p>
                <p class="map_num text-white mx-1 my-1">2</p>
                <p class="map_num text-white mx-1 my-1">3</p>
            </div>

        <div class="mt-5">
            <p class="tit_h3">.loader_sm</p>
            <div class="d-flex align-items-center justify-content-center py-5">
                <p class="loader loader_sm mr-3"></p>
            </div>

            <p class="tit_h3">.loader_md</p>
            <div class="d-flex align-items-center justify-content-center py-5">
                <p class="loader loader_md mr-3"></p>
            </div>

        </div>
            <h1 id="guide_pg3" class="guide_pg mb-3 mt-5"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 결과없음</span></h1>
            <div class="">
                <div class="pt-5 text-center">
                    <img src="./img/warring.png" width="82px" alt="자료없음"/>
                    <p class="mt_20 fc_gray_900 text-center">검색한 키워드와 일치하는<br/>
                    검색결과가 없습니다.</p>
                </div>
            </div>
            

            <div class="py-4"></div>
            <h1 id="" class="guide_pg mb-3"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 백그라운드 컬러</span></h1>
            <div class="mem_wrap d-flex align-items-center">
                <div class="w_fit mr_12">
                    <a href="#">
                        <div class="prd_img mx-auto flex-shrink-0 arm">
                            <div class="rect_square rounded_14">
                                <img src="./img/sample01.png" alt="이미지"/>
                            </div>
                        </div>
                        <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic">최지우</p>
                    </a>
                </div>
                <div class="w_fit mr_12">
                    <a href="#">
                        <div class="prd_img mx-auto flex-shrink-0">
                            <div class="rect_square rounded_14">
                                <img src="./img/sample01.png" alt="이미지"/>
                            </div>
                        </div>
                        <p class="fs_12 fw_400 text-center mt-2 line_h1_2 text_dynamic">최지우</p>
                    </a>
                </div>
            </div>

            <div class="d-flex align-items-center">
                <div class="w_fit">
                    <a href="#" class="d-flex align-items-center">
                        <div class="prd_img flex-shrink-0 mr_12 mine">
                            <div class="rect_square rounded_14">
                                <img src="./img/sample01.png" alt="이미지"/>
                            </div>
                        </div>
                        <div>
                            <p class="fs_14 fw_500 text_dynamic mr-2">최지우</p>
                            <div class="d-flex align-items-center flex-wrap">
                                <p class="fs_12 fw_400 text_dynamic fc_green line_h1_2 mt-1 mr-2">이동중 ·</p>
                                <p class="fs_12 fw_400 text_dynamic fc_green line_h1_2 mt-1">3km/h</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="w_fit">
                    <a href="#" class="d-flex align-items-center">
                        <div class="prd_img flex-shrink-0 mr_12">
                            <div class="rect_square rounded_14">
                                <img src="./img/sample01.png" alt="이미지"/>
                            </div>
                        </div>
                        <div>
                            <p class="fs_14 fw_500 text_dynamic mr-2">최지우</p>
                            <div class="d-flex align-items-center flex-wrap">
                                <p class="fs_12 fw_400 text_dynamic text-primary line_h1_2 mt-1">오너</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="py_09">
                <a href="#" class="d-flex align-items-center">
                    <div class="prd_img flex-shrink-0 mr_16">
                        <div class="rect_square rounded-pill">
                            <img src="./img/sample01.png" alt="이미지"/>
                        </div>
                    </div>
                    <div class="d-flex align-items-center flex-wrap">
                        <p class="fs_14 text_dynamic line_h1_2 mr_08"><span class="fw_700">다연</span> 님이 피아노학원 일정 시작 20분 전 입니다.</p>
                        <p class="fs_14 text_light_gray line_h1_2">오전 08:37</p>
                    </div>
                </a>
            </div>

            <div class="border rounded-lg px_16 py_16 mt-4 none_box mb_25">
                <div class="text-center">
                    <p class="fs_14 text_gray text_dynamic">등록된 일정이 없습니다.</p>
                    <button type="button" class="btn btn-secondary btn-sm fc_primary pl_14 pr_11 mt_12 mx-auto">일정 등록하러 가기<i class="xi-angle-right-min ml_19"></i></button>
                </div>
            </div>

            <div class="border rounded-lg pl_20 pr_16 py_08 bg-white">
                <div class="py_08 list_routine">
                    <a href="form.php">
                        <div class="border-bottom pb_16 ml-3">
                            <div class="d-flex aling-items-center justify-content-between">
                                <p class="fs_13 fw_500 text_gray text_dynamic position-relative slash4 text-left">오전 8:40 ~ 오후 6:20</p>
                                <i class="xi-angle-right-min fs_15 text_gray"></i>
                            </div>
                            <div class="mt-3">
                                <div class="mr-2">
                                    <p class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line1_text line_h1_4 w_fit mb-2">집 집집집집 집집집 집 집집집집 집집집 집 집집집집 집집집 집 집집집집 집집집 집 집집집집 집집집</p>
                                </div>
                                <p class="text_dynamic fs_13 text_light_gray line2_text line_h1_4 mt-2 text-left">서울 영등포구 여의대로56</p>
                                <p class="text_dynamic fs_13 fw_600 fc_navy mt-3 line_h1_4 text-left">물건 발송 일정 확인, 프로젝트 회의 잡기, 프로젝트 물건 발송 일정 확인, 프로젝트 회의 잡기, 프로젝트</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="py_08 list_routine">
                    <a href="form.php">
                        <div class="border-bottom pb_16 ml-3">                        
                            <div class="d-flex aling-items-center justify-content-between">
                                <p class="fs_13 fw_500 text_gray text_dynamic position-relative slash4 text-left">오전 8:40 ~ 오후 6:20</p>
                                <i class="xi-angle-right-min fs_15 text_gray"></i>
                            </div>
                            <div class="mt-3">
                                <div class="mr-2">
                                    <p class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line1_text line_h1_4 w_fit mb-2">집</p>
                                </div>
                                <p class="text_dynamic fs_13 text_light_gray line2_text line_h1_4 mt-2 text-left">서울 영등포구 여의대로56</p>
                                <p class="text_dynamic fs_13 fw_600 fc_navy mt-3 line_h1_4 text-left">물건 발송 일정 확인, 프로젝트 회의 잡기, 프로젝트</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

			<div></div>
            
            
            <h1 id="" class="guide_pg mb-3 mt-5"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 백그라운드 컬러</span></h1>
            <ul class="sq_guide mb-5">
                <li class="border bg-primary text-white">primary</li>
                <li class="border bg-secondary text-white">secondary</li>
                <li class="border bg-success text-white">success</li>
                <li class="border bg-danger text-white">danger</li>
                <li class="border bg-warning text-dark">warning</li>
                <li class="border bg-info text-white">info</li>
                <li class="border bg-light text-dark">light</li>
                <li class="border bg-dark text-white">dark</li>
                <li class="border bg-white text-dark">white</li>
                <li class="border bg-transparent text-dark">transparent</li>
            </ul>

            <h1 id="" class="guide_pg mb-3"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 보더 컬러</span></h1>
            <ul class="sq_guide mb-5">
                <li class="border bg-white text-dark border-primary">primary</li>
                <li class="border bg-white text-dark border-secondary">secondary</li>
                <li class="border bg-white text-dark border-success">success</li>
                <li class="border bg-white text-dark border-danger">danger</li>
                <li class="border bg-white text-dark border-warning">warning</li>
                <li class="border bg-white text-dark border-info">info</li>
                <li class="border bg-dark text-white border-light">light</li>
                <li class="border bg-white text-dark border-dark">dark</li>
                <li class="border bg-dark text-white border-white">white</li>
            </ul>

            <h1 id="" class="guide_pg mb-3"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 폰트 컬러</span></h1>
            <div class="fs_20">
                <p class="mb-3 text-primary">text-primary</p>
                <p class="mb-3 text-secondary bg-white">text-secondary</p>
                <p class="mb-3 text-success">text-success</p>
                <p class="mb-3 text-danger">text-danger</p>
                <p class="mb-3 text-warning">text-warning</p>
                <p class="mb-3 text-info">text-info</p>
                <p class="mb-3 text-light bg-dark">text-light</p>
                <p class="mb-3 text-dark">text-dark</p>
                <p class="mb-3 text-body">text-body</p>
                <p class="mb-3 text-muted bg-white">text-muted</p>
                <p class="mb-3 text-white bg-dark">text-white</p>
                <p class="mb-3 text-black-50">text-black-50</p>
                <p class="mb-3 text-white-50 bg-dark">text-white-50</p>
            </div>

            <h1 id="" class="guide_pg mb-3"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 타이틀</span></h1>
            <h1 class="mb-3 mt-5 fs_16">▼ 타이틀 스타일1</h1>
            <p class="tit_h1">1. 타이틀용서체 22 px Bold / Pretendard 체</p>
            <h1 class="mb-3 mt-5 fs_16">▼ 타이틀 스타일2</h1>
            <p class="tit_h2">2. 타이틀용서체 20px Bold / Pretendard 체</p>
            <h1 class="mb-3 mt-5 fs_16">▼ 타이틀 스타일3</h1>
            <p class="tit_h3">3. 타이틀용서체 18px Bold / Pretendard 체</p>


            <div class="py-4"></div>

            <h1 id="" class="guide_pg mb-3"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ badge</span></h1>
            
            <p class="fs_13 fc_primary rounded_04 bg_secondary text-center px_06 py_03 mr-2 line_h1_2 w_fit">회사</p>
           

            <div class="py-4"></div>

            <h1 id="" class="guide_pg mb-3"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 요소</span></h1>
            <div class="d-flex align-items-start rounded-sm bg_main px_16 py-3">
                <i class="fs_14 fw_500 fc_gray_500 xi-info mr-2 mt_02"></i>
                <p class="fs_14 fw_500 fc_gray_500 text_dynamic">멘토링을 받고싶은 날짜를 선택해주세요.</p>
            </div>

            <div class="py-4"></div>

            <h1 id="" class="guide_pg mb-3"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 토스트</span></h1>
            <button type="button" class="btn btn-primary btn-sm" id="ToastBtn">토스트 생성버튼</button>

            <div class="py-4"></div>

            <!-- 토스트 Toast -->
            <div id="Toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
                <div class="toast-body">
                    <p><i class="xi-error mr-2"></i>아이디 or 비밀번호를 다시 확인해주세요!</p>
                </div>
            </div>

             <!-- 모달 modal -->
             <button type="button" class="btn btn-primary btn-md"  data-toggle="modal" data-target="#one_modal">모달 버튼 1개</button>
             <button type="button" class="btn btn-primary btn-md"  data-toggle="modal" data-target="#two_modal">모달 버튼 2개</button>

             <button type="button" class="btn btn-primary btn-md"  data-toggle="modal" data-target="#modal_default_ex">modal-default</button>
             <button type="button" class="btn btn-primary btn-md"  data-toggle="modal" data-target="#modal_full_ex">modal_full</button>

             
            <!-- 모달 버튼 1개  -->
            <div class="modal fade" id="one_modal" tabindex="-1">
                <div class="modal-dialog modal-sm modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">와이파이 혹은 네트워크에
                            연결되어 있지 않습니다.</p>
                            <p class="fs_14 fw_400 text_gray mt-3 text_dynamic text-center">앱 종료 후 다시 실행해 보세요.</p>
                        </div>
                        <div class="modal-footer px-0 py-0">
                            <button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0"  data-dismiss="modal" aria-label="Close">확인</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 모달 버튼 2개 -->
            <div class="modal fade" id="two_modal" tabindex="-1">
                <div class="modal-dialog modal-sm modal-dialog-centered"><!-- opt_bottom_wrap 이거 넣으면 바텀시트 / modal-dialog-scrollable 필요시-->
                    <div class="modal-content">
                        <!-- <div class="modal-header border-0 pt_20 px_20 align-items-center justify-content-between">
                            <div></div>
                            <div></div>
                            <button type="button" class="btn h-auto p-0 mb-n3 close" data-dismiss="modal" aria-label="Close"><i class="xi-close fc_gray_200 fs_23"></i></button>
                        </div> -->
                        <div class="modal-body pt_40 pb_27 px-3 ">
                            <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">업데이트 버전이 있습니다.</p>
                            <p class="fs_14 fw_400 text_gray mt-3 text_dynamic text-center">업데이트를 하여 앱을 최적화한 후
                            이용해주세요.</p>
                        </div>
                        <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                            <div class="d-flex align-items-center w-100 mx-0 my-0">
                                <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">취소</button>
                                <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="location.href='.php'">업데이트</button>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>





			<!-- modal-default 예시  -->
			<div class="modal fade" id="modal_default_ex" tabindex="-1">
				<div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header justify-content-between border-0 pt_20 pb_4">
							<div class="modal-title">타이틀</div>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="./img/modal_close.png"></button>
						</div>
						<div class="modal-body">
							<form>
								내용
							</form>
						</div>
						<div class="modal-footer px-0 py-0">
							<button type="button" class="btn btn-md btn-block btn-primary mx-0 my-0"  data-dismiss="modal" aria-label="Close">시간 저장하기</button>
						</div>
					</div>
				</div>
			</div>


			<!-- modal_full 예시  -->
			<div class="modal modal_full" id="modal_full_ex" tabindex="-1">
				<div class="modal-dialog modal-dialog-scrollable">
					<div class="modal-content">
						<div class="modal-header">
							<div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="./img/top_back_b.png" width="24px" alt="뒤로"></button></div>
							<p class="modal-title line1_text fs_16 fw_700">타이틀</p>
							<div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="./img/modal_close.png"></button></div>
						</div>
						<div class="modal-body scroll_bar_y">
							<form class="h-100">
							</form>
						</div>
						<div class="modal-footer border-0">
							<button type="button" class="btn btn-lg btn-block btn-primary mx-0 my-0" data-dismiss="modal" aria-label="Close">반복 주기 선택완료</button>
						</div>
					</div>
				</div>
			</div>



            <h1 id="guide_pg2" class="guide_pg mb-3"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 폰트</span></h1>
            <h1 class="mb-3 mt-3 fs_16">▼ 폰트 사이즈</h1>
            <div class="px-2">
                <div class="fs_8">fs_8</div>
                <div class="fs_9">fs_9</div>
                <div class="fs_10">fs_10</div>
                <div class="fs_11">fs_11</div>
                <div class="fs_17">~</div>
                <div class="fs_32">fs_52</div>
            </div>
            <h1 class="mb-3 mt-3 fs_16">▼ 폰트 굵기</h1>
            <div class="px-2 py-2">
                <div class="fw_100">fw_100 Thin</div>
                <div class="fw_200">fw_200 ExtraLight</div>
                <div class="fw_300">fw_300 Light</div>
                <div class="fw_400">fw_400 Regular</div>
                <div class="fw_500">fw_500 Medium</div>
                <div class="fw_600">fw_600 SemiBold</div>
                <div class="fw_700">fw_700 Bold</div>
                <div class="fw_800">fw_800 ExtraBold</div>
                <div class="fw_900">fw_900 Black</div>
            </div>

            <h1 id="guide_pg3" class="guide_pg mb-3 mt-5"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 버튼</span></h1>
            <h1 class="mb-0 mt-3 fs_16">▼ 버튼</h1>

            <div class="py-3">
                <button type="button" class="btn btn-secondary fs_12 rounded-pill h_28 fc_primary line_h1_1 px_12">인증버호가 안와요!<i class="xi-long-arrow-right ml-2"></i></button>
                <button type="button" class="btn btn-secondary btn-sm fc_primary pl_14 pr_11">그룹 생성 하러가기<i class="xi-angle-right-min ml_19"></i></button>
            </div>

            <div class="py-3">
                <button type="button" class="btn btn-primary btn-sm">회원가입</button>
                <button type="button" class="btn btn-primary btn-sm disabled">비활성화 disabled</button>
                <button type="button" class="btn btn-outline-primary btn-sm ">회원가입</button>
                <button type="button" class="btn btn-secondary btn-sm ">회원가입</button>
                <button type="button" class="btn btn-outline-secondary btn-sm ">회원가입</button>
            </div>

            <div class="py-3">
                <button type="button" class="btn btn-primary btn-md">회원가입</button>
                <button type="button" class="btn btn-outline-primary btn-md">회원가입</button>
                <button type="button" class="btn btn-secondary btn-md">회원가입</button>
                <button type="button" class="btn btn-outline-secondary btn-md">회원가입</button>
            </div>

            <div class="py-3">
                <button type="button" class="btn btn-primary">회원가입</button>
                <button type="button" class="btn btn-outline-primary">회원가입</button>
                <button type="button" class="btn btn-secondary">회원가입</button>
                <button type="button" class="btn btn-outline-secondary">회원가입</button>
            </div>

            <div class="py-3">
                <button type="button" class="btn btn-primary btn-lg btn-block">회원가입</button>
                <button type="button" class="btn btn-outline-primary btn-lg btn-block">회원가입</button>
                <button type="button" class="btn btn-secondary btn-lg btn-block">회원가입</button>
                <button type="button" class="btn btn-outline-secondary btn-lg btn-block">회원가입</button>
            </div>

            <div class="py-3">
                <button type="button" class="btn w-100 rounded-0 btn-primary btn-lg btn-block">회원가입</button>
                <button type="button" class="btn w-100 rounded-0 btn-outline-primary btn-lg btn-block">회원가입</button>
                <button type="button" class="btn w-100 rounded-0 btn-secondary btn-lg btn-block">회원가입</button>
                <button type="button" class="btn w-100 rounded-0 btn-outline-secondary btn-lg btn-block">회원가입</button>
            </div>

            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='form_email.php'">동의했어요!</button>
            </div>


            

            <h1 id="guide_pg3" class="guide_pg mb-3 mt-5"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 이미지등록</span></h1>
            <div class="ip_wr mt-5">
                <div class="ip_tit">
                    <h5>이미지 등록</h5>
                </div>
                <form action="">
                    <div class="upload_img_wrap">
                        <div class="form-group upload_img_item">
                            <label for="file_upload" class="file_upload square"><i class="xi-plus"></i></label>
                            <input type="file" class="form-control-file d-none" id="file_upload">
                        </div>
                        <div class="form-group upload_img_item">
                            <label for="file_upload" class="file_upload square d-none"><i class="xi-plus"></i></label>
                            <input type="file" class="form-control-file d-none" id="file_upload">
                            <div class="rect_square rounded-lg">
                                <img src="./img/no_image.png" alt="이미지">
                                <div class="dimmed"></div>
                                <button class="btn btn-link btn-sm btn_delete"><i class="xi-close text-white"></i></button>
                            </div>
                        </div>
                        <div class="form-group upload_img_item">
                            <label for="file_upload" class="file_upload square d-none"><i class="xi-plus"></i></label>
                            <input type="file" class="form-control-file d-none" id="file_upload">
                            <div class="rect_square rounded-lg">
                                <img src="./img/no_image.png" alt="이미지">
                                <div class="dimmed"></div>
                                <button class="btn btn-link btn-sm btn_delete"><i class="xi-close text-white"></i></button>
                            </div>
                        </div>
                        <div class="form-group upload_img_item">
                            <label for="file_upload" class="file_upload square d-none"><i class="xi-plus"></i></label>
                            <input type="file" class="form-control-file d-none" id="file_upload">
                            <div class="rect_square rounded-lg">
                                <img src="./img/no_image.png" alt="이미지">
                                <div class="dimmed"></div>
                                <button class="btn btn-link btn-sm btn_delete"><i class="xi-close text-white"></i></button>
                            </div>
                        </div>
                        <div class="form-group upload_img_item">
                            <label for="file_upload" class="file_upload square d-none"><i class="xi-plus"></i></label>
                            <input type="file" class="form-control-file d-none" id="file_upload">
                            <div class="rect_square rounded-lg">
                                <img src="./img/no_image.png" alt="이미지">
                                <div class="dimmed"></div>
                                <button class="btn btn-link btn-sm btn_delete"><i class="xi-close text-white"></i></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <h1 id="guide_pg3" class="guide_pg mb-3 mt-5"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 입력폼</span></h1>
            <h1 class="mb-3 mt-3 fs_16">▼ 기본</h1>
            <div class="">

                <form action="">
                    <div class="upload_img_wrap profile_upolad">
                        <div class="form-group upload_img_item profile_add_btn">
                            <label for="file_upload" class="file_upload fs_12 fw_700 square border"><i class="xi-camera"></i></label>
                            <input type="file" class="form-control-file d-none" id="file_upload">
                        </div>       
                        <div class="form-group upload_img_item profile_view_img">
                            <label for="file_upload" class="file_upload square d-none"><i class="xi-plus"></i></label>
                            <input type="file" class="form-control-file d-none" id="file_upload">
                            <div class="rect_square">
                                <!-- 이미지 없을 때 -->
                                <img src="./img/no_profile.png" alt="프로필이미지">
                                <div class="dimmed"></div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="ip_wr mt-5">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5 class="">이름</h5>
                        <p class="fs_12 fc_gray_600">(0/30)</p>
                    </div>
                    <input type="text" class="form-control" placeholder="이름을 입력해주세요">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                </div>

                <div class="ip_wr mt_25">
                    <div class="ip_tit">
                        <h5 class="">핸드폰번호</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="핸드폰번호를 입력해주세요.">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                </div>

                <div class="ip_wr mt-5 ip_valid">
                    <div class="ip_tit">
                        <h5 class="essential">아이디</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="입력해주세요.">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                </div>

                <div class="ip_wr mt-5 ip_valid">
                    <div class="ip_tit">
                        <h5>비밀번호</h5>
                    </div>
                    <input type="password" class="form-control" placeholder="비밀번호를 입력해주세요.">
                    <div class="fs_12 fc_gray_600 mt-3 px-4 line_h1_2">비밀번호는 최소 9글자 이상 공백 없이 문자, 숫자 조합입니다.</div>
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 비밀번호를 다시 확인해주세요</div>
                </div>

                <div class="ip_wr mt-5 ip_valid">
                    <div class="ip_tit">
                        <h5 class="">이메일</h5>
                    </div>
                    <input type="text" class="form-control" placeholder="test@test.com">
                    <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                    <button type="button" class="btn fs_12 fc_primary rounded-pill bg_secondary text-center px_12 py_07 text_dynamic w_fit h_fit_im d-flex align-items-center mt-3">인증번호가 안와요! <i class="xi-arrow-right ml-2"></i></button>
                </div>

                <div class="ip_wr mt-5 ip_valid">
                    <div class="ip_tit ">
                        <h5 class="">검색</h5>
                    </div>
                    <input type="search" class="form-control" placeholder="다양한 분야의 멘토를 만나보세요!">
                </div>
                

                <div class="form-row">
                    <div class="ip_wr mt-5 col-md-6">
                        <div class="ip_tit">
                            <h5>타이틀</h5>
                        </div>
                        <input type="text" class="form-control" placeholder="입력하세요">
                    </div>
                    <div class="ip_wr mt-5 col-md-6">
                        <div class="ip_tit">
                            <h5>타이틀</h5>
                        </div>
                        <input type="text" class="form-control" placeholder="0">
                    </div>
                </div>

                <div class="form-row">
                    <div class="ip_wr mt-5 col-6 col-md-3">
                        <div class="ip_tit">
                            <h5>타이틀</h5>
                        </div>
                        <input type="text" class="form-control" placeholder="입력하세요">
                    </div>
                    <div class="ip_wr mt-5 col-6 col-md-3">
                        <div class="ip_tit">
                            <h5>타이틀</h5>
                        </div>
                        <input type="text" class="form-control" placeholder="0">
                    </div>
                    <div class="ip_wr mt-5 col-md-6">
                        <div class="ip_tit">
                            <h5>타이틀</h5>
                        </div>
                        <input type="text" class="form-control" placeholder="0">
                    </div>
                </div>

                <div class="ip_wr mt-5">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5>후기를 작성해주세요</h5>
                    </div>
                    <textarea class="form-control" placeholder="입력해주세요" rows="5"></textarea>
                    <p class="fc_gray_600 fs_12 text-right">(0/1000)</p>
                    <div class="invalid-feedback">1000자까지만 써주세요</div>
                </div>

                <div class="ip_wr mt-5">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5>셀렉트박스</h5>
                    </div>
                    <select class="form-control custom-select">
                        <option selected>선택하기</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>

                <div class="ip_wr mt-5">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5>날짜 선택</h5>
                    </div>
                    <input type="text" id="datetimepicker" class="form-control">
                </div>

                <script>
                    $("#datetimepicker").datetimepicker();
                </script>
                
                <div class="ip_wr mt-5">
                    <div class="ip_tit">
                        <h5>휴대폰번호</h5>
                    </div>
                    <div class="form-row">
                        <div class="col-12">
                            <input type="text" class="form-control" placeholder="‘-’ 없이 숫자만 입력">
                        </div>
                        <div class="col-12 mt-3">
                            <button type="button" class="btn btn-primary btn-block disabled">인증요청</button>
                        </div>
                    </div>
                </div>
                <div class="ip_wr mt-5">
                    <div class="ip_tit">
                        <h5>인증번호 입력</h5>
                    </div>
                    <div class="form-row mt_06 ip_valid">
                        <div class="col-9">
                            <input type="text" class="form-control input_time_input" placeholder="인증번호를 입력해주세요">
                            <span class="fc_red fs_16 fw_300 bg_gray_100 input_time">02:59</span>
                        </div>
                        <div class="col-3">
                            <button type="button" class="btn btn-primary btn-lg rounded btn-block">인증받기</button>
                        </div>
                        <div class="col-12">
                            <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                            <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div>
                        </div>
                    </div>
                </div>
            </div>



            <h1 id="guide_pg3" class="guide_pg mb-3 mt-5"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 체크박스 / 라디오 버튼</span></h1>
            <h1 class="mb-3 mt-5 fs_16">▼ 체크박스 / 라디오 버튼</h1>

            <div class="ip_wr mt-5">
                <div class="ip_tit">
                    <h5>체크박스1</h5>
                </div>
                <div class="checks_wr">
                    <div class="checks">
                        <label>
                            <input type="checkbox" name="chk1">
                            <span class="ic_box"><i class="xi-check-min"></i></span>
                            <div class="chk_p ">
                                <p class="text_dynamic">체크박스체크박스체크박스</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="ip_wr mt-5">
                <div class="ip_tit">
                    <h5>체크박스2</h5>
                </div>
                <div class="checks_wr">
                    <div class="checks">
                        <label class="chk_right">
                            <input type="checkbox" name="chk2">
                            <span class="ic_box"><i class="xi-check-min"></i></span>
                            <div class="chk_p">
                                <p class="text_dynamic">체크박스</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="ip_wr mt-5">
                <div class="ip_tit">
                    <h5>라디오1</h5>
                </div>
                <div class="checks_wr">
                    <div class="checks">
                        <label>
                            <input type="radio" name="rd1">
                            <span class="ic_box"><i class="xi-check-min"></i></span>
                            <div class="chk_p">
                                <p class="text_dynamic">라디오1_1</p>
                            </div>
                        </label>
                    </div>
                    <div class="checks">
                        <label>
                            <input type="radio" name="rd1">
                            <span class="ic_box"><i class="xi-check-min"></i></span>
                            <div class="chk_p">
                                <p class="text_dynamic">라디오1_2</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="ip_wr mt-5">
                <div class="ip_tit">
                    <h5>라디오2</h5>
                </div>
                <div class="checks_wr">
                    <div class="checks">
                        <label class="chk_right">
                            <input type="radio" name="rd2">
                            <span class="ic_box"><i class="xi-check-min"></i></span>
                            <div class="chk_p">
                                <p class="text_dynamic">라디오1_1</p>
                            </div>
                        </label>
                    </div>
                    <div class="checks">
                        <label class="chk_right">
                            <input type="radio" name="rd2">
                            <span class="ic_box"><i class="xi-check-min"></i></span>
                            <div class="chk_p">
                                <p class="text_dynamic">라디오1_2</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <h1 class="guide_pg mb-3 mt-5"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 테이블 style(넓이 작은 테이블은 class table_scroll 빼기)</span></h1>
            <div class="table_scroll">
                <table class="table_01" summary=" ">
                    <caption>
                        수시 일정
                    </caption>
                    <colgroup>
                        <col width="15%">
                        <col width="25%">
                        <col width="30%">
                        <col width="35%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="backslash fs_13">
                                <div>제목1</div>제목2
                            </th>
                            <th>제목</th>
                            <th>제목</th>
                            <th class="slash fs_13">
                                제목1<div>제목2</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-left">왼쪽정렬</td>
                            <td class="text-right">오른쪽정렬</td>
                            <td>내용</td>
                            <td>내용</td>
                        </tr>
                        <tr>
                            <td>내용</td>
                            <td>내용</td>
                            <td>내용</td>
                            <td>내용</td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <h1 id="guide_pg3" class="guide_pg mb-3 mt-5"><span class="bg-primary d-block py-3 px-3 text-white fs_17">▼ 페이지 네이션</span></h1>
            <h1 class="mb-3 mt-5 fs_16">▼ 페이지 네이션</h1>
            <ul class="pagination">
                <li class="pgn_prev"><a href="#" class="disabled"><i class="xi-angle-left-min"></i></a></li>
                <li class=""><a href="#" class="on">1</a></li>
                <li class=""><a href="#">2</a></li>
                <li class=""><a href="#">3</a></li>
                <li class="pgn_next"><a href="#"><i class="xi-angle-right-min"></i></a></li>
            </ul>

            <h1 class="mb-3 mt-5 fs_16">▼ 페이저</h1>
            <article class="pager">
                <button class="btn p-0 d-flex align-items-center"><i class="xi-long-arrow-left fs_26"></i></button>
                <p class="fs_22 mx-5"><span class="text-primary">1</span> / <span>12</span></p>
                <button class="btn p-0 d-flex align-items-center"><i class="xi-long-arrow-right fs_26"></i></button>
            </article>
        </div>
    </div>
</div>

<? 
include_once("./inc/b_menu.php");
include_once("./inc/tail.php");
 ?>