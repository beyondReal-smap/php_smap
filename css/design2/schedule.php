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
					<button type="button" class="btn h-auto px-1 pl-4 mr-3"><i class="xi-angle-left-min"></i></button>
                    <div class="sel_month d-inline-flex">
                        <img class="mr-2" src="./img/sel_month.png" alt="월 선택 아이콘" style="width:1.6rem; ">
                        <p class="fs_15 fw_600">2023년 09월</p>
                    </div>
					<button type="button" class="btn h-auto px-1 pr-4"><i class="xi-angle-right-min"></i></button>
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

				<!-- -->
                <div class="grp_list">
					<ul>
						<li id="mbr_wr" class="schdl_list rounded_14 bg-white border">
							<ul>
								<li id="mbr_hd01" class="mbr_hd">
									<div class="d-flex justify-content-between">
										<div class="d-flex align-items-center flex-fill">
											<a href="#" class="d-flex align-items-center flex-fill">
												<div class="prd_img flex-shrink-0 mr_12">
													<div class="rect_square rounded_14">
														<img src="./img/sample01.png" alt="이미지"/>
													</div>
												</div>
												<p class="fs_14 fw_500 text_dynamic mr-2">나</p>
											</a>
										</div>
										<div class="d-flex align-items-center flex-shrink-0">
											<a href="schedule_form.php" class="fs_13 fc_navy"><i class="xi-plus-min"></i>일정 추가하기</a>
											<button type="button" class="btn btn-link ml-3" data-toggle="collapse" data-target="#mbr01" aria-expanded="false" aria-controls="mbr01"><img class="open_ic" src="./img/ic_open.png" style="width:1.0rem;"></button>
										</div>
									</div>
									<div id="mbr01" class="collapse " aria-labelledby="mbr01" aria-labelledby="mbr_hd01" data-parent="#mbr_wr">
										내용1
									</div>
								</li>
							</ul>
						</li>

						
						<li id="mbr_wr" class="schdl_list rounded_14 bg-white border">
							<div class="grp_tit"><p class="fs_17 fw_700">아가들</p></div>
							<ul>
								<li id="mbr_hd02" class="mbr_hd">
									<div class="d-flex justify-content-between">
										<div class="d-flex align-items-center flex-fill">
											<a href="#" class="d-flex align-items-center flex-fill">
												<div class="prd_img flex-shrink-0 mr_12">
													<div class="rect_square rounded_14">
														<img src="./img/sample01.png" alt="이미지"/>
													</div>
												</div>
												<p class="fs_14 fw_500 text_dynamic mr-2">나</p>
											</a>
										</div>
										<div class="d-flex align-items-center flex-shrink-0">
											<a href="schedule_form.php" class="fs_13 fc_navy"><i class="xi-plus-min"></i>일정 추가하기</a>
											<button type="button" class="btn btn-link ml-3" data-toggle="collapse" data-target="#mbr01" aria-expanded="false" aria-controls="mbr01"><img class="open_ic" src="./img/ic_open.png" style="width:1.0rem;"></button>
										</div>
									</div>
									<div id="mbr02" class="collapse " aria-labelledby="mbr02" aria-labelledby="mbr_hd02" data-parent="#mbr_wr">
										내용1
									</div>
								</li>
							</ul>
						</li>
					</ul>
                </div>
				<!--  -->


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

