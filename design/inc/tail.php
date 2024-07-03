	
    <div class="ft pt_20 pb_80 px_20 <?php if ($_GET['hd_num'] == '') { ?> d-block <?php } ?>">
        <p class="fs_12 text-center py-1">서울특별시 OO구 OO로(하단정보필요)</p>
        <p class="fs_12 text-center py-1"><span>회사명 : smap</span><span> | </span><span> 대표 :  홍길동</span></p>
        <p class="fs_12 text-center py-1">사업자등록번호 : 123-12345  </p>
    </div>


    <!-- top btn-->
    <div class="top_btn_wr <?php if (!empty($b_menu)) { echo ($b_menu == "2" || $b_menu == "3") ? 'b_on_top' : 'b_on'; } ?>">
        <button type="button" class="top_btn"><img src="./img/ico_scr_top.png" alt="스크롤탑"/></button>
    </div>

	</body>

	</html>