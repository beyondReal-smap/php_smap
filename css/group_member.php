<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

if ($_SESSION['_mt_idx'] == '') {
    header('Content-Type: application/json');
    echo json_encode(['error' => '로그인이 필요합니다.']);
    exit;
}
$sgt_cnt = f_get_owner_cnt($_SESSION['_mt_idx']); //오너인 그룹수
$DB->where('sgdt_idx', $_POST['group_sgdt_idx']);
$sgdt_row = $DB->getone('smap_group_detail_t');

$DB->where('mt_idx', $_SESSION['_mt_idx']);
$DB->where('sgdt_discharge', 'N');
$DB->where('sgdt_exit', 'N');
$row_sgdt = $DB->getone('smap_group_detail_t', 'GROUP_CONCAT(sgt_idx) as gc_sgt_idx');

unset($list_sgt);
$DB->where("sgt_idx in (" . $_POST['gc_sgt_idx'] . ")");
$DB->where('sgt_show', 'Y');
$DB->orderBy("sgt_udate", "desc");
$DB->orderBy("sgt_idx", "asc");
$list_sgt = $DB->get('smap_group_t');

error_log("sgdt_idx: " . $_POST['gc_sgt_idx'] . "\n", 3, '/data/wwwroot/app2.smap.site/logfile.log');

$group_members = []; // 초기화

// 사용자 본인 정보 추가
$group_members[] = [
    'sgdt_idx' => $sgdt_row['sgdt_idx'],
    'mt_file1_url' => $_SESSION['_mt_file1'],
    'mt_nickname' => $_SESSION['_mt_nickname'] ? $_SESSION['_mt_nickname'] : $_SESSION['_mt_name']
];

if ($list_sgt) {
    foreach ($list_sgt as $row_sgt) {
        $list_sgdt = get_sgdt_member_list($row_sgt['sgt_idx']);
        // error_log("list_sgdt: " . print_r($list_sgdt['data'], true) . "\n", 3, '/data/wwwroot/app2.smap.site/logfile.log');
        if ($list_sgdt['data']) {
            foreach ($list_sgdt['data'] as $key => $val) {
               // 로그 출력
                error_log("sgdt_idx: " . $val['sgdt_idx'] . "\n", 3, '/data/wwwroot/app2.smap.site/logfile.log');
                error_log("mt_file1_url: " . $val['mt_file1_url'] . "\n", 3, '/data/wwwroot/app2.smap.site/logfile.log');
                error_log("mt_nickname: " . $val['mt_nickname'] . "\n", 3, '/data/wwwroot/app2.smap.site/logfile.log');
                $group_members[] = [
                    'sgdt_idx' => $val['sgdt_idx'],
                    'mt_file1_url' => $val['mt_file1_url'],
                    'mt_nickname' => $val['mt_nickname'] ? $val['mt_nickname'] : $val['mt_name']
                ];
            }
        }
    }
}

// 오류 처리
if ($DB->getLastError()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => '데이터베이스 오류가 발생했습니다.']);
    exit;
}

// 그룹원 정보를 JSON 형식으로 반환
header('Content-Type: application/json');
echo json_encode($group_members);
exit;
?>