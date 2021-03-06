/*!
 * Topbox
 * http://d-xp.com
 *
 * Copyright 2011, Artur Heinze
 * Licensed under the GPL Version 2.
 * https://github.com/jquery/jquery/blob/master/GPL-LICENSE.txt
 */

(function($){

    var $this = null;
    
    $.topbox = $this = {
        
        defaults: {
            'title'     : false,
            'closeOnEsc': true,
            'closeBtn'  : true,
            'theme'     : 'default',
            'height'    : 'auto',
            'width'     : 'auto',
            'speed'     : 500,
            'easing'    : 'swing',
            'buttons'   : false,
            
            //private
            '_status'   : true,

            //events
            'beforeShow'  : function(){},
            'beforeClose' : function(){},
            'onClose'     : function(){}
        },

        box: null,
        options: {},
        persist: false,
        
        show: function(content, options) {
            
            if(this.box) {this.clear();}
            
            this.options = $.extend({}, this.defaults, options);
			
            var tplDlg = '<div class="topbox-window '+$this.options.theme+'">';
                tplDlg+=  '<div class="topbox-closebutton"></div>';
                tplDlg+=  '<div class="topbox-title" style="display:none;"></div>';
                tplDlg+=  '<div class="topbox-content"><div class="topbox-innercontent"></div></div>';
                tplDlg+=  '<div class="topbox-buttonsbar"><div class="topbox-buttons"></div></div>';
                tplDlg+= '</div>';
            
            this.box = $(tplDlg);

            if(!this.options.closeBtn) {
                this.box.find(".topbox-closebutton").hide();
            } else {
                this.box.find(".topbox-closebutton").bind("click",function(){
                    $this.close();
                });   
            }
            
            if(this.options.buttons){
                
                var btns = this.box.find(".topbox-buttons");
                
                $.each(this.options.buttons, function(caption, fn){
                    
					$('<button type="button" class="topbox-button">'+caption+'</button>').bind("click", function(e){
						e.preventDefault();
						fn.apply($this);
                    }).appendTo(btns);
                });
            }else{
               this.box.find(".topbox-buttonsbar").hide(); 
            }
            
            if($this.options.height != 'auto'){
                this.box.find(".topbox-innercontent").css({
                  'height'    : $this.options.height,
                  'overflow-y': 'auto'
                });
            }
            
            if($this.options.width != 'auto'){
                this.box.find(".topbox-innercontent").css({
                  'width'     : $this.options.width,
                  'overflow-x': 'auto'
                });
            }
      
            this.setContent(content).setTitle(this.options.title);
			
			
            this.box.css({
                'opacity'   : 0,
                'visibility': 'hidden'
            })
            .appendTo("body");
			
			this.options.beforeShow.apply(this);
			
            this.box.css({
                'left' : ($(window).width()/2-$this.box.width()/2),
                'top'  : ((-1.5) * $this.box.height())
            }).css({
                'visibility': 'visible'
            }).animate({
                top: 0,
                opacity: 1
            }, this.options.speed, this.options.easing, function(){
            
                //focus
                if($this.box.find(":input:first").length) {
                    $this.box.find(":input:first").focus();
                }
            
            });
            
            $(window).bind('resize.topbox', function(){
                
				$this.center();
				
				$this.overlay.hide().css({
					width: $(document).width(),
					height: $(document).height()
				}).show();
            });
            
            // bind esc
            if(this.options.closeOnEsc){
                $(document).bind('keydown.topbox', function (e) {
                    if (e.keyCode === 27) { // ESC
                        e.preventDefault();
                        $this.close();
                    }
                });
            }
            
            this.showOverlay();
			
            return this;
        },
        
        close: function(){
            
            if(!this.box) {return;}
            
            if(this.options.beforeClose.apply(this)===false){
                return this;
            }
            
            this.overlay.fadeOut();
            
            this.box.animate({ 
                'top'  : ((-1.5) * $this.box.height()),
                'opacity': 0
            }, this.options.speed, this.options.easing, function(){
                $this.clear();
            });
			
			this.options.onClose.apply(this);

            return this;
        },

        blockUI: function(content, options) {
            
            var options = $.extend({
                closeBtn: false,
                closeOnEsc: false
            }, options);
            
            this.show(content, options);
        },
		
		'confirm': function(content, fn, options){

			var defaults = {
				title : $.topbox.i18n.Confirm,
				buttons: {}
			};

            defaults["buttons"][$.topbox.i18n.Ok] = function(){ fn.apply($this);};
            defaults["buttons"][$.topbox.i18n.Cancel] = function(){ this.close();};
			
			this.show(content, $.extend(defaults, options));
		
		},

        'input': function(message, fn, options){
            
            var defaults = {
                title : $.topbox.i18n.Input,
                value : "",
                buttons: {}
            };

            defaults["buttons"][$.topbox.i18n.Ok] = function(){
                        
                var val = this.box.find("input[type=text]:first").val();
                fn.apply($this,[val]);
            };

            defaults["buttons"][$.topbox.i18n.Cancel] = function(){ this.close();};

            var content = '<form class="topbox-input-form">';
                content+= '<div class="topbox-input-message">'+message+'</div>';
                content+= '<input type="text" class="topbox-input" style="width:100%;" />';
                content+= '</form>';

            content = $(content).bind("submit", function(e){
                e.preventDefault()

                $.topbox.box.find(".topbox-buttons button:first").trigger("click");
            });

            var options = $.extend(defaults, options);

            content.find("input[type=text]:first").val(options.value);

            this.show(content, options);
        
        },
		
		'alert': function(content, options){
			
            var defaults = {
                title : $.topbox.i18n.Alert,
                buttons: {}
            };

            defaults["buttons"][$.topbox.i18n.Ok] = function(){ this.close();};
            
            this.show(content, $.extend(defaults, options));
		},
        
        clear: function(){
            
            if(!this.box) {return;}
            
            if (this.persist) {
                this.persist.appendTo(this.persist.data("tb-persist-parent"));
                this.persist = false;
            }
            
            this.box.stop().remove();
            this.box = null;
            
            if(this.overlay){
                this.overlay.hide();
            }
            
            $(window).unbind('resize.topbox');
            $(document).unbind('keydown.topbox');
            
            return this;
        },
		
		center: function(){
			
			if(!this.box) {return;}
			
			this.box.css({
				'left': ($(window).width()/2-$this.box.width()/2)
			});
		},
        
        setTitle: function(title){ 
          
          if(!this.box) {return;}
          
          if(title){
            this.box.find(".topbox-title").html(title).show();
          }else{
            this.box.find(".topbox-title").html(title).hide();
          }
          
          return this;
        },

        setContent: function(content){ 
            
            if(!this.box) {return;}
            
            if (typeof content === 'object') {
				// convert DOM object to a jQuery object
				content = content instanceof jQuery ? content : $(content);
                
                if(content.parent().length) {
                    this.persist = content;
                    this.persist.data("tb-persist-parent", content.parent());
                }
			}
			else if (typeof content === 'string' || typeof content === 'number') {
				// just insert the data as innerHTML
				content = $('<div></div>').html(content);
			}
			else {
				// unsupported data type!
				content = $('<div></div>').html('SimpleModal Error: Unsupported data type: ' + typeof content);
			}
          
            content.appendTo(this.box.find(".topbox-innercontent").html(''));

            return this;
        },
        
        showOverlay: function(){
            
            if(!this.box) {return;}
            
            if(!this.overlay){
                if(!$("#topbox-overlay").length) {
                    $("<div>").attr('id','topbox-overlay').css({
                        top: 0,
                        left: 0,
                        position: 'absolute'
                    }).prependTo('body');
                                        
                }
                
                this.overlay = $("#topbox-overlay");
            }
            
            this.overlay.css({
                width: $(document).width(),
                height: $(document).height()
            }).show();
        }
    };

    $.topbox.i18n = {
        "Cancel" : "Cancel",
        "Ok"     : "Ok",
        "Confirm": "Please confirm",
        "Input"  : "Please input",
        "Alert"  : "Alert"   
    };

    $.fn.topbox = function() {

        var args    = arguments;
        var options = args[0] ? args[0] : {};

        return this.each(function() {
            
			var ele = $(this);
			
			ele.bind("click", function(e) {
				
				e.preventDefault();
				
				var target = String(ele.data('target') || ele.attr("href")),
					type   = ele.data("type") || "html";
				
				//html source
				if(target[0]=="#" || type=="html") {
					$.topbox.show($(target), options);
				}

			});
        });
    };
})(jQuery);