<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu = '2';
$chk_sub_menu = '1';
$chk_webeditor = 'Y';
include $_SERVER['DOCUMENT_ROOT']."/mng/head_menu.inc.php";

if ($_GET['act'] == "update") {
    $DB->where('sgt_idx', $_GET['sgt_idx']);
    $row = $DB->getone('smap_group_t');

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
                    <h4 class="card-title">그룹정보<?=$_act_txt?></h4>

                    <form method="post" name="frm_form" id="frm_form" action="./group_update" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="sgt_idx" id="sgt_idx" value="<?=$row['sgt_idx']?>" />

                        <div class="form-group row">
                            <label for="sgt_title" class="col-sm-2 col-form-label">그룹명 <b class="text-danger">*</b></label>
                            <div class="col-sm-5">
                                <input type="text" name="sgt_title" id="sgt_title" value="<?=$row['sgt_title']?>" class="form-control form-control-sm" maxlength="50" />
                                <small id="sgt_title_help" class="form-text text-muted">* 50자내외</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sgt_code" class="col-sm-2 col-form-label">초대코드</label>
                            <div class="col-sm-3">
                                <input type="text" name="sgt_code" id="sgt_code" value="<?=$row['sgt_code']?>" class="form-control-plaintext" readonly />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="nt_title" class="col-sm-2 col-form-label">그룹원</label>
                            <div class="col-sm-10">
                                <div class="row">
                                    <?php
                                        unset($list_gp);
$DB->where('sgt_idx', $row['sgt_idx']);
$DB->where('sgdt_discharge', 'N');
$DB->where('sgdt_exit', 'N');
$DB->orderBy("sgdt_owner_chk", "asc");
$DB->orderBy("sgdt_leader_chk", "asc");
$list_gp = $DB->get('smap_group_detail_t');

if($list_gp) {
    foreach($list_gp as $row_gp) {
        $DB->where('mt_idx', $row_gp['mt_idx']);
        $row_mt = $DB->getone('member_t');

        if($row_gp['sgdt_owner_chk'] == 'Y') {
            $owner_t = '오너';
        } else {
            if($row_gp['sgdt_leader_chk'] == 'Y') {
                $owner_t = '리더';
            } else {
                $owner_t = '<button type="button" class="btn btn-xs btn-info" onclick="f_group_leader(\''.$row_gp['sgt_idx'].'\', \''.$row_mt['mt_idx'].'\');">리더임명</button>';
            }
        }
        ?>
                                    <div class="col-md-4 grid-margin stretch-card">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-sm-flex flex-row flex-wrap text-center text-sm-left align-items-center">
                                                    <span class="avatar">
                                                        <img src="../../../../images/faces/face11.jpg" onerror="this.src='<?=$ct_no_profile_img_url?>'" class="img-lg border" alt="<?=$row_mt['mt_id']?>">
                                                    </span>
                                                    <div class="ml-sm-3 ml-md-0 ml-xl-3 mt-2 mt-sm-0 mt-md-2 mt-xl-0">
                                                        <h6 class="mb-0"><?=$row_mt['mt_id']?></h6>
                                                        <p class="text-muted mb-1"><?=$row_mt['mt_name']?> (<?=$row_mt['mt_nickname']?>)</p>
                                                        <p class="mb-0 text-success font-weight-bold"><?=$owner_t?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <?php
    }
}
?>
                                </div>
                            </div>
                        </div>

                        <p class="p-3 text-center">
                            <input type="submit" value="확인" class="btn btn-outline-primary" />
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
                            sgt_title: {
                                required: true,
                            },
                        },
                        messages: {
                            nt_title: {
                                required: "그룹명을 입력해주세요.",
                            },
                        },
                        errorPlacement: function(error, element) {
                            $(element)
                                .closest("form")
                                .find("span[for='" + element.attr("id") + "']")
                                .append(error);
                        },
                    });

                    function f_group_leader(g, i) {
                        $.confirm({
                            title: "주의",
                            content: "리더를 변경하시겠습니까?",
                            buttons: {
                                confirm: {
                                    text: "확인",
                                    action: function() {
                                        $.post(
                                            './group_update', {
                                                act: "chg_leader",
                                                sgt_idx: g,
                                                mt_idx: i,
                                            },
                                            function(data) {
                                                if (data == "Y") {
                                                    document.location.reload();
                                                }
                                            }
                                        );
                                    },
                                },
                                cancel: {
                                    text: "취소",
                                    action: function() {
                                        close();
                                    },
                                },
                            },
                        });

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