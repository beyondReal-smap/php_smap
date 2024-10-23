<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '';
$h_menu = '6';
$h_url = './notice';
$_SUB_HEAD_TITLE = "공지사항 상세";
include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";

if($_GET['nt_idx']) {
    $DB->where('nt_idx', $_GET['nt_idx']);
    $row = $DB->getone('notice_t');

    if($_COOKIE['nt_idx_chk']!=$row['nt_idx']) {
        unset($arr_query);
        $arr_query = array(
            "nt_hit" => ($row['nt_hit']+1),
        );

        $DB->where('nt_idx', $row['nt_idx']);

        $DB->update('notice_t', $arr_query);

        setcookie('nt_idx_chk', $row['nt_idx'], time()+3600);
    }
} else {
    alert('잘못된 접근입니다.', './');
}
?>
<div class="container sub_pg px_16">
    <div class="mt-4">
        <div class="pb-4 border-bottom text-center">
            <p class="fs_16 fw_600 text_dynamic line_h1_3"><?=$row['nt_title']?></p>
            <p class="text_light_gray fs_13 fw_300 mt_08"><?=DateType($row['nt_wdate'], 6)?></p>
        </div>
        <div class="py-3">
            <p class="fs_15 text_dynamic"><?=$row['nt_content']?></p>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>