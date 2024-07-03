<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "";
$_GET['hd_num'] = '';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>



<div class="container sub_pg psn_wrap">
    <div class="mt-4">
        <div class="">
            <p class="fs_24 fw_700 wh_pre line_h1_3">SMAP 권한 안내</p>
            <p class="fs_12 fc_gray_600 mt-3 line_h1_2 text_dynamic line_h1_3">SMAP의 서비스를 이용하기위해
                아래 권한을 승인해주세요!
            </p>
        </div>
        <div class="mt_45 psn_textwrap">
            <ul class="">
                <li class="d-flex fs_13 py-4 d-flex">
                    <p class="psn_tit flex-shrink-0 fw_700 text_dynamic line_h1_3 mr-3">위치 접근권한</p>
                    <p class="text_gray text_dynamic line_h1_3">smap에서 등록하신 관심 장소의 위치 정보를 파악하며, 해당 장소에 도착하거나 떠날 때 알림을 보내드립니다.</p>
                </li>
                <li class="d-flex fs_13 py-4 d-flex">
                    <p class="psn_tit flex-shrink-0 fw_700 text_dynamic line_h1_3 mr-3">카메라 접근권한</p>
                    <p class="text_gray text_dynamic line_h1_3">프로필 사진 촬영 및 저장에 필요합니다.</p>
                </li>
                <li class="d-flex fs_13 py-4 d-flex">
                    <p class="psn_tit flex-shrink-0 fw_700 text_dynamic line_h1_3 mr-3">저장소 접근권한</p>
                    <p class="text_gray text_dynamic line_h1_3">프로필 사진 촬영 및 저장에 필요합니다.</p>
                </li>
                <li class="d-flex fs_13 py-4 d-flex">
                    <p class="psn_tit flex-shrink-0 fw_700 text_dynamic line_h1_3 mr-3">활동인식 접근권한</p>
                    <p class="text_gray text_dynamic line_h1_3">smap은 사용자의 이동 패턴을 파악하고, 이를 바탕으로 등록된 관심 장소에 도착하거나 떠날 때 맞춤형 알림을 보내드립니다.</p>
                </li>
                <li class="d-flex fs_13 py-4 d-flex">
                    <p class="psn_tit flex-shrink-0 fw_700 text_dynamic line_h1_3 mr-3">절전모드 해제</p>
                    <p class="text_gray text_dynamic line_h1_3">사용자의 스마트폰이 절전 모드로 전환되더라도, 앱이 정상적으로 동작하며 위치 정보를 추적하고 필요한 알림을 보낼 수 있게 해줍니다.</p>
                </li>
                <li class="d-flex fs_13 py-4 d-flex">
                    <p class="psn_tit flex-shrink-0 fw_700 text_dynamic line_h1_3 mr-3">절약모드시
                        데이터 사용 허용</p>
                    <p class="text_gray text_dynamic line_h1_3">데이터 절약 모드에서도 smap이 일정과 관심 장소 정보를 실시간으로 업데이트하고, 알림을 보낼 수 있도록 이 권한이 필요합니다.</p>
                </li>
                <li class="d-flex fs_13 py-4 d-flex">
                    <p class="psn_tit flex-shrink-0 fw_700 text_dynamic line_h1_3 mr-3">배터리 절전 제외
                        앱 등록</p>
                    <p class="text_gray text_dynamic line_h1_3">배터리 절약 모드에서도 smap이 사용자의 일정과 관심 장소를 정상적으로 관리하고 알림을 보내드릴 수 있게 도와드립니다.</p>
                </li>
            </ul>
        </div>
    </div>
    <div class="b_botton">
        <button type="button" class="btn w-100 rounded btn-primary btn-lg btn-block" onclick="location.href='./onbding'">확인했어요!</button>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>