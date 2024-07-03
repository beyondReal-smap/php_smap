$('.custom-select').on('change',function () {
    $(this).css('color', 'var(--input_text)');
});

document.addEventListener('DOMContentLoaded', function() {
    const testElements = document.querySelectorAll('.text_dynamic');

    testElements.forEach(function(element) {
        if (element.textContent.includes(' ')) {
            element.style.whiteSpace = 'pre-line'; // 띄어쓰기가 있으면 줄바꿈 유지
        } else {
            element.style.wordBreak = 'break-all'; // 띄어쓰기가 없으면 자동 줄바꿈
        }
    });
});


 // 배너텍스트 높이 동일하게


//모달 달력 스크롤 
// document.addEventListener('DOMContentLoaded', function () {

//     var scheduleDateTime = document.getElementById('schedule_date_time');
//     var moCldBody = document.querySelector('.mo_cld_body');
//     var moCldBodyIn = document.querySelector('.mo_cld_body_in');

//     // #schedule_date_time을 클릭하거나
//     // .mo_cld_body에 scroll 이벤트
//     function handleInteraction() {
//         // .off 클래스를 .mo_cld_body에 추가합니다.
//         moCldBody.classList.add('off');
//     }

//     // #schedule_date_time에 click 이벤트 추가
//     scheduleDateTime.addEventListener('click', handleInteraction);

//     // .mo_cld_body에 scroll 이벤트 추가
//     moCldBodyIn.addEventListener('scroll', handleInteraction);
// });

// 수평
window.onload = function () {
    const tabScrollElements = document.querySelectorAll(".tab_scroll");
    
    tabScrollElements.forEach(function(tabScrollElement) {
        bindScroll(tabScrollElement);
    });
};

function bindScroll(element) {
    // 이전 스크립트 내용을 그대로 사용

    const slider = element;
    let isDragging = false;
    let startX;
    let scrollLeft;

    slider.addEventListener("mousedown", (e) => {
        isDragging = true;
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
        cancelMomentumTracking();
    });

    document.addEventListener("mousemove", (e) => {
        if (!isDragging) return;
        e.preventDefault();
        const x = e.pageX - slider.offsetLeft;
        const walk = (x - startX) * 3;
        var prevScrollLeft = slider.scrollLeft;
        slider.scrollLeft = scrollLeft - walk;
        velX = slider.scrollLeft - prevScrollLeft;
    });

    document.addEventListener("mouseup", () => {
        if (isDragging) {
            isDragging = false;
            beginMomentumTracking();
        }
    });

    slider.addEventListener("touchstart", (e) => {
        isDragging = true;
        startX = e.touches[0].pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
        cancelMomentumTracking();
    });

    document.addEventListener("touchmove", (e) => {
        if (!isDragging) return;
        e.preventDefault();
        const x = e.touches[0].pageX - slider.offsetLeft;
        const walk = (x - startX) * 3;
        var prevScrollLeft = slider.scrollLeft;
        slider.scrollLeft = scrollLeft - walk;
        velX = slider.scrollLeft - prevScrollLeft;
    }, { passive: false }); // "passive"를 false로 설정

    document.addEventListener("touchend", () => {
        if (isDragging) {
            isDragging = false;
            beginMomentumTracking();
        }
    });

    var velX = 0;
    var momentumID;

    function beginMomentumTracking() {
        cancelMomentumTracking();
        momentumID = requestAnimationFrame(momentumLoop);
    }

    function cancelMomentumTracking() {
        cancelAnimationFrame(momentumID);
    }

    function momentumLoop() {
        slider.scrollLeft += velX;
        velX *= 0.95;
        if (Math.abs(velX) > 0.5) {
            momentumID = requestAnimationFrame(momentumLoop);
        }
    }
}


//수직

function bindVerticalScroll(element) {
    const slider = element;
    let isDragging = false;
    let startY;
    let scrollTop;

    slider.addEventListener("mousedown", (e) => {
        isDragging = true;
        startY = e.pageY - slider.offsetTop;
        scrollTop = slider.scrollTop;
        cancelMomentumTracking();
    });

    document.addEventListener("mousemove", (e) => {
        if (!isDragging) return;
        e.preventDefault();
        const y = e.pageY - slider.offsetTop;
        const walk = (y - startY) * 3;
        var prevScrollTop = slider.scrollTop;
        slider.scrollTop = scrollTop - walk;
        velY = slider.scrollTop - prevScrollTop;
    });

    document.addEventListener("mouseup", () => {
        if (isDragging) {
            isDragging = false;
            beginMomentumTracking();
        }
    });

    slider.addEventListener("touchstart", (e) => {
        isDragging = true;
        startY = e.touches[0].pageY - slider.offsetTop;
        scrollTop = slider.scrollTop;
        cancelMomentumTracking();
    });

    document.addEventListener("touchmove", (e) => {
        if (!isDragging) return;
        e.preventDefault();
        const y = e.touches[0].pageY - slider.offsetTop;
        const walk = (y - startY) * 3;
        var prevScrollTop = slider.scrollTop;
        slider.scrollTop = scrollTop - walk;
        velY = slider.scrollTop - prevScrollTop;
    }, { passive: false }); // "passive"를 false로 설정

    document.addEventListener("touchend", () => {
        if (isDragging) {
            isDragging = false;
            beginMomentumTracking();
        }
    });

    var velY = 0;
    var momentumID;

    function beginMomentumTracking() {
        cancelMomentumTracking();
        momentumID = requestAnimationFrame(momentumLoop);
    }

    function cancelMomentumTracking() {
        cancelAnimationFrame(momentumID);
    }

    function momentumLoop() {
        slider.scrollTop += velY;
        velY *= 0.95;
        if (Math.abs(velY) > 0.5) {
            momentumID = requestAnimationFrame(momentumLoop);
        }
    }
}

window.onload = function () {
    const tabScrollElements = document.querySelectorAll(".tab_scroll");
    const tabScrollYElements = document.querySelectorAll(".tab_scroll_y");

    tabScrollElements.forEach(function (tabScrollElement) {
        bindScroll(tabScrollElement);
    });

    tabScrollYElements.forEach(function (tabScrollYElement) {
        bindVerticalScroll(tabScrollYElement);
    });
};



  
  

//상세페이지
//텝클릭시 스크롤
$('.tab_menu li').click(function(e) {
    e.preventDefault();
    
    var targetId = $(this).find('a').attr('href');
    var targetOffset;
    
    if ($(window).width() >= 991) {
        targetOffset = $(targetId).offset().top - ($(window).height() * 0.12);
    } else {
        targetOffset = $(targetId).offset().top - ($(window).height() * 0.08);
    }
    
    $('html, body').animate({
        scrollTop: targetOffset
    }, 500);
    
    // .on 클래스 제어
    $('.tab_menu li a').click(function(){
        $(this).find('a').toggleClass('on');
    });
});

$(".review_more").click(function () {
    // 클릭된 요소의 부모 요소에서 ".detail_review_cont" 요소를 찾습니다.
    var $detailReviewCont = $(this).closest(".detail_review_cont");

    // ".on" 클래스가 없는 경우에는 클래스를 추가하고, 있는 경우에는 제거합니다.
    if (!$detailReviewCont.hasClass("on")) {
        $detailReviewCont.addClass("on");
    } else {
        $detailReviewCont.removeClass("on");
    }
});


//테이블 가로스크롤 마우스로 터치
// window.onload = function () {
//     if ($(".tab_scroll").length) {
//       touchScroll(".tab_scroll");
//     }
//   };

//   function touchScroll($bind = "") {
//     const slider = document.querySelector($bind);
//     let isDown = false;
//     let startX;
//     let scrollLeft;

//     slider.addEventListener("mousedown", (e) => {
//       isDown = true;
//       slider.classList.add("active");
//       startX = e.pageX - slider.offsetLeft;
//       scrollLeft = slider.scrollLeft;
//       cancelMomentumTracking();
//     });

//     slider.addEventListener("mouseleave", () => {
//       isDown = false;
//       slider.classList.remove("active");
//     });

//     slider.addEventListener("mouseup", () => {
//       isDown = false;
//       slider.classList.remove("active");
//       beginMomentumTracking();
//     });

//     slider.addEventListener("mousemove", (e) => {
//       if (!isDown) return;
//       e.preventDefault();
//       const x = e.pageX - slider.offsetLeft;
//       const walk = (x - startX) * 3; //scroll-fast
//       var prevScrollLeft = slider.scrollLeft;
//       slider.scrollLeft = scrollLeft - walk;
//       velX = slider.scrollLeft - prevScrollLeft;
//     });

//     slider.addEventListener("wheel", (e) => {
//       cancelMomentumTracking();
//     });

//     var velX = 0;
//     var momentumID;

//     function beginMomentumTracking() {
//       cancelMomentumTracking();
//       momentumID = requestAnimationFrame(momentumLoop);
//     }
//     function cancelMomentumTracking() {
//       cancelAnimationFrame(momentumID);
//     }
//     function momentumLoop() {
//       slider.scrollLeft += velX;
//       velX *= 0.95;
//       if (Math.abs(velX) > 0.5) {
//         momentumID = requestAnimationFrame(momentumLoop);
//       }
//     }
//   }

// 모바일 메뉴
$('.hd_menu_btn').on('click',function(){
    $('body').addClass('menu_on');
});

// 모바일 메뉴 닫기
$('.close_btn_wr').on('click',function(){
    $('body').removeClass('menu_on');
});

// 검은색 배경 눌러도 메뉴 닫기
$('.menu_bg').on('click',function(){
    $('body').removeClass('menu_on');
});

// 모바일 메뉴 내부
$('.m_nav .nav_a').on('click',function(){
    // 2차메뉴가 있을경우
    if($(this).siblings('.nav_ul2').length){
        // 2차 메뉴가 열려있을경우
        if($(this).siblings('.nav_ul2').hasClass('on')){

        } else{
            $('.nav_ul2').slideUp();
            $('.nav_ul2').removeClass('on');
        }
        $(this).siblings('.nav_ul2').slideToggle().toggleClass('on');
    }
    });
    // PC Nav
    $('.pc_nav .nav_a').on('click',function(e){
        // 2차메뉴가 있을경우
        if($(this).siblings('.nav_ul2').length){
            e.preventDefault();
            // 2차 메뉴가 열려있을경우
            if($(this).siblings('.nav_ul2').hasClass('on')){

            } else{
                $('.pc_nav .nav_ul2').slideUp();
                $('.pc_nav .nav_ul2').removeClass('on');
            }
            $(this).siblings('.nav_ul2').slideToggle().toggleClass('on');
        }
    });

    // 다른곳 클릭시 PC nav 닫기
    $('html').click(function (e) {
        if ($(e.target).parents('.nav_li').length < 1) {
            $('.pc_nav .nav_ul2').slideUp();
            $('.pc_nav .nav_ul2').removeClass('on');
        }
});

// 스크롤 헤더
$(window).scroll(function(){
    if($(window).scrollTop() > 50){
        $('.head_06').addClass('scroll');
        // 탑버튼 활성화
        $('.top_btn_wr').addClass('active');
    }else{
        $('.head_06').removeClass('scroll');
        // 탑버튼 비활성화
        $('.top_btn_wr').removeClass('active');
    }
})

// 탑버튼
$('.top_btn').click(function (event) {
    event.preventDefault();
    $('html, body').animate({ scrollTop: 0 }, 400);
});

// bottom fixed 버튼이 있는 경우 탑버튼 위치 변경
$(document).ready(function(){
    if ($('.b_botton').length) {
        $('.top_btn_wr').addClass('b_on');
        $('.sub_pg').css({"paddingBottom" :  "10rem"});
    }
})


// 토스트 toast
const toastTrigger = document.getElementById('ToastBtn')
const toastToast = document.getElementById('Toast');
if (toastTrigger) {
        toastTrigger.addEventListener('click', () => {
        const toast_confirm = new bootstrap.Toast(toastToast);
        toast_confirm.show();
    });
}

// 비밀번호 표시 비표시 버튼 on/off
$('.pw_eye').on('click', function () {
    $('.pw_eye').toggleClass('on');
});


//좋아요 토글
$('.like_btn').on('click', function () {
    $('.like_btn').toggleClass('on');
});


//중첩모달
// 두 번째 모달을 수동으로 열기
$('.open_contact_modal_btn').click(function () {
    $('#contact_modal').modal('show');
});

// 두 번째 모달이 열릴 때 이벤트 리스너 추가
$('#contact_modal').on('show.bs.modal', function () {
    // body에 .modal-open 클래스 추가
    $('body').addClass('modal-open');
});

// 두 번째 모달이 닫힐 때 이벤트 리스너 추가 (선택 사항)
$('#contact_modal').on('hidden.bs.modal', function () {
    // body에서 .modal-open 클래스 제거
    $('body').removeClass('modal-open');
});



// 채팅 입력창의 높이를 내용에 맞게 조절
function adjustTextareaHeight() {
    var textarea = $(".line_ip textarea");
    textarea.css("height", "2.0rem"); // 임시로 높이 초기화
    var newHeight = textarea[0].scrollHeight; // 스크롤이 생기는 높이
    textarea.css("height", newHeight + "px");
}

// 채팅 입력창에 내용이 변경될 때마다 높이 조절
$(".line_ip textarea").on("input", function () {
    adjustTextareaHeight();
});



$('.flt_close').click(function(){
    $('.floating_wrap').removeClass('on');
});

