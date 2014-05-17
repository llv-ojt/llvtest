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
	<footer id="footer" class="<?php echo tc__f('tc_footer_classes', '') ?>">
            <div id="s_menu" class="s_menu">
                <ul>
                    <li>
                        HOME
                    </li>
                    <li>
                        ROOMS
                    </li>
                    <li>
                        RESORT
                    </li>
                    <li>
                        ATTRACTIONS
                    </li>
                    <li>
                        ABOUT
                    </li>
                    <li>
                        CONTACT
                    </li>                    
                </ul>
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
