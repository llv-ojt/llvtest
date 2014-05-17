(function ($) {
  var api = wp.customize;

  $.each({
    'tc_theme_options[tc_show_featured_pages]': {
      controls: TCControlParams.FPControls,
      callback: function (to) {
        return '1' == to
      }
    },
    'tc_theme_options[tc_front_slider]': {
      controls: [
        'tc_theme_options[tc_slider_width]',
        'tc_theme_options[tc_slider_delay]'
      ],
      callback: function (to) {
        return '0' !== to
      }
    }
  }, function (settingId, o) {
    api(settingId, function (setting) {
      $.each(o.controls, function (i, controlId) {
        api.control(controlId, function (control) {
          var visibility = function (to) {
            control.container.toggle(o.callback(to));
          };
          visibility(setting.get());
          setting.bind(visibility);
        });
      });
    });
  });


})(jQuery);


jQuery(document).ready(function () {
    ! function ($) {
      var html = '';
      html += '  <div id="tc-donate-customizer">';              
      html += '    <h3>We do our best do make Customizr the perfect free theme for you!</h3>';
      html += '    <span class="tc-notice"> Please help support it\'s continued development with a donation of $20, $50, or even $100.</span>';
      html += '      <a class="tc-donate-link" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=8CTH6YFDBQYGU" target="_blank" rel="nofollow">';
      html += '        <img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" alt="Make a donation for Customizr">';
      html += '      </a>';
      //html += '    </p>';
      html += '  </div>';

      $('#customize-info').append( html );

    }(window.jQuery)
});