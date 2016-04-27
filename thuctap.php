<?php
/*
Plugin Name: Manage Teamwork
Description: Wordpress Plugin đơn giản quản lý thông tin một teamwork.
Plugin URI:  https:localhost
Author URI:  https:localhost
Author:      Truong Tuyen Anh
License:     Public Domain
Version:     1.0
Text Domain: simple_plugin
*/
define( TT_DIR_PATH, plugin_dir_path( __FILE__ ) ); //Lấy ra dường dẫn tuyệt đối tới thu muc của plugin này 
define( TT_DIR_URL, plugin_dir_url( __FILE__ ) ); //Lấy ra url của plugin này

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );//sử dụng file này để có thể dùng hàm dbDelta();
require_once TT_DIR_PATH . 'classes/class.TT_KyNang.php';  //Class Ky Nang xu ly thong tin liên quan den ky nang
require_once TT_DIR_PATH . 'classes/class.TT_Nhanvien.php';//Class Nhan Vien xu ly thong tin lien quan den nhan vien
require_once TT_DIR_PATH . 'classes/class.TT_Duan.php';//Class Du An xu ly thong tin lien quan den du an

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class TT_Teamwork{
    public $my_db_version = '1.0';
    
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
            CREATE TABLE {$wpdb->prefix}duan(
                id_duan BIGINT NOT NULL AUTO_INCREMENT,
                tenduan VARCHAR(225) NOT NULL,
                thoigianbatdau DATETIME NOT NULL,
                thoigianketthuc DATETIME NOT NULL,
                trangthai VARCHAR(225) NOT NULL,
                ghichu TEXT NULL,
                PRIMARY KEY (id_duan)   
            );
                         
            CREATE TABLE {$wpdb->prefix}nhanvien( 
                id_nhanvien BIGINT NOT NULL AUTO_INCREMENT , 
                hoten VARCHAR(225) NOT NULL , 
                namsinh VARCHAR(4) NOT NULL , 
                gioitinh VARCHAR(3) NOT NULL ,
                quequan VARCHAR(225) NOT NULL , 
                avatar VARCHAR(255) NOT NULL,
                PRIMARY KEY (id_nhanvien)
            );
            
            CREATE TABLE {$wpdb->prefix}kynang(
                id_kynang BIGINT NOT NULL AUTO_INCREMENT,
                tenkynang VARCHAR(225) NOT NULL,
                chuthich TEXT NULL,
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
        $sql = "DROP TABLE IF EXISTS `{$wpdb->prefix}duan`, `{$wpdb->prefix}nhanvien`, `{$wpdb->prefix}kynang`, `{$wpdb->prefix}chitiet_duan`, `{$wpdb->prefix}chitiet_kynang`";
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
        //Dữ liệu mẫu cho bảng duan
        $wpdb->insert( $wpdb->prefix . 'duan', array(
            'id_duan'            => 1,
            'tenduan'            => 'Website bán hàng cho công ty Incomtech',
            'thoigianbatdau'     => '2015-10-16',
            'thoigianketthuc'    => '2015-11-16',
            'trangthai'          => 'Đã hoàn thành', // Đã hoàn thành, Đang triển khai, Chưa hoàn thành, Đã hủy   
            'ghichu'             => 'Website bán hàng nội thất nhựa'   
        ));
        $wpdb->insert( $wpdb->prefix . 'duan', array(
            'id_duan'            => 2,
            'tenduan'            => 'Website tin tức nguoivietnews.net',
            'thoigianbatdau'     => '2015-11-10',
            'thoigianketthuc'    => '2015-12-10',
            'trangthai'          => 'Đã hoàn thành',
            'ghichu'             => 'website tin tức cho người việt'   
        ));
        
        $wpdb->insert( $wpdb->prefix . 'duan', array(
            'id_duan'            => 3,
            'tenduan'            => 'Website công ty Otvina',
            'thoigianbatdau'     => '2016-01-16',
            'thoigianketthuc'    => '2016-11-16',
            'trangthai'          => 'Đang triển khai', // Đã hoàn thành, Đang triển khai, Chưa hoàn thành, Đã hủy   
            'ghichu'             => null   
        ));
        $wpdb->insert( $wpdb->prefix . 'duan', array(
            'id_duan'            => 4,
            'tenduan'            => 'Website giới thiệu sách',
            'thoigianbatdau'     => '2016-03-03',
            'thoigianketthuc'    => '2016-12-10',
            'trangthai'          => 'Đang triển khai',
            'ghichu'             => null 
        ));
        
        $wpdb->insert( $wpdb->prefix . 'duan', array(
            'id_duan'            => 5,
            'tenduan'            => 'Website giới thiệu khóa học',
            'thoigianbatdau'     => '2015-10-16',
            'thoigianketthuc'    => '2015-11-16',
            'trangthai'          => 'Chưa hoàn thành', // Đã hoàn thành, Đang triển khai, Chưa hoàn thành, Đã hủy   
            'ghichu'             => 'Website giới thiệu khóa học cho trẻ: kidscourse.vn -- chưa hoàn thành do yêu cầu của khách hàng'   
        ));
        $wpdb->insert( $wpdb->prefix . 'duan', array(
            'id_duan'            => 6,
            'tenduan'            => 'Website bất động sản',
            'thoigianbatdau'     => '2015-10-16',
            'thoigianketthuc'    => '2015-11-16',
            'trangthai'          => 'Chưa hoàn thành', // Đã hoàn thành, Đang triển khai, Chưa hoàn thành, Đã hủy   
            'ghichu'             => 'Website bất động sản: nhadatphongthuy.vn -- chưa hoàn thành do yêu cầu của khách hàng'   
        ));
        $wpdb->insert( $wpdb->prefix . 'duan', array(
            'id_duan'            => 7,
            'tenduan'            => 'Website giới thiệu khóa học',
            'thoigianbatdau'     => '2015-11-10',
            'thoigianketthuc'    => '2015-12-10',
            'trangthai'          => 'Đã hủy',
            'ghichu'             => 'website giới thiệu các khóa học đồ họa của công ty truyền thông Grouple -- đã hủy do bên B phá hợp đồng'   
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
            'tenkynang'         => 'HTML',
            'chuthich'          => 'Thành thạo HTML',
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 2,
            'tenkynang'         => 'CSS',
            'chuthich'          => 'Thành thạo CSS',
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 3,
            'tenkynang'         => 'Javascript, jQuery',
            'chuthich'          => 'Thành thạo Javascript, jQuery',
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 4,
            'tenkynang'         => 'PHP, MySQL',
            'chuthich'          => 'Thành thạo ngôn ngữ PHP và hệ quản trị CSDL MySQL',
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 5,
            'tenkynang'         => 'Photoshop',
            'chuthich'          => 'Sử dụng thành thạo Photoshop',
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 6,
            'tenkynang'         => 'Android',
            'chuthich'          => 'Phát triển Android chuyên nghiệp',
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 7,
            'tenkynang'         => 'iOS',
            'chuthich'          => 'Phát triển iOS chuyên nghiệp',
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 8,
            'tenkynang'         => 'PHP Framework CodeIgniter',
            'chuthich'          => 'Sử dụng thành thạo framework CodeIgniter',
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 9,
            'tenkynang'         => 'PHP Framework Laravel',
            'chuthich'          => 'Sử dụng thành thạo framework Laravel',
        ));
        $wpdb->insert( $wpdb->prefix . 'kynang', array(
            'id_kynang'         => 10,
            'tenkynang'         => 'WordPress',
            'chuthich'          => 'Phát triển theme và plugin WordPress chuyên nghiệp',
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
            <div class="team_member"  >
                <?php $num_member = self::tt_count_total_members(); ?>
                <h2><?php _e( 'Tất cả các thành viên: ('. $num_member .')', 'simple_plugin' ); ?></h2>
                <?php self::tt_get_team_member(); ?>
            </div>
            
            <div class="team_projects">
                <?php $num_project = self::tt_count_total_projects(); ?>
                <h2><?php _e( "Tất cả các dự án: ({$num_project})", "simple_plugin" ); ?></h2>
                <?php self::tt_get_project_status(); ?>
            </div>
            
            
        </div>
<?php        
    }//End function tt_teamwork_callback()
    
    public static function tt_count_total_members(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nhanvien';
        $all_member = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );
        
        if( is_numeric( $all_member ) ){
            return $all_member;
        }else{
            return 0;
        }
    }
    
    public static function tt_count_total_projects(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'duan';
        $all_projects = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );
        
        if( is_numeric( $all_projects ) ){
            return $all_projects;
        }else{
            return 0;
        }
    }
    
    public static function tt_get_team_member(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nhanvien';
        
        $all_member =  $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
        
        if( is_array( $all_member ) && !empty( $all_member ) ){
            echo '<div class="team_member owl-carousel" data-nav="true" data-autoplay="false" data-dots="false" data-loop="true" data-margin="10" data-responsive=\'{"0":{"items":4},"600":{"items":4},"1000":{"items":4}}\' >';
            foreach( $all_member as $key=>$value ){ 
                $nhanvien_kynang = TT_Nhanvien::tt_get_selected_detail_kynang( $value['id_nhanvien'] );
                $nhanvien_duan   = TT_Nhanvien::tt_get_selected_detail_duan( $value['id_nhanvien'] );
                
        ?>
                <div class="each_member">
                    <div class="member_avatar">
                        <?php if( !empty( $value['avatar'] ) ){
                            echo '<img src="'. $value['avatar'] .'" />';
                        }else{
                            self::tt_default_avatar( $value['gioitinh'] );
                        } ?>
                    </div>
                    <div class="member_info">
                        <?php if( $value['hoten'] ): ?><p class="member-name"><strong>Họ Tên:</strong> <?php echo '<a href="?page=new_nhanvien&id_nhanvien=' . $value['id_nhanvien'] . '">' . esc_html( $value['hoten'] ) . '</a>'; ?></p><?php endif; ?>
                        <?php if( $value['namsinh'] ): ?><p class="member-dateofbirth"><strong>Năm sinh:</strong> <?php echo esc_html( $value['namsinh'] ); ?></p><?php endif; ?>
                        <?php if( $nhanvien_kynang ): ?><p class="member-skills"><strong>Các kỹ năng:</strong> <?php echo $nhanvien_kynang; ?></p><?php endif; ?>
                        <?php if( $nhanvien_duan ): ?><p class="member-projects"><strong>Các dự án:</strong> <?php echo $nhanvien_duan; ?></p><?php endif; ?>
                        
                    </div>
                    <div class="clearfix"></div>
                </div>
<?php                
            }
            echo "</div>";
        } 
        
    }
    
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
    public static function tt_get_project_status(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'duan';
        
        $all_member =  $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
        
        if( is_array( $all_member ) && !empty( $all_member ) ){
            echo '<div class="team_project owl-carousel" data-nav="true" data-autoplay="false" data-dots="false" data-loop="true" data-margin="10" data-responsive=\'{"0":{"items":4},"600":{"items":4},"1000":{"items":4}}\' >';
            foreach( $all_member as $key=>$value ){
        ?>
                <div class="each_member">
                    <div class="prject_preview">
                        <img src="<?php echo esc_url( TT_DIR_URL . '/assets/img/project_thumb.jpg' ); ?>" />
                    </div>
                    <div class="project_info">
                        <?php if( $value['tenduan'] ): ?><p class="project-name"><strong>Tên dự án:</strong> <?php echo '<a href="?page=new_duan&id_duan=' . $value['id_duan'] . '">' . esc_html( $value['tenduan'] ) . '</a>'; ?></p><?php endif; ?>
                        <?php if( $value['thoigianbatdau'] ): ?><p class="start_time"><strong>Thời gian:</strong> <?php echo  esc_html( date( 'd-m-Y', strtotime( $value['thoigianbatdau'] ) ) ); ?> <i class="fa fa-long-arrow-right"></i> <?php if( !empty( $value['thoigianketthuc'] ) ){ echo date( 'd-m-Y', strtotime( $value['thoigianketthuc'] ) ); } ?></p><?php endif; ?>
                        
                        <?php if( $value['trangthai'] ): ?><p class="projects-status"><strong>Trạng thái:</strong> <?php echo $value['trangthai']; ?></p><?php endif; ?>
                        <p class="joined_member">
                            <strong>Các thành viên tham gia:</strong>
                            <?php $list_ids = self::tt_get_member_joined_project( $value['id_duan'] ); ?>
                        </p>
                    </div>
                    <div class="clearfix"></div>
                </div>
<?php                
            }
            echo "</div>";
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
    
    wp_enqueue_style( 'jquery-ui-css', TT_DIR_URL . 'assets/css/jquery-ui.min.css', false, '' );
    wp_enqueue_style( 'jquery-ui-theme-css', TT_DIR_URL . 'assets/css/jquery-ui.theme.min.css', false, '' );
    wp_enqueue_style( 'jquery-ui-structure-css', TT_DIR_URL . 'assets/css/jquery-ui.structure.min.css', false, '' );
    wp_enqueue_style( 'choosen.min.js', TT_DIR_URL . 'assets/css/chosen.min.css', false, '' );
    wp_enqueue_style( 'fontawesome-css', TT_DIR_URL .'assets/css/font-awesome.min.css', false, '' );
    wp_enqueue_style( 'carousel-css', TT_DIR_URL . 'assets/css/owl.carousel.css', false, '' );
    wp_enqueue_style( 'custom-css', TT_DIR_URL . 'assets/css/custom.css', false, '' );
    
    wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'enqueue_script' );
















