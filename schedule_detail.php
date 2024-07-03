<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '';
$h_menu = '2';
$_SUB_HEAD_TITLE = "일정 입력";
include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";

if($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
}
?>
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
                                <input type="checkbox" class="custom-control-input" id="search_switch" checked>
                                <label class="custom-control-label" for="search_switch"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="line_ip mt_25">
                    <div class="row">
                        <div class="col col-auto line_tit">
                            <h5>시작</h5>
                        </div>
                        <div class="col">
                            <input type="readolny" class="form-none cursor_pointer" placeholder="0000/00/00 00:00" value="9월 1일 (금) 12:00" data-toggle="modal" data-target="#schedule_date_time">
                            <!-- value 안에 데이터 넣어 주세요 -->
                        </div>
                    </div>
                </div>
                <div class="line_ip mt_25">
                    <div class="row">
                        <div class="col col-auto line_tit">
                            <h5>종료</h5>
                        </div>
                        <div class="col">
                            <input type="readolny" class="form-none cursor_pointer" placeholder="0000/00/00 00:00" value="9월 2일 (토) 12:00" data-toggle="modal" data-target="#schedule_date_time">
                            <!-- value 안에 데이터 넣어 주세요 -->
                        </div>
                    </div>
                </div>
                <div class="line_ip mt_25">
                    <div class="row">
                        <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_repeat.png" alt="반복 아이콘"></div>
                        <div class="col">
                            <input type="readolny" class="form-none cursor_pointer" placeholder="반복" value="매주 월, 화, 수, 목, 금" data-toggle="modal" data-target="#schedule_repeat">
                            <!-- value 안에 데이터 넣어 주세요 -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_member.png" alt="멤버 아이콘"></div>
                    <div class="col">
                        <input type="readolny" class="form-none cursor_pointer" placeholder="멤버 선택" value="다은" data-toggle="modal" data-target="#schedule_member">
                        <!-- value 안에 데이터 넣어 주세요 -->
                    </div>
                </div>
            </div>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_notice.png" alt="알림 아이콘"></div>
                    <div class="col">
                        <input type="readolny" class="form-none cursor_pointer" placeholder="알림 선택" value="20분 전" data-toggle="modal" data-target="#schedule_notice">
                        <!-- value 안에 데이터 넣어 주세요 -->
                    </div>
                </div>
            </div>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_location.png" alt="위치 아이콘"></div>
                    <div class="col">
                        <div class="d-flex align-items-center">
                            <span class="text-primary mr_12">KT&G</span>
                            <input type="readolny" class="form-none cursor_pointer flex-fill" placeholder="위치 선택" value="서울 영등포구 선유로11" data-toggle="modal" data-target="#schedule_location">
                        </div>
                        <!-- value 안에 데이터 넣어 주세요 -->
                    </div>
                </div>
            </div>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_material.png" alt="준비물 아이콘"></div>
                    <div class="col"><input type="text" class="form-none" placeholder="준비물 입력" value="가위, 줄자, 색연필"></div>
                    <!-- value 안에 데이터 넣어 주세요 -->
                </div>
            </div>
            <p class="fc_gray_500 fs_12 text-right mt-2">(0/100)</p>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_memo.png" alt="메모 아이콘"></div>
                    <div class="col">
                        <textarea class="form-none line_h1_4" placeholder="메모 입력">"따른 있을 시작하다 기관을, 진행한데 제안서가 확고하다" 결과는 십이월의 하라 사회의, 종업원에 상태로부터 있은 장관의 없은지 있다. "느끼면 굳으면, 없는 구월이어서 시작할 비닐의 아니다" 취하는 차례와, 말하는 생각하며, 처리장만 따라서 20일 한은 떤다. "나부터 닿다 미달될 제목의 자식의 명칭의 한쪽에 만연하다"</textarea>
                        <!-- 높이 조절되는 스크립트 넣어 뒀는데 custom.js .line_ip textarea 이미 입력되어 있을대도 높이가 적용 되어졌으면 좋겠습니다. 스크립트 수정 해주세요 -->
                    </div>
                </div>
            </div>
            <p class="fc_gray_500 fs_12 text-right mt-2">(0/500)</p>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_contact.png" alt="연락처 아이콘"></div>
                    <div class="col">
                        <!-- <input type="readolny" class="form-none cursor_pointer" placeholder="연락처 입력" value="" data-toggle="modal" data-target="#schedule_contact"> -->
                        <!-- 연락처미입력시 ↑-->


                        <div class="contact_group fs_15 fc_gray_800 fw_600">
                            <ul>
                                <li class="cursor_pointer" data-toggle="modal" data-target="#contact_modify">
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
                                <li class="cursor_pointer" data-toggle="modal" data-target="#contact_modify">
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
                            <button class="border bg-white px_12 py-4 rounded_16 align-items-center d-flex flex-column justify-content-center w-100">
                                <img class="d-block" src="<?=CDN_HTTP?>/img/ico_add.png" style="width:2.0rem;">
                                <span class="fc_gray_500 fw_700 mt-3">새로운 카테고리추가</span>
                            </button>
                        </div>
                        <!-- 연락처입력시 ↑-->
                    </div>
                </div>
            </div>

            <div class="b_botton">
                <div class="form-row">
                    <div class="col-5"><button type="button" class="btn rounded btn-bg_gray btn-lg btn-block" data-toggle="modal" data-target="#schedule_delete">일정 삭제하기</button></div>
                    <div class="col-7"><button type="button" class="btn rounded btn-primary btn-lg btn-block">일정 수정하기</button></div>
                </div>
            </div>

        </form>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>