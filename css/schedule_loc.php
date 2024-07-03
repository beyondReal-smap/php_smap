<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$h_menu = '';
$$location_page = '1';
$_SUB_HEAD_TITLE = "위치 선택";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>

<div class="container sub_pg  sch_loc_wrap">
    <form action="">
        <input type="hidden" id="sst_location_add" name="sst_location_add" value="">
        <input type="hidden" id="sst_location_lat" name="sst_location_lat" value="">
        <input type="hidden" id="sst_location_long" name="sst_location_long" value="">
        <input type="hidden" id="slt_title" name="slt_title" value="">
        <div class="fixed_top bg-white" style="top:-2rem">
            <div class="py_20 px_16">
                <div class="ip_wr ip_valid pt-2">
                    <div class="ip_tit">
                        <h5 class="">주소검색</h5>
                    </div>
                    <div class="loc_search_wrap">
                        <input type="search" class="form-control search_location" placeholder="주소를 검색해주세요." id="search_location" name="search_location">
                        <button type="button" class="btn w-auto h-auto p-2 loc_search_btn"><i class="xi-search fs_24"></i></button>
                    </div>
                    <!-- <div class="form-text ip_valid"><i class="xi-check-circle-o"></i> 확인되었습니다.</div>
                    <div class="form-text ip_invalid"><i class="xi-error-o"></i> 아이디를 다시 확인해주세요</div> -->
                </div>
            </div>
            <div class="bar"></div>
        </div>
        <div class="mt_70 pb_100">
            <!-- 검색 전 -->
            <div>
                <ul class="search_results">
                    <p class="fw_700 pt-4">검색Tip</p>
                    <p class="position-relative slash1 pl-3 mt-3">도로명 + <span class="fw_600">건물번호</span></p>
                    <p class="position-relative slash6 pl-3 fs_13 text_light_gray mt-2">(예:송파대로 570)</p>
                    <p class="position-relative slash1 pl-3 mt-3">동/읍/면/리 + <span class="fw_600">번지</span></p>
                    <p class="position-relative slash6 pl-3 fs_13 text_light_gray mt-2">(예:신청동 7-30)</p>
                    <p class="position-relative slash1 pl-3 mt-3">건물명, 아파트명</p>
                    <p class="position-relative slash6 pl-3 fs_13 text_light_gray mt-2">(예:반포자이아파트)</p>
                </ul>
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        // 페이지 로드 시 실행되는 함수
        // 페이지 로드 시 주소 검색 입력 요소에 포커스를 줌
        $('#search_location').on('keyup', function() {
            // 키 입력 시 실행되는 함수
            var keyword = $('#search_location').val();
            $.ajax({
                url: 'https://dapi.kakao.com/v2/local/search/keyword.json',
                type: 'GET',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('Authorization', 'KakaoAK bc7899314df5dc2bebcb2a7960ac89bf');
                },
                data: {
                    query: keyword
                },
                success: function(response) {
                    $('.search_results').empty(); // 이전 검색 결과를 지움
                    if (response.documents.length > 0) {
                        // 검색 결과가 있을 경우
                        $.each(response.documents, function(index, place) {
                            var html = '<li class="d-flex align-items-center justify-content-between border-bottom py-4">';
                            html += '<p class="fs_16 fw_600 text_dynamic line_h1_2 mr-3">' + place.place_name;
                            html += '<br><span class="fs_14 fw_500 text_gray text_dynamic line_h1_2 mr-3">' + place.road_address_name + '</span></p>';
                            html += '<button type="button" class="btn btn-outline-secondary schloc_ch_btn border rounded-sm text_gray" onclick="f_location_select(\'' + place.road_address_name + '\',\'' + place.y + '\',\'' + place.x + '\',\'' + place.place_name + '\')">선택</button>';
                            html += '</li>';
                            $('.search_results').append(html);
                        });
                    } else {
                        // 검색 결과가 없을 경우
                        $('.search_results').html('<div class="pt_60 text-center"><img src="./img/warring.png" width="82px" alt="자료없음"><p class="mt_20 fc_gray_500 text-center line_h1_4">검색하신 주소를 찾을 수 없습니다.</p></div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('API 요청 실패:', status, error);
                    $('.search_results').html('<div class="pt_60 text-center"><img src="./img/warring.png" width="82px" alt="자료없음"><p class="mt_20 fc_gray_500 text-center line_h1_4">검색하신 주소를 찾을 수 없습니다.</p></div>');
                }
            });
        });
        setTimeout(function() {
            if (isAndroid()) {
                var inputElement = document.getElementById('search_location');
                inputElement.focus(); // 입력 요소에 포커스를 줌
                inputElement.setSelectionRange(0, inputElement.value.length); // 텍스트를 선택
            } else if (isiOS()) {
                var inputElement = document.getElementById('search_location');
                inputElement.focus(); // 입력 요소에 포커스를 줌
                inputElement.setSelectionRange(0, inputElement.value.length); // 텍스트를 선택
                /* 
                var message = {
                    "type": "keyboard"
                };
                window.webkit.messageHandlers.smapIos.postMessage(message);

                
                var inputElement = document.getElementById('search_location');
                inputElement.focus(); // 입력 요소에 포커스를 줌
                inputElement.setSelectionRange(0, inputElement.value.length); // 텍스트를 선택 
                */
            }
        }, 500);
    });

    function f_location_select(add, lat, long, place) {
        // 장소 선택 시 실행되는 함수
        document.getElementById('slt_title').value = place; // 장소명
        document.getElementById('sst_location_add').value = add; // 주소
        document.getElementById('sst_location_lat').value = lat; // 위도
        document.getElementById('sst_location_long').value = long; // 경도

        var slt_title = $('#slt_title').val();
        var sst_location_add = $('#sst_location_add').val();
        var sst_location_lat = $('#sst_location_lat').val();
        var sst_location_long = $('#sst_location_long').val();
        // 부모 페이지의 함수 호출하여 값을 전달
        window.parent.onlocationSearchComplete({
            slt_title: slt_title,
            sst_location_add: sst_location_add,
            sst_location_lat: sst_location_lat,
            sst_location_long: sst_location_long
        });
    }
</script>