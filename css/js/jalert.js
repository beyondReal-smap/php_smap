jconfirm.defaults = {
  scrollToPreviousElement: false,
  scrollToPreviousElementAnimate: false,
};

function jalert(c, t = "", a = "") {
  $.alert({
    title: t,
    type: "blue",
    typeAnimated: true,
    // content: c,
    // 직접 CSS를 추가하여 content에 적용
    content: `<div class="text-center pb-4"><p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4">${c}</p></div>`,
    buttons: {
      confirm: {
        // btnClass: "btn-default btn-sm btn-block",
        btnClass: "btn btn-bg_gray btn-md w-100 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0",
        text: "확인",
      },
    },
    onClose: function () {
      if (a) {
        a();
      }
    },
  });
}

function jalert_url(c, u, t = "", f = "") {
  $.alert({
    title: t,
    type: "blue",
    typeAnimated: true,
    // content: c,
    content: `<div class="text-center pb-4"><p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4">${c}</p></div>`,
    buttons: {
      confirm: {
        // btnClass: "btn-default btn-sm btn-block",
        btnClass: "btn btn-bg_gray btn-md w-100 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0",
        text: "확인",
        action: function () {
          if (u == "back") {
            history.go(-1);
          } else if (u == "reload") {
            location.hash = "";
            location.reload();
          } else if (u == "focus") {
            $(f).focus();
          } else {
            if (u == "function") {
              document.write(f);
            } else {
              location.replace(u);
            }
          }
        },
      },
    },
  });
}

function jalert_focus(c, t = "", i = "") {
  $.alert({
    title: t,
    type: "blue",
    typeAnimated: true,
    // content: c,
    // 직접 CSS를 추가하여 content에 적용
    content: `<div class="text-center pb-4"><p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4">${c}</p></div>`,
    buttons: {
      confirm: {
        // btnClass: "btn-default btn-sm btn-block",
        btnClass: "btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0",
        text: "확인",
      },
    },
    onClose: function () {
      $("#" + i).focus();
    },
  });
}

function jalert_confirm(c, u, t = "", a = "") {
  $.alert({
    title: t,
    type: "blue",
    typeAnimated: true,
    content: c,
    contentLoaded: function () {
      // 메시지 텍스트 자간 조정
      $('.jconfirm-content').css('letter-spacing', '1px');
      // 메시지 텍스트 줄간격 조정
      $('.jconfirm-content').css('line-height', '1.5');
      // 메시지 텍스트 글자 크기 조정
      $('.jconfirm-content').css('font-size', '16px');
    },
    buttons: {
      confirm: {
        btnClass: "btn-secondary btn-sm btn-block",
        text: "네",
        action: function () {
          if (u == "back") {
            history.go(-1);
          } else if (u == "reload") {
            location.hash = "";
            location.reload();
          } else if (u == "focus") {
            $(f).focus();
          } else {
            if (u == "function") {
              document.write(f);
            } else {
              location.replace(u);
            }
          }
        },
      },
      cancel: {
        btnClass: "btn-default btn-sm btn-block",
        text: "아니요",
        action: function () {
          if (cancelCallback) {
            cancelCallback();
          }
        },
      },
    },
    onClose: function () {
      if (a) {
        a();
      }
    },
  });
}