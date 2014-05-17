var afterFormClickCreateJson = true;
var editingButtonNow = false;
var agca_ajax_url = agca_global_plugin_url + "ajax.php";
function booleanToChecked(bool){
    if(bool == 'true'){	
        return 'checked="checked"';
    }else if(bool == 'checked'){	
        return 'checked="checked"';
    }
}

function agcaLog(text){
	console.log(text);
}

function agcaDebug(text){
	if(agca_debug){
		console.log('- '+text);
	}
}

function agcaDebugObj(obj){
	if(agca_debug){
		console.log(obj);
	}
}

function hideShowSubmenus(index){
	
    var finish = false;
    var	found = false;
    jQuery('#ag_edit_adminmenu td').each(function(){	
        if(jQuery('#ag_edit_adminmenu td').index(jQuery(this)) >= index && (finish == false)){	
         //  jQuery(this).find('.agcaMenuEditorPlusMinus').show();
         var caller = jQuery('#ag_edit_adminmenu td:eq(' + index + ')');
         jQuery(caller).find('.agcaMenuEditorPlusMinus .minus').toggle();
         jQuery(caller).find('.agcaMenuEditorPlusMinus .plus').toggle();
            if(jQuery(this).hasClass('ag_admin_menu_child')){
                jQuery(this).parent().toggleClass('noclass');
                found = true;
            }
            if((jQuery('#ag_edit_adminmenu td').index(jQuery(this)) > index) && jQuery(this).hasClass('ag_admin_menu_parent')){			
                finish = true;                 
            }
        }
    });
    /*FOCUS*/
    if(!jQuery('#ag_edit_adminmenu td').eq((index+2)).parent().hasClass('noclass') && (found == true)){
        jQuery('#ag_edit_adminmenu td').eq((index+2)).find('a').trigger('focus');		
    };	
}

/*
	Makes admin edit page pretty grouping items and submenus, and adding fancy interactions
*/
function prettyEditMenuPage(){
    jQuery('#ag_edit_adminmenu td').each(function(){
        if(jQuery(this).hasClass('ag_admin_menu_child')){
            jQuery(this).parent().addClass('noclass');
        };
    });
    jQuery('#ag_edit_adminmenu td').each(function(){
        if(jQuery(this).hasClass('ag_admin_menu_parent')){
            jQuery(this).parent().css('background-color','#d8eAd8');
            jQuery(this).bind('click',function(evt){	
                if(evt.target.className == 'ag_admin_menu_parent'){
                    hideShowSubmenus(jQuery('#ag_edit_adminmenu td').index(this));
                }			
            });
        };
    });
    jQuery('#ag_edit_adminmenu td > a').bind('click',function(){	
        jQuery(this).parent().click();		
    //jQuery(this).parent().focus();
    });
};

function createEditMenuPage(checkboxes,textboxes){		
    /*Create menu page in AGCA settings*/	
	
    //console.log(textboxes);
    var counter = 0;
    var TBlength = textboxes.length;
    if(textboxes==""){
        TBlength = 9999999;
    }
	
    var topElement="";
    jQuery('ul#adminmenu li').each(function(){  
        if(!jQuery(this).hasClass("wp-menu-separator") && !jQuery(this).hasClass("wp-menu-separator-last") && !jQuery(this).hasClass("ag-custom-button") && (counter < TBlength )){	
			
            //if subelement
            if(jQuery(this).parent().parent().hasClass('wp-submenu')){	
                subElement = jQuery(this).find('a').text();
                //console.log(jQuery(this));
                //console.log(subElement);
                var isHidden = "";
                var sub_item_text_value;
                if(textboxes ==""){	
                    sub_item_text_value = subElement;
                }else{
                    sub_item_text_value = textboxes[counter][1];
                    isHidden = checkboxes[counter][1];
                }	
                jQuery('#ag_edit_adminmenu').append("<tr><td class='ag_admin_menu_child'><div style=\"float:left\"><a tabindex=\"-1\" href=\"javascript:void(0)\" style=\"font-weight:bold;\"title=\""+topElement+" submenu: "+subElement+"\"><span style=\"font-weight:normal\">submenu: </span>"+subElement+"</a></div><div style=\"float:right\"><input type=\"checkbox\" title=\"Remove "+topElement+" submenu: "+subElement+" from menu\" class=\""+subElement+" agca-checkbox\" "+booleanToChecked(isHidden)+"  name=\"ag_edit_adminmenu_item_sub_"+counter+"\" /></div></td><td class='ag_admin_menu_child2' ><input type=\"text\" title=\"Rename submenu item "+subElement+" with this value\" class=\""+subElement+"\" size=\"47\" value=\""+sub_item_text_value+"\" name=\"ag_edit_adminmenu_item_sub_"+counter+"\" /></td></tr>");
            }
            //if top element
            else{
                topElement = jQuery(this).children('a').clone().children().remove().end().text();		
                topElement = jQuery.trim(topElement);
                var top_item_text_value;
                var isHidden = "";
                if(textboxes ==""){	
                    top_item_text_value = topElement;
                }else{
                    top_item_text_value = textboxes[counter][1];
                    isHidden = checkboxes[counter][1];					
                }	
                jQuery('#ag_edit_adminmenu').append("<tr><td class='ag_admin_menu_parent'><br /><span class=\"agcaMenuEditorPlusMinus\"><span class=\"plus\">+</span><span class=\"minus\">-</span></span><a tabindex=\"0\" href=\"javascript:void(0)\" >" + topElement +"</a><div style=\"float:right\"><input title=\"Remove "+topElement+" from menu\" class=\""+jQuery(this).attr("id")+" agca-checkbox\" type=\"checkbox\" "+booleanToChecked(isHidden)+" name=\"ag_edit_adminmenu_item_top_"+counter+"\" /></div></td><td class='ag_admin_menu_parent2' ><input title=\"Rename "+topElement+" with this value\" type=\"text\" class=\""+jQuery(this).attr("id")+"\" size=\"47\" value=\""+top_item_text_value+"\" name=\"ag_edit_adminmenu_item_top_"+counter+"\" /></td></tr>");
            }			
            counter++;
        }		
    });
	 
    //console.log(checkboxes.replace('<-TOP->','')+"|"+textboxes.replace('<-TOP->',''));	  
    prettyEditMenuPage();   
    agcaChangeCheckBoxStyles();
}

function createEditMenuPageV32(checkboxes,textboxes){		
    /*Create menu page in AGCA settings*/	
	
    var counter = 0;
    var TBlength = textboxes.length;
    if(textboxes==""){
        TBlength = 9999999;
    }
	
    var topElement="";
    jQuery('ul#adminmenu li').each(function(){  
        if(!jQuery(this).hasClass("wp-menu-separator") && !jQuery(this).hasClass("wp-menu-separator-last") && !jQuery(this).hasClass("ag-custom-button") && (jQuery(this).attr('id') !="collapse-menu") && (counter < TBlength )){	
			
            //if subelement
            if(jQuery(this).parent().parent().parent().hasClass('wp-submenu')){	
                subElement = jQuery(this).find('a').text();
                //console.log(jQuery(this));
                //console.log(subElement);
                var isHidden = "";
                var sub_item_text_value;
                if(textboxes ==""){	
                    sub_item_text_value = subElement;
                }else{
                    sub_item_text_value = textboxes[counter][1];
                    isHidden = checkboxes[counter][1];					
                }	
                jQuery('#ag_edit_adminmenu').append("<tr><td class='ag_admin_menu_child'><div style=\"float:left\"><a tabindex=\"-1\" href=\"javascript:void(0)\" style=\"font-weight:bold;\"title=\""+topElement+" submenu: "+subElement+"\"><span style=\"font-weight:normal\">submenu: </span>"+subElement+"</a></div><div style=\"float:right\"><input type=\"checkbox\" title=\"Remove "+topElement+" submenu: "+subElement+" from menu\" class=\""+subElement+" agca-checkbox\" "+booleanToChecked(isHidden)+"  name=\"ag_edit_adminmenu_item_sub_"+counter+"\" /></div></td><td class='ag_admin_menu_child2' ><input type=\"text\" title=\"Rename submenu item "+subElement+" with this value\" class=\""+subElement+"\" size=\"47\" value=\""+sub_item_text_value+"\" name=\"ag_edit_adminmenu_item_sub_"+counter+"\" /></td></tr>");
            }
            //if top element
            else{
                topElement = jQuery(this).children('a').clone().children().remove().end().text();		
                topElement = jQuery.trim(topElement);
                var top_item_text_value;
                var isHidden = "";
                if(textboxes ==""){	
                    top_item_text_value = topElement;
                }else{
                    top_item_text_value = textboxes[counter][1];
                    isHidden = checkboxes[counter][1];
                }	
                jQuery('#ag_edit_adminmenu').append("<tr><td class='ag_admin_menu_parent'><br /><span class=\"agcaMenuEditorPlusMinus\"><span class=\"plus\">+</span><span class=\"minus\">-</span></span><a tabindex=\"0\" href=\"javascript:void(0)\" >" + topElement +"</a><div style=\"float:right\"><input title=\"Remove "+topElement+" from menu\" class=\""+jQuery(this).attr("id")+" agca-checkbox\" type=\"checkbox\" "+booleanToChecked(isHidden)+" name=\"ag_edit_adminmenu_item_top_"+counter+"\" /></div></td><td class='ag_admin_menu_parent2' ><input title=\"Rename "+topElement+" with this value\" type=\"text\" class=\""+jQuery(this).attr("id")+"\" size=\"47\" value=\""+top_item_text_value+"\" name=\"ag_edit_adminmenu_item_top_"+counter+"\" /></td></tr>");
            }			
            counter++;
        }else if(jQuery(this).attr('id') =="collapse-menu"){
            jQuery(this).remove();
        }
    });
	 
    //console.log(checkboxes.replace('<-TOP->','')+"|"+textboxes.replace('<-TOP->',''));	  
    prettyEditMenuPage();
    
    var parent = null;
    var subs = 0;
    jQuery('#ag_edit_adminmenu tr').each(function(){
        if(jQuery(this).find('td').hasClass('ag_admin_menu_parent')){
            //jQuery(this).css('color','#ffffff');
            if(parent != null){
                if(subs == 0){
                    jQuery(parent).find('.agcaMenuEditorPlusMinus').html('<span>&nbsp;&nbsp;&nbsp;</span>');
                }
            }
            subs = 0;
            parent = jQuery(this);
        }else{
            subs++;
        }        
      
    });
	agcaChangeCheckBoxStyles();
}

function createEditMenuPageV35(checkboxes,textboxes){		
    /*Create menu page in AGCA settings*/	
	//console.log(checkboxes);
    var counter = 0;
    var TBlength = textboxes.length;
    if(textboxes==""){
        TBlength = 9999999;
    }
	
    var topElement="";
    jQuery('ul#adminmenu li').each(function(){  
        if(!jQuery(this).hasClass("wp-menu-separator") && !jQuery(this).hasClass("wp-menu-separator-last") && !jQuery(this).hasClass("ag-custom-button") && (jQuery(this).attr('id') !="collapse-menu") && (counter < TBlength )){	
			
                //console.log(jQuery(this).text());        
            //if subelement
            if(jQuery(this).parent().hasClass('wp-submenu') && !jQuery(this).hasClass('wp-submenu-head')){	
                //console.log(jQuery(this).find('a').text());
                var el = jQuery(this).find('a').clone();
                el.find('span').remove();               
                subElement = el.html();
                //console.log(jQuery(this));
               //console.log(subElement);
                var isHidden = "";
                var sub_item_text_value;
                if(textboxes ==""){	
                    sub_item_text_value = subElement;
                }else{
                    sub_item_text_value = textboxes[counter][1];
                    isHidden = checkboxes[counter][1];					
                }	
                jQuery('#ag_edit_adminmenu').append("<tr><td class='ag_admin_menu_child'><div style=\"float:left\"><a tabindex=\"-1\" href=\"javascript:void(0)\" style=\"font-weight:bold;\"title=\""+topElement+" submenu: "+subElement+"\"><span style=\"font-weight:normal\">submenu: </span>"+subElement+"</a></div><div style=\"float:right\"><input type=\"checkbox\" title=\"Remove "+topElement+" submenu: "+subElement+" from menu\" class=\""+subElement+" agca-checkbox\" "+booleanToChecked(isHidden)+"  name=\"ag_edit_adminmenu_item_sub_"+counter+"\" /></div></td><td class='ag_admin_menu_child2' ><input type=\"text\" title=\"Rename submenu item "+subElement+" with this value\" class=\""+subElement+"\" size=\"47\" value=\""+agca_escapeHTMLChars(sub_item_text_value)+"\" name=\"ag_edit_adminmenu_item_sub_"+counter+"\" /></td></tr>");
                counter++;
            }
            //if top element
            else if(jQuery(this).parent().attr('id') == 'adminmenu'){
                //console.log(jQuery(this).attr('id'));
                var el = jQuery(this).find('.wp-menu-name').clone();
                    el.find('span').remove();		                
                topElement = el.text();
                //console.log(topElement);
                var top_item_text_value;
                var isHidden = "";
                if(textboxes ==""){	
                    top_item_text_value = topElement;
                }else{
                    top_item_text_value = textboxes[counter][1];					
                    isHidden = checkboxes[counter][1];
                }					
                jQuery('#ag_edit_adminmenu').append("<tr><td class='ag_admin_menu_parent'><br /><span class=\"agcaMenuEditorPlusMinus\"><span class=\"plus\">+</span><span class=\"minus\">-</span></span><a tabindex=\"0\" href=\"javascript:void(0)\" >" + topElement +"</a><div style=\"float:right\"><input title=\"Remove "+topElement+" from menu\" class=\""+jQuery(this).attr("id")+" agca-checkbox\" type=\"checkbox\" "+booleanToChecked(isHidden)+" name=\"ag_edit_adminmenu_item_top_"+counter+"\" /></div></td><td class='ag_admin_menu_parent2' ><input title=\"Rename "+topElement+" with this value\" type=\"text\" class=\""+jQuery(this).attr("id")+"\" size=\"47\" value=\""+agca_escapeHTMLChars(top_item_text_value)+"\" name=\"ag_edit_adminmenu_item_top_"+counter+"\" /></td></tr>");
                counter++;
            }			
            
        }else if(jQuery(this).attr('id') =="collapse-menu"){
            jQuery(this).remove();
        }
    });
	 
    //console.log(checkboxes.replace('<-TOP->','')+"|"+textboxes.replace('<-TOP->',''));	  
    prettyEditMenuPage();
    
    var parent = null;
    var subs = 0;
    jQuery('#ag_edit_adminmenu tr').each(function(){
        if(jQuery(this).find('td').hasClass('ag_admin_menu_parent')){
            //jQuery(this).css('color','#ffffff');
            if(parent != null){
                if(subs == 0){
                    jQuery(parent).find('.agcaMenuEditorPlusMinus').html('<span>&nbsp;&nbsp;&nbsp;</span>');
                }
            }
            subs = 0;
            parent = jQuery(this);
        }else{
            subs++;
        }       
      
    });
	agcaChangeCheckBoxStyles();
}

function showHideSection(text) {	
    switch(text)
    {
        case 'Admin Bar':
            jQuery('#section_admin_bar').show();
            jQuery('#section_admin_bar .section_title').trigger('focus');						
            break;
        case 'Admin Footer':
            jQuery('#section_admin_footer').show();
            jQuery('#section_admin_footer .section_title').trigger('focus');
            break;
        case 'Dashboard Page':
            jQuery('#section_dashboard_page').show();
            jQuery('#section_dashboard_page .section_title').trigger('focus');
            break;
        case 'Login Page':
            jQuery('#section_login_page').show();
            jQuery('#section_login_page .section_title').trigger('focus');
            break;
        case 'Admin Menu':
            jQuery('#section_admin_menu').show();
            jQuery('#section_admin_menu .section_title').trigger('focus');
            break; 
        case 'Colorizer':
            jQuery('#section_ag_colorizer_settings').show();
            jQuery('#section_ag_colorizer_settings .section_title').trigger('focus');
            break;
		case 'Admin Themes':
			if(!jQuery('#section_templates').hasClass("loaded")){
				jQuery('#section_templates').addClass('loaded');
				agca_client_init();
			}				
            jQuery('#section_templates').show();
            jQuery('#section_templates .section_title').trigger('focus');			
            break;
        case 'Advanced':
            jQuery('#section_advanced').show();
            jQuery('#section_advanced .section_title').trigger('focus');
            break;
        default:
            jQuery('#section_admin_bar').show();
            jQuery('#section_admin_bar .section_title').trigger('focus');		
    }
}

function hideAllSections(){
    jQuery('#ag_main_menu li').each(function(){
        jQuery(this).attr("class","normal");
    });
    jQuery('.ag_section').each(function(){
        jQuery(this).hide();
    });
}
function reloadRemoveButtonEvents(){
}
function createTargetCombo(target,clas){

    var combo = ""
	
    combo+= "<select";
    if( clas != null){
        combo+=" class=\"editTarget\" ";
    }
    combo+= ">";
    combo+= "<option value=\"_blank\"";
    if(target == "_blank"){
        combo+= " selected ";
    }
    combo+= ">blank</option>";
    combo+= "<option value=\"_self\"";
    if(target == "_self"){
        combo+= " selected ";
    }
    combo+= ">self</option>";
    combo+= "<option value=\"_parent\"";
    if(target == "_parent"){
        combo+= " selected ";
    }
    combo+= ">parent</option>";
    combo+= "<option value=\"_top\"";
    if(target == "_top"){
        combo+= " selected ";
    }
    combo+= ">top</option>";
    combo+= "</select>";
    return combo;
}

function exportSettings(){
    if(jQuery('#export_settings_additional').css("display") == "none"){
        jQuery('#export_settings_additional').show("slide");
    }else{
        jQuery('#agca_form').attr('action','tools.php?page=ag-custom-admin/plugin.php');
        jQuery('#agca_form #_agca_import_settings').val("false");
        jQuery('#agca_form #_agca_export_settings').val("true");
        
        
        jQuery('#ag_add_adminmenu_json').val('');
        jQuery('#ag_edit_adminmenu_json').val('');
        
        
        jQuery('#agca_form').submit();
    }    
}

function importSettings(){
    if(jQuery('#settings_import_file').css("display") == "none"){
        jQuery('#settings_import_file').show("slide");
    }else{
        jQuery('#agca_form').attr('action','tools.php?page=ag-custom-admin/plugin.php');
        if(jQuery('#settings_import_file').val() !=""){
            jQuery('#agca_form #_agca_import_settings').val("true");
            jQuery('#agca_form #_agca_export_settings').val("false");
            jQuery('#agca_form').attr('enctype','multipart/form-data');        
            jQuery('#agca_form').submit();
        }else{
            alert("File for import is not selected!");
        } 
    }
      
}

function savePluginSettings(){
    jQuery('#import_file_area').remove();
    jQuery('#agca_form').submit();
}

jQuery(document).ready(function(){     
    jQuery('#ag_add_adminmenu').on("click", 'a.button_remove', function(){
        jQuery(this).parent().parent().remove();
    });		
    jQuery('#ag_add_adminmenu').on('click', 'a.button_edit', function(){			
        if(editingButtonNow == false){				
            var name = jQuery(this).parent().find('button').text();
            var url = jQuery(this).parent().find('button').attr('title');				
            var target = jQuery(this).parent().find('button').attr('target');
            //console.log(target);
            editingButtonNow = name;
            jQuery(this).parent().append('<div id="temporary_button_edit">name:<input type="text" size="47" value="'+name+'" id="ag_add_adminmenu_name_edit" name="ag_add_adminmenu_name_edit" />url:<input type="text" size="47" value="'+url+'" id="ag_add_adminmenu_url_edit" name="ag_add_adminmenu_url_edit" />' + createTargetCombo(target,"edit")+ '<input type="button" id="ag_add_adminmenu_button_edit" name="ag_add_adminmenu_button_edit" value="Save changes" /></div>');
            reloadRemoveButtonEvents();
        }		
    });/*Save editing changes*/
    jQuery('#ag_add_adminmenu').on("click", '#ag_add_adminmenu_button_edit', function(){			
        //alert(jQuery(this).parent().html());			
        var name = jQuery('#ag_add_adminmenu_name_edit').val();
        var url = jQuery('#ag_add_adminmenu_url_edit').val();
        var target = jQuery('select.editTarget').val();
        //var target = jQuery(this).parent().find('button').attr('target');
        name = name.replace(/["']{1}/gi,"");
        url = url.replace(/["']{1}/gi,"");	
        jQuery('#temporary_button_edit').remove();
			
        var element = 0;
        jQuery('#ag_add_adminmenu :button').each(function(){
            //dont use first button for adding new buttons				
            if(element > 0){						
                if(jQuery(this).html() == editingButtonNow){
                    jQuery(this).attr('title',url);
                    jQuery(this).attr('target',target);
                    jQuery(this).html(name);						
                }
            }
            element++;
        });
        editingButtonNow = false;
    });
                
    setTimeout(function(){
        jQuery('#agca_advertising').show(),700
        });
});	
/*ToolTip*/
function agcaApplyTooltip(){ 
	if(jQuery(this).attr('title') != ""){
        jQuery(this).hover(function(e) {
            if(jQuery(this).hasClass('feedback')){
               jQuery(this).mousemove(function(e) {			
                var tipY = e.pageY + 16; 
                var tipX = e.pageX - 236;	
                var type = '#fee6e6';
                var border = '1px solid red';
                if(jQuery(this).hasClass('positive')) {
                    type = '#eafee6';
                    border = '1px solid green';
                }
                jQuery("#AGToolTipDiv").css({
                    'top': tipY, 
                    'left': tipX,
                    'background': type,
                    'border': border
                });
            }); 
            }else{
                jQuery(this).mousemove(function(e) {			
                var tipY = e.pageY + 16; 
                var tipX = e.pageX + 16;	
                jQuery("#AGToolTipDiv").css({
                    'top': tipY, 
                    'left': tipX,
                    'background': '#FFFFD4',
                    'border': '1px solid #FFFF00'
                });
            });                
            }            
			jQuery("#AGToolTipDiv")
			.html(jQuery(this).attr('title'))
			.stop(true,true)
			.fadeIn("fast");
			jQuery(this).removeAttr('title');			          
        }, function() {
            jQuery("#AGToolTipDiv")
            .stop(true,true)
            .fadeOut("fast");
            jQuery(this).attr('title', jQuery("#AGToolTipDiv").html());
        });
	}
}	

jQuery(document).ready(function(){	
    /*Add click handler on main buttons*/
    jQuery('#ag_main_menu a, #ag_main_menu li').bind('click',function(){
        hideAllSections();		
        var text = jQuery(this).text();
        jQuery(this).attr("class","selected");		
        showHideSection(text);
    });
	
    /*Admin Menu Reset all setings button*/	
    jQuery('#ag_edit_adminmenu_reset_button').click(function(){	
        afterFormClickCreateJson = false;
        jQuery('#agca_form').submit();
    });	

    /*Add new menu item button - creates new HTMl button elements*/
    jQuery('#ag_add_adminmenu_button').click(function(){	
        var name = jQuery('#ag_add_adminmenu_name').val();
        var url = jQuery('#ag_add_adminmenu_url').val();		
        var target = jQuery('#ag_add_adminmenu_target').val();		
        name = name.replace(/["']{1}/gi,"");
        url = url.replace(/["']{1}/gi,"");		
        jQuery('#ag_add_adminmenu_name').val("");
        jQuery('#ag_add_adminmenu_url').val("");
        jQuery('#ag_add_adminmenu_target').val("_none");
        jQuery('#ag_add_adminmenu').append('<tr><td colspan="2"><button target="'+target+'" title="'+url+'" type="button">'+name+'</button>&nbsp;(<a style="cursor:pointer" class="button_edit">edit</a>)&nbsp;(<a style="cursor:pointer" class="button_remove">remove</a>)</td><td></td></tr>');
        reloadRemoveButtonEvents();
    });	
	
    /*Add tooltip box*/
    jQuery("body").append("<div id='AGToolTipDiv'></div>");	
	
    /*ToolTip*/
    jQuery("label[title],#agca_donate_button, a.feedback").each(agcaApplyTooltip);
	  
    /*SECTION FOCUS*/
    jQuery('.section_title').focus(function(){			  
        });	 
	  
    /*HIDE/SHOW New items click*/
    jQuery('input[name=agca_admin_bar_new_content]').bind("click",function(){				
        var checked = jQuery(this).is(":checked");
        if(!checked){
            jQuery(".new_content_header_submenu").show("slide");
        }else{
            jQuery(".new_content_header_submenu").hide("slideDown");
        }		
    });
          
    /*HIDE/SHOW size of rounded box on login page*/
    jQuery('input[name=agca_login_round_box]').bind("click",function(){				
        var checked = jQuery(this).is(":checked");
        if(checked){
            jQuery("#agca_login_round_box_size_block").show("slide");
        }else{
            jQuery("#agca_login_round_box_size_block").hide("slideDown");
        }		
    });
    
     /*HIDE/SHOW size of rounded submenu box on admin page*/
    jQuery('input[name=agca_admin_menu_submenu_round]').bind("click",function(){				
        var checked = jQuery(this).is(":checked");
        if(checked){
            jQuery("#agca_admin_menu_submenu_round_block").show("slide");
        }else{
            jQuery("#agca_admin_menu_submenu_round_block").hide("slideDown");
        }		
    });
	
	 /*HIDE/SHOW size hyperllink on register link on login page*/
    jQuery('input[name=agca_login_register_remove]').bind("click",function(){				
        var checked = jQuery(this).is(":checked");
        if(!checked){
            jQuery("#agca_login_register_href_block").show("slide");
        }else{
            jQuery("#agca_login_register_href_block").hide("slideDown");
        }		
    });
	  
	  
});

/*CLICKING ON ITEMS HANDLING*/
jQuery(document).ready(function(){	
    jQuery('#agca_footer').change(function(){
        });
});

/*Admin menu*/
jQuery(document).ready(function(){	
    jQuery('#adminmenu').css('display','block');
});

/*FORM SUBMITTED*/
jQuery(document).ready(function(){
    jQuery('#agca_form').submit(function(){	
        processData();
        return true;                
    });
});


function processData(){
    /*Serialize checkboxes*/
    var array = "{";
    var firstElement = true;
    var topMarker = "";
	jQuery('#ag_edit_adminmenu input').removeClass('agca-checkbox');
    jQuery('#ag_edit_adminmenu :checkbox').each(function(){		
        if(firstElement != true){
            array += ", ";				
        }
        topMarker = "";
        if(jQuery(this).parent().parent().hasClass('ag_admin_menu_parent')){
            topMarker="<-TOP->"; 
        }
        array += "\"" + topMarker + jQuery(this).attr('class') + "\" : ";
        array += "\"" + jQuery(this).attr('checked') + "\"";
        firstElement = false;			
    });
    array += "}|";
		
    /*Serialize textboxes*/
    array += "{";
    firstElement = true;
    jQuery('#ag_edit_adminmenu :text').each(function(){		
        if(firstElement != true){
            array += ", ";				
        }
        topMarker = "";
        if(jQuery(this).parent().hasClass('ag_admin_menu_parent2')){
            topMarker="<-TOP->";
        }
        array += "\"" + topMarker + jQuery(this).attr('class') + "\" : ";
        array += "\"" + agca_escapeChars(jQuery(this).val()) + "\"";
        firstElement = false;			
    });
    array += "}";
		
    if(afterFormClickCreateJson == true){
        jQuery('#ag_edit_adminmenu_json').val(array);		
    }else{
        jQuery('#ag_edit_adminmenu_json').val('');						
    }
	//console.clear();
    //console.log(array);
    //serialize buttons
    array = "{";
    var element = 0;
    jQuery('#ag_add_adminmenu :button').each(function(){
        //console.log(jQuery(this).html()+jQuery(this).attr('title'));
        if(element > 0){
            if(element > 1){
                array += ", ";				
            }
            array += "\"" + jQuery(this).html() + "\" : {";
            array += " \"value\" : ";
            array += "\"" + jQuery(this).attr('title') + "\"";
            array += ", \"target\" : ";
            array += "\"" + jQuery(this).attr('target') + "\"}";					
        }
        element++;
    });
    array += "}";	
    if(element == 1){
        array="";
    }
    jQuery('#ag_add_adminmenu_json').val(array);	
    
    
		
    /*Serialize colors*/
    var array = "{";
    var firstElement = true;
    var topMarker = "";
    jQuery('input.color_picker').each(function(){	
        if(firstElement != true){
            array += ", ";				
        }			
        array += "\"" + jQuery(this).attr('id') + "\" : ";
        array += "\"" + jQuery(this).val() + "\"";
        firstElement = false;			
    });
    array += "}";	
    
    if(!isSettingsImport){
        jQuery('#ag_colorizer_json').val(array);                
    }    
}

function agca_escapeHTMLChars(str){
	return str
		.replace(/&/g, '&amp;')
		.replace(/'/g, '&#39;')
		.replace(/"/g, '&quot;');		
}

function agca_escapeChars(str){
	return str.replace(/[\/\\\"\']/g, "\\$&");
	//return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
}

/*C O L O R I Z E R*/
function updateTargetColor(id, color){ 
    switch(id)
    { 
        case 'color_background':		
            jQuery('html, .wp-dialog').css({
                'background-color':color
            });	           	
            break; 
        case 'login_color_background':		
            jQuery('body.login').css({
                'background-color':color
            });	           	
            break;             
            
        case 'color_header':		 
            jQuery('#wphead').css({
                'background':color
            });          
            
            //wp > 3.3
            jQuery('#wpadminbar').css({'background':color});
            jQuery('#wpadminbar .ab-top-menu').css({'background':color});
            
            if(wpversion >= 3.2){
                jQuery('#wphead').css({
                    'margin':'0',
                    'margin-left':'-14px',
                    'padding-left':'15px'
                });
                jQuery("#backtoblog").attr("style","");
            }
            break;
        case 'color_admin_menu_top_button_background':
            jQuery('#adminmenu a.menu-top').css({
                'background':color
            });
			jQuery('#adminmenu li.menu-top').css({
                'background':color
            });
            break;
        case 'color_admin_menu_top_button_current_background':
            jQuery('#adminmenu li.menu-top.wp-menu-open a.menu-top').css({
                'background':color
				
            });
            jQuery('#adminmenu li.menu-top.current a.menu-top').css({
                'background':color
            });
			jQuery('body.folded #adminmenu li.menu-top.wp-menu-open').css({
                'background':color
            });
            break;
        case 'color_admin_menu_top_button_hover_background':
            if(color == "")break;
            var selector = '#adminmenu a.menu-top';  
			if(jQuery('body').hasClass('folded')){
				selector = '#adminmenu li.menu-top';  
			}
            var originalBackground = jQuery(selector).css('background-color'); 

            //if first is selected, use second
             if((jQuery(selector+":eq(0)").hasClass('current') || jQuery(selector+":eq(0)").hasClass('wp-has-current-submenu'))){                 
                 originalBackground = jQuery(selector+':eq(1)').css('background-color');              
             }         
            jQuery(selector).mouseover(function(){                
                        if(!(jQuery(this).hasClass('current') || jQuery(this).hasClass('wp-has-current-submenu'))){ 
                            jQuery(this).css({'background':color});                             
                        }            
            }).mouseout(function(){
                if(!(jQuery(this).hasClass('current') || jQuery(this).hasClass('wp-has-current-submenu'))){ 
                    jQuery(this).css('background',originalBackground);                
                }                
            }); 
			
			//for folded menu
	
           
            break;
        case 'color_admin_menu_submenu_background':
           // jQuery("#adminmenu li.wp-has-current-submenu").removeClass("wp-has-current-submenu");
            jQuery('#adminmenu .wp-submenu.sub-open').remove();
            jQuery('#adminmenu .wp-submenu a, #adminmenu li.wp-has-current-submenu .wp-submenu a').each(function(){
                jQuery(this).css({
                    'background':color
                });			
            });
            jQuery('#adminmenu .wp-submenu').css({'background':color,'margin-left':'0',"padding-top":roundedSidberSize+"px","padding-bottom":roundedSidberSize+"px"});
            jQuery('#adminmenu .wp-has-current-submenu .wp-submenu').css("padding","0");
            //jQuery('#adminmenu .wp-submenu').css('border','3px solid red');
            jQuery('#adminmenu .wp-submenu ul').css({'background':'none'});
            jQuery('#adminmenu .wp-submenu ul').css('border','none');
            jQuery('#adminmenu .wp-submenu .wp-submenu-wrap').css('border','none');            
            
            break;            
        case 'color_admin_submenu_font':		  
            jQuery('#adminmenu .wp-submenu li a').css('color',color);	
            break;
        case 'color_admin_menu_submenu_border_top':		  
			if(jQuery('body').hasClass('folded')){
				jQuery('#adminmenu li.menu-top').css('border-top-color',color);	
			}else{
				jQuery('#adminmenu > li > a').css('border-top-color',color);	
			}
            break;
        case 'color_admin_menu_submenu_border_bottom':
			if(jQuery('body').hasClass('folded')){
				jQuery('#adminmenu li.menu-top').css('border-bottom-color',color);
			}else{
				jQuery('#adminmenu > li > a').css('border-bottom-color',color);
			}            
            break;                  
        case 'color_admin_menu_font':
            jQuery('#adminmenu, #adminmenu a, #adminmenu p').css({
                'color':color
            });
            break;
        case 'color_admin_menu_behind_background':
            jQuery('#adminmenuback, #adminmenuwrap').css({
                'background-color':color
            });
            break;
        case 'color_admin_menu_behind_border':
            jQuery('#adminmenuback, #adminmenuwrap').css({
                'border-color':color
            });
            break;
        case 'color_admin_menu_submenu_background_hover':
             var submenuSelector = '#adminmenu .wp-submenu a';
             var originalSubBackground = jQuery(submenuSelector).css('background-color');
              jQuery(submenuSelector).mouseover(function(){                
                jQuery(this).css({
                 'background':color
                });                
            }).mouseout(function(){
                jQuery(this).css('background',originalSubBackground);                
            });
            //jQuery('#adminmenu .wp-submenu a:hover').css({'background':color});
            break;
        case 'color_font_content':
            jQuery('#wpbody-content, #wpbody-content label, #wpbody-content p,#wpbody-content .form-table th, #wpbody-content .form-wrap label').css({
                'color':color
            });
            break;
        case 'color_font_header':
            jQuery('#wphead, #wphead a, #wpadminbar a, #wpadminbar span').css({
                'color':color
            });
            break;
        case 'color_font_footer':
            jQuery('#wpfooter, #wpfooter a').css({
                'color':color
            });
            break;
        case 'color_widget_bar':
            jQuery(".widget .widget-top, .postbox h3, .stuffbox h3").css({
                'background' : color, 
                'text-shadow' :'none'
            });
            break;
        case 'color_widget_background':
            jQuery(".widget, .postbox").css('background',color);			
            //jQuery(".widget, #widget-list .widget-top, .postbox, .menu-item-settings").css('background',color); remove if <3.2 work
            break;		 
        default:	
    }		
}
function updateColor(id,color){
    jQuery("#"+id).css({
        'background-color':color
    });
    jQuery("#"+id).val(color);
    if(isDarker(color) == true){
        jQuery("#"+id).css('color','#ffffff');
    }else{
        jQuery("#"+id).css('color','#000000');
    }		
    updateTargetColor(id,color);
}
/*First load apply colours from fields*/

/*C O L O R I Z E R  E N D*/

/*A J A X*//*
jQuery(document).ready(function(){	
	jQuery.ajax({          
		url: "http://wordpress.argonius.com/ag-custom-admin/news.php",           
		type: "POST",       
			cache: false,      
			success: function (html) {             
				//if data turned       
				if (html==1) { 
				  // jQuery('#agca_news').html(html);
				alert(html+'3');
				} else{   
					alert('no');
				}
			}      
	});
});*/
/*A J A X*/
jQuery(document).ready(function(){		
    
    //Ams
    if(typeof isAGCAPage !== 'undefined'){
        if(isAGCAPage == true){
                 //alert('admin page');
            var url="http://argonius.com/ag-custom-admin/ep/ads?jsoncallback=?";
        jQuery.getJSON(
            url,{
                wp_ver: wpversion,
                agca_ver: agca_version,
                format: "json"
            },
            function(json){                      
                jQuery.each(json,function(i,post){
                    jQuery('#agca_advertising ul').append('<li><a target="_blank" href="http://www.argonius.com/ag-custom-admin/ep/ad?id=' + post.id + '" ><img height=\"100px\" src=\"'+post.src+'\"  title=\"'+post.title+'\" /></a></li>');
                });    
                jQuery('#agca_advertising').show();
                
            }); 
        }                
    }          
        
    
    //News
    jQuery(document).ready(function(){
         if(typeof isAGCAPage !== 'undefined'){
                if(isAGCAPage == true){
                      var url="http://wordpress.argonius.com/agca/info.php/info?jsoncallback=?";
                        jQuery.getJSON(
                            url,{
                                wp_ver: wpversion,
                                agca_ver: agca_version,
                                format: "json"
                            },
                            function(json){                                    
                                jQuery.each(json.posts,function(i,post){						
                                    jQuery('#agca_news').append('<p><strong>Info: </strong>'+post.news+'</p>');
                                });
                                jQuery('#agca_news p').each(function(){						
                                    jQuery(this).hide();
                                });

                            });
                            
                            setInterval(function() {
                            if(jQuery('#agca_news p.news_online').size() == 0){
                                jQuery('#agca_news p:first').addClass('news_online');
                                jQuery('#agca_news p:first').show();
                            }else{
                                var changed = false;
                                var finish = false;
                                jQuery('#agca_news p').each(function(){
                                    if(finish != true){
                                        if(changed == true){						
                                            jQuery(this).addClass('news_online');
                                            jQuery(this).show();
                                            finish = true;
                                        }
                                        else if(jQuery(this).hasClass('news_online')){
                                            jQuery(this).hide();
                                            jQuery(this).removeClass('news_online');
                                            changed = true;								
                                        };
                                    }						
                                });
                                if(jQuery('#agca_news p.news_online').size() == 0){
                                    jQuery('#agca_news p:first').addClass('news_online');
                                    jQuery('#agca_news p:first').show();
                                }
                            }
                        }, 5000);
                }
         }
      
    });    
    

});
/*A J A X*/

/*AGCA CHECKBOX RADIOBOX*/
function agcaCheckBoxOnClick(obj){	
	obj.prev().trigger('click');
	//console.log(obj.prev());
	if(jQuery(obj).prev().is(':checked')){
		jQuery(obj).addClass('agca-checkbox-box-checked');
		jQuery(obj).removeClass('agca-checkbox-box');
	}else{
		jQuery(obj).addClass('agca-checkbox-box');
		jQuery(obj).removeClass('agca-checkbox-box-checked');
	}
}
function agcaRadioBoxOnClick(obj){	
	obj.prev().trigger('click');
	//console.log(obj.prev());
	obj.parent().find('.agca-radiobox').removeClass('checked');
	if(jQuery(obj).prev().is(':checked')){
		jQuery(obj).addClass('checked');		
	}else{
		jQuery(obj).removeClass('checked');
	}
}
function agcaChangeCheckBoxStyles(){
	
	/*checkbox*/
	jQuery('.agca-checkbox-box-checked').remove();
	jQuery('.agca-checkbox-box').remove();
	jQuery('.agca-checkbox').each(function(){
		jQuery(this).hide();
		var cls = "agca-checkbox-box";
		if(jQuery(this).is(':checked')){
			cls = "agca-checkbox-box-checked";
		}
		jQuery(this).after('<div onClick="agcaCheckBoxOnClick(jQuery(this));" class="' + cls + '" title="'+jQuery(this).attr('title')+'"><div/>');		
	});
	
	/*radio*/
	jQuery('.agca-radiobox').remove();
	jQuery('.agca-radio').each(function(){
		jQuery(this).hide();
		var cls = "agca-radiobox";		
		if(jQuery(this).is(':checked')){
			cls = "agca-radiobox checked";		
		}
		
		jQuery(this).after('<div onClick="agcaRadioBoxOnClick(jQuery(this));" value="'+jQuery(this).attr('value')+'" class="' + cls + '" title="'+jQuery(this).attr('title')+'"><div/>');		
	});
}

function isWPHigherOrEqualThan(targetVersion){
    var versions = ["0.7",
    "0.71",
    "0.711",
    "0.72",
    "1.0",
    "1.0.1",
    "1.0.2",
    "1.2",
    "1.2.1",
    "1.2.2",
    "1.5",
    "1.5.1",
    "1.5.1.2",
    "1.5.1.3",
    "1.5.2",
    "2.0",
    "2.0.1",
    "2.0.2",
    "2.0.3",
    "2.0.4",
    "2.0.5",
    "2.0.6",
    "2.0.7",
    "2.0.8",
    "2.0.9",
    "2.0.10",
    "2.0.11",
    "2.1",
    "2.1.1",
    "2.1.2",
    "2.1.3",
    "2.2",
    "2.2.1",
    "2.2.2",
    "2.2.3",
    "2.3",
    "2.3.1",
    "2.3.2",
    "2.3.3",
    "2.5",
    "2.5.1",
    "2.6",
    "2.6.1",
    "2.6.2",
    "2.6.3",
    "2.6.5",
    "2.7",
    "2.7.1",
    "2.8",
    "2.8.1",
    "2.8.2",
    "2.8.3",
    "2.8.4",
    "2.8.5",
    "2.8.6",
    "2.9",
    "2.9.1",
    "2.9.2",
    "3.0",
    "3.0.1",
    "3.0.2",
    "3.0.3",
    "3.0.4",
    "3.0.5",
    "3.0.6",
    "3.1",
    "3.1.1",
    "3.1.2",
    "3.1.3",
    "3.1.4",
    "3.2",
    "3.2.1",
    "3.3",
    "3.3.1",
    "3.3.2",
    "3.4",
    "3.4.1",
    "3.4.2",
    "3.5",
    "3.5.1",
    "3.5.2",
    "3.5.3",
    "3.5.4",
    "3.6",
	"3.6.1",
	"3.6.2",
	"3.6.3",
	"3.7",
	"3.7.1",
	"3.7.2",
	"3.7.3",
	"3.7.4",
	"3.8",
	"3.8.1",
	"3.8.2",
	"3.8.3",
	"3.9",
	"3.9.1"];
    //remove sufixes, beta RC etc
    if (wpversion.indexOf("-")!=-1){
        var parts = wpversion.split("-");
        wpversion = parts[0];
    }
    for(var i in versions){
        if(versions[i] == targetVersion){
            if(wpversion >= targetVersion) return true; 
        }
    }	
    return false;	
}


//VALIDATION
jQuery(document).ready(function() {
    jQuery(".validateNumber").on('keydown',function(event) {
        // Allow only backspace and delete
        var limit = jQuery(this).attr('limit');
        var value = jQuery(this).val();
       
        if ( event.keyCode == 46 || event.keyCode == 8 ) {
        } else {
             if((typeof(limit) != undefined) && value.length >= limit){
                   return false;
                }
            if (event.keyCode < 95) {
                if (event.keyCode < 48 || event.keyCode > 57 ) {
                    event.preventDefault();
                }
            } else {
                if (event.keyCode < 96 || event.keyCode > 105 ) {
                    event.preventDefault();
                }
            }
        }
    });
});
