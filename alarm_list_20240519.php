<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '6';
$h_url = './';
$_SUB_HEAD_TITLE = "알림";
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    alert('로그인이 필요합니다.', './login', '');
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', './logout');
    }
}

//읽지않은 알림 읽음 처리
unset($arr_query);
$arr_query = array(
    "plt_read_chk" => 'Y',
    "plt_rdate" => $DB->now(),
);

$DB->where('plt_read_chk', 'N');
$DB->where('mt_idx', $_SESSION['_mt_idx']);

$DB->update('push_log_t', $arr_query);
?>
<div class="container sub_pg">
    <div class="">
        <div class="text-right mt-3 mb-4">
            <button type="button" class="btn h_fit_im fs_13 fc_gray_600 px-0" data-toggle="modal" data-target="#arm_delete_modal"><i class="xi-trash-o mr_04"></i>전체삭제</button>
        </div>
        <?php
        unset($list);
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
        $DB->where('plt_status', '2');
        $DB->where('plt_show', 'Y');
        $DB->groupBy("left(plt_sdate, 10)");
        $DB->orderBy("plt_sdate", "desc");
        $list = $DB->get('push_log_t');

        if ($list) {
            foreach ($list as $row) {
                $plt_sdate_t = substr($row['plt_sdate'], 0, 10);
                if ($plt_sdate_t == date('Y-m-d')) {
                    $tt = ' <span class="text-primary ml-2">오늘 알림</span>';
                } else {
                    $tt = '';
                }
        ?>
                <div class="mb_24">
                    <p class="fs_16 fw_600"><?= DateType($row['plt_sdate'], 3) ?><?= $tt ?></p>
                    <div class="mt-4">
                        <?php
                        unset($list2);
                        $DB->where('mt_idx', $_SESSION['_mt_idx']);
                        $DB->where('plt_status', '2');
                        $DB->where('plt_show', 'Y');
                        $DB->where("plt_sdate between '" . $plt_sdate_t . " 00:00:00' and '" . $plt_sdate_t . " 23:59:59'");
                        $DB->orderBy("plt_sdate", "desc");
                        $list2 = $DB->get('push_log_t');

                        if ($list2) {
                            foreach ($list2 as $row2) {
                                $mt_info = get_member_t_info($row2['plt_mt_idx']);
                                $mt_file1_url = get_image_url($mt_info['mt_file1']);
                        ?>
                                <div class="py_09">
                                    <a href="#" class="d-flex align-items-center">
                                        <div class="prd_img flex-shrink-0 mr_16">
                                            <div class="rect_square border_opacity_50 rounded-pill">
                                                <img src="<?= $mt_file1_url ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="프로필이미지" />
                                            </div>
                                        </div>
                                        <!-- <div class="d-flex align-items-center flex-wrap"> -->
                                        <div class="align-items-center flex-wrap">
                                            <p class="fs_15 text_dynamic line_h1_2 mr_08"><span class="fw_900"><?= $row2['plt_title'] ?></p>
                                            <!-- <p class="fs_14 text_dynamic line_h1_2 mr_08"><span class="fw_700"><?= $mt_info['mt_nickname'] ?></span> 님이 <?= $row2['plt_content'] ?></p> -->
                                            <p class="fs_13 text_dynamic line_h1_2 mr_08"><span class="fw_700"><?= $row2['plt_content'] ?></p>
                                            <p class="fs_12 text_light_gray line_h1_2"><?= get_date_ttime($row2['plt_sdate']) ?></p>
                                        </div>
                                    </a>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
        <?php
            }
        }
        ?>
    </div>
</div>

<!-- D-5 알림 목록 -->
<div class="modal fade" id="arm_delete_modal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pt_40 pb_27 px-3 ">
                <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center">전체 삭제하시겠습니까?</p>
            </div>
            <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                <div class="d-flex align-items-center w-100 mx-0 my-0">
                    <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" onclick="f_del_alarm();">네</button>
                    <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" data-dismiss="modal" aria-label="Close">아니요</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 토스트 Toast 토스트 넣어두었습니다. 필요하시면 사용하심됩니다.! 사용할 버튼에 id="ToastBtn" 넣으면 사용가능! -->
<div id="Toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-body">
        <p><i class="xi-check-circle mr-2"></i>삭제되었습니다.</p> <!-- 성공메시지 -->
        <!-- <p><i class="xi-error mr-2"></i>에러메시지</p> -->
    </div>
</div>

<script>
    function f_del_alarm() {
        $('#arm_delete_modal').modal('hide');

        var form_data = new FormData();
        form_data.append("act", "delete_all");

        $.ajax({
            url: "./alarm_update",
            enctype: "multipart/form-data",
            data: form_data,
            type: "POST",
            async: true,
            contentType: false,
            processData: false,
            cache: true,
            timeout: 5000,
            success: function(data) {
                if (data == 'Y') {
                    $.alert({
                        title: '',
                        type: "blue",
                        typeAnimated: true,
                        content: '삭제되었습니다.',
                        buttons: {
                            confirm: {
                                btnClass: "btn-default btn-lg btn-block",
                                text: "확인",
                                action: function() {
                                    location.hash = "";
                                    location.reload();
                                },
                            },
                        },
                    });
                } else {
                    console.log(data);
                }
            },
            error: function(err) {
                console.log(err);
            },
        });
    }
</script>

<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>
