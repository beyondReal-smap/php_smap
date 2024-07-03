<?php
$title = "일정 입력";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>
<?php include_once("./inc/modal.php");?>


<div class="container sub_pg">
    <div class="mt_22">
        <form action="">
			<div class="ip_wr">
				<input type="text" class="form-custom" placeholder="일정 내용을 입력해주세요.">
				<p class="fc_gray_500 fs_12 text-right mt-2">(0/15)</p>
			</div>

			<div class="line_ip_box border rounded-lg px_20 py_20 mt_20">
               <div class="line_ip">
			   		<div class="row justify-content-between">
						<h5 class="col col-auto">하루 종일</h5>
						<div class="col">
							<div class="custom-switch ml-auto">
								<input type="checkbox" class="custom-control-input" id="search_switch">
								<label class="custom-control-label" for="search_switch"></label>
							</div>
						</div>
					</div>
				</div>
               <div class="line_ip mt_25">
			   		<div class="row">
						<div class="col col-auto line_tit"><h5>시작</h5></div>
						<div class="col">
							<input type="text" readonly class="form-none cursor_pointer" placeholder="0000/00/00 00:00" value="" data-toggle="modal" data-target="#schedule_date_time">
							<!-- value 안에 데이터 넣어 주세요 -->
						</div>
					</div>
				</div>
               <div class="line_ip mt_25">
			   		<div class="row">
						<div class="col col-auto line_tit"><h5>종료</h5></div>
						<div class="col">
							<input type="text" readonly class="form-none cursor_pointer" placeholder="0000/00/00 00:00" value="" data-toggle="modal" data-target="#schedule_date_time">
							<!-- value 안에 데이터 넣어 주세요 -->
						</div>
					</div>
				</div>
               <div class="line_ip mt_25">
			   		<div class="row">
						<div class="col col-auto line_tit"><img src="./img/ip_ic_repeat.png" alt="반복 아이콘"></div>
						<div class="col">
							<input type="text" readonly class="form-none cursor_pointer" placeholder="반복" value="" data-toggle="modal" data-target="#schedule_repeat">
							<!-- value 안에 데이터 넣어 주세요 -->
						</div>
					</div>
				</div>
            </div>
			<div class="line_ip mt_25">
				<div class="row">
					<div class="col col-auto line_tit"><img src="./img/ip_ic_member.png" alt="멤버 아이콘"></div>
					<div class="col">
						<input type="text" readonly class="form-none cursor_pointer" placeholder="멤버 선택" value="" data-toggle="modal" data-target="#schedule_member">
						<!-- value 안에 데이터 넣어 주세요 -->
					</div>
				</div>
			</div>
			<div class="line_ip mt_25">
				<div class="row">
					<div class="col col-auto line_tit"><img src="./img/ip_ic_notice.png" alt="알림 아이콘"></div>
					<div class="col">
						<input type="text" readonly class="form-none cursor_pointer" placeholder="알림 선택" value="" data-toggle="modal" data-target="#schedule_notice">
						<!-- value 안에 데이터 넣어 주세요 -->
					</div>
				</div>
			</div>
			<div class="line_ip mt_25">
				<div class="row">
					<div class="col col-auto line_tit"><img src="./img/ip_ic_location.png" alt="위치 아이콘"></div>
					<div class="col">
						<div class="d-flex align-items-center">
							<!-- <span class="text-primary mr_12">KT&G</span> --><!-- 별칭 출력 -->
							<input type="text" readonly class="form-none cursor_pointer flex-fill" placeholder="위치 선택" value="" data-toggle="modal" data-target="#schedule_location">
						</div>
						<!-- value 안에 데이터 넣어 주세요 -->
					</div>
				</div>
			</div>
			<div class="line_ip mt_25">
				<div class="row">
					<div class="col col-auto line_tit"><img src="./img/ip_ic_material.png" alt="준비물 아이콘"></div>
					<div class="col"><input type="text" class="form-none" placeholder="준비물 입력" value=""></div>
					<!-- value 안에 데이터 넣어 주세요 -->
				</div>
			</div>
			<p class="fc_gray_500 fs_12 text-right mt-2">(0/100)</p>
			<div class="line_ip mt_25">
				<div class="row">
					<div class="col col-auto line_tit"><img src="./img/ip_ic_memo.png" alt="메모 아이콘"></div>
					<div class="col">
						<textarea class="form-none line_h1_4" placeholder="메모 입력"></textarea>
					</div>
				</div>
			</div>
			<p class="fc_gray_500 fs_12 text-right mt-2">(0/500)</p>
			<div class="line_ip mt_25">
				<div class="row">
					<div class="col col-auto line_tit"><img src="./img/ip_ic_contact.png" alt="연락처 아이콘"></div>
					<div class="col">
						<input type="text" readonly class="form-none cursor_pointer" placeholder="연락처 입력" value="" data-toggle="modal" data-target="#schedule_contact">
						<!-- 연락처미입력시 ↑-->


						<div class="contact_group fs_15 fc_gray_800 fw_600">
							<ul>
								<li data-toggle="modal" data-target="#contact_modify">
									<div class="text-primary mb-3">수학학원</div>
									<ul class="contact_list">
										<li class="d-flex justify-content-between">
											<div>기사아저씨</div>
											<div class="fc_gray_500">010-1234-5678</div>
										</li>
										<li class="d-flex justify-content-between">
											<div>기사아저씨</div>
											<div class="fc_gray_500">010-1234-5678</div>
										</li>
									</ul>
								</li>
								<li data-toggle="modal" data-target="#contact_modify">
									<div class="text-primary mb-3">영어학원</div>
									<ul class="contact_list">
										<li class="d-flex justify-content-between">
											<div>기사아저씨</div>
											<div class="fc_gray_500">010-1234-5678</div>
										</li>
										<li class="d-flex justify-content-between">
											<div>기사아저씨</div>
											<div class="fc_gray_500">010-1234-5678</div>
										</li>
									</ul>
								</li>
							</ul>
							<button type="button" class="border bg-white px_12 py-4 rounded_16 align-items-center d-flex flex-column justify-content-center w-100"  data-toggle="modal" data-target="#schedule_contact">
								<img class="d-block" src="./img/ico_add.png" style="width:2.0rem;">
								<span class="fc_gray_500 fw_700 mt-3">새로운 카테고리추가</span>
							</button>
						</div>
						<!-- 연락처입력시 ↑-->
					</div>
				</div>
			</div>

			<div class="b_botton">
                <button type="button" class="btn rounded btn-primary btn-lg btn-block"  id="ToastBtn">입력한 일정 저장하기</button>
            </div>
        </form>
    </div>
</div>

<!-- 토스트 Toast -->
<div id="Toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-body">
        <p><i class="xi-check-circle mr-2"></i>일정이 등록되었습니다</p>
        <!-- <p><i class="xi-error mr-2"></i>에러메시지</p> -->
    </div>
</div>