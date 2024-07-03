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
// 변수를 통해 endAd 호출 여부를 저장
var adEnded = false;
var failcount = 0;
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
});

function f_preview_image_delete(obj_id, obj_name) {
    var obj_t = obj_name + obj_id;

    if (obj_t) {
        $("#" + obj_t).val("");
        $("#" + obj_t + "_on").val("");
        $("#" + obj_t + "_del").hide();
        $("#" + obj_t + "_box").css("border", "1px dashed #ddd");
        $("#" + obj_t + "_box").html('<i class="xi-plus"></i>');
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

        if (f.size > 1200000) {
            jalert("업로드는 1메가 이하만 가능합니다.");
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

function f_hp_chk() {
    if ($("#srt_tel").val() == "") {
        jalert("연락처를 입력해주세요.", "", $("#srt_tel").focus());
        return false;
    }

    $.post("./step_update.php", { act: "chk_mt_hp", srt_tel: $("#srt_tel").val() }, function (data) {
        if (data == "Y") {
            set_timer();
        }
    });

    return false;
}
function set_timer() {
    var time = 119;
    var min = "";
    var sec = "";
    $("#hp_chk_btn").prop("disabled", true);
    $("#hp_chk_btn").css("background-color", "#e9ecef");
    $("#hp_chk_btn").css("border-color", "#e9ecef");
    $("#hp_chk_btn").css("color", "#222222");
    $("#srt_tel").prop("readonly", true);
    $("#srt_tel").css("background-color", "#e9ecef");
    timer = setInterval(function () {
        min = parseInt(time / 60);
        sec = time % 60;
        $("#certi_hp").show();
        document.getElementById("hp_confirm_timer").innerHTML = "인증번호를 발송했습니다. (유효시간 : " + min + ":" + sec + ")";
        time--;
        if (time < -1) {
            jalert("인증번호 유효시간이 만료 되었습니다.", "", "");
            clearInterval(timer);
            $("#certi_hp").hide();
            $("#hp_chk_btn").prop("disabled", false);
            $("#hp_chk_btn").css("background-color", "#F04E5A");
            $("#hp_chk_btn").css("border-color", "#F04E5A");
            $("#hp_chk_btn").css("color", "#ffffff");
            $("#srt_tel").prop("readonly", false);
        }
    }, 1000);
}

function f_hp_confirm() {
    if ($("#hp_confirm").val() == "") {
        jalert("인증번호를 등록해주세요.", "", $("#hp_confirm").focus());
        return false;
    }

    $.post("./step_update.php", { act: "confirm_hp", srt_tel: $("#srt_tel").val(), hp_confirm: $("#hp_confirm").val() }, function (data) {
        if (data == "Y") {
            jalert("인증이 확인되었습니다.", "", "");
            clearInterval(timer);
            $("#srt_tel_chk").val("Y");
            $("#certi_hp").hide();
            $("#hp_confirm").prop("readonly", true);
            $("#srt_tel").prop("readonly", true);
        } else {
            jalert("인증이 확인되지 않습니다. 인증문자를 확인바랍니다.", "", "");
        }
    });

    return false;
}

function page_replace(url) {
    location.replace(url);
}

function page_move(url) {
    location.href = url;
}

function validateMtId(mt_id) {
    //유저의 아이디체크
    var regex = /^[a-zA-Z0-9_]{4,12}$/;
    return regex.test(mt_id);
}

function validateMtPw(value) {
    var regex = /^[a-zA-Z0-9!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]{4,12}$/;
    return regex.test(value);
}

function page_auth_move(mt_rank1) {
    if (mt_rank1.trim() === "") {
        jalert("해당 페이지에 접근하려면 해당페이지에 대한 권한이 필요합니다.", "", "");
    } else {
        $.ajax({
            url: "/ajax.menu_chg.php",
            type: "POST",
            data: {
                mt_rank1: mt_rank1,
            },
            dataType: "json",
            async: true,
            success: function (data) {
                // console.log(data);
                if (data.result) {
                    location.href = data.page_url;
                } else {
                    jalert("개발된 페이지가 없습니다.", "", "");
                }
            },
            error: function (request, status, error) {
                console.log(status);
                console.log(error);
            },
        });
        // page_move("./menu_chg.php?mt_rank="+mt_rank);
    }
}

function page_reload() {
    location.reload();
}

function openFilePicker() {
    var fileInput = $("#file-input");
    fileInput.click();
}

function handleFileSelect(event) {
    var maxFileSize = 90 * 1024 * 1024; // 프론트단 100MB로 설정
    var files = event.target.files; // 선택한 파일들의 목록
    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        if (file.size <= maxFileSize) {
            console.log("파일 이름:", file.name);
            console.log("파일 크기:", file.size);
            console.log("파일 유형:", file.type);
        } else {
            jalert("파일 크기가 제한을 초과했습니다. 최대 파일 크기는 100MB입니다.");
            return false;
        }
        var formData = new FormData();
        for (var i = 0; i < files.length; i++) {
            formData.append("files[]", files[i]);
        }
        $("#loadding").modal("toggle");
        $.ajax({
            url: "/dev/iacuc/file_upload.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                setTimeout(() => {
                    $("#loadding").modal("hide");
                }, 500);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("파일 업로드 실패:", errorThrown);
            },
        });
    }
}

function f_get_box_list_reset(obj_frm = "") {
    if (obj_frm) {
        var obj_frm_t = obj_frm + " ";
    } else {
        var obj_frm_t = "frm_list ";
    }

    $("#" + obj_frm_t)[0].reset();

    f_get_box_list("1");
}

function f_get_box_list(pg = "") {
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
function f_get_box_list2(pg = "") {
    var form_t = $("#obj_frm2").val();
    var obj_frm_t = "#" + form_t + " ";

    if (pg == null || pg == "") {
        var ls_obj_pg = localStorage.getItem("obj_pg2");
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

    $(obj_frm_t + "#obj_pg2").val(parseInt(pg));

    var form_t = $("#" + form_t)[0];
    var formData_t = new FormData(form_t);

    $.ajax({
        url: $("#obj_uri2").val(),
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

            $("#" + $(obj_frm_t + "#obj_list2").val()).html(data);
        }
        },
        error: function (err) {
        console.log(err);
        },
    });

    return false;
}
function f_show_chg(u, i, v) {
    $.confirm({
        title: "변경",
        content: "정보를 변경하시겠습니까?",
        buttons: {
            confirm: {
                text: "확인",
                action: function () {
                    $.post(
                        u,
                        {
                            obj_act: "show_chg",
                            idx: i,
                            show_v: v,
                        },
                        function (data) {
                            if (data == "Y") {
                                jalert("변경되었습니다.");
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

function f_post_del(u, i, o = "") {
    if (o) {
        var o_t = o;
    } else {
        var o_t = "delete";
    }

    $.confirm({
        title: "경고",
        content: "정말 삭제하시겠습니까? 삭제된 자료는 복구되지 않습니다.",
        buttons: {
            confirm: {
                text: "확인",
                action: function () {
                    $.post(
                        u,
                        {
                            obj_act: o_t,
                            idx: i,
                        },
                        function (data) {
                            if (data == "Y") {
                                jalert_url("삭제되었습니다.", "reload");
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

    return false;
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
    form_data.append("obj_act", "upload");
    form_data.append("ctype", ctype);
    form_data.append("file_no", no);
    form_data.append("file", file);
    $.ajax({
        data: form_data,
        type: "POST",
        enctype: "multipart/form-data",
        url: "/dev/sendfile_summernote",
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

function bytesToSize(x) {
    const units = ["bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];

    let l = 0,
        n = parseInt(x, 10) || 0;

    while (n >= 1024 && ++l) {
        n = n / 1024;
    }

    return n.toFixed(n < 10 && l > 0 ? 1 : 0) + " " + units[l];
}

function f_file_btn_upload(obj_name, o = "") {
    var file_up_max_t = parseInt($("#file_up_num").val());
    var obj_id = $("#file_cnt").val();
    if (obj_id < 1) {
        obj_id = 1;
    }
    if (o == "") {
        var obj_t = obj_name + obj_id;
    } else {
        var obj_t = obj_name + o;
    }

    if (obj_id > file_up_max_t) {
        jalert("업로드는 " + file_up_max_t + "개 이하만 가능합니다.");
        return;
    } else {
        var ww = 1;
        $(".lt_file_vi").each(function () {
            if ($(this).val() == "") {
                obj_id = ww;
                return false;
            }
            ww++;
        });

        $("#" + obj_t).click();
    }

    return false;
}

function f_file_box_reset(o = "") {
    if (o == "") {
        $("#file_cnt").val(0);
        $("#file_up_box").html('<li><div><div class="under"><i class="xi xi-diskette fc_aaa mr-2"></i>파일을 업로드해주세요</div></div></li>');
        $("#file_cnt_t").html("");
        $("#btn_file_delete").hide();
    } else {
        $("#file_cnt").val(0);
        $("#" + o).html('<li><div><div class="under"><i class="xi xi-diskette fc_aaa mr-2"></i>파일을 업로드해주세요</div></div></li>');
        $("#file_cnt_t").html("");
        $("#btn_file_delete").hide();
    }

    return false;
}

function f_preview_file_selected(e, obj_name, o = "") {
    var files = e.target.files;
    var filesArr = Array.prototype.slice.call(files);
    var obj_id = $("#file_cnt").val();
    if (obj_id < 1) {
        obj_id = 1;
    }
    var file_up_max_t = parseInt($("#file_up_num").val()) + 1;
    if (o == "") {
        var box_t = "file_up_box";
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
            if (f.size > $("#file_up_size").val()) {
                jalert("업로드는 " + bytesToSize($("#file_up_size").val()) + " 이하만 가능합니다.");

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
                        '_li"><div> <a class="under"><i class="xi xi-diskette fc_aaa mr-2"></i><u>' +
                        f.name +
                        '</u></a><p class="mt-2 fc_666"><span class="mr-3"><span class="mr-2">파일크기</span> <span>' +
                        bytesToSize(e.loaded) +
                        '</span></span></p></div><div><button type="button" class="btn btn_icon"><img src="/img/del_btn.png" style="width:3.2rem;" onclick="f_preview_file_delete(\'' +
                        obj_id +
                        "', '" +
                        obj_name +
                        "', '" +
                        o +
                        "')\"></button> </div></li>"
                );
                $("#btn_file_delete").show();
                $("#" + obj_t + "_ori").val(f.name);
            };
            reader.readAsDataURL(f);

            $("#file_cnt").val(parseInt(obj_id) + 1);
            $("#file_cnt_t").html(" (" + parseInt(obj_id) + ")");
        });
    }
}

function f_preview_file_selected2(e, obj_name, o = "") {
    var files = e.target.files;
    var filesArr = Array.prototype.slice.call(files);
    var obj_id = $("#file_cnt").val();
    if (obj_id < 1) {
        obj_id = 1;
    }
    var file_up_max_t = parseInt($("#file_up_num").val()) + 1;
    if (o == "") {
        var box_t = "file_up_box";
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
            if (f.size > $("#file_up_size").val()) {
                jalert("업로드는 " + bytesToSize($("#file_up_size").val()) + " 이하만 가능합니다.");

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
                        '_li"><div class="d-flex align-items-center flex-wrap"><input type="text" class="form-control ml-0" name="it_file' +
                        obj_id +
                        '_ori" id="it_file' +
                        obj_id +
                        '_ori" placeholder="제출 서류명 직접 입력" value="' +
                        f.name +
                        '" style="height:4.2rem; max-width:21rem;"><p class="mt-2 fc_666 pl-0"><i class="xi xi-diskette fc_aaa mr-2"></i> <span class="mr-3"><span class="mr-2">파일크기</span> <span>' +
                        bytesToSize(e.loaded) +
                        '</span></span></p></div><button type="button" class="btn btn_icon"><img src="/img/del_btn.png" style="width:3.2rem;" onclick="f_preview_file_delete(\'' +
                        obj_id +
                        "', '" +
                        obj_name +
                        "', '" +
                        o +
                        "')\"></button> </div></li>"
                );
                $("#btn_file_delete").show();
                $("#" + obj_t + "_ori").val(f.name);
            };
            reader.readAsDataURL(f);

            $("#file_cnt").val(parseInt(obj_id) + 1);
            $("#file_cnt_t").html(" (" + parseInt(obj_id) + ")");
        });
    }
}

function f_preview_file_delete(obj_id, obj_name, o = "") {
    if (o == "") {
        var obj_t = obj_name + obj_id;
    } else {
        var obj_t = obj_name;
    }
    var file_cnt_t = parseInt($("#file_cnt").val());

    if (obj_t) {
        $("#" + obj_t).val("");
        $("#" + obj_t + "_on").val("");
        $("#" + obj_t + "_ori").val("");
        $("#" + obj_t + "_li").remove();

        if (o == "") {
            if (file_cnt_t == 2) {
                f_file_box_reset();
            } else {
                $("#file_cnt").val(file_cnt_t - 1);
                $("#file_cnt_t").html(" (" + (file_cnt_t - 2) + ")");
            }
        } else {
            f_file_box_reset(o);
        }
    }
}

function f_preview_file_delete_all(obj_name) {
    if (obj_name) {
        $("." + obj_name + "_v").val("");
        $("." + obj_name + "_vo").val("");
        $("." + obj_name + "_vi").val("");
        $("." + obj_name + "_vs").val("");
        f_file_box_reset();
    }
}

function gourl(url) {
    if (url != "") {
        window.smapAndroid.openUrlBlank(url);
    }
}

function f_preview_one_image_delete(obj_id, obj_name) {
    var obj_t = obj_name + obj_id;

    if (obj_t) {
        $("#" + obj_t).val("");
        $("#" + obj_t + "_on").val("");
        $("#" + obj_t + "_del").hide();
        $("#" + obj_t + "_box").css("border", "1px dashed #ddd");
        $("#" + obj_t + "_box").html('<i class="xi xi-camera-o"></i>');
    }
}

function f_preview_one_image_selected(e, obj_id, obj_name, fs = "10485760") {
    var files = e.target.files;
    var filesArr = Array.prototype.slice.call(files);
    var obj_t = obj_name + obj_id;
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

function get_2_year_month(v1, v2) {
    var date1 = new Date(v1);
    var date2 = new Date(v2);

    var elapsedMSec = date2.getTime() - date1.getTime();
    var elapsedDay = elapsedMSec / 1000 / 60 / 60 / 24;
    var elapsedMonth = elapsedMSec / 1000 / 60 / 60 / 24 / 30;

    second = Math.floor(elapsedMSec / 1000);
    minute = Math.floor(second / 60);
    second = second % 60;
    hour = Math.floor(minute / 60);
    minute = minute % 60;
    day = Math.floor(hour / 24);
    hour = hour % 24;
    month = Math.floor(day / 30);
    day = day % 30;
    year = Math.floor(month / 12);
    month = month % 12;

    // console.log(year + "|" + month + "|" + day);

    if (year) {
        var rtn = year + "년 " + month + "개월 " + day + "일";
    } else {
        if (month > 1) {
            var rtn = month + "개월 " + day + "일";
        } else {
            var rtn = day + "일";
        }
    }

    return rtn;
}

function get_date_t(d) {
    var date = new Date(d);

    var y = date.getFullYear();
    var m = date.getMonth() + 1;
    var d = date.getDate();
    var w = "일월화수목금토".charAt(date.getUTCDay());

    // console.log(y+"."+m+"."+d+" ("+w+")");
    return y + "." + m + "." + d + " (" + w + ")";
}

function checkAllToggle(all_selector, check_selector) {
    let el_all_check = document.querySelector(all_selector);
    let el_check_all = document.querySelectorAll(check_selector);
    let is_check = el_all_check.checked;

    if (is_check === true) {
        el_check_all.forEach((checkbox) => {
            if (checkbox.disabled !== true) {
                checkbox.setAttribute("checked", "checked");
                checkbox.checked = true;
            }
        });
    } else {
        el_check_all.forEach((checkbox) => {
            checkbox.removeAttribute("checked", "checked");
            checkbox.checked = false;
        });
    }
}

function checkBoxToggle(all_selector, check_selector) {
    let el_all_check = document.querySelector(all_selector);
    let checkbox_ln = document.querySelectorAll(check_selector + ":enabled").length;
    let check_ln = document.querySelectorAll(check_selector + ":checked:enabled").length;
    if (checkbox_ln === check_ln) {
        el_all_check.setAttribute("checked", "checked");
        el_all_check.checked = true;
    } else {
        el_all_check.removeAttribute("checked", "checked");
        el_all_check.checked = false;
    }
}

function checkBoxToggleEvent(all_selector, check_selector) {
    let el_all_check = document.querySelector(all_selector);
    el_all_check.addEventListener("change", function () {
        checkAllToggle(all_selector, check_selector);
    });

    let el_check_all = document.querySelectorAll(check_selector);
    el_check_all.forEach((el_check, idx) => {
        el_check.addEventListener("change", function () {
            checkBoxToggle(all_selector, check_selector);
        });
    });
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

function f_checkbox_all(obj, name) {
    if ($(obj).prop("checked") == true) {
        $('input:checkbox[name="' + name + '[]"]').each(function () {
            $(this).prop("checked", true);
        });
    } else {
        $('input:checkbox[name="' + name + '[]"]').each(function () {
            $(this).prop("checked", false);
        });
    }

    return false;
}

function f_checkbox_each(name) {
    var count = 0;
    $('input:checkbox[name="' + name + '[]"]').each(function () {
        if ($(this).prop("checked") == false) {
            count++;
        }
    });

    if (count == 0) {
        $("#" + name + "_all").prop("checked", true);
    } else {
        $("#" + name + "_all").prop("checked", false);
    }

    return false;
}

function f_member_file_upload_done() {
  $("#camera_album").modal("hide");

  var form_data = new FormData();
  form_data.append("act", "member_profile_get");
  $.ajax({
    data: form_data,
    type: "POST",
    enctype: "multipart/form-data",
    url: "./form_update",
    cache: false,
    timeout: 5000,
    contentType: false,
    processData: false,
    success: function (data) {
      if (data) {
        $("#member_profile_img").attr("src", data);
      }
    },
    error: function (err) {
      console.log(err);
    },
  });
}

function dateFormat(date) {
    let dateFormat2 = date.getFullYear() + "-" + (date.getMonth() + 1 < 9 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1) + "-" + (date.getDate() < 9 ? "0" + date.getDate() : date.getDate());
    return dateFormat2;
}

function f_calendar_init(t = "") {
  var form_data = new FormData();
  var week_chk = $("#week_calendar").val();

  if (t == "today") {
    var cday = new Date();
  } else {
    var sdate = $("#csdate").val();
    var cday = new Date(sdate);
  }

  form_data.append("act", "calendar_list");
  form_data.append("week_chk", week_chk);

  if (week_chk == "N") {
    if (t == "prev") {
      cday.setMonth(cday.getMonth() - 1);
      form_data.append("sdate", dateFormat(cday));
    } else if (t == "next") {
      cday.setMonth(cday.getMonth() + 1);
      form_data.append("sdate", dateFormat(cday));
    } else if (t == "today") {
      form_data.append("sdate", dateFormat(cday));
    } else {
      form_data.append("sdate", dateFormat(cday));
    }

    var cday2 =
      cday.getFullYear() +
      "-" +
      (cday.getMonth() + 1 < 9
        ? "0" + (cday.getMonth() + 1)
        : cday.getMonth() + 1) +
      "-02";
    $("#csdate").val(cday2);
  } else {
    if (t == "prev") {
      cday.setDate(cday.getDate() - 7);
      form_data.append("sdate", dateFormat(cday));
    } else if (t == "next") {
      cday.setDate(cday.getDate() + 7);
      form_data.append("sdate", dateFormat(cday));
    } else if (t == "today") {
      form_data.append("sdate", dateFormat(cday));
    } else {
      form_data.append("sdate", dateFormat(cday));
    }
    $("#csdate").val(dateFormat(cday));
  }

  setTimeout(() => {
    $("#calendar_date_title").html(
      cday.getFullYear() + "년 " + (cday.getMonth() + 1) + "월"
    );
  }, 100);

  $.ajax({
    url: "./schedule_update",
    enctype: "multipart/form-data",
    data: form_data,
    type: "POST",
    async: true,
    contentType: false,
    processData: false,
    cache: true,
    timeout: 5000,
    success: function (data) {
      if (data) {
        $("#schedule_calandar_box").html(data);
      }
    },
    error: function (err) {
      console.log(err);
    },
  });
}

function f_calendar_log_init(t = "") {
    var form_data = new FormData();
    var week_chk = $("#week_calendar").val();
    var sgdt_mt_idx = $("#sgdt_mt_idx").val();

    if (t == "today") {
        var cday = new Date();
    } else {
        var sdate = $("#csdate").val();
        var lsdate = $("#lsdate").val();
        var ledate = $("#ledate").val();
        var cday = new Date(sdate);
    }

    form_data.append("act", "calendar_list");
    form_data.append("week_chk", week_chk);
    form_data.append("lsdate", lsdate);
    form_data.append("ledate", ledate);
    form_data.append("sgdt_mt_idx", sgdt_mt_idx);

    if (week_chk == "N") {
        if (t == "prev") {
            cday.setMonth(cday.getMonth() - 1);
            form_data.append("sdate", dateFormat(cday));
        } else if (t == "next") {
            cday.setMonth(cday.getMonth() + 1);
            form_data.append("sdate", dateFormat(cday));
        } else if (t == "today") {
            form_data.append("sdate", dateFormat(cday));
        } else {
            form_data.append("sdate", dateFormat(cday));
        }
        var cday2 = cday.getFullYear() + "-" + (cday.getMonth() + 1 < 9 ? "0" + (cday.getMonth() + 1) : cday.getMonth() + 1) + "-02";
        $("#csdate").val(cday2);
    } else {
        if (t == "prev") {
            cday.setDate(cday.getDate() - 7);
            form_data.append("sdate", dateFormat(cday));
        } else if (t == "next") {
            cday.setDate(cday.getDate() + 7);
            form_data.append("sdate", dateFormat(cday));
        } else if (t == "today") {
            form_data.append("sdate", dateFormat(cday));
        } else {
            form_data.append("sdate", dateFormat(cday));
        }
        $("#csdate").val(dateFormat(cday));
    }

    setTimeout(() => {
        $("#calendar_date_title").html(cday.getFullYear() + "년 " + (cday.getMonth() + 1) + "월");  
    }, 100);

    $.ajax({
        url: "./location_update",
        enctype: "multipart/form-data",
        data: form_data,
        type: "POST",
        async: true,
        contentType: false,
        processData: false,
        cache: true,
        timeout: 5000,
        success: function (data) {
            if (data) {
                $("#schedule_calandar_box").html(data);
            }
        },
        error: function (err) {
            console.log(err);
        },
    });
}

function f_member_receipt_done(product_id, purchaseToken, package_name, JsonString, mt_idx) {  // 구독 결제 완료
  var form_data = new FormData();
  form_data.append("act", "member_receipt_done");
  form_data.append("mt_idx", mt_idx);
  form_data.append("product_id", product_id);
  form_data.append("purchaseToken", purchaseToken);
  form_data.append("package_name", package_name);
  form_data.append("JsonString", JsonString);
  $("#splinner_modal").modal("toggle");
  $.ajax({
    data: form_data,
    type: "POST",
    enctype: "multipart/form-data",
    url: "./api/subscribe_upload",
    cache: false,
    timeout: 5000,
    contentType: false,
    processData: false,
    success: function (data) {
      if (data) {
        if(data == 'Y'){
          $("#splinner_modal").modal("toggle");
          jalert_url("결제가 완료되었습니다.", "./purchase_list");
        }else{
          $("#splinner_modal").modal("toggle");
          jalert_url("결제 실패되었습니다.", "reload");
        }
      }
    },
    error: function (err) {
      console.log(err);
    },
  });
}

function f_member_receipt_check(product_id, purchaseToken, package_name, JsonString, mt_idx) {// 구독 결제 확인
  var form_data = new FormData();
  form_data.append("act", "member_receipt_check");
  form_data.append("mt_idx", mt_idx);
  form_data.append("product_id", product_id);
  form_data.append("purchaseToken", purchaseToken);
  form_data.append("package_name", package_name);
  form_data.append("JsonString", JsonString);
  if(purchaseToken){
    $.ajax({
      data: form_data,
      type: "POST",
      enctype: "multipart/form-data",
      url: "./api/subscribe_upload",
      cache: false,
      timeout: 5000,
      contentType: false,
      processData: false,
      success: function (data) {
        if (data) {
          // $("#member_profile_img").attr("src", data);
        }
      },
      error: function (err) {
        console.log(err);
      },
    });
  }else{
    // jalert_url("결제정보가 없습니다.", "reload");
  }
}

function f_member_receipt_done_ios(order_id, product_id, token, mt_idx) { // ios 결제완료 확인
  var form_data = new FormData();
  form_data.append("act", "member_receipt_done_ios");
  form_data.append("mt_idx", mt_idx);
  form_data.append("order_id", order_id);
  form_data.append("product_id", product_id);
  form_data.append("token", token);

  $('#splinner_modal').modal('toggle');
  $.ajax({
    data: form_data,
    type: "POST",
    enctype: "multipart/form-data",
    url: "./api/subscribe_upload_ios",
    cache: false,
    timeout: 5000,
    contentType: false,
    processData: false,
    success: function (data) {
      if (data) {
        if (data == "Y") {
          $("#splinner_modal").modal("toggle");
          jalert_url("결제가 완료되었습니다.", "./purchase_list");
        } else {
          $("#splinner_modal").modal("toggle");
          jalert_url("결제 실패되었습니다.", "reload");
        }
      }
    },
    error: function (err) {
      console.log(err);
    },
  });
}

function f_member_receipt_check_ios(token, mt_idx) { // ios 결제 확인
  var form_data = new FormData();
  form_data.append("act", "member_receipt_check_ios");
  form_data.append("mt_idx", mt_idx);
  form_data.append("token", token);
  if(token){
    $.ajax({
      data: form_data,
      type: "POST",
      enctype: "multipart/form-data",
      url: "./api/subscribe_upload_ios",
      cache: false,
      timeout: 5000,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (data) {
        if (data) {
         /*  if (data.status == "end") {
            alert("end");
            var message = {
              type: "purchaseCheck",
            };
           if (isAndroid()) {
             window.smapAndroid.restorePurchase();
           } else if (isiOS()) {
             window.webkit.messageHandlers.smapIos.postMessage(message);
           }
          }else if (data.status == "pass") {
            alert("pass");
          }else{
            alert('fail');
          } */
        }
      },
      error: function (err) {
        console.log(err);
      },
    });
  }else{
    jalert_url("결제정보가 없습니다.", "reload");
  }
}

function f_member_receipt_restore_ios(token, mt_idx) { // ios 복원
  var form_data = new FormData();
  form_data.append("act", "member_receipt_restore_ios");
  form_data.append("mt_idx", mt_idx);
  form_data.append("token", token);
  $.ajax({
    data: form_data,
    type: "POST",
    enctype: "multipart/form-data",
    url: "./api/subscribe_upload_ios",
    cache: false,
    timeout: 5000,
    contentType: false,
    processData: false,
    success: function (data) {
      if (data) {
        if (data == "Y") {
          jalert_url("결제가 완료되었습니다.", "./purchase_list");
        } else {
          jalert_url("결제 실패되었습니다.", "reload");
        }
      }
    },
    error: function (err) {
      console.log(err);
    },
  });
}

function invite_code_insert(sit_code) {
    location.replace("./invitation_code?sit_code=" +sit_code);
}
function get_pad(v) {
    return v > 9 ? v : "0" + String(v);
}
function maxLengthCheck(object) {
    if (object.value.length > object.maxLength) {
        object.value = object.value.slice(0, object.maxLength);
    }
}
function location_refresh(pagetype, lat, long) {
    if (adEnded) {
        adEnded = false;
      return;
    }
      var form_data = new FormData();
    //   alert('pagetype: '+pagetype+', lat: '+ lat + ', long: '+long);
      form_data.append("act", "location_refresh");
      form_data.append("lat", lat);
      form_data.append("long", long);
      if (pagetype == "index" || pagetype == "location" || pagetype == "log"){
        $.ajax({
          url: "./session_refresh",
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
              location.reload(true);
            } else {
              location.reload(true);
            }
          },
          error: function (err) {
            console.log(err);
          },
        });
      }
}

// function showAd() {
//     var message = {
//         type: "showAd",
//     };
//     if (isAndroid()) {
//         window.smapAndroid.showAd();
//     } else if (isiOS()) {
//         window.webkit.messageHandlers.smapIos.postMessage(message);
//     }
// }

// function endAd() {
//     failcount = 0;
//   // 이미 endAd가 호출된 경우에는 location_refresh를 실행하지 않음
//   if (!adEnded) {
//     adEnded = true; // endAd 호출됨을 표시
//   }
// }

// function failAd(status) {
//     if (failcount == 0){ // 첫 실패 시 다시 광고요청 보내기
//         failcount++;
//         showAd();
//     }else{ // 실패 후 다시 광고요청 후 다시 실패 시 안내
//       if (status == "load") {
//         // jalert_url("광고 로드를 실패하였습니다.", "reload");
//         console.log("광고 로드를 실패하였습니다.");
//       } else if (status == " show") {
//         // jalert_url("광고 실행에 실패하였습니다.", "reload");
//         console.log("광고 실행에 실패하였습니다.");
//       }
//     }
// }

function isAndroid() {
      return navigator.userAgent.match(/Android/i);
}

function isiOS() {
        return navigator.userAgent.match(/iPhone|iPad|iPod|Mac|Apple/i);
}

[
    {
        relation: ["delegate_permission/common.handle_all_urls"],
        target: {
            namespace: "android_app",
            package_name: "com.dmonster.smap",
            sha256_cert_fingerprints: [
                "79:89:52:D7:5A:16:E6:8E:D8:E4:26:B2:1C:FA:1D:E4:89:52:7E:BE:69:9D:E8:1A:F3:05:8D:1C:1D:BC:28:88",
            ],
        },
    },
];