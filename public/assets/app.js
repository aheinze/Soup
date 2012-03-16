;(function($, $win, $doc){

	App = {
		
		_scripts: {},

		route: function(url){
			return Soup.base_route+"/"+trim(url,"/");
		},

		base_url: function(url){
			return Soup.base_url+"/"+trim(url,"/");
		},
		
		script: function(url, callback){
			
			var callback = callback || function(){};

			if (!this._scripts[url]) {

				this._scripts[url] = $.getScript(this.url(url));
			}

			this._scripts[url].done(callback);

			return this;
		},

		style: function(url){
			$("head:first").append('<link rel="stylesheet" type="text/css" href="'+this.url(url)+'" />');

			return this;
		},

		post: function(url, param1, param2, param3){
			return $.post(this.url(url), param1, param2, param3);
		},

		subscribe: function(event, callback){
			$doc.bind(event, callback);
		},

		unsubscribe: function(event, callback){
			$doc.unbind(event, callback);
		}
	};

	$win["App"] = App;

	// helper functions

	function trim (str, charlist) {
	    var whitespace, l = 0,
	        i = 0;
	    
	    str += '';
	 
	    if (!charlist) {
	        // default list
	        whitespace = " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
	    } else {
	        // preg_quote custom list
	        charlist += '';
	        whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
	    }
	 
	    l = str.length;
	    for (i = 0; i < l; i++) {
	        if (whitespace.indexOf(str.charAt(i)) === -1) {
	            str = str.substring(i);
	            break;
	        }
	    }
	 
	    l = str.length;
	    for (i = l - 1; i >= 0; i--) {
	        if (whitespace.indexOf(str.charAt(i)) === -1) {
	            str = str.substring(0, i + 1);
	            break;
	        }
	    }
	    return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
	}


})(jQuery, window, document);