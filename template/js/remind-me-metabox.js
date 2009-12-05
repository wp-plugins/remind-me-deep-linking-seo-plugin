jQuery(window).load(function(){

	var remindMeIframe = jQuery("#content_ifr").contents();
	//console.log(remindMeIframe);
	function remindMeProcess(){
		var url = "http://localhost/wordpress/wp-content/plugins/remind-me/remind-me.php";
		var i = 0;
		var catString = jQuery("#categories-all :checked").serialize();
		var selString = tinyMCE.activeEditor.selection.getContent();
		var tagString = jQuery("textarea.the-tags").val();
		var data = "action=remindMe&jt_ajax=1&jt_highlight="+selString+"&"+catString+"&jt_tags="+tagString;		
		jQuery.post(ajaxurl, data, function(response){
			var myString = response.substr(0,response.length-1);
			jQuery("#remind-me-list").html(myString);
		});
	
	}
	jQuery("#remind-me-refresh").click(function(event){
		event.preventDefault();
		remindMeProcess();
	});
	
	remindMeIframe.keypress(function(e){		
		var empty;
		var selString = tinyMCE.activeEditor.selection.getContent();
		if (selString == empty || selString == ""){
			return;
		} else {
			if (e.which == 247){
				e.preventDefault();
				remindMeProcess();		
			}
		}
		return;
	});
	
	jQuery(".remind-me-link").live("click", function(event){
		event.preventDefault();
		tinyMCE.execCommand('mceInsertLink',false, this.href);
	});
	
});