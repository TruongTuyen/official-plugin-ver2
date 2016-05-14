<?php
class TT_Doitac extends WP_List_Table{
    public function __construct(){
        global $status, $page, $wpdb;
        parent::__construct(
            array(
                'singular' => "mot_doitac",
                'plural'   => "nhieu_doitac" 
            )
        );
        
    }
    
    function column_default( $item, $column_name ){
        return $item[ $column_name ];
    }
    
    function column_id_doitac( $item ){
        return '<em>' . $item['id_doitac'] . '</em>';
    }
    
    function column_loai( $item ){
        return '<em>' . $item['loai'] . '</em>';
    }
    
    function column_mota( $item ){
        return '<em>' . $item['mota'] . '</em>';
    }
    
    function column_hoten_tendonvi( $item ){
        $actions = array(
            'edit'   => sprintf( '<a href="?page=new_doitac&id_doitac=%s">%s</a>', $item['id_doitac'], __( 'Sửa dữ liệu', 'simple_plugin' ) ),
            'delete' => sprintf( '<a href="?page=%s&action=delete&id_doitac=%s">%s</a>', $_REQUEST['page'], $item['id_doitac'], __( 'Xóa dữ liệu', 'simple_plugin' ) ),
        );
        return sprintf('%s %s', $item['hoten_tendonvi'], $this->row_actions( $actions ) );
    }

    function column_cb( $item ){
        return sprintf( '<input type="checkbox" name="id_doitac[]" value="%s" />', $item['id_doitac'] );
    }

    function get_columns()
    {
        $columns = array(
            'cb'                  => '<input type="checkbox" />', //Tạo nut checkbox
            'id_doitac'           => __( 'ID', 'simple_plugin' ),
            'hoten_tendonvi'      => __( 'Họ Tên / Tên đơn vị', 'simple_plugin' ),
            'loai'                => __( 'Loại', 'simple_plugin' ),    
            'mota'                => __( 'Mô tả', 'simple_plugin' ),
        );
        return $columns;
    }

    function get_sortable_columns(){
        $sortable_columns = array(
            'id_doitac'           => array( 'id_doitac', true ),
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => __( 'Xóa dữ liệu', 'simple_plugin' )
        );
        return $actions;
    }

    function process_bulk_action(){
        global $wpdb;
        $table_doitac         = $wpdb->prefix . 'doitac'; 

        if ( 'delete' === $this->current_action() && $_REQUEST['page'] == 'ds_doitac' ) {
            $id_doitac = isset( $_REQUEST['id_doitac'] ) ? $_REQUEST['id_doitac'] : array();
            if( is_array( $id_doitac ) && !empty( $id_doitac )) {
                $data_update = array( 'display_status' => 'hidden' );
                 foreach( $id_doitac as $key=>$value ){
                    $where_clause = array( 'id_doitac' => $value );
                    $wpdb->update( $table_doitac, $data_update, $where_clause );
                 }
                //$wpdb->query( "DELETE FROM {$table_duan} WHERE id_duan IN( {$ids} )" );
            }else if( !empty( $id_doitac ) & is_numeric( $id_doitac ) ){
                $wpdb->update( $table_doitac, array( 'display_status' => 'hidden' ), array( 'id_doitac' => $id_doitac ) );
            }
        }
    }
    function prepare_items(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'doitac';
        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->process_bulk_action();
        $total_items = $wpdb->get_var( "SELECT COUNT(id_doitac) FROM {$table_name} WHERE display_status = 'show'" );

        $paged   = isset( $_REQUEST['paged'] ) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $offset  = $paged * $per_page;
        
        $orderby = (isset( $_REQUEST['orderby'] ) && in_array($_REQUEST['orderby'], array_keys( $this->get_sortable_columns())) ) ? $_REQUEST['orderby'] : 'id_doitac';
        $order   = (isset( $_REQUEST['order'] ) && in_array($_REQUEST['order'], array( 'asc', 'desc' ))) ? $_REQUEST['order'] : 'asc';
 
        $this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE display_status = %s ORDER BY $orderby $order LIMIT %d OFFSET %d", 'show' ,$per_page, $offset ), ARRAY_A );

        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page'    => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
        
        
    public function tt_new_doitac_callback(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'doitac'; 
        $message = '';
        $notice = '';
        
        $default = array(
            'id_doitac'         => 0,
            'hoten_tendonvi'    => '',
            'loai'              => '',
            'mota'              => '',
            'display_status'    => 'show'
        );
        if( wp_verify_nonce( $_REQUEST['nonce'], basename( __FILE__) )){
            $item = shortcode_atts($default, $_REQUEST);
            
            $item_valid = self::tt_validate_data_doitac( $item );
            
            if( $item_valid == true ){
                if( $item['id_doitac'] == 0 ){ //Thêm mới dl
                    $result = $wpdb->insert( $table_name, $item );
                    $item['id_doitac'] = $wpdb->insert_id;
                    
                    if( $result ){
                        $message = __( "Đã thêm dữ liệu thành công", "simple_plugin" );
                    }else{
                        $notice  = __( "Xảy ra lỗi trong quá trình thêm dữ liệu, xin vui lòng thử lại", "simple_plugin" );
                    }
                    
                }else{ //Cập nhật dl
                    $result = $wpdb->update( $table_name, $item, array( "id_doitac" => $item['id_doitac']) );
                    if( $result ){
                        $message = __( "Cập nhật dữ liệu thành công", "simple_plugin" );
                    }else{
                        $notice  = __(); 
                    }
                }
            }else{
                $notice = $item_valid;
            }
            
        }else{
            $item = $default;
            if( isset( $_REQUEST['id_doitac'] )){
                $item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id_doitac = %d AND display_status = %s ", $_REQUEST['id_doitac'], 'show' ), ARRAY_A );
                if( empty( $item ) ){
                    $item   = $default;
                    $notice = __( "Không tìm thấy dữ liệu ", "simple_plugin" );
                }
            }
        }
        
       
        ?>
        <div class="wrap">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
            <?php $title = ( !empty( $item['hoten_tendonvi'] ) ) ? __( "Cập nhật thông tin", "simple_plugin" ) : __( "Thêm mới", "simple_plugin" ); ?>
            <h2><?php _e( "{$title} đối tác", 'simple_plugin')?> <a class="add-new-h2" href="<?php echo get_admin_url( get_current_blog_id(), 'admin.php?page=ds_doitac');?>"><?php _e( 'Danh sách đối tác', 'simple_plugin' ); ?></a></h2>
            <?php if ( !empty( $notice ) ){ ?>
                <div id="notice" class="error"><p><?php echo $notice ?></p></div>
            <?php }// !empty( $notice ) ?>
            <?php if ( !empty($message) ){ ?>
                <div id="message" class="updated"><p><?php echo $message ?></p></div>
            <?php }//!empty( $message ) ?>
            <form id="form" method="POST">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
                <input type="hidden" name="id_doitac" value="<?php echo $item['id_doitac'] ?>"/>
                <div class="metabox-holder" id="poststuff">
                    <div id="post-body">
                        <div id="post-body-content">
                            <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
                                <tbody>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="loai"><?php _e( 'Loại', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <select name="loai" id="loai_doitac">
                                                <option value="cá nhân" <?php if( isset( $item['loai'] ) && !empty( $item['loai'] ) && $item['loai'] === 'cá nhân'  ){ echo 'selected="selected"'; } ?>>Cá nhân</option>
                                                <option value="doanh nghiệp" <?php if( isset( $item['loai'] ) && !empty( $item['loai'] ) && $item['loai'] === 'doanh nghiệp'  ){ echo 'selected="selected"'; } ?> >Doanh nghiệp</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="hoten"><?php _e( 'Họ tên/Tên đơn vị', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <input type="text" name="hoten_tendonvi" value="<?php if( !empty( $item['hoten_tendonvi'])){ echo esc_html( $item['hoten_tendonvi'] ); } ?>" class="code" style="width: 95%" />
                                        </td>
                                    </tr>
                                    
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="mota"><?php _e( 'Ghi chú', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <textarea class="" style="width: 95%;" name="mota"><?php if( !empty( $item['mota'] ) ){ echo esc_html( $item['mota'] ); } ?></textarea>
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
    }//end function tt_new_doitac_callback()
    
    public function tt_doitac_page_callback(){
        global $wpdb;
        $table = new TT_Doitac();
        $table->prepare_items();
        $message = '';
        if ('delete' === $table->current_action()) {
            $message = '<div class="updated below-h2" id="message"><p>' . sprintf( __( 'Số bản ghi đã xóa: %d', 'custom_table_example'), count( $_REQUEST['id_doitac']) ) . '</p></div>';
        }
?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br/></div>
        <h2><?php _e( 'Danh sách đối tác', 'simple_plugin' )?> <a class="add-new-h2" href="<?php echo get_admin_url( get_current_blog_id(), 'admin.php?page=new_doitac');?>"><?php _e( 'Thêm mới đối tác', 'simple_plugin' )?></a></h2>
        <?php echo $message; ?>
        <form id="duan-table" method="GET">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <?php $table->display(); ?>
        </form>
    </div>
<?php     
    }
    
    public static function danhsach_doitac( $display_status = "show" ){ //show or hidden or all for default
        global $wpdb;
        $table_name = $wpdb->prefix . 'doitac';
        if( $display_status == 'show' ){
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE display_status = %s", $display_status ), ARRAY_A );
        }else if( $display_status == 'hidden' ){
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE display_status = %s", $display_status ), ARRAY_A );
        }else if( $display_status == 'all' ){    
            $result = $wpdb->get_results( "SELECT * FROM {$table_name}", ARRAY_A );
        }
        return $result;
    }
    
    public static function render_select_option_doitac( $doitac_display_status = "show", $id_selected_value = null ){
        if( $doitac_display_status == "show" ){
            $list_doitac = self::danhsach_doitac( "show" );
        }else if( $doitac_display_status == "hidden"){
            $list_doitac = self::danhsach_doitac( "hidden" );
        }else if( $doitac_display_status == "all"){
            $list_doitac = self::danhsach_doitac( "all" );
        }
        ob_start();
        if( !empty( $list_doitac ) ){
            foreach( $list_doitac as $key => $value ){
                if( $id_selected_value ){
                    if( $value['id_doitac'] == $id_selected_value ){
                        $selected = 'selected="selected"';
                    }else{
                        $selected = '';
                    }
                }else{
                    $selected = '';
                }
                
                printf( '<option value="%d" %s>%s</option>', $value['id_doitac'],$selected, $value['hoten_tendonvi'] );
                
            }
        }else{
            printf( '<option value="%d">%s</option>', 0, __( "Vui lòng thêm dữ liệu về đối tác trước khi chọn.", "simple_plugin" ) );
        }
        
        return ob_get_clean();
        
    } 
    
    
    public static function tt_validate_data_doitac( $item ){
        $messages = array();
         
        if ( empty( $item['loai'] ) ){ 
            $messages[] = __( 'Vui lòng chọn loại', 'simple_plugin' );
        }
        
        if ( empty($messages ) ){
            return true; 
        }else{
            return implode( '<br />', $messages );
        }
    }
    
    public static function get_doitac_name_by_id( $id_doitac ){
        global $wpdb;
        $table_name = $wpdb->prefix . 'doitac';
        $doitac_name = $wpdb->get_var( $wpdb->prepare("SELECT hoten_tendonvi FROM {$table_name} WHERE display_status = %s AND id_doitac = %d", 'show', $id_doitac) );
        return $doitac_name;
    }
    
    
}
new TT_Doitac();