<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

if ($_POST['act'] == "update") {
    if($_POST['qt_idx'] == "") {
        p_alert("잘못된 접근입니다. fct_idx", 'back');
    }

    unset($arr_query);
    $arr_query = array(
        "qt_atitle" => $_POST['qt_atitle'],
        "qt_acontent" => $_POST['qt_acontent'],
        "qt_status" => $_POST['qt_status'],
        "qt_adate" => $DB->now(),
    );

    $DB->where('qt_idx', $_POST['qt_idx']);

    $DB->update('qna_t', $arr_query);
    $_last_idx = $_POST['qt_idx'];

    p_alert("수정되었습니다.");
} elseif ($_POST['act'] == "delete") {
    unset($arr_query);
    $arr_query = array(
        "qt_show" => 'N',
    );

    $DB->where('qt_idx', $_POST['obj_idx']);

    $DB->update('qna_t', $arr_query);

    echo "Y";
} elseif ($_POST['act'] == "content_view") {
    $DB->where('qt_idx', $_POST['qt_idx']);
    $row = $DB->getone('qna_t');
?>
<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">문의 상세보기</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <b><?=$row['qt_qtitle']?></b></br>
    <b><?=DateType($row['qt_qdate'], 6)?></b></br></br>
    <p><?=nl2br($row['qt_qcontent'])?></p>

    <hr />

    <form method="post" name="frm_form_qna" id="frm_form_qna" action="./qna_update" target="hidden_ifrm" enctype="multipart/form-data">
        <input type="hidden" name="act" id="act" value="update" />
        <input type="hidden" name="qt_idx" id="qt_idx" value="<?=$row['qt_idx']?>" />
        <div class="form-group d-none-temp">
            <label for="qt_atitle">답변 제목 <b class="text-danger">*</b></label>
            <input type="text" name="qt_atitle" id="qt_atitle" value="<?=$row['qt_atitle']?>" class="form-control form-control-sm" maxlength="100" />
        </div>
        <div class="form-group">
            <label for="qt_acontent">답변 내용 <b class="text-danger">*</b></label>
            <textarea name="qt_acontent" id="qt_acontent" style="height: 200px;line-height:1.4rem;font-size:1rem;" class="form-control"><?=$row['qt_acontent']?></textarea>
        </div>
        <div class="form-group">
            <label for="qt_status">상태</label>
            <select class="form-control" name="qt_status" id="qt_status">
                <?=$arr_qt_status_option?>
            </select>
        </div>
        <?php if($row['qt_status']=='2') { ?>
        <div class="form-group">
            <label for="qt_adate">답변일시 : <?=DateType($row['qt_adate'], 6)?></label>
        </div>
        <? } ?>
        <button type="submit" class="btn btn-primary">확인</button>
    </form>

    <script type="text/javascript">
    $("#frm_form_qna").validate({
        submitHandler: function() {
            var f = $("#frm_form_qna")[0];
            var form_data = new FormData(f);

            $('#splinner_modal').modal('toggle');

            return true;
        },
        rules: {
            // qt_atitle: {
            //     required: true,
            // },
            qt_acontent: {
                required: true,
            },
        },
        messages: {
            // qt_atitle: {
            //     required: "답변 제목을 입력해주세요.",
            // },
            qt_acontent: {
                required: "답변 내용을 입력해주세요.",
            },
        },
        errorPlacement: function(error, element) {
            $(element)
                .closest("form")
                .find("span[for='" + element.attr("id") + "']")
                .append(error);
        },
    });

    <?php if ($row['qt_status']) { ?>
    $('#qt_status').val('<?=$row['qt_status']?>');
    <?php } ?>
    </script>
</div>
<?php
} elseif ($_POST['act'] == "list") {
    unset($list);
    $DB->pageLimit = $n_limit_num;
    $pg = $_POST['obj_pg'];

    //고정 검색값
    $DB->where('a1.qt_show', 'Y');

    //검색
    if ($_POST['obj_search_txt']) {
        if ($_POST['obj_sel_search'] == "all") {
            $DB->Where('( instr(a1.mt_id, \''.$_POST['obj_search_txt'].'\') or instr(a1.mt_name, \''.$_POST['obj_search_txt'].'\') or instr(a1.qt_qtitle, \''.$_POST['obj_search_txt'].'\') or instr(a1.qt_qcontent, \''.$_POST['obj_search_txt'].'\') or instr(a1.qt_atitle, \''.$_POST['obj_search_txt'].'\') or instr(a1.qt_acontent, \''.$_POST['obj_search_txt'].'\') )');
        } else {
            $DB->Where('( instr('.$_POST['obj_sel_search'].', \''.$_POST['obj_search_txt'].'\') )');
        }
    }

    if ($_POST['sel_qt_status']) {
        $DB->where('a1.qt_status', $_POST['sel_qt_status']);
    }

    //정렬
    if ($_POST['obj_order_desc_asc'] == '1') {
        $DB->orderBy("a1.qt_idx", "desc");
    } else {
        $DB->orderBy("a1.qt_idx", "asc");
    }

    $list = $DB->arraybuilder()->paginate("qna_t a1", $pg);

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
                아이디
            </th>
            <th class="text-center" style="width:120px;">
                이름
            </th>

            <th class="text-center">
                문의제목
            </th>
            <th class="text-center" style="width:120px;">
                답변상태
            </th>
            <th class="text-center" style="width:120px;">
                문의일시
            </th>
            <th class="text-center" style="width:120px;">
                답변일시
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
    if ($list) {
        foreach ($list as $row) {
            ?>
        <tr>
            <td data-title="번호" class="text-center">
                <?=$counts?>
            </td>
            <td data-title="관리" class="text-center">
                <input type="button" class="btn btn-outline-primary btn-sm" value="상세" onclick="f_qna_content('<?=$row['qt_idx']?>');" />
                <input type="button" class="btn btn-outline-danger btn-sm" value="삭제" onclick="f_post_del('./qna_update', '<?=$row['qt_idx']?>');" />
            </td>
            <td data-title="아이디" class="text-center">
                <?=$row['mt_id']?>
            </td>
            <td data-title="이름" class="text-center">
                <?=$row['mt_name']?>
            </td>
            <td data-title="문의제목">
                <span class="line1_text"><?=$row['qt_qtitle']?></span>
            </td>
            <td data-title="답변상태" class="text-center">
                <?=$arr_qt_status[$row['qt_status']]?>
            </td>
            <td data-title="문의일시" class="text-center">
                <?=DateType($row['qt_qdate'], 6)?>
            </td>
            <td data-title="답변일시" class="text-center">
                <?=DateType($row['qt_adate'], 6)?>
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