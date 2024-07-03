<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu = '4';
$chk_sub_menu = '1';
include $_SERVER['DOCUMENT_ROOT']."/mng/head_menu.inc.php";
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">문의관리</h4>
                    <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
                        <input type="hidden" name="act" id="act" value="list" />
                        <input type="hidden" name="obj_list" id="obj_list" value="qna_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./qna_update" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />

                        <div class="row no-gutters mb-2">
                            <div class="col-xl-3">
                            </div>
                            <div class="col-xl-9">
                                <div class="float-right form-inline">
                                    <div class="form-group mx-1">
                                        <select name="sel_qt_status" id="sel_qt_status" class="form-control form-control-sm">
                                            <option value="">답변상태</option>
                                            <?=$arr_qt_status_option?>
                                        </select>
                                    </div>

                                    <div class="form-group mx-1">
                                        <select name="obj_sel_search" id="obj_sel_search" class="form-control form-control-sm">
                                            <option value="all">통합검색</option>
                                            <option value="a1.mt_id">아이디</option>
                                            <option value="a1.mt_name">이름</option>
                                            <option value="a1.qt_qtitle">문의 제목</option>
                                            <option value="a1.qt_qcontent">문의 내용</option>
                                            <option value="a1.qt_atitle">답변 제목</option>
                                            <option value="a1.qt_acontent">답변 내용</option>
                                        </select>
                                    </div>

                                    <div class="form-group mx-1">
                                        <input type="text" class="form-control form-control-sm" style="width:200px;" name="obj_search_txt" id="obj_search_txt" value="" />
                                    </div>

                                    <div class="form-group mx-1">
                                        <input type="button" class="btn btn-info" value="검색" onclick="f_get_box_mng_list()" />
                                    </div>

                                    <div class="form-group mx-1">
                                        <input type="button" class="btn btn-secondary" value="초기화" onclick="f_localStorage_reset_go('./qna_list');" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <script>
                    $(document).ready(function() {
                        f_get_box_mng_list();
                    });

                    function f_qna_content(qt_idx) {
                        $.post("./qna_update", {
                            act: "content_view",
                            qt_idx: qt_idx
                        }, function(data) {
                            if (data) {
                                $("#modal-default-content").html(data);
                                $("#modal-default").modal();
                            }
                        });

                        return false;
                    }

                    <?php if ($_POST['obj_sel_search']) { ?>
                    $('#obj_sel_search').val('<?=$_POST['obj_sel_search']?>');
                    <?php } ?>
                    <?php if ($_POST['sel_qt_status']) { ?>
                    $('#sel_qt_status').val('<?=$_POST['sel_qt_status']?>');
                    <?php } ?>
                    </script>

                    <div id="qna_list_box"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>