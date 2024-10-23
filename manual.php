<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lib.inc.php";
$b_menu = '';
$h_menu = '6';
$h_url = './setting';

$_SUB_HEAD_TITLE = $translations['txt_manual']; 
include $_SERVER['DOCUMENT_ROOT'] . "/head.inc.php";

// 유튜브 링크 - 번역 키 사용
$youtubeLinks = [
    'txt_intro_1' => 'https://youtu.be/fRLxsHCvwuQ',
    'txt_intro_2' => 'https://youtu.be/xOqCizxr2uk',
    'txt_group' => 'https://youtu.be/Bvzaz5vFyAo',
    'txt_schedule' => 'https://youtu.be/Ba83-yfjvBQ',
    'txt_my_places' => 'https://youtube.com/shorts/EDcvCwZmF38?feature=share' 
];
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

    function openYoutube(link) { 
        window.location.href = link;
    }
</script>

<div class="container sub_pg px-0">
    <div class=" py_16">
        <div class="px_16">
            <div class="border rounded-lg py_16 bg-white mb-3">
                <?php foreach ($youtubeLinks as $key => $link) : ?>
                    <a href="<?= $link ?>" target="_blank" class="d-flex align-items-center justify-content-between px_16 py_16">
                        <p class="fs_16 fw_600"><?=$translations[$key] ?></p>
                        <i class="xi-angle-right-thin text_light_gray fs_16"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php
include $_SERVER['DOCUMENT_ROOT'] . "/foot.inc.php";
include $_SERVER['DOCUMENT_ROOT'] . "/tail.inc.php";
?>