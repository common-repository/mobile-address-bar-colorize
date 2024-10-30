<?php
/**
 * Plugin Name: Mobile Address Bar Colorize
 * Plugin URI:  https://learnhowwp.com/address-bar-colorize
 * Description: A plugin that lets you change the Address Bar color of the Mobile Browsers. It adds the meta theme-color to the head tag of your website.
 * Version:     1.1
 * Author:      learnhowwp.com
 * Author URI:  http://learnhowwp.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
 


defined( 'ABSPATH' ) or die( 'No script kiddies please!' ); 


/** Function to add the meta tag to the <head> **/
function learnhowwp_add_meta() { 
    if(get_option('learnhowwp_address_color')){
        echo '<meta name="theme-color" content="'.get_option('learnhowwp_address_color').'">';
        echo '<meta name="msapplication-navbutton-color" content="'.get_option('learnhowwp_address_color').'">';
        echo '<meta name="apple-mobile-web-app-capable" content="yes">';
        echo '<meta name="apple-mobile-web-app-status-bar-style" content="'.get_option('learnhowwp_address_color').'">';                        
    }
}
add_action('wp_head', 'learnhowwp_add_meta');
 

/** Creating a menu page **/
function learnhowwp_colorize_menu() {
	add_options_page( 'Address Bar Colorize', 'Address Bar Colorize', 'manage_options', 'address-bar-colorize-lwp', 'learnhowwp_colorize_options' );
}

/** Calling the function to create the menu in a hook **/
add_action( 'admin_menu', 'learnhowwp_colorize_menu' );

/** Creating the Plugin options page **/
function learnhowwp_colorize_options() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	/**Loading the Color Picker**/
	wp_enqueue_script('wp-color-picker');
    wp_enqueue_style( 'wp-color-picker');


    // variables for the field and option names 
    $opt_name = 'learnhowwp_address_color';
    $hidden_field_name = 'learnhowwp_submit_hidden';
    $data_field_name = 'learnhowwp_address_color';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );
    //If it exists in the database sanitize it
    if($opt_val)
        $opt_val=sanitize_hex_color($opt_val);
    else
        $opt_val="#ffffff"; //If value does not exist in the database set a default value

    //Check if the Form was posted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $nonce = $_REQUEST['_wpnonce']; //Storing nonce

        if(wp_verify_nonce($nonce,'update-lwp-color-form')){ //checking nonce

            if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
                
                // Read their posted value
                $opt_val = sanitize_hex_color($_POST[ $data_field_name ]);
                
                //The posted value was not a hex color
                if($opt_val==null){
                    $opt_val = get_option( $opt_name ); //get the previously saved value from the database
                    
                    //If value does not exist in database set the value to #fff
                    if($opt_val==false)
                        $opt_val="#ffffff";
        //Display the error message                
        ?>
        <div class="error notice"><p><strong><?php _e('Enter a Valid Hex Value', 'learnhowwp-colorize' ); ?></strong></p></div>
        <?php            
                }
                else{   //If posted value was a hex color

                    // Save the posted value in the database
                    update_option( $opt_name,$opt_val);
            
                    // Put a "settings saved" message on the screen
        ?>
        <div class="updated notice"><p><strong><?php _e('Settings Saved', 'learnhowwp-colorize' ); ?></strong></p></div>
        <?php
                }

            }
        }
        else{//nonce fails
        ?>
            <div class="error notice"><p><strong><?php _e('Nonce Failed', 'learnhowwp-colorize' ); ?></strong></p></div>
        <?php            
    

        }    
    }    

    //Now display the settings editing screen
    echo '<div class="wrap">';
    // header
    echo "<h2>" . __( 'Address Bar Color Settings', 'learnhowwp-colorize' ) . "</h2>";
    //echo get_option( $opt_name ) ? $opt_val : '#ffffff';

    // settings form    
    ?>
    <form name="lwp-color-form" method="post" action="">
    <?php
        if ( function_exists('wp_nonce_field') ) 
            wp_nonce_field('update-lwp-color-form'); 
    ?>    
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">        
        <p><?php _e("Select Color :", 'learnhowwp-colorize' ); ?>         
        <input name="<?php echo $data_field_name; ?>" type="text" id="lwp_address_bar_color_picker" value="<?php echo $opt_val; ?>" data-default-color="#ffffff">
        </p>
        <hr/>        
        <p class="submit">
        <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
        </p>    
    </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {   
    $('#lwp_address_bar_color_picker').wpColorPicker();
});             
</script>

<?php		

}   

?>