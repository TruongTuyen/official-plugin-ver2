<tr class="form-field">
    <th valign="top" scope="row">
        <h2><a class="add-new-h2" href="#" id="button_hangmuc_them_congviec">Thêm công việc</a></h2>
    </th>
    <td>
        <div class="" id="hangmuc_them_congviec">
            <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table form-table-hangmuc-congviec">
                <tbody>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="hangmuc_tencongviec[]"><?php _e( 'Tên công việc', 'simple_plugin' ); ?></label>
                        </th>
                        <td>
                            <input  name="hangmuc_tencongviec[]" type="text" style="width: 95%" value="" class="code"  required />
                        </td>
                    </tr>  
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="hangmuc_noidungcongviec[]"><?php _e( 'Nội dung công việc', 'simple_plugin' ); ?></label>
                        </th>
                        <td>
                            <textarea style="width: 95%"class="code" required ></textarea>
                        </td>
                    </tr> 
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="trangthai_hangmuc_congviec"><?php _e( 'Trạng thái', 'simple_plugin' ); ?></label>
                        </th>
                        <td>
                            <select  name="trangthai_hangmuc_congviec[]" class="code">
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
                                
                            </div>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="hangmuc_congviec_tgbatdau"><?php _e( 'Thời gian bắt đầu', 'simple_plugin' ); ?></label>
                        </th>
                        <td>
                            <input id="hangmuc_congviec_tgbatdau" name="hangmuc_congviec_tgbatdau[]" type="text" style="width: 95%" value="<?php if( !empty( $item['thoigianbatdau'] ) ) echo esc_attr( $item['thoigianbatdau'] );?>" class="code"  required />
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th valign="top" scope="row">
                            <label for="hangmuc_congviec_tgketthuc"><?php _e( 'Thời gian kết thúc', 'simple_plugin' ); ?></label>
                        </th>
                        <td>
                            <input id="hangmuc_congviec_tgketthuc" name="hangmuc_congviec_tgketthuc[]" type="text" style="width: 95%" value="<?php if( !empty( $item['thoigianketthuc'] ) ) echo esc_attr( $item['thoigianketthuc'] );?>" class="code"  required />
                        </td>
                    </tr> 
                    
                </tbody>    
            </table>
        </div>
    </td>
</tr><!-- end thêm công viêc -->