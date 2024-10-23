<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

// 파라미터 저장
$mt_pass = $_GET['mt_pass'];
$mt_gender = $_GET['mt_gender'];
$pick_year = $_GET['pick_year'];
$pick_month = $_GET['pick_month'];
$pick_day = $_GET['pick_day'];
$mt_name = $_GET['mt_name'];
$mt_email = $_GET['mt_email'];
$mt_hp = $_GET['mt_hp'];

$title = "";
$h_menu = '7';
$h_func = "back_confirm()";
// $h_menu = '2';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>
<div class="modal fade" id="back_confirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center"><?= $translations['txt_leave_signup_confirm'] ?></p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" onclick="$.ajax({url: './join_update', type: 'POST', data: {act: 'join_delete'}, success: function() {location.replace('./join_entry?phoneNumber=<?= $_GET['phoneNumber'] ?>&mt_email=<?= $_GET['mt_email'] ?>&mt_idx=<?= $_SESSION['_mt_idx'] ?>&mt_gender=<?= $mt_gender ?>&pick_year=<?= $pick_year ?>&pick_month=<?= $pick_month ?>&pick_day=<?= $pick_day ?>&mt_name=<?= $mt_name ?>');}})"><?= $translations['txt_yes'] ?></button>
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" data-dismiss="modal" aria-label="Close"><?= $translations['txt_no'] ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container sub_pg">
    <div class="mt-4">
        <p class="tit_h1 wh_pre line_h1_3"><?= $translations['txt_agree_to_terms']; ?></p>
        <form role="form" method="post" name="frm_form" id="frm_form" action="./join_update" target="hidden_ifrm" enctype="multipart/form-data">
            <input type="hidden" name="firstname" id="firstname" value="" />
            <input type="hidden" name="act" id="act" value="join_agree" />
            <input type="hidden" id="mt_idx" name="mt_idx" value="<?= $_SESSION['_mt_idx'] ?>">
            <!-- 추가된 hidden input fields -->
            <input type="hidden" name="mt_gender" value="<?= $mt_gender ?>" />
            <input type="hidden" name="pick_year" value="<?= $pick_year ?>" />
            <input type="hidden" name="pick_month" value="<?= $pick_month ?>" />
            <input type="hidden" name="pick_day" value="<?= $pick_day ?>" />
            <input type="hidden" name="mt_name" value="<?= $mt_name ?>" />
            <input type="hidden" name="mt_email" value="<?= $mt_email ?>" />
            <input type="hidden" name="mt_hp" value="<?= $mt_hp ?>" />
            <input type="hidden" name="mt_pass" value="<?= $mt_pass ?>" />
            <div class="border-bottom mt-5">
                <div class="ip_wr pb-4">
                    <div class="checks_wr">
                        <div class="checks">
                            <label>
                                <input type="checkbox" id="mt_agree_all" name="mt_agree_all" onchange="f_checkbox_all(this,'mt_agree')" />
                                <span class="ic_box"><i class="xi-check-min"></i></span>
                                <div class="chk_p mt-0">
                                    <p class="fs_14 fw_700"><?= $translations['txt_agree_to_all_terms']; ?></p>
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
                                    <p class="text_dynamic fs_14 text-text line_h1_3"><span class="text_gray">(<?= $translations['txt_required']; ?>)</span><?= $translations['txt_terms_of_service']; ?></p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <a href="https://schedulemap.notion.site/30b32b5ad0bc4f99a39b28c0fe5f1de4?pvs=4" target="_blank" class="py-3 pl-3 pr-2 cursor_pointer flex-shrink-0">
                    <img src="<?= CDN_HTTP ?>/img/ico_min_arrow_r.png" width="5px" alt="<?= $translations['txt_terms_of_service']; ?>" /> <!-- "서비스 이용약관" 번역 -->
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
                                    <p class="text_dynamic fs_14 text-text line_h1_3"><span class="text_gray">(<?= $translations['txt_required']; ?>)</span><?= $translations['txt_privacy_policy']; ?></p> <!-- "필수", "개인정보 처리방침" 번역 -->
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <a href="https://schedulemap.notion.site/schedulemap/2ac62e02f97b4d61945d68c2d89109e9" target="_blank" class="py-3 pl-3 pr-2 cursor_pointer flex-shrink-0">
                    <img src="<?= CDN_HTTP ?>/img/ico_min_arrow_r.png" width="5px" alt="<?= $translations['txt_privacy_policy']; ?>" /> <!-- "개인정보 처리방침" 번역 -->
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
                                    <p class="text_dynamic fs_14 text-text line_h1_3"><span class="text_gray">(<?= $translations['txt_required']; ?>)</span><?= $translations['txt_location_based_service_terms']; ?></p> <!-- "필수", "위치기반서비스 이용약관" 번역 -->
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <a href="https://schedulemap.notion.site/schedulemap/69cf94c6a04e471d8c3e3043f95baefb" target="_blank" class="py-3 pl-3 pr-2 cursor_pointer flex-shrink-0">
                    <img src="<?= CDN_HTTP ?>/img/ico_min_arrow_r.png" width="5px" alt="<?= $translations['txt_location_based_service_terms']; ?>" /> <!-- "위치기반서비스 이용약관" 번역 -->
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
                                    <p class="text_dynamic fs_14 text-text line_h1_3"><span class="text_gray">(<?= $translations['txt_optional']; ?>)</span><?= $translations['txt_third_party_info_disclosure']; ?></p> <!-- "선택", "개인정보 제3자 제공 동의" 번역 -->
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <a href="https://schedulemap.notion.site/schedulemap/3-21b302dcaba0490fbaa9430618a74f01" target="_blank" class="py-3 pl-3 pr-2 cursor_pointer flex-shrink-0">
                    <img src="<?= CDN_HTTP ?>/img/ico_min_arrow_r.png" width="5px" alt="<?= $translations['txt_third_party_info_disclosure']; ?>" /> <!-- "개인정보 제3자 제공 동의" 번역 -->
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
                                    <p class="text_dynamic fs_14 text-text line_h1_3"><span class="text_gray">(<?= $translations['txt_optional']; ?>)</span><?= $translations['txt_marketing_info_collection']; ?></p> <!-- "선택", "마케팅 정보 수집 및 이용 동의" 번역 -->
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <a href="https://schedulemap.notion.site/schedulemap/7e35638d106f433f86fa95f88ba6efb1" target="_blank" class="py-3 pl-3 pr-2 cursor_pointer flex-shrink-0">
                    <img src="<?= CDN_HTTP ?>/img/ico_min_arrow_r.png" width="5px" alt="<?= $translations['txt_marketing_info_collection']; ?>" /> <!-- "마케팅 정보 수집 및 이용 동의" 번역 -->
                </a>
            </div>
            <div class="b_botton">
                <button type="submit" class="btn w-100 rounded btn-primary btn-lg btn-block" disabled><?= $translations['txt_i_agree']; ?></button>
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
                setTimeout(() => {
                    f_isValid();
                }, 100);

                $('#frm_form input[type="checkbox"]').on('change', function() {
                    f_isValid();
                });
                console.log('mt_email : ' + '<?= $mt_email ?>' + ' mt_hp : ' + '<?= $mt_hp ?>' + ' mt_pass : ' + '<?= $mt_pass ?>' + ' mt_gender : ' + '<?= $mt_gender ?>' + ' pick_year : ' + '<?= $pick_year ?>' + ' pick_month : ' + '<?= $pick_month ?>' + ' pick_day : ' + '<?= $pick_day ?>' + ' mt_name :' + '<?= $mt_name ?>');
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
                        required: "<?= $translations['txt_agree_to_all_terms_please']; ?>",
                    },
                },
                errorPlacement: function(error, element) {
                    $(element)
                        .closest("form")
                        .find("span[for='" + element.attr("id") + "']")
                        .append(error);
                },
            });

            function back_confirm() {

                // 중복된 이메일이 존재하는 경우 모달 표시
                $('#back_confirm').modal('show');
            }
        </script>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>