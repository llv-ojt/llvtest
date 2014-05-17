<?php
 /**
 * The template for displaying the footer.
 *
 *
 * @package Customizr
 * @since Customizr 3.0
 */
do_action( '__before_footer' );
?>
	<!-- FOOTER -->
	<footer id="footer" class="<?php //echo tc__f('tc_footer_classes', '') ?>">
            <div class="footTable">
                <div class="footnavs"><?php wp_nav_menu(); ?></div> 
                <div><img src="wp-content/themes/customizr/images/Facebook_seal.png" width="90">
                        <span style="font-weight: bold; font-size: 20px;">&nbsp; Like Lalaguna</span>
                        <br /><br /><br />
                        <div style="padding-top: 5px;"><img src="wp-content/themes/customizr/images/Twitter.png" width="90"><span style="font-weight: bold; font-size: 20px;">&nbsp; Follow @lalagunavillas</span></div>                            
                        <br /><br />
                        <div style="padding-top: 5px;"><img src="wp-content/themes/customizr/images/TripAdvisor-logo.png" width="120"></div>                            
                </div>
                <div><img src="wp-content/themes/customizr/images/Email icon.png" width="90">
                        <span style="font-weight: bold; font-size: 20px;">&nbsp; info@lalagunavillas.com.ph</span>
                        <br /><br /><br />
                        <div style="padding-top: 5px;"><img src="wp-content/themes/customizr/images/landline-icon.png" width="90"><span style="font-weight: bold; font-size: 20px;">&nbsp; +63 43 287 3697</span></div>                                            
                </div>                            
                <div><img src="wp-content/themes/customizr/images/mobile-icon.png" width="90">
                    <span style="font-weight: bold; font-size: 20px;">&nbsp; +63 917 446 5722</span>
                        <br /><br /><br />
                        <div style="padding-top: 5px;"><img src="wp-content/themes/customizr/images/fax-icon.png" width="90"><span style="font-weight: bold; font-size: 20px;">&nbsp; +63 2 857 2657</span></div>                                                            
                </div>            
            </div>
        <?php do_action( '__footer' ); // hook of footer widget and colophon?>
	</footer>
<?php 
wp_footer(); //do not remove, used by the theme and many plugins
do_action( '__after_footer' ); 
?>
	</body>
<?php do_action( '__after_body' );  ?>
</html>
