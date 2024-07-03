var eng_num = /[^a-zA-Z0-9_-]/g;
var eng_kor = /[^a-zA-Zㄱ-ㅎ가-힣]/g;
var eng_kor_num = /[^a-zA-Zㄱ-ㅎ가-힣0-9]/g;
var num = /[^0-9]/g;
var eng = /[^a-zA-Z]/g;
var kor = /[ㄱ-ㅎ가-힣]/g;
var email = /[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,3}$/i;
var emailf = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/;
var password = /^.*(?=.{6,20})(?=.*[0-9])(?=.*[a-zA-Z]).*$/;
var space = /\s/g;

$(document).ready(function () {
    $(document).on("keyup", "input:text[numberOnly]", function () {
        $(this).val(
            $(this)
                .val()
                .replace(/[^0-9]/gi, "")
        );
    });
    $(document).on("keyup", "input:text[abcOnly]", function () {
        $(this).val(
            $(this)
                .val()
                .replace(/[^a-zA-Z0-9]/gi, "")
        );
    });
    $(document).on("keyup", "input:text[datetimeOnly]", function () {
        $(this).val(
            $(this)
                .val()
                .replace(/[^0-9:\-]/gi, "")
        );
    });
    $(document).on("keyup", "input:text[abcOnlySamll]", function () {
        $(this).val(
            $(this)
                .val()
                .replace(/[^a-z0-9]/gi, "")
        );
    });
    $('a[data-toggle="tooltip"]').tooltip({
        animated: "fade",
        html: true,
    });
});

(function ($) {
    "use strict";
    $(function () {
        $(document).on("mouseenter mouseleave", ".sidebar .nav-item", function (ev) {
            var body = $("body");
            var sidebarIconOnly = body.hasClass("sidebar-icon-only");
            var sidebarFixed = body.hasClass("sidebar-fixed");
            if (!("ontouchstart" in document.documentElement)) {
                if (sidebarIconOnly) {
                    if (sidebarFixed) {
                        if (ev.type === "mouseenter") {
                            body.removeClass("sidebar-icon-only");
                        }
                    } else {
                        var $menuItem = $(this);
                        if (ev.type === "mouseenter") {
                            $menuItem.addClass("hover-open");
                        } else {
                            $menuItem.removeClass("hover-open");
                        }
                    }
                }
            }
        });

        $('[data-toggle="offcanvas"]').on("click", function () {
            $(".sidebar-offcanvas").toggleClass("active");
        });

        var body = $("body");
        var contentWrapper = $(".content-wrapper");
        var scroller = $(".container-scroller");
        var footer = $(".footer");
        var sidebar = $(".sidebar");
        sidebar.on("show.bs.collapse", ".collapse", function () {
            sidebar.find(".collapse.show").collapse("hide");
        });
        applyStyles();

        function addActiveClass(element) {
            if (current === "") {
                //for root url
                if (element.attr("href").indexOf("index.html") !== -1) {
                    element.parents(".nav-item").last().addClass("active");
                    if (element.parents(".sub-menu").length) {
                        element.closest(".collapse").addClass("show");
                        element.addClass("active");
                    }
                }
            } else {
                //for other url
                if (element.attr("href").indexOf(current) !== -1) {
                    element.parents(".nav-item").last().addClass("active");
                    if (element.parents(".sub-menu").length) {
                        element.closest(".collapse").addClass("show");
                        element.addClass("active");
                    }
                    if (element.parents(".submenu-item").length) {
                        element.addClass("active");
                    }
                }
            }
        }
        var current = location.pathname
            .split("/")
            .slice(-1)[0]
            .replace(/^\/|\/$/g, "");
        $(".nav li a", sidebar).each(function () {
            var $this = $(this);
            //addActiveClass($this);
        });

        function applyStyles() {
            if (!body.hasClass("rtl")) {
                if ($(".settings-panel .tab-content .tab-pane.scroll-wrapper").length) {
                    const settingsPanelScroll = new PerfectScrollbar(".settings-panel .tab-content .tab-pane.scroll-wrapper");
                }
                if ($(".chats").length) {
                    const chatsScroll = new PerfectScrollbar(".chats");
                }
                if (body.hasClass("sidebar-fixed")) {
                    if ($("#sidebar").length) {
                        var fixedSidebarScroll = new PerfectScrollbar("#sidebar .nav");
                    }
                }
            }
        }
        $('[data-toggle="minimize"]').on("click", function () {
            if (body.hasClass("sidebar-toggle-display") || body.hasClass("sidebar-absolute")) {
                body.toggleClass("sidebar-hidden");
            } else {
                body.toggleClass("sidebar-icon-only");
            }
        });
        $(".form-check label,.form-radio label").append('<i class="input-helper"></i>');
        $('[data-toggle="horizontal-menu-toggle"]').on("click", function () {
            $(".horizontal-menu .bottom-navbar").toggleClass("header-toggled");
        });
        var navItemClicked = $(".horizontal-menu .page-navigation >.nav-item");
        navItemClicked.on("click", function (event) {
            if (window.matchMedia("(max-width: 991px)").matches) {
                if (!$(this).hasClass("show-submenu")) {
                    navItemClicked.removeClass("show-submenu");
                }
                $(this).toggleClass("show-submenu");
            }
        });
        $(window).scroll(function () {
            if (window.matchMedia("(min-width: 992px)").matches) {
                var header = $(".horizontal-menu");
                if ($(window).scrollTop() >= 70) {
                    $(header).addClass("fixed-on-scroll");
                } else {
                    $(header).removeClass("fixed-on-scroll");
                }
            }
        });

        /* Code for attribute data-custom-class for adding custom class to tooltip */
        if (typeof $.fn.popover.Constructor === "undefined") {
            throw new Error("Bootstrap Popover must be included first!");
        }
        var Popover = $.fn.popover.Constructor;
        // add customClass option to Bootstrap Tooltip
        $.extend(Popover.Default, {
            customClass: "",
        });
        var _show = Popover.prototype.show;
        Popover.prototype.show = function () {
            // invoke parent method
            _show.apply(this, Array.prototype.slice.apply(arguments));
            if (this.config.customClass) {
                var tip = this.getTipElement();
                $(tip).addClass(this.config.customClass);
            }
        };
        $('[data-toggle="popover"]').popover();
    });
})(jQuery);

function retire(url) {
    $.confirm({
        title: "경고",
        content: "정말 탈퇴하시겠습니까?",
        buttons: {
            confirm: {
                text: "확인",
                action: function () {
                    hidden_ifrm.location.href = url;
                },
            },
            cancel: {
                text: "취소",
                action: function () {
                    close();
                },
            },
        },
    });

    return false;
}

function update_confirm(txt, url) {
    $.confirm({
        title: "경고",
        content: txt,
        buttons: {
            confirm: {
                text: "확인",
                action: function () {
                    hidden_ifrm.location.href = url;
                },
            },
            cancel: {
                text: "취소",
                action: function () {
                    close();
                },
            },
        },
    });

    return false;
}

function comma_num(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function setCookie(cName, cValue, cDay) {
    var expire = new Date();
    expire.setDate(expire.getDate() + cDay);
    cookies = cName + "=" + escape(cValue) + "; path=/ ";
    if (typeof cDay != "undefined") cookies += ";expires=" + expire.toGMTString() + ";";
    document.cookie = cookies;
}

function get_text_length(str, obj) {
    var len = 0;

    for (var i = 0; i < str.length; i++) {
        if (escape(str.charAt(i)).length == 6) {
            len++;
        }
        len++;
    }

    if (len > 0) {
        $(obj).html(len);
    }

    return false;
}

function f_checkbox_all(obj) {
    $('input:checkbox[name="' + obj + '[]"]').each(function () {
        if ($(this).prop("checked") == true) {
            $(this).prop("checked", false);
        } else {
            $(this).prop("checked", true);
        }
    });

    return false;
}

function getCookie(cName) {
    cName = cName + "=";
    var cookieData = document.cookie;
    var start = cookieData.indexOf(cName);
    var cValue = "";
    if (start != -1) {
        start += cName.length;
        var end = cookieData.indexOf(";", start);
        if (end == -1) end = cookieData.length;
        cValue = cookieData.substring(start, end);
    }
    return unescape(cValue);
}

function popup(url, wval, hval, tval, lval) {
    window.open(url, "popup", "height=" + hval + ",width=" + wval + ",top=" + tval + ",left=" + lval + ",menubar=no,scrollbars=no,status=yes");
}

function f_retire_mem(mt_idx) {
    $.confirm({
        title: "경고",
        content: "정말 탈퇴하시겠습니까?",
        buttons: {
            confirm: {
                text: "확인",
                action: function () {
                    $.post("./member_update", { act: "retire", mt_idx_t: mt_idx }, function (data) {
                        if (data == "Y") {
                            $.alert({
                                title: "",
                                content: "관리자권한 회원탈퇴 처리되었습니다.",
                                buttons: {
                                    confirm: {
                                        text: "확인",
                                        action: function () {
                                            f_localStorage_reset_go("./member_list");
                                        },
                                    },
                                },
                            });
                        }
                    });
                },
            },
            cancel: {
                text: "취소",
                action: function () {
                    close();
                },
            },
        },
    });

    return false;
}

function f_return_mem(mt_idx) {
    $.confirm({
        title: "경고",
        content: "정말 복구처리하겠습니까?",
        buttons: {
            confirm: {
                text: "확인",
                action: function () {
                    $.post("./member_retire_update", { act: "return", mt_idx_t: mt_idx }, function (data) {
                        if (data == "Y") {
                            $.alert({
                                title: "",
                                content: "관리자권한 회원 복구 처리되었습니다.",
                                buttons: {
                                    confirm: {
                                        text: "확인",
                                        action: function () {
                                            f_localStorage_reset_go("./member_list");
                                        },
                                    },
                                },
                            });
                        }
                    });
                },
            },
            cancel: {
                text: "취소",
                action: function () {
                    close();
                },
            },
        },
    });

    return false;
}

function f_preview_image_delete(obj_id, obj_name) {
    var obj_t = obj_name + obj_id;

    if (obj_t) {
        $("#" + obj_t).val("");
        $("#" + obj_t + "_on").val("");
        $("#" + obj_t + "_del").hide();
        $("#" + obj_t + "_box").css("border", "1px dashed #ddd");
        $("#" + obj_t + "_box").html('<i class="mdi mdi-plus"></i>');
    }
}

function f_preview_image_selected(e, obj_id, obj_name) {
    var files = e.target.files;
    var filesArr = Array.prototype.slice.call(files);
    var obj_t = obj_name + obj_id;

    filesArr.forEach(function (f) {
        if (!f.type.match("image.*")) {
            jalert("확장자는 이미지 확장자만 가능합니다.");
            return;
        }

        if (f.size > 12000000) {
            jalert("업로드는 10메가 이하만 가능합니다.");
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            $("#" + obj_t + "_box").css("border", "none");
            $("#" + obj_t + "_box").html('<img src="' + e.target.result + '" />');
            $("#" + obj_t + "_del").show();
        };
        reader.readAsDataURL(f);
    });
}

function sendfile_summernote(ctype, file, no, editor) {
    if (!file.type.match("image.*")) {
        jalert("확장자는 이미지 확장자만 가능합니다.");
        return;
    }

    if (file.size > 12000000) {
        jalert("업로드는 10메가 이하만 가능합니다.");
        return;
    }

    var form_data = new FormData();
    form_data.append("act", "upload");
    form_data.append("ctype", ctype);
    form_data.append("file_no", no);
    form_data.append("file", file);
    $.ajax({
        data: form_data,
        type: "POST",
        enctype: "multipart/form-data",
        url: "./sendfile_summernote.php",
        cache: false,
        timeout: 5000,
        contentType: false,
        processData: false,
        success: function (data) {
            var obj = JSON.parse(data);
            $(editor).summernote("insertImage", obj.url);
        },
        error: function (err) {
            console.log(err);
        },
    });
}

function f_preview_file_selected(e, obj_id, obj_name) {
    var files = e.target.files;
    var filesArr = Array.prototype.slice.call(files);
    var obj_t = obj_name + obj_id;

    filesArr.forEach(function (f) {
        if (f.size > 12000000) {
            jalert("업로드는 10메가 이하만 가능합니다.");
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            $("#" + obj_t + "_box").html('<div class="inx-chip">' + f.name + ' <a class="inx-chip-action" href="javascript:f_preview_file_delete(\'' + obj_id + "', '" + obj_name + '\');"><i class="mdi mdi-close-circle"></i></a></div>');
        };
        reader.readAsDataURL(f);
    });
}

function f_preview_file_delete(obj_id, obj_name) {
    var obj_t = obj_name + obj_id;

    if (obj_t) {
        $("#" + obj_t).val("");
        $("#" + obj_t + "_on").val("");
        $("#" + obj_t + "_box").html("");
    }
}

function f_localStorage_reset() {
    localStorage.clear();
}

function f_localStorage_reset_go(url) {
    localStorage.clear();
    location.href = url;
}

function f_get_box_mng_list_reset(obj_frm = "") {
    if (obj_frm) {
        var obj_frm_t = obj_frm + " ";
    } else {
        var obj_frm_t = "frm_list ";
    }

    $("#" + obj_frm_t)[0].reset();

    f_get_box_mng_list("1");
}

function f_get_box_mng_list(pg = "") {
    var form_t = $("#obj_frm").val();
    var obj_frm_t = "#" + form_t + " ";

    if (pg == null || pg == "") {
        var ls_obj_pg = localStorage.getItem("obj_pg");
        if (ls_obj_pg) {
            pg = ls_obj_pg;

            for (let i = 0; i < localStorage.length; i++) {
                let key = localStorage.key(i);
                if (localStorage.getItem(key) && $(obj_frm_t + "#" + key).val() == "") {
                    // $(obj_frm_t + "#" + key).val(localStorage.getItem(key));
                }
            }
        } else {
            pg = 1;
        }
    }

    $(obj_frm_t + "#obj_pg").val(parseInt(pg));

    var form_t = $("#" + form_t)[0];
    var formData_t = new FormData(form_t);

    $.ajax({
        url: $("#obj_uri").val(),
        enctype: "multipart/form-data",
        data: formData_t,
        type: "POST",
        async: true,
        contentType: false,
        processData: false,
        cache: true,
        timeout: 5000,
        success: function (data) {
            if (data) {
                for (const [key, value] of formData_t.entries()) {
                    localStorage.setItem(key, value);
                }

                $("#" + $(obj_frm_t + "#obj_list").val()).html(data);
            }
        },
        error: function (err) {
            console.log(err);
        },
    });

    return false;
}

function f_post_del(url, idx, f = "") {
    $.confirm({
        title: "경고",
        content: "정말 삭제하시겠습니까? 삭제된 자료는 복구되지 않습니다.",
        buttons: {
            confirm: {
                text: "확인",
                action: function () {
                    $.post(
                        url,
                        {
                            act: "delete",
                            obj_idx: idx,
                        },
                        function (data) {
                            if (data == "Y") {
                                f_get_box_mng_list();
                            }
                        }
                    );
                },
            },
            cancel: {
                text: "취소",
                action: function () {
                    close();
                },
            },
        },
    });

    return false;
}

function bytesToSize(x) {
    const units = ["bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];

    let l = 0,
        n = parseInt(x, 10) || 0;

    while (n >= 1024 && ++l) {
        n = n / 1024;
    }

    return n.toFixed(n < 10 && l > 0 ? 1 : 0) + " " + units[l];
}

function f_preview_one_image_delete(obj_t) {
    if (obj_t) {
        $("#" + obj_t).val("");
        $("#" + obj_t + "_on").val("");
        $("#" + obj_t + "_del").hide();
        $("#" + obj_t + "_box").css("border", "1px dashed #ddd");
        $("#" + obj_t + "_box").html('<i class="mdi mdi-plus"></i>');
    }
}

function f_preview_one_image_selected(e, obj_t, fs = "10485760") {
    var files = e.target.files;
    var filesArr = Array.prototype.slice.call(files);
    var file_up_max_t = bytesToSize(parseInt(fs));

    filesArr.forEach(function (f) {
        if (!f.type.match("image.*")) {
            jalert("확장자는 이미지 확장자만 가능합니다.");
            return;
        }

        if (f.size > fs) {
            jalert("업로드는 " + file_up_max_t + " 이하만 가능합니다.");
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            $("#" + obj_t + "_box").css("border", "none");
            $("#" + obj_t + "_box").html('<img src="' + e.target.result + '" />');
            $("#" + obj_t + "_del").show();
        };
        reader.readAsDataURL(f);
    });
}

function f_multi_file_select(e, obj_name, o = "") {
    var files = e.target.files;
    var filesArr = Array.prototype.slice.call(files);
    var obj_id = $("#" + obj_name + "_file_cnt").val();
    if (obj_id < 1) {
        obj_id = 1;
    }
    var file_up_max_t = parseInt($("#" + obj_name + "_file_up_num").val()) + 1;
    if (o == "") {
        var box_t = obj_name + "_file_up_box";
        var obj_t = obj_name + obj_id;
    } else {
        var box_t = o;
        var obj_t = obj_name;
        obj_id = 1;
    }

    if (obj_id >= file_up_max_t) {
        jalert("업로드는 " + file_up_max_t + "개 이하만 가능합니다.");
        return;
    } else {
        filesArr.forEach(function (f) {
            if (f.size > $("#" + obj_name + "_file_up_size").val()) {
                jalert("업로드는 " + bytesToSize($("#" + obj_name + "_file_up_size").val()) + " 이하만 가능합니다.");

                $("#" + obj_t).val("");
                $("#" + obj_t + "_on").val("");
                return false;
            }

            if (obj_id == 1) {
                $("#" + box_t).html("");
            }

            var reader = new FileReader();
            reader.onload = function (e) {
                $("#" + box_t).append(
                    '<li id="' +
                        obj_name +
                        obj_id +
                        '_li"><div> <a class="under"><u>' +
                        f.name +
                        '</u></a><span class="ml-3"> <span>' +
                        bytesToSize(e.loaded) +
                        '</span></span></div><div class="text-right"><button type="button" class="btn btn_icon" onclick="f_multi_file_delete(\'' +
                        obj_id +
                        "', '" +
                        obj_name +
                        "', '" +
                        o +
                        '\')"><i class="mdi mdi-close-box-outline"></i></button> </div></li>'
                );
                $("#btn_file_delete").show();
                $("#" + obj_t + "_ori").val(f.name);
            };
            reader.readAsDataURL(f);

            $("#" + obj_name + "_file_cnt").val(parseInt(obj_id) + 1);
            $("#" + obj_name + "_file_cnt_t").html(" (" + parseInt(obj_id) + ")");
        });
    }
}

function f_multi_file_delete(obj_id, obj_name, o = "") {
    if (o == "") {
        var obj_t = obj_name + obj_id;
    } else {
        var obj_t = obj_name;
    }
    var file_cnt_t = parseInt($("#" + obj_name + "_file_cnt").val());

    if (obj_t) {
        $("#" + obj_t).val("");
        $("#" + obj_t + "_on").val("");
        $("#" + obj_t + "_ori").val("");
        $("#" + obj_t + "_li").remove();
        if (o == "") {
            if (file_cnt_t == 2) {
                f_file_box_reset(obj_name);
            } else {
                $("#" + obj_name + "_file_cnt").val(file_cnt_t - 1);
                $("#" + obj_name + "_file_cnt_t").html(" (" + (file_cnt_t - 2) + ")");
            }
        } else {
            f_file_box_reset(obj_name, o);
        }
    }
}

function f_multi_file_delete_all(obj_name) {
    if (obj_name) {
        $("." + obj_name + "_v").val("");
        $("." + obj_name + "_vo").val("");
        $("." + obj_name + "_vi").val("");
        $("." + obj_name + "_vs").val("");
        f_file_box_reset(obj_name);
    }
}

function f_file_box_reset(obj_name, o = "") {
    if (o == "") {
        $("#" + obj_name + "_file_cnt").val(0);
        $("#" + obj_name + "_file_up_box").html('<li class="mt-2 mb-2"><div><i class="mdi mdi-cloud-upload-outline mr-2"></i>파일을 업로드해주세요</div></li>');
        $("#" + obj_name + "_file_cnt_t").html("");
        $("#btn_file_delete").hide();
    } else {
        $("#" + obj_name + "_file_cnt").val(0);
        $("#" + o).html('<li class="mt-2 mb-2"><div><i class="mdi mdi-cloud-upload-outline mr-2"></i>파일을 업로드해주세요</div></li>');
        $("#" + obj_name + "_file_cnt_t").html("");
        $("#btn_file_delete").hide();
    }

    return false;
}

function f_checkbox_cnt() {
    var chk_cnt = 0;
    var chk_idx_rtn = "";

    $('input:checkbox[name="chk_all[]"]').each(function () {
        if ($(this).prop("checked") == true) {
            chk_cnt++;
            chk_idx_rtn += $(this).val() + "|";
        }
    });

    if (chk_cnt < 1) {
        alert("처리할 내역을 선택해주세요.");
        return false;
    }

    return chk_idx_rtn;
}

function f_status_chk(v, u) {
    $.confirm({
        title: "변경",
        content: "정보를 변경하시겠습니까?",
        buttons: {
            confirm: {
                text: "확인",
                action: function () {
                    var chk_idx_rtn = f_checkbox_cnt();

                    if (chk_idx_rtn == "") {
                        return false;
                    }

                    $.post(
                        u,
                        {
                            act: "status_chk",
                            chk_idx_rtn: chk_idx_rtn,
                            chg_status: v,
                        },
                        function (data) {
                            if (data == "Y") {
                                jalert("변경되었습니다.", f_get_box_mng_list());
                            }
                        }
                    );
                },
            },
            cancel: {
                btnClass: "btn-outline-default",
                text: "취소",
                action: function () {
                    close();
                },
            },
        },
    });
}
function f_post_retf(url, idx) {
    $.confirm({
        title: "재학습",
        content: "해당 GPT를 재학습하시겠습니까?",
        buttons: {
            confirm: {
                text: "확인",
                action: function () {
                    $.post(
                        url,
                        {
                            act: "re_findtune",
                            obj_idx: idx,
                        },
                        function (data) {
                            if (data == "Y") {
                                f_get_box_mng_list();
                            } else if (data == "S") {
                                jalert("학습 중인 GPT입니다. 학습이 완료된 후에 다시 실행해주세요.");
                            } else if (data == "F") {
                                jalert("학습이 완료된 GPT입니다. 추가학습을 하시려면 상세에서 내용을 추가해주세요.");
                            } else {
                                jalert(data);
                            }
                        }
                    );
                },
            },
            cancel: {
                text: "취소",
                action: function () {
                    close();
                },
            },
        },
    });
}

function f_order_search_date_range(nm, sd, ed) {
    $("#sel_search_sdate").val(sd);
    $("#sel_search_edate").val(ed);

    $(".c_pt_selling_date_range").removeClass("btn-info text-white");
    $("#f_order_search_date_range" + nm).addClass("btn-info text-white");

    return false;
}

function gourl(url) {
    if (url != "") window.open(url);
}

function f_add_table_json_tbody(tr_obj) {
    var qq = $("#table_json_tbody tr:last").index() + 2;
    var clone_tr = $("#table_json_tbody").children().first().clone();
    clone_tr.find(".tr_input_val").val("");

    var max_node = $("#table_node_max").val();

    if (max_node < qq) {
        jalert("최대 " + max_node + "개 까지 가능합니다.");
        return false;
    }

    if (tr_obj) {
        $(tr_obj).parent().parent().after(clone_tr);
    } else {
        $("#table_json_tbody").append(clone_tr);
    }

    f_tr_node_no_chk();
}

function f_tr_node_no_chk() {
    var t = 1;
    $(".tr_node_no").each(function (e) {
        $(this).html(t);
        t = t + 1;
    });

    if ($(".tr_node_no_v").length) {
        var t = 1;
        $(".tr_node_no_v").each(function (e) {
            $(this).val(t);
            t = t + 1;
        });
    }
}

function f_delete_table_json_tbody(id) {
    var qq = $("#table_json_tbody tr:last").index() + 1;
    if (qq > 1) {
        $("#" + id).remove();

        f_tr_node_no_chk();
    } else {
        jalert("마지막 남은 1개는 삭제 할 수 없습니다.");
        return false;
    }
}
const mt_dp_arr = ["10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00"];
function getDate(id) {
    var obj_id = id.trim();
    if (obj_id == "" || obj_id == null) {
        return false;
    }

    if ($("#" + obj_id).val()) {
        return $("#" + obj_id).val();
    } else {
        return false;
    }
}
function getTime(sdate, edate, type = 1) {
    if (sdate == "" || edate == "") {
        return false;
    }
    let sid = sdate;
    let eid = edate;
    if (type == 1) {
        if ($("#" + eid).val()) {
            let sdataTime = $("#" + sid)
                .val()
                .split(" ");
            let edateTime = $("#" + eid)
                .val()
                .split(" ");
            if (edateTime == 1) {
                return false;
            }
            if (sdataTime[0] == edateTime[0] || $("#" + sid).val() == "") {
                return edateTime[1];
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        if ($("#" + sid).val()) {
            let sdataTime = $("#" + sid)
                .val()
                .split(" ");
            let edateTime = $("#" + eid)
                .val()
                .split(" ");
            if (sdataTime == 1) {
                return false;
            }
            if (sdataTime[0] == edateTime[0] || $("#" + eid).val() == "") {
                let mt_dp_arr_idx = mt_dp_arr.indexOf(sdataTime[1]);
                if (mt_dp_arr_idx < mt_dp_arr.length - 1) {
                    mt_dp_arr_idx += 1;
                } else {
                    return "18:00";
                }
                return mt_dp_arr[mt_dp_arr_idx];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
function f_add_datepicker(sid, eid) {
    if (sid == "" || eid == "") {
        return console.log("f_add_datepicker 오류");
    }
    $("#" + sid).datetimepicker({
        //mask:'9999-19-39 29:00',
        format: "Y-m-d H:i",
        allowTimes: mt_dp_arr,
        defaultTime: "10:00",
        onShow: function (ct) {
            this.setOptions({
                maxDate: getDate(eid),
                maxTime: getTime(sid, eid, 1),
            });
        },
        onChangeDateTime: function (ct) {
            this.setOptions({
                maxTime: getTime(sid, eid, 1),
            });
        },
    });
    $("#" + eid).datetimepicker({
        //mask:'9999-19-39 29:00',
        format: "Y-m-d H:i",
        useCurrent: false,
        allowTimes: mt_dp_arr,
        defaultTime: "17:00",
        onShow: function (ct) {
            this.setOptions({
                minDate: getDate(sid),
                minTime: getTime(sid, eid, 2),
            });
        },
        onChangeDateTime: function (ct) {
            this.setOptions({
                minTime: getTime(sid, eid, 2),
            });
        },
    });
}

function f_list_excel_download(url) {
    var form = $("#frm_list")[0];
    var form_data = new FormData(form);
    form_data.append("act", "excel");
    $.ajax({
        data: form_data,
        type: "POST",
        dataType: "json",
        enctype: "multipart/form-data",
        url: url,
        cache: false,
        timeout: 5000,
        contentType: false,
        processData: false,
        success: function (data) {
            // console.log(data);
            if (data["success"] == "Y") {
                gourl(data["url"]);
            } else {
                jalert(data["error"]);
            }
        },
        error: function (err) {},
    });
}

function f_sel_date_range(v) {
    if (v) {
        var sd = new moment();
        sd = sd.format("YYYY-MM-DD");
        var ed = new moment();
        ed = ed.add(v, "day").format("YYYY-MM-DD");

        $("#sel_search_sdate").val(sd);
        $("#sel_search_edate").val(ed);
    }
}

function f_status_chg(v, o, t) {
    $.alert({
        title: "",
        content: t,
        buttons: {
            confirm: {
                text: "확인",
                action: function () {
                    var form_data = new FormData();
                    form_data.append("act", "status_chg");
                    form_data.append("mt_idx", $("#frm_form #mt_idx").val());
                    form_data.append("mt_obj", o);
                    form_data.append("mt_val", v);

                    $.ajax({
                        url: "./member_update",
                        enctype: "multipart/form-data",
                        data: form_data,
                        type: "POST",
                        async: true,
                        contentType: false,
                        processData: false,
                        cache: true,
                        timeout: 5000,
                        success: function (data) {
                            if (data == "Y") {
                                jalert_url("수정되었습니다.", "reload");
                            }
                        },
                        error: function (err) {
                            console.log(err);
                        },
                    });
                },
            },
            cancel: {
                text: "취소",
                action: function () {
                    close();
                },
            },
        },
    });
}

function f_post_del_admin_member(url, idx, f = "") {
    $.confirm({
        title: "경고",
        content: "정말 삭제하시겠습니까? 삭제된 자료는 복구되지 않습니다.",
        buttons: {
            confirm: {
                text: "확인",
                action: function () {
                    $.post(
                        url,
                        {
                            act: "delete",
                            obj_idx: idx,
                        },
                        function (data) {
                            if (data == "Y") {
                                f_localStorage_reset_go("./member_admin_list");
                            }
                        }
                    );
                },
            },
            cancel: {
                text: "취소",
                action: function () {
                    close();
                },
            },
        },
    });

    return false;
}
