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
        
        $("#hangmuc_tgbatdau").datepicker({
            minDate: 0,
            onSelect: function(selected) {
                $("#hangmuc_tgbatdau").datepicker("option","minDate", selected)
            }

        });
        $("#hangmuc_tgketthuc" ).datepicker({
            minDate: 0,
            onSelect: function(selected) {
                $("#hangmuc_tgketthuc").datepicker("option","minDate", selected)
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
function updateTextInput(val) {
    //document.getElementById('textInput').value=val; 
    var percent = $("#percent_range_input").val();
    $("#percent_number").html( percent );
    $("#input_percent_number").val( percent );
}

//accordion cho hạng mục
$(document).ready(function() {
    $('.accordionButton').click(function() {
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
    
});


//Button xóa hạng muc
$(document).ready(function(){
   $(".remove_hangmuc_button").on('click', function(e){
        e.preventDefault();
        $(this).closest('.hangmuc_items_wrapper').remove();
   }); 
});

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

//Button button_hangmuc_them_congviec
$(document).ready( function(){
   $("#button_hangmuc_them_congviec").click(function(e){
        e.preventDefault();
        
   });
});

//tenhangmuc on keyup
$(document).ready(function(){
   $(".tenhangmuc").keyup(function(){
        current_value = $(this).val();
        var current_node = $(this).closest(".accordionContent");
        //$(this).closest(".accordionButton").find('.span_ten_hangmuc').text( current_value );
        console.log(current_node);
   }); 
});