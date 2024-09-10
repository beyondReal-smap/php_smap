<?php
include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";
$b_menu = '';
$h_menu = '3';
$h_url = './setting';
$_SUB_HEAD_TITLE = translate("1:1문의", $userLang); // "1:1문의" 번역
include $_SERVER['DOCUMENT_ROOT']."/head.inc.php";
if ($_SESSION['_mt_idx'] == '') {
    alert(translate('로그인이 필요합니다.', $userLang), './login', ''); // "로그인이 필요합니다." 번역
} else {
    // 앱토큰값이 DB와 같은지 확인
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $mem_row = $DB->getone('member_t');
    if ($_SESSION['_mt_token_id'] != $mem_row['mt_token_id']) {
        alert(translate('다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다.', $userLang), './logout'); // "다른기기에서 로그인 시도 하였습니다. 다시 로그인 부탁드립니다." 번역
    }
}
?>
<div class="container sub_pg">
    <form method="post" name="frm_list" id="frm_list" onsubmit="return false;">
        <input type="hidden" name="act" id="act" value="list" />
        <input type="hidden" name="obj_list" id="obj_list" value="inquiry_list_box" />
        <input type="hidden" name="obj_frm" id="obj_frm" value="frm_list" />
        <input type="hidden" name="obj_uri" id="obj_uri" value="./inquiry_update" />
        <input type="hidden" name="obj_pg" id="obj_pg" value="1" />
        <input type="hidden" name="obj_orderby" id="obj_orderby" value="" />
        <input type="hidden" name="obj_order_desc_asc" id="obj_order_desc_asc" value="1" />
    </form>

    <script>
    $(document).ready(function() {
        f_get_box_list();
    });
    </script>

    <div id="inquiry_list_box"></div>

</div>
<?php
include $_SERVER['DOCUMENT_ROOT']."/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>