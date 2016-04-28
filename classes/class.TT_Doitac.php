<?php
    class TT_Doitac extends WP_List_Table{
        
        
        public function __construct(){
            global $wpdb;
            
        }
        public function tt_new_doitac_callback(){
            
        }
        
        public function tt_doitac_page_callback(){
            
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
                    if( $value['loai'] == 'cá nhân' ){
                        printf( '<option value="%d" %s>%s</option>', $value['id_doitac'],$selected, $value['hoten'] );
                    }else{
                        printf( '<option value="%d" %s >%s</option>', $value['id_doitac'],$selected, $value['tendonvi'] );
                    }
                }
            }else{
                printf( '<option value="%d">%s</option>', 0, __( "Vui lòng thêm dữ liệu về đối tác trước khi chọn.", "simple_plugin" ) );
            }
            
            return ob_get_clean();
            
        } 
        
    }
    new TT_Doitac();