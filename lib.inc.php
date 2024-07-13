<?php

ob_start('ob_gzhandler');
header("Content-Type: text/html; charset=utf-8");
header("Access-Control-Allow-Origin: *");

ini_set('session.cache_expire', 86400);
ini_set('session.gc_maxlifetime', 86400);
ini_set('session.use_trans_sid', 0);
ini_set('url_rewriter.tags', '');
ini_set("session.gc_probability", 1);
ini_set("session.gc_divisor", 100);

//redis 세션 사용
ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://127.0.0.1:6379');

//파일 세션 사용
// session_save_path($_SERVER['DOCUMENT_ROOT'].'/sessions');

session_cache_limiter('nocache, must_revalidate');
session_set_cookie_params(0, "/");
session_start();

header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');

//허니팟
if (isset($_POST['firstname']) && $_POST['firstname']) {
    exit;
}

include $_SERVER['DOCUMENT_ROOT']."/db.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/config.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/config_arr.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/mail.inc.php";
include $_SERVER['DOCUMENT_ROOT']."/MobileDetect.inc.php";
require_once $_SERVER['DOCUMENT_ROOT']."/redis_cache_util.php";
include $_SERVER['DOCUMENT_ROOT'] . "/queries.php"; // 쿼리 파일 포함

class Logger {
    private $logFile;
    private $maxSize;
    private $backupCount;

    public function __construct($logFile = 'application.log', $maxSize = 5242880, $backupCount = 5) {
        $this->logFile = __DIR__ . '/' . $logFile;
        $this->maxSize = $maxSize;  // 5MB
        $this->backupCount = $backupCount;
    }

    public function write($message) {
        $this->rotateIfNeeded();
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

    private function rotateIfNeeded() {
        if (file_exists($this->logFile) && filesize($this->logFile) > $this->maxSize) {
            for ($i = $this->backupCount; $i > 0; $i--) {
                $oldFile = $this->logFile . '.' . $i;
                $newFile = $this->logFile . '.' . ($i + 1);
                if (file_exists($oldFile)) {
                    rename($oldFile, $newFile);
                }
            }
            rename($this->logFile, $this->logFile . '.1');
        }
    }
}

$logger = new Logger();

$detect_mobile = new \Detection\MobileDetect();
if ($detect_mobile->isMobile()) {
    $chk_mobile = true;
} else {
    $chk_mobile = false;
}
if(!$cron_chk) {
    if ($_SERVER['REMOTE_ADDR'] == '115.93.39.5') {
        error_reporting(E_ERROR);
        ini_set('display_errors', '1');

        $chk_admin = true;
    } else {
        if ($_SESSION['_mt_level'] == '9') {
            $chk_admin = false;
        } else {
            $chk_admin = false;
        }
    }
} else {
    error_reporting(E_ERROR);
    ini_set('display_errors', '1');
}

error_reporting(E_ERROR);
ini_set('display_errors', '1');


//캐시서버 사용시 리셋
if($chk_admin) {
    opcache_reset();

    include $_SERVER['DOCUMENT_ROOT']."/config_mng.inc.php";
}

function alert($msg, $url = "", $ttl = "")
{
    if ($msg != "") {
        echo "<script type=\"text/javascript\">
jalert_url('".$msg."', '".$url."', '".$ttl."');
</script>";
    } else {
        echo "<script type=\"text/javascript\">
".$url.";
</script>";
    }
    exit;
}

function alert_b($msg, $url = "")
{
    if ($url == "") {
        $url = "history.go(-1)";
    } else {
        $url = "document.location.href = '".$url."'";
    }

    if ($msg != "") {
        echo "<script type=\"text/javascript\">
alert('".$msg."');".$url.";
</script>";
    } else {
        echo "<script type=\"text/javascript\">
".$url.";
</script>";
    }
    exit;
}

function just_alert($msg)
{
    echo "<script type=\"text/javascript\">
parent.jalert('".$msg. "');
</script>";
}

function p_alert($msg, $url = "", $ttl = "")
{
    if ($msg != "") {
        echo "<script type=\"text/javascript\">
            parent.jalert_url('".$msg."', '".$url."', '".$ttl."');
           </script>";
    } else {
        echo "<script type=\"text/javascript\">
            ".$url.";
            </script>";
    }
    exit;
}

function p_confirm($msg, $url1, $url2)
{
    echo "<script type=\"text/javascript\">
if(confirm('".$msg."')) {
parent.document.location.href = '".$url1."';
} else {
parent.document.location.href = '".$url2."';
}
</script>";
    exit;
}

function p_reload_to($url = "")
{
    if ($url == "") {
        $url = "parent.location.reload()";
    } else {
        $url = "parent.document.location.href = '".$url."'";
    }

    echo "<script type=\"text/javascript\">
".$url.";
</script>";
    exit;
}

function gotourl($url)
{
    $url = "document.location.href = '".$url."'";
    echo "<script type=\"text/javascript\">
".$url.";
</script>";
    exit;
}

function top_location_url($url)
{
    $url = "top.location.href = '".$url."'";
    echo "<script type=\"text/javascript\">
".$url.";
</script>";
    exit;
}

function p_gotourl($url)
{
    $url = "parent.document.location.href = '".$url."'";
    echo "<script type=\"text/javascript\">
".$url.";
</script>";
    exit;
}

function ps_gotourl($url)
{
    $url = "opener.document.location.href = '".$url."'";
    echo "<script type=\"text/javascript\">
".$url.";
</script>";
    exit;
}

function page_listing_xhr($cur_page, $total_page, $f_name)
{
    $retValue = "<ul class=\"pagination\">";
    if ($cur_page > 1) {
        $retValue .= "<li class=\"pgn_prev\"><a href=\"#\" onclick=\"".$f_name."('".($cur_page - 1)."')\"><i class=\"xi-angle-left-min\"></i></a></li>";
    } else {
        $retValue .= "<li class=\"pgn_prev\"><a href=\"#\" class=\"disabled\"><i class=\"xi-angle-left-min\"></i></a></li>";
    }
    $start_page = (((int)(($cur_page - 1) / 2)) * 2) + 1;
    $end_page = $start_page + 2;
    if ($end_page >= $total_page) {
        $end_page = $total_page;
    }
    if ($total_page > 1) {
        for ($k = $start_page;$k <= $end_page;$k++) {
            if ($cur_page != $k) {
                $retValue .= "<li class=\"\"><a href=\"javascript:;\" onclick=\"".$f_name."('".$k."')\">".$k."</a></li>";
            } else {
                $retValue .= "<li class=\"on\"><a href=\"javascript:;\" onclick=\"".$f_name."('".$k."')\" class=\"on\">".$k."</a></li>";
            }
        }
    }

    if ($cur_page < $total_page && $total_page > 1) {
        $retValue .= "<li class=\"pgn_next\"><a href=\"javascript:;\" onclick=\"".$f_name."('".($cur_page + 1)."')\"><i class=\"xi-angle-right-min\"></i></a></li>";
    } else {
        $retValue .= "<li class=\"pgn_next\"><a href=\"#\" class=\"disabled\"><i class=\"xi-angle-right-min\"></i></a></li>";
    }
    $retValue .= "</ul>";

    return $retValue;
}

function page_listing_mng_xhr($cur_page, $total_page, $f_name)
{
    $retValue = "<nav class=\"m-3\" aria-label=\"Page navigation\"><ul class=\"page-light pagination justify-content-center\">";
    if ($cur_page > 1) {
        $retValue .= "<li class=\"page-item\"><a class=\"page-link\" aria-label=\"이전\" href=\"javascript:;\" onclick=\"".$f_name."('".($cur_page - 1)."')\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
    } else {
        $retValue .= "<li class=\"page-item disabled\"><a class=\"page-link\" href=\"javascript:;\" tabindex=\"-1\" aria-disabled=\"true\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
    }
    $start_page = (((int)(($cur_page - 1) / 5)) * 5) + 1;
    $end_page = $start_page + 5;
    if ($end_page >= $total_page) {
        $end_page = $total_page;
    }
    if ($total_page > 1) {
        for ($k = $start_page;$k <= $end_page;$k++) {
            if ($cur_page != $k) {
                $retValue .= "<li class=\"page-item\"><a class=\"page-link\" href=\"javascript:;\" onclick=\"".$f_name."('".$k."')\">".$k."</a></li>";
            } else {
                $retValue .= "<li class=\"page-item active\" aria-current=\"page\"><a class=\"page-link\" href=\"javascript:;\" onclick=\"".$f_name."('".$k."')\">".$k."</a></li>";
            }
        }
    }

    if ($cur_page < $total_page && $total_page > 1) {
        $retValue .= "<li class=\"page-item\"><a class=\"page-link\" aria-label=\"다음\" href=\"javascript:;\" onclick=\"".$f_name."('".($cur_page + 1)."')\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
    } else {
        $retValue .= "<li class=\"page-item disabled\"><a class=\"page-link\" href=\"javascript:;\" tabindex=\"-1\" aria-disabled=\"true\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
    }
    $retValue .= "</ul></nav>";

    return $retValue;
}

function page_listing($cur_page, $total_page, $url, $link_id = "")
{
    $retValue = "<nav class=\"m-3\" aria-label=\"Page navigation\"><ul class=\"page-light pagination justify-content-center\">";
    if ($cur_page > 1) {
        $retValue .= "<li class=\"page-item\"><a class=\"page-link\" aria-label=\"이전\" href=\"".$url.($cur_page - 1).$link_id."\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
    } else {
        $retValue .= "<li class=\"page-item disabled\"><a class=\"page-link\" href=\"#\" tabindex=\"-1\" aria-disabled=\"true\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
    }
    $start_page = (((int)(($cur_page - 1) / 5)) * 5) + 1;
    $end_page = $start_page + 5;
    if ($end_page >= $total_page) {
        $end_page = $total_page;
    }
    if ($total_page > 1) {
        for ($k = $start_page;$k <= $end_page;$k++) {
            if ($cur_page != $k) {
                $retValue .= "<li class=\"page-item\"><a class=\"page-link\" href=\"".$url.$k.$link_id."\">".$k."</a></li>";
            } else {
                $retValue .= "<li class=\"page-item active\" aria-current=\"page\"><a class=\"page-link\" href=\"".$url.$k.$link_id."\">".$k."</a></li>";
            }
        }
    }

    if ($cur_page < $total_page && $total_page > 1) {
        $retValue .= "<li class=\"page-item\"><a class=\"page-link\" aria-label=\"다음\" href=\"".$url.($cur_page + 1).$link_id."\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
    } else {
        $retValue .= "<li class=\"page-item disabled\"><a class=\"page-link\" href=\"#\" tabindex=\"-1\" aria-disabled=\"true\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
    }
    $retValue .= "</ul></nav>";

    return $retValue;
}

function check_file_ext($filename, $allow_ext)
{
    if ($filename == "") {
        return true;
    }
    $ext = get_file_ext($filename);
    $allow_ext = explode(";", $allow_ext);
    $sw_allow_ext = false;
    for ($i = 0; $i < count($allow_ext); $i++) {
        if ($ext == $allow_ext[$i]) {
            $sw_allow_ext = true;
            break;
        }
    }

    return $sw_allow_ext;
}

function upload_file($srcfile, $destfile, $dir)
{
    if ($destfile == "") {
        return false;
    }
    move_uploaded_file($srcfile, $dir.$destfile);
    chmod($dir.$destfile, 0666);

    return true;
}

function get_file_ext($filename)
{
    if ($filename == "") {
        return "";
    }
    $type = explode(".", $filename);
    $ext = strtolower($type[count($type) - 1]);

    return $ext;
}

function cut_str($strSource, $iStart, $iLength, $tail = "")
{
    $iSourceLength = mb_strlen($strSource, "UTF-8");

    if ($iSourceLength > $iLength) {
        return mb_substr($strSource, $iStart, $iLength, "UTF-8").$tail;
    } else {
        return $strSource;
    }
}

function mailer_new($fname, $fmail, $to, $tname, $subject, $content)
{
    global $mail;

    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->CharSet = 'UTF-8';
    $mail->Debugoutput = 'html';
    $mail->Host = 'smtp.worksmobile.com';
    $mail->Port = 587;
    $mail->SMTPSecure = 'STARTTLS';
    $mail->SMTPAuth = true;
    $mail->Username = "admin@smap.site";
    $mail->Password = "zt0YUZt33cad";

    $mail->setFrom($fmail, $fname);
    $mail->addAddress($to, $tname);

    $mail->Subject = $subject;
    $mail->msgHTML($content);

    if (!$mail->send()) {
        return 'Message could not be sent.';
        return 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        return 'Message has been sent';
    }
}

function thumnail($file, $save_filename, $save_path, $max_width, $max_height)
{
    $img_info = getimagesize($file);
    if ($img_info[2] == 1) {
        $src_img = ImageCreateFromGif($file);
    } elseif ($img_info[2] == 2) {
        $src_img = ImageCreateFromJPEG($file);
    } elseif ($img_info[2] == 3) {
        $src_img = ImageCreateFromPNG($file);
    } else {
        return 0;
    }
    $img_width = $img_info[0];
    $img_height = $img_info[1];

    if ($img_width > $max_width || $img_height > $max_height) {
        if ($img_width == $img_height) {
            $dst_width = $max_width;
            $dst_height = $max_height;
        } elseif ($img_width > $img_height) {
            $dst_width = $max_width;
            $dst_height = ceil(($max_width / $img_width) * $img_height);
        } else {
            $dst_height = $max_height;
            $dst_width = ceil(($max_height / $img_height) * $img_width);
        }
    } else {
        $dst_width = $img_width;
        $dst_height = $img_height;
    }
    if ($dst_width < $max_width) {
        $srcx = ceil(($max_width - $dst_width) / 2);
    } else {
        $srcx = 0;
    }
    if ($dst_height < $max_height) {
        $srcy = ceil(($max_height - $dst_height) / 2);
    } else {
        $srcy = 0;
    }

    if ($img_info[2] == 1) {
        $dst_img = imagecreate($max_width, $max_height);
    } else {
        $dst_img = imagecreatetruecolor($max_width, $max_height);
    }

    $bgc = ImageColorAllocate($dst_img, 255, 255, 255);
    ImageFilledRectangle($dst_img, 0, 0, $max_width, $max_height, $bgc);
    ImageCopyResampled($dst_img, $src_img, $srcx, $srcy, 0, 0, $dst_width, $dst_height, ImageSX($src_img), ImageSY($src_img));

    if ($img_info[2] == 1) {
        ImageInterlace($dst_img);
        ImageGif($dst_img, $save_path.$save_filename);
    } elseif ($img_info[2] == 2) {
        ImageInterlace($dst_img);
        ImageJPEG($dst_img, $save_path.$save_filename);
    } elseif ($img_info[2] == 3) {
        ImagePNG($dst_img, $save_path.$save_filename);
    }
    @ImageDestroy($dst_img);
    @ImageDestroy($src_img);
}

function thumnail_width($file, $save_filename, $save_path, $max_width)
{
    $img_info = getimagesize($file);
    if ($img_info[2] == 1) {
        $src_img = ImageCreateFromGif($file);
    } elseif ($img_info[2] == 2) {
        $src_img = ImageCreateFromJPEG($file);
    } elseif ($img_info[2] == 3) {
        $src_img = ImageCreateFromPNG($file);
    } else {
        return 0;
    }

    $img_width = $img_info[0];
    $img_height = $img_info[1];

    $dst_width = $max_width;
    $dst_height = round($dst_width * ($img_height / $img_width));

    $srcx = 0;
    $srcy = 0;

    if ($img_info[2] == 1) {
        $dst_img = imagecreate($dst_width, $dst_height);
    } else {
        $dst_img = imagecreatetruecolor($dst_width, $dst_height);
    }

    ImageCopyResampled($dst_img, $src_img, $srcx, $srcy, 0, 0, $dst_width, $dst_height, ImageSX($src_img), ImageSY($src_img));

    if ($img_info[2] == 1) {
        ImageInterlace($dst_img);
        ImageGif($dst_img, $save_path.$save_filename);
    } elseif ($img_info[2] == 2) {
        ImageInterlace($dst_img);
        ImageJPEG($dst_img, $save_path.$save_filename);
    } elseif ($img_info[2] == 3) {
        ImagePNG($dst_img, $save_path.$save_filename);
    }
    @ImageDestroy($dst_img);
    @ImageDestroy($src_img);
}

function thumbnail_crop_center($file, $save_filename, $save_path, $max_width, $max_height)
{
    //사이즈에 맞춰 채워 넣는 방식으로 수정, 아래 scale_image_fill 함수 참고 2015-04-21 이창민
    $img_info = getimagesize($file);

    if ($img_info[2] == 1) {
        $src = ImageCreateFromGif($file);
    } elseif ($img_info[2] == 2) {
        $src = ImageCreateFromJPEG($file);
    } elseif ($img_info[2] == 3) {
        $src = ImageCreateFromPNG($file);
    } else {
        return 0;
    }

    $dst = imagecreatetruecolor($max_width, $max_height);
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));

    $src_width = imagesx($src);
    $src_height = imagesy($src);

    $dst_width = imagesx($dst);
    $dst_height = imagesy($dst);

    $new_width = $dst_width;
    $new_height = round($new_width * ($src_height / $src_width));
    $new_x = 0;
    $new_y = round(($dst_height - $new_height) / 2);

    $next = $new_height < $dst_height;

    if ($next) {
        $new_height = $dst_height;
        $new_width = round($new_height * ($src_width / $src_height));
        $new_x = round(($dst_width - $new_width) / 2);
        $new_y = 0;
    }

    imagecopyresampled($dst, $src, $new_x, $new_y, 0, 0, $new_width, $new_height, $src_width, $src_height);

    if ($img_info[2] == 1) {
        ImageInterlace($dst);
        ImageGif($dst, $save_path.$save_filename);
    } elseif ($img_info[2] == 2) {
        ImageInterlace($dst);
        ImageJPEG($dst, $save_path.$save_filename);
    } elseif ($img_info[2] == 3) {
        ImagePNG($dst, $save_path.$save_filename);
    }

    @ImageDestroy($dst_img);
    @ImageDestroy($src_img);
}

function scale_image_fill($src_image, $save_filename, $save_path, $max_width, $max_height)
{
    $img_info = getimagesize($src_image);

    if ($img_info[2] == 1) {
        $src = ImageCreateFromGif($src_image);
    } elseif ($img_info[2] == 2) {
        $src = ImageCreateFromJPEG($src_image);
    } elseif ($img_info[2] == 3) {
        $src = ImageCreateFromPNG($src_image);
    } else {
        return 0;
    }

    $dst = imagecreatetruecolor($max_width, $max_height);
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));

    $src_width = imagesx($src);
    $src_height = imagesy($src);

    $dst_width = imagesx($dst);
    $dst_height = imagesy($dst);

    $new_width = $dst_width;
    $new_height = round($new_width * ($src_height / $src_width));
    $new_x = 0;
    $new_y = round(($dst_height - $new_height) / 2);

    $next = $new_height < $dst_height;

    if ($next) {
        $new_height = $dst_height;
        $new_width = round($new_height * ($src_width / $src_height));
        $new_x = round(($dst_width - $new_width) / 2);
        $new_y = 0;
    }

    imagecopyresampled($dst, $src, $new_x, $new_y, 0, 0, $new_width, $new_height, $src_width, $src_height);

    if ($img_info[2] == 1) {
        ImageInterlace($dst);
        ImageGif($dst, $save_path.$save_filename);
    } elseif ($img_info[2] == 2) {
        ImageInterlace($dst);
        ImageJPEG($dst, $save_path.$save_filename);
    } elseif ($img_info[2] == 3) {
        ImagePNG($dst, $save_path.$save_filename);
    }

    @ImageDestroy($dst_img);
    @ImageDestroy($src_img);
}

function scale_image_fit($src_image, $save_filename, $save_path, $max_width, $max_height)
{
    $img_info = getimagesize($src_image);

    if ($img_info[2] == 1) {
        $src = ImageCreateFromGif($src_image);
    } elseif ($img_info[2] == 2) {
        $src = ImageCreateFromJPEG($src_image);
    } elseif ($img_info[2] == 3) {
        $src = ImageCreateFromPNG($src_image);
    } else {
        return 0;
    }

    $dst = imagecreatetruecolor($max_width, $max_height);
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));

    $src_width = imagesx($src);
    $src_height = imagesy($src);

    $dst_width = imagesx($dst);
    $dst_height = imagesy($dst);

    $new_width = $dst_width;
    $new_height = round($new_width * ($src_height / $src_width));
    $new_x = 0;
    $new_y = round(($dst_height - $new_height) / 2);

    $next = $new_height > $dst_height;

    if ($next) {
        $new_height = $dst_height;
        $new_width = round($new_height * ($src_width / $src_height));
        $new_x = round(($dst_width - $new_width) / 2);
        $new_y = 0;
    }

    imagecopyresampled($dst, $src, $new_x, $new_y, 0, 0, $new_width, $new_height, $src_width, $src_height);

    if ($img_info[2] == 1) {
        ImageInterlace($dst);
        ImageGif($dst, $save_path.$save_filename);
    } elseif ($img_info[2] == 2) {
        ImageInterlace($dst);
        ImageJPEG($dst, $save_path.$save_filename);
    } elseif ($img_info[2] == 3) {
        ImagePNG($dst, $save_path.$save_filename);
    }

    @ImageDestroy($dst_img);
    @ImageDestroy($src_img);
}

function encrypt($str, $key)
{
    # Add PKCS7 padding.
    $block = mcrypt_get_block_size('des', 'ecb');
    if (($pad = $block - (strlen($str) % $block)) < $block) {
        $str .= str_repeat(chr($pad), $pad);
    }

    return mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
}

function decrypt($str, $key)
{
    $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);

    # Strip padding out.
    $block = mcrypt_get_block_size('des', 'ecb');
    $pad = ord($str[($len = strlen($str)) - 1]);
    if ($pad && $pad < $block && preg_match(
        '/' . chr($pad) . '{' . $pad . '}$/',
        $str
    )
    ) {
        return substr($str, 0, strlen($str) - $pad);
    }
    return $str;
}

function get_openssl_encrypt($data)
{
    $pass = DECODEKEY;
    $iv = DECODEKEY;

    $endata = @openssl_encrypt($data, "aes-256-cbc", $pass, true, $iv);
    $endata = base64_encode($endata);

    return $endata;
}

function get_openssl_decrypt($endata)
{
    $pass = DECODEKEY;
    $iv = DECODEKEY;

    $data = base64_decode($endata);
    $dedata = @openssl_decrypt($data, "aes-256-cbc", $pass, true, $iv);

    return $dedata;
}

function get_text($str)
{
    $source[] = "/</";
    $target[] = "&lt;";
    $source[] = "/>/";
    $target[] = "&gt;";
    $source[] = "/\'/";
    $target[] = "&#039;";

    return preg_replace($source, $target, strip_tags($str));
}

function cal_remain_days($s_date, $e_date)
{
    if ($e_date == "") {
        return "0";
    }

    $s_date_ex = explode(" ", $s_date);
    $s_date_ex2 = explode("-", $s_date_ex[0]);
    $s_date_ex3 = explode(":", $s_date_ex[1]);
    $e_date_ex = explode(" ", $e_date);
    $e_date_ex2 = explode("-", $e_date_ex[0]);
    $e_date_ex3 = explode(":", $e_date_ex[1]);

    $s_time = mktime($s_date_ex3[0], $s_date_ex3[1], $s_date_ex3[2], $s_date_ex2[1], $s_date_ex2[2], $s_date_ex2[0]);
    $e_time = mktime($e_date_ex3[0], $e_date_ex3[1], $e_date_ex3[2], $e_date_ex2[1], $e_date_ex2[2], $e_date_ex2[0]);

    if ($s_time > $e_time) {
        return 1;
    } else {
        $result_time = ($e_time - $s_time) / (60 * 60 * 24);

        if ($result_time < 0) {
            return 1;
        } else {
            return round($result_time);
        }
    }
}

function cal_remain_days2($s_date, $e_date)
{
    if ($e_date == "") {
        return "0";
    }

    $s_date_ex = explode(" ", $s_date);
    $s_date_ex2 = explode("-", $s_date_ex[0]);
    $s_date_ex3 = explode(":", $s_date_ex[1]);
    $e_date_ex = explode(" ", $e_date);
    $e_date_ex2 = explode("-", $e_date_ex[0]);
    $e_date_ex3 = explode(":", $e_date_ex[1]);

    $s_time = mktime(0, 0, 0, $s_date_ex2[1], $s_date_ex2[2], $s_date_ex2[0]);
    $e_time = mktime(23, 59, 59, $e_date_ex2[1], $e_date_ex2[2], $e_date_ex2[0]);

    if ($s_time > $e_time) {
        $rtn = 0;
    } else {
        $result_time = ($e_time - $s_time) / (60 * 60 * 24);

        $rtn = round($result_time);
    }

    return $rtn;
}

function cal_remain_times($s_date, $e_date)
{
    if ($e_date == "") {
        return "0";
    }

    $s_date_ex = explode(" ", $s_date);
    $s_date_ex2 = explode("-", $s_date_ex[0]);
    $s_date_ex3 = explode(":", $s_date_ex[1]);
    $e_date_ex = explode(" ", $e_date);
    $e_date_ex2 = explode("-", $e_date_ex[0]);
    $e_date_ex3 = explode(":", $e_date_ex[1]);

    $s_time = mktime($s_date_ex3[0], $s_date_ex3[1], $s_date_ex3[2], $s_date_ex2[1], $s_date_ex2[2], $s_date_ex2[0]);
    $e_time = mktime($e_date_ex3[0], $e_date_ex3[1], $e_date_ex3[2], $e_date_ex2[1], $e_date_ex2[2], $e_date_ex2[0]);

    $result_time = ($e_time - $s_time);

    return $result_time;
}

function make_mktime($date)
{
    $date_ex = explode(" ", $date);
    $date_ex2 = explode("-", $date_ex[0]);
    $date_ex3 = explode(":", $date_ex[1]);

    if($date_ex[1]) {
        $s_time = mktime($date_ex3[0], $date_ex3[1], $date_ex3[2], $date_ex2[1], $date_ex2[2], $date_ex2[0]);
    } else {
        $s_time = mktime(0, 0, 0, $date_ex2[1], $date_ex2[2], $date_ex2[0]);
    }

    return $s_time;
}

function quote2entities($string, $entities_type = 'number')
{
    $search = array("\"","'");
    $replace_by_entities_name = array("&quot;","&apos;");
    $replace_by_entities_number = array("&#34;","&#39;");
    $do = null;
    if ($entities_type == 'number') {
        $do = str_replace($search, $replace_by_entities_number, $string);
    } elseif ($entities_type == 'name') {
        $do = str_replace($search, $replace_by_entities_name, $string);
    } else {
        $do = addslashes($string);
    }

    return $do;
}

function printr($arr_val)
{
    echo "<pre>";
    print_r($arr_val);
    echo "</pre>";
}

function fnc_Day_Name($strDate)
{
    $strDate = substr($strDate, 0, 10);
    $days = array("일","월","화","수","목","금","토");
    $temp_day = date("w", strtotime($strDate));
    return $days[$temp_day];
}

function TimeType($time_t)
{
    $hour = date("H", strtotime($time_t));
    $min  = date("i", strtotime($time_t));

    if ($hour > 12) {
        $hour = $hour - 12;
        $result = "오후 " . $hour. ":". $min;
    } else {
        $result = "오전 " . $hour. ":". $min;
    }

    return $result;
}

function DateType($strDate, $type = "1")
{
    if ($strDate == "" || $strDate == "0000-00-00 00:00:00") {
        $strDate = "-";
    } else {
        if ($type == "1") {
            $strDate = str_replace("-", ".", substr($strDate, 0, 10));
        } elseif ($type == "2") {
            $strDate = str_replace("-", ".", substr($strDate, 0, 16));
        } elseif ($type == "3") {
            $strDate = str_replace("-", ".", substr($strDate, 0, 10))."&nbsp;(".fnc_Day_Name($strDate).")";
        } elseif ($type == "4") {
            $strDate = str_replace("-", ".", substr($strDate, 0, 10))."&nbsp;(".fnc_Day_Name($strDate).")&nbsp;".substr($strDate, 11, 5);
        } elseif ($type == "5") {
            $strDate = str_replace("-", ".", substr($strDate, 2, 8));
        } elseif ($type == "6") {
            $strDate = str_replace("-", ".", substr($strDate, 2, 8))."&nbsp;(".fnc_Day_Name($strDate).")&nbsp;".substr($strDate, 11, 5);
        } elseif ($type == "7") {
            $strDate = substr($strDate, 11, 5);
        } elseif ($type == "8") {
            $strDate = str_replace("-", ".", substr($strDate, 2, 8))."&nbsp;".substr($strDate, 11, 5);
        } elseif ($type == "9") {
            $strDate = str_replace("-", ".", substr($strDate, 2, 8))."&nbsp;(".fnc_Day_Name($strDate).")<br/>".substr($strDate, 11, 5);
        } elseif ($type == "10") {
            $strDate = str_replace("-", "년 ", substr($strDate, 2, 5))."월";
        } elseif ($type == "11") {
            $strDate_ex1 = explode(' ', $strDate);
            $strDate_ex2 = explode('-', $strDate_ex1[0]);

            $strDate = $strDate_ex2[0]."년 ".$strDate_ex2[1]."월 ".$strDate_ex2[2]."일&nbsp;(".fnc_Day_Name($strDate).")";
        } elseif ($type == "12") {
            $strDate = str_replace("-", ".", substr($strDate, 2, 8))."&nbsp;(".fnc_Day_Name($strDate).")";
        } elseif ($type == "13") {
            $strDate_ex1 = explode(' ', $strDate);
            $strDate_ex2 = explode('-', $strDate_ex1[0]);

            $strDate = $strDate_ex2[1]."월 ".$strDate_ex2[2]."일"."&nbsp;".fnc_Day_Name($strDate)."요일<br>".TimeType($strDate_ex1[1]);
        } elseif ($type == "14") {
            $strDate_ex1 = explode(' ', $strDate);
            $strDate_ex2 = explode('-', $strDate_ex1[0]);

            $strDate = $strDate_ex2[1]."월 ".$strDate_ex2[2]."일"."&nbsp;(".fnc_Day_Name($strDate).")";
        } elseif ($type == "15") {
            $strDate_ex1 = explode(' ', $strDate);

            $strDate = TimeType($strDate_ex1[1]);
        } elseif ($type == "16") {
            $strDate_ex1 = explode(' ', $strDate);
            $strDate_ex2 = explode('-', $strDate_ex1[0]);

            $strDate = $strDate_ex2[1]."월 ".$strDate_ex2[2]."일"."&nbsp;".fnc_Day_Name($strDate)."요일 ".TimeType($strDate_ex1[1]);
        } elseif ($type == "17") {
            $strDate_ex1 = explode(' ', $strDate);
            $strDate_ex2 = explode('-', $strDate_ex1[0]);

            $strDate = fnc_Day_Name($strDate)."요일 ".TimeType($strDate_ex1[1]);
        } elseif ($type == "18") {
            $strDate = substr($strDate, 0, 16);
        } elseif ($type == "19") {
            $strDate = str_replace("-", ".", substr($strDate, 0, 10))." (".fnc_Day_Name($strDate).")";
        } elseif ($type == "20") {
            $strDate_ex1 = explode(' ', $strDate);
            $strDate_ex2 = explode('-', $strDate_ex1[0]);

            if(substr($strDate_ex2[1], 1) < 10) {
                $strDate_ex2[1] = str_replace('0', '', $strDate_ex2[1]);
            }
            if(substr($strDate_ex2[2], 1) < 10) {
                $strDate_ex2[2] = str_replace('0', '', $strDate_ex2[2]);
            }

            $strDate = $strDate_ex2[1]."월 ".$strDate_ex2[2]."일"."&nbsp;(".fnc_Day_Name($strDate).")";
        } elseif ($type == "21") {
            $strDate_ex1 = explode(' ', $strDate);
            $strDate_ex2 = explode('-', $strDate_ex1[0]);

            $strDate = $strDate_ex2[0] . "년 " . $strDate_ex2[1] . "월 " . $strDate_ex2[2] . "일";
        }
    }

    return $strDate;
}

function substr_star($str)
{
    $str_len = mb_strlen($str);
    $str_arr = str_split($str);

    $result = "";
    for ($i = 0 ; $i < $str_len ; $i++) {
        if ($i < 3) {
            $result .= $str_arr[$i];
        } else {
            $result .= "*";
        }
    }
    return $result;
}

function mt_pw_make()
{
    return substr(md5(time()), 0, 8);
}

function mt_sms_make()
{
    return mt_rand(111111, 999999);
}

function save_remote_img_curl_fn($url, $dir, $tmpname)
{
    $filename = '';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    curl_exec($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($http_code == 200) {
        $filename = basename($url);
        if (preg_match("/\.(gif|jpg|jpeg|png)$/i", $filename)) {
            $filepath = $dir;
            @mkdir($filepath, '0755');
            @chmod($filepath, '0755');

            // 파일 다운로드
            $path = $filepath.'/'.$tmpname;
            $fp = fopen($path, 'w');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_exec($ch);
            curl_close($ch);

            fclose($fp);

            // 다운로드 파일이 이미지인지 체크
            if (is_file($path)) {
                $size = @getimagesize($path);
                if ($size[2] < 1 || $size[2] > 3) {
                    @unlink($path);
                    $filename = '';
                } else {
                    $ext = array(1 => 'gif', 2 => 'jpg', 3 => 'png');
                    $filename = $tmpname.'.'.$ext[$size[2]];
                    rename($path, $filepath.'/'.$filename);
                    //@chmod($filepath.'/'.$filename, '0644');
                }
            }
        }
    }

    return $filename;
}

function save_remote_img_curl($url, $dir, $mt_idx)
{
    $filename = '';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    curl_exec($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($http_code == 200) {
        $filename = basename($url);
        if (preg_match("/\.(gif|jpg|jpeg|png)$/i", $filename)) {
            //$tmpname = date('YmdHis').(microtime(true) * 10000);
            $tmpname = "mt_img_".$mt_idx."_".date("YmdHis");
            $filepath = $dir;
            @mkdir($filepath, '0755');
            @chmod($filepath, '0755');

            // 파일 다운로드
            $path = $filepath.'/'.$tmpname;
            $fp = fopen($path, 'w');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_exec($ch);
            curl_close($ch);

            fclose($fp);

            // 다운로드 파일이 이미지인지 체크
            if (is_file($path)) {
                $size = @getimagesize($path);
                if ($size[2] < 1 || $size[2] > 3) {
                    @unlink($path);
                    $filename = '';
                } else {
                    $ext = array(1 => 'gif', 2 => 'jpg', 3 => 'png');
                    $filename = $tmpname.'.'.$ext[$size[2]];
                    rename($path, $filepath.'/'.$filename);
                    @chmod($filepath.'/'.$filename, '0644');
                }
            }
        }
    }

    return $filename;
}

function save_remote_img_file($url, $dir, $mt_idx)
{
    $filename = file_get_contents($url);
    $img_info = pathinfo($url);
    $tmpname = "mt_img_".$mt_idx."_".date("YmdHis").'.'.$img_info[extension];
    file_put_contents($dir."/".$tmpname, $filename);

    return $tmpname;
}

function save_facebook_profile_img($url, $dir, $mt_idx)
{
    $filename = '';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    curl_exec($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($http_code == 200) {
        $filename = basename($url);
        $filename_ex = explode("?", $filename);
        $filename = $filename_ex[0];
        if (preg_match("/\.(gif|jpg|jpeg|png)$/i", $filename)) {
            //$tmpname = date('YmdHis').(microtime(true) * 10000);
            $tmpname = "mt_img_".$mt_idx."_".date("YmdHis");
            $filepath = $dir;
            @mkdir($filepath, '0755');
            @chmod($filepath, '0755');

            // 파일 다운로드
            $path = $filepath.'/'.$tmpname;
            $fp = fopen($path, 'w');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_exec($ch);
            curl_close($ch);

            fclose($fp);

            // 다운로드 파일이 이미지인지 체크
            if (is_file($path)) {
                $size = @getimagesize($path);
                if ($size[2] < 1 || $size[2] > 3) {
                    @unlink($path);
                    $filename = '';
                } else {
                    $ext = array(1 => 'gif', 2 => 'jpg', 3 => 'png');
                    $filename = $tmpname.'.'.$ext[$size[2]];
                    rename($path, $filepath.'/'.$filename);
                    //@chmod($filepath.'/'.$filename, '0644');
                }
            }
        }
    }

    return $filename;
}

function inconv_post($s1, $s2, $arr)
{
    foreach ($arr as $key => $val) {
        $arr[$key] = iconv($s1, $s2, $val);
    }

    return $arr;
}

function date_diffrent($sdate, $edate)
{
    $date1 = new DateTime($sdate);
    $date2 = new DateTime($edate);
    $diff = date_diff($date1, $date2);

    $return = "";
    if ($diff->days == 0) {
        if ($diff->d == 0) {
            if ($diff->h == 0) {
                if ($diff->i == 0) {
                    $return = $diff->s."초";
                } else {
                    $return = $diff->i."분";
                }
            } else {
                $return = $diff->h."시";
            }
        }
    } else {
        if ($diff->days > 7) {
            $return = round($diff->days / 7)."주";
        } else {
            $return = $diff->days."일";
        }
    }

    return $return;
}

function save_parsing_img($url, $dir, $pt_size, $bt_idx, $img_num)
{
    $filename = '';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    curl_exec($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($http_code == 200) {
        $filename = basename($url);
        $filename_ex = explode("?", $filename);
        $filename = $filename_ex[0];
        if (preg_match("/\.(gif|jpg|jpeg|png)$/i", $filename)) {
            //$tmpname = date('YmdHis').(microtime(true) * 10000);
            $tmpname = "pt_img_".$pt_size."_".$bt_idx."_".$img_num;
            $filepath = $dir;
            @mkdir($filepath, '0755');
            @chmod($filepath, '0755');

            // 파일 다운로드
            $path = $filepath.'/'.$tmpname;
            $fp = fopen($path, 'w');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_exec($ch);
            curl_close($ch);

            fclose($fp);

            // 다운로드 파일이 이미지인지 체크
            if (is_file($path)) {
                $size = @getimagesize($path);
                if ($size[2] < 1 || $size[2] > 3) {
                    @unlink($path);
                    $filename = '';
                } else {
                    $ext = array(1 => 'gif', 2 => 'jpg', 3 => 'png');
                    $filename = $tmpname.'.'.$ext[$size[2]];
                    rename($path, $filepath.'/'.$filename);
                    //@chmod($filepath.'/'.$filename, '0644');
                }
            }
        }
    }

    return $filename;
}

function save_owner_img($url, $dir, $pt_barcode, $pt_idx)
{
    $rtn_filename = '';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    curl_exec($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($http_code == 200) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        $raw = curl_exec($ch);
        curl_close($ch);

        if (stristr($url, 'product_image.php')) {
            $url_ex = explode("?img=", $url);
            $filename = $url_ex[1];
        } else {
            $url_info = pathinfo($url);
            $filename = $url_info[basename];
        }

        $path = $dir."/".$filename;

        $fp = fopen($path, 'w');
        fwrite($fp, $raw);
        fclose($fp);

        if (is_file($path)) {
            $size = @getimagesize($path);
            if ($size[2] < 1 || $size[2] > 3) {
                @unlink($path);
                $rtn_filename = '';
            } else {
                $ext = array(1 => 'gif', 2 => 'jpg', 3 => 'png');
                $rtn_filename = $pt_barcode."_".$pt_idx.'.'.$ext[$size[2]];
                rename($path, $dir.'/'.$rtn_filename);
            }
        }
    }

    return $rtn_filename;
}

function get_file_url($file_nm)
{
    global $ct_no_img_url, $ct_img_url, $ct_img_dir;

    if ($file_nm == "http") {
        $rtn = strip_tags($file_nm);
    } else {
        if (is_file($ct_img_dir."/".$file_nm)) {
            $rtn = $ct_img_url."/".$file_nm;
        } else {
            $rtn = $ct_no_img_url;
        }
    }

    return $rtn;
}

function save_url_img($url, $dir, $tmp_nm)
{
    $filename = '';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    curl_exec($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($http_code == 200) {
        $filename = basename($url);
        $filename_ex = explode("?", $filename);
        $filename = $filename_ex[0];
        if (preg_match("/\.(gif|jpg|jpeg|png)$/i", $filename)) {
            $tmpname = $tmp_nm;
            $filepath = $dir;
            //				@mkdir($filepath, '0755');
            //				@chmod($filepath, '0755');

            // 파일 다운로드
            $path = $filepath.'/'.$tmpname;
            $fp = fopen($path, 'w');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_exec($ch);
            curl_close($ch);

            fclose($fp);

            // 다운로드 파일이 이미지인지 체크
            if (is_file($path)) {
                $size = @getimagesize($path);
                if ($size[2] < 1 || $size[2] > 3) {
                    @unlink($path);
                    $filename = '';
                } else {
                    $ext = array(1 => 'gif', 2 => 'jpg', 3 => 'png');
                    $filename = $tmpname.'.'.$ext[$size[2]];
                    rename($path, $filepath.'/'.$filename);
                    //@chmod($filepath.'/'.$filename, '0644');
                }
            }
        }
    }

    return $filename;
}

function f_curl_post($url, $code)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "selfcode=".$code);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $rtn = curl_exec($ch);
    curl_close($ch);

    return $rtn;
}

function f_curl_post_field($url, $field)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $field);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $rtn = curl_exec($ch);
    curl_close($ch);

    return $rtn;
}

function ex_title_chk($title)
{
    global $arr_ex_title;

    $q = 0;
    foreach ($arr_ex_title as $key => $val) {
        if (strstr($title, $val)) {
            $q++;
        }
    }

    if ($q > 0) {
        return "";
    } else {
        return $title;
    }
}

function get_time()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function format_phone($phone)
{
    $phone = preg_replace("/[^0-9]/", "", $phone);
    $length = strlen($phone);

    switch($length) {
        case 11:
            return preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $phone);
            break;
        case 10:
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
            break;
        case 9:
            return preg_replace("/([0-9]{2})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
            break;
        default:
            return $phone;
            break;
    }
}

function delete_all($dir)
{
    $d = @dir($dir);
    while ($entry = $d->read()) {
        if ($entry == "." || $entry == "..") {
            continue;
        }
        if (is_dir($entry)) {
            delete_all($entry);
        } else {
            unlink($dir."/".$entry);
        }
    }
}

function get_datetime_diff($sdatetime, $edatetime)
{
    $sdate  = new DateTime($sdatetime);
    $edate  = new DateTime($edatetime);

    $rtn = $edate->format('U') - $sdate->format('U');

    return $rtn;
}

function recusive_category($_level, $_pid)
{
    global $DB, $ct_img_url, $ct_no_img_url;

    unset($list);
    $query = "select * from category_t where ct_level = '".$_level."' and ct_pid = '".$_pid."' order by ct_rank asc, ct_id asc, ct_name asc";
    $list = $DB->select_query($query);

    if ($list) {
        foreach ($list as $row) {
            $s_level = "";
            if ($row['ct_level']) {
                $s_level = "&nbsp;&nbsp;&nbsp;┗";
                for ($i = 1; $i < $row['ct_level']; $i++) {
                    $s_level = "&nbsp;&nbsp;&nbsp;".$s_level;
                }
            }

            if ($row['ct_level'] == 0) {
                $ct_name_t = get_text($row['ct_name']);
            } else {
                $ct_name_t = $s_level.'&nbsp;'.get_text($row['ct_name']);
            }

            if ($row['ct_level'] == 0) {
                $s_add = "<a href='./category_form.php?act=add&ct_idx=".$row['ct_id']."&ct_level=".$row['ct_level']."' class='btn btn-outline-secondary btn-sm mx-sm-1'>추가</a>";
            }
            $s_mod = "<a href='./category_form.php?act=update&ct_idx=".$row['ct_id']."' class='btn btn-outline-primary btn-sm mx-sm-1'>수정</a>";
            $s_del = "<a href='javascript:;' onclick=\"f_post_del('./category_update.php', '".$row['ct_id']."')\" class='btn btn-outline-danger btn-sm mx-sm-1'>삭제</a>";

            echo "<tr>
<td>".$ct_name_t."</td>
<td class='text-center'>".$row['ct_level']."</td>
<td class='text-center'>".$row['ct_rank']."</td>
<td class='text-center'>".$s_add."&nbsp;".$s_mod."&nbsp;".$s_del."</td>
</tr>";

            recusive_category($row['ct_level'] + 1, $row['ct_id']);
        }
    }

    return false;
}

function recusive_ca_name($ct_id)
{
    global $DB;

    $arr_ca_name = array();

    $query = "select * from category_t where ct_id = '".$ct_id."'";
    $row = $DB->fetch_query($query);

    if ($row['ct_pid'] == '0') {
        return $row['ct_name'];
    } else {
        if ($row['ct_pid']) {
            return $row['ct_name']."|".recusive_ca_name($row['ct_pid']);
        } else {
            return $row['ct_name'];
        }
    }
}

function recusive_ca_id($ct_id)
{
    global $DB;

    $arr_ca_name = array();

    $query = "select * from category_t where ct_id = '".$ct_id."'";
    $row = $DB->fetch_query($query);

    if ($row['ct_id']) {
        if ($row['ct_pid'] == '0') {
            return $row['ct_id'];
        } else {
            return $row['ct_id']."|".recusive_ca_id($row['ct_pid']);
        }
    } else {
        return;
    }
}

function get_ca_name_breadcrumb_mng($ct_id)
{
    $ca_name_t = recusive_ca_id($ct_id);
    $ca_name_t_ex = explode('|', $ca_name_t);
    krsort($ca_name_t_ex);

    $arr_ct_name_t = array();
    if ($ca_name_t_ex) {
        foreach ($ca_name_t_ex as $key => $val) {
            $ct_info = get_category_info($val);

            $arr_ct_name_t[] = $ct_info['ct_name'];
        }
    }

    $arr_ct_name_t_im = implode(' > ', $arr_ct_name_t);

    return $arr_ct_name_t_im;
}

function get_ca_name_breadcrumb($ct_id)
{
    $ca_name_t = recusive_ca_id($ct_id);
    $ca_name_t_ex = explode('|', $ca_name_t);
    krsort($ca_name_t_ex);
    //$ca_name_t_ex_im = implode('|:|', $ca_name_t_ex);

    return $ca_name_t_ex;
}

function get_member_t_info($mt_idx = "")
{
    global $DB, $_SESSION;

    if($mt_idx) {
        $DB->where('mt_idx', $mt_idx);
    } else {
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
    }
    $row = $DB->getone('member_t');

    return $row;
}
function f_get_owner_cnt($mt_idx = "")
{
    global $DB, $_SESSION;

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

        if ($mem_row['mt_level'] == 2 && $mem_row['mt_plan_check'] == 'N') {
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

            // 내장소 오래된 2개 빼고 숨김처리
            if ($sgdt_list) {
                foreach ($sgdt_list as $sgdt_row) {
                    unset($list_slt);
                    $DB->where("( mt_idx = '" . $sgdt_row['mt_idx'] . "' or sgdt_idx = '" . $sgdt_row['sgdt_idx'] . "' )");
                    $DB->where('slt_show', 'Y');
                    $DB->orderby('slt_wdate', 'asc');
                    $list_slt = $DB->get('smap_location_t');
                    // 내장소가 2개 이상일 때
                    if (count($list_slt) >= 2) {
                        // 내장소 오래된 2개 빼고 숨김처리
                        for ($i = 2; $i < count($list_slt); $i++) {
                            unset($arr_query);
                            $arr_query = array(
                                "slt_show" => 'N',
                                "slt_ddate" => $DB->now(),
                                "slt_udate" => $DB->now(),
                            );

                            $DB->where('slt_idx', $list_slt[$i]['slt_idx']);
                            $DB->update('smap_location_t', $arr_query);
                        }
                    }
                }

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
    }
    return $sgt_cnt;
}
function f_get_leader_cnt($mt_idx = "")
{
    global $DB, $_SESSION;

    if ($mt_idx) {
        $DB->where('mt_idx', $mt_idx);
    } else {
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
    }
    $DB->where('sgdt_owner_chk', 'N');
    $DB->where('sgdt_leader_chk', 'Y');
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');

    $sgdt_leader_cnt = $row['cnt'];
    return $sgdt_leader_cnt;
}
function f_group_invite_cnt($mt_idx = "")
{
    global $DB, $_SESSION;

    if ($mt_idx) {
        $DB->where('mt_idx', $mt_idx);
    } else {
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
    }
    $DB->where('sgdt_owner_chk', 'N');
    $DB->where('sgdt_show', 'Y');
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $row = $DB->getone('smap_group_detail_t', 'count(*) as cnt');
    $sgdt_cnt = $row['cnt'];

    return $sgdt_cnt;
}
function f_group_info($mt_idx = "")
{
    global $DB, $_SESSION;

    if ($mt_idx) {
        $DB->where('mt_idx', $mt_idx);
    } else {
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
    }
    $DB->where('sgt_show', 'Y');
    $sgt_row = $DB->getone('smap_group_t');

    return $sgt_row;
}

function get_member_location_log_t_info($mt_idx = "", $sdate = "")
{
    global $DB, $_SESSION;

    if($mt_idx) {
        $DB->where('mt_idx', $mt_idx);
    } else {
        $DB->where('mt_idx', $_SESSION['_mt_idx']);
    }
    if($sdate == '') {
        $sdate = date("Y-m-d");
    }
    $DB->where("mlt_wdate between '".$sdate." 00:00:00' and '".$sdate." 23:59:59'");
    $DB->orderBy("mlt_idx", "desc");
    $row = $DB->getone('member_location_log_t');

    return $row;
}

function get_setup_t_info()
{
    global $DB;

    $DB->where('st_idx', '1');
    $row = $DB->getone('setup_t');

    return $row;
}

function get_category_info($ct_id)
{
    global $DB;

    $query = "select * from category_t where ct_id = '".$ct_id."'";
    $row = $DB->fetch_query($query);

    return $row;
}

function get_bootom_ct_id($ct_id)
{
    global $DB;

    $query = "select * from category_bottom_all where ct_id = '".$ct_id."'";
    $row = $DB->fetch_query($query);

    return $row['ct_id_txt'];
}

function get_bottom_all($ct_id)
{
    global $DB;

    unset($list);
    $query = "select * from category_t where ct_pid = '".$ct_id."'";
    $list = $DB->select_query($query);

    $arr_ct_id_txt = array();
    $arr_ct_id_txt[] = $ct_id;
    if ($list) {
        foreach ($list as $row) {
            if ($row['ct_id']) {
                $arr_ct_id_txt[] = $row['ct_id'];

                unset($list2);
                $query2 = "select * from category_t where ct_pid = '".$row['ct_id']."'";
                $list2 = $DB->select_query($query2);

                if ($list2) {
                    foreach ($list2 as $row2) {
                        if ($row2['ct_id']) {
                            $arr_ct_id_txt[] = $row2['ct_id'];

                            unset($list3);
                            $query3 = "select * from category_t where ct_pid = '".$row2['ct_id']."'";
                            $list3 = $DB->select_query($query3);

                            if ($list3) {
                                foreach ($list3 as $row3) {
                                    if ($row3['ct_id']) {
                                        $arr_ct_id_txt[] = $row3['ct_id'];

                                        unset($list4);
                                        $query4 = "select * from category_t where ct_pid = '".$row3['ct_id']."'";
                                        $list4 = $DB->select_query($query4);

                                        if ($list4) {
                                            foreach ($list4 as $row4) {
                                                if ($row4['ct_id']) {
                                                    $arr_ct_id_txt[] = $row4['ct_id'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return $arr_ct_id_txt;
}

function send_notification($token_list, $title, $message, $clickAction = "", $content_idx = "")
{
    //FCM 인증키
    $FCM_KEY = 'YwcwKQsw3bZgQ1t99myhg_pJ_Ml7PbD1RvzT5zt2rCE';
    //FCM 전송 URL
    $FCM_URL = 'https://fcm.googleapis.com/v1/projects/myproject-b5ae1/messages:send';

    //전송 데이터
    $fields = array(
    'registration_ids' => $token_list,
    'data' => array(
    'title' => $title,
    'message' => $message,
    'intent' => $clickAction,
    'content_idx' => $content_idx,
    ),
    'notification' => array(
    'title' => $title,
    'body' => $message,
    'content_idx' => $content_idx,
    'badge' => 1,
    ),
    );

    //설정
    $headers = array( 'Authorization: Bearer ya29'. $FCM_KEY, 'Content-Type:application/json' );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $FCM_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === false) {
        die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);
    $obj = json_decode($result);

    return $obj;
}

function mt_id_pad($mt_id)
{
    return str_pad(cut_str($mt_id, 0, 3, ''), 7, '****');
}

function get_image_url_mng($pt_image, $no_cache = "", $no_img = "")
{
    global $ct_no_img_url, $ct_img_url, $ct_img_dir_a;

    if ($no_img) {
        $no_img_t = $no_img;
    } else {
        $no_img_t = $ct_no_img_url;
    }

    if (is_file($ct_img_dir_a."/".$pt_image)) {
        if ($no_cache == 'Y') {
            $rtn = $ct_img_url."/".$pt_image."?v=".time();
        } else {
            $rtn = $ct_img_url."/".$pt_image;
        }
    } else {
        $rtn = $no_img_t;
    }

    return $rtn;
}

function get_image_url($pt_image, $no_cache = "", $no_img = "")
{
    global $ct_no_img_url, $ct_img_url, $ct_img_dir;

    if ($no_img) {
        $no_img_t = $no_img;
    } else {
        $no_img_t = $ct_no_img_url;
    }

    if (is_file($ct_img_dir . "/" . $pt_image)) {
        if ($no_cache == 'Y') {
            $rtn = $ct_img_url . "/" . $pt_image . "?v=" . time();
        } else {
            $rtn = $ct_img_url . "/" . $pt_image;
        }
    } else {
        $rtn = $no_img_t;
    }

    return $rtn;
}

function get_profile_image_url($pt_image, $no_cache = "", $no_img = "")
{
    global $ct_no_profile_img_url, $ct_img_url, $ct_img_dir;

    if ($no_img) {
        $no_img_t = $no_img;
    } else {
        $no_img_t = $ct_no_profile_img_url;
    }

    if (is_file($ct_img_dir . "/" . $pt_image)) {
        if ($no_cache == 'Y') {
            $rtn = $ct_img_url . "/" . $pt_image . "?v=" . time();
        } else {
            $rtn = $ct_img_url . "/" . $pt_image;
        }
    } else {
        $rtn = $no_img_t;
    }

    return $rtn;
}

//주문번호
function get_uid()
{
    global $DB;

    $unique = false;
    do {
        $uid = substr("P" . date("ymdHis", time()) . strtoupper(md5(mt_rand())), 0, 16);
        $DB->where("ot_code", $uid);
        $cnt = $DB->getValue("order_t", "count(0)");
        if ($cnt < 1) {
            $unique = true;
            break;
        }
    } while ($unique == false);

    return $uid;
}
//쿠폰코드
function get_coupon_code()
{
    global $DB;

    $unique = false;
    do {
        $uid = substr('C' . strtoupper(md5(time())), 0, 8);
        $DB->where("ct_code", $uid);
        $cnt = $DB->getValue("coupon_t", "count(0)");
        if ($cnt < 1) {
            $unique = true;
            break;
        }
    } while ($unique == false);

    return $uid;
}
function price2kor($total_price)
{
    $trans_kor = array("","일","이","삼","사","오","육","칠","팔","구");
    $price_unit = array("","십","백","천","만","십","백","천","억","십","백","천","조","십","백","천");
    $valuecode = array("","만","억","조");

    $value = strlen($total_price);

    $k = 0;
    for ($i = $value;$i > 0;$i--) {
        $vv = "";
        $vc = substr($total_price, $k, 1);
        $vt = $trans_kor[$vc];
        $k++;

        if ($i % 5 == 0) {
            $vv = $valuecode[$i / 5];
        } else {
            if ($vc) {
                $vv = $price_unit[$i - 1];
            }
        }

        $vr = $vr.$vt.$vv;
    }

    return $vr;
}

function number_shorten($number, $precision = 0)
{
    $suffixes = ['', 'K', 'M', 'B', 'T', 'Qa', 'Qi'];
    if ($number < 1000) {
        return number_format($number);
    } else {
        $index = (int) log(abs($number), 1000);
        $index = max(0, min(count($suffixes) - 1, $index));
        return number_format($number / 1000 ** $index, $precision) . $suffixes[$index];
    }
}

function f_aligo_sms_send($receiver, $msg, $subject = "", $rdate = "", $rtime = "")
{

    $sms_url = "https://apis.aligo.in/send/";
    $sms['user_id'] = ALIGO_USER_ID;
    $sms['key'] = ALIGO_KEY;

    $host_info = explode("/", $sms_url);
    $port = $host_info[0] == 'https:' ? 443 : 80;

    $sms['msg'] = stripslashes($msg);
    $sms['receiver'] = $receiver;
    $sms['destination'] = '';
    $sms['sender'] = ALIGO_SENDER;
    $sms['rdate'] = $rdate;
    $sms['rtime'] = $rtime;
    $sms['testmode_yn'] = 'N';
    $sms['title'] = $subject;
    $sms['msg_type'] = 'SMS';
    if (ALIGO_USER_ID && ALIGO_KEY) {
        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_PORT, $port);
        curl_setopt($oCurl, CURLOPT_URL, $sms_url);
        curl_setopt($oCurl, CURLOPT_POST, 1);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $sms);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
        $ret = curl_exec($oCurl);
        curl_close($oCurl);
    } else {
        $ret = array('result_code' => '000', 'message' => 'test');
    }
    return $ret;
}

function f_naver_sms_send($receiver, $msg)
{
    // 네이버 클라우드 SMS 서비스 설정
    $serviceId = "ncp:sms:kr:319583959217:smap"; // 서비스 ID
    $smsURL = "https://sens.apigw.ntruss.com/sms/v2/services/{$serviceId}/messages";

    // 발신자 및 API 인증 정보
    $sender = '01029565435'; // 발신자 번호
    $accessKeyId = "1gBhuBWQejPsJJNeiEY0"; // 발급받은 액세스 키
    $accessSecretKey = "WZwEXB2px6MZ2ivtqUHzwpbtasJsbwCYJ3zlLR5p"; // 발급받은 시크릿 키

    // 현재 시간 (밀리초)
    $timestamp = floor(microtime(true) * 1000);

    // SMS 발송 데이터 설정
    $postData = array(
        'type' => 'SMS',
        'countryCode' => '82',
        'from' => $sender, // 발신번호
        'subject' => 'NumberCheck',
        'contentType' => 'COMM',
        'content' => $msg,
        'messages' => array(array('subject' => 'NumberCheck', 'content' => $msg, 'to' => $receiver))
    );

    // JSON 형식으로 변환
    $postFields = json_encode($postData);

    // API 서명 생성
    $signature = base64_encode(hash_hmac('sha256', "POST /sms/v2/services/{$serviceId}/messages\n{$timestamp}\n{$accessKeyId}", $accessSecretKey, true));

    // 요청 헤더 설정
    $headers = array(
        'Content-Type: application/json; charset=utf-8',
        "x-ncp-apigw-timestamp: {$timestamp}",
        "x-ncp-iam-access-key: {$accessKeyId}",
        "x-ncp-apigw-signature-v2: {$signature}"
    );

    // cURL 설정
    $ch = curl_init($smsURL);
    curl_setopt_array($ch, array(
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $postFields
    ));

    // cURL 실행 및 응답 받기
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function format_size($bytes, $decimals = 2)
{
    $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

function get_sgt_code()
{
    global $DB;

    $unique = false;
    do {
        $uid = substr("G".strtoupper(md5(mt_rand())), 0, 6);
        $DB->where('sgt_code', $uid);
        $row = $DB->getone('smap_group_t');

        if ($row['sgt_idx'] == '') {
            $unique = true;
            break;
        }
    } while ($unique == false);

    return $uid;
}

function get_sel_fct()
{
    global $DB;

    unset($list);
    $DB->where('fct_show', 'Y');
    $DB->orderBy('fct_rank', 'asc');
    $list = $DB->get('faq_category_t');

    $rtn = '';

    if($list) {
        foreach($list as $row) {
            $rtn .= '<option value="'.$row['fct_idx'].'">'.$row['fct_name'].'</option>';
        }
    }

    return $rtn;
}

function get_search_coordinate2address($lat, $lng)
{
    $url = "https://naveropenapi.apigw.ntruss.com/map-reversegeocode/v2/gc?request=coordsToaddr&coords=" . $lng . "," . $lat . "&sourcecrs=epsg:4326&output=json&orders=admcode";

    $headers = array();
    $headers[] = 'X-NCP-APIGW-API-KEY-ID:' . NCPCLIENTID;
    $headers[] = 'X-NCP-APIGW-API-KEY:' . NCPCLIENTSECRET;

    $post_data = array(
        'request' => 'coordsToaddr',
        'coords' => $lng . ',' . $lat,
        'sourcecrs' => 'epsg:4326',
        'orders' => 'legalcode',
        'output' => 'json',
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    if ($result === false) {
        die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);
    $obj = json_decode($result, true);

    $rtn = array();

    if ($obj['status']['code'] == '0') {
        $rtn['area1'] = $obj['results']['0']['region']['area1']['name'];
        $rtn['area2'] = $obj['results']['0']['region']['area2']['name'];
        $rtn['area3'] = $obj['results']['0']['region']['area3']['name'];
    } else {
        $rtn['area1'] = '서울특별시';
        $rtn['area2'] = '중구';
        $rtn['area3'] = '명동';
    }

    return $rtn;
}

function get_page_nm()
{
    $rtn = str_replace('/', '', $_SERVER['PHP_SELF']);
    $rtn = str_replace('.php', '', $rtn);

    return $rtn;
}

function result_data($success, $title, $message, $data)
{
    $arr = array();

    $arr['data'] = $data;
    $arr['message'] = $message;
    $arr['success'] = $success;
    $arr['title'] = $title;

    $obj = json_encode($arr, JSON_UNESCAPED_UNICODE);

    return $obj;
}

function get_sgdt_member_list($sgt_idx)
{
    global $DB, $_SESSION;

    // 캐시 키 생성
    $cache_key = "sgdt_member_list_{$sgt_idx}_{$_SESSION['_mt_idx']}";

    // 캐시된 데이터 확인
    $cached_data = CacheUtil::get($cache_key);
    
    // 캐시된 데이터가 있을 경우, 유효성 검사
    if ($cached_data) {
        $DB->where('sgt_idx', $sgt_idx);
        $DB->where('sgdt_udate', $cached_data['last_update'], '>');
        $updated = $DB->getOne('smap_group_detail_t', 'COUNT(*) as count');
        
        // DB에 변경이 없으면 캐시된 데이터 반환
        if ($updated['count'] == 0) {
            return $cached_data;
        }
    }

    // 기존 로직
    $DB->where('mt_idx', $_SESSION['_mt_idx']);
    $DB->where('sgt_idx', $sgt_idx);
    $row_sgpt = $DB->getone('smap_group_personal_t');

    unset($list_sgdt);
    if($row_sgpt['sgpt_idx']) {
        $list_sgdt = array();
        $sgdt_json_arr = json_decode($row_sgpt['sgdt_json'], true);

        if($sgdt_json_arr) {
            foreach($sgdt_json_arr as $key => $val) {
                if($val) {
                    $DB->where('sgdt_idx', $val);
                    $DB->where('sgdt_discharge', 'N');
                    $DB->where('sgdt_exit', 'N');
                    $row_sgdt = $DB->getone('smap_group_detail_t');

                    if($row_sgdt) {
                        $list_sgdt[] = $row_sgdt;
                    }
                }
            }
        }
    } else {
        $DB->where('sgt_idx', $sgt_idx);
        $DB->where('mt_idx', $_SESSION['_mt_idx'], '!=');
        $DB->where('sgdt_discharge', 'N');
        $DB->where('sgdt_exit', 'N');
        $DB->orderBy("sgdt_owner_chk", "asc");
        $DB->orderBy("sgdt_leader_chk", "asc");
        $list_sgdt = $DB->get('smap_group_detail_t');
    }

    unset($rtn);
    $rtn = array();

    $chk_leader = 0;
    $member_cnt = 0;

    if($list_sgdt) {
        foreach($list_sgdt as $row_sgdt) {
            $mt_info = get_member_t_info($row_sgdt['mt_idx']);
            $row_mllt = get_member_location_log_t_info($row_sgdt['mt_idx']);
            $my_working_cnt = $row_mllt['mt_health_work'];

            if($row_sgdt['sgdt_owner_chk'] == 'Y') {
                $sgdt_owner_leader_chk_t = '오너';
            } else {
                if($row_sgdt['sgdt_leader_chk'] == 'Y') {
                    $sgdt_owner_leader_chk_t = '리더';
                    $chk_leader++;
                } else {
                    $sgdt_owner_leader_chk_t = '';
                }
            }
            if($row_sgdt['sgdt_group_chk'] == 'Y') {
                $row_sgdt['sgdt_adate'] = '무기한';
            } elseif ($row_sgdt['sgdt_group_chk'] == 'N') {
                // 오늘 날짜
                $today = new DateTime();
                $date = new DateTime($row_sgdt['sgdt_adate']); // 타임스탬프를 이용하여 DateTime 객체 생성

                // 날짜 차이 계산 (음수 포함)
                $remainingDays = floor(($date->getTimestamp() - $today->getTimestamp()) / (60 * 60 * 24)); // 일자구하기
                $remainingTimes = ($date->getTimestamp() - $today->getTimestamp()) / (60 * 60 * 24); // 시간구하기
                $remainingHours = $remainingTimes * 24; // 시간으로 변환
                if ($remainingDays > 0 || $remainingTimes > 0) {
                    if($remainingDays > 0) {
                        $row_sgdt['sgdt_adate'] = $remainingDays . '일';
                    } else {
                        $row_sgdt['sgdt_adate'] = floor($remainingHours) . '시간';
                    }
                } else { // 기한이 지났을 경우 그룹 나가도록 설정
                    unset($arr_query);
                    $arr_query = array(
                        "sgdt_exit" => 'Y',
                        "sgdt_xdate" => $DB->now(),
                        "sgdt_udate" => $DB->now(),
                    );
                    $DB->where('sgdt_idx', $row_sgdt['sgdt_idx']);
                    $DB->update('smap_group_detail_t', $arr_query);

                    unset($arr_query);
                    $arr_query = array(
                        "slt_show" => 'N',
                        "slt_ddate" => $DB->now(),
                        "slt_udate" => $DB->now(),
                    );

                    $DB->where('sgdt_idx', $row_sgdt['sgdt_idx']);
                    $DB->update('smap_location_t', $arr_query);

                    unset($arr_query);
                    $arr_query = array(
                        "sst_show" => 'N',
                        "sst_ddate" => $DB->now(),
                        "sst_udate" => $DB->now(),
                    );

                    $DB->where('sgdt_idx', $row_sgdt['sgdt_idx']);
                    $DB->update('smap_schedule_t', $arr_query);

                    // 기한이 이미 지났으므로 다음으로 넘어가도록 continue 사용
                    continue;
                }
            }

            $mt_file1_url = get_image_url($mt_info['mt_file1']);

            $rtn['data'][] = array(
                'sgdt_idx' => $row_sgdt['sgdt_idx'],
                'sgdt_owner_chk' => $row_sgdt['sgdt_owner_chk'],
                'sgdt_leader_chk' => $row_sgdt['sgdt_leader_chk'],
                'mt_file1_url' => $mt_file1_url,
                'mt_idx' => $mt_info['mt_idx'],
                'mt_nickname' => $mt_info['mt_nickname'],
                'mt_name' => $mt_info['mt_name'],
                'sgdt_owner_leader_chk_t' => $sgdt_owner_leader_chk_t,
                'my_working_cnt' => number_format($my_working_cnt),
                'sgdt_adate' => $row_sgdt['sgdt_adate'],
            );

            $member_cnt++;
        }
    }

    $rtn['chk_leader'] = $chk_leader;
    $rtn['member_cnt'] = $member_cnt;
    $rtn['last_update'] = date('Y-m-d H:i:s');

    // 결과를 캐시에 저장 (예: 10분 동안)
    CacheUtil::set($cache_key, $rtn, 600);


    return $rtn;
}

function get_sgdt_member_lists($sgt_idx)
{
    global $DB, $_SESSION;

    $DB->where('sgt_idx', $sgt_idx);
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $DB->orderBy("sgdt_owner_chk", "asc");
    $DB->orderBy("sgdt_leader_chk", "asc");
    $list_sgdt = $DB->get('smap_group_detail_t');

    unset($rtn);
    $rtn = array();

    $chk_leader = 0;
    $member_cnt = 0;

    if($list_sgdt) {
        foreach($list_sgdt as $row_sgdt) {
            $mt_info = get_member_t_info($row_sgdt['mt_idx']);
            $row_mllt = get_member_location_log_t_info($row_sgdt['mt_idx']);
            $my_working_cnt = $row_mllt['mt_health_work'];

            $mt_file1_url = get_image_url($mt_info['mt_file1']);

            $rtn['data'][] = array(
                'sgdt_idx' => $row_sgdt['sgdt_idx'],
                'sgdt_owner_chk' => $row_sgdt['sgdt_owner_chk'],
                'sgdt_leader_chk' => $row_sgdt['sgdt_leader_chk'],
                'mt_file1_url' => $mt_file1_url,
                'mt_idx' => $mt_info['mt_idx'],
                'mt_nickname' => $mt_info['mt_nickname'],
                'mt_name' => $mt_info['mt_name'],
                'my_working_cnt' => number_format($my_working_cnt),
                'sgdt_adate' => $row_sgdt['sgdt_adate'],
            );

            $member_cnt++;
        }
    }

    $rtn['chk_leader'] = $chk_leader;
    $rtn['member_cnt'] = $member_cnt;

    return $rtn;
}

function get_group_invite_cnt($sgt_idx)
{
    global $DB;

    $DB->where('sgt_idx', $sgt_idx);
    $DB->where('sit_status', '2');
    $row = $DB->getone('smap_invite_t', 'count(*) as cnt');
    $invite_cnt = $row['cnt'];

    return $invite_cnt;
}

function get_group_member_cnt($sgt_idx)
{
    global $DB;
    $DB->where('sgt_idx', $sgt_idx);
    $DB->where('mt_idx', $_SESSION['_mt_idx'], '!=');
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $DB->orderBy("sgdt_owner_chk", "asc");
    $DB->orderBy("sgdt_leader_chk", "asc");
    $list_sgdt = $DB->get('smap_group_detail_t');
    if ($list_sgdt) {
        foreach ($list_sgdt as $row_sgdt) {
            if ($row_sgdt['sgdt_group_chk'] == 'N') {
                // 오늘 날짜
                $today = new DateTime();
                $date = new DateTime($row_sgdt['sgdt_adate']); // 타임스탬프를 이용하여 DateTime 객체 생성

                // 날짜 차이 계산 (음수 포함)
                $remainingDays = floor(($date->getTimestamp() - $today->getTimestamp()) / (60 * 60 * 24)) + 1;
                if ($remainingDays > 0) {
                    $row_sgdt['sgdt_adate'] = $remainingDays . '일 전';
                } else { // 기한이 지났을 경우 그룹 나가도록 설정
                    unset($arr_query);
                    $arr_query = array(
                        "sgdt_exit" => 'Y',
                        "sgdt_xdate" => $DB->now(),
                        "sgdt_udate" => $DB->now(),
                    );
                    $DB->where('sgdt_idx', $row_sgdt['sgdt_idx']);
                    $DB->update('smap_group_detail_t', $arr_query);

                    unset($arr_query);
                    $arr_query = array(
                        "slt_show" => 'N',
                        "slt_ddate" => $DB->now(),
                        "slt_udate" => $DB->now(),
                    );

                    $DB->where('sgdt_idx', $row_sgdt['sgdt_idx']);
                    $DB->update('smap_location_t', $arr_query);

                    unset($arr_query);
                    $arr_query = array(
                        "sst_show" => 'N',
                        "sst_ddate" => $DB->now(),
                        "sst_udate" => $DB->now(),
                    );

                    $DB->where('sgdt_idx', $row_sgdt['sgdt_idx']);
                    $DB->update('smap_schedule_t', $arr_query);
                }
            }
        }
    }

    // 총인원수
    $DB->where('sgt_idx', $sgt_idx);
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $row_sgdt_cnt = $DB->getone("smap_group_detail_t", "count(*) as cnt");
    $member_cnt_t = $row_sgdt_cnt['cnt'];

    return $member_cnt_t;
}

function get_sit_code()
{
    global $DB;

    $unique = false;
    do {
        $uid = substr("SMAP".strtoupper(md5(mt_rand())), 0, 16);
        $DB->where('sit_code', $uid);
        $row = $DB->getone('smap_invite_t');

        if ($row['sit_idx'] == '') {
            $unique = true;
            break;
        }
    } while ($unique == false);

    return $uid;
}

function get_date_ttime($sst_sdate)
{
    if($sst_sdate) {
        $sst_sdate_ex1 = explode(' ', $sst_sdate);
    }
    if($sst_sdate_ex1[1]) {
        $sst_sdate_ex2 = explode(':', $sst_sdate_ex1[1]);
    }

    if($sst_sdate_ex2[0] < 12) {
        $ss_sdate_r = '오전 '.$sst_sdate_ex2[0];
    } else {
        $ss_sdate_r = '오후 '.($sst_sdate_ex2[0] - 12);
    }
    if($ss_sdate_r == '오후 0') {
        $ss_sdate_r = '정오 12';
    } elseif($ss_sdate_r == '오전 0') {
        $ss_sdate_r = '자정 12';
    }
    $ss_sdate_r .= ':'.$sst_sdate_ex2[1];

    return $ss_sdate_r;
}

function get_date_f($sst_sdate)
{
    if($sst_sdate) {
        $sst_sdate_ex1 = explode(' ', $sst_sdate);
    }
    if($sst_sdate_ex1[1]) {
        $sst_sdate_ex2 = explode(':', $sst_sdate_ex1[1]);
    }

    $rtn = array();
    $rtn['date'] = $sst_sdate_ex1[0];
    if($sst_sdate_ex2[0] < 12) {
        $rtn['ampm'] = 1;
        $rtn['hour'] = $sst_sdate_ex2[0];
    } else {
        $rtn['ampm'] = 2;
        $rtn['hour'] = ($sst_sdate_ex2[0] - 12);
    }
    $rtn['min'] = $sst_sdate_ex2[1];

    return $rtn;
}

function get_distance_t($d)
{
    if($d < 1000) {
        $rtn = number_format($d)."m";
    } else {
        $rtn = round(($d / 1000), 2)."km";
    }

    return $rtn;
}

function get_distance_k($d)
{
    if($d < 1) {
        $rtn = round(($d * 1000), 2)."m";
    } else {
        $rtn = round($d, 2)."km";
    }

    if($rtn == 'NANkm') {
        $rtn = '-';
    }

    return $rtn;
}

function get_distance_km($d)
{
    if ($d < 1000) {
        $rtn = round(($d), 2) . "m";
    } else {
        $rtn = round($d / 1000, 2) . "km";
    }

    if ($rtn == 'NANkm') {
        $rtn = '-';
    }

    return $rtn;
}
function get_distance_m($d)
{
    $rtn = round($d / 60) . "분";

    return $rtn;
}

function get_distance_hm($d)
{
    $rtn = round($d) . "분";

    return $rtn;
}

function get_gps_distance_k($mt_idx, $sdate)
{
    global $DB, $slt_mlt_accuacy, $slt_mlt_speed;

    // SQL 쿼리 준비
    $DB->where('mt_idx', $mt_idx);
    $DB->where('mlt_accuacy <', $slt_mlt_accuacy);
    $DB->where('mlt_speed >=', $slt_mlt_speed);
    $DB->where('mlt_wdate >=', $sdate . ' 00:00:00');
    $DB->where('mlt_wdate <=', $sdate . ' 23:59:59');
    $DB->orderby('mlt_wdate', 'asc');
    $list = $DB->get('member_location_log_t', null, 'mlt_lat, mlt_long, mlt_wdate, mt_health_work');

    $gsp_km = 0;
    $gps_time = 0;
    $gps_health_work = 0;
    $prev_gps_time = null;

    if ($list) {
        foreach ($list as $key => $row) {
            // 거리 계산
            if ($key > 0) {
                $gsp_km += gps_distance($list[$key - 1]['mlt_lat'], $list[$key - 1]['mlt_long'], $row['mlt_lat'], $row['mlt_long']);
            }

            // 시간 계산
            if ($prev_gps_time !== null) {
                $gps_time += (strtotime($row['mlt_wdate']) - strtotime($prev_gps_time));
            }
            
            // 걸음수 계산
            $gps_health_work = $row['mt_health_work'];

            // 이전 시간 업데이트
            $prev_gps_time = $row['mlt_wdate'];
        }
    }

    $rtn = array($gsp_km, $gps_time, $gps_health_work);

    return $rtn;
}

function get_gps_distance($mt_idx, $sdate)
{

    global $DB, $slt_mlt_accuacy, $slt_mlt_speed;

    $gsp_km = 0;
    $gps_time = 0;
    $gps_health_work = 0;

    $sub_query = "
    WITH RankedLogs AS (
    SELECT
        mt_idx,
        mlt_accuacy,
        mlt_speed,
        mlt_lat,
        mlt_long,
        mlt_gps_time,
        ROW_NUMBER() OVER (ORDER BY mlt_gps_time ASC) AS rn
    FROM
        member_location_log_t
    WHERE 1=1
        AND mt_idx = " . $mt_idx . "
        AND mlt_gps_time BETWEEN '" . $sdate . " 00:00:00' AND '" . $sdate . " 23:59:59'
        AND mlt_speed > 0
        AND mlt_accuacy < ". $slt_mlt_accuacy ."
    ),
    Diffs AS (
        SELECT
            L1.rn,
            L1.mlt_lat AS lat1,
            L1.mlt_long AS long1,
            L2.mlt_lat AS lat2,
            L2.mlt_long AS long2,
            L1.mlt_gps_time AS wdate1,
            L2.mlt_gps_time AS wdate2,
            L1.mlt_speed AS speed,
            L1.mlt_accuacy AS accuracy,
            TIMESTAMPDIFF(SECOND, L1.mlt_gps_time, L2.mlt_gps_time) AS time_diff_seconds
        FROM
            RankedLogs L1
        INNER JOIN RankedLogs L2 ON L1.rn = L2.rn - 1
    )
    SELECT SUM(CASE
                WHEN rslt.time_diff_seconds > 10
                AND ROUND((rslt.distance_meters / rslt.time_diff_seconds) * 3600 / 1000, 1) < " . $slt_mlt_speed . "
                AND rslt.time_diff_seconds > rslt.distance_meters / 0.6
                THEN rslt.distance_meters / 0.6
            ELSE time_diff_seconds
            END) / 60 AS movig_minute
        ,SUM(rslt.distance_meters) AS moving_meters


    FROM
    (
        SELECT
            rn,
            lat1,
            long1,
            lat2,
            long2,
            wdate1,
            wdate2,
            speed,
            accuracy,
            time_diff_seconds,
            ROUND(6371000 * ACOS(COS(RADIANS(lat2)) * COS(RADIANS(lat1)) * COS(RADIANS(long1) - RADIANS(long2)) + SIN(RADIANS(lat1)) * SIN(RADIANS(lat2))), 1) AS distance_meters
        FROM
            Diffs
    ) rslt
    -- WHERE ROUND((rslt.distance_meters / rslt.time_diff_seconds) * 3600 / 1000, 1) > 2
    WHERE ROUND((rslt.distance_meters / rslt.time_diff_seconds) * 3600 / 1000, 1) between 2 and 55
";

    // 하위 쿼리 실행
    $list = $DB->Query($sub_query);
    if ($list) {
        // 가져온 결과를 사용하여 필요한 작업 수행
        foreach ($list as $row) {
            $gps_time = $row['movig_minute'];
            $gsp_km = $row['moving_meters'];
            // 나머지 필드들에 대한 작업 수행
        }
    } else {
        $gps_time = 0;
        $gsp_km = 0;
    }
    unset($row);
    $DB->where('mt_idx', $mt_idx);
    // $DB->where("mlt_accuacy < " . $slt_mlt_accuacy);
    // $DB->where("mlt_speed >= " . $slt_mlt_speed);
    $DB->where("mlt_wdate BETWEEN '" . $sdate . " 00:00:00' AND '" . $sdate . " 23:59:59'");
    $DB->orderby('mlt_gps_time', 'desc');
    $row = $DB->getone('member_location_log_t');
    // 현재날짜의 마지막 걸음수 들고오기
    $gps_health_work = $row['mt_health_work'] ? $row['mt_health_work'] : 0;

    $rtn = array($gsp_km, $gps_time, $gps_health_work);

    return $rtn;
}

function gps_distance($lat1, $lon1, $lat2, $lon2, $unit = "K")
{
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return ($miles * 1.609344);
    } elseif ($unit == "N") {
        return ($miles * 0.8684);
    } else {
        return $miles;
    }
}

function get_date2unixtime($s_date)
{
    $s_date_ex = explode(" ", $s_date);
    $s_date_ex2 = explode("-", $s_date_ex[0]);
    $s_date_ex3 = explode(":", $s_date_ex[1]);

    $s_time = mktime($s_date_ex3[0], $s_date_ex3[1], $s_date_ex3[2], $s_date_ex2[1], $s_date_ex2[2], $s_date_ex2[0]);

    return $s_time;
}

function get_schedule_array($mt_idx, $s_date)
{
    global $DB;

    $arr_sst_idx = array();

    //나의 일정
    unset($list);
    $DB->where('mt_idx', $mt_idx);
    $DB->where('sgt_idx is null');
    $DB->where('(sgdt_idx is null or sgdt_idx=0)');
    $DB->where(" ( sst_sdate <= '" . $s_date . " 23:59:59' and sst_edate >= '" . $s_date . " 00:00:00' )");
    $DB->where('sst_show', 'Y');
    $list = $DB->get('smap_schedule_t');

    if ($list) {
        foreach ($list as $row) {
            if ($row['sst_title']) {
                $arr_sst_idx[] = $row['sst_idx'];
            }
        }
    }
    //나에게 온 일정
    $DB->where('mt_idx', $mt_idx);
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $row_sgdt = $DB->getone('smap_group_detail_t', 'GROUP_CONCAT(sgt_idx) as gc_sgt_idx, GROUP_CONCAT(sgdt_idx) as gc_sgdt_idx');

    if ($row_sgdt['gc_sgt_idx']) {
        unset($list);
        // $DB->where("sgt_idx in (" . $row_sgdt['gc_sgt_idx'] . ")");
        $DB->where("sgdt_idx in (" . $row_sgdt['gc_sgdt_idx'] . ")");
        $DB->where(" ( sst_sdate <= '" . $s_date . " 23:59:59' and sst_edate >= '" . $s_date . " 00:00:00' )");
        $DB->where('sst_show', 'Y');
        $list = $DB->get('smap_schedule_t');

        if ($list) {
            foreach ($list as $row) {
                if ($row['sst_title']) {
                    $arr_sst_idx[] = $row['sst_idx'];
                }
            }
        }
    }

    $arr_sst_idx = array_unique($arr_sst_idx);

    return $arr_sst_idx;
}
function get_schedule_main($sgdt_idx, $s_date, $mt_idx)
{
    global $DB;

    $arr_sst_idx = array();

    //나의 일정
    unset($list);
    $DB->where('sgdt_idx', $sgdt_idx);
    $DB->where(" ( sst_sdate <= '" . $s_date . " 23:59:59' and sst_edate >= '" . $s_date . " 00:00:00' )");
    $DB->where('sst_show', 'Y');
    $list = $DB->get('smap_schedule_t');

    if ($list) {
        foreach ($list as $row) {
            if ($row['sst_title']) {
                $arr_sst_idx[] = $row['sst_idx'];
            }
        }
    }

    //나의 일정
    /*
    $DB->where('mt_idx', $mt_idx);
    $DB->where('sgt_idx is null');
    $DB->where('(sgdt_idx is null or sgdt_idx=0)');
    $DB->where(" ( sst_sdate <= '" . $s_date . " 23:59:59' and sst_edate >= '" . $s_date . " 00:00:00' )");
    $DB->where('sst_show', 'Y');
    $list = $DB->get('smap_schedule_t');

    if ($list) {
        foreach ($list as $row) {
            if ($row['sst_title']) {
                $arr_sst_idx[] = $row['sst_idx'];
            }
        }
    }
    */
    /*
    //나에게 온 일정
    $DB->where('sgdt_idx', $sgdt_idx);
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $row_sgdt = $DB->getone('smap_group_detail_t', 'GROUP_CONCAT(sgt_idx) as gc_sgt_idx');

    if ($row_sgdt['gc_sgt_idx']) {
        unset($list);
        $DB->where("sgt_idx in (" . $row_sgdt['gc_sgt_idx'] . ")");
        $DB->where(" ( sst_sdate <= '" . $s_date . " 23:59:59' and sst_edate >= '" . $s_date . " 00:00:00' )");
        $DB->where('sst_show', 'Y');
        $list = $DB->get('smap_schedule_t');

        if ($list) {
            foreach ($list as $row) {
                if ($row['sst_title']) {
                    $arr_sst_idx[] = $row['sst_idx'];
                }
            }
        }
    }
    */
    $arr_sst_idx = array_unique($arr_sst_idx);

    return $arr_sst_idx;
}

function get_schedule_date($sgdt_idx, $s_date, $mt_idx)
{
    global $DB;

    $arr_sst_date = array();

    //나의 일정
    unset($list);
    $DB->where('sgdt_idx', $sgdt_idx);
    $DB->where(" ( sst_sdate <= '" . $s_date . " 23:59:59' and sst_edate >= '" . $s_date . " 00:00:00' )");
    $DB->where('sst_show', 'Y');
    $list = $DB->get('smap_schedule_t');

    if ($list) {
        foreach ($list as $row) {
            if ($row['sst_idx']) {
                $arr_sst_date[] = $row['sst_wdate'];
                $arr_sst_date[] = $row['sst_udate'];
            }
        }
    }

    /*
    //나의 일정
    $DB->where('mt_idx', $mt_idx);
    $DB->where('sgt_idx is null');
    $DB->where('(sgdt_idx is null or sgdt_idx=0)');
    $DB->where(" ( sst_sdate <= '" . $s_date . " 23:59:59' and sst_edate >= '" . $s_date . " 00:00:00' )");
    $DB->where('sst_show', 'Y');
    $list = $DB->get('smap_schedule_t');

    if ($list) {
        foreach ($list as $row) {
            if ($row['sst_title']) {
                $arr_sst_date[] = $row['sst_wdate'];
                $arr_sst_date[] = $row['sst_udate'];
            }
        }
    }
    */
    $arr_sst_date = array_unique($arr_sst_date);

    return $arr_sst_date;
}

function get_schedule_array2($sgdt_idx, $s_date, $mt_idx)
{
    global $DB;

    $arr_sst_idx = array();

    //나의 일정
    unset($list);
    $DB->where('sgdt_idx', $sgdt_idx);
    $DB->where(" ( sst_sdate <= '" . $s_date . " 23:59:59' and sst_edate >= '" . $s_date . " 00:00:00' )");
    $DB->where('sst_show', 'Y');
    $list = $DB->get('smap_schedule_t');

    if ($list) {
        foreach ($list as $row) {
            if ($row['sst_title']) {
                $arr_sst_idx[] = $row['sst_idx'];
            }
        }
    }
    //나에게 온 일정
    $DB->where('sgdt_idx', $sgdt_idx);
    $DB->where('sgdt_discharge', 'N');
    $DB->where('sgdt_exit', 'N');
    $row_sgdt = $DB->getone('smap_group_detail_t', 'GROUP_CONCAT(sgt_idx) as gc_sgt_idx, GROUP_CONCAT(sgdt_idx) as gc_sgdt_idx');

    if ($row_sgdt['gc_sgt_idx']) {
        unset($list);
        $DB->where("sgt_idx in (" . $row_sgdt['gc_sgt_idx'] . ")");
        $DB->where("sgdt_idx in (" . $row_sgdt['gc_sgdt_idx'] . ")");
        $DB->where(" ( sst_sdate <= '" . $s_date . " 23:59:59' and sst_edate >= '" . $s_date . " 00:00:00' )");
        $DB->where('sst_show', 'Y');
        $list = $DB->get('smap_schedule_t');

        if ($list) {
            foreach ($list as $row) {
                if ($row['sst_title']) {
                    $arr_sst_idx[] = $row['sst_idx'];
                }
            }
        }
    }
    //직접 등록한 나의 일정
    $DB->where('mt_idx', $mt_idx);
    $DB->where('sgt_idx is null');
    $DB->where('(sgdt_idx is null or sgdt_idx=0)');
    $DB->where(" ( sst_sdate <= '" . $s_date . " 23:59:59' and sst_edate >= '" . $s_date . " 00:00:00' )");
    $DB->where('sst_show', 'Y');
    $list = $DB->get('smap_schedule_t');

    if ($list) {
        foreach ($list as $row) {
            if ($row['sst_title']) {
                $arr_sst_idx[] = $row['sst_idx'];
            }
        }
    }
    $arr_sst_idx = array_unique($arr_sst_idx);

    return $arr_sst_idx;
}

function api_push_send($plt_type, $sst_idx, $plt_condition, $plt_memo, $mt_id, $plt_title, $plt_content)
{
    //전송 URL
    $API_URL = 'https://api2.smap.site/api/fcm_sendone/';

    //전송 데이터
    $fields = array(
    'plt_type' => $plt_type,
    'sst_idx' => $sst_idx,
    'plt_condition' => $plt_condition,
    'plt_memo' => $plt_memo,
    'mt_id' => $mt_id,
    'plt_title' => $plt_title,
    'plt_content' => $plt_content,
    );

    $headers = array('Content-Type:application/json');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $API_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);

    if ($result === false) {
        die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);
    $obj = json_decode($result);

    return $obj;

}

function group_invite_del($mt_idx)
{
    global $DB;

    $current_date = date('Y-m-d H:i:s');
    $DB->where('mt_idx', $mt_idx);
    $DB->where('sit_status', '2');
    $sit_list = $DB->get('smap_invite_t');

    if($sit_list) {
        foreach($sit_list as $sit_row) {
            $new_date = date('Y-m-d', strtotime($sit_row['sit_wdate'] . ' +1 day'));
            if($new_date < $current_date) {
                unset($arr_query);
                $arr_query = array(
                    "sit_status" => '4',
                    "sit_ddate" => $DB->now()
                );
                $DB->where('sit_idx', $sit_row['sit_idx']);
                $DB->update('smap_invite_t', $arr_query);
            }
        }
    }
}

function member_location_history_delete()
{
    global $DB;

    $current_date = date('Y-m-d H:i:s');
    $before_date = date('Y-m-d H:i:s', strtotime($current_date. ' -14 day'));

    $DB->where('mlt_gps_time <= "'. $before_date.'"');
    $DB->delete('member_location_log_t');

}

function coupon_end_check()
{
    global $DB;

    $current_date = date('Y-m-d');
    $DB->where('ct_end', 'N');
    $ct_list = $DB->get('coupon_t');

    if ($ct_list) {
        foreach ($ct_list as $ct_row) {
            if ($ct_row['ct_edate']  <= $current_date) {
                unset($arr_query);
                $arr_query = array(
                    "ct_end" => 'Y',
                    "ct_udate" => $DB->now()
                );
                $DB->where('ct_idx', $ct_row['ct_idx']);
                $DB->update('coupon_t', $arr_query);
            }
        }
    }
}

function session_location_update($mt_idx) // 회원 위치 로그값 기준으로 세션 다시 부여
{
    global $DB;

    $DB->where('mt_idx', $mt_idx);
    $DB->orderby('mlt_gps_time', 'desc');
    $my_location_info = $DB->getone('member_location_log_t');
    if ($my_location_info['mlt_idx']) {
        // 본인 위치 세션 재등록 하기
        if (isset($my_location_info['mlt_lat'])) {
            $_SESSION['_mt_lat'] = $my_location_info['mlt_lat'];
        }
        if (isset($my_location_info['mlt_long'])) {
            $_SESSION['_mt_long'] = $my_location_info['mlt_long'];
        }
    }
}

function member_plan_check($mt_idx)    // 유료회원 마감되었는지 확인
{
    global $DB;

    $current_date = date("Y-m-d H:i:s");
    $DB->where('mt_idx', $mt_idx);
    $DB->where('mt_level', 5);
    $plan_End_row = $DB->getone('member_t');
    if ($plan_End_row['mt_idx'] && $plan_End_row['mt_level'] == 5 && $current_date > $plan_End_row['mt_plan_date'] && $plan_End_row['mt_plan_check'] == 'N') {
        unset($arr_query);
        $arr_query = array(
            'mt_level' => '2'
        );
        $DB->where('mt_idx', $mt_idx);
        $DB->update('member_t', $arr_query);
    }
}
function get_ad_log_check($mt_idx)
{
    global $DB;

    $s_date = date('Y-m-d');
    //나의 일정
    unset($row);
    $DB->where('mt_idx', $mt_idx);
    $DB->where('salt_date', $s_date);
    $row = $DB->getone('smap_ad_log_t');

    // $DB->where('mt.mt_idx', $mt_idx);
    // $DB->where('salt.salt_date', $s_date);
    // $DB->join('member_t mt', 'mt.mt_idx = salt.mt_idx', 'LEFT');
    // $DB->select("
    //     CASE
    //         WHEN TIMESTAMPDIFF(HOUR, mt.mt_wdate, NOW()) <= 24 THEN 0
    //         ELSE salt.log_count
    //     END AS log_count
    // ");
    // $DB->select("
    //     CASE
    //         WHEN TIMESTAMPDIFF(HOUR, mt.mt_wdate, NOW()) <= 24 THEN 0
    //         ELSE salt.path_count
    //     END AS path_count
    // ");
    // $DB->select("salt.salt_idx");
    // $row = $DB->getone('smap_ad_log_t salt');

    if(!$row['salt_idx']) {
        unset($arr_query);
        $arr_query = array(
            "mt_idx" => $mt_idx,
            "log_count" => 0,
            "path_count" => 0,
            "salt_date" => $s_date,
            "salt_wdate" => date('Y-m-d H:i:s')
        );
        $last_idx = $DB->insert('smap_ad_log_t', $arr_query);

        $DB->where('salt_idx', $last_idx);
        $row = $DB->getone('smap_ad_log_t');
    }

    return $row;
}

// 로그를 파일에 저장하는 함수
function logToFile($message) {
    $currentDir = dirname(__FILE__); // 현재 PHP 파일의 디렉토리 경로를 가져옵니다.
    $logFile = $currentDir . '/logfile.txt';  // 로그 파일 경로 설정
    $message = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
    file_put_contents($logFile, $message, FILE_APPEND | LOCK_EX);
}

if($chk_mobile) {
    // 앱토큰값 손실 확인 로그
    unset($arr_query);
    $arr_query = array(
        "mt_token_id" => $_SESSION['_mt_token_id'],
        "mt_token_id_cookie" => $_COOKIE['_mt_token_id'],
        "mt_lat" => $_SESSION['_mt_lat'],
        "mt_long" => $_SESSION['_mt_long'],
        "event_url" => $_SESSION['_event_url'],
        "referer_url" => $_SERVER['HTTP_REFERER'],
        "now_url" => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
        "agent" => $_SERVER['HTTP_USER_AGENT'],
        "ip" => $_SERVER['REMOTE_ADDR'],
        "auth_chk" => $_SESSION['_auth_chk'],
        "wdate" => $DB->now(),
    );
    $DB->insert('page_log_t', $arr_query);
}
