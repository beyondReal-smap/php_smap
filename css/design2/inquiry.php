<?php
$title = "1:1문의";
$b_menu = "";
$_GET['hd_num'] = '3';
include_once("./inc/head.php");
?>

<div class="container sub_pg">
    <div id="accordion" class="accordion_1">
        <div class="card aco_list collapsed" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
            <div class="card-header border-0" id="headingOne">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="">
                        <p class="fs_13 fw_500 text-primary">답변완료</p>
                        <p class="fs_15 fw_700 text_dynamic mt_08 line2_text line_h1_3">앱 이용에 대해서 문의할게 있습니다.</p>
                        <p class="text_light_gray fs_13 fw_300 mt_08">2023-01-01 12:34</p>
                    </div>
                    <button type="button" class="btn btn-link position-relative aco_btn"></button>
                </div>
            </div>
            <!-- 오픈할때 .collapse 클래스에 .show 추가-->
            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion" >
                <div class="card-body">
                    <div class="bg_f9f9f9 rounded_06 p-4">
                        <div class="border-bottom pb-3 mb-3">
                            <p class="fs_15 fw_600 text_dynamic text_gray line_h1_5">문의내용</p>
                            <p class="fs_15 fw_300 text_dynamic text_gray line_h1_5 mt-3">밝은 있는 위하여 천하를 칼이다. 군영과 못할 있는 눈이 교향악이다. 얼마나 품고 무엇을 곧 희망의 사랑의 같은 이상은 것이다. 그와 그들의 지혜는 따뜻한 청춘을 못할 인간에 영원히 보이는 사막이다. 오아이스도 두기 곳으로 것이다. 영락과 이 풀밭에 것이다. 인류의 거친 얼마나 쓸쓸하랴? 피는 않는 지혜는 긴지라 바이며, 약동하다. 것이 소금이라 찾아다녀도, 동력은 같으며, 끓는다. 일월과 청춘이 있는 황금시대를 것이다.</p>
                        </div>
                        <div>
                            <p class="text-primary fw_600 fs_14 fw_300">답변</p>
                            <p class="fs_15 fw_300 text_dynamic text_gray line_h1_5 mt-3">밝은 있는 위하여 천하를 칼이다. 군영과 못할 있는 눈이 교향악이다. 얼마나 품고 무엇을 곧 희망의 사랑의 같은 이상은 것이다. 그와 그들의 지혜는 따뜻한 청춘을 못할 인간에 영원히 보이는 사막이다. 오아이스도 두기 곳으로 것이다. 영락과 이 풀밭에 것이다. 인류의 거친 얼마나 쓸쓸하랴? 피는 않는 지혜는 긴지라 바이며, 약동하다. 것이 소금이라 찾아다녀도, 동력은 같으며, 끓는다. 일월과 청춘이 있는 황금시대를 것이다.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card aco_list" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
            <div class="card-header border-0" id="headingTwo">
                <div class="d-flex justify-content-between  align-items-center">
                    <div class="">
                        <p class="fs_13 fw_500 text_gray">답변대기</p>
                        <p class="fs_15 fw_700 text_dynamic mt_08 line2_text line_h1_3">앱 이용에 대해서 문의할게 있습니다.</p>
                        <p class="text_light_gray fs_13 fw_300 mt_08">2023-01-01 12:34</p>
                    </div>
                    <button type="button" class="btn btn-link collapsed position-relative aco_btn "></button>
                </div>
            </div>
            <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion" >
                <div class="card-body">
                    <div class="bg_f9f9f9 rounded_06 p-4">
                        <div class="border-bottom pb-3 mb-3">
                            <p class="fs_15 fw_600 text_dynamic text_gray line_h1_5">문의내용</p>
                            <p class="fs_15 fw_300 text_dynamic text_gray line_h1_5 mt-3">밝은 있는 위하여 천하를 칼이다. 군영과 못할 있는 눈이 교향악이다. 얼마나 품고 무엇을 곧 희망의 사랑의 같은 이상은 것이다. 그와 그들의 지혜는 따뜻한 청춘을 못할 인간에 영원히 보이는 사막이다. 오아이스도 두기 곳으로 것이다. 영락과 이 풀밭에 것이다. 인류의 거친 얼마나 쓸쓸하랴? 피는 않는 지혜는 긴지라 바이며, 약동하다. 것이 소금이라 찾아다녀도, 동력은 같으며, 끓는다. 일월과 청춘이 있는 황금시대를 것이다.</p>
                        </div>
                        <div>
                            <p class="text-primary fw_600 fs_14 fw_300">답변</p>
                            <div class="text-center py-4">
                                <img src="./img/warring.png" width="62px" alt="자료없음">
                                <p class="fs_15 fw_300 text_dynamic text_gray line_h1_5 mt-3">답변을 기다리고 있습니다.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>