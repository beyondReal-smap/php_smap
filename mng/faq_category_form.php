<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu = '4';
$chk_sub_menu = '3';
include $_SERVER['DOCUMENT_ROOT']."/mng/head_menu.inc.php";

if ($_GET['act'] == "update") {
    $DB->where('fct_idx', $_GET['fct_idx']);
    $row = $DB->getone('faq_category_t');

    $_act = "update";
    $_act_txt = " 수정";
} else {
    $_act = "input";
    $_act_txt = " 등록";
}
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">FAQ 카테고리<?=$_act_txt?></h4>

                    <form method="post" name="frm_form" id="frm_form" action="./faq_category_update" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="fct_idx" id="fct_idx" value="<?=$row['fct_idx']?>" />

                        <div class="form-group row">
                            <label for="fct_name" class="col-sm-2 col-form-label">카테고리명</label>
                            <div class="col-sm-10">
                                <input type="text" name="fct_name" id="fct_name" value="<?=$row['fct_name']?>" class="form-control form-control-sm" maxlength="100" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="fct_rank" class="col-sm-2 col-form-label">노출순서</label>
                            <div class="col-sm-2">
                                <input type="text" name="fct_rank" id="fct_rank" value="<?=$row['fct_rank']?>" class="form-control form-control-sm" numberOnly maxlength="5" />
                                <small id="fct_rank_help" class="form-text text-muted">* 낮을수록 상위 노출됩니다.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="fct_show" class="col-sm-2 col-form-label">노출여부</label>
                            <div class="col-sm-2">
                                <select name="fct_show" id="fct_show" class="form-control form-control-sm">
                                    <option value="Y">Y</option>
                                    <option value="N">N</option>
                                </select>
                            </div>
                        </div>

                        <p class="p-3 text-center">
                            <input type="submit" value="확인" class="btn btn-outline-primary" />
                            <input type="button" value="목록" onclick="history.go(-1);" class="btn btn-outline-secondary mx-2" />
                        </p>

                    </form>
                    <script type="text/javascript">
                    <?php if ($row['fct_show']) { ?>
                    $('#fct_show').val('<?=$row['fct_show']?>');
                    <?php } ?>

                    $("#frm_form").validate({
                        submitHandler: function() {
                            var f = document.frm_form;

                            $('#splinner_modal').modal('toggle');

                            return true;
                        },
                        rules: {
                            fct_name: {
                                required: true,
                                minlength: 2,
                                maxlength: 100
                            },
                        },
                        messages: {
                            fct_name: {
                                required: "제목을 입력해주세요.",
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