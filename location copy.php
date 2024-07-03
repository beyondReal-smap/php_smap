<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '4';
$h_menu = '5';
$_SUB_HEAD_TITLE = "위치";
include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";

if($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
}

if($_GET['sdate']=='') {
    $_GET['sdate'] = date('Y-m-d');
}

$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->orderBy("slmt_idx", "desc");
$row_slmt = $DB->getone('smap_location_member_t');

if($_GET['sgdt_mt_idx']) {
    $row_slmt['sgdt_mt_idx'] = $_GET['sgdt_mt_idx'];
}

$mt_location_info = get_member_location_log_t_info($row_slmt['sgdt_mt_idx']);
$mt_info = get_member_t_info($row_slmt['sgdt_mt_idx']);

$m_mt_lat = $mt_location_info['mlt_lat'];
$m_mt_long = $mt_location_info['mlt_long'];
$mt_file1_url = get_image_url($mt_info['mt_file1']);
?>
<style>
html {
    height: 100%;
}

.h_menu {
    background-color: #fff !important;
}

.sub_pg {
    height: calc(100% - 4.8rem) !important;
    min-height: calc(100% - 4.8rem) !important;
    overflow: hidden;
    padding-top: 4.8rem;
}
</style>
<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?=NCPCLIENTID?>"></script>
<div class="container sub_pg px-0 py-0 h-100">
    <div class="map_wrap_h_sm">
        <div class="map_wrap_h_div">
            <div class="map_ab" id="naver_map" style="height: 100vh">
            </div>
        </div>
    </div>
    <!-- G-2 위치 페이지 -->
    <div class="opt_bottom opt_bottom_2 on">
        <div class="top_bar_wrap text-center pt_08">
            <img src="<?=CDN_HTTP?>/img/top_bar.png" class="top_bar" width="34px" alt="탑바" />
            <img src="<?=CDN_HTTP?>/img/btn_tl_arrow.png" class="top_down mx-auto" width="12px" alt="탑업" />
        </div>
        <div class="scroll_bar_y pb_100 scroll_bar_none">
            <input type="hidden" name="sgdt_mt_idx" id="sgdt_mt_idx" value="<?=$row_slmt['sgdt_mt_idx']?>" />
            <input type="hidden" name="event_start_date" id="event_start_date" value="<?=$_GET['sdate']?>" />
            <input type="hidden" name="map_mt_lat" id="map_mt_lat" value="<?=$m_mt_lat?>" />
            <input type="hidden" name="map_mt_long" id="map_mt_long" value="<?=$m_mt_long?>" />
            <input type="hidden" name="map_mt_file1" id="map_mt_file1" value="<?=$mt_file1_url?>" />
            <input type="hidden" name="sst_idx" id="sst_idx" value="" />
            <div class="mem_wrap" id="location_member_box"></div>

            <div id="location_info_box"></div>

            <div class="bg_main px_20 py_20">
                <p class="fs_16 fw_600 pt-2 pb-3">추천장소</p>
                <div id="recom_location_list_box"></div>
            </div>
        </div>
    </div>
</div>

<button type="button" class="btn w-100 floating_btn rounded" onclick="location.href='./location_form'"><i class="xi-plus-min mr-3"></i> 위치 추가하기</button>

<script>
var map = null;

$(document).ready(function() {
    f_get_recom_list();
    f_get_member_location('<?=$row_slmt['sgdt_mt_idx']?>');
    f_get_info_location('<?=$row_slmt['sgdt_mt_idx']?>');

    var map = new naver.maps.Map('map', {
        mapTypeId: naver.maps.MapTypeId.HYBRID
    });
});

function f_get_recom_list() {
    var form_data = new FormData();
    form_data.append("act", "recom_list");

    $.ajax({
        url: "./location_update",
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
                $('#recom_location_list_box').html(data);
            }
        },
        error: function(err) {
            console.log(err);
        },
    });
}

function f_get_member_location(i) {
    var form_data = new FormData();
    form_data.append("act", "location_member");

    if (i) {
        form_data.append("mt_idx", i);
    }

    $.ajax({
        url: "./location_update",
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
                $('#location_member_box').html(data);
            }
        },
        error: function(err) {
            console.log(err);
        },
    });
}

function f_get_info_location(i, s = "") {
    var form_data = new FormData();
    form_data.append("act", "location_info");
    if (s) {
        form_data.append("event_start_date", s);
    } else {
        form_data.append("event_start_date", $('#event_start_date').val());
    }

    if (i) {
        form_data.append("mt_idx", i);
        $('#sgdt_mt_idx').val(i);
    }

    $.ajax({
        url: "./location_update",
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
                $('#location_info_box').html(data);
            }
        },
        error: function(err) {
            console.log(err);
        },
    });
}

function f_modal_schedule_member() {
    var form_data = new FormData();
    form_data.append("act", "get_schedule_member");

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
                $('#schedule_member_content').html(data);
                setTimeout(() => {
                    $('#schedule_member').modal('show');
                }, 100);
            }
        },
        error: function(err) {
            console.log(err);
        },
    });
}

function f_get_mt_file1(i) {
    var form_data = new FormData();
    form_data.append("act", "get_mt_file1");
    form_data.append("mt_idx", i);

    $.ajax({
        url: "./index_update",
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
                $('#map_mt_file1').val(data);
            }
        },
        error: function(err) {
            console.log(err);
        },
    });
}
</script>

<!-- 토스트 Toast 토스트 넣어두었습니다. 필요하시면 사용하심됩니다.! 사용할 버튼에 id="ToastBtn" 넣으면 사용가능! -->
<div id="Toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-body">
        <p><i class="xi-check-circle mr-2"></i>위치가 등록되었습니다.</p> <!-- 성공메시지 -->
        <!-- <p><i class="xi-error mr-2"></i>에러메시지</p> -->
    </div>
</div>

<div class="modal fade" id="schedule_member" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <form method="post" name="frm_schedule_member" id="frm_schedule_member">
                <div class="modal-header">
                    <p class="modal-title line1_text fs_20 fw_700">멤버 선택</p>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?=CDN_HTTP?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y px-0" style="min-height:380px;" id="schedule_member_content"></div>
                <div class="modal-footer border-0 p-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0">멤버 선택완료</button>
                </div>
            </form>
            <script>
            $("#frm_schedule_member").validate({
                submitHandler: function() {
                    var f = document.frm_schedule_member;

                    var q = 0;
                    $(".sgdt_idx_c").each(function() {
                        if ($(this).prop("checked") == true) {
                            q++;
                        }
                    });

                    if (q < 1) {
                        jalert("멤버를 선택해주세요.");
                        return false;
                    }

                    var sgdt_idx_t = f.sgdt_idx_r1.value;

                    var form_data = new FormData();
                    form_data.append("act", "location_member_input");
                    form_data.append("sgdt_idx", $('#sgdt_idx_r1_' + sgdt_idx_t).val());
                    form_data.append("sgdt_mt_idx", $('#mt_idx_r1_' + sgdt_idx_t).val());

                    $.ajax({
                        url: "./location_update",
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
                                setTimeout(() => {
                                    f_get_member_location($('#mt_idx_r1_' + sgdt_idx_t).val());
                                    f_get_info_location($('#mt_idx_r1_' + sgdt_idx_t).val());
                                }, 100);
                                $('#schedule_member').modal('hide');
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });

                    return false;
                },
                rules: {
                    sgdt_idx_r1: {
                        required: true,
                    },
                },
                messages: {
                    sgdt_idx_r1: {
                        required: "멤버를 선택해주세요.",
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
        </div>
    </div>
</div>

<!-- G-5 알림 설정 -->
<div class="modal fade" id="arm_setting_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">위치 알림을 설정합니다.</p>
                <p class="fs_14 fw_400 text_gray mt-3 text_dynamic text-center">위치와 관련된 일정에 대한 알림을 설정합니다.</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0">알림설정하기</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- G-5 위치 삭제 -->
<div class="modal fade" id="location_delete_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">위치를 삭제합니다.</p>
                <p class="fs_14 fw_400 text_gray mt-3 text_dynamic text-center">위치 삭제 시 연관된 일정도 전체 삭제됩니다.</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" data-dismiss="modal" aria-label="Close">아니요</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="f_delete_schedule();">삭제하기</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function f_del_location_modal(i) {
    $('#sst_idx').val(i);
    $('#location_delete_modal').modal('show');
}

function f_delete_schedule() {
    $('#location_delete_modal').modal('hide');

    var sst_idx = $('#sst_idx').val();

    var form_data = new FormData();
    form_data.append("act", "schedule_delete");
    form_data.append("sst_idx", sst_idx);

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
                var sgdt_mt_idx = $('#sgdt_mt_idx').val();
                f_get_info_location(sgdt_mt_idx);
            }
        },
        error: function(err) {
            console.log(err);
        },
    });
}

// 바텀시트 업다운
$('.opt_bottom .top_bar_wrap').click(function() {
    $('.opt_bottom').toggleClass('on');
});

(function($) {
    'use strict';
    $(function() {
        f_member_location_info();
    });
})(jQuery);


function f_get_angle(lat1, lon1, lat2, lon2) {
    var lat1 = lat1 * Math.PI / 180;
    if (lat2 == '') {
        var lat2 = lat1 * Math.PI / 180;
        var lon2 = lon1;
    } else {
        var lat2 = lat2 * Math.PI / 180;
    }
    var dLon = (lon2 - lon1) * Math.PI / 180;

    var y = Math.sin(dLon) * Math.cos(lat2);
    var x = Math.cos(lat1) * Math.sin(lat2) -
        Math.sin(lat1) * Math.cos(lat2) * Math.cos(dLon);

    var brng = Math.atan2(y, x);

    return (((brng * 180 / Math.PI) + 360) % 360);
}

function f_member_location_info() {
    $.post("./location_update", {
        act: "get_line",
        sgdt_mt_idx: $('#sgdt_mt_idx').val(),
        event_start_date: $('#event_start_date').val(),
    }, function(data) {
        var json_data = JSON.parse(data);

        if (json_data.result == 'true') {
            var st_lat;
            var st_lng;

            st_lat = json_data.data.map_gps[0];
            st_lng = json_data.data.map_gps[1];

            map = new naver.maps.Map("naver_map", {
                center: new naver.maps.LatLng(st_lat, st_lng),
                zoom: 19,
                scaleControl: false,
                logoControl: false,
                mapDataControl: false,
                zoomControl: false,
            });

            var polylinePath = [];

            var ii = 0;
            for (const [key, value] of Object.entries(json_data.data.gps)) {
                polylinePath.push(new naver.maps.LatLng(value[0], value[1]));

                if (ii > 0) {
                    makeMarker(map, new naver.maps.LatLng(value[0], value[1]), polylinePath[polylinePath.length - 2], ii);
                }

                ii++;
            }

            var polyline = new naver.maps.Polyline({
                path: polylinePath, //선 위치 변수배열
                strokeColor: '#0046FE', //선 색 빨강 #빨강,초록,파랑
                strokeOpacity: 0.8, //선 투명도 0 ~ 1
                strokeWeight: 12, //선 두께
                map: map //오버레이할 지도,
            });

            for (const [key, value] of Object.entries(json_data.data.marker)) {
                new naver.maps.Marker({
                    position: new naver.maps.LatLng(value[0], value[1]),
                    map: map,
                    icon: {
                        content: [
                            '<div class="map_rt">',
                            '<div class="map_rt_round">',
                            '<div class="map_rt_img">',
                            '<div class="rect_square rounded-pill">',
                            '<img src="' + value[2] + '" onerror="this.src=\'<?=$ct_no_profile_img_url?>\'" alt="이미지" />',
                            '</div>',
                            '</div>',
                            '</div>',
                            '</div>',
                        ].join(''),
                        size: new naver.maps.Size(38, 58),
                        anchor: new naver.maps.Point(19, 58),
                    }
                });
            }

            for (const [key, value] of Object.entries(json_data.data.marker_like)) {
                new naver.maps.Marker({
                    position: new naver.maps.LatLng(value[0], value[1]),
                    map: map,
                    icon: {
                        content: [
                            '<img src="<?=$ct_map_recom_point_img_url?>" style="height:40px;" onerror="this.src=\'<?=$ct_no_profile_img_url?>\'" alt="즐겨찾는 이미지" />'
                        ].join(''),
                        size: new naver.maps.Size(15, 21),
                    }
                });
            }

            map.setCursor('pointer');
        } else {
            jalert('지도를 표시할 수 없습니다.');
        }
    });
}

function naver_map_panto(lat, lng) {
    map.setCenter(new naver.maps.LatLng(lat, lng));
}

function makeMarker(map, position1, position2, index) {
    var ICON_GAP = 0;
    var ICON_SPRITE_IMAGE_URL = 'https://smap.dmonster.kr/design/img/map_direction.svg';
    var iconSpritePositionX = (index * ICON_GAP) + 1;
    var iconSpritePositionY = 1;

    var marker = new naver.maps.Marker({
        map: map,
        position: position1,
        title: 'map_maker' + index,
        icon: {
            url: ICON_SPRITE_IMAGE_URL,
            size: new naver.maps.Size(12, 12), // 이미지 크기
            // origin: new naver.maps.Point(iconSpritePositionX, iconSpritePositionY), // 스프라이트 이미지에서 클리핑 위치
            anchor: new naver.maps.Point(6, 6), // 지도상 위치에서 이미지 위치의 offset
            scaledSize: new naver.maps.Size(12, 12)
        }
    });

    var angle_t = f_get_angle(position2['x'], position2['y'], position1['x'], position1['y']);
    // console.log(position1['x'], position1['y'], position2['x'], position2['y'], angle_t);

    $("div[title|='map_maker" + index + "'").css('transform', 'rotate(' + angle_t + 'deg)');

    return marker;
}

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