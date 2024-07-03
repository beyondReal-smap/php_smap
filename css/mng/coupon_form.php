<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head.inc.php";
$chk_menu = '6';
$chk_sub_menu = '3';
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head_menu.inc.php";

if ($_GET['act'] == "update") {
    $DB->where('ct_idx', $_GET['ct_idx']);
    $row = $DB->getone('coupon_t');

    $_act = "update";
    $_act_txt = "쿠폰수정";
} else {
    $_act = "input";
    $_act_txt = "쿠폰등록";
}
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><?= $_act_txt ?></h4>

                    <form method="post" name="frm_form" id="frm_form" action="./coupon_update" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?= $_act ?>" />
                        <input type="hidden" name="ct_idx" id="ct_idx" value="<?= $row['ct_idx'] ?>" />

                        <div class="form-group row">
                            <label for="ct_code" class="col-sm-3 col-form-label">쿠폰코드 <b class="text-danger">*</b></label>
                            <div class="col-sm-3">
                                <input type="text" name="ct_code" id="ct_code" value="<?php echo $row['ct_code']; ?>" class="form-control form-control-sm" readonly placeholder="쿠폰코드는 등록 후 자동생성됩니다." />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="ct_title" class="col-sm-3 col-form-label">쿠폰명 <b class="text-danger">*</b></label>
                            <div class="col-sm-6">
                                <input type="text" name="ct_title" id="ct_title" value="<?php echo $row['ct_title']; ?>" class="form-control form-control-sm" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="ct_days" class="col-sm-3 col-form-label">가용일 <b class="text-danger">*</b></label>
                            <div class="col-sm-6 input-group">
                                <select name="ct_days" id="ct_days" class="form-control form-control-sm col-sm-2">
                                    <option value="30" <?php if ($row['ct_days'] == "30") echo "selected"; ?>>1개월</option>
                                    <option value="60" <?php if ($row['ct_days'] == "60") echo "selected"; ?>>2개월</option>
                                    <option value="180" <?php if ($row['ct_days'] == "180") echo "selected"; ?>>6개월</option>
                                    <option value="365" <?php if ($row['ct_days'] == "365") echo "selected"; ?>>1년</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row align-items-center">
                            <label for="sel_search_sdate" class="col-sm-3 col-form-label">만료일시 <b class="text-danger">*</b></label>
                            <div class="col-sm-4 input-group">
                                <input type="hidden" name="sel_search_sdate" id="sel_search_sdate" value="<?= $row['ct_sdate'] ? $row['ct_sdate'] : date("Y-m-d"); ?>" class="form-control" readonly />
                                <input type="text" name="sel_search_edate" id="sel_search_edate" value="<?php echo $row['ct_edate']; ?>" class="form-control" readonly />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="ct_show" class="col-sm-3 col-form-label">노출여부</label>
                            <div class="col-sm-6 input-group">
                                <select name="ct_show" id="ct_show" class="form-control form-control-sm col-sm-2">
                                    <option value="Y" <?php if ($row['ct_show'] == "Y") echo "selected"; ?>>노출</option>
                                    <option value="N" <?php if ($row['ct_show'] == "N") echo "selected"; ?>>미노출</option>
                                </select>
                            </div>
                        </div>

                        <? if ($_act == 'update') { ?>
                            <div class="form-group row">
                                <label for="ct_use" class="col-sm-3 col-form-label">사용여부</label>
                                <div class="col-sm-6 input-group">
                                    <? if ($row['ct_use'] == 'Y') { ?>
                                        <input type="hidden" name="ct_use" id="ct_use" value="<?= $row['ct_use']?>">
                                        사용완료
                                    <? } else { ?>
                                        <select name="ct_use" id="ct_use" class="form-control form-control-sm col-sm-2">
                                            <option value="Y" <?php if ($row['ct_use'] == "Y") echo "selected"; ?>>사용</option>
                                            <option value="N" <?php if ($row['ct_use'] == "N") echo "selected"; ?>>미사용</option>
                                        </select>
                                    <? } ?>
                                </div>
                            </div>
                        <? } ?>

                        <div class="form-group row">
                            <label for="ct_subtitle" class="col-sm-3 col-form-label">비고</label>
                            <div class="col-sm-6">
                                <input type="text" name="ct_subtitle" id="ct_subtitle" value="<?php echo $row['ct_subtitle']; ?>" class="form-control form-control-sm" />
                            </div>
                        </div>
                        <?php if ($_act == 'update') : ?>
                            <div class="form-group row">
                                <label for="ct_wdate" class="col-sm-3 col-form-label">등록일시</label>
                                <div class="col-sm-6 input-group">
                                    <?php echo DateType($row['ct_wdate']); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <p class="p-3 text-center">
                            <input type="submit" value="완료" class="btn btn-outline-primary" />
                            <input type="button" value="목록" onclick="history.go(-1);" class="btn btn-outline-secondary mx-2" />
                        </p>

                    </form>
                    <script type="text/javascript">
                        $("#frm_form").validate({
                            submitHandler: function() {
                                var f = document.frm_form;

                                $('#splinner_modal').modal('toggle');

                                return true;
                            },
                            rules: {
                                ct_title: {
                                    required: true,
                                    minlength: 2
                                },
                                sel_search_sdate: {
                                    required: true,
                                    minlength: 2
                                },
                                sel_search_edate: {
                                    required: true,
                                    minlength: 2
                                },
                            },
                            messages: {
                                ct_title: {
                                    required: "쿠폰 이름을 입력해주세요.",
                                    minlength: "최소 {0}글자이상이어야 합니다",
                                },
                                sel_search_sdate: {
                                    required: "기간을 입력해주세요.",
                                    minlength: "최소 {0}글자이상이어야 합니다",
                                },
                                sel_search_edate: {
                                    required: "기간을 입력해주세요.",
                                    minlength: "최소 {0}글자이상이어야 합니다",
                                },
                            },
                            errorPlacement: function(error, element) {
                                $(element)
                                    .closest("form")
                                    .find("span[for='" + element.attr("id") + "']")
                                    .append(error);
                            },
                        });

                        (function($) {
                            'use strict';
                            $(function() {
                                jQuery.datetimepicker.setLocale('ko');

                                jQuery(function() {
                                    jQuery('#sel_search_sdate').datetimepicker({
                                        format: 'Y-m-d',
                                        onShow: function(ct) {
                                            this.setOptions({
                                                maxDate: jQuery('#sel_search_edate').val() ? jQuery('#sel_search_edate').val() : false
                                            })
                                        },
                                        timepicker: false
                                    });
                                    jQuery('#sel_search_edate').datetimepicker({
                                        format: 'Y-m-d',
                                        onShow: function(ct) {
                                            this.setOptions({
                                                minDate: jQuery('#sel_search_sdate').val() ? jQuery('#sel_search_sdate').val() : false
                                            })
                                        },
                                        timepicker: false
                                    });
                                });
                            });
                        })(jQuery);
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/foot.inc.php";
?>