jQuery(document).ready(function() {    
    jQuery( "#the_champ_ss_rearrange" ).sortable();
	// provider checkbox
	jQuery('.theChampSharingProviderContainer input').click(function(){
		if(jQuery(this).is(':checked')){
			jQuery('#the_champ_ss_rearrange').append('<li title="' + jQuery(this).val() + '" id="the_champ_re_' + jQuery(this).val().replace(' ', '_') + '" ><i class="theChampSharingButton theChampSharing'+ jQuery(this).val().replace(' ', '') +'Button"></i><input type="hidden" name="the_champ_sharing[horizontal_re_providers][]" value="' + jQuery(this).val() + '"></li>');
		}else{
			jQuery('#the_champ_re_' + jQuery(this).val().replace(' ', '_')).remove();
		}
	});
});