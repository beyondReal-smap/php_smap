<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu = '2';
$chk_sub_menu = '2';
include $_SERVER['DOCUMENT_ROOT']."/mng/head_menu.inc.php";

if ($_GET['act'] == "update") {
    $DB->where('sst_idx', $_GET['sst_idx']);
    $row = $DB->getone('smap_schedule_t');

    if($row['mt_idx']) {
        $DB->where('mt_idx', $row['mt_idx']);
        $row_mt = $DB->getone('member_t');
    }

    if($row['slt_idx']) {
        $DB->where('slt_idx', $row['slt_idx']);
        $row_slt = $DB->getone('smap_location_t');
    }

    $row['sst_sdate'] = substr($row['sst_sdate'], 0, 16);
    $row['sst_edate'] = substr($row['sst_edate'], 0, 16);

    if($row['sst_repeat_json']) {
        $sst_repeat_json_de = json_decode($row['sst_repeat_json'], true);

        $sst_repeat_json_t1 = $sst_repeat_json_de['r1'];

        if($sst_repeat_json_de['r1'] == '3' && $sst_repeat_json_de['r2']) {
            $sst_repeat_json_t2 = explode(',', $sst_repeat_json_de['r2']);
        }
    }

    $_act = "update";
    $_act_txt = " 수정";
} else {
    $_act = "input";
    $_act_txt = " 등록";
}
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ko.js"></script>

<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?=NCPCLIENTID?>&submodules=geocoder"></script>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">일정정보<?=$_act_txt?></h4>

                    <form method="post" name="frm_form" id="frm_form" action="./schedule_update" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="sst_idx" id="sst_idx" value="<?=$row['sst_idx']?>" />
                        <input type="hidden" name="sst_location_lat" id="sst_location_lat" value="<?=$row['sst_location_lat']?>" />
                        <input type="hidden" name="sst_location_long" id="sst_location_long" value="<?=$row['sst_location_long']?>" />

                        <div class="form-group row">
                            <label for="mt_idx" class="col-sm-2 col-form-label">작성자 <b class="text-danger">*</b></label>
                            <div class="col-sm-3">
                                <select name="mt_idx" id="mt_idx" class="form-control form-control-sm" onchange="f_mt_idx_chg(this.value);">
                                    <option>아이디 또는 이름을 입력바랍니다.</option>
                                    <?php if($row_mt['mt_idx']) { ?>
                                    <option value="<?=$row_mt['mt_idx']?>" selected><?=$row_mt['mt_name']?> (<?=$row_mt['mt_id']?>)</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sst_title" class="col-sm-2 col-form-label">일정명 <b class="text-danger">*</b></label>
                            <div class="col-sm-5">
                                <input type="text" name="sst_title" id="sst_title" value="<?=$row['sst_title']?>" class="form-control form-control-sm" maxlength="50" />
                                <small id="sst_title_help" class="form-text text-muted">* 50자내외</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sst_sdate" class="col-sm-2 col-form-label">일정기간</label>
                            <div class="col-sm-3 input-group">
                                <input type="text" name="sst_sdate" id="sst_sdate" value="<?=$row['sst_sdate']?>" class="form-control form-control-sm" readonly /> <span class="m-2">~</span> <input type="text" name="sst_edate" id="sst_edate" value="<?=$row['sst_edate']?>" class="form-control form-control-sm" readonly />
                            </div>
                            <div class="col-sm-2 ml-3">
                                <div class="custom-control custom-switch mt-2">
                                    <input type="checkbox" class="custom-control-input" id="sst_all_day" name="sst_all_day" value="Y">
                                    <label class="custom-control-label" for="sst_all_day">하루종일</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sst_repeat_json" class="col-sm-2 col-form-label">반복</label>
                            <div class="col-sm-2">
                                <select name="sst_repeat_json" id="sst_repeat_json" class="form-control form-control-sm" onchange="f_sst_repeat_json_chg(this.value);">
                                    <option value="">선택</option>
                                    <?=$arr_sst_repeat_json_option?>
                                </select>
                            </div>
                            <div class="col-sm-6 d-none-temp mt-2" id="week_box">
                                <?php
                                    foreach ($arr_sst_repeat_json_r2 as $key => $val) {
                                        ?>
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" id="sst_repeat_json_week<?=$key?>" name="sst_repeat_json_week[]" value="<?=$key?>">
                                    <label class="custom-control-label" for="sst_repeat_json_week<?=$key?>"><?=$val?></label>
                                </div>
                                <?php
                                    }
?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sgt_idx" class="col-sm-2 col-form-label">멤버선택 <b class="text-danger">*</b></label>
                            <div class="col-sm-5 form-inline">
                                <select name="sgt_idx" id="sgt_idx" class="form-control form-control-sm" onchange="f_find_group_detail(this.value);">
                                    <option value="">그룹 선택</option>
                                </select>

                                <select name="sgdt_idx" id="sgdt_idx" class="form-control form-control-sm ml-3">
                                    <option value="">멤버 선택</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sst_alram" class="col-sm-2 col-form-label">알림</label>
                            <div class="col-sm-2">
                                <select name="sst_alram" id="sst_alram" class="form-control form-control-sm">
                                    <option value="">선택</option>
                                    <?=$arr_sst_alram_option?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="slt_idx" class="col-sm-2 col-form-label">즐겨찾는위치 <b class="text-danger">*</b></label>
                            <div class="col-sm-5">
                                <select name="slt_idx" id="slt_idx" class="form-control form-control-sm">
                                    <option value="">즐겨찾는 위치를 선택해주세요.</option>
                                    <?php if($row_slt['slt_idx']) { ?>
                                    <option value="<?=$row_slt['slt_idx']?>" selected><?=$row_slt['slt_title']?> (<?=$row_slt['slt_add']?>)</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row" id="naver_map_box">
                            <label for="naver_map" class="col-sm-2 col-form-label">지도로 위치선택 <b class="text-danger">*</b></label>
                            <div class="col-sm-7">
                                <input type="text" name="sst_location_add" id="sst_location_add" value="<?=$row['sst_location_add']?>" class="form-control form-control-sm" readonly />

                                <style type="text/css">
                                .search {
                                    position: absolute;
                                    z-index: 1000;
                                    top: 20px;
                                    left: 20px;
                                }

                                .search #address {
                                    width: 150px;
                                    height: 20px;
                                    line-height: 20px;
                                    border: solid 1px #555;
                                    padding: 5px;
                                    font-size: 12px;
                                    box-sizing: content-box;
                                }

                                .search #map_submit {
                                    height: 30px;
                                    line-height: 30px;
                                    padding: 0 10px;
                                    font-size: 12px;
                                    border: solid 1px #555;
                                    border-radius: 3px;
                                    cursor: pointer;
                                    box-sizing: content-box;
                                }
                                </style>
                                <div id="naver_map" class="mt-2" style="width:100%;height:600px;">
                                    <div class="search" style="">
                                        <input id="address" type="text" placeholder="도로명으로 검색바랍니다." value="" />
                                        <input id="map_submit" type="button" value="주소 검색" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sst_supplies" class="col-sm-2 col-form-label">준비물 입력</label>
                            <div class="col-sm-4">
                                <input type="text" name="sst_supplies" id="sst_supplies" value="<?=$row['sst_supplies']?>" class="form-control form-control-sm" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sst_memo" class="col-sm-2 col-form-label">메모 입력</label>
                            <div class="col-sm-4">
                                <input type="text" name="sst_memo" id="sst_memo" value="<?=$row['sst_memo']?>" class="form-control form-control-sm" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sst_contacrt" class="col-sm-2 col-form-label">연락처 입력</label>
                            <div class="col-sm-6">
                                <div class="mb-2">
                                    <input type="button" value="연락처 추가" onclick="f_sst_contacrt('add', '');" class="btn btn-primary btn-sm" />
                                </div>
                                <script>
                                function f_sst_contacrt(t, i) {
                                    if (t == 'add') {
                                        $.post("./schedule_update", {
                                            act: "contacrt_modal",
                                            mt_idx: $('#mt_idx').val(),
                                            sst_idx: $('#sst_idx').val(),
                                            sct_idx: i
                                        }, function(data) {
                                            if (data) {
                                                $('#modal-default-content').html(data);
                                                setTimeout(() => {
                                                    $('#modal-default').modal('show');
                                                }, 100);
                                            }
                                        });
                                    } else if (t == 'modify') {
                                        $.post("./schedule_update", {
                                            act: "contacrt_modal",
                                            mt_idx: $('#mt_idx').val(),
                                            sst_idx: $('#sst_idx').val(),
                                            sct_idx: i
                                        }, function(data) {
                                            if (data) {
                                                $('#modal-default-content').html(data);
                                                setTimeout(() => {
                                                    $('#modal-default').modal('show');
                                                }, 100);
                                            }
                                        });
                                    } else if (t == 'delete') {
                                        $.confirm({
                                            title: "경고",
                                            content: "정말 삭제하시겠습니까? 삭제된 자료는 복구되지 않습니다.",
                                            buttons: {
                                                confirm: {
                                                    text: "확인",
                                                    action: function() {
                                                        $.post("./schedule_update", {
                                                            act: "contacrt_delete",
                                                            mt_idx: $('#mt_idx').val(),
                                                            sct_idx: i
                                                        }, function(data) {
                                                            if (data == 'Y') {
                                                                $.alert({
                                                                    title: '',
                                                                    content: '삭제되었습니다.',
                                                                    buttons: {
                                                                        confirm: {
                                                                            text: "확인",
                                                                        },
                                                                    },
                                                                    onClose: function() {
                                                                        f_contact_list();
                                                                    },
                                                                });
                                                            }
                                                        });
                                                    },
                                                },
                                                cancel: {
                                                    text: "취소",
                                                    action: function() {
                                                        close();
                                                    },
                                                },
                                            },
                                        });
                                    }
                                }

                                function f_contact_list() {
                                    $.post("./schedule_update", {
                                        act: "contacrt_list",
                                        mt_idx: $('#mt_idx').val(),
                                        sst_idx: $('#sst_idx').val(),
                                    }, function(data) {
                                        if (data) {
                                            $('#contacrt_list_box').html(data);
                                        } else {
                                            $('#contacrt_list_box').html('');
                                        }
                                    });
                                }
                                </script>
                                <ul class="list-unstyled" id="contacrt_list_box">
                                </ul>
                            </div>
                        </div>

                        <p class="p-3 text-center">
                            <input type="submit" value="확인" class="btn btn-outline-primary" />
                            <input type="button" value="목록" onclick="history.go(-1);" class="btn btn-outline-secondary mx-2" />
                        </p>

                    </form>
                    <script type="text/javascript">
                    (function($) {
                        'use strict';
                        $(function() {
                            <?php if ($row['slt_idx']) { ?>
                            $('#slt_idx').val('<?=$row['slt_idx']?>');
                            <?php } ?>
                            <?php if ($row['mt_idx']) { ?>
                            f_find_group('<?=$row['mt_idx']?>');
                            setTimeout(() => {
                                $('#sgt_idx').val('<?=$row['sgt_idx']?>');
                                f_find_group_detail('<?=$row['sgt_idx']?>');
                                setTimeout(() => {
                                    $('#sgdt_idx').val('<?=$row['sgdt_idx']?>');
                                }, 100);
                            }, 100);
                            <?php } ?>
                            <?php if ($row['sst_all_day'] == 'Y') { ?>
                            $('#sst_all_day').prop('checked', true);
                            <?php } ?>
                            <?php if ($row['sst_alram']) { ?>
                            $('#sst_alram').val('<?=$row['sst_alram']?>');
                            <?php } ?>
                            <?php if ($sst_repeat_json_t1) { ?>
                            $('#sst_repeat_json').val('<?=$sst_repeat_json_t1?>');
                            <?php } ?>
                            <?php if ($sst_repeat_json_t1 == '3') { ?>
                            f_sst_repeat_json_chg('3');
                            setTimeout(() => {
                                <?php
if($sst_repeat_json_t2) {
    foreach($sst_repeat_json_t2 as $key => $val) {
        if($val) {
            echo "$('#sst_repeat_json_week".$val."').prop('checked', true);";
        }
    }
}
                                ?>
                            }, 100);
                            <?php } ?>

                            f_contact_list();

                            jQuery(function() {
                                jQuery('#sst_sdate').datetimepicker({
                                    format: 'Y-m-d H:i',
                                    onShow: function(ct) {
                                        this.setOptions({
                                            maxDate: jQuery('#sst_edate').val() ? jQuery('#sst_edate').val() : false
                                        })
                                    },
                                    timepicker: true
                                });
                                jQuery('#sst_edate').datetimepicker({
                                    format: 'Y-m-d H:i',
                                    onShow: function(ct) {
                                        this.setOptions({
                                            minDate: jQuery('#sst_sdate').val() ? jQuery('#sst_sdate').val() : false
                                        })
                                    },
                                    timepicker: true
                                });
                            });

                            $('#mt_idx').select2({
                                ajax: {
                                    url: './select2_update',
                                    type: "POST",
                                    dataType: 'json',
                                    data: function(params) {
                                        var query = {
                                            act: 'member_t',
                                            obj_search_txt: params.term
                                        }

                                        return query;
                                    },
                                    processResults: function(data) {
                                        return {
                                            results: data
                                        };
                                    },
                                },
                                minimumInputLength: 1,
                                theme: 'bootstrap4',
                                language: "ko"
                            });

                            $('#slt_idx').select2({
                                ajax: {
                                    url: './select2_update',
                                    type: "POST",
                                    dataType: 'json',
                                    data: function(params) {
                                        var query = {
                                            act: 'smap_location_t',
                                            obj_search_txt: params.term,
                                            mt_idx: $('#mt_idx').val()
                                        }

                                        return query;
                                    },
                                    processResults: function(data) {
                                        return {
                                            results: data
                                        };
                                    },
                                },
                                templateSelection: f_slt_selected,
                                minimumInputLength: 1,
                                theme: 'bootstrap4',
                                language: "ko"
                            });

                            var st_lat;
                            var st_lng;

                            st_lat = '<?php echo $row['sst_location_lat'] == "" ? 37.5665 : $row['sst_location_lat']; ?>';
                            st_lng = '<?php echo $row['sst_location_long'] == "" ? 126.9780 : $row['sst_location_long']; ?>';

                            var map = new naver.maps.Map("naver_map", {
                                center: new naver.maps.LatLng(st_lat, st_lng),
                                zoom: 19,
                                mapTypeControl: true
                            });

                            var marker = new naver.maps.Marker({
                                position: new naver.maps.LatLng(st_lat, st_lng),
                                map: map
                            });

                            var infoWindow = new naver.maps.InfoWindow({
                                anchorSkew: true
                            });

                            map.setCursor('pointer');

                            function initGeocoder() {
                                map.addListener("click", function(e) {
                                    searchCoordinateToAddress(e.coord);
                                });

                                $("#address").on("keydown", function(e) {
                                    var keyCode = e.which;

                                    if (keyCode === 13) {
                                        // Enter Key
                                        event.preventDefault();

                                        searchAddressToCoordinate($("#address").val());
                                    }
                                });

                                $("#map_submit").on("click", function(e) {
                                    e.preventDefault();

                                    searchAddressToCoordinate($("#address").val());
                                });

                                return false;
                            }

                            function searchCoordinateToAddress(latlng) {
                                infoWindow.close();

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

                                            f_map_info_val(latlng._lat, latlng._lng, address);
                                        }

                                        infoWindow.setContent(['<div style="padding:10px;min-width:200px;line-height:150%;">', '<h4 style="margin-top:5px;">검색 좌표</h4><br />', htmlAddresses.join("<br />"), "</div>"].join("\n"));

                                        infoWindow.open(map, latlng);
                                    }
                                );
                            }

                            function searchAddressToCoordinate(address) {
                                naver.maps.Service.geocode({
                                        query: address,
                                    },
                                    function(status, response) {
                                        if (status === naver.maps.Service.Status.ERROR) {
                                            return jalert("검색결과를 찾을 수 없습니다. 재검색바랍니다.");
                                        }

                                        if (response.v2.meta.totalCount === 0) {
                                            return jalert("검색결과 : " + response.v2.meta.totalCount + "건");
                                        }

                                        var add_t = "";

                                        var htmlAddresses = [],
                                            item = response.v2.addresses[0],
                                            point = new naver.maps.Point(item.x, item.y);

                                        if (item.roadAddress) {
                                            htmlAddresses.push("[도로명 주소] " + item.roadAddress);

                                            add_t = item.roadAddress;
                                        }

                                        if (item.jibunAddress) {
                                            htmlAddresses.push("[지번 주소] " + item.jibunAddress);

                                            add_t = item.jibunAddress;
                                        }

                                        if (item.y && item.x) {
                                            htmlAddresses.push("[GPS] 위도:" + item.y + ", 경도: " + item.x);

                                            f_map_info_val(item.y, item.x, add_t);
                                        }

                                        infoWindow.setContent(['<div style="padding:10px;min-width:200px;line-height:150%;">', '<h4 style="margin-top:5px;">검색 주소 : ' + address + "</h4><br />", htmlAddresses.join("<br />"), "</div>"].join("\n"));

                                        map.setCenter(point);
                                        infoWindow.open(map, point);
                                    }
                                );

                                return false;
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


                            function f_map_info_val(lat, lng, radd) {
                                if (lat) {
                                    $('#sst_location_lat').val(lat);
                                }
                                if (lng) {
                                    $('#sst_location_long').val(lng);
                                }
                                if (radd) {
                                    $('#sst_location_add').val(radd);
                                }

                                return false;
                            }

                            naver.maps.onJSContentLoaded = initGeocoder;
                        });
                    })(jQuery);

                    function f_map_open(o, i) {
                        $('#naver_map_box').show('toggle');
                    }

                    function f_slt_selected(d) {
                        if (d.slt_add) {
                            $('#sst_location_add').val(d.slt_add);
                        }

                        return d.text || d.id;
                    }

                    function f_mt_idx_chg(v) {
                        $('#sgt_idx').val('');
                        $('#sgdt_idx').val('');
                        $('#slt_idx').val(null).trigger('change');
                        $('#sst_location_title').val('');
                        $('#sst_location_add').val('');
                        $('#sst_location_lat').val('');
                        $('#sst_location_long').val('');
                        f_contact_list();

                        if (v) {
                            f_find_group(v);
                        }
                    }

                    function f_find_group(v) {
                        if (v) {
                            $('#sgt_idx').empty();

                            $('#sgt_idx').append("<option value=''>그룹 선택</option>");

                            $.post("./select2_update", {
                                act: "find_group",
                                mt_idx: v
                            }, function(data) {
                                if (data) {
                                    $('#sgt_idx').append(data);
                                }
                            });
                        }
                    }

                    function f_find_group_detail(v) {
                        if (v) {
                            $('#sgdt_idx').empty();

                            $('#sgdt_idx').append("<option value=''>멤버 선택</option>");

                            $.post("./select2_update", {
                                act: "find_group_detail",
                                sgt_idx: v
                            }, function(data) {
                                if (data) {
                                    $('#sgdt_idx').append(data);
                                }
                            });
                        }
                    }

                    function f_sst_repeat_json_chg(v) {
                        if (v == '3') {
                            $('#week_box').show();
                        } else {
                            $('#week_box').hide();
                        }
                    }

                    $("#frm_form").validate({
                        submitHandler: function() {
                            var f = document.frm_form;

                            $('#splinner_modal').modal('toggle');

                            return true;
                        },
                        rules: {
                            mt_idx: {
                                required: true,
                            },
                            sst_title: {
                                required: true,
                            },
                            sgt_idx: {
                                required: true,
                            },
                            sgdt_idx: {
                                required: true,
                            },
                            sst_location_add: {
                                required: true,
                            },
                        },
                        messages: {
                            mt_idx: {
                                required: "작성자를 선택해주세요.",
                            },
                            sst_title: {
                                required: "일정명을 입력해주세요.",
                            },
                            sgt_idx: {
                                required: "그룹을 선택해주세요.",
                            },
                            sgdt_idx: {
                                required: "멤버를 선택해주세요.",
                            },
                            sst_location_add: {
                                required: "즐겨찾는 위치 또는 지도로 위치를 선택해주세요.",
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
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>