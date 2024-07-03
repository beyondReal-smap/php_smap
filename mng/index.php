<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head.inc.php";
$chk_menu = '';
$chk_sub_menu = '';
include $_SERVER['DOCUMENT_ROOT'] . "/mng/head_menu.inc.php";
?>
<div class="content-wrapper">
    <div class="row">
        <div class="col-xl-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    대시보드
                </div>
                <div class="card-body">
                    <form class="form-inline">
                        <div class="form-group mr-3 mb-2">
                            <div class="btn-group">
                                <button type="button" onclick="f_order_search_date_range('1', '<?= date('Y-m-d', strtotime("-2 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range1" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">3일</button>
                                <button type="button" onclick="f_order_search_date_range('2', '<?= date('Y-m-d', strtotime("-4 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range2" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">5일</button>
                                <button type="button" onclick="f_order_search_date_range('3', '<?= date('Y-m-d', strtotime("-6 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range3" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">7일</button>
                                <button type="button" onclick="f_order_search_date_range('4', '<?= date('Y-m-d', strtotime("-14 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range4" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">15일</button>
                                <button type="button" onclick="f_order_search_date_range('5', '<?= date('Y-m-d', strtotime("-29 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range5" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">30일</button>
                                <button type="button" onclick="f_order_search_date_range('6', '<?= date('Y-m-d', strtotime("-59 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range6" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">60일</button>
                                <button type="button" onclick="f_order_search_date_range('7', '<?= date('Y-m-d', strtotime("-89 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range7" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">90일</button>
                                <button type="button" onclick="f_order_search_date_range('8', '<?= date('Y-m-d', strtotime("-119 days")) ?>', '<?= date('Y-m-d') ?>');" id="f_order_search_date_range8" class="btn btn-outline-secondary btn-sm c_pt_selling_date_range">120일</button>
                            </div>
                        </div>
                        <div class="form-group mr-3 mb-2">
                            <div class="input-group">
                                <input type="text" name="sel_search_sdate" id="sel_search_sdate" value="<?= $_GET['sel_search_sdate'] ?>" class="form-control form-control-sm" readonly /> <span class="m-2">~</span> <input type="text" name="sel_search_edate" id="sel_search_edate" value="<?= $_GET['sel_search_edate'] ?>" class="form-control form-control-sm" readonly />
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <input type="button" class="btn btn-info" value="검색" onclick="updateChart()" />
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" onclick="location.href='./member_list'">
                    회원
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <p>가입수</p>
                                    <h2 class="mb-0" id="card1_data3"></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <p>탈퇴수</p>
                                    <h2 class="mb-0" id="card1_data4"></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="cash-deposits-chart-legend" class="d-flex justify-content-center pt-3">
                        <ul class="dashboard-chart-legend">
                            <li><span style="background-color: #ff4747"></span>가입</li>
                            <li><span style="background-color: #4d83ff"></span>탈퇴</li>
                        </ul>
                    </div>
                    <canvas id="cash-deposits-chart" width="755" height="377" style="display: block; width: 755px; height: 377px;" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" onclick="location.href='./group_list'">
                    그룹
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <p>등록수</p>
                                    <h2 class="mb-0" id="card2_data3"></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <p>일정수</p>
                                    <h2 class="mb-0" id="card2_data4"></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="cash-deposits-chart-legend2" class="d-flex justify-content-center pt-3">
                        <ul class="dashboard-chart-legend">
                            <li><span style="background-color: #ff4747 "></span>등록</li>
                            <li><span style="background-color: #4d83ff "></span>일정</li>
                        </ul>
                    </div>
                    <canvas id="cash-deposits-chart2" width="755" height="377" style="display: block; width: 755px; height: 377px;" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" onclick="location.href='./qna_list'">
                    문의
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <p>문의</p>
                                    <h2 class="mb-0" id="card3_data3"></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="cash-deposits-chart-legend3" class="d-flex justify-content-center pt-3">
                        <ul class="dashboard-chart-legend">
                            <li><span style="background-color: #ff4747 "></span>문의</li>
                        </ul>
                    </div>
                    <canvas id="cash-deposits-chart3" width="755" height="377" style="display: block; width: 755px; height: 377px;" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-header" onclick="location.href='./plan_use_list'">
                    유료플랜 가입자수
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <p>가입자</p>
                                    <h2 class="mb-0" id="card4_data3"></h2>
                                </div>
                                <div>
                                    <p>만료</p>
                                    <h2 class="mb-0" id="card4_data4"></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="cash-deposits-chart-legend4" class="d-flex justify-content-center pt-3">
                        <ul class="dashboard-chart-legend">
                            <li><span style="background-color: #ff4747 "></span>가입/유지</li>
                            <li><span style="background-color: #4d83ff "></span>만료</li>
                        </ul>
                    </div>
                    <canvas id="cash-deposits-chart4" width="755" height="377" style="display: block; width: 755px; height: 377px;" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6 grid-margin stretch-card">
        </div>
    </div>
</div>

<script>
    function updateChart() {
        var startDate = $("#sel_search_sdate").val();
        var endDate = $("#sel_search_edate").val();

        var form_data1 = new FormData();
        form_data1.append("act", "chart1");
        form_data1.append("sdate", $('#sel_search_sdate').val());
        form_data1.append("edate", $('#sel_search_edate').val());
        $.ajax({
            data: form_data1,
            type: "POST",
            enctype: "multipart/form-data",
            url: "./index_update",
            cache: false,
            timeout: 5000,
            contentType: false,
            processData: false,
            success: function(data) {
                var json_data = JSON.parse(data);

                if (json_data.data3 >= 0) {
                    $('#card1_data3').html(json_data.data3);
                }
                if (json_data.data4 >= 0) {
                    $('#card1_data4').html(json_data.data4);
                }
                var cashDepositsCanvas = $("#cash-deposits-chart").get(0).getContext("2d");
                var data = {
                    labels: json_data.labels,
                    datasets: [{
                            label: '가입',
                            data: json_data.data1,
                            borderColor: [
                                '#ff4747'
                            ],
                            borderWidth: 2,
                            fill: false,
                            pointBackgroundColor: "#fff"
                        },
                        {
                            label: '탈퇴',
                            data: json_data.data2,
                            borderColor: [
                                '#4d83ff'
                            ],
                            borderWidth: 2,
                            fill: false,
                            pointBackgroundColor: "#fff"
                        },
                    ]
                };
                var options = {
                    scales: {
                        yAxes: [{
                            display: true,
                            gridLines: {
                                drawBorder: false,
                                lineWidth: 1,
                                color: "#e9e9e9",
                                zeroLineColor: "#e9e9e9",
                            },
                            ticks: {
                                min: 0,
                                max: 100,
                                stepSize: 20,
                                fontColor: "#6c7383",
                                fontSize: 16,
                                fontStyle: 300,
                                padding: 15
                            }
                        }],
                        xAxes: [{
                            type: 'time',
                            time: {
                                unit: "day",
                                displayFormats: {
                                    'millisecond': 'MMM DD',
                                    'second': 'MMM DD',
                                    'minute': 'MMM DD',
                                    'hour': 'MMM DD',
                                    'day': 'MM.DD',
                                    'week': 'MMM DD',
                                    'month': 'MMM DD',
                                    'quarter': 'MMM DD',
                                    'year': 'MMM DD',
                                }
                            },
                            display: true,
                            gridLines: {
                                drawBorder: false,
                                lineWidth: 1,
                                color: "#e9e9e9",
                            },
                            ticks: {
                                fontColor: "#6c7383",
                                fontSize: 16,
                                fontStyle: 300,
                                padding: 15
                            }
                        }]
                    },
                    legend: {
                        display: false
                    },
                    legendCallback: function(chart) {
                        var text = [];
                        text.push('<ul class="dashboard-chart-legend">');
                        for (var i = 0; i < chart.data.datasets.length; i++) {
                            text.push('<li><span style="background-color: ' + chart.data.datasets[i].borderColor[0] + ' "></span>');
                            if (chart.data.datasets[i].label) {
                                text.push(chart.data.datasets[i].label);
                            }
                        }
                        text.push('</ul>');
                        return text.join("");
                    },
                    elements: {
                        point: {
                            radius: 3
                        },
                        line: {
                            tension: 0
                        }
                    },
                    stepsize: 1,
                    layout: {
                        padding: {
                            top: 0,
                            bottom: -10,
                            left: -10,
                            right: 0
                        }
                    }
                };
                cashDeposits = new Chart(cashDepositsCanvas, {
                    type: 'line',
                    data: data,
                    options: options
                });
                console.log(options);
                document.getElementById('cash-deposits-chart-legend').innerHTML = cashDeposits.generateLegend();
            }
        });

        var form_data2 = new FormData();
        form_data2.append("act", "chart2");
        form_data2.append("sdate", $('#sel_search_sdate').val());
        form_data2.append("edate", $('#sel_search_edate').val());
        $.ajax({
            data: form_data2,
            type: "POST",
            enctype: "multipart/form-data",
            url: "./index_update",
            cache: false,
            timeout: 5000,
            contentType: false,
            processData: false,
            success: function(data) {
                var json_data = JSON.parse(data);

                if (json_data.data3 >= 0) {
                    $('#card2_data3').html(json_data.data3);
                }
                if (json_data.data4 >= 0) {
                    $('#card2_data4').html(json_data.data4);
                }

                var cashDepositsCanvas = $("#cash-deposits-chart2").get(0).getContext("2d");
                var data = {
                    labels: json_data.labels,
                    datasets: [{
                            label: '그룹',
                            data: json_data.data1,
                            borderColor: [
                                '#ff4747'
                            ],
                            borderWidth: 2,
                            fill: false,
                            pointBackgroundColor: "#fff"
                        },
                        {
                            label: '일정',
                            data: json_data.data2,
                            borderColor: [
                                '#4d83ff'
                            ],
                            borderWidth: 2,
                            fill: false,
                            pointBackgroundColor: "#fff"
                        },
                    ]
                };
                var options = {
                    scales: {
                        yAxes: [{
                            display: true,
                            gridLines: {
                                drawBorder: false,
                                lineWidth: 1,
                                color: "#e9e9e9",
                                zeroLineColor: "#e9e9e9",
                            },
                            ticks: {
                                min: 0,
                                max: 100,
                                stepSize: 20,
                                fontColor: "#6c7383",
                                fontSize: 16,
                                fontStyle: 300,
                                padding: 15
                            }
                        }],
                        xAxes: [{
                            type: 'time',
                            time: {
                                unit: "day",
                                displayFormats: {
                                    'millisecond': 'MMM DD',
                                    'second': 'MMM DD',
                                    'minute': 'MMM DD',
                                    'hour': 'MMM DD',
                                    'day': 'MM.DD',
                                    'week': 'MMM DD',
                                    'month': 'MMM DD',
                                    'quarter': 'MMM DD',
                                    'year': 'MMM DD',
                                }
                            },
                            display: true,
                            gridLines: {
                                drawBorder: false,
                                lineWidth: 1,
                                color: "#e9e9e9",
                            },
                            ticks: {
                                fontColor: "#6c7383",
                                fontSize: 16,
                                fontStyle: 300,
                                padding: 15
                            }
                        }]
                    },
                    legend: {
                        display: false
                    },
                    legendCallback: function(chart) {
                        var text = [];
                        text.push('<ul class="dashboard-chart-legend">');
                        for (var i = 0; i < chart.data.datasets.length; i++) {
                            text.push('<li><span style="background-color: ' + chart.data.datasets[i].borderColor[0] + ' "></span>');
                            if (chart.data.datasets[i].label) {
                                text.push(chart.data.datasets[i].label);
                            }
                        }
                        text.push('</ul>');
                        return text.join("");
                    },
                    elements: {
                        point: {
                            radius: 3
                        },
                        line: {
                            tension: 0
                        }
                    },
                    stepsize: 1,
                    layout: {
                        padding: {
                            top: 0,
                            bottom: -10,
                            left: -10,
                            right: 0
                        }
                    }
                };
                var cashDeposits = new Chart(cashDepositsCanvas, {
                    type: 'line',
                    data: data,
                    options: options
                });
                document.getElementById('cash-deposits-chart-legend2').innerHTML = cashDeposits.generateLegend();
            }
        });

        var form_data3 = new FormData();
        form_data3.append("act", "chart3");
        form_data3.append("sdate", $('#sel_search_sdate').val());
        form_data3.append("edate", $('#sel_search_edate').val());
        $.ajax({
            data: form_data3,
            type: "POST",
            enctype: "multipart/form-data",
            url: "./index_update",
            cache: false,
            timeout: 5000,
            contentType: false,
            processData: false,
            success: function(data) {
                var json_data = JSON.parse(data);

                if (json_data.data3 >= 0) {
                    $('#card3_data3').html(json_data.data3);
                }

                var cashDepositsCanvas = $("#cash-deposits-chart3").get(0).getContext("2d");
                var data = {
                    labels: json_data.labels,
                    datasets: [{
                        label: '신청',
                        data: json_data.data1,
                        borderColor: [
                            '#ff4747'
                        ],
                        borderWidth: 2,
                        fill: false,
                        pointBackgroundColor: "#fff"
                    }, ]
                };
                var options = {
                    scales: {
                        yAxes: [{
                            display: true,
                            gridLines: {
                                drawBorder: false,
                                lineWidth: 1,
                                color: "#e9e9e9",
                                zeroLineColor: "#e9e9e9",
                            },
                            ticks: {
                                min: 0,
                                max: 100,
                                stepSize: 20,
                                fontColor: "#6c7383",
                                fontSize: 16,
                                fontStyle: 300,
                                padding: 15
                            }
                        }],
                        xAxes: [{
                            type: 'time',
                            time: {
                                unit: "day",
                                displayFormats: {
                                    'millisecond': 'MMM DD',
                                    'second': 'MMM DD',
                                    'minute': 'MMM DD',
                                    'hour': 'MMM DD',
                                    'day': 'MM.DD',
                                    'week': 'MMM DD',
                                    'month': 'MMM DD',
                                    'quarter': 'MMM DD',
                                    'year': 'MMM DD',
                                }
                            },
                            display: true,
                            gridLines: {
                                drawBorder: false,
                                lineWidth: 1,
                                color: "#e9e9e9",
                            },
                            ticks: {
                                fontColor: "#6c7383",
                                fontSize: 16,
                                fontStyle: 300,
                                padding: 15
                            }
                        }]
                    },
                    legend: {
                        display: false
                    },
                    legendCallback: function(chart) {
                        var text = [];
                        text.push('<ul class="dashboard-chart-legend">');
                        for (var i = 0; i < chart.data.datasets.length; i++) {
                            text.push('<li><span style="background-color: ' + chart.data.datasets[i].borderColor[0] + ' "></span>');
                            if (chart.data.datasets[i].label) {
                                text.push(chart.data.datasets[i].label);
                            }
                        }
                        text.push('</ul>');
                        return text.join("");
                    },
                    elements: {
                        point: {
                            radius: 3
                        },
                        line: {
                            tension: 0
                        }
                    },
                    stepsize: 1,
                    layout: {
                        padding: {
                            top: 0,
                            bottom: -10,
                            left: -10,
                            right: 0
                        }
                    }
                };
                var cashDeposits = new Chart(cashDepositsCanvas, {
                    type: 'line',
                    data: data,
                    options: options
                });
                document.getElementById('cash-deposits-chart-legend3').innerHTML = cashDeposits.generateLegend();
            }
        });

        var form_data4 = new FormData();
        form_data4.append("act", "chart4");
        form_data4.append("sdate", $('#sel_search_sdate').val());
        form_data4.append("edate", $('#sel_search_edate').val());
        $.ajax({
            data: form_data4,
            type: "POST",
            enctype: "multipart/form-data",
            url: "./index_update",
            cache: false,
            timeout: 5000,
            contentType: false,
            processData: false,
            success: function(data) {
                var json_data = JSON.parse(data);

                if (json_data.data3 >= 0) {
                    $('#card4_data3').html(json_data.data3);
                }
                if (json_data.data4 >= 0) {
                    $('#card4_data4').html(json_data.data4);
                }

                var cashDepositsCanvas = $("#cash-deposits-chart4").get(0).getContext("2d");
                var data = {
                    labels: json_data.labels,
                    datasets: [{
                            label: '신청',
                            data: json_data.data1,
                            borderColor: [
                                '#ff4747'
                            ],
                            borderWidth: 2,
                            fill: false,
                            pointBackgroundColor: "#fff"
                        },
                        {
                            label: '취소',
                            data: json_data.data2,
                            borderColor: [
                                '#4d83ff'
                            ],
                            borderWidth: 2,
                            fill: false,
                            pointBackgroundColor: "#fff"
                        },
                    ]
                };
                var options = {
                    scales: {
                        yAxes: [{
                            display: true,
                            gridLines: {
                                drawBorder: false,
                                lineWidth: 1,
                                color: "#e9e9e9",
                                zeroLineColor: "#e9e9e9",
                            },
                            ticks: {
                                min: 0,
                                max: 100,
                                stepSize: 20,
                                fontColor: "#6c7383",
                                fontSize: 16,
                                fontStyle: 300,
                                padding: 15
                            }
                        }],
                        xAxes: [{
                            type: 'time',
                            time: {
                                unit: "day",
                                displayFormats: {
                                    'millisecond': 'MMM DD',
                                    'second': 'MMM DD',
                                    'minute': 'MMM DD',
                                    'hour': 'MMM DD',
                                    'day': 'MM.DD',
                                    'week': 'MMM DD',
                                    'month': 'MMM DD',
                                    'quarter': 'MMM DD',
                                    'year': 'MMM DD',
                                }
                            },
                            display: true,
                            gridLines: {
                                drawBorder: false,
                                lineWidth: 1,
                                color: "#e9e9e9",
                            },
                            ticks: {
                                fontColor: "#6c7383",
                                fontSize: 16,
                                fontStyle: 300,
                                padding: 15
                            }
                        }]
                    },
                    legend: {
                        display: false
                    },
                    legendCallback: function(chart) {
                        var text = [];
                        text.push('<ul class="dashboard-chart-legend">');
                        for (var i = 0; i < chart.data.datasets.length; i++) {
                            text.push('<li><span style="background-color: ' + chart.data.datasets[i].borderColor[0] + ' "></span>');
                            if (chart.data.datasets[i].label) {
                                text.push(chart.data.datasets[i].label);
                            }
                        }
                        text.push('</ul>');
                        return text.join("");
                    },
                    elements: {
                        point: {
                            radius: 3
                        },
                        line: {
                            tension: 0
                        }
                    },
                    stepsize: 1,
                    layout: {
                        padding: {
                            top: 0,
                            bottom: -10,
                            left: -10,
                            right: 0
                        }
                    }
                };
                var cashDeposits = new Chart(cashDepositsCanvas, {
                    type: 'line',
                    data: data,
                    options: options
                });
                document.getElementById('cash-deposits-chart-legend4').innerHTML = cashDeposits.generateLegend();
            }
        });
    }

    (function($) {
        'use strict';
        $(function() {
            f_order_search_date_range('3', '<?= date('Y-m-d', strtotime("-6 days")) ?>', '<?= date('Y-m-d') ?>');

            jQuery(function() {
                jQuery('#sel_search_sdate').datetimepicker({
                    format: 'Y-m-d',
                    onShow: function(ct) {
                        this.setOptions({
                            maxDate: jQuery(
                                    '#sel_search_edate')
                                .val() ? jQuery(
                                    '#sel_search_edate')
                                .val() : false
                        })
                    },
                    timepicker: false
                });
                jQuery('#sel_search_edate').datetimepicker({
                    format: 'Y-m-d',
                    onShow: function(ct) {
                        this.setOptions({
                            minDate: jQuery(
                                    '#sel_search_sdate')
                                .val() ? jQuery(
                                    '#sel_search_sdate')
                                .val() : false
                        })
                    },
                    timepicker: false
                });
            });

            if ($('#cash-deposits-chart').length) {
                var form_data = new FormData();
                form_data.append("act", "chart1");
                form_data.append("sdate", $('#sel_search_sdate').val());
                form_data.append("edate", $('#sel_search_edate').val());
                $.ajax({
                    data: form_data,
                    type: "POST",
                    enctype: "multipart/form-data",
                    url: "./index_update",
                    cache: false,
                    timeout: 5000,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var json_data = JSON.parse(data);

                        if (json_data.data3 >= 0) {
                            $('#card1_data3').html(json_data.data3);
                        }
                        if (json_data.data4 >= 0) {
                            $('#card1_data4').html(json_data.data4);
                        }
                        var cashDepositsCanvas = $("#cash-deposits-chart").get(0).getContext("2d");
                        var data = {
                            labels: json_data.labels,
                            datasets: [{
                                    label: '가입',
                                    data: json_data.data1,
                                    borderColor: [
                                        '#ff4747'
                                    ],
                                    borderWidth: 2,
                                    fill: false,
                                    pointBackgroundColor: "#fff"
                                },
                                {
                                    label: '탈퇴',
                                    data: json_data.data2,
                                    borderColor: [
                                        '#4d83ff'
                                    ],
                                    borderWidth: 2,
                                    fill: false,
                                    pointBackgroundColor: "#fff"
                                },
                            ]
                        };
                        var options = {
                            scales: {
                                yAxes: [{
                                    display: true,
                                    gridLines: {
                                        drawBorder: false,
                                        lineWidth: 1,
                                        color: "#e9e9e9",
                                        zeroLineColor: "#e9e9e9",
                                    },
                                    ticks: {
                                        min: 0,
                                        max: 100,
                                        stepSize: 20,
                                        fontColor: "#6c7383",
                                        fontSize: 16,
                                        fontStyle: 300,
                                        padding: 15
                                    }
                                }],
                                xAxes: [{
                                    type: 'time',
                                    time: {
                                        unit: "day",
                                        displayFormats: {
                                            'millisecond': 'MMM DD',
                                            'second': 'MMM DD',
                                            'minute': 'MMM DD',
                                            'hour': 'MMM DD',
                                            'day': 'MM.DD',
                                            'week': 'MMM DD',
                                            'month': 'MMM DD',
                                            'quarter': 'MMM DD',
                                            'year': 'MMM DD',
                                        }
                                    },
                                    display: true,
                                    gridLines: {
                                        drawBorder: false,
                                        lineWidth: 1,
                                        color: "#e9e9e9",
                                    },
                                    ticks: {
                                        fontColor: "#6c7383",
                                        fontSize: 16,
                                        fontStyle: 300,
                                        padding: 15
                                    }
                                }]
                            },
                            legend: {
                                display: false
                            },
                            legendCallback: function(chart) {
                                var text = [];
                                text.push('<ul class="dashboard-chart-legend">');
                                for (var i = 0; i < chart.data.datasets.length; i++) {
                                    text.push('<li><span style="background-color: ' + chart.data.datasets[i].borderColor[0] + ' "></span>');
                                    if (chart.data.datasets[i].label) {
                                        text.push(chart.data.datasets[i].label);
                                    }
                                }
                                text.push('</ul>');
                                return text.join("");
                            },
                            elements: {
                                point: {
                                    radius: 3
                                },
                                line: {
                                    tension: 0
                                }
                            },
                            stepsize: 1,
                            layout: {
                                padding: {
                                    top: 0,
                                    bottom: -10,
                                    left: -10,
                                    right: 0
                                }
                            }
                        };
                        // 차트 생성
                        var cashDeposits = new Chart(cashDepositsCanvas, {
                            type: 'line',
                            data: data,
                            options: options
                        });
                        // 범례 추가
                        document.getElementById('cash-deposits-chart-legend').innerHTML = cashDeposits.generateLegend();
                    }
                });
            }

            if ($('#cash-deposits-chart2').length) {
                var form_data = new FormData();
                form_data.append("act", "chart2");
                form_data.append("sdate", $('#sel_search_sdate').val());
                form_data.append("edate", $('#sel_search_edate').val());
                $.ajax({
                    data: form_data,
                    type: "POST",
                    enctype: "multipart/form-data",
                    url: "./index_update",
                    cache: false,
                    timeout: 5000,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var json_data = JSON.parse(data);

                        if (json_data.data3 >= 0) {
                            $('#card2_data3').html(json_data.data3);
                        }
                        if (json_data.data4 >= 0) {
                            $('#card2_data4').html(json_data.data4);
                        }
                        var cashDepositsCanvas = $("#cash-deposits-chart2").get(0).getContext("2d");
                        var data = {
                            labels: json_data.labels,
                            datasets: [{
                                    label: '그룹',
                                    data: json_data.data1,
                                    borderColor: [
                                        '#ff4747'
                                    ],
                                    borderWidth: 2,
                                    fill: false,
                                    pointBackgroundColor: "#fff"
                                },
                                {
                                    label: '일정',
                                    data: json_data.data2,
                                    borderColor: [
                                        '#4d83ff'
                                    ],
                                    borderWidth: 2,
                                    fill: false,
                                    pointBackgroundColor: "#fff"
                                },
                            ]
                        };
                        var options = {
                            scales: {
                                yAxes: [{
                                    display: true,
                                    gridLines: {
                                        drawBorder: false,
                                        lineWidth: 1,
                                        color: "#e9e9e9",
                                        zeroLineColor: "#e9e9e9",
                                    },
                                    ticks: {
                                        min: 0,
                                        max: 100,
                                        stepSize: 20,
                                        fontColor: "#6c7383",
                                        fontSize: 16,
                                        fontStyle: 300,
                                        padding: 15
                                    }
                                }],
                                xAxes: [{
                                    type: 'time',
                                    time: {
                                        unit: "day",
                                        displayFormats: {
                                            'millisecond': 'MMM DD',
                                            'second': 'MMM DD',
                                            'minute': 'MMM DD',
                                            'hour': 'MMM DD',
                                            'day': 'MM.DD',
                                            'week': 'MMM DD',
                                            'month': 'MMM DD',
                                            'quarter': 'MMM DD',
                                            'year': 'MMM DD',
                                        }
                                    },
                                    display: true,
                                    gridLines: {
                                        drawBorder: false,
                                        lineWidth: 1,
                                        color: "#e9e9e9",
                                    },
                                    ticks: {
                                        fontColor: "#6c7383",
                                        fontSize: 16,
                                        fontStyle: 300,
                                        padding: 15
                                    }
                                }]
                            },
                            legend: {
                                display: false
                            },
                            legendCallback: function(chart) {
                                var text = [];
                                text.push('<ul class="dashboard-chart-legend">');
                                for (var i = 0; i < chart.data.datasets.length; i++) {
                                    text.push('<li><span style="background-color: ' + chart.data.datasets[i].borderColor[0] + ' "></span>');
                                    if (chart.data.datasets[i].label) {
                                        text.push(chart.data.datasets[i].label);
                                    }
                                }
                                text.push('</ul>');
                                return text.join("");
                            },
                            elements: {
                                point: {
                                    radius: 3
                                },
                                line: {
                                    tension: 0
                                }
                            },
                            stepsize: 1,
                            layout: {
                                padding: {
                                    top: 0,
                                    bottom: -10,
                                    left: -10,
                                    right: 0
                                }
                            }
                        };
                        var cashDeposits = new Chart(cashDepositsCanvas, {
                            type: 'line',
                            data: data,
                            options: options
                        });
                        document.getElementById('cash-deposits-chart-legend2').innerHTML = cashDeposits.generateLegend();
                    }
                });
            }

            if ($('#cash-deposits-chart3').length) {
                var form_data = new FormData();
                form_data.append("act", "chart3");
                form_data.append("sdate", $('#sel_search_sdate').val());
                form_data.append("edate", $('#sel_search_edate').val());
                $.ajax({
                    data: form_data,
                    type: "POST",
                    enctype: "multipart/form-data",
                    url: "./index_update",
                    cache: false,
                    timeout: 5000,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var json_data = JSON.parse(data);

                        if (json_data.data3 >= 0) {
                            $('#card3_data3').html(json_data.data3);
                        }

                        var cashDepositsCanvas = $("#cash-deposits-chart3").get(0).getContext("2d");
                        var data = {
                            labels: json_data.labels,
                            datasets: [{
                                label: '신청',
                                data: json_data.data1,
                                borderColor: [
                                    '#ff4747'
                                ],
                                borderWidth: 2,
                                fill: false,
                                pointBackgroundColor: "#fff"
                            }, ]
                        };
                        var options = {
                            scales: {
                                yAxes: [{
                                    display: true,
                                    gridLines: {
                                        drawBorder: false,
                                        lineWidth: 1,
                                        color: "#e9e9e9",
                                        zeroLineColor: "#e9e9e9",
                                    },
                                    ticks: {
                                        min: 0,
                                        max: 100,
                                        stepSize: 20,
                                        fontColor: "#6c7383",
                                        fontSize: 16,
                                        fontStyle: 300,
                                        padding: 15
                                    }
                                }],
                                xAxes: [{
                                    type: 'time',
                                    time: {
                                        unit: "day",
                                        displayFormats: {
                                            'millisecond': 'MMM DD',
                                            'second': 'MMM DD',
                                            'minute': 'MMM DD',
                                            'hour': 'MMM DD',
                                            'day': 'MM.DD',
                                            'week': 'MMM DD',
                                            'month': 'MMM DD',
                                            'quarter': 'MMM DD',
                                            'year': 'MMM DD',
                                        }
                                    },
                                    display: true,
                                    gridLines: {
                                        drawBorder: false,
                                        lineWidth: 1,
                                        color: "#e9e9e9",
                                    },
                                    ticks: {
                                        fontColor: "#6c7383",
                                        fontSize: 16,
                                        fontStyle: 300,
                                        padding: 15
                                    }
                                }]
                            },
                            legend: {
                                display: false
                            },
                            legendCallback: function(chart) {
                                var text = [];
                                text.push('<ul class="dashboard-chart-legend">');
                                for (var i = 0; i < chart.data.datasets.length; i++) {
                                    text.push('<li><span style="background-color: ' + chart.data.datasets[i].borderColor[0] + ' "></span>');
                                    if (chart.data.datasets[i].label) {
                                        text.push(chart.data.datasets[i].label);
                                    }
                                }
                                text.push('</ul>');
                                return text.join("");
                            },
                            elements: {
                                point: {
                                    radius: 3
                                },
                                line: {
                                    tension: 0
                                }
                            },
                            stepsize: 1,
                            layout: {
                                padding: {
                                    top: 0,
                                    bottom: -10,
                                    left: -10,
                                    right: 0
                                }
                            }
                        };
                        var cashDeposits = new Chart(cashDepositsCanvas, {
                            type: 'line',
                            data: data,
                            options: options
                        });
                        document.getElementById('cash-deposits-chart-legend3').innerHTML = cashDeposits.generateLegend();
                    }
                });
            }

            if ($('#cash-deposits-chart4').length) {
                var form_data = new FormData();
                form_data.append("act", "chart4");
                form_data.append("sdate", $('#sel_search_sdate').val());
                form_data.append("edate", $('#sel_search_edate').val());
                $.ajax({
                    data: form_data,
                    type: "POST",
                    enctype: "multipart/form-data",
                    url: "./index_update",
                    cache: false,
                    timeout: 5000,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        var json_data = JSON.parse(data);

                        if (json_data.data3 >= 0) {
                            $('#card4_data3').html(json_data.data3);
                        }
                        if (json_data.data4 >= 0) {
                            $('#card4_data4').html(json_data.data4);
                        }

                        var cashDepositsCanvas = $("#cash-deposits-chart4").get(0).getContext("2d");
                        var data = {
                            labels: json_data.labels,
                            datasets: [{
                                    label: '신청',
                                    data: json_data.data1,
                                    borderColor: [
                                        '#ff4747'
                                    ],
                                    borderWidth: 2,
                                    fill: false,
                                    pointBackgroundColor: "#fff"
                                },
                                {
                                    label: '취소',
                                    data: json_data.data2,
                                    borderColor: [
                                        '#4d83ff'
                                    ],
                                    borderWidth: 2,
                                    fill: false,
                                    pointBackgroundColor: "#fff"
                                },
                            ]
                        };
                        var options = {
                            scales: {
                                yAxes: [{
                                    display: true,
                                    gridLines: {
                                        drawBorder: false,
                                        lineWidth: 1,
                                        color: "#e9e9e9",
                                        zeroLineColor: "#e9e9e9",
                                    },
                                    ticks: {
                                        min: 0,
                                        max: 100,
                                        stepSize: 20,
                                        fontColor: "#6c7383",
                                        fontSize: 16,
                                        fontStyle: 300,
                                        padding: 15
                                    }
                                }],
                                xAxes: [{
                                    type: 'time',
                                    time: {
                                        unit: "day",
                                        displayFormats: {
                                            'millisecond': 'MMM DD',
                                            'second': 'MMM DD',
                                            'minute': 'MMM DD',
                                            'hour': 'MMM DD',
                                            'day': 'MM.DD',
                                            'week': 'MMM DD',
                                            'month': 'MMM DD',
                                            'quarter': 'MMM DD',
                                            'year': 'MMM DD',
                                        }
                                    },
                                    display: true,
                                    gridLines: {
                                        drawBorder: false,
                                        lineWidth: 1,
                                        color: "#e9e9e9",
                                    },
                                    ticks: {
                                        fontColor: "#6c7383",
                                        fontSize: 16,
                                        fontStyle: 300,
                                        padding: 15
                                    }
                                }]
                            },
                            legend: {
                                display: false
                            },
                            legendCallback: function(chart) {
                                var text = [];
                                text.push('<ul class="dashboard-chart-legend">');
                                for (var i = 0; i < chart.data.datasets.length; i++) {
                                    text.push('<li><span style="background-color: ' + chart.data.datasets[i].borderColor[0] + ' "></span>');
                                    if (chart.data.datasets[i].label) {
                                        text.push(chart.data.datasets[i].label);
                                    }
                                }
                                text.push('</ul>');
                                return text.join("");
                            },
                            elements: {
                                point: {
                                    radius: 3
                                },
                                line: {
                                    tension: 0
                                }
                            },
                            stepsize: 1,
                            layout: {
                                padding: {
                                    top: 0,
                                    bottom: -10,
                                    left: -10,
                                    right: 0
                                }
                            }
                        };
                        var cashDeposits = new Chart(cashDepositsCanvas, {
                            type: 'line',
                            data: data,
                            options: options
                        });
                        document.getElementById('cash-deposits-chart-legend4').innerHTML = cashDeposits.generateLegend();
                    }
                });
            }
        });
    })(jQuery);
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/mng/foot.inc.php";
?>