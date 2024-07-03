<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '';
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

$mt_idx = '98';

if ($mt_idx) {
    $DB->where('mt_idx', $mt_idx);
} else {
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
}
$DB->where('sgt_show', 'Y');
$row = $DB->getone('smap_group_t', 'count(*) as cnt');

$sgt_cnt = $row['cnt'];
// 내가 오너일 경우 나의 회원레벨 확인 후 그룹 정보 수정하기
if ($sgt_cnt > 0) {
    if ($mt_idx) {
        $DB->where('mt_idx', $mt_idx);
    } else {
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
    }
    $DB->where('mt_show', 'Y');
    $mem_row = $DB->getone('member_t');
    if ($mem_row['mt_level'] == 2) {
        if ($mt_idx) {
            $DB->where('mt_idx', $mt_idx);
        } else {
            $DB->where('mt_idx', $_SESSION['_mt_idx']);
        }
        $DB->where('sgt_show', 'Y');
        $row = $DB->getone('smap_group_t');

        $DB->where('sgt_idx', $row['sgt_idx']);
        $DB->where('sgdt_owner_chk', 'N'); // 오너가아니고
        $DB->where('sgdt_discharge', 'N'); // 방출안당하고
        $DB->where('sgdt_exit', 'N'); // 그룹안나가고
        $DB->where('sgdt_show', 'Y'); // 보여지는 상태
        $DB->orderby('sgdt_wdate', 'asc'); // 오래된 순서
        $sgdt_list = $DB->get('smap_group_detail_t');
        // 가져온 그룹원의 수가 4명 이상인 경우
        if (count($sgdt_list) >= 4) {
            // 첫 4명은 그대로 두고, 나머지 그룹원들을 방출 처리
            for ($i = 4; $i < count($sgdt_list); $i++) {
                unset($arr_query);
                $arr_query = array(
                    "sgdt_discharge" => 'Y',
                    "sgdt_exit" => 'Y',
                    "sgdt_show" => 'N',
                    "sgdt_xdate" => $DB->now(),
                    "sgdt_ddate" => $DB->now(),
                    "sgdt_udate" => $DB->now(),
                );

                $DB->where('sgdt_idx', $sgdt_list[$i]['sgdt_idx']);
                $DB->update('smap_group_detail_t', $arr_query);


                unset($arr_query);
                $arr_query = array(
                    "slt_show" => 'N',
                    "slt_ddate" => $DB->now(),
                    "slt_udate" => $DB->now(),
                );

                $DB->where('sgdt_idx', $sgdt_list[$i]['sgdt_idx']);
                $DB->update('smap_location_t', $arr_query);

                unset($arr_query);
                $arr_query = array(
                    "sst_show" => 'N',
                    "sst_ddate" => $DB->now(),
                    "sst_udate" => $DB->now(),
                );

                $DB->where('sgdt_idx', $sgdt_list[$i]['sgdt_idx']);
                $DB->update('smap_schedule_t', $arr_query);
            }
        }
    }
}