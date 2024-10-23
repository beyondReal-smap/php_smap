jconfirm.defaults = {
  scrollToPreviousElement: false,
  scrollToPreviousElementAnimate: false,
};

function jalert(c, t = "", a = "") {
  const lang = getCurrentLanguage(); // 현재 언어 가져오기
  $.alert({
    title: t,
    type: "blue",
    typeAnimated: true,
    content: `<div class="text-center pb-4"><p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4">${c}</p></div>`,
    buttons: {
      confirm: {
        btnClass: "btn btn-bg_gray btn-md w-100 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0",
        text: getTranslation('confirm', lang), // 언어에 따른 번역 가져오기
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
  const lang = getCurrentLanguage(); // 현재 언어 가져오기
  $.alert({
    title: t,
    type: "blue",
    typeAnimated: true,
    content: `<div class="text-center pb-4"><p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4">${c}</p></div>`,
    buttons: {
      confirm: {
        btnClass: "btn btn-bg_gray btn-md w-100 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0",
        text: getTranslation('confirm', lang),
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
  const lang = getCurrentLanguage(); // 현재 언어 가져오기
  $.alert({
    title: t,
    type: "blue",
    typeAnimated: true,
    content: `<div class="text-center pb-4"><p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4">${c}</p></div>`,
    buttons: {
      confirm: {
        btnClass: "btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0",
        text: getTranslation('confirm', lang),
      },
    },
    onClose: function () {
      $("#" + i).focus();
    },
  });
}

function jalert_confirm(c, u, t = "", a = "") {
  const lang = getCurrentLanguage(); // 현재 언어 가져오기
  $.alert({
    title: t,
    type: "blue",
    typeAnimated: true,
    content: `<div class="text-center pb-4"><p class="fs_16 text_dynamic fw_700 line_h1_3 mt-4">${c}</p></div>`,
    // contentLoaded: function () {
    //   // 메시지 텍스트 자간 조정
    //   $('.jconfirm-content').css('letter-spacing', '1px');
    //   // 메시지 텍스트 줄간격 조정
    //   $('.jconfirm-content').css('line-height', '1.5');
    //   // 메시지 텍스트 글자 크기 조정
    //   $('.jconfirm-content').css('font-size', '26px');
    // },
    buttons: {
      confirm: {
        btnClass: "btn-secondary btn-sm btn-block",
        text: getTranslation('yes', lang), // 언어에 따른 번역 가져오기
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
        text: getTranslation('no', lang), // 언어에 따른 번역 가져오기
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

// 현재 언어를 가져오는 함수
function getCurrentLanguage() {
  // 브라우저의 기본 언어 설정을 가져옵니다
  const browserLang = navigator.language || navigator.userLanguage;
  
  // 언어 코드의 첫 두 글자만 사용합니다 (예: 'en-US'에서 'en'만 사용)
  const langCode = browserLang.substr(0, 2).toLowerCase();
  
  // 지원하는 언어 목록
  const supportedLangs = ['ko', 'en', 'id', 'ja', 'es', 'vi', 'hi', 'th']; // 새로운 언어 추가
  
  // 브라우저 언어가 지원되는 경우 해당 언어를 반환, 그렇지 않으면 기본값으로 'en' 반환
  return supportedLangs.includes(langCode) ? langCode : 'en';
}

// 번역을 가져오는 함수
function getTranslation(key, lang) {
  const translations = {
    'ko': {
      'confirm': '확인',
      'yes': '네',
      'no': '아니요'
    },
    'en': {
      'confirm': 'Confirm',
      'yes': 'Yes',
      'no': 'No'
    },
    'id': {
      'confirm': 'Konfirmasi',
      'yes': 'Ya',
      'no': 'Tidak'
    },
    'ja': {
      'confirm': '確認',
      'yes': 'はい',
      'no': 'いいえ'
    },
    'es': {
      'confirm': 'Confirmar',
      'yes': 'Sí',
      'no': 'No'
    },
    'vi': {
      'confirm': 'Xác nhận',
      'yes': 'Có',
      'no': 'Không'
    },
    'hi': {
      'confirm': 'पुष्टि करें',
      'yes': 'हाँ',
      'no': 'नहीं'
    },
    'th': {
      'confirm': 'ยืนยัน',
      'yes': 'ใช่',
      'no': 'ไม่'
    }
  };
  return translations[lang][key] || key;
}