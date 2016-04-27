<?php
class TT_KyNang extends WP_List_Table{
    function __construct(){
        global $status, $page;
        parent::__construct(
            array(
                'singular' => "mot_kynang",
                'plural'   => "nhieu_kynang" 
            )
        );
    }
    
    public function column_default( $item, $colum_name ){
        return $item[ $colum_name ];
    }
    
    public function colum_id_kynang( $item ){
        return '<em>' . $item['id_kynang'] . '</em>';
    }
    
    public function colum_chuthich( $item ){
        return '<em>' . $item['chuthich'] . '</em>';
    }
    
    public function column_tenkynang( $item ){
        $actions = array(
            'edit'   => sprintf( '<a href="?page=new_kynang&id_kynang=%s">%s</a>', $item['id_kynang'], __( 'Sửa dữ liệu', 'simple_plugin' ) ),
            'delete' => sprintf( '<a href="?page=%s&action=delete&id_kynang=%s">%s</a>', $_REQUEST['page'], $item['id_kynang'], __( 'Xóa dữ liệu', 'simple_plugin' ) )
        );
        return sprintf( "%s %s", $item['tenkynang'], $this->row_actions( $actions ) );
    }
    
    public function column_cb( $item ){
        return sprintf( '<input type="checkbox" name="id_kynang[]" value="%s" />', $item['id_kynang'] );
    }
    
    public function get_columns(){
        return array(
            'cb'        => '<input type="checkbox" />',
            'id_kynang' => __( "ID", "simple_plugin" ),
            'tenkynang' => __( "Tên Kỹ Năng", "simple_plugin" ),
            'chuthich'  => __( "Chú Thích", "simple_plugin" )
        );
    }
    
    public function get_sortable_columns(){
        return array(
            'id_kynang' => array( 'id_kynang', true ),
            'tenkynang' => array( 'tenkynang', false ),
            'chuthich'  => array( 'chuthich', false )
            
        );
    }
    
    public function get_bulk_actions(){
        return array(
            'delete' => 'Xóa dữ liệu'
        );
    }
    
    public function process_bulk_action(){
        global $wpdb;
        $table_kynang = $wpdb->prefix . 'kynang';
        $table_chitet_kynang = $wpdb->prefix . 'chitiet_kynang';
        
        if( $this->current_action() === 'delete' && $_REQUEST['page'] == 'ds_ky_nang' ){
            $ids = isset( $_REQUEST['id_kynang'] ) ? $_REQUEST['id_kynang'] : array();
            $id_kynang = isset( $_REQUEST['id_kynang'] ) ? $_REQUEST['id_kynang'] : '';
            
            if( is_array( $ids ) && !empty( $ids ) ){
                $ids = implode( ',', $ids );
                $wpdb->query( "DELETE FROM $table_kynang WHERE id_kynang IN($ids)" );
                $wpdb->query( "DELETE FROM $table_chitet_kynang WHERE id_kynang IN($ids)" );
            }
            
            if( $id_kynang != '' ){
                $wpdb->query( "DELETE FROM $table_kynang WHERE id_kynang = {$id_kynang}" );
                $wpdb->query( "DELETE FROM $table_chitet_kynang WHERE id_kynang = {$id_kynang}" );
            }
            
            
        }
    }
    
    
    public function prepare_items(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'kynang';
        
        $per_page = 5;
        $colums   = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array( $colums, $hidden, $sortable );
        $this->process_bulk_action();
        
        $total_items = $wpdb->get_var( "SELECT COUNT(id_kynang) FROM $table_name" );//lấy tổng số bản ghi để có thể tính toán phân trang
        
        //Tính toán các tham số cần thiết
        $paged   = isset( $_REQUEST['paged'] ) ? max( 0, intval( $_REQUEST['paged']) - 1 ) : 0;
        $offset  = $paged * $per_page;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id_kynang';
        $order   = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';
    
        $this->items = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $offset
        ), ARRAY_A );
        
        $this->set_pagination_args(
            array(
                'total_items' => $total_items, 
                'per_page'    => $per_page, 
                'total_pages' => ceil( $total_items / $per_page ) 
            )
        );
        
        
    }
    
    public function tt_kynang_page_callback(){ //Hàm xử lý page list tất cả các kỹ năng
        global $wpdb;
        $kynang = new TT_KyNang();
        $kynang->prepare_items();
        $message = '';
        
        if( $kynang->current_action() == 'delete' ){
            $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Số bản ghi đã xóa: %d', 'simple_plugin'), count( $_REQUEST['id_kynang']) ) . '</p></div>';
        }
    ?>    
        <div class="wrap">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br/></div>
            <h2><?php _e( 'Danh sách Kỹ Năng', 'simple_plugin' ); ?> 
                <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=new_kynang');?>"><?php _e( 'Thêm mới kỹ năng', 'simple_plugin' ); ?></a>
            </h2>
            <?php echo $message; ?>
        
            <form id="persons-table" method="GET">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
                <?php $kynang->display(); ?>
            </form>
        
        </div>
    <?php
    }//end tt_kynang_page_callback()
    
    public function tt_new_kynang_callback(){ //Hàm xử lý thêm mới một kỹ năng hoặc update kỹ năng đã có
        global $wpdb;
        $table_name = $wpdb->prefix . 'kynang';
        
        $message = '';
        $notice  = '';
        
        $default = array(
            'id_kynang' => 0,
            'tenkynang' => '',
            'chuthich'  => ''
        );
        
        if( wp_verify_nonce( $_REQUEST['nonce'], basename( __FILE__) )){
            $item = shortcode_atts($default, $_REQUEST);
            $item_valid = self::tt_kynang_validate_data( $item );
            
            if( $item_valid == true ){
                if( $item['id_kynang'] == 0 ){ //Thêm mới dl
                    $result = $wpdb->insert( $table_name, $item );
                    $item['id_kynang'] = $wpdb->insert_id;
                    
                    if( $result ){
                        $message = __( "Đã thêm dữ liệu thành công", "simple_plugin" );
                    }else{
                        $notice  = __( "Xảy ra lỗi trong quá trình thêm dữ liệu, xin vui lòng thử lại", "simple_plugin" );
                    }
                    
                }else{ //Cập nhật dl
                    $result = $wpdb->update( $table_name, $item, array( "id_kynang" => $item['id_kynang']) );
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
            if( isset( $_REQUEST['id_kynang'] )){
                $item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id_kynang = %d", $_REQUEST['id_kynang'] ), ARRAY_A );
                if( empty( $item ) ){
                    $item   = $default;
                    $notice = __( "Không tìm thấy dữ liệu ", "simple_plugin" );
                }
            }
        }
        ?>
        <div class="wrap">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br /></div>
            <h2><?php _e( "Thêm mới kỹ năng", "simple_plugin" ); ?>
                <a class="add-new-h2" href="<?php echo get_admin_url( get_current_blog_id(), 'admin.php?page=ds_ky_nang' ); ?>"><?php _e( "Danh sách kỹ năng", "simple_plugin" ); ?></a>
            </h2>
            <?php if( !empty( $notice ) ) { ?>
                <div id="notice" class="error"><p><?php echo $notice; ?></p></div>
            <?php } ?>
            
            <?php if( !empty( $message ) ){ ?>
                <div id="message" class="updated"><p><?php echo $message; ?></p></div>
            <?php } ?>
            
            <form id="form" method="post">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce( basename(__FILE__) );?>" />
                <input type="hidden" name="id_kynang" value="<?php echo $item['id_kynang'] ?>" />
                <div class="metabox-holder" id="poststuff">
                    <div id="post-body"> 
                        <div id="post-body-content">
                            <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
                                <tbody>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="tenkynang"><?php _e( "Tên kỹ năng", "simple_plugin" ); ?></label>
                                        </th>
                                        <td>
                                            <input name="tenkynang" id="tenkynang" type="text" style="width:95%;" value="<?php if( !empty( $item['tenkynang'] ) ){ echo esc_attr( $item['tenkynang'] ); }?>" class="code" required />
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="chuthich"><?php _e( "Chú thích", "simple_plugin" ); ?></label>
                                        </th>
                                        <td>
                                            <textarea name="chuthich" id="chuthich" style="width:95%;padding:0;"><?php if( !empty( $item['chuthich'] ) ){ echo trim( esc_attr( $item['chuthich'] ) ); } ?></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <input type="submit" value="<?php _e( "Gửi", "simple_plugin" ); ?>" id="submit" class="button-primary" name="submit" />
                        </div>
                    </div>
                </div>
            
            </form>
            
        </div>
<?php        
        
    }
    
    public static function tt_kynang_validate_data( $item ){
        $message = array();
        if( empty( $item['tenkynang'] ) ){
            $message[] = __( "Vui lòng nhập vào tên kỹ năng.", "simple_plugin" );
        } 
        if( empty( $message ) ){ 
            return true; 
        }else{
            return implode( "<br/>", $message );
        }
        
    }
    
}
 new TT_KyNang();