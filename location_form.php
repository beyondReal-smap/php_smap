<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '4';
$h_menu = '2';
$_SUB_HEAD_TITLE = $translations['txt_location']; // 위치
include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";

if($_SESSION['_mt_idx'] == '') {
    alert($translations['txt_login_required'], './login', ''); // 로그인이 필요합니다.
}
?>
<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?=NCPCLIENTID?>&submodules=geocoder&callback=CALLBACK_FUNCTION"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/anypicker@latest/dist/anypicker-all.min.css" />
<!-- <script type="text/javascript" src="//cdn.jsdelivr.net/npm/anypicker@latest/dist/anypicker.min.js"></script> -->
<script type="text/javascript" src="<?=CDN_HTTP?>/lib/anypicker/anypicker.js?v=<?=$v_txt?>"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/anypicker@latest/dist/i18n/anypicker-i18n.js"></script>

<div class="container sub_pg">
    <div class="mt_22">
        <form method="post" name="frm_form" id="frm_form" action="./location_update" target="hidden_ifrm" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="input_location" />
            <div class="ip_wr">
                <input type="text" class="form-control txt-cnt" name="slt_title" id="slt_title" value="" minlength="2" maxlength="20" data-length-id="slt_title_cnt" oninput="maxLengthCheck(this)" placeholder="<?=$translations['txt_enter_location_name']?>"> <!-- 위치명을 입력해주세요. -->
                <p class="fc_gray_500 fs_12 text-right mt-2">(<span id="slt_title_cnt">0</span>/20)</p>
            </div>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_member.png" alt="<?=$translations['txt_member_icon']?>"></div> <!-- 멤버 아이콘 -->
                    <div class="col">
                        <input type="hidden" name="sgdt_idx" id="sgdt_idx" value="" />
                        <input type="text" readonly class="form-none cursor_pointer" name="sgdt_idx_t" id="sgdt_idx_t" placeholder="<?=$translations['txt_select_member']?>" value="<?=$row_sst['sgdt_idx_t']?>" onclick="f_modal_schedule_member();"> <!-- 멤버 선택 -->
                    </div>
                </div>
            </div>
            <div class="line_ip mt_25">
                <div class="row">
                    <div class="col col-auto line_tit"><img src="<?=CDN_HTTP?>/img/ip_ic_location.png" alt="<?=$translations['txt_location_icon']?>"></div> <!-- 위치 아이콘 -->
                    <div class="col">
                        <div class="d-flex align-items-center">
                            <input type="text" readonly class="form-none cursor_pointer flex-fill" name="slt_idx_t" id="slt_idx_t" placeholder="<?=$translations['txt_select_location']?>" value="" onclick="f_modal_schedule_location();"> <!-- 위치 선택 -->
                        </div>
                        <!-- value 안에 데이터 넣어 주세요 -->

                        <input type="hidden" name="slt_idx" id="slt_idx" value="<?=$row_sst['slt_idx']?>" />
                        <input type="hidden" name="sst_location_title" id="sst_location_title" value="<?=$row_sst['sst_location_title']?>" />
                        <input type="hidden" name="sst_location_add" id="sst_location_add" value="<?=$row_sst['sst_location_add']?>" />
                        <input type="hidden" name="sst_location_lat" id="sst_location_lat" value="<?=$row_sst['sst_location_lat']?>" />
                        <input type="hidden" name="sst_location_long" id="sst_location_long" value="<?=$row_sst['sst_location_long']?>" />
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="button" class="btn btn-secondary btn-sm fc_primary pl_14 pr_11 mt_25 mx-auto" onclick="location.href='./schedule_form'"><?=$translations['txt_enter_schedule_together']?><i class="xi-angle-right-min ml_19"></i></button> <!-- 일정도 같이 입력할래요! -->
            </div>
            <div class="b_botton">
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block" id="ToastBtn"><?=$translations['txt_complete_location_input']?></button> <!-- 위치입력 완료 -->
            </div>
        </form>
    </div>
</div>

<!-- 토스트 Toast 토스트 넣어두었습니다. 필요하시면 사용하심됩니다.! 사용할 버튼에 id="ToastBtn" 넣으면 사용가능! -->
<div id="Toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-body">
        <p><i class="xi-check-circle mr-2"></i><?=$translations['txt_location_registration_success']?></p> <!-- 위치가 등록되었습니다. -->
        <!-- <p><i class="xi-error mr-2"></i>에러메시지</p> -->
    </div>
</div>

<!-- F-4 일정 입력 > 멤버 선택  -->
<div class="modal fade" id="schedule_member" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <form method="post" name="frm_schedule_member" id="frm_schedule_member">
                <div class="modal-header">
                    <p class="modal-title line1_text fs_20 fw_700"><?=$translations['txt_select_member']?></p> <!-- 멤버 선택 -->
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?=CDN_HTTP?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y px-0" style="min-height:380px;" id="schedule_member_content"></div>
                <div class="modal-footer border-0 p-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0"><?=$translations['txt_complete_member_selection']?></button> <!-- 멤버 선택완료 -->
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
                        jalert("<?=$translations['txt_please_select_member']?>"); // 멤버를 선택해주세요.
                        return false;
                    }

                    $('#sgdt_idx').val(f.sgdt_idx_r1.value);
                    $('#sgdt_idx_t').val($('#mt_nickname_r1_' + f.sgdt_idx_r1.value).val());
                    $('#schedule_member').modal('hide');

                    return false;
                },
                rules: {
                    sgdt_idx_r1: {
                        required: true,
                    },
                },
                messages: {
                    sgdt_idx_r1: {
                        required: "<?=$translations['txt_please_select_member']?>", // 멤버를 선택해주세요.
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

<!-- F-4 위치선택 목록  -->
<div class="modal fade" id="schedule_location" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <form method="post" name="frm_schedule_location" id="frm_schedule_location">
                <div class="modal-header">
                    <p class="modal-title line1_text fs_20 fw_700"><?=$translations['txt_select_location']?></p> <!-- 위치선택 -->
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?=CDN_HTTP?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y pt-0" style="height:400px;">
                    <div class="text-center py-5 border-top">
                        <div class="mx-auto"><img src="<?=CDN_HTTP?>/img/icon_location.png" style="max-width:4.9rem;"></div>
                        <button type="button" class="btn btn-secondary btn-sm fc_primary pl_14 pr_11 mt_12 mx-auto" onclick="f_modal_schedule_map();">지도에서 선택할래요<i class="xi-angle-right-min ml_19"></i></button>
                    </div>
                    <div class="bargray_fluid mx_n20"></div>

                    <div class="location_mark my_20">
                        <p class="tit_h3 fs_15 mb-4"><?=$translations['txt_favorite_location']?></p> <!-- 즐겨찾는 위치 -->
                        <ul id="location_like_list_box" class="scroll_bar_y" style="min-height:30rem;">

                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 p-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0"><?=$translations['txt_select_location']?></button> <!-- 위치 선택하기 -->
                </div>
            </form>
            <script>
            function f_modal_schedule_map() {
                $('#schedule_location').modal('hide');
                setTimeout(() => {
                    var form_data = new FormData();
                    form_data.append("act", "get_schedule_map");

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
                                setTimeout(() => {
                                    $('#schedule_map').modal('show');
                                }, 100);
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        },
                    });
                }, 100);
            }

            $("#frm_schedule_location").validate({
                submitHandler: function() {
                    var f = document.frm_schedule_location;

                    var q = 0;
                    $(".slt_idx_c").each(function() {
                        if ($(this).prop("checked") == true) {
                            q++;
                        }
                    });

                    if (q < 1) {
                        jalert("위치를 선택해주세요.");
                        return false;
                    }

                    var slt_idx_r1_t = f.slt_idx_r1.value;

                    $('#slt_idx').val(slt_idx_r1_t);
                    $('#sst_location_title').val($('#slt_title_' + slt_idx_r1_t).val());
                    $('#sst_location_add').val($('#slt_add_' + slt_idx_r1_t).val());
                    $('#sst_location_lat').val($('#slt_lat_' + slt_idx_r1_t).val());
                    $('#sst_location_long').val($('#slt_long_' + slt_idx_r1_t).val());
                    if ($('#slt_title_' + slt_idx_r1_t).val()) {
                        $('#slt_idx_t').val($('#slt_title_' + slt_idx_r1_t).val());
                    } else {
                        $('#slt_idx_t').val($('#slt_add_' + slt_idx_r1_t).val());
                    }
                    $('#schedule_location').modal('hide');

                    return false;
                },
                rules: {
                    sgdt_idx_r1: {
                        required: true,
                    },
                },
                messages: {
                    sgdt_idx_r1: {
                        required: "<?=$translations['txt_please_select_member']?>", // 멤버를 선택해주세요.
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

<style>
.pin_cont {
    position: absolute;
    top: 2rem;
}
</style>
<div class="modal fade" id="schedule_map" tabindex="-1">
    <div class="modal-dialog modal-default modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content" id="schedule_map_content">
            <form method="post" name="frm_schedule_map" id="frm_schedule_map">
                <div class="modal-header">
                    <p class="modal-title line1_text fs_20 fw_700">위치 선택</p>
                    <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><img src="<?=CDN_HTTP?>/img/modal_close.png"></button></div>
                </div>
                <div class="modal-body scroll_bar_y" style="height:70vh;">
                    <div id="naver_map" style="width:100%;height:65vh;"></div>
                    <div class="px-0 py-0 map_wrap d-none-temp" id="map_info_box">
                        <div class="map_wrap_re">
                            <div class="pin_cont bg-white pt_20 px_16 pb_16 rounded_10 ml-2 mr-2">
                                <ul>
                                    <li class="d-flex">
                                        <div class="name flex-fill">
                                            <span class="fs_12 fw_600 text-primary"><?=$translations['txt_selected_location']?></span> <!-- 선택한 위치 -->
                                            <div class="fs_14 fw_600 text_dynamic mt-1 line_h1_3" id="location_add"></div>
                                        </div>
                                        <button type="button" class="mark_btn" id="btn_location_like" onclick="f_location_like();"></button>
                                    </li>
                                    <li class="d-flex mt-3">
                                        <div class="name flex-fill">
                                            <span class="fs_12 fw_600 text-primary"><?=$translations['txt_location_nickname']?></span> <!-- 별칭 -->
                                            <input class="fs_14 fw_600 fc_gray_600 form-control text_dynamic mt-1 line_h1_3 loc_nickname" name="slt_title" id="slt_title" value="" placeholder="<?=$translations['txt_enter_location_name']?>"> <!-- 별칭을 입력해주세요 -->
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 p-0">
                    <button type="submit" class="btn btn-md btn-block btn-primary mx-0 my-0"><?=$translations['txt_select_location_complete']?></button> <!-- 위치 선택완료 -->
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function f_location_like_delete(i) {
    $.alert({
        title: '',
        type: "blue",
        typeAnimated: true,
        content: '<?=$translations['txt_delete_favorite_location']?>', // 즐겨찾는 위치를 삭제하시겠습니까?
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
        jalert('<?=$translations['txt_enter_location_name']?>'); // 별칭을 입력바랍니다.
        return false;
    }

    $.alert({
        title: '',
        type: "blue",
        typeAnimated: true,
        content: '<?=$translations['txt_register_location']?>', // 위치를 등록하시겠습니까?
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
            map.addListener("click", function(e) {
                searchCoordinateToAddress(e.coord);
            });
            return false;
        }

        function searchCoordinateToAddress(latlng) {
            // infoWindow.close();

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

                    // infoWindow.setContent(['<div style="padding:10px;min-width:200px;line-height:150%;">', '<h4 style="margin-top:5px;">검색 좌표</h4><br />', htmlAddresses.join("<br />"), "</div>"].join("\n"));
                    // infoWindow.open(map, latlng);

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
            jalert('<?=$translations['txt_please_select_location']?>'); // 위치를 선택해주세요.
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
            required: "<?=$translations['txt_please_select_location']?>", // 위치를 선택해주세요.
        },
    },
    errorPlacement: function(error, element) {
        $(element)
            .closest("form")
            .find("span[for='" + element.attr("id") + "']")
            .append(error);
    },
});

function f_modal_schedule_member() {
    var form_data = new FormData();
    form_data.append("act", "get_schedule_member");
    <?php if($row_sst['sgdt_idx']) { ?>
    form_data.append("sgdt_idx", "<?=$row_sst['sgdt_idx']?>");
    <?php } ?>

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

function f_modal_schedule_location() {
    f_location_like_list();
    setTimeout(() => {
        $('#schedule_location').modal('show');
    }, 100);
}

function f_location_like_list() {
    var form_data = new FormData();
    form_data.append("act", "list_like_location");

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
                $('#location_like_list_box').html(data);
            }
        },
        error: function(err) {
            console.log(err);
        },
    });
}

$("#frm_form").validate({
    submitHandler: function() {
        // $('#splinner_modal').modal('toggle');

        return true;
    },
    rules: {
        slt_title: {
            required: true,
        },
        sgdt_idx_t: {
            required: true,
        },
        slt_idx_t: {
            required: true,
        },
    },
    messages: {
        slt_title: {
            required: "<?=$translations['txt_enter_location_name']?>", // 위치명을 입력해주세요.
        },
        sgdt_idx_t: {
            required: "<?=$translations['txt_please_select_member']?>", // 멤버를 선택해주세요.
        },
        slt_idx_t: {
            required: "<?=$translations['txt_please_select_location']?>", // 위치를 선택해주세요.
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