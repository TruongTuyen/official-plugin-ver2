<?php
/*
Plugin Name: Manage Teamwork Version 2
Description: Wordpress Plugin đơn giản quản lý thông tin một teamwork.
Plugin URI:  https:localhost
Author URI:  https:localhost
Author:      Truong Tuyen Anh
Version:     2.0
Text Domain: simple_plugin
*/

define( 'TT_DIR_PATH', plugin_dir_path( __FILE__ )); //Lấy ra dường dẫn tuyệt đối tới thu muc của plugin này 
define( 'TT_DIR_URL', plugin_dir_url( __FILE__ )); //Lấy ra url của plugin này

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );//sử dụng file này để có thể dùng hàm dbDelta();
require_once TT_DIR_PATH . 'classes/class.TT_KyNang.php';  //Class Ky Nang xu ly thong tin liên quan den ky nang
require_once TT_DIR_PATH . 'classes/class.TT_Nhanvien.php';//Class Nhan Vien xu ly thong tin lien quan den nhan vien
require_once TT_DIR_PATH . 'classes/class.TT_Duan.php';//Class Du An xu ly thong tin lien quan den du an
require_once TT_DIR_PATH . 'classes/class.TT_Doitac.php';//Class Du An xu ly thong tin lien quan den du an

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class TT_Teamwork{
    public $my_db_version = '2.0';
    
    function __construct(){
        register_activation_hook( __FILE__,  array( $this, 'create_table' ) );//Đăng ký activation_hook thông qua hàm create_table để tạo ra các bảng dữ liệu cần thiết khi kích hoạt plugin
        register_activation_hook( __FILE__,  array( $this, 'dummy_data' ) );//Đăng ký activation_hook thông qua hàm dummy_data để chèn dữ liệu mẫu vào các bảng plugin, tránh các lỗi không có dữ liệu
        register_deactivation_hook( __FILE__, array( $this, 'delete_table' ) );//Đăng ký deactivation_hook để tiến hành xóa các bảng dữ liệu khi ngừng kích hoạt plugin
        
        add_action( 'admin_menu', array( $this, 'register_setting_menu' ) );
        add_action( 'init', array( $this, 'tt_load_languages' ) );
        
        
    }
    
    public function create_table(){
        global $wpdb;
        $query = "
            CREATE TABLE {$wpdb->prefix}doitac(
                id_doitac BIGINT NOT NULL AUTO_INCREMENT,
                hoten_tendonvi VARCHAR(100) NOT NULL,
                loai VARCHAR(50) NOT NULL,
                mota TEXT NULL,
                display_status VARCHAR(20) NOT NULL,
                PRIMARY KEY (id_doitac)
            );
            
            CREATE TABLE {$wpdb->prefix}duan(
                id_duan BIGINT NOT NULL AUTO_INCREMENT,
                id_doitac BIGINT NOT NULL,
                id_quanly_duan BIGINT NOT NULL,
                tenduan VARCHAR(225) NOT NULL,
                ngaybatdau DATETIME NOT NULL,
                ngayketthuc DATETIME NOT NULL,
                tinhtrangduan VARCHAR(225) NOT NULL,
                mota TEXT NULL,
                display_status VARCHAR(20) NOT NULL,
                PRIMARY KEY (id_duan)   
            );
                         
            CREATE TABLE {$wpdb->prefix}nhanvien( 
                id_nhanvien BIGINT NOT NULL AUTO_INCREMENT , 
                hoten VARCHAR(225) NOT NULL , 
                namsinh VARCHAR(4) NOT NULL , 
                gioitinh VARCHAR(3) NOT NULL ,
                quequan VARCHAR(225) NOT NULL , 
                avatar VARCHAR(255) NOT NULL,
                display_status VARCHAR(20) NOT NULL,
                PRIMARY KEY (id_nhanvien)
            );
            
            CREATE TABLE {$wpdb->prefix}kynang(
                id_kynang BIGINT NOT NULL AUTO_INCREMENT,
                tenkynang VARCHAR(225) NOT NULL,
                mota TEXT NULL,
                display_status VARCHAR(20) NOT NULL,
                PRIMARY KEY (id_kynang)
            );
            
            CREATE TABLE {$wpdb->prefix}chitiet_duan(
                id BIGINT NOT NULL AUTO_INCREMENT,
                id_duan BIGINT NOT NULL,
                id_nhanvien BIGINT NOT NULL,
                PRIMARY KEY (id)
            );
            
            CREATE TABLE {$wpdb->prefix}chitiet_kynang(
                id BIGINT NOT NULL AUTO_INCREMENT,
                id_kynang BIGINT NOT NULL,
                id_nhanvien BIGINT NOT NULL,
                PRIMARY KEY (id)
            ); 
            
            CREATE TABLE {$wpdb->prefix}hangmuc(
                id_hangmuc BIGINT NOT NULL AUTO_INCREMENT,
                id_duan BIGINT NOT NULL,
                ten_hangmuc VARCHAR(100) NOT NULL,
                noi_dung TEXT NOT NULL,
                ngaybatdau DATETIME NOT NULL,
                ngayketthuc DATETIME NOT NULL, 
                display_status VARCHAR(20) NOT NULL,
                phantram_hoanthanh INT UNSIGNED NOT NULL,
                trangthai_hangmuc VARCHAR(255) NOT NULL,
                PRIMARY KEY (id_hangmuc)
            );
            
            CREATE TABLE {$wpdb->prefix}congviec(
                id_congviec BIGINT NOT NULL AUTO_INCREMENT,
                id_hangmuc BIGINT NOT NULL,
                ten_congviec VARCHAR(200) NOT NULL,
                nhanvien_thamgia TEXT NOT NULL,
                noidung_congviec TEXT NOT NULL,
                ngaybatdau DATETIME NOT NULL,
                ngayketthuc DATETIME NOT NULL, 
                display_status VARCHAR(20) NOT NULL,
                trangthai_congviec VARCHAR(255) NOT NULL,
                PRIMARY KEY (id_congviec)
            );
        ";
        dbDelta( $query );
    }
    
    public function db_version_option( $version = '1.0' ){
        add_option( 'my_db_version', $version );
    }
    
    public function del_db_version(){
        delete_option( 'my_db_version' );
    }
    
    public function update_db_version( $new_version ){
        update_option( 'my_db_version', $new_version );
    }
    
    public function delete_table(){
        global $wpdb;
        $sql = "DROP TABLE IF EXISTS `{$wpdb->prefix}doitac`, `{$wpdb->prefix}duan`, `{$wpdb->prefix}nhanvien`, `{$wpdb->prefix}kynang`, `{$wpdb->prefix}chitiet_duan`, `{$wpdb->prefix}chitiet_kynang`,`{$wpdb->prefix}hangmuc`, `{$wpdb->prefix}congviec`";
        $wpdb->query( $sql );
        $this->del_db_version();
    }
    
    public function check_db_version(){
        $current_version = get_option( 'my_db_version' );
        if( $this->my_db_version != $current_version ){
            $this->delete_table();
            $this->create_table();
            $this->update_db_version( $this->my_db_version );
        }
    }
    
    public function dummy_data(){
        global $wpdb;
        //Dữ liệu cho bảng đối tác
        $wpdb->insert( $wpdb->prefix . 'doitac', array(
            'id_doitac'          => 1,
            'hoten_tendonvi'     => 'Nguyễn Văn An',
            'loai'               => 'cá nhân', //doanh nghiệp
            'mota'               => '',
            'display_status'     => 'show', //hidden   
        ));
        
        $wpdb->insert( $wpdb->prefix . 'doitac', array(
            'id_doitac'          => 2,
            'hoten_tendonvi'     => 'Nguyễn Văn Bùi',
            'loai'               => 'cá nhân', //doanh nghiệp
            'mota'               => '',
            'display_status'     => 'show', //hidden   
        ));
        $wpdb->insert( $wpdb->prefix . 'doitac', array(
            'id_doitac'          => 3,
            'hoten_tendonvi'     => 'Công ty phần mềm OTVINA',
            'loai'               => 'doanh nghiệp', //doanh nghiệp
            'mota'               => '',
            'display_status'     => 'show', //hidden   
        ));
        
        $wpdb->insert( $wpdb->prefix . 'doitac', array(
            'id_doitac'          => 4,
            'hoten_tendonvi'     => 'Công ty nội thất nhựa Incomtech',
            'loai'               => 'doanh nghiệp', //doanh nghiệp
            'mota'               => '',
            'display_status'     => 'show', //hidden   
        ));
        
        $wpdb->insert( $wpdb->prefix . 'doitac', array(
            'id_doitac'          => 5,
            'hoten_tendonvi'     => 'Công ty bất động sản nhadatphongthuy.vn',
            'loai'               => 'doanh nghiệp', //doanh nghiệp
            'mota'               => '',
            'display_status'     => 'show', //hidden   
        ));
        
        //Dữ liệu mẫu cho bảng duan
        $wpdb->insert( $wpdb->prefix . 'duan', array(
            'id_duan'            => 1,
            'id_doitac'          => 1,
            'id_quanly_duan'     => 1,
            'tenduan'            => 'Website bán hàng cho công ty Incomtech',
            'ngaybatdau'         => '2015-10-16',
            'ngayketthuc'        => '2015-11-16',
            'tinhtrangduan'      => 'Đã hoàn thành', // Đã hoàn thành, Đang triển khai, Chưa hoàn thành, Đã hủy   
            'mota'               => 'Website bán hàng nội thất nhựa',
            'display_status'     => 'show'    
        ));
        
        $wpdb->insert( $wpdb->prefix . 'duan', array(
            'id_duan'            => 2,
            'id_doitac'          => 2,
            'id_quanly_duan'     => 2,
            'tenduan'            => 'Website bất động sản',
            'ngaybatdau'         => '2015-10-16',
            'ngayketthuc'        => '2015-11-16',
            'tinhtrangduan'      => 'Đã hoàn thành', // Đã hoàn thành, Đang triển khai, Chưa hoàn thành, Đã hủy   
            'mota'               => '',
            'display_status'     => 'show'    
        ));
        //du lieu mau cho bang _nhanvien
        $wpdb->insert( $wpdb->prefix . 'nhanvien', array(
            'id_nhanvien'       => 1, 
            'hoten'             => 'Nguyễn Văn An', 
            'namsinh'           => '1990', 
            'gioitinh'          => "Nam",
            'quequan'           => 'Thái Bình', 
        ));
        $wpdb->insert( $wpdb->prefix . 'nhanvien', array(
            'id_nhanvien'       => 2, 
            'hoten'             => 'Nguyen Thị Ba', 
            'namsinh'           => '1991', 
            'gioitinh'          => "Nữ",
            'quequan'           => 'Cao Bằng', 
        ));
        $wpdb->insert( $wpdb->prefix . 'nhanvien', array(
            'id_nhanvien'       => 3, 
            'hoten'             => 'Phạm Văn Bình', 
            'namsinh'           => '1996', 
            'gioitinh'          => "Nam",
            'quequan'           => 'Quảng Ninh', 
        ));
        $wpdb->insert( $wpdb->prefix . 'nhanvien', array(
            'id_nhanvien'       => 4, 
            'hoten'             => 'Nguyễn Văn Trường', 
            'namsinh'           => '1993', 
            'gioitinh'          => "Nam",
            'quequan'           => 'Thái Nguyên', 
        ));
        $wpdb->insert( $wpdb->prefix . 'nhanvien', array(
            'id_nhanvien'       => 5, 
            'hoten'             => 'Trần Bá Trịnh Trọng', 
            'namsinh'           => '1996', 
            'gioitinh'          => "Nam",
            'quequan'           => 'Thái Nguyên', 
        ));
        $wpdb->insert( $wpdb->prefix . 'nhanvien', array(
            'id_nhanvien'       => 6, 
            'hoten'             => 'Nguyễn Thu Thủy', 
            'namsinh'           => '1996', 
            'gioitinh'          => "Nữ",
            'quequan'           => 'Bắc Giang', 
        ));
        $wpdb->insert( $wpdb->prefix . 'nhanvien', array(
            'id_nhanvien'       => 7, 
            'hoten'             => 'Phạm Thu Hương', 
            'namsinh'           => '1996', 
            'gioitinh'          => "Nữ",
            'quequan'           => 'Quảng Ninh', 
        ));
        $wpdb->insert( $wpdb->prefix . 'nhanvien', array(
            'id_nhanvien'       => 8, 
            'hoten'             => 'Nguyễn Văn Quân', 
            'namsinh'           => '1993', 
            'gioitinh'          => "Nam",
            'quequan'           => 'Bắc Ninh', 
        ));
        $wpdb->insert( $wpdb->prefix . 'nhanvien', array(
            'id_nhanvien'       => 9, 
            'hoten'             => 'Trần Văn Duy', 
            'namsinh'           => '1995', 
            'gioitinh'          => "Nam",
            'quequan'           => 'Thái Nguyên', 
        ));
        //du lieu mau cho bang _kynang
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 1,
            'tenkynang'         => 'HTML,CSS',
            'mota'              => '',
            'display_status'    => 'show'
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 2,
            'tenkynang'         => 'Design',
            'mota'              => '',
            'display_status'    => 'show'
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 3,
            'tenkynang'         => 'Javascript, jQuery',
            'mota'              => '',
            'display_status'    => 'show'
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 4,
            'tenkynang'         => 'PHP, MySQL',
            'mota'              => 'Thành thạo ngôn ngữ PHP và hệ quản trị CSDL MySQL',
            'display_status'    => 'show'
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 5,
            'tenkynang'         => 'Photoshop',
            'mota'              => 'Sử dụng thành thạo Photoshop',
            'display_status'    => 'show'
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 6,
            'tenkynang'         => 'Android',
            'mota'              => 'Phát triển Android chuyên nghiệp',
            'display_status'    => 'show'
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 7,
            'tenkynang'         => 'iOS',
            'mota'              => 'Phát triển iOS chuyên nghiệp',
            'display_status'    => 'show'
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 8,
            'tenkynang'         => 'PHP Framework CodeIgniter',
            'mota'              => 'Sử dụng thành thạo framework CodeIgniter',
            'display_status'    => 'show'
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 9,
            'tenkynang'         => 'PHP Framework Laravel',
            'mota'              => 'Sử dụng thành thạo framework Laravel',
            'display_status'    => 'show'
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 10,
            'tenkynang'         => 'WordPress',
            'mota'              => 'Phát triển theme và plugin WordPress chuyên nghiệp',
            'display_status'    => 'show'
        ));
        //du lieu mau cho bang: _chitiet_duan
        $wpdb->insert( $wpdb->prefix . 'chitiet_duan', array(
            'id'                => 1,
        	'id_duan'           => 1,
        	'id_nhanvien'       => 1,
        ));
        $wpdb->insert( $wpdb->prefix . 'chitiet_duan', array(
            'id'                => 2,
        	'id_duan'           => 1,
        	'id_nhanvien'       => 2,
        ));
        $wpdb->insert( $wpdb->prefix . 'chitiet_duan', array(
            'id'                => 3,
        	'id_duan'           => 2,
        	'id_nhanvien'       => 1,
        ));
        $wpdb->insert( $wpdb->prefix . 'chitiet_duan', array(
            'id'                => 4,
        	'id_duan'           => 2,
        	'id_nhanvien'       => 2,
        ));
        
        //du lieu cho bang _chitiet_kynang
        $wpdb->insert( $wpdb->prefix . 'chitiet_kynang', array(
            'id'                => 1,
    	    'id_kynang'         => 1,
    	    'id_nhanvien'       => 1,
        ));
        $wpdb->insert( $wpdb->prefix . 'chitiet_kynang', array(
            'id'                => 2,
    	    'id_kynang'         => 2,
    	    'id_nhanvien'       => 1,
        ));
        $wpdb->insert( $wpdb->prefix . 'chitiet_kynang', array(
            'id'                => 3,
    	    'id_kynang'         => 1,
    	    'id_nhanvien'       => 2,
        ));
        $wpdb->insert( $wpdb->prefix . 'chitiet_kynang', array(
            'id'                => 4,
    	    'id_kynang'         => 2,
    	    'id_nhanvien'       => 2,
        ));
    }  
    
    public function tt_teamwork_callback(){ ?>
        <div class="wrap">
            <div class="team_projects"  >
                <h2><?php _e( 'Dự án', 'simple_plugin' ); ?></h2>
                <div class="tablenav top">
                   <div class="alignleft actions bulkactions">
                      <form action="" method="post">                
                      <!--<div class="filter_by_status">-->
                          <label for="filter_trangthai_duan" class="screen-reader-text">Trạng thái</label>
                          <select name="filter_trangthai_duan" id="filter_trangthai_duan">
                             <option value="Đã hoàn thành">Đã hoàn thành</option>
                             <option value="Đang triển khai">Đang triển khai</option>
                             <option value="Chưa hoàn thành">Chưa hoàn thành</option>
                             <option value="Đã hủy">Đã hủy</option>
                          </select>
                      <!--</div>-->
                      
                          <input type="text" class="input_field_time" placeholder="Thời gian bắt đầu" value="" id="filter_start_date" name="filter_start_date" />
                          <input type="text" class="input_field_time" placeholder="Thời gian kết thúc" value="" id="filter_end_date" name="filter_end_date" />
                  
                          <input type="submit" id="loc_duan" class="button action" value="Lọc"/>
                          <input type="submit" id="print_table" class="button action" value="In"/>
                      </form>    
                   </div>
                </div>
            </div>
            
            <div class="table" id="thongtinduan">
                <?php TT_Duan::tt_get_default_duan_info( array() ); ?>
            </div>
            
            <!-- thông tin ve nhân viên -->
            <div class="team_member"  >
                <h2><?php _e( "Nhân viên", "simple_plugin" ); ?></h2>
                <div class="tablenav top">
                   <div class="alignleft actions bulkactions">
                      <form action="" method="post">                
                      <!--<div class="filter_by_status">-->
                          <select name="filter_with_skill" id="filter_with_skill">
                             <option value="filter_nhanvien_project" selected="selected">Lọc theo kỹ năng</option>
                             <option value="filter_nhanvien_skill" >Lọc theo dự án</option>
                          </select>
                          <select name="filter_nhanvien_skill" id="filter_nhanvien_skill">
                             <option value="" selected="selected">Chọn kỹ năng</option>
                             <?php TT_KyNang::tt_render_list_kynang_showed(); ?>
                          </select>
                          <select name="filter_nhanvien_project" id="filter_nhanvien_project" class="dont_show">
                             <option value="" selected="selected">Chọn dự án</option>
                             <?php TT_Duan::tt_render_list_duan_showed(); ?>
                          </select>
                      <!--</div>-->
                      
                          <input type="submit" id="loc_nhanvien" class="button action" value="Lọc"/>
                          <input type="submit" id="print_table_nhanvien" class="button action" value="In"/>
                          <div style="float: none; clear: both;"></div>
                      </form>    
                   </div>
                </div>
            </div>
            
            <div class="table" id="thongtinnhanvien" style="margin-top:20px;">
                <?php TT_Nhanvien::tt_get_default_nhanvien_info( array( 'project'=> 5 ) ); ?>
            </div>
            
            
        </div>
<?php        
    }//End function tt_teamwork_callback()
    
    
    public static function tt_get_member_joined_project( $project_id ){
        global $wpdb;
        $table_name = $wpdb->prefix . 'chitiet_duan';
        $list_member = $wpdb->get_results( $wpdb->prepare( "SELECT id_nhanvien FROM $table_name WHERE id_duan = %d", $project_id ), ARRAY_A );
        
        if( is_array( $list_member ) && !empty( $list_member ) ){
            echo "<ol>";
            foreach( $list_member as $key=>$value ){
                echo "<li>";
                self::tt_get_nhanvien_name( $value['id_nhanvien'] );
                echo "</li>";
            }
            echo "</ol>";
            
        }else{
            echo "Không có thành viên nào tham gia dự án này!";
        }
    }
    
    public static function tt_get_nhanvien_name( $id_nhanvien ){
        global $wpdb;
        $table_name = $wpdb->prefix . "nhanvien";
        $nhanvien = $wpdb->get_results( $wpdb->prepare( "SELECT id_nhanvien,hoten FROM {$table_name} WHERE id_nhanvien = %d", $id_nhanvien ), ARRAY_A );
        
        if( is_array( $nhanvien ) && !empty( $nhanvien ) ){
            echo sprintf( '<a href="?page=new_nhanvien&id_nhanvien=%d">%s</a>', $nhanvien[0]['id_nhanvien'], $nhanvien[0]['hoten'] );
        }else{
            echo __( "Không có dữ liệu", "simple_plugin" );
        }
        
    }
    
    
    public static function tt_default_avatar( $gender = "Nam" ){
        if( $gender == "Nữ" || $gender == "nữ" || $gender == "nu" || $gender == "Nu" ){
            echo '<img src="' . TT_DIR_URL . 'assets/img/female-avatar.jpg'. '" />';
        }else{
            echo '<img src="' . TT_DIR_URL . 'assets/img/male-avatar.jpg'. '" />';
        }
    }
    
    public function register_setting_menu(){
        add_menu_page( __("TT Teamwork", "simple_plugin"), __("TT Teamwork","simple_plugin"), "activate_plugins", "tt_teamwork", array( $this, "tt_teamwork_callback" ) );
        
        add_submenu_page( "tt_teamwork", __( "Danh sách nhân viên","simple_plugin" ), __( "Danh sách nhân viên","simple_plugin" ), "activate_plugins", "ds_nhanvien", array( "TT_Nhanvien", "tt_page_nhanvien_callback" ) );
        add_submenu_page( "tt_teamwork", __( "Thêm mới nhân viên", "simple_plugin" ), __( "Thêm mới nhân viên", "simple_plugin" ), "activate_plugins", "new_nhanvien", array( "TT_Nhanvien", "tt_new_nhanvien_callback" ) );
        
        add_submenu_page( 'tt_teamwork', __( "Danh sách kỹ năng", "simple_plugin" ), __( "Danh sách kỹ năng", "simple_plugin" ), "activate_plugins", "ds_ky_nang", array( "TT_KyNang", "tt_kynang_page_callback" ) );
        add_submenu_page( 'tt_teamwork', __( "Thêm mới kỹ năng", "simple_plugin" ), __( "Thêm mới kỹ năng", "simple_plugin" ), "activate_plugins", "new_kynang", array( "TT_KyNang", "tt_new_kynang_callback" ) );
    
        add_submenu_page( 'tt_teamwork', __( "Danh sách đối tác", "simple_plugin" ), __( "Danh sách đối tác", "simple_plugin" ), "activate_plugins", "ds_doitac", array( "TT_Doitac", "tt_doitac_page_callback" ) );
        add_submenu_page( 'tt_teamwork', __( "Thêm mới đối tác", "simple_plugin" ), __( "Thêm mới đối tác", "simple_plugin" ), "activate_plugins", "new_doitac", array( "TT_Doitac", "tt_new_doitac_callback" ) );
    
        add_submenu_page( 'tt_teamwork', __( "Danh sách dự án", "simple_plugin" ), __( "Danh sách dự án", "simple_plugin" ), "activate_plugins", "ds_duan", array( "TT_Duan", "tt_duan_page_callback") );
        add_submenu_page( 'tt_teamwork', __( "Thêm mới dự án", "simple_plugin" ), __( "Thêm mới dự án", "simple_plugin" ), "activate_plugins", "new_duan", array( "TT_Duan", "tt_new_duan_page_callback") );
    
    }
    
    public function tt_load_languages(){
        load_plugin_textdomain( 'simple_plugin', false, dirname(plugin_basename(__FILE__) ));
    }
    
    public static function tt_selected( $select, $value ){
        if( isset( $select ) && !empty( $select ) ){
            if( $select == $value){
                echo 'selected="selected"';
            }
        }
    }
    
    public static function tt_checked( $check, $value ){
        if( isset( $check ) && !empty( $check ) ){
            if( $check == $value){
                echo 'checked="checked"';
            }
        }
    }
    
}

new TT_Teamwork();

function enqueue_script(){
    wp_enqueue_script( 'jquery_min', TT_DIR_URL . 'assets/js/jquery-1.12.3.min.js', array('jquery'), null, true );    
    wp_enqueue_script( 'jquery_ui', TT_DIR_URL . 'assets/js/jquery-ui.min.js', array('jquery'), null, true );
    wp_enqueue_script( 'jquer_choosen', TT_DIR_URL . 'assets/js/chosen.jquery.min.js', array('jquery'), null, true );
    wp_enqueue_script( 'jquery_function', TT_DIR_URL . 'assets/js/function.js', array('jquery'), null, true );
    wp_enqueue_script( 'jquery-carousel', TT_DIR_URL . 'assets/js/owl.carousel.min.js', array( 'jquery' ), null, true );
    wp_enqueue_script( 'jquery-jQuery.print.js', TT_DIR_URL . 'assets/js/jQuery.print.js', array( 'jquery' ), null, true );
    //jQuery.print.js
    
    wp_enqueue_style( 'jquery-ui-css', TT_DIR_URL . 'assets/css/jquery-ui.min.css', false, '' );
    wp_enqueue_style( 'jquery-ui-theme-css', TT_DIR_URL . 'assets/css/jquery-ui.theme.min.css', false, '' );
    wp_enqueue_style( 'jquery-ui-structure-css', TT_DIR_URL . 'assets/css/jquery-ui.structure.min.css', false, '' );
    wp_enqueue_style( 'choosen.min.js', TT_DIR_URL . 'assets/css/chosen.min.css', false, '' );
    wp_enqueue_style( 'fontawesome-css', TT_DIR_URL .'assets/css/font-awesome.min.css', false, '' );
    wp_enqueue_style( 'carousel-css', TT_DIR_URL . 'assets/css/owl.carousel.css', false, '' );
    wp_enqueue_style( 'custom-css', TT_DIR_URL . 'assets/css/custom.css', false, '' );
    
    wp_enqueue_media();
    
    wp_localize_script( 'jquery_function', 'tt_ajax_load_form', array(
        'ajaxurl'  => admin_url( 'admin-ajax.php' ),
        'security' => wp_create_nonce( 'tt_ajax_form' ),
	) );
}
add_action( 'admin_enqueue_scripts', 'enqueue_script' );
















