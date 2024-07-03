<?php
$title = "문의 작성";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>


<div class="container sub_pg">
    <div class="mt-4">
        <form action="">
                <div class="ip_wr mt_25">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5 class="">문의 제목</h5>
                        <p class="text_num fs_12 fc_gray_600">(0/30)</p>
                    </div>
                    <input type="text" class="form-control" placeholder="문의 제목을 입력해주세요">
                </div>
                <div class="ip_wr mt_25">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5 class="">문의 제목</h5>
                        <p class="text_num fs_12 fc_gray_600">(0/200)</p>
                    </div>
                    <textarea class="form-control" placeholder="문의 내용을 입력해 주세요. 폭언 욕설 등의 문의글은 통보 없이 삭제 될 수 있습니다." rows="15"></textarea>
                </div>
            </div>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" id="ToastBtn">등록하기</button>
            </div>
        </form>
    </div>
</div>

<!-- 토스트 Toast -->
<div id="Toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-body">
        <p><i class="xi-check-circle mr-2"></i>문의가 등록되었습니다.</p>
        <!-- <p><i class="xi-error mr-2"></i>에러메시지</p> -->
    </div>
</div>