<?php
class TT_Duan extends WP_List_Table{
    public function __construct(){
       
       global $status, $page;
        parent::__construct(
            array(
                'singular' => "mot_duan",
                'plural'   => "nhieu_duan" 
            )
        );
        
        
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
    }
    
    function column_default( $item, $column_name ){
        return $item[ $column_name ];
    }
    
    function column_id_duan( $item ){
        return '<em>' . $item['id_duan'] . '</em>';
    }
    
    function column_trangthai( $item ){
        return '<em>' . $item['trangthai'] . '</em>'; // trang thai: da hoan thanh, dang thuc hien, chua hoan thanh, da huy
    }
    
    function column_thoigianbatdau( $item ){
        return '<em>' . $item['thoigianbatdau'] . '</em>';
    }
    
    function column_thoigianketthuc( $item ){
        return '<em>' . $item['thoigianketthuc'] . '</em>';
    }
    
    function column_ghichu( $item ){
        return '<em>' . $item['ghichu'] . '</em>';
    }

    function column_tenduan( $item ){
        $actions = array(
            'edit'   => sprintf( '<a href="?page=new_duan&id_duan=%s">%s</a>', $item['id_duan'], __( 'Sửa dữ liệu', 'simple_plugin' ) ),
            'delete' => sprintf( '<a href="?page=%s&action=delete&id_duan=%s">%s</a>', $_REQUEST['page'], $item['id_duan'], __( 'Xóa dữ liệu', 'simple_plugin' ) ),
        );
        return sprintf('%s %s', $item['tenduan'], $this->row_actions( $actions ) );
    }

    function column_cb( $item ){
        return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $item['id_duan'] );
    }

    function get_columns()
    {
        $columns = array(
            'cb'                => '<input type="checkbox" />', //Tạo nut checkbox
            'id_duan'           => __( 'ID', 'simple_plugin' ),
            'tenduan'           => __( 'Tên dự án', 'simple_plugin' ),
            'thoigianbatdau'    => __( 'Thời gian bắt đầu', 'simple_plugin' ),
            'thoigianketthuc'   => __( 'Thời gian kết thúc', 'simple_plugin' ),
            'trangthai'         => __( 'Trạng thái', 'simple_plugin' ),    
            'ghichu'            => __( 'Ghi chú', 'simple_plugin' ),
        );
        return $columns;
    }

    function get_sortable_columns(){
        $sortable_columns = array(
            'id_duan'           => array( 'id_duan', true ),
            'tenduan'           => array( 'tenduan', false ),
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
        $table_duan         = $wpdb->prefix . 'duan'; 
        $table_chitiet_duan = $wpdb->prefix . 'chitiet_duan';

        if ( 'delete' === $this->current_action() && $_REQUEST['page'] == 'ds_duan' ) {
            //$ids     = isset( $_REQUEST['id_duan'] ) ? $_REQUEST['id_duan'] : array();
            $ids     = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : array();
            $id_duan = isset( $_REQUEST['id_duan'] ) ? $_REQUEST['id_duan'] : '';
            
            if( is_array( $ids ) && !empty( $ids )) {
                $ids = implode( ',', $ids );
                $wpdb->query( "DELETE FROM {$table_duan} WHERE id_duan IN( {$ids} )" );
                $wpdb->query( "DELETE FROM {$table_chitiet_duan} WHERE id_duan IN( {$ids} )" );
            }elseif( $id_duan != '' ){
                $wpdb->query( "DELETE FROM {$table_duan} WHERE id_duan = {$id_duan}" );
                $wpdb->query( "DELETE FROM {$table_chitiet_duan} WHERE id_duan = {$id_duan}" );
            }  
            
        }
    }
    function prepare_items(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'duan';
        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        $this->process_bulk_action();
        $total_items = $wpdb->get_var( "SELECT COUNT(id_duan) FROM {$table_name}" );

        $paged   = isset( $_REQUEST['paged'] ) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $offset  = $paged * $per_page;
        
        $orderby = (isset( $_REQUEST['orderby'] ) && in_array($_REQUEST['orderby'], array_keys( $this->get_sortable_columns())) ) ? $_REQUEST['orderby'] : 'id_duan';
        $order   = (isset( $_REQUEST['order'] ) && in_array($_REQUEST['order'], array( 'asc', 'desc' ))) ? $_REQUEST['order'] : 'asc';
 
        $this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $offset ), ARRAY_A );

        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page'    => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
    
    public function enqueue_script(){
        wp_enqueue_script( 'jquery_ui', TT_DIR_URL . '/asset/js/jquery-ui.min.js', array('jquery') );
        wp_enqueue_script( 'jquery_ui', TT_DIR_URL . '/asset/js/function.js', array('jquery') );
    }
    
    public function tt_duan_page_callback(){
        global $wpdb;
        $table = new TT_Duan();
        $table->prepare_items();
        $message = '';
        if ('delete' === $table->current_action()) {
            $message = '<div class="updated below-h2" id="message"><p>' . sprintf( __( 'Số bản ghi đã xóa: %d', 'custom_table_example'), count( $_REQUEST['id_duan']) ) . '</p></div>';
        }
?>
    <div class="wrap">
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h2><?php _e( 'Danh sách dự án', 'simple_plugin' )?> <a class="add-new-h2" href="<?php echo get_admin_url( get_current_blog_id(), 'admin.php?page=new_duan');?>"><?php _e( 'Thêm mới dự án', 'simple_plugin' )?></a></h2>
        <?php echo $message; ?>
        <form id="duan-table" method="GET">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <?php $table->display(); ?>
        </form>
    </div>
<?php     
    }//End function tt_duan_page_callback
    
    
    public function tt_new_duan_page_callback(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'duan'; 
        $message = '';
        $notice = '';
    
        $default = array(
            'id_duan'           => 0,
            'tenduan'           => '',
            'thoigianbatdau'    => '',
            'thoigianketthuc'   => '',
            'trangthai'         => '',
            'ghichu'            => ''
        );
        if ( wp_verify_nonce( $_REQUEST['nonce'], basename(__FILE__)) ) {
            $item = shortcode_atts( $default, $_REQUEST );
            
            $item_valid = self::tt_validate_data_duan( $item );
            if ( $item_valid === true ) {
                if ( $item['id_duan'] == 0 ) {
                    $result = $wpdb->insert( $table_name, $item );
                    $item['id_duan'] = $wpdb->insert_id;
                    if ( $result ) {
                        $message = __( 'Thêm dữ liệu thành công', 'simple_plugin' );
                    } else {
                        $notice = __( 'Xảy ra lỗi trong quá trình thêm dữ liệu', 'simple_plugin' );
                    }
                } else {
                    $result = $wpdb->update( $table_name, $item, array( 'id_duan' => $item['id_duan']) );
                    if ( $result ) {
                        $message = __( 'Cập nhật dữ liêu thành công', 'simple_plugin' );
                    } else {
                        $notice = __( 'Xảy ra lỗi trong quá trình cập nhật dữ liệu', 'simple_plugin' );
                    }
                }
            } else {
                $notice = $item_valid;
            }
        }
        else {
            $item = $default;
            if ( isset( $_REQUEST['id_duan'] ) ) {
                $item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id_duan = %d", $_REQUEST['id_duan']), ARRAY_A );
                if ( !$item ) {
                    $item = $default;
                    $notice = __( 'Không tìm thấy dữ liệu', 'simple_plugin' );
                }
            }
        }
        ?>
        <div class="wrap">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
            <h2><?php _e( 'Thêm mới dự án', 'simple_plugin')?> <a class="add-new-h2" href="<?php echo get_admin_url( get_current_blog_id(), 'admin.php?page=ds_duan');?>"><?php _e( 'Danh sách dự án', 'simple_plugin' ); ?></a></h2>
            <?php if ( !empty( $notice ) ){ ?>
                <div id="notice" class="error"><p><?php echo $notice ?></p></div>
            <?php }// !empty( $notice ) ?>
            <?php if ( !empty($message) ){ ?>
                <div id="message" class="updated"><p><?php echo $message ?></p></div>
            <?php }//!empty( $message ) ?>
            <form id="form" method="POST">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
                <input type="hidden" name="id_duan" value="<?php echo $item['id_duan'] ?>"/>
                <div class="metabox-holder" id="poststuff">
                    <div id="post-body">
                        <div id="post-body-content">
                            <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
                                <tbody>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="tenduan"><?php _e( 'Tên dự án', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <input id="tenduan" name="tenduan" type="text" style="width: 95%" value="<?php if( !empty( $item['tenduan'] ) ) echo esc_attr( $item['tenduan'] );?>" class="code"  required />
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="thoigianbatdau"><?php _e( 'Thời gian bắt đầu', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <input id="thoigianbatdau" name="thoigianbatdau" type="text" style="width: 95%" value="<?php if( !empty( $item['thoigianbatdau'] ) ) echo esc_attr( $item['thoigianbatdau'] );?>" class="code"  required />
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="thoigianketthuc"><?php _e( 'Thời gian kết thúc', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <input id="thoigianketthuc" name="thoigianketthuc" type="text" style="width: 95%" value="<?php if( !empty( $item['thoigianketthuc'] ) ) echo esc_attr( $item['thoigianketthuc'] );?>" class="code"  required />
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="trangthai"><?php _e( 'Trạng thái', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <select id="trangthai" name="trangthai" class="code">
                                                <option value="Đã hoàn thành" <?php if( !empty( $item['trangthai']) ){ TT_Teamwork::tt_selected( $item['trangthai'], 'Đã hoàn thành' ); }?> >Đã hoàn thành</option>
                                                <option value="Đang triển khai" <?php if( empty( $item['trangthai'] ) ){ echo 'selected="selected"'; }else{ TT_Teamwork::tt_selected( $item['trangthai'], 'Đang triển khai' ); } ?> >Đang triển khai</option>
                                                <option value="Chưa hoàn thành" <?php if( !empty( $item['trangthai']) ){ TT_Teamwork::tt_selected( $item['trangthai'], 'Chưa hoàn thành' ); } ?> >Chưa hoàn thành</option>
                                                <option value="Đã hủy" <?php if( !empty( $item['trangthai']) ){ TT_Teamwork::tt_selected( $item['trangthai'], 'Đã hủy' ); } ?> >Đã hủy</option>
                                            </select>
                                        </td>
                                    </tr> 
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="ghichu"><?php _e( 'Ghi chú', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <textarea id="ghichu" name="ghichu" class="code" style="width: 95%" ><?php if( !empty( $item['ghichu'] ) ) echo esc_attr( $item['ghichu'] );?></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <input type="submit" value="<?php _e( 'Gửi', 'simple_plugin' ); ?>" id="submit" class="button-primary" name="submit">
                        </div>
                    </div>
                </div>
            </form>
        </div>
<?php        
    }//End function tt_new_duan_page_callback()
    
    
    public static function tt_validate_data_duan( $item ){
        $messages = array();
         
        if ( empty( $item['tenduan'] ) ){ 
            $messages[] = __( 'Vui lòng nhập vào tên dự án', 'simple_plugin' );
        }
        
        if ( empty( $item['thoigianbatdau'] ) ){ 
            $messages[] = __( 'Vui chọn thời gian bắt đầu cho dự án', 'simple_plugin' );
        }
        
        if ( empty( $item['thoigianketthuc'] ) ){ 
            $messages[] = __( 'Vui lòng chọn thời gian kết thúc dự án', 'simple_plugin' );
        }
        
        if ( empty($messages ) ){
            return true; 
        }else{
            return implode( '<br />', $messages );
        }
        
    }
    
    
}//End class TT_Duan
