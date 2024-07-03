<?php
$title = "위치 입력";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>
<?php include_once("./inc/modal.php");?>

<div class="container sub_pg">
    <div class="mt_22">
        <form action="">
			<div class="ip_wr">
				<input type="text" class="form-custom" placeholder="위치명을 입력해주세요.">
				<p class="fc_gray_500 fs_12 text-right mt-2">(0/20)</p>
			</div>
			<div class="line_ip mt_25">
				<div class="row">
					<div class="col col-auto line_tit"><img src="./img/ip_ic_member.png" alt="멤버 아이콘"></div>
					<div class="col">
                        <input type="text" readonly class="form-none cursor_pointer" placeholder="멤버 선택" value="다은" data-toggle="modal" data-target="#schedule_member">
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
            <div class="text-center">
                <button type="button" class="btn btn-secondary btn-sm fc_primary pl_14 pr_11 mt_25 mx-auto" onclick="location.href='schedule_form.php'">일정도 같이 입력할래요!<i class="xi-angle-right-min ml_19"></i></button>
            </div>
			<div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='location.php'" id="ToastBtn">위치입력 완료</button>
            </div>
        </form>
    </div>
</div>

<!-- 토스트 Toast 토스트 넣어두었습니다. 필요하시면 사용하심됩니다.! 사용할 버튼에 id="ToastBtn" 넣으면 사용가능! -->
<div id="Toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-body">
        <p><i class="xi-check-circle mr-2"></i>위치가 등록되었습니다.</p> <!-- 성공메시지 -->
        <!-- <p><i class="xi-error mr-2"></i>에러메시지</p> -->
    </div>
</div>