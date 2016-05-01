<?php global $stt_congviec, $stt_hangmuc; ?>
<?php $unig_id = uniqid(); ?>
<div class="hangmuc_congviec_items_wrapper">
    <div class="accordionButton2" >
        <span># Công việc</span><span>:</span> <span class="span_ten_hangmuc_congviec"></span>
        <span class="plusMinus">+</span>
        <span><a href="#" class="remove_hangmuc_cong_viec_button">Xóa</a></span>
    </div>
    <div class="accordionContent2">
        <div class="hangmuc_them_congviec" id="hangmuc_them_congviec_<?php echo $unig_id; ?>">
            <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table form-table-hangmuc-congviec">
                <tbody>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="hangmuc_tencongviec[]"><?php _e( 'Tên công việc', 'simple_plugin' ); ?></label>
                        </th>
                        <td>
                            <input name="hangmuc[<?php echo $stt_hangmuc; ?>][congviec][<?php echo $stt_congviec; ?>][tencongviec]" type="text" style="width: 95%" value="" class="code hangmuc_tencongviec"  required />
                        </td>
                    </tr>  
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="hangmuc[<?php echo $stt_hangmuc; ?>][congviec][<?php echo $stt_congviec; ?>][noidungcongviec]"><?php _e( 'Nội dung công việc', 'simple_plugin' ); ?></label>
                        </th>
                        <td>
                            <textarea name="hangmuc[<?php echo $stt_hangmuc; ?>][congviec][<?php echo $stt_congviec; ?>][noidungcongviec]" style="width: 95%"class="code" required ></textarea>
                        </td>
                    </tr> 
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="trangthai_hangmuc_congviec"><?php _e( 'Trạng thái', 'simple_plugin' ); ?></label>
                        </th>
                        <td>
                            <select  name="hangmuc[<?php echo $stt_hangmuc; ?>][congviec][<?php echo $stt_congviec; ?>][trangthai_hoanthanh]" class="code">
                                <option value="Đã hoàn thành" >Đã hoàn thành</option>
                                <option value="Đang triển khai">Đang triển khai</option>
                                <option value="Chưa hoàn thành">Chưa hoàn thành</option>
                                <option value="Đã hủy">Đã hủy</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th valign="top" scope="row">
                            <label for="thanhvien_thamgia"><?php _e( 'Thành viên tham gia', 'simple_plugin' ); ?></label>
                        </th>
                        <td>
                            <div id="nhanvien_thamgia_hangmuc_congviec">
                                <?php $checkbox_name = "hangmuc[".$stt_hangmuc. "][congviec][". $stt_congviec ."][nhanvien_thamgia][]"; ?>
                                <?php echo TT_Nhanvien::render_list_checkbox_nhanvien( 'show', $checkbox_name ); ?>
                            </div>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="hangmuc_congviec_tgbatdau"><?php _e( 'Thời gian bắt đầu', 'simple_plugin' ); ?></label>
                        </th>
                        <td>
                            <input class="hangmuc_congviec_tgbatdau" name="hangmuc[<?php echo $stt_hangmuc; ?>][congviec][<?php echo $stt_congviec; ?>][tg_batdau]" type="text" style="width: 95%" value="<?php if( !empty( $item['thoigianbatdau'] ) ) echo esc_attr( $item['thoigianbatdau'] );?>" class="code"  required />
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="hangmuc_congviec_tgketthuc"><?php _e( 'Thời gian kết thúc', 'simple_plugin' ); ?></label>
                        </th>
                        <td>
                            <input class="hangmuc_congviec_tgketthuc" name="hangmuc[<?php echo $stt_hangmuc; ?>][congviec][<?php echo $stt_congviec; ?>][tg_ketthuc]" type="text" style="width: 95%" value="<?php if( !empty( $item['thoigianketthuc'] ) ) echo esc_attr( $item['thoigianketthuc'] );?>" class="code"  required />
                        </td>
                    </tr> 
                    
                </tbody>    
            </table>
        </div>
      </div>
      <div class="clearfix"></div>
</div>        