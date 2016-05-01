<?php global $so_thutu; ?>
<div class="hangmuc_items_wrapper">
    <div class="accordionButton" id="a">
        <span># Hạng mục</span><span>:</span> <span class="span_ten_hangmuc"></span>
        <span class="plusMinus">+</span>
        <span><a href="#" class="remove_hangmuc_button">Xóa</a></span>
    </div>
    <div class="accordionContent">
        <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table form-table-hangmuc">
            <tbody>
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="tenhangmuc"><?php _e( 'Tên hạng mục', 'simple_plugin' ); ?></label>
                    </th>
                    <td>
                        <input  name="hangmuc[<?php echo $so_thutu; ?>][tenhangmuc]" type="text" style="width: 95%" value="" class="code tenhangmuc"  required />
                    </td>
                </tr>
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="trangthai_hangmuc"><?php _e( 'Trạng thái', 'simple_plugin' ); ?></label>
                    </th>
                    <td>
                        <select  name="hangmuc[<?php echo $so_thutu; ?>][trangthai_hoanthanh]" class="code">
                            <option value="Đã hoàn thành" >Đã hoàn thành</option>
                            <option value="Đang triển khai">Đang triển khai</option>
                            <option value="Chưa hoàn thành">Chưa hoàn thành</option>
                            <option value="Đã hủy">Đã hủy</option>
                        </select>
                    </td>
                </tr>
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="noidung_hangmuc"><?php _e( 'Nội dung', 'simple_plugin' ); ?></label>
                    </th>
                    <td>
                        <textarea name="hangmuc[<?php echo $so_thutu; ?>][noidung_hangmuc]"  class="code" style="width: 95%"></textarea>
                    </td>
                </tr>
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="hangmuc_tgbatdau"><?php _e( 'Thời gian bắt đầu', 'simple_plugin' ); ?></label>
                    </th>
                    <td>
                        <input class="hangmuc_tgbatdau" name="hangmuc[<?php echo $so_thutu; ?>][thoigianbatdau]" type="text" style="width: 95%" value="<?php if( !empty( $item['thoigianbatdau'] ) ) echo esc_attr( $item['thoigianbatdau'] );?>" class="code"  required />
                    </td>
                </tr>
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="hangmuc_tgketthuc"><?php _e( 'Thời gian kết thúc', 'simple_plugin' ); ?></label>
                    </th>
                    <td>
                        <input class="hangmuc_tgketthuc" name="hangmuc[<?php echo $so_thutu; ?>][thoigianketthuc]" type="text" style="width: 95%" value="<?php if( !empty( $item['thoigianketthuc'] ) ) echo esc_attr( $item['thoigianketthuc'] );?>" class="code"  required />
                    </td>
                </tr>
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="phantram_hoanthanh_hangmuc"><?php _e( 'Phần trăm hoàn thành', 'simple_plugin' ); ?></label>
                    </th>
                    <td>
                        <input type="range" class="percent_range_input" name="rangeInput[hangmuc][]" min="0" max="100"  value="0" style="width: 70%;" />  <!-- onchange="updateTextInput(this.value);" -->                                                     
                        <span class="percent_number">0</span><span id="percent_unit">%</span>
                        <input type="hidden" class="input_percent_number" name="hangmuc[<?php echo $so_thutu; ?>][phantram_hoanthanh_hangmuc]" value=""/>
                        
                    </td>
                </tr>
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <h2><a class="add-new-h2" href="#" id="button_hangmuc_them_congviec" data-stt_cv="0" data-stt_hangmuc="<?php echo $so_thutu; ?>">Thêm công việc</a></h2>
                    </th>
                    <td>
                        <div class="hangmuc_congviec_wrapper_all" id="hangmuc_congviec_wrapper_all">
                        
                        </div>
                        <?php //include( TT_DIR_PATH. '/templates/new_congviec.php' ); ?>
                    </td>
                </tr><!-- end thêm công viêc -->    
                
            </tbody>
        </table>
    </div>
    <div class="clearfix"></div>
</div><!-- hangmuc_items_wrapper -->