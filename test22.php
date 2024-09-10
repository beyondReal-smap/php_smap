var scheduleMarkers = [];
var optimalPath;
var drawInfoArr = [];
var resultDrawArr = [];
var scheduleMarkerCoordinates = [];
var scheduleStatus = [];
var startX, startY, endX, endY;
var markers;
var polylines = [];
var profileMarkers = [];

// 버튼 엘리먼트
var showPathButton = document.getElementById('showPathButton');
var showPathAdButton = document.getElementById('showPathAdButton');
let map;

// 전역 상태 객체
const state = {
    pathData: null,
    walkingData: null,
    isDataLoaded: false
};

// 그룹원별 슬라이드 컨테이너
const groupMemberSlides = {};
let googleMapsLoaded = false;
let googleMapsLoadPromise = null;

// 페이지 로드 시 초기화
document.addEventListener('DOMContentLoaded', () => {
    console.log("DOM fully loaded and parsed");
    initialize().catch(error => showErrorToUser("초기화 중 오류가 발생했습니다. 다시 시도해 주세요."));
});

// 초기화 함수
async function initialize() {
    const sgdt_idx = <?= $sgdt_row['sgdt_idx'] ?>; // PHP에서 그룹 ID 가져오기

    try {
        // 멤버 선택 및 데이터 로딩
        const { scheduleData, pedestrianData } = await loadMemberData(sgdt_idx);

        // 지도 초기화
        await initializeMap(scheduleData);

        // 경로 데이터 처리 및 슬라이드 업데이트
        await processPathData(pedestrianData, sgdt_idx);

        // 광고 카운트 확인
        checkAdCount();

        // 날씨 정보 가져오기
        await fetchWeatherData();
    } catch (error) {
        console.error("Error during initialization:", error);
        throw error;
    }
}

// 멤버 데이터 로딩 함수
async function loadMemberData(sgdt_idx) {
    if (window.FakeLoader && typeof window.FakeLoader.showOverlay === 'function') {
        window.FakeLoader.showOverlay();
    }

    try {
        const [scheduleData, memberScheduleData] = await Promise.all([
            fetchScheduleMapData(sgdt_idx),
            fetchMemberScheduleData(sgdt_idx),
        ]);

        // pedestrian_path_check() 함수의 결과를 Promise로 처리
        const pedestrianData = await fetchPedestrianPathData(sgdt_idx);

        return { scheduleData, memberScheduleData, pedestrianData };
    } catch (error) {
        console.error("Error loading member data:", error);
        throw error;
    } finally {
        if (window.FakeLoader && typeof window.FakeLoader.hideOverlay === 'function') {
            window.FakeLoader.hideOverlay();
        }
    }
}

// 지도 초기화 함수
async function initializeMap(data) {
    if ('ko' === '<?= $userLang ?>') {
        await initializeNaverMap(data);
    } else {
        await initializeGoogleMap(data);
    }
}

// 네이버 지도 초기화 함수
async function initializeNaverMap(data) {
    map = new naver.maps.Map("map", {
        center: new naver.maps.LatLng(data.my_lat, data.mt_long),
        zoom: 16,
        mapTypeControl: false
    });

    if (markerData.marker_reload == 'Y') {
                // profileMarkers 배열에 담겨있는 마커 제거
                for (var i = 0; i < profileMarkers.length; i++) {
                    profileMarkers[i].setMap(null); // 지도에서 마커 제거
                }
                // 마커 배열 초기화
                profileMarkers = [];
                // 기존 프로필 마커 추가
                var profileMarkerOptions = {
                    position: new naver.maps.LatLng(st_lat, st_lng),
                    map: map,
                    icon: {
                        content: '<div class="point_wrap"><div class="map_user"><div class="map_rt_img rounded_14"><div class="rect_square"><img src="' + my_profile + '" alt="이미지" onerror="this.src=\'<?= $ct_no_img_url ?>\'"/></div></div></div></div>',
                        size: new naver.maps.Size(44, 44),
                        origin: new naver.maps.Point(0, 0),
                        anchor: new naver.maps.Point(22, 22)
                    },
                    zIndex: 3
                };
                var profileMarker = new naver.maps.Marker(profileMarkerOptions);
                profileMarkers.push(profileMarker);

                for (var i = 1; i <= markerData.profile_count; i++) {
                    var profileMarkerOptions = {
                        position: new naver.maps.LatLng(markerData['profilemarkerLat_' + i], markerData['profilemarkerLong_' + i]),
                        map: map,
                        icon: {
                            content: '<div class="point_wrap"><div class="map_user"><div class="map_rt_img rounded_14"><div class="rect_square"><img src="' + markerData['profilemarkerImg_' + i] + '" alt="이미지" onerror="this.src=\'<?= $ct_no_img_url ?>\'"/></div></div></div></div>',
                            size: new naver.maps.Size(44, 44),
                            origin: new naver.maps.Point(0, 0),
                            anchor: new naver.maps.Point(22, 22)
                        },
                        zIndex: 2
                    };
                    var profileMarker = new naver.maps.Marker(profileMarkerOptions);
                    profileMarkers.push(profileMarker);
                }
            } else {
                map = new naver.maps.Map("map", {
                    center: new naver.maps.LatLng(st_lat, st_lng),
                    zoom: 16,
                    mapTypeControl: false
                });

                var optBottom = document.querySelector('.opt_bottom');
                if (optBottom) {
                    var transformY = optBottom.style.transform;
                    if (transformY == 'translateY(0px)') {
                        map.panBy(new naver.maps.Point(0, 180)); // 위로 180 픽셀 이동
                    }
                }
                // 마커 배열 초기화
                markers = [];
                polylines = [];
                profileMarkers = [];
                // 기존 프로필 마커 추가
                var profileMarkerOptions = {
                    position: new naver.maps.LatLng(st_lat, st_lng),
                    map: map,
                    icon: {
                        content: '<div class="point_wrap"><div class="map_user"><div class="map_rt_img rounded_14"><div class="rect_square"><img src="' + my_profile + '" alt="이미지" onerror="this.src=\'<?= $ct_no_img_url ?>\'"/></div></div></div></div>',
                        size: new naver.maps.Size(44, 44),
                        origin: new naver.maps.Point(0, 0),
                        anchor: new naver.maps.Point(22, 22)
                    },
                    zIndex: 3
                };
                var profileMarker = new naver.maps.Marker(profileMarkerOptions);
                profileMarkers.push(profileMarker);
                // markers.push(profileMarker);

                for (var i = 1; i <= markerData.profile_count; i++) {
                    var profileMarkerOptions = {
                        position: new naver.maps.LatLng(markerData['profilemarkerLat_' + i], markerData['profilemarkerLong_' + i]),
                        map: map,
                        icon: {
                            content: '<div class="point_wrap"><div class="map_user"><div class="map_rt_img rounded_14"><div class="rect_square"><img src="' + markerData['profilemarkerImg_' + i] + '" alt="이미지" onerror="this.src=\'<?= $ct_no_img_url ?>\'"/></div></div></div></div>',
                            size: new naver.maps.Size(44, 44),
                            origin: new naver.maps.Point(0, 0),
                            anchor: new naver.maps.Point(22, 22)
                        },
                        zIndex: 2
                    };
                    var profileMarker = new naver.maps.Marker(profileMarkerOptions);
                    profileMarkers.push(profileMarker);
                    // markers.push(profileMarker);
                }
                // 스케줄 마커 추가
                if (markerData.schedule_chk === 'Y') {
                    var positions = [];
                    for (var i = 1; i <= markerData.count; i++) {
                        if (i === 1) {
                            // 출발지 좌표
                            startX = markerData['markerLat_' + i];
                            startY = markerData['markerLong_' + i];
                        } else if (i === markerData.count) {
                            // 도착지 좌표
                            endX = markerData['markerLat_' + i];
                            endY = markerData['markerLong_' + i];
                        }

                        var markerLat = markerData['markerLat_' + i];
                        var markerOptions = {
                            position: new naver.maps.LatLng(markerData['markerLat_' + i], markerData['markerLong_' + i]),
                            map: map,
                            icon: {
                                content: markerData['markerContent_' + i],
                                size: new naver.maps.Size(61, 61),
                                origin: new naver.maps.Point(0, 0),
                                anchor: new naver.maps.Point(30, 30)
                            },
                            zIndex: 1
                        };

                        var marker = new naver.maps.Marker(markerOptions);
                        positions.push(marker.getPosition());
                        scheduleMarkers.push(marker);
                        markers.push(marker);
                    }
                    // 스케줄 마커의 개수
                    var markerCount = markerData['count'];
                    // 스케줄 마커의 좌표 배열
                    scheduleMarkerCoordinates = [];
                    scheduleStatus = [];
                    for (var i = 1; i <= markerCount; i++) {
                        var lat = markerData['markerLat_' + i];
                        var lng = markerData['markerLong_' + i];
                        var status = markerData['markerStatus_' + i];
                        scheduleMarkerCoordinates.push(new naver.maps.LatLng(lat, lng));
                        scheduleStatus.push(status);
                    }
                }
            }
            // 지도 이동 시 이벤트 리스너 추가
            naver.maps.Event.addListener(map, 'idle', function() {
                var bounds = map.getBounds();
                markers.forEach(function(marker) {
                    if (bounds.hasLatLng(marker.getPosition())) {
                        marker.setMap(map);
                    } else {
                        marker.setMap(null);
                    }
                });
                polylines.forEach(function(polyline_) {
                    // 폴리라인의 경계를 가져옵니다.
                    var polylineBounds = polyline_.getBounds();
                    if (polylineBounds && bounds.intersects(polylineBounds)) {
                        polyline_.setMap(map);
                    } else {
                        polyline_.setMap(null);
                    }
                });
            });

            // initializeMap 함수 끝에 map 변수의 상태를 체크하고 map이 정상적으로 생성되었을 때에만 setCursor 호출
            if (map) {
                map.setCursor('pointer');
            }
}

// 구글 지도 초기화 함수
async function initializeGoogleMap(data) {
    await loadGoogleMapsScript();

    if (!map) {
        await initGoogleMap(data.my_lat, data.mt_long);
    } else {
        map.setCenter({
            lat: parseFloat(data.my_lat),
            lng: parseFloat(data.mt_long)
        });
    }

    // ... (나머지 구글 지도 초기화 로직: 마커, 이벤트 리스너 추가) ...
}

// 경로 데이터 처리 함수
async function processPathData(pedestrianData, sgdt_idx) {
    if (pedestrianData && pedestrianData.members[sgdt_idx] && pedestrianData.members[sgdt_idx].sllt_json_text) {
        if ('ko' === '<?= $userLang ?>') {
            processNaverPathData(pedestrianData);
        } else {
            processGooglePathData(pedestrianData, sgdt_idx);
        }
    }
}

// 네이버 경로 데이터 처리 함수
function processNaverPathData(data) {
    // ... (네이버 경로 데이터 처리 로직) ...
}

// 구글 경로 데이터 처리 함수
function processGooglePathData(data, sgdt_idx) {
    // ... (구글 경로 데이터 처리 로직) ...
}

// 일정 슬라이드 업데이트 함수
async function updateScheduleSlides(data, sgdt_idx) {
    // ... (일정 슬라이드 업데이트 로직) ...

    // 최적 경로 데이터 기반 슬라이드 업데이트
    await createOrUpdateSlidesForMember(sgdt_idx, data.path_data);
}

// ... (나머지 함수들: fetchScheduleMapData, fetchMemberScheduleData, fetchPedestrianPathData, 
//     createGoogleScheduleMarker, addGoogleProfileMarker, showOptimalPath, 
//     createGradientGoogle, interpolateColor, drawPathAndMarkers, createGradient, 
//     isPathDrawn, retryDrawPath, makeMarker, f_get_angle, getDistance, 
//     calculateSegmentDistance, deg2rad, calculateWalkingTime, getWalkingTime, 
//     updateLocationInfo, map_panto, f_my_location_btn, checkAdCount, 
//     fetchAdDisplayStatus, requestAdDisplay, saveErrorLog, updateAdDisplayCount, 
//     isAndroidDevice, isiOSDevice) ...

// 오류 메시지 표시 함수
function showErrorToUser(message) {
    alert(message);
}

// 날씨 정보 가져오기 함수
async function fetchWeatherData() {
    const formData = new FormData();
    formData.append("act", "weather_get");

    try {
        const response = await fetch("./index_update", {
            method: "POST",
            body: formData,
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.text();

        if (data) {
            $('#top_weather_box').empty().html(data);
            my_location_update();
            // ... (날씨 정보 관련 GA 이벤트 전송) ...
        } else {
            console.warn("No weather data received");
        }
    } catch (err) {
        console.error("Error fetching weather data:", err);
        showErrorToUser("날씨 정보를 가져오는 중 오류가 발생했습니다.");
    }
}

// ... (나머지 함수들: getPointStatus, getStatusText, addSchedule) ...