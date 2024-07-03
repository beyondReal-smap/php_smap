<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head.inc.php";
$chk_menu = '2';
$chk_sub_menu = '5';
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head_menu.inc.php";

if ($_GET['act'] == "update") {
    $DB->where('slt_idx', $_GET['slt_idx']);
    $row = $DB->getone('smap_location_t');

    $DB->where('sgdt_idx', $row['sgdt_idx']);
    $sgdt_row = $DB->getone('smap_group_detail_t');

    $DB->where('mt_idx', $row['mt_idx']);
    $sgdt_mem_row = $DB->getone('member_t');

    $DB->where('sgt_idx', $sgdt_row['sgt_idx']);
    $sgt_row = $DB->getone('smap_group_t');

    $DB->where('mt_idx', $row['insert_mt_idx']);
    $insert_mem_row = $DB->getone('member_t');

    $_act = "update";
    $_act_txt = " 상세";
} else {
    $_act = "input";
    $_act_txt = " 등록";
}
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ko.js"></script>

<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?= NCPCLIENTID ?>&submodules=geocoder"></script>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">내장소<?= $_act_txt ?></h4>

                    <form method="post" name="frm_form" id="frm_form" action="./member_location_update" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?= $_act ?>" />
                        <input type="hidden" name="sst_idx" id="sst_idx" value="<?= $row['sst_idx'] ?>" />
                        <input type="hidden" name="sst_location_lat" id="sst_location_lat" value="<?= $row['sst_location_lat'] ?>" />
                        <input type="hidden" name="sst_location_long" id="sst_location_long" value="<?= $row['sst_location_long'] ?>" />

                        <div class="form-group row">
                            <label for="sgt_title" class="col-sm-2 col-form-label">그룹명</label>
                            <div class="col-sm-3">
                                <input type="text" name="sgt_title" id="sgt_title" value="<?= $sgt_row['sgt_title'] ?>" class="form-control-plaintext" readonly />
                            </div>
                            <label for="slt_title" class="col-sm-2 col-form-label">장소명</label>
                            <div class="col-sm-3">
                                <input type="text" name="slt_title" id="slt_title" value="<?= $row['slt_title'] ?>" class="form-control-plaintext" readonly />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="slt_title" class="col-sm-2 col-form-label">그룹원</label>
                            <div class="col-sm-3">
                                <input type="text" name="slt_title" id="slt_title" value="<?= $sgdt_mem_row['mt_name'] ?><?= $sgdt_mem_row['mt_nickname'] ? '(' . $sgdt_mem_row['mt_nickname'] . ')' : '' ?>" class="form-control-plaintext" readonly />
                            </div>
                            <label for="slt_title" class="col-sm-2 col-form-label">주소</label>
                            <div class="col-sm-3">
                                <input type="text" name="slt_title" id="slt_title" value="<?= $row['slt_add'] ?>" class="form-control-plaintext" readonly />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="slt_title" class="col-sm-2 col-form-label">위치알림여부</label>
                            <div class="col-sm-3">
                                <input type="text" name="slt_title" id="slt_title" value="<?= $arr_slt_enter_chk[$row['slt_enter_alarm']] ?>" class="form-control-plaintext" readonly />
                            </div>
                            <label for="slt_title" class="col-sm-2 col-form-label">등록일시</label>
                            <div class="col-sm-3">
                                <input type="text" name="slt_title" id="slt_title" value="<?= DateType($row['slt_wdate'], 6) ?>" class="form-control-plaintext" readonly />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="slt_title" class="col-sm-2 col-form-label">등록 그룹원</label>
                            <div class="col-sm-3">
                                <input type="text" name="slt_title" id="slt_title" value="<?= $insert_mem_row['mt_name'] ?><?= $insert_mem_row['mt_nickname'] ? '(' . $insert_mem_row['mt_nickname'] . ')' : '' ?>" class="form-control-plaintext" readonly />
                            </div>
                        </div>

                        <div class="form-group row" id="naver_map_box">
                            <label for="naver_map" class="col-sm-2 col-form-label">장소 지도</label>
                            <div class="col-sm-7">
                                <input type="text" name="slt_add" id="slt_add" value="<?= $row['slt_add'] ?>" class="form-control-plaintext" readonly />
                                <!-- <input type="text" name="sst_location_add" id="sst_location_add" value="<?= $row['slt_add'] ?>" class="form-control form-control-sm" readonly /> -->

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
                                    <div class="search d-none">
                                        <input id="address" type="text" placeholder="도로명으로 검색바랍니다." value="" />
                                        <input id="map_submit" type="button" value="주소 검색" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="p-3 text-center">
                            <!-- <input type="submit" value="확인" class="btn btn-outline-primary" /> -->
                            <input type="button" value="목록" onclick="history.go(-1);" class="btn btn-outline-secondary mx-2" />
                        </p>

                    </form>
                    <script type="text/javascript">
                        (function($) {
                            'use strict';
                            $(function() {
                                <?php if ($row['slt_idx']) { ?>
                                    $('#slt_idx').val('<?= $row['slt_idx'] ?>');
                                <?php } ?>

                                var st_lat;
                                var st_lng;
                                var markers = [],
                                    infoWindows = [];

                                st_lat = '<?php echo $row['slt_lat'] == "" ? 37.5665 : $row['slt_lat']; ?>';
                                st_lng = '<?php echo $row['slt_long'] == "" ? 126.9780 : $row['slt_long']; ?>';

                                var map = new naver.maps.Map("naver_map", {
                                    center: new naver.maps.LatLng(st_lat, st_lng),
                                    zoom: 19,
                                    mapTypeControl: true
                                });

                                var marker = new naver.maps.Marker({
                                    position: new naver.maps.LatLng(st_lat, st_lng),
                                    map: map,
                                    title: '<?= $row['slt_add'] ?>'
                                });
                                var infoWindow = new naver.maps.InfoWindow({
                                    content: '<div style="width:100%;text-align:center;padding:10px;"><p><b><?= $row['slt_title'] ?></b></p><p><?= $row['slt_add'] ?></p></div>'
                                });

                                naver.maps.Event.addListener(marker, "click", function(e) {
                                    if (infoWindow.getMap()) {
                                        infoWindow.close();
                                    } else {
                                        infoWindow.open(map, marker);
                                    }
                                });

                                map.setCursor('pointer');
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
include $_SERVER['DOCUMENT_ROOT'] . "/mng/foot.inc.php";
?>