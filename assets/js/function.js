jQuery(function ($){
  $.datepicker.regional["vi-VN"] =
	{
		closeText: "Đóng",
		prevText: "Trước",
		nextText: "Sau",
		currentText: "Hôm nay",
		monthNames: ["Tháng một", "Tháng hai", "Tháng ba", "Tháng tư", "Tháng năm", "Tháng sáu", "Tháng bảy", "Tháng tám", "Tháng chín", "Tháng mười", "Tháng mười một", "Tháng mười hai"],
		monthNamesShort: ["Một", "Hai", "Ba", "Bốn", "Năm", "Sáu", "Bảy", "Tám", "Chín", "Mười", "Mười một", "Mười hai"],
		dayNames: ["Chủ nhật", "Thứ hai", "Thứ ba", "Thứ tư", "Thứ năm", "Thứ sáu", "Thứ bảy"],
		dayNamesShort: ["CN", "Hai", "Ba", "Tư", "Năm", "Sáu", "Bảy"],
		dayNamesMin: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
		weekHeader: "Tuần",
		dateFormat: "yy/mm/dd",
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: "",
        showButtonPanel: true
	};

	$.datepicker.setDefaults($.datepicker.regional["vi-VN"]);
});


$(document).ready(function() {
    // Datepicker Popups calender to Choose date.
    
    $(function() {
        $("#thoigianbatdau").datepicker({
            minDate: 0,
            onSelect: function(selected) {
                $("#thoigianbatdau").datepicker("option","minDate", selected)
            }

        });
        $("#thoigianketthuc" ).datepicker({
            minDate: 0,
            onSelect: function(selected) {
                $("#thoigianketthuc").datepicker("option","minDate", selected)
            }
        });
        
        //hangmuc_tgbatdau, hangmuc_tgketthuc
        
        $(".hangmuc_tgbatdau").datepicker({
            minDate: 0,
            onSelect: function(selected) {
                $(".hangmuc_tgbatdau").datepicker("option","minDate", selected)
            }

        });
        $(".hangmuc_tgketthuc" ).datepicker({
            minDate: 0,
            onSelect: function(selected) {
                $(".hangmuc_tgketthuc").datepicker("option","minDate", selected)
            }
        });
        
        
        //hangmuc_congviec_tgbatdau,hangmuc_congviec_tgketthuc
        $("#hangmuc_congviec_tgbatdau").datepicker({
            minDate: 0,
            onSelect: function(selected) {
                $("#hangmuc_congviec_tgbatdau").datepicker("option","minDate", selected)
            }

        });
        $("#hangmuc_congviec_tgketthuc" ).datepicker({
            minDate: 0,
            onSelect: function(selected) {
                $("#hangmuc_congviec_tgketthuc").datepicker("option","minDate", selected)
            }
        });
        
        
        //$("#format").change(function() {
        //$("#datepicker").datepicker("option", "dateFormat", $(this).val());
        //});
        
        
        //for( var selector in config ) {
          //$( selector ).chosen( config[selector] );
        //}
        
        $("#chon_cac_duan").chosen({});
        $("#chon_cac_kynang").chosen({});
        
        $(".delete a").click( function(){
            $confirm = confirm( "Bạn có chắc chắn muốn xóa dữ liệu này. Hãy cẩn trọng, dữ liệu bị xóa sẽ không thể khôi phục lại!" );
            
            if( $confirm == true ){
                return true;
            }else{
                return false;
            }
        });
        
        function init_carousel(){
            $('.owl-carousel').each(function(){
                var owl = $(this);
                var config     = owl.data();
                var animateOut = owl.data('animateout');
                var animateIn  = owl.data('animatein');
                var slidespeed = owl.data('slidespeed');
                if(typeof animateOut != 'undefined' ){
                    config.animateOut = animateOut;
                }
                if(typeof animateIn != 'undefined' ){
                    config.animateIn = animateIn;
                }
                if(typeof (slidespeed) != 'undefined' ){
                    config.smartSpeed = slidespeed;
                }
                config.navText = ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
                owl.owlCarousel(config);
            });
        }
        
        init_carousel();
        
        
    });
});

/** custom function for upload image **/
jQuery(document).ready(function($){

  var mediaUploader;

  $('#upload-button').click(function(e) {
    e.preventDefault();
    // If the uploader object has already been created, reopen the dialog
      if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    // Extend the wp.media object
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: 'Chọn Avatar',
      button: {
        text: 'Chọn Avatar'
    }, multiple: false });

    // When a file is selected, grab the URL and set it as the text field's value
    mediaUploader.on('select', function() {
      attachment = mediaUploader.state().get('selection').first().toJSON();
      $('#avatar').val(attachment.url);
    });
    // Open the uploader dialog
    mediaUploader.open();
  });

});
/**
$(document).ready(function() {
    var max_fields = 20; //maximum input boxes allowed
    var wrapper = $("#items"); //Fields wrapper
    var add_button = $(".add_field_button"); //Add button ID
     
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div class="form-group"><label for="title">Author Email:</label>' +'<input class="form-control col-md-11" id="author_email" type="email" placeholder=""name="author"/>' + '<a href="#" class="remove_field"><i class="fa fa-times"></a></div>'); //add input box
        }
    });
     
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove field
    e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});


$(document).ready(function() {
    var removeButton = "<button id='remove'>Remove</button>";
    $('#add').click(function(e) {
        e.preventDefault();
        $('div.container:last').after($('div.container:first').clone());
        $('div.container:last').append(removeButton);

    });
    $('#remove').on('click', function() { //$('#remove').live('click', function() {
        $(this).closest('div.container').remove();
    });
});   
**/
//Chọn % hoan thanh
/**
function updateTextInput(val) {
    //document.getElementById('textInput').value=val; 
    
    //var percent = $(".percent_range_input").val();
    var percent = $(this).val();
    
    
    $(".percent_number").html( percent );
    $(".input_percent_number").val( percent ); 
    
    
}
**/
//accordion cho hạng mục
$(function(){
    $(document).on('click', '.accordionButton', function(){ //$('.accordionButton').click(function() {
        $('.accordionButton').removeClass('on');
        $('.accordionContent').slideUp('normal');
        $('.plusMinus').text('+');
        if($(this).next().is(':hidden') == true) {
            $(this).addClass('on');
            $(this).next().slideDown('normal');
            $(this).children('.plusMinus').text('-');
         } 
     });
    $('.accordionButton').mouseover(function() {
        $(this).addClass('over');
    }).mouseout(function() {
        $(this).removeClass('over');
    });
    $('.accordionContent').hide();
    
    //02
    $(document).on('click', '.accordionButton2', function(){
        $('.accordionButton2').removeClass('on');
        $('.accordionContent2').slideUp('normal');
        $('.plusMinus').text('+');
        if($(this).next().is(':hidden') == true) {
            $(this).addClass('on');
            $(this).next().slideDown('normal');
            $(this).children('.plusMinus').text('-');
         } 
     });
    $('.accordionButton2').mouseover(function() {
        $(this).addClass('over');
    }).mouseout(function() {
        $(this).removeClass('over');
    });
    $('.accordionContent2').hide(); 
    
    //Button xoa hang muc
    $(document).on('click', '.remove_hangmuc_button', function(e){
        e.preventDefault();
        
        var confirm_box = confirm( "Bạn thực sự muốn xóa." );
        if( confirm_box ){
            $(this).closest('.hangmuc_items_wrapper').remove();
        }else{
            return false;
        }
        
    }); 
    
    //Button xoa cong viec trong hang muc
    $(document).on('click', '.remove_hangmuc_cong_viec_button', function(e){
        e.preventDefault();
        var confirm_box = confirm( "Bạn thực sự muốn xóa." );
        if( confirm_box ){
            $(this).closest('.hangmuc_congviec_items_wrapper').remove();
        }else{
            return false;
        }
        
    }); 
    
    //Load text on keyup
    //$(".tenhangmuc").keyup(function(){//liveQuery
    $(document).on('keyup', '.tenhangmuc', function(){
        current_value = $(this).val();
        $(this).closest(".accordionContent").siblings().find('.span_ten_hangmuc').text( current_value );
        
    }); 
    $(document).on('keyup', '.hangmuc_tencongviec', function(){
        current_value = $(this).val();
        $(this).closest(".accordionContent2").siblings().find('.span_ten_hangmuc_congviec').text( current_value );
        
    }); 
    //load percent 
    $(document).on('change', '.percent_range_input', function(){
        var current_percent = $(this).val();
        $(this).parent().find('.percent_number').html( current_percent );
        $(this).parent().find('.input_percent_number').val( current_percent );
    });
    
    
    
});

//Date picker init
//Datepicker
function datepicker_init(){
    $(".hangmuc_tgbatdau").datepicker({
        minDate: 0,
        onSelect: function(selected) {
            $(".hangmuc_tgbatdau").datepicker("option","minDate", selected)
        }

    });
    $(".hangmuc_tgketthuc" ).datepicker({
        minDate: 0,
        onSelect: function(selected) {
            $(".hangmuc_tgketthuc").datepicker("option","minDate", selected)
        }
    });
}

function datepicker_init_cv(){
    //hangmuc_congviec_tgbatdau,hangmuc_congviec_tgketthuc
    $(".hangmuc_congviec_tgbatdau").datepicker({
        minDate: 0,
        onSelect: function(selected) {
            $(".hangmuc_congviec_tgbatdau").datepicker("option","minDate", selected)
        }

    });
    $(".hangmuc_congviec_tgketthuc" ).datepicker({
        minDate: 0,
        onSelect: function(selected) {
            $(".hangmuc_congviec_tgketthuc").datepicker("option","minDate", selected)
        }
    });
}

//disable nhan vien duoc chon lam quan ly du an
$(document).ready( function(){
    var id_quanly_duan;
    var id_quanly_duan_bandau;
    
    id_quanly_duan_bandau = $("select#id_quanly_duan").val();
    $("#nhanvien_thamgia_duan input:checkbox[value="+id_quanly_duan_bandau+"]").attr("disabled","disabled");
    
    $("select#id_quanly_duan").on( 'change', function(){
        id_quanly_duan = $(this).val();
        
        $("#nhanvien_thamgia_duan input:checkbox").removeAttr("disabled");
        $("#nhanvien_thamgia_duan input:checkbox[value="+id_quanly_duan+"]").attr("disabled","disabled");
        
    });
    
    
});

/**
//Lấy ra checkbox nhân viên cho từng công việc trong từng hang mục với các thành viên lấy từ danh sách các thành viên tham gia dự án.
$(document).ready( function(){
   $("#nhanvien_thamgia_duan input[type=\"checkbox\"]").click(function(){
        if( $(this).is(":checked") ){
            var id_nhanvien = $(this).val();
            var hoten_nhanvien = $(this).data('hoten');
            var checkbox_code = '<span><input type="checkbox" class="nhanvien_tg_hangmuc_congviec" name="nhanvien_tg_hangmuc_congviec[]" value="'+ id_nhanvien +'" />'+hoten_nhanvien +'</br></span>';
            
            $(".nhanvien_tg_hangmuc_congviec").each(function(){
                if( $(this).val() == id_nhanvien ){
                    $(this).parent().remove();
                }
            });
            
            $("#nhanvien_thamgia_hangmuc_congviec").append( checkbox_code );
            
        }else{
            var id_nhanvien = $(this).val();
            $(".nhanvien_tg_hangmuc_congviec").each(function(){
                if( $(this).val() == id_nhanvien ){
                    $(this).parent().remove();
                }
            });
        }
        
   });
});
**/
//tenhangmuc on keyup
$(document).ready(function(){
   $(".tenhangmuc").keyup(function(){//liveQuery
        current_value = $(this).val();
        $(this).closest(".accordionContent").siblings().find('.span_ten_hangmuc').text( current_value );
        
   }); 
});

//Load các form cong viec trong hang muc khi click
$(document).on('click','#button_hangmuc_them_congviec',function(e){
    e.preventDefault();
    
    var stt_hangmuc  = $(this).data('stt_hangmuc');
    var stt_congviec = $(this).data('stt_cv');
    var current_ob   = $(this);
    
    var data = {
        action      : 'tt_ajax_load_form_them_congviec_in_hangmuc',
        security    : tt_ajax_load_form.security,
        id_hangmuc  : stt_hangmuc,
        id_congviec : stt_congviec
        //productdata : productdata,
        //page        : page
    }
    
    var t = $(this);
    var content_div = $(this).parent().parent().next().find('#hangmuc_congviec_wrapper_all');
    $.post(ajaxurl, data, function(response){
       if( response.type == 'done' ){
            $( content_div ).append(response.data);
            datepicker_init_cv();
            current_ob.data('stt_cv', (stt_congviec+1) );
       }
    });
});


$(document).ready(function(e){
    $("#them-moi-hangmuc-btn").on('click', function(e){
       e.preventDefault();
       var stt = $(this).data('stt');
       var current_ob = $(this);
       var data2 = {
            action     : 'tt_ajax_load_form_them_hangmuc',
            security   : tt_ajax_load_form.security,
            stt        : stt
       }
       
       var content_div = $('#hangmuc_add_new');
       $.post( ajaxurl, data2, function(result){
            if( result.type == 'done' ){
                $( content_div ).append( result.data );
                datepicker_init();
                current_ob.data('stt', (stt+1) );
            }
       });
    });
});

