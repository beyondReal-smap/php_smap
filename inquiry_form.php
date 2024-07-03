<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '';
$h_menu = '6';
$h_url = './inquiry';
$_SUB_HEAD_TITLE = "문의 작성";
include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";
?>
<div class="container sub_pg">
    <form method="post" name="frm_form" id="frm_form" action="./inquiry_update" target="hidden_ifrm" enctype="multipart/form-data">
        <input type="hidden" name="firstname" id="firstname" value="" />
        <input type="hidden" name="act" id="act" value="input" />
        <div class="mt-4">
            <div class="ip_wr mt_25">
                <div class="ip_tit d-flex align-items-center justify-content-between">
                    <h5 class="">문의 제목</h5>
                    <p class="text_num fs_12 fc_gray_600">(<span id="qt_qtitle_cnt">0</span>/50)</p>
                </div>
                <input type="text" class="form-control txt-cnt" name="qt_qtitle" id="qt_qtitle" maxlength="50" data-length-id="qt_qtitle_cnt" oninput="maxLengthCheck(this)" placeholder="문의 제목을 입력해주세요">
            </div>
            <div class="ip_wr mt_25">
                <div class="ip_tit d-flex align-items-center justify-content-between">
                    <h5 class="">문의 제목</h5>
                    <p class="text_num fs_12 fc_gray_600">(<span id="qt_qcontent_cnt">0</span>/200)</p>
                </div>
                <textarea class="form-control txt-cnt" name="qt_qcontent" id="qt_qcontent" maxlength="200" data-length-id="qt_qcontent_cnt" oninput="maxLengthCheck(this)" placeholder="문의 내용을 입력해 주세요. 폭언 욕설 등의 문의글은 통보 없이 삭제 될 수 있습니다." rows="15"></textarea>
            </div>
        </div>
        <div class="b_botton">
            <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block" id="ToastBtn">등록하기</button>
        </div>
    </form>

    <script>
    $(document).ready(function() {
        $(document).on("keyup", "#qt_qtitle.txt-cnt", function() {
            var cnt_id = $(this).data('length-id');
            $('#' + cnt_id).text($(this).val().length);
        });
        $(document).on("keyup", "#qt_qcontent.txt-cnt", function() {
            var cnt_id = $(this).data('length-id');
            $('#' + cnt_id).text($(this).val().length);
        });
    });

    $("#frm_form").validate({
        submitHandler: function() {
            var f = document.frm_login;

            // $('#splinner_modal').modal('toggle');

            return true;
        },
        rules: {
            qt_qtitle: {
                required: true,
                minlength: 5,
            },
            qt_qcontent: {
                required: true,
                minlength: 10,
            },
        },
        messages: {
            qt_qtitle: {
                required: "문의 제목을 입력해주세요.",
            },
            qt_qcontent: {
                required: "문의 내용을 입력해주세요.",
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

<!-- 토스트 Toast -->
<div id="Toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-body">
        <p><i class="xi-check-circle mr-2"></i>문의가 등록되었습니다.</p>
        <!-- <p><i class="xi-error mr-2"></i>에러메시지</p> -->
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>