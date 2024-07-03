<?php
$title = "";
$_GET['hd_num'] = '2';
include_once("./inc/head.php");
?>


<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3">약관에 동의해주세요.</p>
        <form action="" class="">
            <div class="border-bottom mt-5">
                <div class="ip_wr pb-4">
                    <div class="checks_wr">
                        <div class="checks">
                            <label>
                                <input type="checkbox" name="chk1">
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p mt-0">
                                    <p class="fs_14 fw_700">전체 약관에 동의합니다.</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between pt_07">
                <div class="ip_wr py_07">
                    <div class="checks_wr">
                        <div class="checks">
                            <label>
                                <input type="checkbox" name="chk1">
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p mt-0">
                                    <p class="text_dynamic fs_14 text-text line_h1_3"><span class="text_gray">(필수)</span>서비스 이용약관</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <a href="terms1.php" target="_blank" class="py-3 pl-3 pr-2 cursor_pointer flex-shrink-0" >
                    <img src="./img/ico_min_arrow_r.png" width="5px" alt="서비스 이용약관"/>
                </a>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="ip_wr py_07">
                    <div class="checks_wr">
                        <div class="checks">
                            <label>
                                <input type="checkbox" name="chk1">
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p mt-0">
                                    <p class="text_dynamic fs_14 text-text line_h1_3"><span class="text_gray">(필수)</span>개인정보 처리방침</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <a href="terms1.php" target="_blank" class="py-3 pl-3 pr-2 cursor_pointer flex-shrink-0">
                    <img src="./img/ico_min_arrow_r.png" width="5px" alt="개인정보 처리방침"/>
                </a>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="ip_wr py_07">
                    <div class="checks_wr">
                        <div class="checks">
                            <label>
                                <input type="checkbox" name="chk1">
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p mt-0">
                                    <p class="text_dynamic fs_14 text-text line_h1_3"><span class="text_gray">(필수)</span>위치기반서비스 이용약관</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <a href="terms1.php" target="_blank" class="py-3 pl-3 pr-2 cursor_pointer flex-shrink-0">
                    <img src="./img/ico_min_arrow_r.png" width="5px" alt="위치기반서비스 이용약관"/>
                </a>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="ip_wr py_07">
                    <div class="checks_wr">
                        <div class="checks">
                            <label>
                                <input type="checkbox" name="chk1">
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p mt-0">
                                    <p class="text_dynamic fs_14 text-text line_h1_3"><span class="text_gray">(선택)</span>개인정보 제3자 제공 동의</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <a href="terms1.php" target="_blank" class="py-3 pl-3 pr-2 cursor_pointer flex-shrink-0">
                    <img src="./img/ico_min_arrow_r.png" width="5px" alt="개인정보 제3자 제공 동의"/>
                </a>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="ip_wr py_07">
                    <div class="checks_wr">
                        <div class="checks">
                            <label>
                                <input type="checkbox" name="chk1">
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p mt-0">
                                    <p class="text_dynamic fs_14 text-text line_h1_3"><span class="text_gray">(선택)</span>마케팅 정보 수집 및 이용 동의</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <a href="https://schedulemap.notion.site/schedulemap/7e35638d106f433f86fa95f88ba6efb1" target="_blank" class="py-3 pl-3 pr-2 cursor_pointer flex-shrink-0">
                    <img src="./img/ico_min_arrow_r.png" width="5px" alt="마케팅 정보 수집 및 이용 동의"/>
                </a>
            </div>
            <div class="b_botton">
                <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='form_email.php'">동의했어요!</button>
            </div>
        </form>
    </div>
</div>