<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head.inc.php";
$chk_menu = '90';
$chk_sub_menu = '1';
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head_menu.inc.php";

$DB->where('st_idx', '1');
$row = $DB->getone('setup_t');

$_act = "update";
$_act_txt = " 수정";
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">기본설정</h4>

                    <form method="post" name="frm_form" id="frm_form" action="./setup_update.php" target="hidden_ifrm">
                        <input type="hidden" name="act" id="act" value="<?= $_act ?>" />

                        <div class="form-group row">
                            <label for="st_agree1" class="col-sm-2 col-form-label">서비스 이용약관 노션링크</label>
                            <div class="col-sm-10">
                                <input type="text" name="st_agree1" id="st_agree1" value="<?= $row['st_agree1'] ?>" class="form-control form-control-sm" maxlength="255" />
                                <small id="mt_pwd_help" class="form-text text-muted">* http, https 를 포함하여 입력바랍니다.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="st_agree2" class="col-sm-2 col-form-label">개인정보취급방침 노션링크</label>
                            <div class="col-sm-10">
                                <input type="text" name="st_agree2" id="st_agree2" value="<?= $row['st_agree2'] ?>" class="form-control form-control-sm" maxlength="255" />
                                <small id="mt_pwd_help" class="form-text text-muted">* http, https 를 포함하여 입력바랍니다.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="st_agree3" class="col-sm-2 col-form-label">위치기반서비스 이용약관 노션링크</label>
                            <div class="col-sm-10">
                                <input type="text" name="st_agree3" id="st_agree3" value="<?= $row['st_agree3'] ?>" class="form-control form-control-sm" maxlength="255" />
                                <small id="mt_pwd_help" class="form-text text-muted">* http, https 를 포함하여 입력바랍니다.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="st_agree4" class="col-sm-2 col-form-label">개인정보 제3자 제공 노션링크</label>
                            <div class="col-sm-10">
                                <input type="text" name="st_agree4" id="st_agree4" value="<?= $row['st_agree4'] ?>" class="form-control form-control-sm" maxlength="255" />
                                <small id="mt_pwd_help" class="form-text text-muted">* http, https 를 포함하여 입력바랍니다.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="st_agree5" class="col-sm-2 col-form-label">마케팅정보수집 노션링크</label>
                            <div class="col-sm-10">
                                <input type="text" name="st_agree5" id="st_agree5" value="<?= $row['st_agree5'] ?>" class="form-control form-control-sm" maxlength="255" />
                                <small id="mt_pwd_help" class="form-text text-muted">* http, https 를 포함하여 입력바랍니다.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="st_agree6" class="col-sm-2 col-form-label">메뉴얼 노션링크</label>
                            <div class="col-sm-10">
                                <input type="text" name="st_agree6" id="st_agree6" value="<?= $row['st_agree6'] ?>" class="form-control form-control-sm" maxlength="255" />
                                <small id="mt_pwd_help" class="form-text text-muted">* http, https 를 포함하여 입력바랍니다.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="st_company_boss" class="col-sm-2 col-form-label">대표자명 <b class="text-danger">*</b></label>
                            <div class="col-sm-2">
                                <input type="text" name="st_company_boss" id="st_company_boss" value="<?= $row['st_company_boss'] ?>" class="form-control form-control-sm" maxlength="50" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="st_company_num1" class="col-sm-2 col-form-label">사업자등록번호 <b class="text-danger">*</b></label>
                            <div class="col-sm-3">
                                <input type="text" name="st_company_num1" id="st_company_num1" value="<?= $row['st_company_num1'] ?>" class="form-control form-control-sm" maxlength="20" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="st_company_num2" class="col-sm-2 col-form-label">통신판매신고번호</label>
                            <div class="col-sm-3">
                                <input type="text" name="st_company_num2" id="st_company_num2" value="<?= $row['st_company_num2'] ?>" class="form-control form-control-sm" maxlength="20" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="st_company_name" class="col-sm-2 col-form-label">회사명</label>
                            <div class="col-sm-3">
                                <input type="text" name="st_company_name" id="st_company_name" value="<?= $row['st_company_name'] ?>" class="form-control form-control-sm" maxlength="100" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="st_company_add1" class="col-sm-2 col-form-label">회사주소</label>
                            <div class="col-sm-10">
                                <input type="text" name="st_company_add1" id="st_company_add1" value="<?= $row['st_company_add1'] ?>" class="form-control form-control-sm" maxlength="200" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="st_privacy_admin" class="col-sm-2 col-form-label">개인정보책임관리자</label>
                            <div class="col-sm-2">
                                <input type="text" name="st_privacy_admin" id="st_privacy_admin" value="<?= $row['st_privacy_admin'] ?>" class="form-control form-control-sm" maxlength="50" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="st_customer_tel" class="col-sm-2 col-form-label">고객센터 전화</label>
                            <div class="col-sm-3">
                                <input type="text" name="st_customer_tel" id="st_customer_tel" value="<?= $row['st_customer_tel'] ?>" class="form-control form-control-sm" maxlength="20" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="st_customer_email" class="col-sm-2 col-form-label">고객센터 이메일</label>
                            <div class="col-sm-3">
                                <input type="text" name="st_customer_email" id="st_customer_email" value="<?= $row['st_customer_email'] ?>" class="form-control form-control-sm" maxlength="100" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="st_customer_time" class="col-sm-2 col-form-label">고객센터 운영시간</label>
                            <div class="col-sm-6">
                                <input type="text" name="st_customer_time" id="st_customer_time" value="<?= $row['st_customer_time'] ?>" class="form-control form-control-sm" maxlength="100" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="st_app_version_aos" class="col-sm-2 col-form-label">앱버전 안드로이드</label>
                            <div class="col-sm-2">
                                <input type="text" name="st_app_version_aos" id="st_app_version_aos" value="<?= $row['st_app_version_aos'] ?>" class="form-control form-control-sm" maxlength="10" />
                            </div>
                            <label for="st_app_version_aos_chk" class="col-sm-2 col-form-label">앱업데이트 강제여부</label>
                            <div class="col-sm-2">
                                <select name="st_app_version_aos_chk" id="st_app_version_aos_chk" class="form-control form-control-sm">
                                    <option value="Y">강제</option>
                                    <option value="N">선택</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="st_app_version_ios" class="col-sm-2 col-form-label">앱버전 아이폰</label>
                            <div class="col-sm-2">
                                <input type="text" name="st_app_version_ios" id="st_app_version_ios" value="<?= $row['st_app_version_ios'] ?>" class="form-control form-control-sm" maxlength="10" />
                            </div>
                            <label for="st_app_version_ios_chk" class="col-sm-2 col-form-label">앱업데이트 강제여부</label>
                            <div class="col-sm-2">
                                <select name="st_app_version_ios_chk" id="st_app_version_ios_chk" class="form-control form-control-sm">
                                    <option value="Y">강제</option>
                                    <option value="N">선택</option>
                                </select>
                            </div>
                        </div>

                        <p class="p-3 text-center">
                            <input type="submit" value="확인" class="btn btn-outline-primary" />
                        </p>
                    </form>
                    <script type="text/javascript">
                        <?php if ($row['st_app_version_aos_chk']) { ?>
                            $('#st_app_version_aos_chk').val('<?= $row['st_app_version_aos_chk'] ?>');
                        <?php } ?>
                        <?php if ($row['st_app_version_aos_chk']) { ?>
                            $('#st_app_version_aos_chk').val('<?= $row['st_app_version_aos_chk'] ?>');
                        <?php } ?>

                        $("#frm_form").validate({
                            submitHandler: function() {
                                var f = $("#frm_form")[0];
                                var form_data = new FormData(f);

                                $('#splinner_modal').modal('toggle');

                                return true;
                            },
                            rules: {
                                st_company_boss: {
                                    required: true,
                                },
                                st_company_num1: {
                                    required: true,
                                },
                            },
                            messages: {
                                st_company_boss: {
                                    required: "대표자명을 입력바랍니다.",
                                },
                                st_company_num1: {
                                    required: "사업자등록번호를 입력바랍니다.",
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