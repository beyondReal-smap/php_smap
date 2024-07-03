<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu = '1';
$chk_sub_menu = '1';
include $_SERVER['DOCUMENT_ROOT']."/mng/head_menu.inc.php";
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">일반회원</h4>
                    <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
                        <input type="hidden" name="act" id="act" value="list" />
                        <input type="hidden" name="obj_list" id="obj_list" value="member_list_box" />
                        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
                        <input type="hidden" name="obj_uri" id="obj_uri" value="./member_update" />
                        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
                        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
                        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />

                        <div class="row no-gutters mb-2">
                            <div class="col-xl-3">
                            </div>
                            <div class="col-xl-9">
                                <div class="float-right form-inline">
                                    <div class="form-group mx-sm-1 d-none">
                                        <select name="obj_sel_mt_type" id="obj_sel_mt_type" class="form-control form-control-sm" onchange="f_get_box_mng_list();">
                                            <option value="all">로그인 전체</option>
                                            <?=$arr_mt_type_option?>
                                        </select>
                                    </div>

                                    <div class="form-group mx-sm-1">
                                        <select name="obj_sel_mt_status" id="obj_sel_mt_status" class="form-control form-control-sm" onchange="f_get_box_mng_list();">
                                            <option value="">회원상태</option>
                                            <?=$arr_mt_status_option?>
                                        </select>
                                    </div>

                                    <div class="form-group mx-sm-1">
                                        <select name="obj_sel_mt_level" id="obj_sel_mt_level" class="form-control form-control-sm" onchange="f_get_box_mng_list();">
                                            <option value="">회원등급</option>
                                            <?=$arr_mt_level_option?>
                                        </select>
                                    </div>

                                    <div class="form-group mx-sm-1">
                                        <select name="obj_sel_search" id="obj_sel_search" class="form-control form-control-sm">
                                            <option value="all">통합검색</option>
                                            <option value="a1.mt_id">아이디</option>
                                            <option value="a1.mt_name">이름</option>
                                            <option value="a1.mt_nickname">닉네임</option>
                                            <option value="a1.mt_hp">연락처</option>
                                        </select>
                                    </div>

                                    <div class="form-group mx-1">
                                        <input type="text" class="form-control form-control-sm" style="width:200px;" name="obj_search_txt" id="obj_search_txt" value="" />
                                    </div>

                                    <div class="form-group mx-1">
                                        <input type="button" class="btn btn-info" value="검색" onclick="f_get_box_mng_list();" />
                                    </div>

                                    <div class="form-group mx-1">
                                        <input type="button" class="btn btn-secondary" value="초기화" onclick="f_localStorage_reset_go('./member_list');" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <script>
                    $(document).ready(function() {
                        f_get_box_mng_list();
                    });

                    <?php if ($_POST['obj_sel_search']) { ?>
                    $('#obj_sel_search').val('<?=$_POST['obj_sel_search']?>');
                    <?php } ?>
                    <?php if ($_POST['obj_sel_mt_type']) { ?>
                    $('#obj_sel_mt_type').val('<?=$_POST['obj_sel_mt_type']?>');
                    <?php } ?>
                    <?php if ($_POST['obj_sel_mt_status']) { ?>
                    $('#obj_sel_mt_status').val('<?=$_POST['obj_sel_mt_status']?>');
                    <?php } ?>
                    <?php if ($_POST['obj_sel_mt_level']) { ?>
                    $('#obj_sel_mt_level').val('<?=$_POST['obj_sel_mt_level']?>');
                    <?php } ?>
                    </script>

                    <div id="member_list_box"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>