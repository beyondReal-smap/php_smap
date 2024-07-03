<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head.inc.php";
$chk_menu = '6';
$chk_sub_menu = '2';
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head_menu.inc.php";
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">추천인 입력내역</h4>
                    <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
                        <input type="hidden" name="act" id="act" value="list" />
                        <input type="hidden" name="obj_list" id="obj_list" value="recommend_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./recommend_update" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />

                        <div class="row no-gutters mb-2">
                            <div class="col-xl-0">
                            </div>
                            <div class="col-xl-12">
                                <div class="float-right form-inline">
                                    <div class="form-group mr-3 mx-1">
                                        <div class="btn-group">
                                            <button type="button" onclick="f_order_search_date_range('1', '<?= date('Y-m-d', strtotime("-2 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range1" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">3일</button>
                                            <button type="button" onclick="f_order_search_date_range('2', '<?= date('Y-m-d', strtotime("-4 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range2" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">5일</button>
                                            <button type="button" onclick="f_order_search_date_range('3', '<?= date('Y-m-d', strtotime("-6 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range3" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">7일</button>
                                            <button type="button" onclick="f_order_search_date_range('4', '<?= date('Y-m-d', strtotime("-14 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range4" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">15일</button>
                                            <button type="button" onclick="f_order_search_date_range('5', '<?= date('Y-m-d', strtotime("-29 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range5" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">30일</button>
                                            <button type="button" onclick="f_order_search_date_range('6', '<?= date('Y-m-d', strtotime("-59 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range6" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">60일</button>
                                            <button type="button" onclick="f_order_search_date_range('7', '<?= date('Y-m-d', strtotime("-89 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range7" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">90일</button>
                                            <button type="button" onclick="f_order_search_date_range('8', '<?= date('Y-m-d', strtotime("-119 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range8" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">120일</button>
                                        </div>
                                    </div>
                                    <div class="form-group mx-1">
                                        <div class="input-group">
                                            <input type="text" name="sel_search_sdate" id="sel_search_sdate" value="<?= $_GET['sel_search_sdate'] ?>" class="form-control form-control-sm" readonly /> <span class="m-2">~</span> <input type="text" name="sel_search_edate" id="sel_search_edate" value="<?= $_GET['sel_search_edate'] ?>" class="form-control form-control-sm" readonly />
                                        </div>
                                    </div>
                                    <div class="form-group mx-1">
                                        <select name="obj_sel_search" id="obj_sel_search" class="form-control form-control-sm">
                                            <option value="all">통합검색</option>
                                            <option value="a1.mt_id">추천자 아이디</option>
                                            <option value="a1.mt_name">추천자 이름</option>
                                            <option value="a1.rlt_mt_id">추천인 아이디</option>
                                            <option value="a1.rlt_mt_name">추천인 이름</option>
                                        </select>
                                    </div>

                                    <div class="form-group mx-1">
                                        <input type="text" class="form-control form-control-sm" style="width:200px;" name="obj_search_txt" id="obj_search_txt" value="" />
                                    </div>

                                    <div class="form-group mx-1">
                                        <input type="button" class="btn btn-info" value="검색" onclick="f_get_box_mng_list()" />
                                    </div>

                                    <div class="form-group mx-1">
                                        <input type="button" class="btn btn-secondary" value="초기화" onclick="f_localStorage_reset_go('./recommend_list');" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <script>
                        $(document).ready(function() {
                            f_get_box_mng_list();
                            f_order_search_date_range('3', '<?= date('Y-m-d', strtotime("-6 days")) ?>', '<?= date('Y-m-d') ?>');
                        });

                        jQuery(function() {
                            jQuery('#sel_search_sdate').datetimepicker({
                                format: 'Y-m-d',
                                onShow: function(ct) {
                                    this.setOptions({
                                        maxDate: jQuery(
                                                '#sel_search_edate')
                                            .val() ? jQuery(
                                                '#sel_search_edate')
                                            .val() : false
                                    })
                                },
                                timepicker: false
                            });
                            jQuery('#sel_search_edate').datetimepicker({
                                format: 'Y-m-d',
                                onShow: function(ct) {
                                    this.setOptions({
                                        minDate: jQuery(
                                                '#sel_search_sdate')
                                            .val() ? jQuery(
                                                '#sel_search_sdate')
                                            .val() : false
                                    })
                                },
                                timepicker: false
                            });
                        });


                        <?php if ($_POST['obj_sel_search']) { ?>
                            $('#obj_sel_search').val('<?= $_POST['obj_sel_search'] ?>');
                        <?php } ?>
                    </script>

                    <div id="recommend_list_box"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/foot.inc.php";
?>