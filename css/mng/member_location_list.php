<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head.inc.php";
$chk_menu = '2';
$chk_sub_menu = '5';
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head_menu.inc.php";
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">내장소정보</h4>
                    <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
                        <input type="hidden" name="act" id="act" value="list" />
                        <input type="hidden" name="obj_list" id="obj_list" value="member_location_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./member_location_update" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />

                        <div class="row no-gutters mb-2">
                            <div class="col-xl-4">
                            </div>
                            <div class="col-xl-8">
                                <div class="float-right form-inline">
                                    <div class="form-group mx-1">
                                        <select name="obj_sel_search" id="obj_sel_search" class="form-control form-control-sm">
                                            <option value="all">통합검색</option>
                                            <option value="a1.slt_title">장소명</option>
                                            <option value="a1.slt_add">주소</option>
                                        </select>
                                    </div>

                                    <div class="form-group mx-1">
                                        <input type="text" class="form-control form-control-sm" style="width:200px;" name="obj_search_txt" id="obj_search_txt" value="" />
                                    </div>

                                    <div class="form-group mx-1">
                                        <input type="button" class="btn btn-info" value="검색" onclick="f_get_box_mng_list()" />
                                    </div>

                                    <div class="form-group mx-1">
                                        <input type="button" class="btn btn-secondary" value="초기화" onclick="f_localStorage_reset_go('./member_location_list');" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <form method="post" name="frm_form_excel" id="frm_form_excel" class="d-none">
                        <input type="hidden" name="act" id="act" value="excel_upload" />
                        <input type="file" name="excel_file" id="excel_file" value="" />
                    </form>
                    <script>
                        $(document).ready(function() {
                            f_get_box_mng_list();
                        });

                        $('#excel_file').on('change', function(e) {
                            f_excel_smt();
                        });

                        function f_excel_smt() {
                            var form_t = $("#frm_form_excel")[0];
                            var formData_t = new FormData(form_t);

                            if (form_t.excel_file.value == "") {
                                jalert("엑셀을 입력바랍니다.", '', form_t.excel_file.focus());
                                form_t.excel_file.focus();
                                return false;
                            }

                            $('#splinner_modal').modal('toggle');

                            $.ajax({
                                url: './recomand_location_update',
                                enctype: "multipart/form-data",
                                data: formData_t,
                                type: "POST",
                                async: true,
                                contentType: false,
                                processData: false,
                                cache: true,
                                timeout: 5000,
                                success: function(data) {
                                    if (data) {
                                        var json_data = JSON.parse(data);

                                        jalert_url("총 업로드 : " + comma_num(json_data.data.tt_cnt1) + ", 등록 : " + comma_num(json_data.data.tt_cnt2) + "<br/>" + json_data.data.err_msg, 'reload');
                                    } else {
                                        console.log(data);
                                    }
                                },
                                error: function(err) {
                                    console.log(err);
                                },
                            });
                        }

                        <?php if ($_POST['obj_sel_search']) { ?>
                            $('#obj_sel_search').val('<?= $_POST['obj_sel_search'] ?>');
                        <?php } ?>
                        <?php if ($_POST['obj_sel_rlt_cate']) { ?>
                            $('#obj_sel_rlt_cate').val('<?= $_POST['obj_sel_rlt_cate'] ?>');
                        <?php } ?>
                    </script>

                    <div id="member_location_list_box"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/foot.inc.php";
?>