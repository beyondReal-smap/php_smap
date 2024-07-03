<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head.inc.php";
$chk_menu = '1';
$chk_sub_menu = '1';
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head_menu.inc.php";

if ($_GET['act'] == "update") {
    $DB->where('mt_idx', $_GET['mt_idx']);
    $row = $DB->getone('member_t');

    $_act = "update";
    $_act_txt = " 수정";

    if ($row['mt_level'] == '1') {
        $_act_title = '탈퇴회원';
    } else {
        $_act_title = '일반회원';
    }
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
                    <h4 class="card-title"><?= $_act_title ?><?= $_act_txt ?></h4>

                    <form method="post" name="frm_form" id="frm_form" action="./member_update" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?= $_act ?>" />
                        <input type="hidden" name="mt_idx" id="mt_idx" value="<?= $row['mt_idx'] ?>" />
                        <input type="hidden" name="mt_nickname_chk" id="mt_nickname_chk" value="Y" />

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label for="mt_id" class="col-sm-3 col-form-label">아이디 <b class="text-danger">*</b></label>
                                    <div class="col-sm-6">
                                        <input type="text" name="mt_id" id="mt_id" value="<?= format_phone($row['mt_id']) ?>" class="form-control-plaintext" readonly />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="mt_pwd" class="col-sm-3 col-form-label">비밀번호 <b class="text-danger">*</b></label>
                                    <div class="col-sm-6">
                                        <input type="password" name="mt_pwd" id="mt_pwd" value="" autocomplete="one-time-code" class="form-control form-control-sm" minlength="8" maxlength="20" />
                                        <small id="mt_pwd_help" class="form-text text-muted">* 비밀번호 변경시에는 비밀번호 확인까지 입력바랍니다.</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="mt_pwd_re" class="col-sm-3 col-form-label">비밀번호 확인 <b class="text-danger">*</b></label>
                                    <div class="col-sm-6">
                                        <input type="password" name="mt_pwd_re" id="mt_pwd_re" value="" autocomplete="one-time-code" class="form-control form-control-sm" minlength="8" maxlength="20" />
                                        <small id="mt_pwd_re_help" class="form-text text-muted">* 영문, 숫자, 특수문자 8~20자, 허용된 특수문자($@$!%*#?&)</small>
                                    </div>
                                </div>

                                <div class="form-group row align-items-center">
                                    <label for="mt_name" class="col-sm-3 col-form-label">이름 <b class="text-danger">*</b></label>
                                    <div class="col-sm-6">
                                        <input type="text" name="mt_name" id="mt_name" value="<?= $row['mt_name'] ?>" class="form-control form-control-sm" maxlength="20" />
                                    </div>
                                </div>

                                <div class="form-group row align-items-center">
                                    <label for="mt_nickname" class="col-sm-3 col-form-label">닉네임 <b class="text-danger">*</b></label>
                                    <div class="col-sm-6 form-inline">
                                        <input type="text" name="mt_nickname" id="mt_nickname" value="<?= $row['mt_nickname'] ?>" class="form-control form-control-sm" maxlength="20" onchange="$('#mt_nickname_chk').val('N');" />
                                        <input type="button" class="btn btn-secondary ml-2" value="중복확인" id="mt_nickname_chk_btn" onclick="f_mt_nickname_chk();" />
                                    </div>
                                </div>

                                <div class="form-group row align-items-center">
                                    <label for="mt_email" class="col-sm-3 col-form-label">이메일 <b class="text-danger">*</b></label>
                                    <div class="col-sm-6">
                                        <input type="text" name="mt_email" id="mt_email" value="<?= $row['mt_email'] ?>" class="form-control form-control-sm" maxlength="50" />
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label for="mt_wdate" class="col-sm-3 col-form-label">등록일시</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="mt_wdate" id="mt_wdate" value="<?= DateType($row['mt_wdate'], 6) ?>" class="form-control-plaintext" readonly />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mt_ldate" class="col-sm-3 col-form-label">접속일시</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="mt_ldate" id="mt_ldate" value="<?= DateType($row['mt_ldate'], 6) ?>" class="form-control-plaintext" readonly />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mt_status" class="col-sm-3 col-form-label">로그인 상태</label>
                                    <div class="col-sm-2">
                                        <select name="mt_status" id="mt_status" class="form-control form-control-sm" onchange="f_status_chg(this.value, 'mt_status', '회원의 상태를 변경하시겠습니까?<br>정지 선택시, 해당 회원은 로그인이 불가합니다.');">
                                            <option value="1">정상</option>
                                            <option value="2">정지</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="mt_birth" class="col-sm-3 col-form-label">생년월일</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="mt_birth" id="mt_birth" value="<?= $row['mt_birth'] ?>" class="form-control form-control-sm" readonly />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="mt_gender" class="col-sm-3 col-form-label">성별</label>
                                    <div class="col-sm-2">
                                        <select name="mt_gender" id="mt_gender" class="form-control form-control-sm" onchange="f_status_chg(this.value, 'mt_gender', '회원의 성별을 변경하시겠습니까?');">
                                            <?= $arr_mt_gender_option ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="mt_level" class="col-sm-3 col-form-label">회원등급</label>
                                    <div class="col-sm-3">
                                        <select name="mt_level" id="mt_level" class="form-control form-control-sm" onchange="f_status_chg(this.value, 'mt_level', '회원의 등급을 변경하시겠습니까?<br/>일반이 아닌 등급은 로그인이 되지 않습니다.');">
                                            <?= $arr_mt_level_option ?>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label for="mt_plan_date" class="col-sm-3 col-form-label">유료플랜 기한</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="mt_plan_date" id="mt_plan_date" value="<?= $row['mt_plan_date'] ?>" class="form-control form-control-sm" readonly />
                                    </div>

                                </div>
                            </div>

                            <p class="p-3 text-center">
                                <input type="submit" value="확인" class="btn btn-outline-primary" />
                                <input type="button" value="목록" onclick="history.go(-1);" class="btn btn-outline-secondary mx-2" />
                                <?php if ($row['mt_level'] == '1') { ?>
                                    <!-- <input type="button" class="btn btn-outline-success" value="복구" onclick="f_retire_mem('<?= $row['mt_idx'] ?>');" /> -->
                                <?php } else { ?>
                                    <input type="button" class="btn btn-outline-danger" value="탈퇴" onclick="f_retire_mem('<?= $row['mt_idx'] ?>');" />
                                <?php } ?>
                            </p>
                    </form>

                    <script type="text/javascript">
                        <?php if ($row['mt_status']) { ?>
                            $('#mt_status').val('<?= $row['mt_status'] ?>');
                        <?php } ?>
                        <?php if ($row['mt_gender']) { ?>
                            $('#mt_gender').val('<?= $row['mt_gender'] ?>');
                        <?php } ?>
                        <?php if ($row['mt_level']) { ?>
                            $('#mt_level').val('<?= $row['mt_level'] ?>');
                        <?php } ?>

                        jQuery(function() {
                            jQuery('#mt_birth').datetimepicker({
                                format: 'Y-m-d',
                                timepicker: false
                            });
                            jQuery('#mt_plan_date').datetimepicker({
                                format: 'Y-m-d',
                                timepicker: true
                            });
                        });

                        function f_mt_nickname_chk() {
                            if ($('#mt_nickname').val() == '') {
                                jalert_focus("닉네임을 입력해주세요.", '', 'mt_id');
                                return false;
                            }

                            $.post('./member_update', {
                                act: "mt_nickname_chk",
                                mt_nickname: $('#mt_nickname').val()
                            }, function(data) {
                                if (data == "Y") {
                                    $.alert({
                                        title: '',
                                        content: '사용 가능한 아이디입니다.',
                                        buttons: {
                                            confirm: {
                                                text: "확인",
                                                action: function() {
                                                    $('#mt_nickname').attr('readonly', true);
                                                    $('#mt_nickname_chk_btn').attr('disabled', true);
                                                    $('#mt_nickname_chk').val('Y');

                                                },
                                            },
                                        },
                                    });
                                } else {
                                    $.alert({
                                        title: '',
                                        content: '중복된 닉네임이 존재합니다. 확인바랍니다.',
                                        buttons: {
                                            confirm: {
                                                text: "확인",
                                                action: function() {
                                                    $('#mt_nickname').attr('readonly', false);
                                                    $('#mt_nickname_chk_btn').attr('disabled', false);
                                                    $('#mt_nickname').val('');
                                                    $('#mt_nickname_chk').val('N');
                                                },
                                            },
                                        },
                                    });
                                }
                            });
                        }

                        $("#frm_form").validate({
                            submitHandler: function() {
                                var f = document.frm_form;

                                if (f.mt_pwd.value != "" && f.mt_pwd_re.value == "") {
                                    jalert("비밀번호 변경시 비밀번호 확인까지 입력해주세요.", '', f.mt_pwd.focus());
                                    return false;
                                }
                                if (f.mt_pwd.value == "" && f.mt_pwd_re.value != "") {
                                    jalert("비밀번호 변경시 비밀번호 확인까지 입력해주세요.", '', f.mt_pwd.focus());
                                    return false;
                                }
                                if (f.mt_pwd.value != "" && f.mt_pwd_re.value != "") {
                                    if (f.mt_pwd.value != f.mt_pwd_re.value) {
                                        jalert("비밀번호와 비밀번호확인이 동일하지 않습니다.", '', f.mt_pwd.focus());
                                        return false;
                                    }
                                }

                                if (f.mt_nickname_chk.value != 'Y') {
                                    jalert("닉네임 중복확인을 해주세요.", '', f.mt_nickname.focus());
                                    return false;
                                }

                                $('#splinner_modal').modal('toggle');

                                return true;
                            },
                            rules: {
                                mt_name: {
                                    required: true,
                                    minlength: 2
                                },
                                mt_nickname: {
                                    required: true,
                                    minlength: 2,
                                },
                            },
                            messages: {
                                mt_name: {
                                    required: "이름을 입력해주세요.",
                                    minlength: "최소 {0}글자이상이어야 합니다",
                                },
                                mt_nickname: {
                                    required: "닉네임을 입력해주세요.",
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
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/foot.inc.php";
?>