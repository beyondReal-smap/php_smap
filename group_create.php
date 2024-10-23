<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '';
$h_menu = '6';
$h_url = './group';
$_SUB_HEAD_TITLE = $translations['txt_new_group'];
include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";
if ($_SESSION['_mt_idx'] == '') {
    alert($translations['txt_login_required'], './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert($translations['txt_login_another_device'], './logout');
    }
}
?>
<div class="container sub_pg">
    <div class="mt-4">
        <form method="post" name="frm_form" id="frm_form" action="./group_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="group_create" />
            <div class="mt-5">
                <div class="ip_wr mt_25">
                    <div class="ip_tit d-flex align-items-center justify-content-between">
                        <h5 class=""><?php echo $translations['txt_group_name']; ?></h5>
                        <p class="text_num fs_12 fc_gray_600">(<span id="sgt_title_cnt">0</span>/15)</p>
                    </div>
                    <input type="text" class="form-control txt-cnt" id="sgt_title" name="sgt_title" minlength="2" maxlength="15" data-length-id="sgt_title_cnt" oninput="maxLengthCheck(this)" placeholder="<?php echo $translations['txt_enter_group_name']; ?>">
                </div>
            </div>
            <div class="b_botton">
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block"><?php echo $translations['txt_save_new_group_name']; ?></button>
            </div>
        </form>
        <script>
        $(document).ready(function() {
            $(document).on("keyup", "input.txt-cnt", function() {
                var cnt_id = $(this).data('length-id');
                $('#' + cnt_id).text($(this).val().length);
            });
        });

        $.validator.addMethod("sgt_title_chk", function(value, element) {
            var rtn = false;

            $.ajax({
                url: './group_update',
                data: {
                    act: 'chk_sgt_title',
                    sgt_title: value
                },
                type: 'POST',
                async: false,
                success: function(args) {
                    args = $.trim(args);
                    rtn = (args === 'true');
                }
            });

            return rtn;
        });

        $("#frm_form").validate({
            submitHandler: function() {
                // $('#splinner_modal').modal('toggle');

                return true;
            },
            rules: {
                sgt_title: {
                    required: true,
                    sgt_title_chk: true
                },
            },
            messages: {
                sgt_title: {
                    required: "<?php echo $translations['txt_enter_group_name']; ?>",
                    sgt_title_chk: "<?php echo $translations['txt_duplicate_group_name']; ?>",
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
<?php
include $_SERVER['DOCUMENT_ROOT']."/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>