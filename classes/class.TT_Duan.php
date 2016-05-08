<?php
class TT_Duan extends WP_List_Table{
    public static $message;
    public function __construct(){
       
       global $status, $page;
        parent::__construct(
            array(
                'singular' => "mot_duan",
                'plural'   => "nhieu_duan" 
            )
        );
        
        add_action( 'init', array( $this, 'tt_ob_start' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
        add_action( 'wp_ajax_tt_ajax_load_form_them_congviec_in_hangmuc', array( $this, 'tt_ajax_load_form_them_congviec_in_hangmuc_callback' ) );
        add_action( 'wp_ajax_nopriv_tt_ajax_load_form_them_congviec_in_hangmuc', array( $this, 'tt_ajax_load_form_them_congviec_in_hangmuc_callback' ) );
        
        add_action( 'wp_ajax_tt_ajax_load_form_them_hangmuc', array( $this, 'tt_ajax_load_form_them_hangmuc_callback' ) );
        add_action( 'wp_ajax_nopriv_tt_ajax_load_form_them_hangmuc', array( $this, 'tt_ajax_load_form_them_hangmuc_callback' ) );
        
        add_action( 'wp_ajax_tt_ajax_delete_congviec_in_hangmuc', array( $this, 'tt_ajax_delete_congviec_callback' ) );
        add_action( 'wp_ajax_nopriv_tt_ajax_delete_congviec_in_hangmuc', array( $this, 'tt_ajax_delete_congviec_callback' ) );
        
        add_action( 'wp_ajax_tt_ajax_delete_hangmuc_in_duan', array( $this, 'tt_ajax_delete_hangmuc_callback' ) );
        add_action( 'wp_ajax_nopriv_tt_ajax_delete_hangmuc_in_duan', array( $this, 'tt_ajax_delete_hangmuc_callback' ) );
        
        add_action( "wp_ajax_tt_ajax_filter_info_duan", array( $this, "tt_ajax_load_filter_duan_callback" ) );
        add_action( "wp_ajax_nopriv_tt_ajax_filter_info_duan", array( $this, "tt_ajax_load_filter_duan_callback" ) );
    }
    
    function tt_ob_start(){
        ob_start();
    }
    
    function column_default( $item, $column_name ){
        return $item[ $column_name ];
    }
    
    function column_id_duan( $item ){
        return '<em>' . $item['id_duan'] . '</em>';
    }
    
    function column_tendoitac( $item ){
        return '<em>' . $item['tendoitac'] . '</em>';
    }
    
    function column_qlduan( $item ){
        return '<em>' . $item['qlduan'] . '</em>';
    }
    
    function column_tinhtrangduan( $item ){
        return '<em>' . $item['tinhtrangduan'] . '</em>'; // trang thai: da hoan thanh, dang thuc hien, chua hoan thanh, da huy
    }
    
    function column_ngaybatdau( $item ){
        return '<em>' . $item['ngaybatdau'] . '</em>';
    }
    
    function column_ngayketthuc( $item ){
        return '<em>' . $item['ngayketthuc'] . '</em>';
    }
    
    function column_mota( $item ){
        return '<em>' . $item['mota'] . '</em>';
    }

    function column_tenduan( $item ){
        $actions = array(
            'edit'   => sprintf( '<a href="?page=new_duan&action=edit&id_duan=%s">%s</a>', $item['id_duan'], __( 'Sửa dữ liệu', 'simple_plugin' ) ),
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
            'tendoitac'         => __( 'Đối tác', 'simple_plugin' ),
            'qlduan'            => __( 'Quản lý dự án', 'simple_plugin' ),
            
            'ngaybatdau'        => __( 'Ngày bắt đầu', 'simple_plugin' ),
            'ngayketthuc'       => __( 'Ngày kết thúc', 'simple_plugin' ),
            'tinhtrangduan'     => __( 'Trạng thái', 'simple_plugin' ),    
            'mota'              => __( 'Ghi chú', 'simple_plugin' ),
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
        $table_hangmuc      = $wpdb->prefix . 'hangmuc';

        if ( 'delete' === $this->current_action() && $_REQUEST['page'] == 'ds_duan' ) {
            //$ids     = isset( $_REQUEST['id_duan'] ) ? $_REQUEST['id_duan'] : array();
            $ids     = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : array();
            $id_duan = isset( $_REQUEST['id_duan'] ) ? $_REQUEST['id_duan'] : '';
            
            if( is_array( $ids ) && !empty( $ids )) {
                //$ids = implode( ',', $ids );
                //$wpdb->query( "DELETE FROM {$table_duan} WHERE id_duan IN( {$ids} )" );
                //$wpdb->query( "DELETE FROM {$table_chitiet_duan} WHERE id_duan IN( {$ids} )" );
            }elseif( $id_duan != '' ){
                //$wpdb->query( "DELETE FROM {$table_duan} WHERE id_duan = {$id_duan}" );
                
                $find_id_hangmuc_in_duan = $wpdb->get_results( $wpdb->prepare( "SELECT id_hangmuc FROM {$table_hangmuc} WHERE id_duan = %d AND display_status = %s", $id_duan, 'show' ), ARRAY_A );
                
                if( !empty( $find_id_hangmuc_in_duan ) ){ //yêu cầu xóa hết các hạng mục trước khi xóa dự án
                    //echo '<script type="text/javascript">alert("Trước khi xóa dự án này, vui lòng xóa hết các hạng mục có trong dự án!");</script>';
                    self::$message = '<div class="error below-h2" id="message"><p>Trước khi xóa dự án này, vui lòng xóa hết các hạng mục có trong dự án!</p></div>';
                }else{ //Được phép xóa
                    $wpdb->query( "DELETE FROM {$table_chitiet_duan} WHERE id_duan = {$id_duan}" );
                    $result_update = $wpdb->update( $table_duan, array( "display_status" => "hidden" ),array( "id_duan" => $id_duan ) );
                    
                    if( $result_update ){
                        //echo '<script type="text/javascript">alert("Xóa dự án thành công!");</script>';
                        self::$message = '<div class="updated below-h2" id="message"><p>Xóa dự án thành công!</p></div>';
                    }else{
                        //echo '<script type="text/javascript">alert("Lỗi! Không thể xóa dự án. Vui lòng thử lại");</script>';
                        self::$message = '<div class="error below-h2" id="message"><p>Lỗi! Không thể xóa dự án. Vui lòng thử lại</p></div>';
                    }
                }
                //if( )
                //
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
        $total_items = $wpdb->get_var( "SELECT COUNT(id_duan) FROM {$table_name} WHERE display_status = 'show'" );

        $paged   = isset( $_REQUEST['paged'] ) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $offset  = $paged * $per_page;
        
        $orderby = (isset( $_REQUEST['orderby'] ) && in_array($_REQUEST['orderby'], array_keys( $this->get_sortable_columns())) ) ? $_REQUEST['orderby'] : 'id_duan';
        $order   = (isset( $_REQUEST['order'] ) && in_array($_REQUEST['order'], array( 'asc', 'desc' ))) ? $_REQUEST['order'] : 'asc';
 
        $this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE display_status = %s ORDER BY $orderby $order LIMIT %d OFFSET %d",'show', $per_page, $offset ), ARRAY_A );
        
        if( !empty( $this->items ) ){
            foreach( $this->items as $k=>$v ){
                $tendoitac = TT_Doitac::get_doitac_name_by_id( $v['id_doitac'] );
                $tennhanvien = TT_Nhanvien::get_nhanvien_name_by_id( $v['id_quanly_duan'] );
                $this->items[$k]['tendoitac'] = $tendoitac;
                $this->items[$k]['qlduan']    = $tennhanvien;
            }
        }
       
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page'    => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
    
    public function enqueue_script(){
        wp_register_script( 'jquery_arcodion', TT_DIR_URL . 'assets/js/js_arcodion.js', array('jquery') );
    }
    
    public function tt_duan_page_callback(){
        global $wpdb;
        $table = new TT_Duan();
        $table->prepare_items();
        $message = self::$message;
        
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
        $table_name         = $wpdb->prefix . 'duan'; 
        $table_hangmuc      = $wpdb->prefix . 'hangmuc';
        $table_chitiet_duan = $wpdb->prefix . 'chitiet_duan';
        $table_congviec     = $wpdb->prefix . 'congviec';
        $table_nhanvien     = $wpdb->prefix . 'nhanvien';
        
        
        $message = '';
        $notice = '';
    
        $default = array(
            'id_duan'           => 0,
            'id_doitac'         => '',
            'id_quanly_duan'    => '',
            'tenduan'           => '',
            'ngaybatdau'        => '',
            'ngayketthuc'       => '',
            'tinhtrangduan'     => '',
            'mota'              => '',
            'display_status'    => 'show'
        );
        
        $id_nhanvien_tg_duan = $_POST['id_nhanvien_thamgia'];
        $hangmuc_duan        = $_POST['hangmuc'];
        
        if ( wp_verify_nonce( $_REQUEST['nonce'], basename(__FILE__)) ) {
            $item = shortcode_atts( $default, $_REQUEST );
            
            $item_valid = self::tt_validate_data_duan( $item );
            if ( $item_valid === true ) {
                if ( $item['id_duan'] == 0 ) {
                    $result = $wpdb->insert( $table_name, $item );
                    $item['id_duan'] = $wpdb->insert_id;
                    
                    //Thêm dư liệu vào chi tiet du an
                    if( !empty( $id_nhanvien_tg_duan ) ){
                        foreach( $id_nhanvien_tg_duan as $key => $value ){
                            $data = array(
                                'id_duan'       => $item['id_duan'],
                                'id_nhanvien'   => $value
                            );
                            $result = $wpdb->insert( $table_chitiet_duan, $data );
                        }
                    }
                    //Thêm dữ liệu vào bảng hạng mục
                    if( !empty( $hangmuc_duan ) ){
                        echo "<pre>";
                        print_r( $hangmuc_duan );
                        echo "</pre>";
                        foreach( $hangmuc_duan as $key=>$value ){
                            $data_insert_to_hangmuc = array(
                                'id_hangmuc'            => 0,
                                'id_duan'               => $item['id_duan'],
                                'ten_hangmuc'           => $value['tenhangmuc'],
                                'noi_dung'              => $value['noidung_hangmuc'],
                                'ngaybatdau'            => $value['thoigianbatdau'],
                                'ngayketthuc'           => $value['thoigianketthuc'],
                                'display_status'        => 'show',
                                'phantram_hoanthanh'    => $value['phantram_hoanthanh_hangmuc'],
                                'trangthai_hangmuc'     => $value['trangthai_hoanthanh']
                            );
                            $result = $wpdb->insert( $table_hangmuc, $data_insert_to_hangmuc );
                            $id_hangmuc = $wpdb->insert_id;
                            
                            //Thêm dữ liệu vào bảng công việc
                            if( isset( $value['congviec'] ) && !empty( $value['congviec'] ) ){
                                echo "<pre>";
                                print_r( $value['congviec'] );
                                echo "</pre>";
                                foreach( $value['congviec'] as $k=>$v ){
                                    
                                    $data_insert_to_congviec = array(
                                        'id_congviec'       => 0,
                                        'id_hangmuc'        => $id_hangmuc,
                                        'ten_congviec'      => $v['tencongviec'],
                                        'nhanvien_thamgia'  => implode( ',', $v['nhanvien_thamgia'] ),
                                        'noidung_congviec'  => $v['noidungcongviec'],
                                        'ngaybatdau'        => $v['tg_batdau'],
                                        'ngayketthuc'       => $v['tg_ketthuc'],
                                        'display_status'    => 'show',
                                        'trangthai_congviec'=> $v['trangthai_hoanthanh']
                                    );
                                    $result = $wpdb->insert( $table_congviec, $data_insert_to_congviec );
                                }
                            }
                            
                            
                        }
                    }
                    
                    
                    
                    if ( $result ) {
                        $message = __( 'Thêm dữ liệu thành công', 'simple_plugin' );
                    } else {
                        $notice = __( 'Xảy ra lỗi trong quá trình thêm dữ liệu', 'simple_plugin' );
                    }
                    $redirec = true;
                } else {
                    //$result = $wpdb->update( $table_name, $item, array( 'id_duan' => $item['id_duan']) );
                    $post_thongtin_duan = $_POST;
                 
                    //Update dữ liệu bảng duan
                    $result = $wpdb->update( $table_name, $item, array( 'id_duan' => $post_thongtin_duan['id_duan'] ) );
                    
                    //Update du lieu bang chitiet_duan
                    if( !empty( $post_thongtin_duan['id_nhanvien_thamgia'] ) ){
                        $id_nhanvien_tg_duan = implode( ',', $post_thongtin_duan );
                        $wpdb->query( "DELETE FROM {$table_chitiet_duan} WHERE id_duan = {$post_thongtin_duan['id_duan']}" );
                        foreach( $post_thongtin_duan['id_nhanvien_thamgia'] as $key=>$value ){
                            $result = $wpdb->insert( $table_chitiet_duan, array( "id_duan"=> $post_thongtin_duan['id_duan'], "id_nhanvien" => $value ) );
                        }
                    }
                    //Update du lieu bang hang muc
                    if( !empty( $post_thongtin_duan['hangmuc'] ) ){
                        foreach( $post_thongtin_duan['hangmuc'] as $key=>$value ){
                            //=========================>
                            if( isset( $value['id_hangmuc'] ) && !empty( $value['id_hangmuc'] ) && ($value['id_hangmuc'] != 0) ){ //Update dữ liệu đã có
                                $each_id_hangmuc = $value['id_hangmuc'];
                                $data_insert_to_hangmuc = array(
                                    'id_hangmuc'            => $each_id_hangmuc,
                                    'id_duan'               => $item['id_duan'],
                                    'ten_hangmuc'           => $value['tenhangmuc'],
                                    'noi_dung'              => $value['noidung_hangmuc'],
                                    'ngaybatdau'            => $value['thoigianbatdau'],
                                    'ngayketthuc'           => $value['thoigianketthuc'],
                                    'display_status'        => 'show',
                                    'phantram_hoanthanh'    => $value['phantram_hoanthanh_hangmuc'],
                                    'trangthai_hangmuc'     => $value['trangthai_hoanthanh']
                                );
                                $result = $wpdb->update( $table_hangmuc, $data_insert_to_hangmuc, array( 'id_hangmuc' => $value['id_hangmuc'] ) );
                                if( isset( $value['congviec'] ) && !empty( $value['congviec'] ) && ($value['congviec'] !=0)  ){
                                    foreach( $value['congviec'] as $k=>$v ){
                                        if( isset( $v['id_congviec'] ) ){ //update cong viêc dựa vào id có sẵn
                                            $data_insert_to_congviec = array(
                                                'id_congviec'       => $v['id_congviec'],
                                                'id_hangmuc'        => $each_id_hangmuc,
                                                'ten_congviec'      => $v['tencongviec'],
                                                'nhanvien_thamgia'  => implode( ',', $v['nhanvien_thamgia'] ),
                                                'noidung_congviec'  => $v['noidungcongviec'],
                                                'ngaybatdau'        => $v['tg_batdau'],
                                                'ngayketthuc'       => $v['tg_ketthuc'],
                                                'display_status'    => 'show',
                                                'trangthai_congviec'=> $v['trangthai_hoanthanh']
                                            );
                                            $result = $wpdb->update( $table_congviec, $data_insert_to_congviec, array( "id_congviec" => $v['id_congviec'] ) );
                                        }else{ //Thêm mới công việc
                                            $data_insert_to_congviec = array(
                                                'id_congviec'       => 0,
                                                'id_hangmuc'        => $each_id_hangmuc,
                                                'ten_congviec'      => $v['tencongviec'],
                                                'nhanvien_thamgia'  => implode( ',', $v['nhanvien_thamgia'] ),
                                                'noidung_congviec'  => $v['noidungcongviec'],
                                                'ngaybatdau'        => $v['tg_batdau'],
                                                'ngayketthuc'       => $v['tg_ketthuc'],
                                                'display_status'    => 'show',
                                                'trangthai_congviec'=> $v['trangthai_hoanthanh']
                                            );
                                            $result = $wpdb->insert( $table_congviec, $data_insert_to_congviec );
                                        } 
                                    }//end foreach
                                }//end if        
                                
                            }else{//Thêm mới dữ liệu
                                $data_insert_to_hangmuc = array(
                                    'id_hangmuc'            => 0,
                                    'id_duan'               => $item['id_duan'],
                                    'ten_hangmuc'           => $value['tenhangmuc'],
                                    'noi_dung'              => $value['noidung_hangmuc'],
                                    'ngaybatdau'            => $value['thoigianbatdau'],
                                    'ngayketthuc'           => $value['thoigianketthuc'],
                                    'display_status'        => 'show',
                                    'phantram_hoanthanh'    => $value['phantram_hoanthanh_hangmuc'],
                                    'trangthai_hangmuc'     => $value['trangthai_hoanthanh']
                                );
                                $result = $wpdb->insert( $table_hangmuc, $data_insert_to_hangmuc );
                                $id_hangmuc = $wpdb->insert_id;
                                
                                //Thêm dữ liệu vào bảng công việc
                                if( isset( $value['congviec'] ) && !empty( $value['congviec'] ) ){
                                    foreach( $value['congviec'] as $k=>$v ){
                                        
                                        $data_insert_to_congviec = array(
                                            'id_congviec'       => 0,
                                            'id_hangmuc'        => $id_hangmuc,
                                            'ten_congviec'      => $v['tencongviec'],
                                            'nhanvien_thamgia'  => implode( ',', $v['nhanvien_thamgia'] ),
                                            'noidung_congviec'  => $v['noidungcongviec'],
                                            'ngaybatdau'        => $v['tg_batdau'],
                                            'ngayketthuc'       => $v['tg_ketthuc'],
                                            'display_status'    => 'show',
                                            'trangthai_congviec'=> $v['trangthai_hoanthanh']
                                        );
                                        $result = $wpdb->insert( $table_congviec, $data_insert_to_congviec );
                                    }
                                }
                            }
                            
                            //=========================>
                        }
                    }
                    
                    //die();
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
        
            <?php
                
                
                if( isset( $_GET['action'] ) && $_GET['action'] === 'edit' && isset( $_GET['id_duan'] ) && is_numeric( $_GET['id_duan'] ) ){
                    $nv_tg_duan_from_db = $wpdb->get_results( $wpdb->prepare( "SELECT id_nhanvien FROM {$table_chitiet_duan} WHERE id_duan = %d", $_GET['id_duan'] ), ARRAY_A );
                    $deleted_nhanvien   = $wpdb->get_results( $wpdb->prepare( "SELECT id_nhanvien FROM {$table_nhanvien} WHERE display_status = %s", 'hidden' ), ARRAY_A );
                    
                    $formated_nv_tg_duan_from_db = array();
                    $formated_deleted_nhanvien   = array();
                    
                    if( is_array( $nv_tg_duan_from_db ) ){
                        foreach( $nv_tg_duan_from_db as $k=>$v ){
                            $formated_nv_tg_duan_from_db[] = $v['id_nhanvien'];
                        } 
                    }
                    
                    if( is_array( $deleted_nhanvien ) ){
                        foreach( $deleted_nhanvien as $k=>$v ){
                            $formated_deleted_nhanvien[] = $v['id_nhanvien'];
                        }
                        
                        foreach( $formated_nv_tg_duan_from_db as $k=>$v ){
                            if( in_array( $v, $formated_deleted_nhanvien ) ){
                                unset( $formated_nv_tg_duan_from_db[$k] );
                            }
                        }
                    }
                    
                    $item['id_nhanvien_thamgia'] = $formated_nv_tg_duan_from_db;
                    
                }
                
                
            ?>
        
            <div class="icon32 icon32-posts-post" id="icon-edit"><br/></div>
            <div id="ajax_box_notice">
                
            </div>
            <h2><?php if( $item['id_duan'] != 0 ){ _e( 'Sửa dự án', 'simple_plugin'); }else{ _e( 'Thêm mới dự án', 'simple_plugin'); } ?> <a class="add-new-h2" href="<?php echo get_admin_url( get_current_blog_id(), 'admin.php?page=ds_duan');?>"><?php _e( 'Danh sách dự án', 'simple_plugin' ); ?></a></h2>
            <?php if ( !empty( $notice ) ){ ?>
                <div id="notice" class="error"><p><?php echo $notice ?></p></div>
            <?php }// !empty( $notice ) ?>
            <?php if ( !empty($message) ){ ?>
                <div id="message" class="updated"><p><?php echo $message ?></p></div>
                <?php 
                    if( $redirec ){
                        $location = get_admin_url( get_current_blog_id(), 'admin.php?page=ds_duan');
                        wp_safe_redirect( $location );
                        exit; 
                    }
                    
                ?>
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
                                            <label for="id_doitac"><?php _e( 'Đối tác', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <select name="id_doitac" style="width: 50%;">
                                                <?php echo TT_Doitac::render_select_option_doitac( "show", $item['id_doitac'] ); ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="ngaybatdau"><?php _e( 'Thời gian bắt đầu', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <input id="thoigianbatdau" name="ngaybatdau" type="text" style="width: 95%" value="<?php if( !empty( $item['ngaybatdau'] ) ) echo esc_attr( $item['ngaybatdau'] );?>" class="code"  required />
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="ngayketthuc"><?php _e( 'Thời gian kết thúc', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <input id="thoigianketthuc" name="ngayketthuc" type="text" style="width: 95%" value="<?php if( !empty( $item['ngayketthuc'] ) ) echo esc_attr( $item['ngayketthuc'] );?>" class="code"  required />
                                        </td>
                                    </tr>
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="tinhtrangduan"><?php _e( 'Trạng thái', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <select id="trangthai" name="tinhtrangduan" class="code">
                                                <option value="Đã hoàn thành" <?php if( !empty( $item['tinhtrangduan']) ){ TT_Teamwork::tt_selected( $item['tinhtrangduan'], 'Đã hoàn thành' ); }?> >Đã hoàn thành</option>
                                                <option value="Đang triển khai" <?php if( empty( $item['tinhtrangduan'] ) ){ echo 'selected="selected"'; }else{ TT_Teamwork::tt_selected( $item['tinhtrangduan'], 'Đang triển khai' ); } ?> >Đang triển khai</option>
                                                <option value="Chưa hoàn thành" <?php if( !empty( $item['tinhtrangduan']) ){ TT_Teamwork::tt_selected( $item['tinhtrangduan'], 'Chưa hoàn thành' ); } ?> >Chưa hoàn thành</option>
                                                <option value="Đã hủy" <?php if( !empty( $item['tinhtrangduan']) ){ TT_Teamwork::tt_selected( $item['tinhtrangduan'], 'Đã hủy' ); } ?> >Đã hủy</option>
                                            </select>
                                        </td>
                                    </tr> 
                                    <tr class="form-field">
                                        <th valign="top" scope="row">
                                            <label for="mota"><?php _e( 'Mô tả', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <textarea id="ghichu" name="mota" class="code" style="width: 95%" ><?php if( !empty( $item['mota'] ) ) echo esc_attr( $item['mota'] );?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th valign="top" scope="row">
                                            <label for="id_quanly_duan"><?php _e( 'Quản lý dự án', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <select name="id_quanly_duan" id="id_quanly_duan">
                                                <?php echo TT_Nhanvien::render_select_option_nhanvien( 'show', $item['id_quanly_duan'] ); ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th valign="top" scope="row">
                                            <label for="thanhvien_thamgia"><?php _e( 'Thành viên tham gia', 'simple_plugin' ); ?></label>
                                        </th>
                                        <td>
                                            <div id="nhanvien_thamgia_duan">
                                                <?php echo TT_Nhanvien::render_list_checkbox_nhanvien( 'show', "id_nhanvien_thamgia[]", $item['id_nhanvien_thamgia'] ); ?>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                </div>
                
                <div class="metabox-holder" id="poststuff">
                    <div id="post-body-add-hangmuc">
                        <?php $post_stt = 0; ?>
                        <?php if( isset( $_GET['action'] ) && $_GET['action'] === 'edit' && isset( $_GET['id_duan'] ) && is_numeric( $_GET['id_duan'] ) ){ ?>
                                <?php
                                    $get_id_duan = $_GET['id_duan'];
                                    $thongtin_hangmuc = self::tt_get_tt_hangmuc_by_id_duan( $get_id_duan );
                                    
                                    if( !empty( $thongtin_hangmuc ) ){
                                        $post_stt = count( $thongtin_hangmuc );
                                    }
                                ?>
                        <?php } ?>        
                        <div id="post-body-content">
                            <h2>Hạng mục <a class="add-new-h2" href="#" id="them-moi-hangmuc-btn" data-stt="<?php echo $post_stt; ?>"><?php _e( 'Thêm mới hạng mục', 'simple_plugin' ); ?></a></h2>
                        </div>
                        <div id="hangmuc_add_new">
                            <?php if( isset( $_GET['action'] ) && $_GET['action'] === 'edit' && isset( $_GET['id_duan'] ) && is_numeric( $_GET['id_duan'] ) ){ ?>
                                <?php
                                    
                                    if( !empty( $thongtin_hangmuc ) ){
                                        foreach( $thongtin_hangmuc as $key=> $value ){ ?>
                                            <div class="hangmuc_items_wrapper">
                                                <div class="accordionButton" id="a">
                                                    <span># Hạng mục</span><span>:</span> <span class="span_ten_hangmuc"><?php if( !empty( $value['ten_hangmuc'] ) ){ echo esc_html( $value['ten_hangmuc'] ); } ?></span>
                                                    <span class="plusMinus">+</span>
                                                    <span><a href="#" class="remove_hangmuc_button_by_ajax" data-id_hangmuc="<?php if( !empty( $value['id_hangmuc'] ) ){ echo esc_attr( $value['id_hangmuc'] ); } ?>">Xóa</a></span>
                                                </div>
                                                <div class="accordionContent">
                                                    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table form-table-hangmuc">
                                                        <tbody>
                                                            <tr class="form-field">
                                                                <th valign="top" scope="row">
                                                                    <label for="tenhangmuc"><?php _e( 'Tên hạng mục', 'simple_plugin' ); ?></label>
                                                                </th>
                                                                <td>
                                                                    <input type="hidden" name="hangmuc[<?php echo $key; ?>][id_hangmuc]" value="<?php if( !empty( $value['id_hangmuc'] ) ){ echo esc_attr( $value['id_hangmuc'] ); } ?>" />
                                                                    <input  name="hangmuc[<?php echo $key; ?>][tenhangmuc]" type="text" style="width: 95%" value="<?php if( !empty( $value['ten_hangmuc'] ) ){ echo esc_html( $value['ten_hangmuc'] ); } ?>" class="code tenhangmuc"  required />
                                                                </td>
                                                            </tr>
                                                            <tr class="form-field">
                                                                <th valign="top" scope="row">
                                                                    <label for="trangthai_hangmuc"><?php _e( 'Trạng thái', 'simple_plugin' ); ?></label>
                                                                </th>
                                                                <td>
                                                                    <select  name="hangmuc[<?php echo $key; ?>][trangthai_hoanthanh]" class="code">
                                                                        <option value="Đã hoàn thành" <?php if( !empty( $value['trangthai_hangmuc'] ) && $value['trangthai_hangmuc'] === 'Đã hoàn thành' ){ echo 'selected="selected"'; } ?> >Đã hoàn thành</option>
                                                                        <option value="Đang triển khai" <?php if( !empty( $value['trangthai_hangmuc'] ) && $value['trangthai_hangmuc'] === 'Đang triển khai' ){ echo 'selected="selected"'; } ?>>Đang triển khai</option>
                                                                        <option value="Chưa hoàn thành" <?php if( !empty( $value['trangthai_hangmuc'] ) && $value['trangthai_hangmuc'] === 'Chưa hoàn thành' ){ echo 'selected="selected"'; } ?>>Chưa hoàn thành</option>
                                                                        <option value="Đã hủy" <?php if( !empty( $value['trangthai_hangmuc'] ) && $value['trangthai_hangmuc'] === 'Đã hủy' ){ echo 'selected="selected"'; } ?>>Đã hủy</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr class="form-field">
                                                                <th valign="top" scope="row">
                                                                    <label for="noidung_hangmuc"><?php _e( 'Nội dung', 'simple_plugin' ); ?></label>
                                                                </th>
                                                                <td>
                                                                    <textarea name="hangmuc[<?php echo $key; ?>][noidung_hangmuc]"  class="code" style="width: 95%"><?php if( !empty( $value['noi_dung'] ) ){ echo esc_html( $value['noi_dung'] ); } ?></textarea>
                                                                </td>
                                                            </tr>
                                                            <tr class="form-field">
                                                                <th valign="top" scope="row">
                                                                    <label for="hangmuc_tgbatdau"><?php _e( 'Thời gian bắt đầu', 'simple_plugin' ); ?></label>
                                                                </th>
                                                                <td>
                                                                    <input value="<?php if( !empty( $value['ngaybatdau'] ) ){ echo esc_html( $value['ngaybatdau'] ); } ?>" class="hangmuc_tgbatdau_isseted" name="hangmuc[<?php echo $key; ?>][thoigianbatdau]" type="text" style="width: 95%" value="<?php if( !empty( $item['thoigianbatdau'] ) ){ echo esc_html( $item['thoigianbatdau'] ); };?>" class="code"  required />
                                                                </td>
                                                            </tr>
                                                            <tr class="form-field">
                                                                <th valign="top" scope="row">
                                                                    <label for="hangmuc_tgketthuc"><?php _e( 'Thời gian kết thúc', 'simple_plugin' ); ?></label>
                                                                </th>
                                                                <td>
                                                                    <input class="hangmuc_tgketthuc_isseted" name="hangmuc[<?php echo $key; ?>][thoigianketthuc]" type="text" style="width: 95%" value="<?php if( !empty( $value['ngayketthuc'] ) ){ echo esc_html( $value['ngayketthuc'] ); } ?>" class="code"  required />
                                                                </td>
                                                            </tr>
                                                            <tr class="form-field">
                                                                <th valign="top" scope="row">
                                                                    <label for="phantram_hoanthanh_hangmuc"><?php _e( 'Phần trăm hoàn thành', 'simple_plugin' ); ?></label>
                                                                </th>
                                                                <td>
                                                                    <input type="range" class="percent_range_input" name="rangeInput[hangmuc][]" min="0" max="100"  value="<?php if( !empty( $value['phantram_hoanthanh'] ) ){ echo esc_html( $value['phantram_hoanthanh'] ); } ?>" style="width: 70%;" />  <!-- onchange="updateTextInput(this.value);" -->                                                     
                                                                    <span class="percent_number"><?php if( !empty( $value['phantram_hoanthanh'] ) ){ echo esc_html( $value['phantram_hoanthanh'] ); } ?></span><span id="percent_unit">%</span>
                                                                    <input type="hidden" class="input_percent_number" name="hangmuc[<?php echo $key; ?>][phantram_hoanthanh_hangmuc]" value="<?php if( !empty( $value['phantram_hoanthanh'] ) ){ echo esc_html( $value['phantram_hoanthanh'] ); } ?>"/>
                                                                    
                                                                </td>
                                                            </tr>
                                                            <tr class="form-field">
                                                                <?php 
                                                                    $post_stt_cv = 0; 
                                                                    if( !empty( $value['congviec'] ) ){ $post_stt_cv = count( $value['congviec'] ); }
                                                                ?>
                                                                <th valign="top" scope="row">
                                                                    <h2><a class="add-new-h2" href="#" id="button_hangmuc_them_congviec" data-stt_cv="<?php echo $post_stt_cv; ?>" data-stt_hangmuc="<?php echo $key; ?>">Thêm công việc</a></h2>
                                                                </th>
                                                                <td>
                                                                    <div class="hangmuc_congviec_wrapper_all" id="hangmuc_congviec_wrapper_all">
                                                                        <?php
                                                                            $stt_hangmuc = $key;
                                                                            if( !empty( $value['congviec'] ) ){ 
                                                                              foreach( $value['congviec'] as $k=>$v ){ ?>
                                                                                <?php 
                                                                                $unig_id = uniqid(); 
                                                                                
                                                                                $stt_congviec = $k;
                                                                                ?>
                                                                                <div class="hangmuc_congviec_items_wrapper">
                                                                                    <div class="accordionButton2" >
                                                                                        <span># Công việc</span><span>:</span> <span class="span_ten_hangmuc_congviec"><?php if( !empty( $v['ten_congviec'] ) ){ echo esc_html( $v['ten_congviec'] ); }?></span>
                                                                                        <span class="plusMinus">+</span>
                                                                                        <span><a href="#" class="remove_hangmuc_cong_viec_button_by_ajax" data-id_congviec="<?php if( !empty( $v['id_congviec'] ) ){ echo esc_html( $v['id_congviec'] ); }?>">Xóa</a></span>
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
                                                                                                            <input name="hangmuc[<?php echo $stt_hangmuc; ?>][congviec][<?php echo $stt_congviec; ?>][tencongviec]" type="text" style="width: 95%" value="<?php if( !empty( $v['ten_congviec'] ) ){ echo esc_html( $v['ten_congviec'] ); }?>" class="code hangmuc_tencongviec"  required />
                                                                                                            <input name="hangmuc[<?php echo $stt_hangmuc; ?>][congviec][<?php echo $stt_congviec; ?>][id_congviec]" type="hidden" style="width: 95%" value="<?php if( !empty( $v['id_congviec'] ) ){ echo esc_html( $v['id_congviec'] ); }?>" class="code hangmuc_id_congviec"  required />
                                                                                                        </td>
                                                                                                    </tr>  
                                                                                                    <tr class="form-field">
                                                                                                        <th valign="top" scope="row">
                                                                                                            <label for="hangmuc[<?php echo $stt_hangmuc; ?>][congviec][<?php echo $stt_congviec; ?>][noidungcongviec]"><?php _e( 'Nội dung công việc', 'simple_plugin' ); ?></label>
                                                                                                        </th>
                                                                                                        <td>
                                                                                                            <textarea name="hangmuc[<?php echo $stt_hangmuc; ?>][congviec][<?php echo $stt_congviec; ?>][noidungcongviec]" style="width: 95%"class="code" required ><?php if( !empty( $v['noidung_congviec'] ) ){ echo esc_html( $v['noidung_congviec'] ); }?></textarea>
                                                                                                        </td>
                                                                                                    </tr> 
                                                                                                    <tr class="form-field">
                                                                                                        <th valign="top" scope="row">
                                                                                                            <label for="trangthai_hangmuc_congviec"><?php _e( 'Trạng thái', 'simple_plugin' ); ?></label>
                                                                                                        </th>
                                                                                                        <td>
                                                                                                            <select  name="hangmuc[<?php echo $stt_hangmuc; ?>][congviec][<?php echo $stt_congviec; ?>][trangthai_hoanthanh]" class="code">
                                                                                                                <option value="Đã hoàn thành" <?php if( !empty( $v['trangthai_congviec'] ) && $v['trangthai_congviec'] == 'Đã hoàn thành' ){ echo 'selected="selected"'; } ?>  >Đã hoàn thành</option>
                                                                                                                <option value="Đang triển khai" <?php if( !empty( $v['trangthai_congviec'] ) && $v['trangthai_congviec'] == 'Đang triển khai' ){ echo 'selected="selected"'; } ?>>Đang triển khai</option>
                                                                                                                <option value="Chưa hoàn thành" <?php if( !empty( $v['trangthai_congviec'] ) && $v['trangthai_congviec'] == 'Chưa hoàn thành' ){ echo 'selected="selected"'; } ?>>Chưa hoàn thành</option>
                                                                                                                <option value="Đã hủy" <?php if( !empty( $v['trangthai_congviec'] ) && $v['trangthai_congviec'] == 'Đã hủy' ){ echo 'selected="selected"'; } ?>>Đã hủy</option>
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
                                                                                                                <?php 
                                                                                                                    $list_nhanvien_tg = array(); 
                                                                                                                    
                                                                                                                    if( !empty( $v['nhanvien_thamgia'] ) ){
                                                                                                                        $list_nhanvien_tg = explode(',', $v['nhanvien_thamgia'] );
                                                                                                                    }
                                                                                                                    
                                                                                                                    if( !empty( $list_nhanvien_tg ) ){
                                                                                                                        $list_deleted_nhanvien = TT_Nhanvien::tt_get_deleted_nhanvien();
                                                                                                                        foreach( $list_nhanvien_tg as $key=>$value ){
                                                                                                                            if( in_array( $value, $list_deleted_nhanvien ) ){
                                                                                                                                unset( $list_nhanvien_tg[$key] );
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                    
                                                                                                                ?>  
                                                                                                                <?php echo TT_Nhanvien::render_list_checkbox_nhanvien( 'show', $checkbox_name, $list_nhanvien_tg ); ?>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr class="form-field">
                                                                                                        <th valign="top" scope="row">
                                                                                                            <label for="hangmuc_congviec_tgbatdau"><?php _e( 'Thời gian bắt đầu', 'simple_plugin' ); ?></label>
                                                                                                        </th>
                                                                                                        <td>
                                                                                                            <input class="hangmuc_congviec_tgbatdau" name="hangmuc[<?php echo $stt_hangmuc; ?>][congviec][<?php echo $stt_congviec; ?>][tg_batdau]" type="text" style="width: 95%" value="<?php if( !empty( $v['ngaybatdau'] ) ){ echo esc_html( $v['ngaybatdau'] ); }?>" class="code"  required />
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr class="form-field">
                                                                                                        <th valign="top" scope="row">
                                                                                                            <label for="hangmuc_congviec_tgketthuc"><?php _e( 'Thời gian kết thúc', 'simple_plugin' ); ?></label>
                                                                                                        </th>
                                                                                                        <td>
                                                                                                            <input class="hangmuc_congviec_tgketthuc" name="hangmuc[<?php echo $stt_hangmuc; ?>][congviec][<?php echo $stt_congviec; ?>][tg_ketthuc]" type="text" style="width: 95%" value="<?php if( !empty( $v['ngayketthuc'] ) ){ echo esc_html( $v['ngayketthuc'] ); }?>" class="code"  required />
                                                                                                        </td>
                                                                                                    </tr> 
                                                                                                    
                                                                                                </tbody>    
                                                                                            </table>
                                                                                        </div>
                                                                                      </div>
                                                                                      <div class="clearfix"></div>
                                                                                </div>        
                                                                        <?php }//endforeach       
                                                                            }//endif
                                                                            
                                                                        ?>
                                                                    </div>
                                                                    <?php //include( TT_DIR_PATH. '/templates/new_congviec.php' ); ?>
                                                                </td>
                                                            </tr><!-- end thêm công viêc -->    
                                                            
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div><!-- hangmuc_items_wrapper -->
                                            
                           <?php        }
                                    }
                                ?>
                                <?php //include( TT_DIR_PATH . '/templates/new_hangmuc.php' ); ?>
                            <?php } ?>
                        </div><!-- hangmuc_add_new -->
                        
                    </div>
                </div>            
                
                <!-- Custom code -->
                
                <!-- End custom code -->
                
                <input type="submit" value="<?php _e( 'Gửi', 'simple_plugin' ); ?>" id="submit" class="button-primary" name="submit"/>
            </form>
        </div>
<?php        
    }//End function tt_new_duan_page_callback()
    
    public static function tt_get_tt_hangmuc_by_id_duan( $id_duan ){
        global $wpdb;
        $table_hangmuc = $wpdb->prefix . 'hangmuc';
        $table_congviec= $wpdb->prefix . 'congviec';
        
        $tt_hangmuc = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_hangmuc} WHERE id_duan = %d AND display_status = %s", $id_duan, 'show' ), ARRAY_A );
    
        if( is_array( $tt_hangmuc ) && !empty( $tt_hangmuc ) ){
            foreach( $tt_hangmuc as $k=>$v ){
                $tt_congviec = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_congviec} WHERE id_hangmuc = %d AND display_status = %s", $v['id_hangmuc'], 'show' ), ARRAY_A );
                if( !empty( $tt_congviec ) ){
                    $tt_hangmuc[$k]['congviec'] = $tt_congviec;
                }
            }
            return $tt_hangmuc;
        }else{
            return array();
        }
    }
    
    public static function tt_validate_data_duan( $item ){
        $messages = array();
         
        if ( empty( $item['tenduan'] ) ){ 
            $messages[] = __( 'Vui lòng nhập vào tên dự án', 'simple_plugin' );
        }
        
        if ( empty( $item['ngaybatdau'] ) ){ 
            $messages[] = __( 'Vui chọn thời gian bắt đầu cho dự án', 'simple_plugin' );
        }
        
        if ( empty( $item['ngayketthuc'] ) ){ 
            $messages[] = __( 'Vui lòng chọn thời gian kết thúc dự án', 'simple_plugin' );
        }
        
        if ( empty($messages ) ){
            return true; 
        }else{
            return implode( '<br />', $messages );
        }
        
    }
    
    public function  tt_ajax_load_form_them_congviec_in_hangmuc_callback(){
        global $stt_congviec, $stt_hangmuc;
        $type = 'done';
        
        $stt_congviec = $_POST['id_congviec'];
        $stt_hangmuc  = $_POST['id_hangmuc'];
        
        if( !check_ajax_referer( 'tt_ajax_form', 'security' ) ){
            $type = 'false';
            $result = array( 
                'type' => $type,
                'data' => ''
            );
		    die( wp_send_json( $result ) );
        }
        
        ob_start();
        include( TT_DIR_PATH . '/templates/new_congviec.php' );
        $data = ob_get_clean();
        $result = array( 
            'type' => $type, 
            'data' => $data 
        );
		die( wp_send_json( $result ) );
        
    }
    
    public function  tt_ajax_load_form_them_hangmuc_callback(){
        global $so_thutu;
        $type = 'done';
        $so_thutu = $_POST['stt'];
        if( !check_ajax_referer( 'tt_ajax_form', 'security' ) ){
            $type = 'false';
            $result = array( 
                'type' => $type,
                'data' => ''
            );
		    die( wp_send_json( $result ) );
        }
        wp_enqueue_script( 'jquery_arcodion' );
        ob_start();
        include( TT_DIR_PATH . '/templates/new_hangmuc.php' );
        $data = ob_get_clean();
        
        $result = array( 
            'type' => $type, 
            'data' => $data 
        );
		die( wp_send_json( $result ) );
        
    }
    
    public function  tt_ajax_delete_congviec_callback(){
        global $wpdb;
        $table_congviec   = $wpdb->prefix . 'congviec';
        
        $type             = 'done';
        $post_id_congviec = $_POST['post_id_congviec'];
        
        if( !check_ajax_referer( 'tt_ajax_form', 'security' ) ){
            $type = 'false';
            $result = array( 
                'type' => $type,
                'data' => 'Lỗi bảo mật! Vui lòng thử lại sau',
                'is_done' => 'no'
            );
		    die( wp_send_json( $result ) );
        }
        
        $res = $wpdb->update( $table_congviec, array( 'display_status' => 'hidden' ), array( "id_congviec" => $post_id_congviec ) );
        
        if( $res ){
            $data = '<div id="message" class="updated"><p>Xóa thành công công việc</p></div>';
            $is_done = 'yes';
        }else{
            $data = '<div id="notice" class="error"><p>Lỗi, dữ liệu không chính xác</p></div>';
            $is_done = 'no';
        }
       
        $result = array( 
            'type' => $type, 
            'data' => $data,
            'is_done' => $is_done,
        );
		die( wp_send_json( $result ) );
    }
    
    public static function tt_ajax_delete_hangmuc_callback(){
        global $wpdb;
        $table_congviec   = $wpdb->prefix . 'congviec';
        $table_hangmuc    = $wpdb->prefix . 'hangmuc';
        
        $type             = 'done';
        $post_id_hangmuc  = $_POST['post_id_hangmuc'];
        
        if( !check_ajax_referer( 'tt_ajax_form', 'security' ) ){
            $type = 'false';
            $result = array( 
                'type' => $type,
                'data' => 'Lỗi bảo mật! Vui lòng thử lại sau',
                'is_done' => 'no'
            );
		    die( wp_send_json( $result ) );
        }
        
        //$res = $wpdb->update( $table_congviec, array( 'display_status' => 'hidden' ), array( "id_congviec" => $post_id_congviec ) );
        //Kiểm tra xem trong hạng mục có còn công việc không, nếu có thì k thể xóa
        $check_id_congviec = $wpdb->get_results( $wpdb->prepare( "SELECT id_congviec FROM {$table_congviec} WHERE id_hangmuc = %d AND display_status = %s", $post_id_hangmuc, 'show' ), ARRAY_A );
        if( !empty( $check_id_congviec ) ){ //k cho xóa, y/c xóa hết các công việc có trong hạng mục này trước
            $data = '<div id="notice" class="error"><p>Lỗi! Vui lòng xóa hết các công việc có trong hạng mục này.</p></div>';
            $is_done = 'no';
        }else{ //Cho xóa hạng mục
            $res = $wpdb->update( $table_hangmuc, array( 'display_status' => 'hidden' ), array( "id_hangmuc" => $post_id_hangmuc ) );
            if( $res ){
                $data = '<div id="message" class="updated"><p>Xóa thành công hạng mục</p></div>';
                $is_done = 'yes';
            }else{
                $data = '<div id="notice" class="error"><p>Lỗi, dữ liệu không chính xác</p></div>';
                $is_done = 'no';
            }
        }
        
        $result = array( 
            'type' => $type, 
            'data' => $data,
            'is_done' => $is_done,
        );
		die( wp_send_json( $result ) );
    }
    
    public static function tt_get_all_duan_info( $args = array() ){
        global $wpdb;
        $table_duan = $wpdb->prefix . 'duan';
        $table_doitac = $wpdb->prefix . 'doitac';
        $table_nhanvien = $wpdb->prefix . 'nhanvien';
        
        if( empty( $args ) ){
            //Nếu rỗng $args, thì lấy tất cả các dự án.
            $query   = $wpdb->prepare( "SELECT * FROM {$table_duan} WHERE display_status = %s", 'show' );
            $results = $wpdb->get_results( $query, ARRAY_A );
            
            foreach( $results as $key=>$value ){
                $results[$key]['ten_doitac'] = '';
                $results[$key]['ten_ql_duan'] = '';
                
                if( !empty( $value['id_doitac'] ) ){
                    $get_name_doitac = $wpdb->prepare( "SELECT hoten_tendonvi FROM {$table_doitac} WHERE id_doitac = %d AND display_status = %s", $value['id_doitac'], 'show' );
                    $ten_doitac = $wpdb->get_var( $get_name_doitac );
                    
                    if( !empty( $ten_doitac ) ){
                        $results[$key]['ten_doitac'] = $ten_doitac;
                    }
                }
                
                if( !empty( $value['id_quanly_duan'] ) ){
                    $get_name_ql_duan = $wpdb->prepare( "SELECT hoten FROM {$table_nhanvien} WHERE id_nhanvien = %d AND display_status = %s", $value['id_quanly_duan'], 'show' );
                    $ten_ql_duan = $wpdb->get_var( $get_name_ql_duan );
                    
                    if(!empty( $ten_ql_duan ) ){
                        $results[$key]['ten_ql_duan'] = $ten_ql_duan;
                    }
                }
            }
            return $results;
            
        }else{
            $query = "SELECT * FROM {$table_duan} WHERE display_status = 'show'";
            if( isset( $args['trangthai_duan'] ) && !empty( $args['trangthai_duan'] ) ){
               $query .= " AND tinhtrangduan = '{$args['trangthai_duan']}'";    
            }//isset trangthai_duan
            
            if( isset( $args['start_time'] ) || isset( $args['end_time'] ) ){
                //Thông tin dự án filter by trạng thái 
                $where_time = "";
                if( !empty( $args['start_time'] ) && empty( $args['end_time'] ) ){
                    $query .= " AND ngaybatdau >= '{$args['start_time']}'";
                }elseif( !empty( $args['end_time'] ) && empty( $args['start_time'] ) ){
                    $query .= " AND ngayketthuc <= '{$args['end_time']}'";
                }elseif( !empty( $args['end_time'] ) && !empty( $args['start_time'] ) ){
                    $query .= " AND ngayketthuc BETWEEN '{$args['start_time']}' AND '{$args['end_time']}'";
                }
            }//end isset start and date time
            
            $results = $wpdb->get_results( $query, ARRAY_A );
            foreach( $results as $key=>$value ){
                $results[$key]['ten_doitac'] = '';
                $results[$key]['ten_ql_duan'] = '';
                
                if( !empty( $value['id_doitac'] ) ){
                    $get_name_doitac = $wpdb->prepare( "SELECT hoten_tendonvi FROM {$table_doitac} WHERE id_doitac = %d AND display_status = %s", $value['id_doitac'], 'show' );
                    $ten_doitac = $wpdb->get_var( $get_name_doitac );
                    
                    if( !empty( $ten_doitac ) ){
                        $results[$key]['ten_doitac'] = $ten_doitac;
                    }
                }
                
                if( !empty( $value['id_quanly_duan'] ) ){
                    $get_name_ql_duan = $wpdb->prepare( "SELECT hoten FROM {$table_nhanvien} WHERE id_nhanvien = %d AND display_status = %s", $value['id_quanly_duan'], 'show' );
                    $ten_ql_duan = $wpdb->get_var( $get_name_ql_duan );
                    
                    if(!empty( $ten_ql_duan ) ){
                        $results[$key]['ten_ql_duan'] = $ten_ql_duan;
                    }
                }
            }
            return $results;
            
        }
    }
    
    public function  tt_ajax_load_filter_duan_callback(){
        $type = 'done';
        check_ajax_referer( 'tt_ajax_form', 'security' );
        
        $args = array();
        if( !empty( $_POST['post_trangthai_duan'] ) ){
            $args['trangthai_duan'] = $_POST['post_trangthai_duan'];
        }
        
        if( !empty( $_POST['post_start_date'] ) ){
            $args['start_time'] = $_POST['post_start_date'];
        }
        
        if( !empty( $_POST['post_end_date'] ) ){
            $args['end_time'] = $_POST['post_end_date'];
        }
        
        $results = self::tt_get_all_duan_info( $args );
        if( !empty( $results ) ){
            ob_start();
        ?>
        <div class="row header green">
          <div class="cell">Tên dự án</div>
          <div class="cell">Đối tác</div>
          <div class="cell">Quản lý dự án</div>
          <div class="cell">Ngày bắt đầu</div>
          <div class="cell">Ngày kết thúc</div>
          <div class="cell">Trạng thái</div>
        </div>
        <?php foreach( $results as $key=> $value ){ ?>
        <div class="row">
          <div class="cell"><?php if( !empty( $value['tenduan'] ) ){ echo esc_html( $value['tenduan'] ) ;} ?></div>
          <div class="cell"><?php if( !empty( $value['ten_doitac'] ) ){ echo esc_html( $value['ten_doitac'] ) ;} ?></div>
          <div class="cell"><?php if( !empty( $value['ten_ql_duan'] ) ){ echo esc_html( $value['ten_ql_duan'] ) ;} ?></div>
          <div class="cell"><?php if( !empty( $value['ngaybatdau'] ) ){ echo esc_html( date( 'd-m-Y', strtotime( $value['ngaybatdau'] ) ) ) ;} ?></div>
          <div class="cell"><?php if( !empty( $value['ngayketthuc'] ) ){ echo esc_html( date( 'd-m-Y', strtotime( $value['ngayketthuc'] ) ) ) ;} ?></div>
          <div class="cell"><?php if( !empty( $value['tinhtrangduan'] ) ){ echo esc_html( $value['tinhtrangduan'] ) ;} ?></div>
        </div>
        <?php } ?>    
<?php       $data = ob_get_clean();     
        }else{
            ob_start();
        ?>
        
        <div class="row header green">
          <div class="cell">Tên dự án</div>
          <div class="cell">Đối tác</div>
          <div class="cell">Quản lý dự án</div>
          <div class="cell">Thành viên tham gia</div>
          <div class="cell">Ngày bắt đầu</div>
          <div class="cell">Ngày kết thúc</div>
          <div class="cell">Trạng thái</div>
        </div>
        <div class="row">
          <div class="cell">Không có dữ liệu</div>
          <div class="cell">...</div>
          <div class="cell">...</div>
          <div class="cell">...</div>
          <div class="cell">...</div>
          <div class="cell">...</div>
          <div class="cell">...</div>
        </div>
<?php            
            $data = ob_get_clean();
        }
        
        $result = array( 
            'type' => $type, 
            'data' => $data 
        );
		die( wp_send_json( $result ) );
    }
    
    public static function tt_get_default_duan_info(){
        $results = self::tt_get_all_duan_info( $args );
        if( !empty( $results ) ){
?>
                <div class="row header green">
                  <div class="cell">Tên dự án</div>
                  <div class="cell">Đối tác</div>
                  <div class="cell">Quản lý dự án</div>
                  <div class="cell">Ngày bắt đầu</div>
                  <div class="cell">Ngày kết thúc</div>
                  <div class="cell">Trạng thái</div>
                </div>
            <?php foreach( $results as $key=> $value ){ ?>
                <div class="row">
                  <div class="cell"><?php if( !empty( $value['tenduan'] ) ){ echo esc_html( $value['tenduan'] ) ;} ?></div>
                  <div class="cell"><?php if( !empty( $value['ten_doitac'] ) ){ echo esc_html( $value['ten_doitac'] ) ;} ?></div>
                  <div class="cell"><?php if( !empty( $value['ten_ql_duan'] ) ){ echo esc_html( $value['ten_ql_duan'] ) ;} ?></div>
                  <div class="cell"><?php if( !empty( $value['ngaybatdau'] ) ){ echo esc_html( date( 'd-m-Y', strtotime( $value['ngaybatdau'] ) ) ) ;} ?></div>
          <div class="cell"><?php if( !empty( $value['ngayketthuc'] ) ){ echo esc_html( date( 'd-m-Y', strtotime( $value['ngayketthuc'] ) ) ) ;} ?></div>
                  <div class="cell"><?php if( !empty( $value['tinhtrangduan'] ) ){ echo esc_html( $value['tinhtrangduan'] ) ;} ?></div>
                </div>
            <?php } ?> 
        <?php }else{ ?>
            <div class="row">
              <div class="cell">Không có dữ liệu</div>
              <div class="cell">...</div>
              <div class="cell">...</div>
              <div class="cell">...</div>
              <div class="cell">...</div>
              <div class="cell">...</div>
              <div class="cell">...</div>
            </div>
<?php   } ?>
<?php    
    }//End function
    
    public static function tt_render_list_duan_showed(){
        global $wpdb;
        $table = $wpdb->prefix . 'duan';
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT id_duan, tenduan FROM {$table} WHERE display_status = %s", "show" ), ARRAY_A );
        
        if( !empty( $results ) ){
            foreach( $results as $key=>$value ){
                echo '<option value="'.$value['id_duan'].'">'. $value['tenduan'] .'</option>';
            }
        }
    }
    
}//End class TT_Duan
new TT_Duan();

