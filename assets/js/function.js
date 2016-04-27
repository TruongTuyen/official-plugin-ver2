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