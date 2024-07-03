<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "update") {
    if($_POST['sst_idx'] == "") {
        p_alert("잘못된 접근입니다. sst_idx", 'back');
    }
    if($_POST['mt_idx'] == "") {
        p_alert("잘못된 접근입니다. mt_idx", 'back');
    }
    if($_POST['sst_title'] == "") {
        p_alert("잘못된 접근입니다. sst_title", 'back');
    }
    if($_POST['sgt_idx'] == "") {
        p_alert("잘못된 접근입니다. sgt_idx", 'back');
    }
    if($_POST['sgdt_idx'] == "") {
        p_alert("잘못된 접근입니다. sgdt_idx", 'back');
    }
    if($_POST['sst_location_add'] == "") {
        p_alert("잘못된 접근입니다. sst_location_add", 'back');
    }

    if($_POST['sst_repeat_json']) {
        $arr_sst_repeat = array();

        $arr_arr_sst_repeat['r1'] = $_POST['sst_repeat_json'];

        if($_POST['sst_repeat_json'] == '3') {
            if($_POST['sst_repeat_json_week']) {
                $arr_arr_sst_repeat['r2'] = implode(',', $_POST['sst_repeat_json_week']);
            } else {
                $arr_arr_sst_repeat['r2'] = '';
            }
        } else {
            $arr_arr_sst_repeat['r2'] = '';
        }

        $_POST['sst_repeat_json'] = json_encode($arr_arr_sst_repeat);
    }

    unset($arr_query);
    $arr_query = array(
        "mt_idx" => $_POST['mt_idx'],
        "sst_title" => $_POST['sst_title'],
        "sst_sdate" => $_POST['sst_sdate'],
        "sst_edate" => $_POST['sst_edate'],
        "sst_all_day" => $_POST['sst_all_day'],
        "sst_repeat_json" => $_POST['sst_repeat_json'],
        "sgt_idx" => $_POST['sgt_idx'],
        "sgdt_idx" => $_POST['sgdt_idx'],
        "sst_alram" => $_POST['sst_alram'],
        "slt_idx" => $_POST['slt_idx'],
        "sst_location_title" => $_POST['sst_location_title'],
        "sst_location_add" => $_POST['sst_location_add'],
        "sst_location_lat" => $_POST['sst_location_lat'],
        "sst_location_long" => $_POST['sst_location_long'],
        "sst_supplies" => $_POST['sst_supplies'],
        "sst_memo" => $_POST['sst_memo'],
    );

    $DB->where('sst_idx', $_POST['sst_idx'], );

    $DB->update('smap_schedule_t', $arr_query);
    $_last_idx = $_POST['sst_idx'];

    p_alert("수정되었습니다.");
} elseif ($_POST['act'] == "delete") {
    unset($arr_query);
    $arr_query = array(
        "sst_show" => 'N',
    );

    $DB->where('sst_idx', $_POST['obj_idx']);

    $DB->update('smap_schedule_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "contacrt_list") {
    unset($list_sc);
    $DB->where('a1.mt_idx', $_POST['mt_idx']);
    $DB->groupBy("a1.sct_category");
    $list_sc = $DB->get('smap_contact_t a1');

    if($list_sc) {
        foreach($list_sc as $row_sc) {
            ?>
<li class="media border p-2 mb-2">
    <div class="media-body">
        <h4 class="badge badge-info mb-2"><?=$row_sc['sct_category']?></h4>

        <ul class="list-group">
            <?php
            unset($list_sc2);
            $DB->where('a1.mt_idx', $row_sc['mt_idx']);
            $DB->where('a1.sct_category', $row_sc['sct_category']);
            $list_sc2 = $DB->get('smap_contact_t a1');

            if($list_sc2) {
                foreach($list_sc2 as $row_sc2) {
                    ?>
            <li class="list-group-item">
                <?=$row_sc2['sct_title']?> <?=$row_sc2['sct_hp']?>
                <input type="button" class="btn btn-outline-primary btn-xs ml-2" value="수정" onclick="f_sst_contacrt('modify', '<?=$row_sc2['sct_idx']?>');" />
                <input type="button" class="btn btn-outline-danger btn-xs" value="삭제" onclick="f_sst_contacrt('delete', '<?=$row_sc2['sct_idx']?>');" />
            </li>
            <?php
                }
            }
            ?>
        </ul>
    </div>
</li>
<?php
        }
    }
} elseif ($_POST['act'] == "contacrt_modal") {
    if($_POST['sct_idx']) {
        $DB->where('sct_idx', $_POST['sct_idx']);
        $row = $DB->getone('smap_contact_t');

        $_act = 'contacrt_update';
        $_act_msg = '수정되었습니다.';
    } else {
        $_act = 'contacrt_input';
        $_act_msg = '등록되었습니다.';
    }
    ?>
<div class="card">
    <div class="card-body">
        <h4 class="card-title">연락처 추가</h4>
        <form method="post" name="frm_form_modal" id="frm_form_modal">
            <input type="hidden" name="act" id="act" value="<?=$_act?>" />
            <input type="hidden" name="mt_idx" id="mt_idx" value="<?=$_POST['mt_idx']?>" />
            <input type="hidden" name="sst_idx" id="sst_idx" value="<?=$_POST['sst_idx']?>" />
            <input type="hidden" name="sct_idx" id="sct_idx" value="<?=$_POST['sct_idx']?>" />
            <div class="row no-gutters mb-2">
                <div class="col-sm-12">
                    <div class="form-group row align-items-center">
                        <label for="sct_category" class="col-sm-3 col-form-label">카테고리 <b class="text-danger">*</b></label>
                        <div class="col-sm-9">
                            <select name="sct_category" id="sct_category" style="width:200px;" class="form-control form-control-sm ml-2">
                                <option value="">카테고리 선택</option>
                                <?php
    unset($list_sc);
    $DB->where('a1.mt_idx', $_POST['mt_idx']);
    $DB->groupBy("a1.sct_category");
    $list_sc = $DB->get('smap_contact_t a1');

    if($list_sc) {
        foreach($list_sc as $row_sc) {
            if($row['sct_category'] == $row_sc['sct_category']) {
                $sel_t = ' selected';
            } else {
                $sel_t = '';
            }
            ?>
                                    <option value="<?=$row_sc['sct_category']?>"<?=$sel_t?>><?=$row_sc['sct_category']?></option>
                                    <?php
        }
    }
    ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label for="sct_title" class="col-sm-3 col-form-label">이름 <b class="text-danger">*</b></label>
                        <div class="col-sm-3">
                            <input type="text" name="sct_title" id="sct_title" value="<?=$row['sct_title']?>" class="form-control form-control-sm" maxlength="20" />
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label for="sct_hp" class="col-sm-3 col-form-label">연락처 <b class="text-danger">*</b></label>
                        <div class="col-sm-4">
                            <input type="text" name="sct_hp" id="sct_hp" numberOnly value="<?=$row['sct_hp']?>" class="form-control form-control-sm" maxlength="20" />
                            <small id="sct_hp_help" class="form-text text-muted">* 숫자만 입력바랍니다.</small>
                        </div>
                    </div>
                </div>
            </div>

            <p class="p-3 text-center">
                <input type="submit" value="확인" class="btn btn-outline-primary" />
                <input type="button" value="닫기" data-dismiss="modal" class="btn btn-outline-secondary mx-2" />
            </p>
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#sct_category').select2({
        theme: 'bootstrap4',
        language: "ko",
        tags: true
    });
});

$("#frm_form_modal").validate({
    submitHandler: function() {
        var f = document.frm_form_modal;

        $('#splinner_modal').modal('toggle');

        $('#modal-default').modal('hide');

        var form = $("#frm_form_modal")[0];
        var form_data = new FormData(form);

        $.ajax({
            data: form_data,
            type: "POST",
            enctype: "multipart/form-data",
            url: './schedule_update',
            cache: false,
            timeout: 5000,
            contentType: false,
            processData: false,
            success: function(data) {
                console.log(data);
                if (data == 'Y') {
                    $('#splinner_modal').modal('toggle');
                    $.alert({
                        title: '',
                        content: '<?=$_act_msg?>',
                        buttons: {
                            confirm: {
                                text: "확인",
                            },
                        },
                        onClose: function() {
                            f_contact_list();
                        },
                    });
                } else {
                    jalert(data["error"]);
                }
            },
            error: function(err) {},
        });

        return false;
    },
    rules: {
        sct_category: {
            required: true,
        },
        sct_title: {
            required: true,
        },
        sct_hp: {
            required: true,
        },
    },
    messages: {
        sct_category: {
            required: "카테고리를 입력해주세요.",
        },
        sct_title: {
            required: "이름을 입력해주세요.",
        },
        sct_hp: {
            required: "연락처를 입력해주세요.",
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
<?php
} elseif ($_POST['act'] == "contacrt_input") {
    if($_POST['sct_category_new']) {
        $_POST['sct_category'] = $_POST['sct_category_new'];
    }

    if($_POST['mt_idx'] == "") {
        p_alert("잘못된 접근입니다. mt_idx", 'back');
    }
    if($_POST['sst_idx'] == "") {
        p_alert("잘못된 접근입니다. sst_idx", 'back');
    }
    if($_POST['sct_category'] == "") {
        p_alert("잘못된 접근입니다. sct_category", 'back');
    }
    if($_POST['sct_title'] == "") {
        p_alert("잘못된 접근입니다. sct_title", 'back');
    }
    if($_POST['sct_hp'] == "") {
        p_alert("잘못된 접근입니다. sct_hp", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "mt_idx" => $_POST['mt_idx'],
        "sst_idx" => $_POST['sst_idx'],
        "sct_category" => $_POST['sct_category'],
        "sct_title" => $_POST['sct_title'],
        "sct_hp" => $_POST['sct_hp'],
        "sct_wdate" => $DB->now(),
    );

    $_last_idx = $DB->insert('smap_contact_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "contacrt_update") {
    if($_POST['sct_category_new']) {
        $_POST['sct_category'] = $_POST['sct_category_new'];
    }

    if($_POST['mt_idx'] == "") {
        p_alert("잘못된 접근입니다. mt_idx", 'back');
    }
    if($_POST['sct_idx'] == "") {
        p_alert("잘못된 접근입니다. sct_idx", 'back');
    }
    if($_POST['sct_category'] == "") {
        p_alert("잘못된 접근입니다. sct_category", 'back');
    }
    if($_POST['sct_title'] == "") {
        p_alert("잘못된 접근입니다. sct_title", 'back');
    }
    if($_POST['sct_hp'] == "") {
        p_alert("잘못된 접근입니다. sct_hp", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "sct_category" => $_POST['sct_category'],
        "sct_title" => $_POST['sct_title'],
        "sct_hp" => $_POST['sct_hp'],
    );

    $DB->where('mt_idx', $_POST['mt_idx']);
    $DB->where('sct_idx', $_POST['sct_idx']);

    $DB->update('smap_contact_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "contacrt_delete") {
    if($_POST['mt_idx'] == "") {
        p_alert("잘못된 접근입니다. mt_idx", 'back');
    }
    if($_POST['sct_idx'] == "") {
        p_alert("잘못된 접근입니다. sct_idx", 'back');
    }

    $DB->where('mt_idx', $_POST['mt_idx']);
    $DB->where('sct_idx', $_POST['sct_idx']);
    $DB->delete('smap_contact_t');

    echo "Y";
} elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //고정 검색값
    $DB->where('a1.sst_show', 'Y');
    // $DB->where('a1.sst_pidx is null');

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.sst_title, \''.$_POST['obj_search_txt'].'\') or instr(a4.sgt_title, \''.$_POST['obj_search_txt'].'\') or instr(a2.mt_name, \''.$_POST['obj_search_txt'].'\') or instr(a2.mt_id, \''.$_POST['obj_search_txt'].'\') or instr(a1.sst_location_add, \''.$_POST['obj_search_txt'].'\') or instr(a1.sst_memo, \''.$_POST['obj_search_txt'].'\') )');
        } else {
            $DB->Where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    //Join
    $DB->join("member_t a2", "a1.mt_idx=a2.mt_idx", "LEFT");
    $DB->join("smap_group_detail_t a3", "a1.sgdt_idx=a3.sgdt_idx", "LEFT");
    $DB->join("smap_group_t a4", "a3.sgt_idx=a4.sgt_idx", "LEFT");

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.sst_idx", "desc");
    } else {
        $DB->orderBy("a1.sst_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("smap_schedule_t a1", $pg);

    //페이징
    $n_page = $DB->totalPages;
    $counts = $DB->totalCount;
    $counts = $counts - (($pg - 1) * $n_limit_num);

    ?>
<table class="table inx-table inx-table-card">
    <thead class="thead-dark">
        <tr>
            <th class="text-center" style="width:100px;">
                번호
            </th>
            <th class="text-center" style="width:160px;">
                관리
            </th>
            <th class="text-center" style="width:120px;">
                그룹
            </th>
            <th class="text-center" style="width:120px;">
                작성자
            </th>

            <th class="text-center">
                일정
            </th>
            <th class="text-center">
                장소
            </th>
            <th class="text-center">
                메모
            </th>
            <th class="text-center" style="width:140px;">
                등록일시
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
    if ($list) {
        foreach ($list as $row) {
            if($row['sst_repeat_json']) {
                $sst_repeat_json_de = json_decode($row['sst_repeat_json'], true);

                $arr_sst_repeat_json_t = $arr_sst_repeat_json[$sst_repeat_json_de['r1']];

                if($sst_repeat_json_de['r1'] == '3' && $sst_repeat_json_de['r2']) {
                    $sst_repeat_json_de_ex = explode(',', $sst_repeat_json_de['r2']);

                    unset($arr_sst_repeat_json_de_r2);
                    $arr_sst_repeat_json_de_r2 = array();
                    if($sst_repeat_json_de_ex) {
                        foreach($sst_repeat_json_de_ex as $key => $val) {
                            if($val) {
                                $arr_sst_repeat_json_de_r2[] = $arr_sst_repeat_json_r2[$val];
                            }
                        }

                        if($arr_sst_repeat_json_de_r2) {
                            $arr_sst_repeat_json_t .= " ".implode(', ', $arr_sst_repeat_json_de_r2);
                        }
                    }
                }
            }

            if($row['sst_all_day'] == 'Y') {
                $sst_all_day_t = '<span class="badge badge-secondary">하루종일</span> ';
                $ss_date_t = DateType($row['sst_sdate'], 12);
            } else {
                $sst_all_day_t = '';
                $ss_date_t = DateType($row['sst_sdate'], 6).' ~ '.DateType($row['sst_edate'], 6);
            }
            ?>
        <tr>
            <td data-title="번호" class="text-center">
                <?=$counts?>
            </td>
            <td data-title="관리" class="text-center">
                <input type="button" class="btn btn-outline-primary btn-sm" value="상세" onclick="location.href='./schedule_form?act=update&sst_idx=<?=$row['sst_idx']?>'" />
                <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./schedule_update', '<?=$row['sst_idx']?>');" />
            </td>
            <td data-title="그룹" class="text-center">
                <?=$row['sgt_title']?>
            </td>
            <td data-title="작성자" class="text-center">
                <?=$row['mt_name']?> (<?=$row['mt_id']?>)
            </td>

            <td data-title="일정">
                <span class="line1_text"><?=$row['sst_title']?></span>
                <span class="line1_text"><?=$ss_date_t?></span>
                <?=$sst_all_day_t?><span class="badge badge-info"><?=$arr_sst_repeat_json_t?></span> <span class="badge badge-primary"><?=$arr_sst_alram[$row['sst_alram']]?></span>
            </td>

            <td data-title="장소" class="text-center">
                <?=$row['sst_location_add']?>
            </td>
            <td data-title="메모">
                <span class="line1_text"><?=$row['sst_memo']?></span>
            </td>
            <td data-title="등록일시" class="text-center">
                <?=DateType($row['sst_wdate'], 6)?>
            </td>
        </tr>
        <?php
            $counts--;
        }
    } else {
        ?>
        <tr>
            <td colspan="8" class="text-center"><b>자료가 없습니다.</b></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
<?php
    if($n_page > 1) {
        echo page_listing_mng_xhr($pg, $n_page, 'f_get_box_mng_list');
    }
}

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
