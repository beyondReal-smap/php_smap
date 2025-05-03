<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";

$sgdt_idx = isset($_POST['sgdt_idx']) ? intval($_POST['sgdt_idx']) : 0;
$mt_idx = null;

if ($sgdt_idx > 0) {
    $DB->where('sgdt_idx', $sgdt_idx);
    $sgdt_row = $DB->getOne('smap_group_detail_t', 'mt_idx');
    if ($sgdt_row) {
        $mt_idx = $sgdt_row['mt_idx'];
    }
}

header('Content-Type: application/json');
echo json_encode(['mt_idx' => $mt_idx]);
?> 