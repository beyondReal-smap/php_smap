Kakao.init("aedb04496bb7d33dcf2d4f3a94142503");

function f_share_link(t) {
    var currentURL = $("#share_url").val();

    if (t == "kakao") {
        Kakao.Link.sendDefault({
            objectType: "feed",
            content: {
                title: JS_SHARE_TITLE, // 콘텐츠의 타이틀
                description: JS_SHARE_DESC, // 콘텐츠 상세설명
                imageUrl: JS_SHARE_IMG, // 썸네일 이미지
                imageWidth: "400",
                imageHeight: "400",
                link: {
                    mobileWebUrl: currentURL, // 모바일 카카오톡에서 사용하는 웹 링크 URL
                    webUrl: currentURL, // PC버전 카카오톡에서 사용하는 웹 링크 URL
                },
            },
            buttons: [
                {
                    title: _btnTitle, // 버튼 제목
                    link: {
                        mobileWebUrl: currentURL, // 모바일 카카오톡에서 사용하는 웹 링크 URL
                        webUrl: currentURL, // PC버전 카카오톡에서 사용하는 웹 링크 URL
                    },
                },
            ],
        });
    } else if (t == "contact") {
        jalert("연락처 열기");
    }
}
