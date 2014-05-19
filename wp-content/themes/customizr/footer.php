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
            <div class="span4 credits" style="width: 20%;">
                <div class="footnavs"><?php wp_nav_menu(); ?></div>
            </div>
            <div class="span4 social-block pull-left">
             <div class="container footer-widgets ">
                <div class="row widget-area" role="complementary">                                                    
                <div id="footer_one" class="span4">
                <aside title="Shift-click to edit this widget." id="synved_social_follow-3" class="widget widget_synved_social_follow">
                <h3 class="widget-title"></h3>
                <div>
                    <a class="synved-social-button synved-social-button-follow synved-social-size-96 synved-social-resolution-single synved-social-provider-facebook" data-provider="facebook" target="_blank" rel="nofollow" title="Follow us on Facebook" href="http://www.facebook.com/MyAvatarName" style="font-size: 0px; width:96px;height:96px;margin:0;margin-bottom:5px;margin-right:5px;">
                    <img alt="facebook" title="Follow us on Facebook" class="synved-share-image synved-social-image synved-social-image-follow" style="display: inline; width:96px;height:96px; margin: 0; padding: 0; border: none; box-shadow: none;" src="http://localhost/llvtest/wp-content/plugins/social-media-feather/synved-social/image/social/regular/128x128/facebook.png" height="96" width="96"></a>
                    <a class="synved-social-button synved-social-button-follow synved-social-size-96 synved-social-resolution-single synved-social-provider-twitter" data-provider="twitter" target="_blank" rel="nofollow" title="Follow us on Twitter" href="http://twitter.com/MyAvatarName" style="font-size: 0px; width:96px;height:96px;margin:0;margin-bottom:5px;margin-right:5px;">
                    <img alt="twitter" title="Follow us on Twitter" class="synved-share-image synved-social-image synved-social-image-follow" style="display: inline; width:96px;height:96px; margin: 0; padding: 0; border: none; box-shadow: none;" src="http://localhost/llvtest/wp-content/plugins/social-media-feather/synved-social/image/social/regular/128x128/twitter.png" height="96" width="96"></a>
                    <a class="synved-social-button synved-social-button-follow synved-social-size-96 synved-social-resolution-single synved-social-provider-google_plus" data-provider="google_plus" target="_blank" rel="nofollow" title="Follow us on Google+" href="http://plus.google.com/needlessly_long_google_plus_id" style="font-size: 0px; width:96px;height:96px;margin:0;margin-bottom:5px;margin-right:5px;">
                    <img alt="google_plus" title="Follow us on Google+" class="synved-share-image synved-social-image synved-social-image-follow" style="display: inline; width:96px;height:96px; margin: 0; padding: 0; border: none; box-shadow: none;" src="http://localhost/llvtest/wp-content/plugins/social-media-feather/synved-social/image/social/regular/128x128/google_plus.png" height="96" width="96"></a>
                    <a class="synved-social-button synved-social-button-follow synved-social-size-96 synved-social-resolution-single synved-social-provider-linkedin" data-provider="linkedin" target="_blank" rel="nofollow" title="Find us on Linkedin" href="http://www.linkedin.com/in/yourid" style="font-size: 0px; width:96px;height:96px;margin:0;margin-bottom:5px;margin-right:5px;">
                    <img alt="linkedin" title="Find us on Linkedin" class="synved-share-image synved-social-image synved-social-image-follow" style="display: inline; width:96px;height:96px; margin: 0; padding: 0; border: none; box-shadow: none;" src="http://localhost/llvtest/wp-content/plugins/social-media-feather/synved-social/image/social/regular/128x128/linkedin.png" height="96" width="96"></a>
                    <a class="synved-social-button synved-social-button-follow synved-social-size-96 synved-social-resolution-single synved-social-provider-rss" data-provider="rss" target="_blank" rel="nofollow" title="Subscribe to our RSS Feed" href="http://feeds.feedburner.com/MyFeedName" style="font-size: 0px; width:96px;height:96px;margin:0;margin-bottom:5px;margin-right:5px;">
                    <img alt="rss" title="Subscribe to our RSS Feed" class="synved-share-image synved-social-image synved-social-image-follow" style="display: inline; width:96px;height:96px; margin: 0; padding: 0; border: none; box-shadow: none;" src="http://localhost/llvtest/wp-content/plugins/social-media-feather/synved-social/image/social/regular/128x128/rss.png" height="96" width="96"></a>
                    <a class="synved-social-button synved-social-button-follow synved-social-size-96 synved-social-resolution-single synved-social-provider-youtube" data-provider="youtube" target="_blank" rel="nofollow" title="Find us on YouTube" href="http://www.youtube.com/MyYouTubeName" style="font-size: 0px; width:96px;height:96px;margin:0;margin-bottom:5px;">
                    <img alt="youtube" title="Find us on YouTube" class="synved-share-image synved-social-image synved-social-image-follow" style="display: inline; width:96px;height:96px; margin: 0; padding: 0; border: none; box-shadow: none;" src="http://localhost/llvtest/wp-content/plugins/social-media-feather/synved-social/image/social/regular/128x128/youtube.png" height="96" width="96"></a>
                    <a class="synved-social-credit" target="_blank" rel="nofollow" title="WordPress Social Media Feather" href="http://synved.com/wordpress-social-media-feather/" style="color:#444; text-decoration:none; font-size:8px; margin-left:5px;vertical-align:10px;white-space:nowrap;">
                    <span>by </span>
                    <img style="display: inline;margin:0;padding:0;width:16px;height:16px;" alt="feather" src="http://localhost/llvtest/wp-content/plugins/social-media-feather/synved-social/image/icon.png" height="16" width="16"></a>
                    </div>
                    </aside>
                    </div>
                </div>
            </div>                 
            </div>
           <!--
            <div class="span4 social-block pull-left">
            </div>
            -->
        <?php do_action( '__footer' ); // hook of footer widget and colophon?>
	</footer>
<?php 
wp_footer(); //do not remove, used by the theme and many plugins
do_action( '__after_footer' ); 
?>
	</body>
<?php do_action( '__after_body' );  ?>
</html>
