<?php
$title = "위치 선택";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>


<div class="container sub_pg px-0 py-0 map_wrap">
    <div class="mt_22 map_wrap_re">
        <form action="">
			<div class="pin_cont bg-white pt_20 px_16 pb_16 rounded_10">
				<ul>
					<li class="d-flex">
						<div class="name flex-fill">
							<span class="fs_12 fw_600 text-primary">선택한 위치</span>
							<div class="fs_14 fw_600 fc_gray_600 text_dynamic mt-1 line_h1_3">위치를 선택해주세요</div>
						</div>
					</li>
					<li class="d-flex mt-3">
						<div class="name d-flex flex-fill flex-column">
							<labe class="fs_12 fw_600 text-primary">별칭</labe>
							<input class="fs_14 fw_600 fc_gray_600 form-control text_dynamic mt-1 line_h1_3 loc_nickname"  placeholder="별칭을 입력해주세요">
						</div>
					</li>
				</ul>
				<!-- F-4 일정 입력 > 위치 선책 전 - 2 -->

				
				<!-- <ul>
					<li class="d-flex">
						<div class="name flex-fill">
							<span class="fs_12 fw_600 text-primary">선택한 위치</span>
							<div class="fs_14 fw_600 text_dynamic mt-1 line_h1_3">서울 영등포구 여의대로56</div>
						</div>
						<button type="button" class="mark_btn on"></button>
					</li>
					<li class="d-flex mt-3">
						<div class="name flex-fill">
							<span class="fs_12 fw_600 text-primary">별칭</span>
							<div class="fs_14 fw_600 text_dynamic mt-1 line_h1_3">KT&G</div>
						</div>
					</li>
				</ul> -->
				<!-- F-4 일정 입력 > 위치 선책 후 - 2 -->
			</div>
			<div class="map_ab">
				<div class="point point2">
					<img src="./img/pin_marker.png" width="39px" alt="이미지">
				</div>
			</div>

			<div class="b_botton bg_blur_0">
                <button type="button" class="btn rounded btn-primary btn-lg btn-block" onclick="location.href='schedule_form.php'">위치 선택완료</button>
				<!-- 위치 미선택 시 비활성화 disabled -->
            </div>
        </form>
    </div>
</div>
