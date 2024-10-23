<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '';
$h_menu = '2';
$_SUB_HEAD_TITLE = "위치 선택";
include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";
?>
<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?=NCPCLIENTID?>&submodules=geocoder"></script>
<div class="container sub_pg px-0 py-0 map_wrap">
    <div class="mt_22 map_wrap_re" id="naver_map" style="height: 100vh">
        <div class="map_wrap_re border">
            <div class="pin_cont bg-white pt_20 px_16 pb_16 rounded_10 ml-2 mr-2">
                <ul>
                    <li class="d-flex">
                        <div class="name flex-fill">
                            <span class="fs_12 fw_600 text-primary">선택한 위치</span>
                            <div class="fs_14 fw_600 text_dynamic mt-1 line_h1_3" id="location_add"></div>
                        </div>
                        <button type="button" class="mark_btn" id="btn_location_like" onclick="f_location_like();"></button>
                    </li>
                    <li class="d-flex mt-3">
                        <div class="name flex-fill">
                            <span class="fs_12 fw_600 text-primary">별칭</span>
                            <input class="fs_14 fw_600 fc_gray_600 form-control text_dynamic mt-1 line_h1_3 loc_nickname" name="slt_title" id="slt_title" value="" placeholder="별칭을 입력해주세요">
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="b_botton bg_blur_0">
            <button type="button" class="btn rounded btn-primary btn-lg btn-block" onclick="location.href='schedule_form.php'">위치 선택완료</button>
            <!-- 위치 미선택 시 비활성화 disabled -->
        </div>
    </div>
</div>

<script>
function f_location_like_delete(i) {
    $.alert({
        title: '',
        type: "blue",
        typeAnimated: true,
        content: '즐겨찾는 위치를 삭제하시겠습니까?',
        buttons: {
            confirm: {
                btnClass: "btn-default btn-lg btn-block",
                text: "<?=$translations['txt_confirm']?>", // 확인
                action: function() {
                    var form_data = new FormData();
                    form_data.append("act", "map_location_like_delete");
                    form_data.append("slt_idx", i);

                    $.ajax({
                        url: "./schedule_update",
                        enctype: "multipart/form-data",
                        data: form_data,
                        type: "POST",
                        async: true,
                        contentType: false,
                        processData: false,
                        cache: true,
                        timeout: 5000,
                        success: function(data) {
                            if (data == 'Y') {
                                f_location_like_list();
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });
                },
            },
        },
    });

    return false;
}


function f_location_like() {
    if ($('#slt_title').val() == '') {
        jalert('별칭을 입력바랍니다.');
        return false;
    }

    $.alert({
        title: '',
        type: "blue",
        typeAnimated: true,
        content: '위치를 등록하시겠습니까?',
        buttons: {
            confirm: {
                btnClass: "btn-default btn-lg btn-block",
                text: "<?=$translations['txt_confirm']?>", // 확인
                action: function() {
                    var form_data = new FormData();
                    form_data.append("act", "map_location_input");
                    form_data.append("slt_title", $('#slt_title').val());
                    form_data.append("slt_add", $('#sst_location_add').val());
                    form_data.append("slt_lat", $('#sst_location_lat').val());
                    form_data.append("slt_long", $('#sst_location_long').val());

                    $.ajax({
                        url: "./schedule_update",
                        enctype: "multipart/form-data",
                        data: form_data,
                        type: "POST",
                        async: true,
                        contentType: false,
                        processData: false,
                        cache: true,
                        timeout: 5000,
                        success: function(data) {
                            if (data) {
                                jalert('등록되었습니다.');
                                $('#btn_location_like').addClass('on');
                                $('#slt_title').attr('readonly', true);
                                $('#slt_idx').val(data);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });
                },
            },
        },
    });
}

(function($) {
    'use strict';
    $(function() {
        var st_lat;
        var st_lng;

        st_lat = '<?php echo $_SESSION['_mt_lat'] == "" ? 37.5665 : $_SESSION['_mt_lat']; ?>';
        st_lng = '<?php echo $_SESSION['_mt_long'] == "" ? 126.9780 : $_SESSION['_mt_long']; ?>';

        var map = new naver.maps.Map("naver_map", {
            center: new naver.maps.LatLng(st_lat, st_lng),
            zoom: 19,
            mapTypeControl: false
        });

        var marker = new naver.maps.Marker({
            position: new naver.maps.LatLng(st_lat, st_lng),
            map: map
        });

        map.setCursor('pointer');

        naver.maps.Event.addListener(map, 'click', function(e) {
            searchCoordinateToAddress(e.coord);
        });

        function initGeocoder() {
            map.addListener("touchstart", function(e) {
                searchCoordinateToAddress(e.coord);
            });

            return false;
        }

        function searchCoordinateToAddress(latlng) {
            naver.maps.Service.reverseGeocode({
                    coords: latlng,
                    orders: [naver.maps.Service.OrderType.ADDR, naver.maps.Service.OrderType.ROAD_ADDR].join(","),
                },
                function(status, response) {
                    if (status === naver.maps.Service.Status.ERROR) {
                        return alert("Something Wrong!");
                    }

                    var items = response.v2.results,
                        address = "",
                        htmlAddresses = [];

                    for (var i = 0, ii = items.length, item, addrType; i < ii; i++) {
                        item = items[i];
                        address = makeAddress(item) || "";
                        if (item.name == "roadaddr") {
                            addrType = "[도로명 주소]";
                        } else {
                            addrType = "[지번 주소]";
                        }

                        htmlAddresses.push(i + 1 + ". " + addrType + " " + address);
                    }

                    if (latlng._lat && latlng._lng) {
                        htmlAddresses.push("[GPS] 위도:" + latlng._lat + ", 경도: " + latlng._lng);
                    }

                    $('#location_add').html(address);

                    $('#sst_location_add').val(address);
                    $('#sst_location_lat').val(latlng._lat);
                    $('#sst_location_long').val(latlng._lng);
                    $('#map_info_box').removeClass('d-none-temp');
                }
            );
        }

        function makeAddress(item) {
            if (!item) {
                return;
            }

            var name = item.name,
                region = item.region,
                land = item.land,
                isRoadAddress = name === "roadaddr";

            var sido = "",
                sigugun = "",
                dongmyun = "",
                ri = "",
                rest = "";

            if (hasArea(region.area1)) {
                sido = region.area1.name;
            }

            if (hasArea(region.area2)) {
                sigugun = region.area2.name;
            }

            if (hasArea(region.area3)) {
                dongmyun = region.area3.name;
            }

            if (hasArea(region.area4)) {
                ri = region.area4.name;
            }

            if (land) {
                if (hasData(land.number1)) {
                    if (hasData(land.type) && land.type === "2") {
                        rest += "산";
                    }

                    rest += land.number1;

                    if (hasData(land.number2)) {
                        rest += "-" + land.number2;
                    }
                }

                if (isRoadAddress === true) {
                    if (checkLastString(dongmyun, "면")) {
                        ri = land.name;
                    } else {
                        dongmyun = land.name;
                        ri = "";
                    }

                    if (hasAddition(land.addition0)) {
                        rest += " " + land.addition0.value;
                    }
                }
            }

            return [sido, sigugun, dongmyun, ri, rest].join(" ");
        }

        function hasArea(area) {
            return !!(area && area.name && area.name !== "");
        }

        function hasData(data) {
            return !!(data && data !== "");
        }

        function checkLastString(word, lastString) {
            return new RegExp(lastString + "$").test(word);
        }

        function hasAddition(addition) {
            return !!(addition && addition.value);
        }

        naver.maps.onJSContentLoaded = initGeocoder;
    });
})(jQuery);

$("#frm_schedule_map").validate({
    submitHandler: function() {
        var f = document.frm_schedule_map;

        if ($('#sst_location_add').val() == '') {
            jalert('위치를 선택해주세요.');
            return false;
        }

        $('#slt_idx_t').val($('#sst_location_add').val());
        $('#schedule_map').modal('hide');

        return false;
    },
    rules: {
        sst_location_add: {
            required: true,
        },
    },
    messages: {
        sst_location_add: {
            required: "위치를 선택해주세요.",
        },
    },
    errorPlacement: function(error, element) {
        $(element)
            .closest("form")
            .find("span[for='" + element.attr("id") + "']")
            .append(error);
    },
});
</script>
<?php
include $_SERVER['DOCUMENT_ROOT']."/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>