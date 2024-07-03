<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu = '2';
$chk_sub_menu = '4';
$chk_post_code = 'Y';
include $_SERVER['DOCUMENT_ROOT']."/mng/head_menu.inc.php";

if ($_GET['act'] == "update") {
    $DB->where('rlt_idx', $_GET['rlt_idx']);
    $row = $DB->getone('recomand_location_t');

    $_act = "update";
    $_act_txt = " 수정";
} else {
    $_act = "input";
    $_act_txt = " 등록";
}
?>
<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpClientId=<?=NCPCLIENTID?>&submodules=geocoder"></script>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">추천장소<?=$_act_txt?></h4>

                    <form method="post" name="frm_form" id="frm_form" action="./recomand_location_update" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="rlt_idx" id="rlt_idx" value="<?=$row['rlt_idx']?>" />

                        <div class="form-group row">
                            <label for="rlt_cate" class="col-sm-2 col-form-label">분야 <b class="text-danger">*</b></label>
                            <div class="col-sm-4">
                                <select name="rlt_cate" id="rlt_cate" class="form-control form-control-sm">
                                    <option value="">분야선택</option>
                                    <?=$arr_rlt_cate_option?>
                                </select>
                            </div>
                            <label for="rlt_title" class="col-sm-2 col-form-label">추천장소명 <b class="text-danger">*</b></label>
                            <div class="col-sm-4">
                                <input type="text" name="rlt_title" id="rlt_title" value="<?=$row['rlt_title']?>" class="form-control form-control-sm" maxlength="100" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="rlt_student" class="col-sm-2 col-form-label">수용능력</label>
                            <div class="col-sm-1">
                                <input type="text" name="rlt_student" id="rlt_student" value="<?=$row['rlt_student']?>" class="form-control form-control-sm" numberOnly maxlength="4" />
                            </div>
                            <label for="rlt_psnby_thcc_cntnt" class="col-sm-2 col-form-label">인당수강료</label>
                            <div class="col-sm-7">
                                <input type="text" name="rlt_psnby_thcc_cntnt" id="rlt_psnby_thcc_cntnt" value="<?=$row['rlt_psnby_thcc_cntnt']?>" class="form-control form-control-sm" maxlength="200" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="rlt_zip" class="col-sm-2 col-form-label">주소 <b class="text-danger">*</b></label>
                            <div class="col-sm-8">
                                <p class="form-inline">
                                    <input type="text" class="form-control form-control-sm" name="rlt_zip" id="rlt_zip" value="<?=$row['rlt_zip']?>" style="width:100px;" placeholder="" readonly>
                                    <button type="button" class="btn btn-secondary ml-2" onclick="DaumPostcode('rlt_zip', 'rlt_add1', 'rlt_add2', 'wrap_zip1');">우편번호</button>
                                </p>

                                <div id="wrap_zip1" style="display:none;border:1px solid;width:100%;height:300px;margin:5px 0;position:relative">
                                    <img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnFoldWrap" style="cursor:pointer;position:absolute;right:0px;top:-1px;z-index:1" onclick="foldDaumPostcode('wrap_zip1')" alt="접기 버튼">
                                </div>

                                <p>
                                    <input type="text" class="form-control form-control-sm" name="rlt_add1" id="rlt_add1" value="<?=$row['rlt_add1']?>" placeholder="" readonly>
                                </p>

                                <p>
                                    <input type="text" class="form-control form-control-sm" name="rlt_add2" id="rlt_add2" value="<?=$row['rlt_add2']?>" placeholder="">
                                </p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="rlt_url" class="col-sm-2 col-form-label">홈페이지주소</label>
                            <div class="col-sm-8">
                                <input type="text" name="rlt_url" id="rlt_url" value="<?=$row['rlt_url']?>" class="form-control form-control-sm" maxlength="500" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="rlt_tel1" class="col-sm-2 col-form-label">연락처1 <b class="text-danger">*</b></label>
                            <div class="col-sm-2">
                                <input type="text" name="rlt_tel1" id="rlt_tel1" value="<?=$row['rlt_tel1']?>" class="form-control form-control-sm" maxlength="20" numberOnly />
                                <small id="rlt_tel1_help" class="form-text text-muted">* "-" 없이 입력, 숫자만</small>
                            </div>
                            <label for="rlt_tel2" class="col-sm-2 col-form-label">연락처2</label>
                            <div class="col-sm-2">
                                <input type="text" name="rlt_tel2" id="rlt_tel2" value="<?=$row['rlt_tel2']?>" class="form-control form-control-sm" maxlength="100" />
                                <small id="rlt_tel2_help" class="form-text text-muted">* "-" 없이 입력, 숫자만</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="rlt_udate" class="col-sm-2 col-form-label">수정일자</label>
                            <div class="col-sm-2">
                                <input type="text" name="rlt_udate" id="rlt_udate" value="<?=$row['rlt_udate']?>" class="form-control form-control-sm" readonly />
                            </div>
                            <label for="rlt_lat" class="col-sm-2 col-form-label">GPS <b class="text-danger">*</b></label>
                            <div class="col-sm-4 form-inline">
                                위도 <input type="text" name="rlt_lat" id="rlt_lat" value="<?=$row['rlt_lat']?>" class="form-control form-control-sm mr-2 ml-2" style="width:120px;" readonly />
                                경도 <input type="text" name="rlt_long" id="rlt_long" value="<?=$row['rlt_long']?>" class="form-control form-control-sm ml-2" style="width:120px;" readonly />
                            </div>
                        </div>

                        <p class="p-3 text-center">
                            <input type="submit" value="확인" class="btn btn-outline-primary" />
                            <input type="button" value="목록" onclick="history.go(-1);" class="btn btn-outline-secondary mx-2" />
                        </p>

                    </form>
                    <script type="text/javascript">
                    <?php if ($row['rlt_cate']) { ?>
                    $('#rlt_cate').val('<?=$row['rlt_cate']?>');
                    <?php } ?>

                    jQuery('#rlt_udate').datetimepicker({
                        format: 'Y-m-d',
                        timepicker: false
                    });

                    $("#frm_form").validate({
                        submitHandler: function() {
                            var f = document.frm_form;

                            $('#splinner_modal').modal('toggle');

                            return true;
                        },
                        rules: {
                            rlt_cate: {
                                required: true,
                            },
                            rlt_title: {
                                required: true,
                            },
                            rlt_add1: {
                                required: true,
                            },
                            rlt_add2: {
                                required: true,
                            },
                            rlt_tel1: {
                                required: true,
                            },
                        },
                        messages: {
                            rlt_cate: {
                                required: "분야를 선택해주세요.",
                            },
                            rlt_title: {
                                required: "추천장소명을 입력해주세요.",
                            },
                            rlt_add1: {
                                required: "주소를 입력해주세요.",
                            },
                            rlt_add2: {
                                required: "상세주소를 입력해주세요.",
                            },
                            rlt_tel1: {
                                required: "연락처1를 입력해주세요.",
                            },
                        },
                        errorPlacement: function(error, element) {
                            $(element)
                                .closest("form")
                                .find("span[for='" + element.attr("id") + "']")
                                .append(error);
                        },
                    });

                    function search_address_2_coordinate(address) {
                        naver.maps.Service.geocode({
                                query: address,
                            },
                            function(status, response) {
                                if (status === naver.maps.Service.Status.ERROR) {
                                    return jalert("검색결과를 찾을 수 없습니다. 재검색바랍니다.");
                                }

                                var item = response.v2.addresses[0];

                                if (item.y && item.x) {
                                    $('#rlt_lat').val(item.y);
                                    $('#rlt_long').val(item.x);
                                }
                            }
                        );

                        return false;
                    }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>