$(document).ready(function() {
	//START SLIDEBAR				
	$(window).scroll(function () {
	    var scrt = $(document).scrollTop();
	    if(scrt < 77){
	    	scrt = 77;
	    }else{
	    	scrt = scrt+20;
	    }
	    $('#slidebar').animate({top:scrt + 'px'},{duration: 500, queue: false}); 
	});
	//END SLIDEBAR
				
	//START NOTIFICATION CLOSE
	$('.notification .close').click(function(){
		$(this).parent().parent().animate({top: '25px', opacity: 0}, 500, function() {
			$(this).slideUp();
  		});
	});
	//END NOTIFICATION CLOSE
		
	//START COOL MENU
    $('.menu-item').each(function() {
    	var attr_class = $(this).find('h3').attr("class");
    	var set_height  = $(this).find('.menu-content').height();
    	
    	if(attr_class == 'close'){
    		$(this).find('.menu-overflow').height(0);
    		$(this).find('.menu-content').css('marginTop', -set_height);
    	}
  	});
  	
    $('.menu-item h3').click(function() {
    	var speed		= 500;
    	var set_height  = $(this).parent().find('.menu-content').height(); 
    	var	attr_class		= $(this).attr("class");
    	if(attr_class == 'close'){
    		$(this).removeClass('close');
    		$(this).addClass('open');
    		$(this).parent().find('.menu-overflow').animate({height: set_height + 'px'}, speed);
    		$(this).parent().find('.menu-content').animate({marginTop: '0'}, speed);
    		$(this).parent().find('.open img').attr('src', 'img/icon/m-close.png?v=1.1');
    	}
    	if(attr_class == 'open'){
    		$(this).removeClass('open');
    		$(this).addClass('close');
    		$(this).parent().find('.menu-overflow').animate({height: '0px'}, speed);
    		$(this).parent().find('.menu-content').animate({marginTop: '-' + set_height}, speed);
    		$(this).parent().find('.close img').attr('src', 'img/icon/m-open.png?v=1.1');
    		
    	}
    });
    
    $(".menu-content li a").hover(
    	function () {$(this).animate({paddingLeft: '25px'}, {queue:false, duration: 250 });}, 
		function () {$(this).animate({paddingLeft: '20px'}, {queue:false, duration: 250 });}
	);
    //END COOL MENU
    
    //START PREVIEW IMAGE
    $(".preview").hover(function(){
    	$("body").append('<div class="show_preview"><img src="' + this.rel + '" alt="preview" /></div>');
    	var doc_width		= $(document).width()/2;
    	var offset		 	= $(this).parent().offset();
		var img_width		= $(".show_preview").find('img').width()
		var pos_top 		= offset.top;
		if(doc_width < offset.left){
			pos_left	 	= offset.left-img_width;
		}else{
			pos_left	 	= offset.left+$(this).find('img').width();
		}
    	$(".show_preview").css("top", pos_top + "px").css("left", pos_left + "px").hide().fadeIn('fast');
    },function(){
    		$(".show_preview").remove();
	});
    //END PREVIEW IMAGE
    
    
    //START CHECK ALL
    $('.check_all').click(function() {
		$(this).parents('form').find('input:checkbox').attr('checked', $(this).is(':checked'));   
	});
	//END CHECK ALL
	
	//START TIPSY PLUGIN
	$('.tooltip').tipsy({fade: true});
	//END TIPSY PLUGIN    
});