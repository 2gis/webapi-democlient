$(document).ready(function(){

    $('.sorting a.pseudo').live('click', function(){
        $(this).parent().parent().find('.active').removeClass('active');
        $(this).parent().addClass('active');
    });

    $('.show-weekly-wh').click(function(){
        $(this).parent(".wh-today").hide().next('.wh-week').show();
        $(this).parent().parent(".wh-today").hide().next('.wh-week').show();
    });

    $('.show-today-wh').click(function(){
        $(this).parent(".wh-week").hide().prev('.wh-today').show();
        $(this).parent().parent(".wh-week").hide().prev('.wh-today').show();
    });

});