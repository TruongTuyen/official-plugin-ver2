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
                        <input  name="tenhangmuc[]" type="text" style="width: 95%" value="" class="code tenhangmuc"  required />
                    </td>
                </tr>
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="trangthai_hangmuc"><?php _e( 'Trạng thái', 'simple_plugin' ); ?></label>
                    </th>
                    <td>
                        <select  name="trangthai_hangmuc[]" class="code">
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
                        <textarea name="noidung_hangmuc"  class="code" style="width: 95%"></textarea>
                    </td>
                </tr>
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="hangmuc_tgbatdau"><?php _e( 'Thời gian bắt đầu', 'simple_plugin' ); ?></label>
                    </th>
                    <td>
                        <input id="hangmuc_tgbatdau" name="thoigianbatdau" type="text" style="width: 95%" value="<?php if( !empty( $item['thoigianbatdau'] ) ) echo esc_attr( $item['thoigianbatdau'] );?>" class="code"  required />
                    </td>
                </tr>
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="hangmuc_tgketthuc"><?php _e( 'Thời gian kết thúc', 'simple_plugin' ); ?></label>
                    </th>
                    <td>
                        <input id="hangmuc_tgketthuc" name="thoigianketthuc" type="text" style="width: 95%" value="<?php if( !empty( $item['thoigianketthuc'] ) ) echo esc_attr( $item['thoigianketthuc'] );?>" class="code"  required />
                    </td>
                </tr>
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="phantram_hoanthanh_hangmuc"><?php _e( 'Phần trăm hoàn thành', 'simple_plugin' ); ?></label>
                    </th>
                    <td>
                        <input type="range" id="percent_range_input" name="rangeInput" min="0" max="100" onchange="updateTextInput(this.value);" value="0" style="width: 70%;" />                                                       
                        <span id="percent_number">0</span><span id="percent_unit">%</span>
                        <input type="hidden" id="input_percent_number" name="phantram_hoanthanh_hangmuc" value=""/>
                        
                    </td>
                </tr>
                
                <?php include( TT_DIR_PATH. '/templates/new_congviec.php' ); ?>
                
            </tbody>
        </table>
    </div>
    <div class="clearfix"></div>
</div><!-- hangmuc_items_wrapper -->