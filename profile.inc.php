<div class="upload_img_wrap profile_upolad" style="cursor:pointer;" data-toggle="modal" data-target="#camera_album">
    <div class="form-group upload_img_item profile_add_btn">
        <label class="file_upload fs_12 fw_700 square border"><i class="xi-camera"></i></label>
    </div>
    <div class="form-group upload_img_item profile_view_img">
        <label class="file_upload square d-none"><i class="xi-plus"></i></label>
        <div class="rect_square">
            <!-- 이미지 없을 때 -->
            <img src="<?= $_SESSION['_mt_file1'] ?>" onerror="this.src='<?= $ct_no_profile_img_url ?>'" alt="프로필이미지" id="member_profile_img" />
            <div class="dimmed"></div>
        </div>
    </div>
</div>

<?php if ($_SESSION['_mt_idx']) { ?>
    <div class="modal fade" id="camera_album" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body pt_40 pb_27 px-3">
                    <p class="fs_16 fw_700 line_h1_4 text_dynamic text-center"><?= $translations['txt_select_camera_or_album'] ?></p>
                </div>
                <div class="modal-footer w-100 px-0 py-0 mt-0 border-0">
                    <div class="d-flex align-items-center w-100 mx-0 my-0">
                        <button type="button" class="btn btn-bg_gray btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_right_0" onclick="open_camera('<?= $_SESSION['_mt_idx'] ?>')"><?= $translations['txt_camera'] ?></button>
                        <button type="button" class="btn btn-primary btn-md w-50 rounded_t_left_0 rounded_t_right_0 rounded_b_left_0" onclick="openAlbum('<?= $_SESSION['_mt_idx'] ?>');"><?= $translations['txt_album'] ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function open_camera(mt_idx) {
            var message = {
                "type": "openCamera",
                "param": mt_idx
            };
            if (isAndroid()) {
                window.smapAndroid.openCamera(mt_idx);
            } else if (isiOS()) {
                window.webkit.messageHandlers.smapIos.postMessage(message);
            }
        }

        function openAlbum(mt_idx) {
            var message = {
                "type": "openAlbum",
                "param": mt_idx
            };
            if (isAndroid()) {
                window.smapAndroid.openAlbum(mt_idx);
            } else if (isiOS()) {
                window.webkit.messageHandlers.smapIos.postMessage(message);
            }
        }

        function isAndroid() {
            return navigator.userAgent.match(/Android/i);
        }

        function isiOS() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        }

        function isAndroidDevice() {
            return /Android/i.test(navigator.userAgent) && typeof window.smapAndroid !== 'undefined';
        }

        function isiOSDevice() {
            return /iPhone|iPad|iPod/i.test(navigator.userAgent) && window.webkit && window.webkit.messageHandlers && window.webkit.messageHandlers.smapIos;
        }
    </script>
<?php } ?>