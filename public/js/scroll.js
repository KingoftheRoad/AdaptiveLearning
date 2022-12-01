
$(window).on('load', function() {
	var scholl_wrapper_width=0;
	var scholl_wrapper_width_div=0;
	setTimeout(function () {
		var idSetData=$(".test-tab.active").attr("href");
		if($(".test-tab.active").length!=0)
		{
			idSetData=idSetData.replace('#','');
			if($("#"+idSetData+"-table").length!=0)
			{	
				$("#"+idSetData+"-table").before('<div class="'+idSetData+'-scroll-wrapper scroll-wrapper"><div class="div1"></div></div>');
				
				scholl_wrapper_width=$("#"+idSetData+"-table_wrapper").width();
				$("."+idSetData+"-scroll-wrapper").width(scholl_wrapper_width);
				scholl_wrapper_width_div=$("#"+idSetData+"-table tbody").width();
				$("."+idSetData+"-scroll-wrapper .div1").width(scholl_wrapper_width_div);
		        $("."+idSetData+"-scroll-wrapper").scroll(function(){
		            $("#"+idSetData+"-table").scrollLeft($("."+idSetData+"-scroll-wrapper").scrollLeft());
		        });
		        $("#"+idSetData+"-table").scroll(function(){
		            $("."+idSetData+"-scroll-wrapper").scrollLeft($("#"+idSetData+"-table").scrollLeft());
		        });
		    }
		}
        
	},500);
	$(document).on("click", ".test-tab", function (e) {
		var idSetData=$(this).attr("href");
		idSetData=idSetData.replace('#','');
		if($("#"+idSetData+"-table").length!=0)
		{	
			if($("#"+idSetData+".tab-pane .scroll-wrapper").length==0)
			{
				setTimeout(function () {
					$("#"+idSetData+"-table").before('<div class="'+idSetData+'-scroll-wrapper scroll-wrapper"><div class="div1"></div></div>');
					
					scholl_wrapper_width=$("#"+idSetData+"-table_wrapper").width();
					$("."+idSetData+"-scroll-wrapper").width(scholl_wrapper_width);
					scholl_wrapper_width_div=$("#"+idSetData+"-table tbody").width();
					$("."+idSetData+"-scroll-wrapper .div1").width(scholl_wrapper_width_div);
			        $("."+idSetData+"-scroll-wrapper").scroll(function(){
			            $("#"+idSetData+"-table").scrollLeft($("."+idSetData+"-scroll-wrapper").scrollLeft());
			        });
			        $("#"+idSetData+"-table").scroll(function(){
			            $("."+idSetData+"-scroll-wrapper").scrollLeft($("#"+idSetData+"-table").scrollLeft());
			        });
			    },200);
		    }
	    }
	});
});