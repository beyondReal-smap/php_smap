<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$title = "";
$_GET['hd_num'] = '2';
$h_menu = '2';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>
<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3">약관에 동의해주세요.</p>
        <form role="form" method="post" name="frm_form" id="frm_form" action="./join_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="join_agree" />
            <div class="border-bottom mt-5">
                <div class="ip_wr pb-4">
                    <div class="checks_wr">
                        <div class="checks">
                            <label>
                                <input type="checkbox" id="mt_agree_all" name="mt_agree_all" onchange="f_checkbox_all(this,'mt_agree')" />
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p mt-0">
                                    <p class="fs_14 fw_700">전체 약관에 동의합니다.</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between pt_07">
                <div class="ip_wr py_07">
                    <div class="checks_wr">
                        <div class="checks">
                            <label>
                                <input type="checkbox" name="mt_agree[]" id="mt_agree1" value="1" onchange="f_checkbox_each('mt_agree')" />
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p mt-0">
                                    <p class="text_dynamic fs_14 text-text line_h1_3"><span class="text_gray">(필수)</span>서비스 이용약관</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <a href="https://schedulemap.notion.site/30b32b5ad0bc4f99a39b28c0fe5f1de4?pvs=4" target="_blank" class="py-3 pl-3 pr-2 cursor_pointer flex-shrink-0">
                    <img src="<?= CDN_HTTP ?>/img/ico_min_arrow_r.png" width="5px" alt="서비스 이용약관" />
                </a>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="ip_wr py_07">
                    <div class="checks_wr">
                        <div class="checks">
                            <label>
                                <input type="checkbox" name="mt_agree[]" id="mt_agree2" value="2" onchange="f_checkbox_each('mt_agree')" />
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p mt-0">
                                    <p class="text_dynamic fs_14 text-text line_h1_3"><span class="text_gray">(필수)</span>개인정보 처리방침</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <a href="https://schedulemap.notion.site/schedulemap/2ac62e02f97b4d61945d68c2d89109e9" target="_blank" class="py-3 pl-3 pr-2 cursor_pointer flex-shrink-0">
                    <img src="<?= CDN_HTTP ?>/img/ico_min_arrow_r.png" width="5px" alt="개인정보 처리방침" />
                </a>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="ip_wr py_07">
                    <div class="checks_wr">
                        <div class="checks">
                            <label>
                                <input type="checkbox" name="mt_agree[]" id="mt_agree3" value="3" onchange="f_checkbox_each('mt_agree')" />
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p mt-0">
                                    <p class="text_dynamic fs_14 text-text line_h1_3"><span class="text_gray">(필수)</span>위치기반서비스 이용약관</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <a href="https://schedulemap.notion.site/schedulemap/69cf94c6a04e471d8c3e3043f95baefb" target="_blank" class="py-3 pl-3 pr-2 cursor_pointer flex-shrink-0">
                    <img src="<?= CDN_HTTP ?>/img/ico_min_arrow_r.png" width="5px" alt="위치기반서비스 이용약관" />
                </a>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="ip_wr py_07">
                    <div class="checks_wr">
                        <div class="checks">
                            <label>
                                <input type="checkbox" name="mt_agree[]" id="mt_agree4" value="4" onchange="f_checkbox_each('mt_agree')" />
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p mt-0">
                                    <p class="text_dynamic fs_14 text-text line_h1_3"><span class="text_gray">(선택)</span>개인정보 제3자 제공 동의</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <a href="https://schedulemap.notion.site/schedulemap/3-21b302dcaba0490fbaa9430618a74f01" target="_blank" class="py-3 pl-3 pr-2 cursor_pointer flex-shrink-0">
                    <img src="<?= CDN_HTTP ?>/img/ico_min_arrow_r.png" width="5px" alt="개인정보 제3자 제공 동의" />
                </a>
            </div>
            <div class="d-flex align-items-center justify-content-between">
                <div class="ip_wr py_07">
                    <div class="checks_wr">
                        <div class="checks">
                            <label>
                                <input type="checkbox" name="mt_agree[]" id="mt_agree5" value="5" onchange="f_checkbox_each('mt_agree')" />
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p mt-0">
                                    <p class="text_dynamic fs_14 text-text line_h1_3"><span class="text_gray">(선택)</span>마케팅 정보 수집 및 이용 동의</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <a href="https://schedulemap.notion.site/schedulemap/7e35638d106f433f86fa95f88ba6efb1" target="_blank" class="py-3 pl-3 pr-2 cursor_pointer flex-shrink-0">
                    <img src="<?= CDN_HTTP ?>/img/ico_min_arrow_r.png" width="5px" alt="마케팅 정보 수집 및 이용 동의" />
                </a>
            </div>
            <div class="b_botton">
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block" disabled>동의했어요!</button>
            </div>
        </form>
        <script>
            function f_isValid() {
                if ($('#mt_agree1').prop("checked") == true && $('#mt_agree2').prop("checked") == true && $('#mt_agree3').prop("checked") == true) {
                    $('#frm_form button[type="submit"]').prop('disabled', false);
                } else {
                    $('#frm_form button[type="submit"]').prop('disabled', true);
                }
            }

            $(document).ready(function() {
               /*  <?php if ($_SESSION['_mt_agree_all'] == 'Y') { ?>
                    $('#mt_agree_all').prop("checked", true);
                <?php } ?>

                <?php
                for ($q = 1; $q < 6; $q++) {
                    if ($_SESSION['_mt_agree' . $q] == 'Y') {
                ?>
                        $('#mt_agree<?= $q ?>').prop("checked", true);
                <?php
                    }
                }
                ?> */
                setTimeout(() => {
                    f_isValid();
                }, 100);

                $('#frm_form input[type="checkbox"]').on('change', function() {
                    f_isValid();
                });
            });

            $('#frm_form').validate({
                submitHandler: function() {

                    // $('#splinner_modal').modal('toggle');

                    return true;
                },
                errorClass: 'errText',
                errorElement: "span",
                ignore: "",
                rules: {
                    mt_agree_all: {
                        required: false,
                    },
                },
                messages: {
                    mt_agree_all: {
                        required: "모든 약관에 동의해주세요.",
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
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>