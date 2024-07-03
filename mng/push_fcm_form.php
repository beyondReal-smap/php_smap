<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head.inc.php";
$chk_menu = '3';
$chk_sub_menu = '1';
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head_menu.inc.php";

if ($_GET['act'] == "update") {
    $DB->where('pft_idx', $_GET['pft_idx']);
    $row = $DB->getone('push_fcm_t');

    if ($row['pft_file1']) {
        unset($arr_images);
        unset($arr_images_json);

        $arr_images[1][]  = array(
            'name' => $row['pft_file1_ori'],
            'url' => $ct_img_url . '/' . $row['pft_file1'],
            'size' => $row['pft_file1_size'],
            'pft_idx' => $row['pft_idx'],
        );

        $arr_images_json = json_encode($arr_images[1]);
    }

    if ($row['pft_send_mt_idx']) {
        $pft_send_mt_idx_group_json = '';
        $pft_send_mt_idx_mem_json = '';
        if ($row['pft_send_type'] == '3') {
            $pft_send_mt_idx_group_json = json_decode($row['pft_send_mt_idx'], true);
        } elseif ($row['pft_send_type'] == '2') {
            $pft_send_mt_idx_mem_json = json_decode($row['pft_send_mt_idx'], true);
        }
    }

    $_act = "update";
    $_act_txt = " 수정";
} else {
    $_act = "input";
    $_act_txt = " 등록";
    $row['pft_status'] = '1';
}
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ko.js"></script>

<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">푸시관리<?= $_act_txt ?></h4>

                    <form method="post" name="frm_form" id="frm_form" action="./push_fcm_update" target="hidden_ifrm" enctype="multipart/form-data">
                        <input type="hidden" name="act" id="act" value="<?= $_act ?>" />
                        <input type="hidden" name="pft_idx" id="pft_idx" value="<?= $row['pft_idx'] ?>" />

                        <div class="form-group row">
                            <label for="pft_send_type" class="col-sm-2 col-form-label">대상 <b class="text-danger">*</b></label>
                            <div class="col-sm-10">
                                <select name="pft_send_type" id="pft_send_type" class="form-control form-control-sm" style="width:120px;">
                                    <option value="">대상</option>
                                    <?= $arr_pft_send_type_option ?>
                                </select>

                                <div class="mt-3 d-none-temp" id="pft_send_type_group_box">
                                    <select name="pft_gt_idx[]" id="pft_gt_idx" class="form-control form-control-sm" style="width:100%;" multiple="multiple">
                                        <option>그룹명을 입력바랍니다.</option>
                                        <?php
                                        if ($pft_send_mt_idx_group_json) {
                                            foreach ($pft_send_mt_idx_group_json as $key => $val) {
                                                $DB->where('sgt_idx', $val);
                                                $row_gt = $DB->getone('smap_group_t');
                                        ?>
                                                <option value="<?= $row_gt['sgt_idx'] ?>" selected><?= $row_gt['sgt_title'] ?> (<?= $row_gt['sgt_code'] ?>)</option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mt-3 d-none-temp" id="pft_send_type_mem_box">
                                    <select name="pft_mt_idx[]" id="pft_mt_idx" class="form-control form-control-sm" style="width:100%;" multiple="multiple">
                                        <option>아이디 또는 이름을 입력바랍니다.</option>
                                        <?php
                                        if ($pft_send_mt_idx_mem_json) {
                                            foreach ($pft_send_mt_idx_mem_json as $key => $val) {
                                                $DB->where('mt_idx', $val);
                                                $row_mt = $DB->getone('member_t');
                                        ?>
                                                <option value="<?= $row_mt['mt_idx'] ?>" selected><?= $row_mt['mt_name'] ?> (<?= $row_mt['mt_id'] ?>)</option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <script>
                            $("#pft_send_type").on("change", function() {
                                var v = $(this).val();

                                $('#pft_gt_idx').val(null).trigger('change');
                                $('#pft_mt_idx').val(null).trigger('change');

                                f_pft_send_type_chg(v);

                                return false;
                            });

                            function f_pft_send_type_chg(v) {
                                if (v == '2') {
                                    $('#pft_send_type_mem_box').show();
                                    $('#pft_send_type_group_box').hide();

                                } else if (v == '3') {
                                    $('#pft_send_type_mem_box').hide();
                                    $('#pft_send_type_group_box').show();
                                } else {
                                    $('#pft_send_type_mem_box').hide();
                                    $('#pft_send_type_group_box').hide();
                                }
                            }

                            $('#pft_gt_idx').select2({
                                ajax: {
                                    url: './select2_update',
                                    type: "POST",
                                    dataType: 'json',
                                    data: function(params) {
                                        var query = {
                                            act: 'group_t',
                                            obj_search_txt: params.term
                                        }

                                        return query;
                                    },
                                    processResults: function(data) {
                                        return {
                                            results: data
                                        };
                                    },
                                },
                                minimumInputLength: 1,
                                theme: 'bootstrap4',
                                language: "ko",
                                placeholder: "그룹명을 입력해주세요."
                            });

                            $('#pft_mt_idx').select2({
                                ajax: {
                                    url: './select2_update',
                                    type: "POST",
                                    dataType: 'json',
                                    data: function(params) {
                                        var query = {
                                            act: 'member_t',
                                            obj_search_txt: params.term
                                        }

                                        return query;
                                    },
                                    processResults: function(data) {
                                        return {
                                            results: data
                                        };
                                    },
                                },
                                minimumInputLength: 1,
                                theme: 'bootstrap4',
                                language: "ko",
                                placeholder: "아이디 또는 이름을 입력바랍니다."
                            });
                        </script>

                        <div class="form-group row">
                            <label for="pft_title" class="col-sm-2 col-form-label">제목 <b class="text-danger">*</b></label>
                            <div class="col-sm-5">
                                <input type="text" name="pft_title" id="pft_title" value="<?= $row['pft_title'] ?>" class="form-control form-control-sm" maxlength="40" />
                                <small id="pft_title_help" class="form-text text-muted">* 40자이하로 작성바랍니다.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="pft_content" class="col-sm-2 col-form-label">내용 <b class="text-danger">*</b></label>
                            <div class="col-sm-8">
                                <input type="text" name="pft_content" id="pft_content" value="<?= $row['pft_content'] ?>" class="form-control form-control-sm" maxlength="140" />
                                <small id="pft_content_help" class="form-text text-muted">* 140자이하로 작성바랍니다.</small>
                            </div>
                        </div>

                        <?php
                        // $file_nm_t = 'pft_file1';
                        // $title_t = '이미지';
                        // $multi_file_up_num = '1';
                        // $multi_file_up_size = '1';
                        ?> <!-- 
                        <div class="form-group row <?= $file_nm_t ?>_box">
                            <label for="<?= $file_nm_t ?>_up" class="col-sm-2 col-form-label"><?= $title_t ?></label>
                            <div class="col-sm-10">
                                <button type="button" class="btn btn-dark btn-sm ml-auto <?= $file_nm_t ?>_fileinput-button dz-clickable">파일첨부</button>
                                <button type="button" class="btn btn-outline-dark btn-sm ml-auto <?= $file_nm_t ?>_fileinput-cancel">전체삭제</button>

                                <small id="<?= $file_nm_t ?>_help" class="form-text text-muted">* 첨부파일은 <?= $multi_file_up_num ?>개까지 가능하며 개당 <?= $multi_file_up_size ?>MB를 넘을 수 없습니다. (500px이하의 정사각형 이미지를 업로드바랍니다.)</small>

                                <ul id="<?= $file_nm_t ?>_previews" class="list-unstyled mt-2 d-flex align-content-start flex-wrap">
                                    <li id="<?= $file_nm_t ?>_template" class="media border p-2 mr-2 mb-2 col-5">
                                        <img data-dz-thumbnail class="align-self-start mr-3 dropzone-thumb" onerror="this.src='<?= $ct_no_img_url ?>'">
                                        <div class="media-body p-2">
                                            <h5 class="mt-0 font-weight-bold" data-dz-name></h5>
                                            <p data-dz-size></p>
                                            <p class="error text-danger" data-dz-errormessage></p>
                                            <button type="button" class="btn btn-outline-danger btn-sm ml-auto" data-dz-remove>삭제</button>
                                        </div>
                                    </li>
                                </ul>

                                <script>
                                var <?= $file_nm_t ?>_previewNode = document.querySelector("#<?= $file_nm_t ?>_template");
                                <?= $file_nm_t ?>_previewNode.id = "";
                                var <?= $file_nm_t ?>_previewTemplate = <?= $file_nm_t ?>_previewNode.parentNode.innerHTML;
                                <?= $file_nm_t ?>_previewNode.parentNode.removeChild(<?= $file_nm_t ?>_previewNode);

                                var <?= $file_nm_t ?>_Dropzone = new Dropzone(".<?= $file_nm_t ?>_box", {
                                    url: "file/post",
                                    autoProcessQueue: false,
                                    createImageThumbnails: true,
                                    parallelUploads: <?= $multi_file_up_num ?>,
                                    maxFiles: <?= $multi_file_up_num ?>,
                                    maxFilesize: <?= $multi_file_up_size ?>,
                                    uploadMultiple: true,
                                    previewTemplate: <?= $file_nm_t ?>_previewTemplate,
                                    previewsContainer: "#<?= $file_nm_t ?>_previews",
                                    clickable: ".<?= $file_nm_t ?>_fileinput-button",
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
                                            if (file.bt_idx) {
                                                $.post(
                                                    './banner_update', {
                                                        act: 'delete_img',
                                                        bt_idx: file.bt_idx,
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

                                document.querySelector(".content-wrapper .<?= $file_nm_t ?>_fileinput-cancel").onclick = function() {
                                    <?= $file_nm_t ?>_Dropzone.removeAllFiles(true);
                                };

                                <?php if ($arr_images_json) { ?>
                                var <?= $file_nm_t ?>_images = <?= $arr_images_json ?>;

                                for (let i = 0; i < <?= $file_nm_t ?>_images.length; i++) {
                                    let img = <?= $file_nm_t ?>_images[i];

                                    var mockFile = {
                                        name: img.name,
                                        size: img.size,
                                        url: img.url,
                                        bt_idx: img.bt_idx,
                                    };
                                    <?= $file_nm_t ?>_Dropzone.emit("addedfile", mockFile);
                                    <?= $file_nm_t ?>_Dropzone.emit("thumbnail", mockFile, img.url);
                                    <?= $file_nm_t ?>_Dropzone.emit("complete", mockFile);
                                    <?= $file_nm_t ?>_Dropzone.files.push(mockFile);
                                    <?php if ($multi_file_up_num > 1) { ?>
                                    var existingFileCount = 1;
                                    <?= $file_nm_t ?>_Dropzone.options.maxFiles = <?= $file_nm_t ?>_Dropzone.options.maxFiles - existingFileCount;
                                    <?php } else { ?>
                                    <?= $file_nm_t ?>_Dropzone.options.maxFiles = 1;
                                    <?php } ?>
                                }
                                <?php } ?>
                                </script>
                            </div>
                        </div> -->

                        <div class="form-group row">
                            <label for="pft_rdate" class="col-sm-2 col-form-label">발송예약일시 <b class="text-danger">*</b></label>
                            <div class="col-sm-3">
                                <input type="text" name="pft_rdate" id="pft_rdate" value="<?= $row['pft_rdate'] ?>" class="form-control form-control-sm" readonly />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="pft_url" class="col-sm-2 col-form-label">링크</label>
                            <div class="col-sm-10">
                                <input type="text" name="pft_url" id="pft_url" value="<?= $row['pft_url'] ?>" class="form-control form-control-sm" maxlength="100" />
                                <small id="pft_url_help" class="form-text text-muted">* http, https 를 포함하는 링크를 입력바랍니다.</small>
                            </div>
                        </div>

                        <?php if ($_act == 'update' && $row['pft_status'] != '1') { ?>
                            <div class="form-group row">
                                <label for="pft_sdate" class="col-sm-2 col-form-label">발송시작일시 <b class="text-danger">*</b></label>
                                <div class="col-sm-10">
                                    <?= DateType($row['pft_sdate'], 6) ?>
                                </div>
                                <label for="pft_edate" class="col-sm-2 col-form-label">발송종료일시</label>
                                <div class="col-sm-10">
                                    <?= DateType($row['pft_edate'], 6) ?>
                                </div>
                            </div>
                        <?php } ?>

                        <p class="p-3 text-center">
                            <?php if ($row['pft_status'] == '1') { ?>
                                <input type="submit" value="확인" class="btn btn-outline-primary" />
                            <?php } ?>
                            <input type="button" value="목록" onclick="history.go(-1);" class="btn btn-outline-secondary mx-2" />
                        </p>

                    </form>
                    <script type="text/javascript">
                        <?php if ($row['pft_send_type']) { ?>
                            $('#pft_send_type').val('<?= $row['pft_send_type'] ?>');
                            f_pft_send_type_chg('<?= $row['pft_send_type'] ?>');
                        <?php } ?>

                        jQuery('#pft_rdate').datetimepicker({
                            format: 'Y-m-d H:i',
                            timepicker: true
                        });

                        $(document).ready(function() {
                            $("#pft_rdate").change(function() {
                                var selectedDate = $(this).val();
                                var currentDate = new Date();
                                var selectedDateTime = new Date(selectedDate);

                                if (selectedDateTime < currentDate) {
                                    alert("발송예약일시는 이전 시간을 선택할 수 없습니다.");
                                    $('#pft_rdate').val('');
                                }
                            });
                        });
                        $("#frm_form").validate({
                            submitHandler: function() {
                                var f = $("#frm_form")[0];
                                var form_data = new FormData(f);

                                /* 
                                var file_arr = <?= $file_nm_t ?>_Dropzone.getAcceptedFiles();
                                file_arr.forEach(function(files) {
                                    form_data.append("file_arr1[]", files);
                                });

                                if (<?= $file_nm_t ?>_Dropzone.files.length < 1) {
                                    jalert("배너 이미지를 첨부바랍니다.");
                                    return false;
                                } 
                                */

                                $('#splinner_modal').modal('toggle');

                                $.ajax({
                                    data: form_data,
                                    type: "POST",
                                    enctype: "multipart/form-data",
                                    url: "./push_fcm_update",
                                    cache: false,
                                    timeout: 10000,
                                    contentType: false,
                                    processData: false,
                                    success: function(data) {
                                        if (data == 'Y') {
                                            <?php if ($_act != 'input') { ?>
                                                jalert_url('수정되었습니다.', 'reload');
                                            <?php } else { ?>
                                                jalert_url('등록되었습니다.', './push_fcm_list');
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
                                pft_send_type: {
                                    required: true,
                                },
                                pft_title: {
                                    required: true,
                                },
                                pft_content: {
                                    required: true,
                                },
                                pft_rdate: {
                                    required: true,
                                },
                            },
                            messages: {
                                pft_send_type: {
                                    required: "대상을 선택해주세요.",
                                },
                                pft_title: {
                                    required: "제목을 입력해주세요.",
                                },
                                pft_content: {
                                    required: "내용을 입력해주세요.",
                                },
                                pft_rdate: {
                                    required: "발송예약일시를 선택해주세요.",
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