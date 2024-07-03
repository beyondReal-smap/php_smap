<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$h_menu = '';
$_SUB_HEAD_TITLE = "위치 선택";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>
<div class="container sub_pg sch_loc_wrap">
    <form action="">
        <input type="hidden" id="sst_location_add" name="sst_location_add" value="">
        <input type="hidden" id="sst_location_lat" name="sst_location_lat" value="">
        <input type="hidden" id="sst_location_long" name="sst_location_long" value="">
        <div class=" fixed_top bg-white" style="top:0.0rem">
            <div class="">
                <div id="daumsearch">
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=e7e1c921e506e190875b4c8f4321c5ac&libraries=services"></script>
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    var element_wrap = document.getElementById('daumsearch');
    element_wrap.innerHTML = '';
    new daum.Postcode({
        oncomplete: function(data) {
            // 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
            //console.log(data);

            // 각 주소의 노출 규칙에 따라 주소를 조합한다.
            // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var fullAddr = data.address; // 최종 주소 변수
            var extraAddr = ''; // 조합형 주소 변수

            // 기본 주소가 도로명 타입일때 조합한다.
            if (data.addressType === 'R') {
                //법정동명이 있을 경우 추가한다.
                if (data.bname !== '') {
                    extraAddr += data.bname;
                }
                // 건물명이 있을 경우 추가한다.
                if (data.buildingName !== '') {
                    extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
                fullAddr += (extraAddr !== '' ? ' (' + extraAddr + ')' : '');
            }
            document.getElementById('sst_location_add').value = fullAddr; // 주소
            // 추가: 위도와 경도 값을 구해 설정한다.
            var geocoder = new daum.maps.services.Geocoder();
            geocoder.addressSearch(fullAddr, function(result, status) {
                if (status === daum.maps.services.Status.OK) {
                    document.getElementById('sst_location_lat').value = result[0].y; // 위도
                    document.getElementById('sst_location_long').value = result[0].x; // 경도

                    var sst_location_add = $('#sst_location_add').val();
                    var sst_location_lat = $('#sst_location_lat').val();
                    var sst_location_long = $('#sst_location_long').val();
                    // 부모 페이지의 함수 호출하여 값을 전달
                    window.parent.onlocationSearchComplete({
                        sst_location_add: sst_location_add,
                        sst_location_lat: sst_location_lat,
                        sst_location_long: sst_location_long
                    });
                }
            });

        },
        // 우편번호 찾기 화면 크기가 조정되었을때 실행할 코드를 작성하는 부분. iframe을 넣은 element의 높이값을 조정한다.
        onresize: function(size) {
            element_wrap.style.height = size.height + 'px';
        },
        width: '100%',
        height: '500px'
    }).embed(element_wrap);
</script>