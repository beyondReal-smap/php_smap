jconfirm.defaults = {
  scrollToPreviousElement: false,
  scrollToPreviousElementAnimate: false,
};

function jalert(c, t = "", a = "") {
  $.alert({
    title: t,
    type: "blue",
    typeAnimated: true,
    content: c,
    buttons: {
      confirm: {
        btnClass: "btn-default btn-lg btn-block",
        text: "확인",
      },
    },
    onClose: function () {
      if (a) {
        a;
      }
    },
  });
}

function jalert_url(c, u, t = "", f = "") {
  $.alert({
    title: t,
    type: "blue",
    typeAnimated: true,
    content: c,
    buttons: {
      confirm: {
        btnClass: "btn-default btn-lg btn-block",
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
    content: c,
    buttons: {
      confirm: {
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
        text: "아니요",
        btnClass: "btn-default btn-sm btn-block",
        action: function () {
          if (cancelCallback) {
            cancelCallback();
          }
        },
      },
    },
    onClose: function () {
      if (a) {
        a(); // 변경된 부분: 함수를 호출하도록 변경
      }
    },
  });
}