<?php
include $_SERVER['DOCUMENT_ROOT']."/mng/head.inc.php";
$chk_menu = '90';
$chk_sub_menu = '4';
include $_SERVER['DOCUMENT_ROOT']."/mng/head_menu.inc.php";

if ($_GET['act'] == "update") {
    $DB->where('pot_idx', $_GET['pot_idx']);
    $row = $DB->getone('popup_t');

    if($row['pot_file']) {
        unset($arr_images);
        unset($arr_images_json);

        $arr_images[1][]  = array(
            'name' => $row['pot_file_ori'],
            'url' => $ct_img_url.'/'.$row['pot_file'],
            'size' => $row['pot_file_size'],
            'pot_idx' => $row['pot_idx'],
        );

        $arr_images_json = json_encode($arr_images[1]);
    }

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
                    <h4 class="card-title">팝업관리<?=$_act_txt?></h4>

                    <form method="post" name="frm_form" id="frm_form" action="./banner_update" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?=$_act?>" />
                        <input type="hidden" name="pot_idx" id="pot_idx" value="<?=$row['pot_idx']?>" />

                        <div class="form-group row">
                            <label for="pot_title" class="col-sm-2 col-form-label">제목 <b class="text-danger">*</b></label>
                            <div class="col-sm-10">
                                <input type="text" name="pot_title" id="pot_title" value="<?=$row['pot_title']?>" class="form-control form-control-sm" maxlength="100" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pot_sdate" class="col-sm-2 col-form-label">노출기간 <b class="text-danger">*</b></label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="text" name="pot_sdate" id="pot_sdate" value="<?=DateType($row['pot_sdate'], 18)?>" class="form-control form-control-sm" readonly /> <span class="m-2">~</span> <input type="text" name="pot_edate" id="pot_edate" value="<?=DateType($row['pot_edate'], 18)?>" class="form-control form-control-sm" readonly />
                                </div>
                            </div>
                        </div>

                        <?php
$file_nm_t = 'pot_file';
$title_t = '이미지';
$multi_file_up_num = '1';
$multi_file_up_size = '10';
?>
                        <div class="form-group row <?=$file_nm_t?>_box">
                            <label for="<?=$file_nm_t?>_up" class="col-sm-2 col-form-label"><?=$title_t?> <b class="text-danger">*</b></label>
                            <div class="col-sm-10">
                                <button type="button" class="btn btn-dark btn-sm ml-auto <?=$file_nm_t?>_fileinput-button dz-clickable">파일첨부</button>
                                <button type="button" class="btn btn-outline-dark btn-sm ml-auto <?=$file_nm_t?>_fileinput-cancel">전체삭제</button>

                                <small id="<?=$file_nm_t?>_help" class="form-text text-muted">* 첨부파일은 <?=$multi_file_up_num?>개까지 가능하며 개당 <?=$multi_file_up_size?>MB를 넘을 수 없습니다.</small>

                                <ul id="<?=$file_nm_t?>_previews" class="list-unstyled mt-2 d-flex align-content-start flex-wrap">
                                    <li id="<?=$file_nm_t?>_template" class="media border p-2 mr-2 mb-2 col-5">
                                        <img data-dz-thumbnail class="align-self-start mr-3 dropzone-thumb" onerror="this.src='<?=$ct_no_img_url?>'">
                                        <div class="media-body p-2">
                                            <h5 class="mt-0 font-weight-bold" data-dz-name></h5>
                                            <p data-dz-size></p>
                                            <p class="error text-danger" data-dz-errormessage></p>
                                            <button type="button" class="btn btn-outline-danger btn-sm ml-auto" data-dz-remove>삭제</button>
                                        </div>
                                    </li>
                                </ul>

                                <script>
                                var <?=$file_nm_t?>_previewNode = document.querySelector("#<?=$file_nm_t?>_template");
                                <?=$file_nm_t?>_previewNode.id = "";
                                var <?=$file_nm_t?>_previewTemplate = <?=$file_nm_t?>_previewNode.parentNode.innerHTML;
                                <?=$file_nm_t?>_previewNode.parentNode.removeChild(<?=$file_nm_t?>_previewNode);

                                var <?=$file_nm_t?>_Dropzone = new Dropzone(".<?=$file_nm_t?>_box", {
                                    url: "file/post",
                                    autoProcessQueue: false,
                                    createImageThumbnails: true,
                                    parallelUploads: <?=$multi_file_up_num?>,
                                    maxFiles: <?=$multi_file_up_num?>,
                                    maxFilesize: <?=$multi_file_up_size?>,
                                    uploadMultiple: true,
                                    previewTemplate: <?=$file_nm_t?>_previewTemplate,
                                    previewsContainer: "#<?=$file_nm_t?>_previews",
                                    clickable: ".<?=$file_nm_t?>_fileinput-button",
                                    addRemoveLinks: false,
                                    acceptedFiles: '.jpeg,.jpg,.png,.gif,.JPEG,.JPG,.PNG,.GIF',
                                    dictDefaultMessage: "업로드할 파일을 여기에 드롭하세요.",
                                    dictFallbackMessage: "귀하의 브라우저는 드래그 앤 드롭 파일 업로드를 지원하지 않습니다.",
                                    dictFallbackText: "예전처럼 파일을 업로드하려면 아래 대체 양식을 사용하세요.",
                                    dictFileTooBig: "파일이 너무 큽니다. ({{filesize}}MiB). 최대 파일 크기: {{maxFilesize}}MiB.",
                                    dictInvalidFileType: "이 유형의 파일은 업로드할 수 없습니다.",
                                    dictResponseError: "서버가 {{statusCode}} 코드로 응답했습니다.",
                                    dictCancelUpload: "업로드 취소",
                                    dictCancelUploadConfirmation: "이 업로드를 취소하시겠습니까?",
                                    dictRemoveFile: "파일 삭제",
                                    dictMaxFilesExceeded: "더 이상 파일을 업로드할 수 없습니다.",
                                    init: function() {
                                        this.on("error", function(file) {
                                            this.removeFile(file);
                                        });
                                        this.on("removedfile", function(file) {
                                            if (file.pot_idx) {
                                                $.post(
                                                    './popup_update', {
                                                        act: 'delete_img',
                                                        pot_idx: file.pot_idx,
                                                    },
                                                    function(data) {
                                                        console.log(data);
                                                    }
                                                );
                                            }
                                        });
                                        this.on("addedfile", function(event) {
                                            while (this.files.length > this.options.maxFiles) {
                                                this.removeFile(this.files[0]);
                                            }
                                        });
                                    }
                                });

                                document.querySelector(".content-wrapper .<?=$file_nm_t?>_fileinput-cancel").onclick = function() {
                                    <?=$file_nm_t?>_Dropzone.removeAllFiles(true);
                                };

                                <?php if($arr_images_json) { ?>
                                var <?=$file_nm_t?>_images = <?=$arr_images_json?>;

                                for (let i = 0; i < <?=$file_nm_t?>_images.length; i++) {
                                    let img = <?=$file_nm_t?>_images[i];

                                    var mockFile = {
                                        name: img.name,
                                        size: img.size,
                                        url: img.url,
                                        pot_idx: img.pot_idx,
                                    };
                                    <?=$file_nm_t?>_Dropzone.emit("addedfile", mockFile);
                                    <?=$file_nm_t?>_Dropzone.emit("thumbnail", mockFile, img.url);
                                    <?=$file_nm_t?>_Dropzone.emit("complete", mockFile);
                                    <?=$file_nm_t?>_Dropzone.files.push(mockFile);
                                    <?php if($multi_file_up_num > 1) { ?>
                                    var existingFileCount = 1;
                                    <?=$file_nm_t?>_Dropzone.options.maxFiles = <?=$file_nm_t?>_Dropzone.options.maxFiles - existingFileCount;
                                    <?php } else { ?>
                                    <?=$file_nm_t?>_Dropzone.options.maxFiles = 1;
                                    <?php } ?>
                                }
                                <?php } ?>
                                </script>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="pot_target" class="col-sm-2 col-form-label">URL <b class="text-danger">*</b></label>
                            <div class="col-sm-10 form-inline">
                                <select name="pot_target" id="pot_target" class="form-control form-control-sm mr-3">
                                    <option value="1">새창</option>
                                    <option value="2">현재창</option>
                                </select>
                                <select name="pot_close" id="pot_close" class="form-control form-control-sm mr-3">
                                    <option value="1">오늘하루</option>
                                    <option value="2">다시열지않음</option>
                                </select>
                                <input type="text" name="pot_url" id="pot_url" value="<?=$row['pot_url']?>" class="form-control form-control-sm" maxlength="200" style="width:75%;" placeholder="http://, https:// 를 포함하여 입력바랍니다." />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="pot_show" class="col-sm-2 col-form-label">노출여부</label>
                            <div class="col-sm-2">
                                <select name="pot_show" id="pot_show" class="form-control form-control-sm">
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
                    <?php if ($row['pot_show']) { ?>
                    $('#pot_show').val('<?=$row['pot_show']?>');
                    <?php } ?>
                    <?php if ($row['pot_target']) { ?>
                    $('#pot_target').val('<?=$row['pot_target']?>');
                    <?php } ?>
                    <?php if ($row['pot_close']) { ?>
                    $('#pot_close').val('<?=$row['pot_close']?>');
                    <?php } ?>

                    jQuery(function() {
                        jQuery('#pot_sdate').datetimepicker({
                            minDate: 0,
                            format: 'Y-m-d H:i',
                            onShow: function(ct) {
                                this.setOptions({
                                    maxDate: jQuery(
                                            '#pot_edate')
                                        .val() ? jQuery(
                                            '#pot_edate')
                                        .val() : false
                                })
                            },
                            timepicker: true
                        });
                        jQuery('#pot_edate').datetimepicker({
                            minDate: 0,
                            format: 'Y-m-d H:i',
                            onShow: function(ct) {
                                this.setOptions({
                                    minDate: jQuery(
                                            '#pot_sdate')
                                        .val() ? jQuery(
                                            '#pot_sdate')
                                        .val() : false
                                })
                            },
                            timepicker: true
                        });
                    });

                    $("#frm_form").validate({
                        submitHandler: function() {
                            var f = $("#frm_form")[0];
                            var form_data = new FormData(f);

                            var file_arr = <?=$file_nm_t?>_Dropzone.getAcceptedFiles();
                            file_arr.forEach(function(files) {
                                form_data.append("file_arr1[]", files);
                            });

                            if (<?=$file_nm_t?>_Dropzone.files.length < 1) {
                                jalert("팝업 이미지를 첨부바랍니다.");
                                return false;
                            }

                            $('#splinner_modal').modal('toggle');

                            $.ajax({
                                data: form_data,
                                type: "POST",
                                enctype: "multipart/form-data",
                                url: "./popup_update",
                                cache: false,
                                timeout: 10000,
                                contentType: false,
                                processData: false,
                                success: function(data) {
                                    if (data == 'Y') {
                                        <?php if($_act != 'input') { ?>
                                        jalert_url('수정되었습니다.', 'reload');
                                        <?php } else { ?>
                                        jalert_url('등록되었습니다.', './popup_list');
                                        <?php } ?>
                                    }
                                },
                                error: function(err) {
                                    console.log(err);
                                },
                            });

                            return false;
                        },
                        rules: {
                            pot_title: {
                                required: true,
                            },
                            pot_sdate: {
                                required: true,
                            },
                            pot_edate: {
                                required: true,
                            },
                            pot_url: {
                                required: true,
                            },
                        },
                        messages: {
                            pot_title: {
                                required: "제목을 입력해주세요.",
                            },
                            pot_sdate: {
                                required: "노출 시작일을 입력해주세요.",
                            },
                            pot_edate: {
                                required: "노출 마감일을 입력해주세요.",
                            },
                            pot_url: {
                                required: "URL을 입력해주세요.",
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