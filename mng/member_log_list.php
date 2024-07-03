<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head.inc.php";
$chk_menu = '2';
$chk_sub_menu = '3';
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head_menu.inc.php";
?>
<style>
    .inx-table-card.inx-table {
        background-color: transparent;
        box-shadow: none;
    }

    .inx-table-card.inx-table .table {
        margin-bottom: 0;
    }

    .inx-table-card.inx-card-main {
        background-color: transparent;
        box-shadow: none;
    }

    .inx-table-card.table.inx-table thead {
        display: none;
    }

    .inx-table-card.table.inx-table tbody {
        display: block;
    }

    .inx-table-card.table.inx-table tr {
        display: block;
        margin-bottom: 1.25rem;
        background-color: #fff;
        border-radius: 0.125rem;
        box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
    }

    .inx-table-card.table.inx-table td {
        display: block;
        text-align: center;
        vertical-align: middle;
    }

    .inx-table-card.table.inx-table td[data-title]:before {
        float: left;
        font-size: inherit;
        font-weight: 400;
        color: rgba(0, 0, 0, 0.54);
        content: attr(data-title);
        width: 60px;
    }

    .inx-table-card.inx-table.table-striped td,
    .inx-table-card.inx-table.table-striped tr:nth-child(odd) {
        background-color: #fff;
    }

    .inx-table-card.inx-table.table-striped td:nth-child(odd) {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .inx-table-card.inx-table.table-dark {
        background-color: transparent;
    }

    .inx-table-card.inx-table.table-dark tr {
        background-color: #343a40;
    }

    .inx-table-card.inx-table.table-dark td[data-title]:before {
        color: rgba(255, 255, 255, 0.7);
    }

    .inx-table-card.inx-table.table-hover tr:hover {
        background-color: #fff;
    }

    .inx-table-card.inx-table.table-hover td:hover {
        background-color: rgba(0, 0, 0, 0.075);
    }

    .inx-table-card.inx-table.table-hover.table-dark tr:hover {
        background-color: #343a40;
    }

    .inx-table-card.inx-table.table-hover.table-dark td:hover {
        background-color: rgba(255, 255, 255, 0.38);
    }

    .inx-table-card.inx-table.table-striped.table-dark td,
    .inx-table-card.inx-table.table-striped.table-dark tr:nth-child(odd) {
        background-color: #343a40;
    }

    .inx-table-card.inx-table.table-striped.table-dark td:nth-child(odd) {
        background-color: rgba(255, 255, 255, 0.02);
    }

    .inx-table-card.inx-z-depth {
        background-color: transparent;
        box-shadow: none;
    }

    #member_location_list_member_box {
        height: 985px;
        overflow-y: scroll;
        padding-left: 10px;
        padding-right: 10px;
    }
</style>
<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=unxdi5mt3f&submodules=geocoder"></script>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">위치정보 - 그룹</h4>
                    <form method="post" name="frm_list1" id="frm_list1" onsubmit="return false;">
                        <input type="hidden" name="act" id="act" value="list1" />
                        <input type="hidden" name="obj_list" id="obj_list" value="member_log_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list1" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./member_log_update" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />

                        <div class="row no-gutters mb-2">
                            <div class="col-xl-12">
                                <div class="float-right form-inline">
                                    <div class="form-group mx-1">
                                        <input type="text" class="form-control form-control-sm" style="width:200px;" name="obj_search_txt" id="obj_search_txt" value="" />
                                    </div>

                                    <div class="form-group mx-1">
                                        <input type="button" class="btn btn-info" value="검색" onclick="f_get_box_mng_list('1')" />
                                    </div>

                                    <div class="form-group mx-1">
                                        <input type="button" class="btn btn-secondary" value="초기화" onclick="f_get_box_mng_list_reset('frm_list1');" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <script>
                        $(document).ready(function() {
                            f_get_box_mng_list('1');
                        });

                        function f_member_location_list_group(i) {
                            $.post("./member_log_update", {
                                act: "group_detail",
                                sgt_idx: i,
                                sel_search_sdate: $('#sel_search_sdate').val()
                            }, function(data) {
                                if (data) {
                                    $('#member_location_list_member_box').html(data);
                                    $('#logFrame').attr('src', '../log_mng?sgt_idx='+i);
                                } else {
                                    $('#member_location_list_member_box').html('');
                                }
                            });
                        }

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

                        function f_member_location_info(i) {
                            $.post("./member_log_update", {
                                act: "get_line",
                                sst_idx: i
                            }, function(data) {
                                var json_data = JSON.parse(data);

                                if (json_data.result == 'true') {
                                    var st_lat;
                                    var st_lng;

                                    st_lat = json_data.data.map_gps[0];
                                    st_lng = json_data.data.map_gps[1];

                                    var map = new naver.maps.Map("naver_map", {
                                        center: new naver.maps.LatLng(st_lat, st_lng),
                                        zoom: 19,
                                        mapTypeControl: true
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

                                    map.setCursor('pointer');
                                } else {
                                    jalert('지도를 표시할 수 없습니다.');
                                }
                            });
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
                    </script>

                    <div id="member_log_list_box"></div>
                </div>
            </div>
        </div>
        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">위치정보 - 지도</h4>

                    <iframe id="logFrame" frameborder="0" width="100%" height="90%"></iframe>
                    <div id="naver_map" style="width:100%;height:600px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/foot.inc.php";
?>