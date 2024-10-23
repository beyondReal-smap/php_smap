<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '4';
$h_menu = '5';
$translations = require $_SERVER['DOCUMENT_ROOT'] . '/lang/' . $userLang . '.php'; // 번역 파일 로드
$_SUB_HEAD_TITLE = $translations['txt_location_summary'];
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>
<div class="container sub_pg px-0">
    <div class="">
        <div class="fixed_top sch_cld_wrap bg-white pt-3 border-bottom">
            <div class="cld_head_wr ">
                <div class="sel_month d-inline-flex pl_16">
                    <img class="mr-2" src="<?=CDN_HTTP?>/img/sel_month.png" alt="<?=$translations['txt_month_selection_icon']?>" style="width:1.6rem;">
                    <select class="form-none custom-select text-text">
                        <option selected value="1">2023년 09월</option>
                        <option value="2">2023년 10월</option>
                        <option value="3">2023년 11월</option>
                        <option value="4">2023년 12월</option>
                    </select>
                </div>
                <div class="cld_head fs_12">
                    <ul>
                        <li class="sun text-danger"><?=$translations['txt_sunday'] ?></li>
                        <li><?=$translations['txt_monday'] ?></li>
                        <li><?=$translations['txt_tuesday'] ?></li>
                        <li><?=$translations['txt_wednesday'] ?></li>
                        <li><?=$translations['txt_thursday'] ?></li>
                        <li><?=$translations['txt_friday'] ?></li>
                        <li class="sat text-primary"><?=$translations['txt_saturday'] ?></li>
                    </ul>
                </div>
            </div>
            <div class="cld_date_wrap">
                <form>
                    <div class="date_conent">
                        <div class="cld_content">
                            <div class="cld_body fs_15 fw_500">
                                <ul>
                                    <li>
                                        <div class="lastday"><span>31</span></div>
                                    </li>
                                    <li>
                                        <div class="lastday"><span>1</span></div>
                                    </li>
                                    <li>
                                        <div class="lastday"><span>2</span></div>
                                    </li>
                                    <li>
                                        <div class="lastday"><span>3</span></div>
                                    </li>
                                    <li>
                                        <div class="today"><span>4</span></div>
                                    </li>
                                    <li>
                                        <div class=""><span>5</span></div>
                                    </li>
                                    <li>
                                        <div class=" sat"><span>6</span></div>
                                    </li>
                                    <li>
                                        <div class="sun"><span>7</span></div>
                                    </li>
                                    <li>
                                        <div class=""><span>8</span></div>
                                    </li>
                                    <li>
                                        <div class=""><span>9</span></div>
                                    </li>
                                    <li>
                                        <div class="schdl"><span>10</span></div>
                                    </li>
                                    <li>
                                        <div class="schdl"><span>11</span></div>
                                    </li>
                                    <li>
                                        <div class="schdl"><span>12</span></div>
                                    </li>
                                    <li>
                                        <div class="sat schdl"><span>13</span></div>
                                    </li>
                                    <li>
                                        <div class="sun"><span>14</span></div>
                                    </li>
                                    <li>
                                        <div class=""><span>15</span></div>
                                    </li>
                                    <li>
                                        <div class=""><span>16</span></div>
                                    </li>
                                    <li>
                                        <div class=""><span>17</span></div>
                                    </li>
                                    <li>
                                        <div class=""><span>18</span></div>
                                    </li>
                                    <li>
                                        <div class=""><span>19</span></div>
                                    </li>
                                    <li>
                                        <div class="sat"><span>20</span></div>
                                    </li>
                                    <li>
                                        <div class="sun"><span>21</span></div>
                                    </li>
                                    <li>
                                        <div class=""><span>22</span></div>
                                    </li>
                                    <li>
                                        <div class=""><span>23</span></div>
                                    </li>
                                    <li>
                                        <div class="active act_ing act_start"><span>24</span></div>
                                    </li>
                                    <li>
                                        <div class="act_ing"><span>25</span></div>
                                    </li>
                                    <li>
                                        <div class="act_ing"><span>26</span></div>
                                    </li>
                                    <li>
                                        <div class="sat act_ing"><span>27</span></div>
                                    </li>
                                    <li>
                                        <div class="sun act_ing"><span>28</span></div>
                                    </li>
                                    <li>
                                        <div class="act_ing"><span>29</span></div>
                                    </li>
                                    <li>
                                        <div class="act_ing"><span>30</span></div>
                                    </li>
                                    <li>
                                        <div class="active act_ing act_end"><span>31</span></div>
                                    </li>
                                    <li>
                                        <div class="sun lastday"><span></span></div>
                                    </li>
                                    <li>
                                        <div class="sun lastday"><span></span></div>
                                    </li>
                                    <li>
                                        <div class="sun lastday"><span></span></div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="down_wrap text-center pt_08 pb-3">
                <img src="<?=CDN_HTTP?>/img/btn_bl_arrow.png" class="top_down mx-auto" width="12px" alt="<?=$translations['txt_top_down'] ?>" />
            </div>
        </div>
        <div class="sch_wrap">
            <!-- 지도 들어가는 영역 -->
            <div class="map_indivi">
                <p class="map_num text-white mx-1 my-1">1</p>
                <p class="map_num text-white mx-1 my-1">2</p>
                <p class="map_num text-white mx-1 my-1">3</p>
            </div>
            <div class="sch_summary row mx-0 align-items-start bg_main py-5 border-bottom">
                <div class="sch_summary_div px-2">
                    <p class="fs_13 text_gray text-center"><?=$translations['txt_schedule_count'] ?></p>
                    <p class="fs_16 fw_700 fc_mian_sec text-center text_dynamic mt_07">3<span><?=$translations['txt_items'] ?></span></p>
                </div>
                <div class="sch_summary_div border-left border-right px-2">
                    <p class="fs_13 text_gray text-center"><?=$translations['txt_distance_km'] ?></p>
                    <p class="fs_16 fw_700 fc_mian_sec text-center text_dynamic mt_07">3565.42<span>km</span></p>
                </div>
                <div class="sch_summary_div px-2">
                    <p class="fs_13 text_gray text-center"><?=$translations['txt_travel_time'] ?></p>
                    <p class="fs_16 fw_700 fc_mian_sec text-center text_dynamic mt_07">5622<span><?=$translations['txt_minute'] ?></span></p>
                </div>
            </div>
            <div class="pt_20 px_16">
                <div class="border rounded-lg px_16 py_16 d-flex align-items-center justify-content-between mb-3">
                    <div class="mr-2">
                        <p class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line_h1_4 w_fit mb-2">집</p>
                    </div>
                    <div class="flex-shrink-0">
                        <p class="fs_11 text_light_gray text-right">체류시간</p>
                        <p class="fs_13 fw_700 text-right mt-2"><?=$translations['txt_hour_label'] ?> 23<?=$translations['txt_minutes_stay'] ?></p>
                    </div>
                </div>
                <div class="border rounded-lg px_16 py_16 d-flex align-items-center justify-content-between mb-3">
                    <div class="mr-2">
                        <p class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line_h1_4 w_fit mb-2">피아노 학원 피아 노 학원 피아노</p>
                    </div>
                    <div class="flex-shrink-0">
                        <p class="fs_11 text_light_gray text-right">체류시간</p>
                        <p class="fs_13 fw_700 text-right mt-2">1<?=$translations['txt_hour_label'] ?> 11<?=$translations['txt_minutes_stay'] ?></p>
                    </div>
                </div>
                <div class="border rounded-lg px_16 py_16 d-flex align-items-center justify-content-between mb-3">
                    <div class="mr-2">
                        <p class="fs_13 fc_primary rounded_04 bg_secondary px_06 py_02 line_h1_4 w_fit mb-2">수학학원</p>
                    </div>
                    <div class="flex-shrink-0">
                        <p class="fs_11 text_light_gray text-right">체류시간</p>
                        <p class="fs_13 fw_700 text-right mt-2">2<?=$translations['txt_hour_label'] ?> 26<?=$translations['txt_minutes_stay'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 바텀시트 업다운
$('.down_wrap').click(function() {
    var cldDateWrap = $('.sch_cld_wrap .cld_date_wrap');

    // .on 클래스를 토글
    cldDateWrap.toggleClass('on');

    // .on 클래스의 유무에 따라 이미지 파일 이름 변경
    var imgSrc = cldDateWrap.hasClass('on') ? 'btn_tl_arrow.png' : 'btn_bl_arrow.png';
    $('.down_wrap img.top_down').attr('src', './img/' + imgSrc);

    // CSS 스타일 추가
    if (cldDateWrap.hasClass('on')) {
        $('.sch_wrap').css('padding-top', '34.7rem');
    } else {
        $('.sch_wrap').css('padding-top', '15.3rem');
    }
});
</script>
<?php
include $_SERVER['DOCUMENT_ROOT']."/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
?>