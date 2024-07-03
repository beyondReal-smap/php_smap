<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu = '90';
$chk_sub_menu = '2';
include $_SERVER['DOCUMENT_ROOT']."/mng/head_menu.inc.php";

if ($_GET['act'] == "update") {
    $DB->where('mt_idx', $_GET['mt_idx']);
    $row = $DB->getone('member_t');

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
                    <h4 class="card-title">관리자설정<?=$_act_txt?></h4>

                    <form method="post" name="frm_form" id="frm_form" action="./member_admin_update" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="mt_idx" id="mt_idx" value="<?=$row['mt_idx']?>" />
                        <input type="hidden" name="mt_id_chk" id="mt_id_chk" value="Y" />

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label for="mt_id" class="col-sm-3 col-form-label">아이디 <b class="text-danger">*</b></label>
                                    <div class="col-sm-6 form-inline">
                                        <?php if($_act == 'input') { ?>
                                        <input type="text" name="mt_id" id="mt_id" value="<?=$row['mt_id']?>" class="form-control form-control-sm lower" minlength="5" maxlength="20" abcOnlySamll onchange="$('#mt_id_chk').val('N');" />
                                        <input type="button" class="btn btn-secondary ml-2" value="중복확인" id="mt_id_chk_btn" onclick="f_mt_id_chk();" />
                                        <small id="mt_id_help" class="form-text text-muted">5~20자 이하로 특수문자(영문 소문자만 가능) 없이 작성해주세요.</small>
                                        <?php } else { ?>
                                        <input type="text" name="mt_id" id="mt_id" value="<?=$row['mt_id']?>" class="form-control-plaintext" readonly />
                                        <?php } ?>
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
                                        <small id="mt_pwd_re_help" class="form-text text-muted">* 영문, 숫자, 특수문자 8~20자, 허용된 특수문자($@!%*#?&)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row align-items-center">
                                    <label for="mt_name" class="col-sm-3 col-form-label">이름 <b class="text-danger">*</b></label>
                                    <div class="col-sm-6">
                                        <input type="text" name="mt_name" id="mt_name" value="<?=$row['mt_name']?>" class="form-control form-control-sm" maxlength="20" />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="mt_wdate" class="col-sm-3 col-form-label">등록일시</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="mt_wdate" id="mt_wdate" value="<?=DateType($row['mt_wdate'], 6)?>" class="form-control-plaintext" readonly />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="mt_ldate" class="col-sm-3 col-form-label">접속일시</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="mt_ldate" id="mt_ldate" value="<?=DateType($row['mt_ldate'], 6)?>" class="form-control-plaintext" readonly />
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
                            </div>
                        </div>

                        <p class="p-3 text-center">
                            <input type="submit" value="확인" class="btn btn-outline-primary" />
                            <input type="button" value="목록" onclick="history.go(-1);" class="btn btn-outline-secondary mx-2" />
                            <input type="button" class="btn btn-outline-danger" value="삭제" onclick="f_post_del_admin_member('./member_admin_update', '<?=$row['mt_idx']?>');" />
                        </p>
                    </form>

                    <script type="text/javascript">
                    <?php if ($row['mt_status']) { ?>
                    $('#mt_status').val('<?=$row['mt_status']?>');
                    <?php } ?>

                    $("#mt_id").filter(".lower").on("keyup", function() {
                        $(this).val($(this).val().toLowerCase());
                    });

                    $.validator.addMethod("mt_id_chk", function(value, element) {
                        var rtn;

                        if (/[~!@#$%^&*()_+|<>?:;{}`\-\=\\\,.'"\[\]/]/gi.test(value)) {
                            rtn = false;
                        } else {
                            rtn = true;
                        }

                        return rtn;
                    });

                    function f_mt_id_chk() {
                        if ($('#mt_id').val() == '') {
                            jalert_focus("아이디를 입력해주세요.", '', 'mt_id');
                            return false;
                        }

                        $.post('./member_admin_update', {
                            act: "mt_id_chk",
                            mt_id: $('#mt_id').val()
                        }, function(data) {
                            if (data == "Y") {
                                $.alert({
                                    title: '',
                                    content: '사용 가능한 아이디입니다.',
                                    buttons: {
                                        confirm: {
                                            text: "확인",
                                            action: function() {
                                                $('#mt_id').attr('readonly', true);
                                                $('#mt_id_chk_btn').attr('disabled', true);
                                                $('#mt_id_chk').val('Y');
                                            },
                                        },
                                    },
                                });
                            } else {
                                $.alert({
                                    title: '',
                                    content: '중복된 아이디가 존재합니다. 확인바랍니다.',
                                    buttons: {
                                        confirm: {
                                            text: "확인",
                                            action: function() {
                                                $('#mt_id').attr('readonly', false);
                                                $('#mt_id_chk_btn').attr('disabled', false);
                                                $('#mt_id').val('');
                                                $('#mt_id_chk').val('N');
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

                            if (f.mt_id_chk.value != 'Y') {
                                jalert("닉네임 중복확인을 해주세요.", '', f.mt_id.focus());
                                return false;
                            }

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

                            $('#splinner_modal').modal('toggle');

                            return true;
                        },
                        rules: {
                            <?php if($_act == 'input') { ?>
                            mt_id: {
                                required: true,
                                mt_id_chk: true,
                            },
                            <?php } ?>
                            mt_name: {
                                required: true,
                                minlength: 2
                            },
                        },
                        messages: {
                            <?php if($_act == 'input') { ?>
                            mt_id: {
                                required: "아이디를 입력해주세요.",
                                minlength: "최소 {0}글자이상이어야 합니다",
                                mt_id_chk: "특수문자제외한 숫자, 영문으로 입력바랍니다.",
                            },
                            <?php } ?>
                            mt_name: {
                                required: "이름을 입력해주세요.",
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
include $_SERVER['DOCUMENT_ROOT']."/mng/foot.inc.php";
?>