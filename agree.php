<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '6';
$h_url = './setting';
$_SUB_HEAD_TITLE = translate("약관 및 동의 관리", $userLang); // "약관 및 동의 관리" 번역
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";
?>
<script>
    function openurl(t) {
        var message = {
            "type": "openUrlBlank",
            "param": t
        };
        if (isAndroid()) {
            window.smapAndroid.openUrlBlank(t);
        } else if (isiOS()) {
            window.webkit.messageHandlers.smapIos.postMessage(message);
        }

    }

    function isAndroid() {
        return navigator.userAgent.match(/Android/i);
    }

    function isiOS() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    }
</script>
<div class="container sub_pg px-0">
    <div class=" py_16">
        <div class="px_16">
            <div class="border rounded-lg py_16 bg-white mb-3">
                <?php
                $st_info = get_setup_t_info();
                foreach ($arr_mt_agree as $key => $val) {
                ?>
                    <a onclick="openurl('<?= $st_info['st_agree' . $key] ?>')" target="_blank" class="d-flex align-items-center justify-content-between px_16 py_16">
                        <p class="fs_16 fw_600"><?= translate($val, $userLang); ?></p> <!-- $val 번역 -->
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>