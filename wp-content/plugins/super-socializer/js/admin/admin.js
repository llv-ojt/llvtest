jQuery(document).ready(function() {    
    jQuery( "#tabs" ).tabs();
	// login redirection
	jQuery('#the_champ_login_redirection_column').find('input[type=radio]').click(function(){
		if(jQuery(this).attr('id') && jQuery(this).attr('id') == 'the_champ_login_redirection_custom'){
			jQuery('#the_champ_login_redirection_url').css('display', 'block');
		}else{
			jQuery('#the_champ_login_redirection_url').css('display', 'none');
		}
	});
	if(jQuery('#the_champ_login_redirection_custom').is(':checked')){
		jQuery('#the_champ_login_redirection_url').css('display', 'block');
	}else{
		jQuery('#the_champ_login_redirection_url').css('display', 'none');
	}
	// registration redirection
	jQuery('#the_champ_register_redirection_column').find('input[type=radio]').click(function(){
		if(jQuery(this).attr('id') && jQuery(this).attr('id') == 'the_champ_register_redirection_custom'){
			jQuery('#the_champ_register_redirection_url').css('display', 'block');
		}else{
			jQuery('#the_champ_register_redirection_url').css('display', 'none');
		}
	});
	if(jQuery('#the_champ_register_redirection_custom').is(':checked')){
		jQuery('#the_champ_register_redirection_url').css('display', 'block');
	}else{
		jQuery('#the_champ_register_redirection_url').css('display', 'none');
	}
	// help content
	jQuery('.the_champ_help_bubble').toggle(
		function(){
			jQuery('#' + jQuery(this) . attr('id') + '_cont').show();
		},
		function(){
			jQuery('#' + jQuery(this) . attr('id') + '_cont').hide();
		}
	);
	
	jQuery('.the_champ_paypal_submit').click(function(){
		var amount = jQuery(this).parent().children('input[type=text]').val().trim();
		var newForm = jQuery('<form>', {
			'action': 'https://www.paypal.com/cgi-bin/webscr',
			'target': '_blank',
			'method': 'post'
		});
		var fields = [{
			'name': 'cmd',
			'value': '_xclick',
			'type': 'hidden'
		}, {
			'name': 'business',
			'value': 'lordofthechamps@gmail.com',
			'type': 'hidden'
		}, {
			'name': 'item_name',
			'value': 'Super Socializer',
			'type': 'hidden'
		}, {
			'name': 'no_shipping',
			'value': '0',
			'type': 'hidden'
		}, {
			'name': 'no_note',
			'value': '1',
			'type': 'hidden'
		}, {
			'name': 'return',
			'value': theChampWebsiteUrl+'/wp-admin/admin.php?page=the-champ',
			'type': 'hidden'
		}, {
			'name': 'cbt',
			'value': 'Return to your dashboard',
			'type': 'hidden'
		}, {
			'name': 'currency_code',
			'value': 'USD',
			'type': 'hidden'
		}, {
			'name': 'amount',
			'value': amount,
			'type': 'hidden'
		}];
		for(var i = 0; i < fields.length; i++){
			jQuery(newForm).append(jQuery('<input>', {
				'name': fields[i].name,
				'value': fields[i].value,
				'type': fields[i].type
			}));
		}
		newForm.submit();
	});
});