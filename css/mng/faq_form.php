<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu = '4';
$chk_sub_menu = '2';
$chk_webeditor = 'Y';
include $_SERVER['DOCUMENT_ROOT']."/mng/head_menu.inc.php";

if ($_GET['act'] == "update") {
    $DB->where('ft_idx', $_GET['ft_idx']);
    $row = $DB->getone('faq_t');

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
                    <h4 class="card-title">FAQ<?=$_act_txt?></h4>

                    <form method="post" name="frm_form" id="frm_form" action="./faq_update" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="ft_idx" id="ft_idx" value="<?=$row['ft_idx']?>" />

                        <div class="form-group row">
                            <label for="fct_idx" class="col-sm-2 col-form-label">카테고리</label>
                            <div class="col-sm-2">
                                <select name="fct_idx" id="fct_idx" class="form-control form-control-sm">
                                    <option value="">선택</option>
                                    <?=get_sel_fct()?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="ft_title" class="col-sm-2 col-form-label">제목</label>
                            <div class="col-sm-10">
                                <input type="text" name="ft_title" id="ft_title" value="<?=$row['ft_title']?>" class="form-control form-control-sm" maxlength="100" />
                            </div>
                        </div>
                        <?php $editor_name = 'ft_content'; ?>
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
                            <label for="ft_rank" class="col-sm-2 col-form-label">노출순서</label>
                            <div class="col-sm-2">
                                <input type="text" name="ft_rank" id="ft_rank" value="<?=$row['ft_rank']?>" class="form-control form-control-sm" numberOnly maxlength="5" />
                                <small id="ft_rank_help" class="form-text text-muted">* 낮을수록 상위 노출됩니다.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="ft_show" class="col-sm-2 col-form-label">노출여부</label>
                            <div class="col-sm-2">
                                <select name="ft_show" id="ft_show" class="form-control form-control-sm">
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
                    <?php if ($row['fct_idx']) { ?>
                    $('#fct_idx').val('<?=$row['fct_idx']?>');
                    <?php } ?>
                    <?php if ($row['ft_show']) { ?>
                    $('#ft_show').val('<?=$row['ft_show']?>');
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
                            fct_idx: {
                                required: true,
                            },
                            ft_title: {
                                required: true,
                                minlength: 2,
                                maxlength: 100
                            },
                        },
                        messages: {
                            fct_idx: {
                                required: "카테고리를 선택해주세요.",
                            },
                            ft_title: {
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