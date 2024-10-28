<?php
/*
Plugin Name: Add HTML, JavaScript and CSS to &lt;head&gt;
Description: A simple, small (<20 KB), free and fast plugin to add HTML, CSS or JavaScript code to the &lt;head&gt; HTML part of your site.
Version: 0.1.2
Author: Rob Bakker
Author URI: https://searchsystems.nl
*/
namespace AddHtmlJavaScriptAndCssToHead;

// This is shown when the user is an admin in the backend.
class AHJACTH_SettingsPage
{
    // Holds the values to be used in the fields callbacks
    private $options;

    // Start up
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    // Add plugin options page
    public function add_plugin_page()
    {
        // With add_menu_page it will be directly visible.
        add_menu_page(
            'HTML in &lt;head&gt;',
            'Add HTML to &lt;head&gt;',
            'manage_options',
            'head-plugin-settings',
            array( $this, 'create_admin_page' ),
            'dashicons-media-code'
        );
    }

    // Options page callback
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'ahjacth_head_option' );
        ?>
        <div class="wrap" style="text-align:justify;">
          <h1> Add HTML, JavaScript and CSS to &lt;head&gt;</h1>

          <div style="float:left; width:73%;display:block;">

            <form method="post" action="options.php" style="">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'ahjacth_head_options' );
                do_settings_sections( 'head-plugin-settings' );
                submit_button();
            ?>
              <h2>More information</h2>
              <p>
                You're allowed to add the following HTML elements in the head element:<br /><br /><code>&lt;style&gt;, &lt;meta&gt;, &lt;link&gt;, &lt;script&gt;, &lt;noscript&gt;, &lt;template&gt; and &lt;base&gt;</code>
                <span style="color:rgb(240,50,50);font-weight:bold;">*</span><br /><br />
                You could for example add a link to a custom favicon file (optimized for Apple iOS devices) like this:<br /><br />
                <code>&lt;link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png"&gt; </code>
              </p>
              <p><span style="color:rgb(240,50,50);font-weight:bold;">*</span> Unfortunately 'data-*' attributes are currently removed by the WordPress. Wildcard inclusion does not work, each 'data- attribute needs to be whitelisted manually. If you have a need for one or more specific 'data-' attributes (e.g. 'data-binding'), please <a href="https://wordpress.org/support/plugin/add-html-javascript-and-css-to-head" target="_blank">create a support topic</a> so I can add them manually to the plugin.</p>
            </form>
          </div>
          <div style="float:right;width:18%;background-color:rgb(250,250,250);">
             <div style="padding:1rem;font-weight:bold;border-bottom:1px solid #F1F1F1;">Add HTML, JavaScript and CSS to &lt;head&gt;</div>
             <div style="padding:1rem;font-weight:bold;border-bottom:1px solid #F1F1F1;">
               <img src="https://ps.w.org/add-html-javascript-and-css-to-head/assets/icon.svg" width="100%" style=""/>
            </div>
            <div style="padding:1rem;border-bottom:1px solid #F1F1F1;">
              <strong>Support</strong><br />
              If you encounter any issues, please create a new topic on the <a href="https://wordpress.org/support/plugin/add-html-javascript-and-css-to-head" target="_blank">support</a> forum.
            </div>
            <div style="padding:1rem;">
              <strong>Do you like this plugin?</strong><br />
              If you find this plugin useful, you may <a href="https://www.paypal.me/rvbakker" target="_blank">donate</a> any amount you would like to donate. Or you could <a href="https://wordpress.org/support/plugin/add-html-javascript-and-css-to-head/reviews/?filter=5" target="_blank">leave a five star review</a>. :-)</div>
          </div>

        </div>
        <?php
    }

    // Print the Section text
    public function print_section_info()
    {
        print '<div style="font-size:1rem; line-height:1rem;">Below you can add your extra HTML, CSS or JavaScript code, which will then be added to the &lt;head&gt; section on every page on your site.<br /></div>';
    }

    // Get the settings option array and print one of its values
    public function head_callback()
    {
        printf(
            '<textarea id="head_html" name="ahjacth_head_option[head_html]" rows="15" style="width:%s">%s</textarea>','100%',
            isset( $this->options['head_html'] ) ? esc_textarea($this->options['head_html']) : ''
              );
    }

    // Register and add settings
    public function page_init()
    {
        register_setting(
            'ahjacth_head_options', // Option group
            'ahjacth_head_option', // Option name
            array( $this, 'sanitize' )
        );

        add_settings_section(
            'setting_section_id', // ID
            'Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'head-plugin-settings' // Page
        );

        add_settings_field(
            'head_html',
            'Enter the HTML code for the &lt;head&gt section here:',
            array( $this, 'head_callback' ),
            'head-plugin-settings',
            'setting_section_id'
        );
    }

    /**
     * Sanitize the input using wp_kses(). Unfortunately wp_kses() does not support
     * data-* attributes, each data- attribute should be added manually.
     * Right now, all data- attributes are stripped from the user input.
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

        $global_attributes = array(
          'accesskey' => array(),
          'autocapitalize' => array(),
          'class' => array(),
          'contenteditable' => array(),
          'contextmenu' => array(),
          'data-*' => array(),
          'data-start' => array(),
          'data-end' => array(),
          'dir' => array(),
          'draggable' => array(),
          'dropzone' => array(),
          'hidden' => array(),
          'id' => array(),
          'is' => array(),
          'itemid' => array(),
          'itemprop' => array(),
          'itemref' => array(),
          'itemscope' => array(),
          'itemtype' => array(),
          'lang' => array(),
          'slot' => array(),
          'spellcheck' => array(),
          'style' => array(),
          'tabindex' => array(),
          'title' => array(),
          'translate' => array(),
          'xml:lang' => array(),
          'xml:base' => array(),
          'onabort' => array(),
          'onautocomplete' => array(),
          'onautocompleteerror' => array(),
          'onblur' => array(),
          'oncancel' => array(),
          'oncanplay' => array(),
          'oncanplaythrough' => array(),
          'onchange' => array(),
          'onclick' => array(),
          'onclose' => array(),
          'oncontextmenu' => array(),
          'oncuechange' => array(),
          'ondblclick' => array(),
          'ondrag' => array(),
          'ondragend' => array(),
          'ondragenter' => array(),
          'ondragexit' => array(),
          'ondragleave' => array(),
          'ondragover' => array(),
          'ondragstart' => array(),
          'ondrop' => array(),
          'ondurationchange' => array(),
          'onemptied' => array(),
          'onended' => array(),
          'onerror' => array(),
          'onfocus' => array(),
          'oninput' => array(),
          'oninvalid' => array(),
          'onkeydown' => array(),
          'onkeypress' => array(),
          'onkeyup' => array(),
          'onload' => array(),
          'onloadeddata' => array(),
          'onloadedmetadata' => array(),
          'onloadstart' => array(),
          'onmousedown' => array(),
          'onmouseenter' => array(),
          'onmouseleave' => array(),
          'onmousemove' => array(),
          'onmouseout' => array(),
          'onmouseover' => array(),
          'onmouseup' => array(),
          'onmousewheel' => array(),
          'onpause' => array(),
          'onplay' => array(),
          'onplaying' => array(),
          'onprogress' => array(),
          'onratechange' => array(),
          'onreset' => array(),
          'onresize' => array(),
          'onscroll' => array(),
          'onseeked' => array(),
          'onseeking' => array(),
          'onselect' => array(),
          'onshow' => array(),
          'onsort' => array(),
          'onstalled' => array(),
          'onsubmit' => array(),
          'onsuspend' => array(),
          'ontimeupdate' => array(),
          'ontoggle' => array(),
          'onvolumechange' => array(),
          'onwaiting' => array(),
          'role' => array(),
        );

        $style_attributes = $global_attributes;
        $style_attributes['type'] = array();
        $style_attributes['media'] = array();
        $style_attributes['nonce'] = array();
        $style_attributes['scoped'] = array();

        $meta_attributes = $global_attributes;
        $meta_attributes['charset'] = array();
        $meta_attributes['content'] = array();
        $meta_attributes['http-equiv'] = array();
        $meta_attributes['name'] = array();
        $meta_attributes['scheme'] = array();

        $link_attributes = $global_attributes;
        $link_attributes['as'] = array();
        $link_attributes['crossorigin'] = array();
        $link_attributes['href'] = array();
        $link_attributes['hreflang'] = array();
        $link_attributes['integrity'] = array();
        $link_attributes['media'] = array();
        $link_attributes['referrerpolicy'] = array();
        $link_attributes['rel'] = array();
        $link_attributes['sizes'] = array();
        $link_attributes['type'] = array();
        $link_attributes['disabled'] = array();
        $link_attributes['methods'] = array();
        $link_attributes['prefetch'] = array();
        $link_attributes['target'] = array();
        $link_attributes['charset'] = array();
        $link_attributes['rev'] = array();
        $link_attributes['color'] = array();

        $script_attributes = $global_attributes;
        $script_attributes['async'] = array();
        $script_attributes['crossorigin'] = array();
        $script_attributes['defer'] = array();
        $script_attributes['integrity'] = array();
        $script_attributes['nomodule'] = array();
        $script_attributes['nonce'] = array();
        $script_attributes['src'] = array();
        $script_attributes['text'] = array();
        $script_attributes['type'] = array();
        $script_attributes['language'] = array();

        $base_attributes = $global_attributes;
        $base_attributes['href'] = array();
        $base_attributes['target'] = array();

        $allowed_tags = array(
                              'title'   => $global_attributes,
                      				'style'   => $style_attributes,
                              'meta'   => $meta_attributes,
                              'link'   => $link_attributes,
                              'script'   => $script_attributes,
                              'base'   => $base_attributes,
                              'noscript'   => $global_attributes,
                              'template'   => $global_attributes,
                            );

        if( isset( $input['head_html'] ) )
        {
          $new_input['head_html'] =  wp_kses( $input['head_html'], $allowed_tags)  ;
        }

        return $new_input;
    }
}

// Create the settings page if the user is an admin.
if( is_admin() )
    $settings_page = new AHJACTH_SettingsPage();

// Add the html to the <head> section.
$head_setting = get_option( 'ahjacth_head_option' );
add_action('wp_head', 'AddHtmlJavaScriptAndCssToHead\ahjacth_add_html_to_head');
function ahjacth_add_html_to_head()
{
    global $head_setting;
    if(isset($head_setting['head_html']))
    {
        echo $head_setting['head_html'];
    }
}

// Add settings and support links to the listing in the Plugins page in Admin.
add_filter( 'plugin_action_links', 'AddHtmlJavaScriptAndCssToHead\ahjacth_add_action_plugin', 10, 5 );
function ahjacth_add_action_plugin( $actions, $plugin_file )
{
  	static $plugin;

  	if (!isset($plugin))
    {
  		  $plugin = plugin_basename(__FILE__);
    }

  	if ($plugin == $plugin_file)
    {
        $settings = array('settings' => '<a href="admin.php?page=head-plugin-settings">' . __('Settings', 'General') . '</a>');
        $site_link = array('support' => '<a href="https://wordpress.org/support/plugin/add-html-javascript-and-css-to-head" target="_blank">Support</a>');

        $actions = array_merge($settings, $actions);
        $actions = array_merge($site_link, $actions);
  	}

		return $actions;
}

// Deal with plugin uninstallation, remove the saved option from the database.
register_uninstall_hook( __FILE__, 'AddHtmlJavaScriptAndCssToHead\ahjacth_uninstall' );
function ahjacth_uninstall() {
    delete_option( 'ahjacth_head_option' );
}
