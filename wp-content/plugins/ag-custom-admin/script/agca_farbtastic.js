 function isDarker(rgb){
	var r=parseInt(rgb.substring(1,3));
	var g=parseInt(rgb.substring(3,5));
	var b=parseInt(rgb.substring(5));	
	
    r = r/255, g = g/255, b = b/255;
    var max = Math.max(r, g, b), min = Math.min(r, g, b);
    var h, s, v = max;

    var d = max - min;
    s = max == 0 ? 0 : d / max;

    if(max == min){
        h = 0; // achromatic
    }else{
        switch(max){
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }
        h /= 6;
    }
    //return [h, s, v];
	if(v < 0.5){
		return true;
	}else{
		return false;
	}
}

 jQuery(document).ready(function() { 

	jQuery('.pick_color_button').click(function(e){		
		jQuery('#'+jQuery(this).attr('alt')).focus();
		jQuery('#picker').css("left",e.pageX-100);
		jQuery('#picker').css("top",e.pageY-100);
		jQuery('#picker').show();
		
	});
    jQuery('.color_picker').click(function(){	
		jQuery('#picker').farbtastic('#'+jQuery(this).attr('id'));	
	});
	 jQuery('.color_picker').bind('keydown',function(){		
		 updateColor(jQuery(this).attr('id'),jQuery(this).val())
		 updateAllColors();
	});
	jQuery('#picker').mouseleave(function(){
		jQuery(this).hide();
	});	
	jQuery('#picker').hide();
	jQuery('.pick_color_button_clear').click(function(){	
		jQuery('#'+jQuery(this).attr('alt')).val("");
		jQuery('#'+jQuery(this).attr('alt')).attr("style","");
	});	
  });
  
  jQuery(document).ready(function() {
	jQuery('#picker').farbtastic(function(color){	
	if(document.activeElement.id !=""){	
		updateColor(document.activeElement.id,color);
			updateAllColors();
	}	   
	});
	jQuery(".color_picker").bind('keydown',function(){
		updateColor(jQuery(this).attr('id'),jQuery(this).val());	
		updateAllColors();
	});
  });