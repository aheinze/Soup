;(function($){
	
	// Tooltips
	$("[title]").tipsy({
		gravity: function(ele){
			var ele = $(this);

			if(ele.hasClass("tipleft")) return "e";
			if(ele.hasClass("tipright")) return "w";
			if(ele.hasClass("tiptop")) return "s";
			if(ele.hasClass("tipbottom")) return "n";

			return "n";
		},
		offset: 5
	});


})(jQuery);