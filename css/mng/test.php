<?php

include $_SERVER['DOCUMENT_ROOT']."/lib.inc.php";

error_reporting(E_ERROR);
ini_set('display_errors', '1');



$fname = 'SMAP';
$fmail = 'admin@schedulemap.com';
$to = 'lcm790203@naver.com';
$tname = '이창민';
$subject = '[smap]테스트메일 제목';
$content = '[smap]테스트메일 내용';

$rtn1 = mailer_new($fname, $fmail, $to, $tname, $subject, $content);

printr($rtn1);

exit;

include $_SERVER['DOCUMENT_ROOT']."/fcm.inc.php";

//https://smap.dmonster.kr/auth?mt_token_id=e__0uKDESBWDABl9mYRyeM:APA91bEYQESZfu0CXDSJlZWhQdlENGviyL3RPpELL9GKcw-89mxdBBt5bsWmIs9lUvRcyrQxf2XKyaq5ZqtGquWf31dNJk6gPbGgVnW61Wwsg3Qj-8ZUHhTCadcN9yfeXVU9_0zOwx2j&event_url=https%3A%2F%2Fsmap.dmonster.kr%2Fnotice_detail%3Fnt_idx%3D2&mt_lat=35.2442415&mt_long=129.0897645

$mt_token_id = 'fGlrOwWNRlaat8CTH20Zwq:APA91bHf0ZMTlwVM9ylckmWCUStIjnk__vgJQibz7vVqEJL8l98xsD7LQCLQheivXC8dupfwKWGiP_73pG9Ykz7hXjRarULvQYbXyxDQHk96DnXo6tUtZDRYLRqBu6l6q3YCL_CNFyNW';

if($_GET['token_id']) {
    $mt_token_id = $_GET['token_id'];
}

$body = [
    'message' => [
        'token' => $mt_token_id,
        // 'notification' => [
        //     'title' => date("Y-m-d H:i:s").' Breaking News2 22 Breaking News2 22 Breaking News2 22 Breaking News2 22 Breaking News2 22 Breaking News2 22 ',
        //     'body' => '쫀몽그커타할에 오쏘힌쳘이라 제농방은 기잉교오정면 마라아서 의기의. 언손과 왈드욱다가 래뉘란드어 다영, 빈누꿔혼은 르퐁얼이의 가기바도가 뎬펵구웠과 가왹에 을골가렙으로, 얀으숑수. 힉리 앴읐한났은 옵엉촐이 그비 뗘개저언도, 야주쟌 몬어다먀는데 뫼운는다 애돈느게 딘듖뭥을 호즈링힌을. 엘났 룰몬더는 고뎐에 머캅겨를 애흘창이, 리알튼는다. 비아 모개혀글며다 려퍼해밍어 노캐혼 담은시런비뿌와 하두믜도, 환솧쟝켜가 오늤은 미뷰캐도 찬헌몽후 룬매. 둘자글읏부터 어작이 션고울 틍아놋머도 훊지덱다로 일뭅알피레 도몽젔딩은 이미샀니 엉있스 나막배에서. 넬닥아아우채 목근자다 헤딸 업벙 흐상을 리타냑헝의 랑자샌을 미릴댔. 간니우신쑈둬속을 니헤할자스거디렝너를 디미경국은 벤꼰기어에서 붜뇌자와 수루고다.',
        // ],
        'data' => [
            'title' => date("Y-m-d H:i:s").' Breaking News2 22 Breaking News2 22 Breaking News2 22 Breaking News2 22 Breaking News2 22 Breaking News2 22 ',
            'body' => '쫀몽그커타할에 오쏘힌쳘이라 제농방은 기잉교오정면 마라아서 의기의. ',
            'event_url' => 'https://smap.dmonster.kr/notice_detail?nt_idx=2',
            'image' => 'https://gjfe.dmonster.kr/img/uploads/summernote_nt_content_1_20231030164832.jpg',
        ],
        // 'android' => [
        //     'notification' => [
        //         'image' => 'https://gjfe.dmonster.kr/img/uploads/summernote_nt_content_1_20231030164832.jpg',
        //     ]
        // ],
        // 'apns' => [
        //     'payload' => [
        //         'aps' => [
        //             'mutable-content' => 1
        //         ],
        //     ],
        //     'fcm_options' => [
        //         'image' => 'https://smap.dmonster.kr/img/sample01.png'
        //     ],
        // ],
    ],
];

printr($body);

$rtn = $send_fcm->send($body);

printr($rtn);


// $fname = 'SMAP';
// $fmail = 'admin@schedulemap.com';
// $to = 'lcm790203@naver.com';
// $tname = '이창민';
// $subject = '[smap]테스트메일 제목';
// $content = '[smap]테스트메일 내용';

// $rtn1 = mailer_new($fname, $fmail, $to, $tname, $subject, $content);

// // $rtn2 = sendMail($fname, $fmail, $tname, $to, $subject, $contents);

// // $rtn3 = mailer($fname, $fmail, $to, $tname, $subject, $content);

// printr($rtn1);

// printr($rtn2);

// printr($rtn3);

printr("DONE");

include $_SERVER['DOCUMENT_ROOT']."/tail.inc.php";
