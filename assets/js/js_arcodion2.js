 $(document).ready(function(){
   $('.accordionButton2').click(function() {
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
});