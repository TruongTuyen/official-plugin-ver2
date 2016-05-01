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