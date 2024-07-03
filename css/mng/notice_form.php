<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu = '4';
$chk_sub_menu = '4';
$chk_webeditor = 'Y';
include $_SERVER['DOCUMENT_ROOT']."/mng/head_menu.inc.php";

if ($_GET['act'] == "update") {
    $DB->where('nt_idx', $_GET['nt_idx']);
    $row = $DB->getone('notice_t');

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
                    <h4 class="card-title">공지사항<?=$_act_txt?></h4>

                    <form method="post" name="frm_form" id="frm_form" action="./notice_update" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="nt_idx" id="nt_idx" value="<?=$row['nt_idx']?>" />

                        <div class="form-group row">
                            <label for="nt_title" class="col-sm-2 col-form-label">제목 <b class="text-danger">*</b></label>
                            <div class="col-sm-10">
                                <input type="text" name="nt_title" id="nt_title" value="<?=$row['nt_title']?>" class="form-control form-control-sm" maxlength="100" />
                            </div>
                        </div>
                        <?php $editor_name = 'nt_content'; ?>
                        <div class="form-group row">
                            <label for="<?=$editor_name?>" class="col-sm-2 col-form-label">내용 <b class="text-danger">*</b></label>
                            <div class="col-sm-10">
                                <textarea name="<?=$editor_name?>" id="<?=$editor_name?>"><?=$row[$editor_name]?></textarea>
                                <script>
                                $(document).ready(function() {
                                    $('#<?=$editor_name?>').summernote({
                                        lang: 'ko-KR',
                                        height: 500,
                                        minHeight: null,
                                        maxHeight: null,
                                        placeholder: '내용을 입력바랍니다.',
                                        toolbar: [
                                            ['style', ['style']],
                                            ['font', ['bold', 'underline', 'clear']],
                                            ['color', ['color']],
                                            ['para', ['ul', 'ol', 'paragraph']],
                                            ['table', ['table']],
                                            ['insert', ['link', 'picture', 'video']],
                                            ['view', ['codeview']]
                                        ],
                                        callbacks: {
                                            onImageUpload: function(files) {
                                                var w = 1;
                                                for (var i = files.length - 1; i >= 0; i--) {
                                                    sendfile_summernote('<?=$editor_name?>', files[i], w, this);
                                                    w++;
                                                }
                                            },
                                            onPaste: function(e) {
                                                var clipboardData = e.originalEvent.clipboardData;
                                                if (clipboardData && clipboardData.items &&
                                                    clipboardData.items.length) {
                                                    var item = clipboardData.items[0];
                                                    if (item.kind === 'file' && item.type.indexOf('image/') !== -1) {
                                                        e.preventDefault();
                                                    }
                                                }
                                            },
                                            onImageLinkInsert: function(url) {
                                                $img = $('<img>').attr({
                                                    src: url
                                                })
                                                $('#<?=$editor_name?>').summernote('insertNode', $img[0]);
                                            }
                                        }
                                    });
                                });
                                </script>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nt_show" class="col-sm-2 col-form-label">노출여부</label>
                            <div class="col-sm-2">
                                <select name="nt_show" id="nt_show" class="form-control form-control-sm">
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
                    <?php if ($row['nt_show']) { ?>
                    $('#nt_show').val('<?=$row['nt_show']?>');
                    <?php } ?>

                    $("#frm_form").validate({
                        submitHandler: function() {
                            var f = document.frm_form;

                            if ($('#<?=$editor_name?>').summernote('isEmpty')) {
                                jalert("내용을 입력해주세요.", '', $('#<?=$editor_name?>').summernote('focus'));
                                return false;
                            }

                            $('#splinner_modal').modal('toggle');

                            return true;
                        },
                        rules: {
                            nt_title: {
                                required: true,
                                minlength: 2,
                                maxlength: 50
                            },
                        },
                        messages: {
                            nt_title: {
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