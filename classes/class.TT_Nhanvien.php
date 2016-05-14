<?php
class TT_Nhanvien extends WP_List_Table{
    public function __construct(){
       global $status, $page;
        parent::__construct(
            array(
                'singular' => "mot_nhanvien",
                'plural'   => "nhieu_nhanvien" 
            )
        );
        
        
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
        
        add_action( "wp_ajax_tt_ajax_filter_info_nhanvien", array( $this, "tt_ajax_load_filter_nhanvien_callback" ) );
        add_action( "wp_ajax_nopriv_tt_ajax_filter_info_nhanvien", array( $this, "tt_ajax_load_filter_nhanvien_callback" ) );
        
    }
    
    function my_current_screen($screen) {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return $screen;
        print_r($screen);
        return $screen;
    }
    
    
    function column_default( $item, $column_name ){
        return $item[ $column_name ];
    }
    
    function column_id_nhanvien( $item ){
        return '<em>' . $item['id_nhanvien'] . '</em>';
    }
    
    function column_namsinh( $item ){
        return '<em>' . $item['namsinh'] . '</em>';
    }
    
    function column_gioitinh( $item ){
        return '<em>' . $item['gioitinh'] . '</em>';
    }
    
    function column_cac_kynang( $item ){
        return '<em>' . $item['cac_kynang'] . '</em>';
    }
    
    function column_quequan( $item ){
        return '<em>' . $item['quequan'] . '</em>';
    }

    function column_hoten( $item ){
        $actions = array(
            'edit'   => sprintf( '<a href="?page=new_nhanvien&id_nhanvien=%s">%s</a>', $item['id_nhanvien'], __( 'Sửa dữ liệu', 'simple_plugin' ) ),
            'delete' => sprintf( '<a href="?page=%s&action=delete&id_nhanvien=%s">%s</a>', $_REQUEST['page'], $item['id_nhanvien'], __( 'Xóa dữ liệu', 'simple_plugin' ) ),
        );
        return sprintf('%s %s', $item['hoten'], $this->row_actions( $actions ) );
    }

    function column_cb( $item ){
        return sprintf( '<input type="checkbox" name="ids[]" value="%d" />', $item['id_nhanvien'] );
    }
    
    function get_columns()
    {
        $columns = array(
            'cb'                => '<input type="checkbox" />', //Tạo nut checkbox
            'id_nhanvien'       => __( 'ID', 'simple_plugin' ),
            'hoten'             => __( 'Họ tên', 'simple_plugin' ),
            'namsinh'           => __( 'Năm sinh', 'simple_plugin' ),
            'gioitinh'          => __( 'Giới tính', 'simple_plugin' ),
            'quequan'           => __( 'Quê quán', 'simple_plugin' ), 
            'cac_kynang'        => __( 'Kỹ năng', 'simple_plugin' ),
        );
        return $columns;
    }

    function get_sortable_columns(){
        $sortable_columns = array(
            'id_nhanvien'     => array( 'id_duan', true ),
            'hoten'           => array( 'tenduan', false ),
            'namsinh'         => array( 'namsinh', false )
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => 'Xóa dữ liệu'
        );
        return $actions;
    }
    
    

    function process_bulk_action(){
        global $wpdb;
        $table_nhanvien       = $wpdb->prefix . 'nhanvien'; 
        $table_chitiet_kynang = $wpdb->prefix . 'chitiet_kynang';

        if ( 'delete' === $this->current_action() && $_REQUEST['page'] == "ds_nhanvien" ) {
            $ids         = isset( $_REQUEST['ids'] ) ? $_REQUEST['ids'] : array();
            $id_nhanvien = isset( $_REQUEST['id_nhanvien'] ) ? $_REQUEST['id_nhanvien'] : '';
            
            if( is_array($ids) && !empty( $ids ) ) {
                $ids = implode(',', $ids);
                $wpdb->query( "DELETE FROM {$table_nhanvien} WHERE id_nhanvien IN( {$ids} )" );
                foreach( $ids as $key=>$value ){
                    $wpdb->update( $table_nhanvien, array( 'display_status'=>'hidden' ), array( 'id_nhanvien' => $value ), array( '%d' ) );
                }
                $wpdb->query( "DELETE FROM {$table_chitiet_kynang} WHERE id_nhanvien IN( {$ids} )" );
            }elseif( $id_nhanvien != '' ){
                $wpdb->update( $table_nhanvien, array( 'display_status'=>'hidden' ), array( 'id_nhanvien' => $id_nhanvien ), array( '%d' ) );
                $wpdb->query( "DELETE FROM {$table_chitiet_kynang} WHERE id_nhanvien = {$id_nhanvien}" );
            } 
           
            
        }
    }
    function prepare_items(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nhanvien';
        $table_kynang = $wpdb->prefix . 'kynang';
        
        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->process_bulk_action();
        $total_items = $wpdb->get_var( "SELECT COUNT(id_nhanvien) FROM {$table_name} WHERE display_status = 'show'" );

        
        $paged   = isset( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : 1;
        $offset  = ( $paged - 1 ) * $per_page; //tinh toan so ban ghi se bi bo qua
        
        $orderby = (isset( $_REQUEST['orderby'] ) && in_array($_REQUEST['orderby'], array_keys( $this->get_sortable_columns())) ) ? $_REQUEST['orderby'] : 'id_nhanvien';
        $order   = (isset( $_REQUEST['order'] ) && in_array($_REQUEST['order'], array( 'asc', 'desc' ))) ? $_REQUEST['order'] : 'asc';
 
        
        $this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE display_status = %s ORDER BY $orderby $order LIMIT %d, %d", 'show', $offset, $per_page ), ARRAY_A );
        
        foreach( $this->items as $k=>$v ){
            $all_skill_each_staff = self::tt_get_all_kynang_by_id_nhanvien( $v['id_nhanvien'] );
            $array_name_skill = array();
            foreach( $all_skill_each_staff as $key=> $value ){
                $array_name_skill[] = $wpdb->get_var( $wpdb->prepare( "SELECT tenkynang FROM {$table_kynang} WHERE id_kynang = %d AND display_status = %s", $value, 'show' ) );
            }
            $this->items[$k]['cac_kynang'] = implode( ', ', $array_name_skill );
        }
        
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page'    => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
    
    
    public function tt_page_nhanvien_callback(){
        global $wpdb;
        $table = new TT_Nhanvien();
        
        $table->prepare_items();
        
        $message = '';
        if ('delete' === $table->current_action()) {
            $message = '<div class="updated below-h2" id="message"><p>' . sprintf( __( 'Số bản ghi đã xóa: %d', 'custom_table_example'), count( $_REQUEST['id_duan']) ) . '</p></div>';
        }
?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br/></div>
        <h2><?php _e( 'Danh sách nhân viên', 'simple_plugin' )?> <a class="add-new-h2" href="<?php echo get_admin_url( get_current_blog_id(), 'admin.php?page=new_nhanvien');?>"><?php _e( 'Thêm mới nhân viên', 'simple_plugin' )?></a></h2>
        <?php echo $message; ?>
        <form id="nhanvien-table" method="GET">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <?php $table->display(); ?>
        </form>
    </div>
<?php     
    }//End function tt_nhanvien_page_callback
    
    public static function tt_get_all_kynang_by_id_nhanvien( $id_nhanvien ){
        global $wpdb;
        $table_kynang = $wpdb->prefix . 'kynang';
        $table_detail_kynang = $wpdb->prefix . 'chitiet_kynang';
        
        $all_kynang_by_id_nhanvien = $wpdb->get_results( $wpdb->prepare( "SELECT id_kynang, id_nhanvien FROM {$table_detail_kynang} WHERE id_nhanvien = %d", $id_nhanvien ), ARRAY_A );
        $all_deleted_kynang        = $wpdb->get_results( "SELECT id_kynang FROM {$table_kynang} WHERE display_status = 'hidden'", ARRAY_A );
        $all_deleted_kynang_fomated = array();
        
        foreach( $all_deleted_kynang as $key=>$value ){
            $all_deleted_kynang_fomated[] = $value['id_kynang'];
        }
        
        
        foreach( $all_deleted_kynang_fomated as $k=>$v ){
            foreach( $all_kynang_by_id_nhanvien as $key=>$value ){
                if( $value['id_kynang'] == $v ){
                    unset( $all_kynang_by_id_nhanvien[$key] );
                    $wpdb->delete( $table_detail_kynang, array( 'id_kynang' => $v ), array( '%d' ) );
                }
            }
        }
        
        $return = array();
        foreach( $all_kynang_by_id_nhanvien as $k=>$v ){
            $return[] = $v['id_kynang'];
        }
        
        return $return;
    }
    
    public function tt_new_nhanvien_callback(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nhanvien'; 
        $table_kynang = $wpdb->prefix . 'kynang';
        $table_chitiet_kynang = $wpdb->prefix . 'chitiet_kynang';
        
        $message = '';
        $notice = '';
        
        $default = array(
            'id_nhanvien'       => 0,
            'hoten'             => '',
            'namsinh'           => '',
            'gioitinh'          => '',
            'quequan'           => '',
            'avatar'            => '',
            'display_status'    => 'show'
        );
        
        if( wp_verify_nonce( $_REQUEST['nonce'], basename( __FILE__) )){
            $item = shortcode_atts($default, $_REQUEST);
            $item_valid = self::tt_validate_data_nhanvien( $item );
            
            $seleted_id_kynang = $_POST['cac_kynang'];
            
            if( $item_valid == true ){
                if( $item['id_nhanvien'] == 0 ){ //Thêm mới dl
                    $result = $wpdb->insert( $table_name, $item );
                    $item['id_nhanvien'] = $wpdb->insert_id;
                    
                    if( $result ){
                        //Thêm dữ liệu vào bảng chitiet_kynang
                        if( is_array( $seleted_id_kynang ) && !empty( $seleted_id_kynang ) ){
                            foreach( $seleted_id_kynang as $key=>$value ){
                                $data_insert = array(
                                    'id_kynang'     => $value,
                                    'id_nhanvien'   => $item['id_nhanvien']
                                );
                                $wpdb->insert( $table_chitiet_kynang, $data_insert );
                            }
                        }
                        $message = __( "Đã thêm dữ liệu thành công", "simple_plugin" );
                    }else{
                        $notice  = __( "Xảy ra lỗi trong quá trình thêm dữ liệu, xin vui lòng thử lại", "simple_plugin" );
                    }
                    
                }else{ //Cập nhật dl
                    $id_nhanvien = $item['id_nhanvien'];
                    //loi xuat hien o day
                    $result = $wpdb->update( $wpdb->prefix. 'nhanvien', $item, array( 'id_nhanvien' => $id_nhanvien ) );
                    if( is_array( $seleted_id_kynang ) && !empty( $seleted_id_kynang ) ){
                        $wpdb->delete( $table_chitiet_kynang, array( 'id_nhanvien' => $id_nhanvien ), array( '%d' ) );
                        foreach( $seleted_id_kynang as $key=>$value ){
                            $data_insert = array(
                                'id_kynang'     => $value,
                                'id_nhanvien'   => $id_nhanvien
                            );
                            $result = $wpdb->insert( $table_chitiet_kynang, $data_insert );
                        }
                    }
                    //end loi
                    if( $result ){
                        $message = __( "Cập nhật dữ liệu thành công", "simple_plugin" );
                    }else{
                        $notice  = __( "Xảy ra lỗi, vui lòng thử lại", "simple_plugin" ); 
                    }
                }
            }else{
                $notice = $item_valid;
            }
            
        }else{
            $item = $default;
            if( isset( $_REQUEST['id_nhanvien'] )){
                $item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id_nhanvien = %d AND display_status = %s", $_REQUEST['id_nhanvien'], 'show' ), ARRAY_A );
                if( empty( $item ) ){
                    $item   = $default;
                    $notice = __( "Không tìm thấy dữ liệu ", "simple_plugin" );
                }
            }
        }
        
        
        $list_kynang = $wpdb->get_results( $wpdb->prepare( "SELECT id_kynang, tenkynang FROM {$table_kynang} WHERE display_status = %s", 'show' ), ARRAY_A );
        $selected_kynang = self::tt_get_all_kynang_by_id_nhanvien( $item['id_nhanvien'] );
        
        /** END GET Duan Info, KyNang Info  **/
        ?>
        <div class="wrap">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
            <?php $title = (!empty( $item['hoten'] )) ? __( "Cập nhật thông tin", "simple_plugin" ) : __( "Thêm mới", "simple_plugin" ) ?>
            <h2><?php _e( "{$title} nhân viên", 'simple_plugin')?> <a class="add-new-h2" href="<?php echo get_admin_url( get_current_blog_id(), 'admin.php?page=ds_nhanvien');?>"><?php _e( 'Danh sách nhân viên', 'simple_plugin' ); ?></a></h2>
            <?php if ( !empty( $notice ) ){ ?>
                <div id="notice" class="error"><p><?php echo $notice ?></p></div>
            <?php }// !empty( $notice ) ?>
            <?php if ( !empty($message) ){ ?>
                <div id="message" class="updated"><p><?php echo $message ?></p></div>
            <?php }//!empty( $message ) ?>
            <form id="form" method="POST">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
                <input type="hidden" name="id_nhanvien" value="<?php echo $item['id_nhanvien'] ?>"/>
                <div class="metabox-holder" id="poststuff">
                    <div id="post-body">
                        <div id="post-body-content">
                            <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
                                <tbody>
                                    <?php if( !empty( $item[ 'avatar' ] ) ){  ?>
                                    <tr class="tt_avatar">
                                        <td colspan="2">
                                            <img src="<?php echo esc_url(  $item[ 'avatar' ] ); ?>" alt="avatar" />
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                        
                                            <label for="hoten"><?php _e( 'Họ tên', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <input id="hoten" name="hoten" type="text" style="width: 95%" value="<?php if( !empty( $item['hoten'] ) ) echo esc_attr( $item['hoten'] );?>" class="code"  required />
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="namsinh"><?php _e( 'Năm sinh', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <select id="namsinh" name="namsinh" class="code" style="width: 15%;">
                                                <?php 
                                                    $selected_year = 1990;
                                                    for( $i = 1930; $i <= date('Y'); $i++ ){ 
                                                ?>
                                                    <option value="<?php echo $i; ?>" <?php if( empty( $item['namsinh'] ) ){ if( $i == $selected_year ){ echo 'selected="selected"'; } }else{ TT_Teamwork::tt_selected( $item['namsinh'], $i ); } ?>><?php echo $i; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="gioitinh"><?php _e( 'Giới tính', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <input type="radio" name="gioitinh" id="Nam" value="Nam" <?php if( empty( $item['gioitinh'] ) ){ echo 'checked="checked"'; }else{  TT_Teamwork::tt_checked( $item['gioitinh'], "Nam" ); } ?> />Nam
                                            <input type="radio" name="gioitinh" id="Nữ" value="Nữ" <?php if( !empty( $item['gioitinh']) ){ TT_Teamwork::tt_checked( $item['gioitinh'], "Nữ" ); } ?>/>Nữ
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="quequan"><?php _e( 'Quê quán', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <input id="quequan" name="quequan" type="text" style="width: 95%" value="<?php if( !empty( $item['quequan'] ) ) echo esc_attr( $item['quequan'] );?>" class="code"  required />
                                        </td>
                                    </tr>
                                    
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="cac_kynang"><?php _e( 'Các kỹ năng', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <select id="chon_cac_kynang" name="cac_kynang[]" class="code" data-placeholder="Chọn kỹ năng" multiple style="width:95%;" tabindex="4">
                                                <?php foreach( $list_kynang as $key=>$value ){ ?>    
                                                    <option value="<?php  echo $value['id_kynang']; ?>" <?php if( isset( $selected_kynang ) && !empty( $selected_kynang ) && in_array( $value['id_kynang'], $selected_kynang ) ){ echo 'selected="selected"'; } ?> ><?php echo $value['tenkynang']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="avatar"><?php _e( 'Avatar', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <input id="avatar" type="text" name="avatar" class="code" style="width:84%;" <?php if( !empty( $item[ 'avatar' ] ) ){ echo 'value="'. esc_url( $item[ 'avatar' ] ) .'"'; } ?> />
                                            <input id="upload-button" type="button" class="button" value="Chọn Avatar" />
                                        </td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                            <input type="submit" value="<?php _e( 'Gửi', 'simple_plugin' ); ?>" id="submit" class="button-primary" name="submit"/>
                        </div>
                    </div>
                </div>
            </form>
        </div>
<?php        
    }//End function tt_new_nhanvien_page_callback()
    
    
    public static function tt_validate_data_nhanvien( $item ){
        $messages = array();
        if ( empty( $item['hoten'] ) ){ 
            $messages[] = __( 'Vui lòng nhập vào họ tên', 'simple_plugin' );
        }
        
        if ( empty( $item['namsinh'] ) ){ 
            $messages[] = __( 'Vui chọn năm sinh', 'simple_plugin' );
        }
        
        if ( empty( $item['gioitinh'] ) ){ 
            $messages[] = __( 'Vui lòng chọn giới tính', 'simple_plugin' );
        }
        
        if( empty( $item['quequan'] ) ){
            $messages[] = __( "Vui lòng nhập vào quê quán", "simple_plugin" );
        }
        
        if ( empty($messages ) ){
            return true; 
        }else{
            return implode( '<br />', $messages );
        }
        
    }
    
    public static function render_list_checkbox_nhanvien( $display_status = 'show', $checkbox_name = "id_nhanvien_thamgia[]", $id_checked = array() ){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nhanvien';
        
        if( $display_status == 'show' ){
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE display_status = %s", $display_status ), ARRAY_A );
        }else if( $display_status == 'hidden' ){
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE display_status = %s", $display_status ), ARRAY_A );
        }else if( $display_status == 'all' ){    
            $result = $wpdb->get_results( "SELECT * FROM {$table_name}", ARRAY_A );
        }
        ob_start();
        if( !empty( $result ) ){
            
            foreach( $result as $key => $value ){
                $checked = '';
                if( !empty( $id_checked ) ){
                    if( in_array( $value['id_nhanvien'], $id_checked ) ){
                        $checked = 'checked="checked"';
                    }
                }
                printf( '<input class="id_nhanvien_thamgia" type="checkbox" value="%d" name="%s" data-hoten="%s" %s /> %s </br>', $value['id_nhanvien'], $checkbox_name, $value['hoten'], $checked, $value['hoten'] );
            }
        }else{
            printf( '<input class="id_nhanvien_thamgia" type="checkbox" value="%d" name="%s"/> %s </br>', 0, $checkbox_name, __( "Vui lòng thêm dữ liệu nhân viên trước khi chọn", "simple_plugin" ) );
        }
        
        return ob_get_clean();
    }
    
    public static function render_select_option_nhanvien( $nhanvien_display_status = "show", $id_selected_value = null ){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nhanvien';
        
        if( $nhanvien_display_status == 'show' ){
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE display_status = %s", $nhanvien_display_status ), ARRAY_A );
        }else if( $nhanvien_display_status == 'hidden' ){
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE display_status = %s", $nhanvien_display_status ), ARRAY_A );
        }else if( $nhanvien_display_status == 'all' ){    
            $result = $wpdb->get_results( "SELECT * FROM {$table_name}", ARRAY_A );
        }
        
        ob_start();
        if( !empty( $result ) ){
            foreach( $result as $key => $value ){
               if( $id_selected_value ){
                    if( $id_selected_value == $value['id_nhanvien'] ){
                        $selected = 'selected="selected"';
                    }else{
                        $selected = '';
                    }
               }else{
                    $selected = '';
               } 
               printf( '<option value="%d" %s>%s</option>', $value['id_nhanvien'], $selected, $value['hoten'] );
            }
        }else{
            printf( '<option value="%d">%s</option>', 0, __( "Vui lòng thêm dữ liệu về nhân viên trước khi chọn.", "simple_plugin" ) );
        }
        
        return ob_get_clean();
        
    } 
    
    public static function get_nhanvien_name_by_id( $id_nhanvien ){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nhanvien';
        
        $nhanvien_name = $wpdb->get_var( $wpdb->prepare( "SELECT hoten FROM {$table_name} WHERE display_status = %s AND id_nhanvien = %d", 'show', $id_nhanvien ) );
        return $nhanvien_name;
    }
    
    public static function tt_get_deleted_nhanvien(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nhanvien';
        
        $deleted_nhanvien = $wpdb->get_results( $wpdb->prepare( "SELECT id_nhanvien FROM {$table_name} WHERE display_status = %s", 'hidden' ), ARRAY_A );
        $formated = array();
        
        if( !empty( $deleted_nhanvien ) ){
            foreach( $deleted_nhanvien as $key=>$value ){
                $formated[] = $value['id_nhanvien'];
            }
        }
        
        return $formated;
    }
    
    public static function tt_get_default_nhanvien_info( $args = array() ){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nhanvien';
        $table_chitiet_kynang = $wpdb->prefix . 'chitiet_kynang';
        $table_chitiet_duan   = $wpdb->prefix . 'chitiet_duan';
        
        if( empty( $args ) ){ //không có tham số
            $results =  $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE display_status = %s ORDER BY namsinh ASC", 'show' ), ARRAY_A );
            if( !empty( $results ) ){  ?>
                <div class="row header green">
                  <div class="cell">Họ Tên</div>
                  <div class="cell">Năm Sinh</div>
                  <div class="cell">Giới Tính</div>
                  <div class="cell">Quê Quán</div>
                </div>
            <?php foreach( $results as $key=> $value ){ ?>
                <div class="row">
                  <div class="cell"><?php if( !empty( $value['hoten'] ) ){ echo esc_html( $value['hoten'] ) ;} ?></div>
                  <div class="cell"><?php if( !empty( $value['namsinh'] ) ){ echo esc_html( $value['namsinh'] ) ;} ?></div>
                  <div class="cell"><?php if( !empty( $value['gioitinh'] ) ){ echo esc_html( $value['gioitinh'] ) ;} ?></div>
                  <div class="cell"><?php if( !empty( $value['quequan'] ) ){ echo esc_html( $value['quequan'] ) ;} ?></div>
                </div>
            <?php } ?> 
        <?php }else{ ?>
                <div class="row">
                  <div class="cell">Không có dữ liệu</div>
                  <div class="cell">...</div>
                  <div class="cell">...</div>
                  <div class="cell">...</div>
                </div>
<?php        } //end else
        }else{ //có tham số
            if( !empty( $args['skill'] ) ){
                $get_id_nhanvien_by_skill_id =  $wpdb->get_results( $wpdb->prepare( "SELECT id_nhanvien FROM {$table_chitiet_kynang} WHERE id_kynang = %d", $args['skill'] ), ARRAY_A );
                $list_nhanvien = array();
                
                if( !empty( $get_id_nhanvien_by_skill_id ) ){
                    foreach( $get_id_nhanvien_by_skill_id as $k=>$v ){
                        $results =  $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE display_status = %s AND id_nhanvien = %d", 'show', $v['id_nhanvien'] ), ARRAY_A );
                        $list_nhanvien[] = $results;
                    }
                }
                /** ========================== **/
                echo '
                    <div class="row header green">
                          <div class="cell">Họ Tên</div>
                          <div class="cell">Năm Sinh</div>
                          <div class="cell">Giới Tính</div>
                          <div class="cell">Quê Quán</div>
                    </div>
                ';
                if( !empty( $list_nhanvien ) ){  ?>
                    <?php foreach( $list_nhanvien as $key=> $value ){ ?>
                    <div class="row">
                      <div class="cell"><?php if( !empty( $value['hoten'] ) ){ echo esc_html( $value['hoten'] ) ;} ?></div>
                      <div class="cell"><?php if( !empty( $value['namsinh'] ) ){ echo esc_html( $value['namsinh'] ) ;} ?></div>
                      <div class="cell"><?php if( !empty( $value['gioitinh'] ) ){ echo esc_html( $value['gioitinh'] ) ;} ?></div>
                      <div class="cell"><?php if( !empty( $value['quequan'] ) ){ echo esc_html( $value['quequan'] ) ;} ?></div>
                    </div>
                    <?php }//end foreach ?> 
        <?php   }else{ ?>
                <div class="row">
                  <div class="cell">Không có dữ liệu</div>
                  <div class="cell">...</div>
                  <div class="cell">...</div>
                  <div class="cell">...</div>
                </div>
<?php           }  
                /** ========================== **/
            }//if $skill
            
            if( !empty( $args['project'] ) ){
                $get_id_nhanvien_by_project_id =  $wpdb->get_results( $wpdb->prepare( "SELECT id_nhanvien FROM {$table_chitiet_duan} WHERE id_duan = %d", $args['project'] ), ARRAY_A );
                $list_nhanvien = array();
                
                if( !empty( $get_id_nhanvien_by_project_id ) ){
                    foreach( $get_id_nhanvien_by_project_id as $k=>$v ){
                        $results =  $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE display_status = %s AND id_nhanvien = %d", 'show', $v['id_nhanvien'] ), ARRAY_A );
                        $list_nhanvien[] = $results;
                    }
                }    
                /** ========================== **/
                echo '
                  <div class="row header green">
                      <div class="cell">Họ Tên</div>
                      <div class="cell">Năm Sinh</div>
                      <div class="cell">Giới Tính</div>
                      <div class="cell">Quê Quán</div>
                  </div> ';
                if( !empty( $list_nhanvien ) ){  ?>
                    <?php foreach( $list_nhanvien as $key=> $value ){ ?>
                    <div class="row">
                      <div class="cell"><?php if( !empty( $value['hoten'] ) ){ echo esc_html( $value['hoten'] ) ;} ?></div>
                      <div class="cell"><?php if( !empty( $value['namsinh'] ) ){ echo esc_html( $value['namsinh'] ) ;} ?></div>
                      <div class="cell"><?php if( !empty( $value['gioitinh'] ) ){ echo esc_html( $value['gioitinh'] ) ;} ?></div>
                      <div class="cell"><?php if( !empty( $value['quequan'] ) ){ echo esc_html( $value['quequan'] ) ;} ?></div>
                    </div>
                    <?php }//end foreach ?> 
        <?php   }else{ ?>
                <div class="row">
                  <div class="cell">Không có dữ liệu</div>
                  <div class="cell">...</div>
                  <div class="cell">...</div>
                  <div class="cell">...</div>
                </div>
<?php           }  
                /** ========================== **/
            }//if $project
            
        }
        
        
    }//end function
    
    public function tt_ajax_load_filter_nhanvien_callback(){
        $type = 'done';
        check_ajax_referer( 'tt_ajax_form', 'security' );
        
        $args = array();
        if( !empty( $_POST['post_nhanvien_skill'] ) ){
            $args['skill'] = $_POST['post_nhanvien_skill'];
        }
        
        if( !empty( $_POST['post_nhanvien_project'] ) ){
            $args['project'] = $_POST['post_nhanvien_project'];
        }
        ob_start();
        self::tt_get_default_nhanvien_info( $args );
        $results = ob_get_clean();
        
        $result = array( 
            'type' => $type, 
            'data' => $results 
        );
        die( wp_send_json( $result ) );
        
    }
    
    
}

new TT_Nhanvien();