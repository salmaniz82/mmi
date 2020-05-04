<?php
add_action('wp_enqueue_scripts', 'porto_child_css', 1001);

// Load CSS
function porto_child_css() {
// porto child theme styles
    wp_deregister_style('styles-child');
    wp_register_style('styles-child', esc_url(get_stylesheet_directory_uri()) . '/style.css');
    wp_enqueue_style('styles-child');

    if (is_rtl()) {
        wp_deregister_style('styles-child-rtl');
        wp_register_style('styles-child-rtl', esc_url(get_stylesheet_directory_uri()) . '/style_rtl.css');
        wp_enqueue_style('styles-child-rtl');
    }
}

function hcf_register_meta_boxes() {
    add_meta_box('hcf-1', __('Doctors Timing', 'hcf'), 'hcf_display_callback', 'member');
    add_meta_box('hcf-2', __('Appointment Details', 'hcf2'), 'appointment_details_box', 'appointments');
}

add_action('add_meta_boxes', 'hcf_register_meta_boxes');

function hcf_display_callback($post) {
    $data = get_post_meta($post->ID, "_member_timing", true);
    if ($data != "") {
        $data = json_decode($data);
    }
    include plugin_dir_path(__FILE__) . './doctor-timings.php';
}

// Add to admin_init function
add_action('save_post_member', 'save_doctors_timing');

function save_doctors_timing($post_id) {

// verify if this is an averifyuto save routine. If it is our form has not been submitted, so we dont want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;


// Check permissions
    if (!current_user_can('edit_post', $post_id))
        return $post_id;


// OK, we're authenticated: we need to find and save the data   
    $post = get_post($post_id);
    $d_ = array("day" => $_POST['day'], "time" => $_POST['time'], "fee" => $_POST['fee']);

    if ($post->post_type == 'member') {
        update_post_meta($post_id, '_member_timing', json_encode($d_));
    }
    return $post_id;
}

function appointments_post_type() {

// Set UI labels for Custom Post Type
    $labels = array(
        'name' => _x('Appointments', 'Post Type General Name', 'twentythirteen'),
        'singular_name' => _x('Appointment', 'Post Type Singular Name', 'twentythirteen'),
        'menu_name' => __('Appointments', 'twentythirteen'),
        'parent_item_colon' => __('Parent Appointment', 'twentythirteen'),
        'all_items' => __('All Appointments', 'twentythirteen'),
        'view_item' => __('View Appointment', 'twentythirteen'),
        'add_new_item' => __('Add New Appointment', 'twentythirteen'),
        'add_new' => __('Add New', 'twentythirteen'),
        'edit_item' => __('Edit Appointment', 'twentythirteen'),
        'update_item' => __('Update Appointment', 'twentythirteen'),
        'search_items' => __('Search Appointment', 'twentythirteen'),
        'not_found' => __('Not Found', 'twentythirteen'),
        'not_found_in_trash' => __('Not found in Trash', 'twentythirteen'),
    );

// Set other options for Custom Post Type

    $args = array(
        'label' => __('appointments', 'twentythirteen'),
        'description' => __('Appointment news and reviews', 'twentythirteen'),
        'labels' => $labels,
        // Features this CPT supports in Post Editor
//'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields',),
        'supports' => array('title', 'revisions'),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        'taxonomies' => array('genres'),
        /* A hierarchical CPT is like Pages and can have
         * Parent and child items. A non-hierarchical CPT
         * is like Posts.
         */
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 7,
        'can_export' => true,
        'has_archive' => true,
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'page',
    );

// Registering your Custom Post Type
    register_post_type('appointments', $args);
}

/* Hook into the 'init' action so that the function
 * Containing our post type registration is not 
 * unnecessarily executed. 
 */

add_action('init', 'appointments_post_type', 0);

function appointment_details_box($post) {
    $data = get_post_meta($post->ID);
    include plugin_dir_path(__FILE__) . './appointment-details.php';
}

// Add to admin_init function
add_action('save_post_appointments', 'save_appointments_details');

function save_appointments_details($post_id) {

// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;


// Check permissions
    if (!current_user_can('edit_post', $post_id))
        return $post_id;


// OK, we're authenticated: we need to find and save the data   
//    $post = get_post($post_id);
//    $d_ = array("day" => $_POST['day'], "time" => $_POST['time']);
//
//    if ($post->post_type == 'member') {
//        update_post_meta($post_id, '_member_timing', json_encode($d_));
//    }
    return $post_id;
}

add_filter('manage_appointments_posts_columns', 'set_custom_edit_appointmentscolumns');

function set_custom_edit_appointmentscolumns($columns) {
    $d = $columns['date'];
    unset($columns['date']);

    $columns['patient_name'] = __('Patient Name', 'your_text_domain');
    $columns['HN'] = __('HN', 'your_text_domain');
    $columns['age'] = __('Age', 'your_text_domain');
    $columns['gender'] = __('Gender', 'your_text_domain');
    $columns['email'] = __('Email', 'your_text_domain');
    $columns['contact_number'] = __('Contact #', 'your_text_domain');
    $columns['appointment_date'] = __('Appintment Date #', 'your_text_domain');
    $columns['date'] = $d;
    return $columns;
}

// Add the data to the custom columns for the book post type:
add_action('manage_appointments_posts_custom_column', 'custom_appointments_column', 10, 2);

//function my_column_register_sortable( $columns )
//{
//    $columns['age'] = __('Age', 'your_text_domain');
//    return $columns;
//}
//
//add_filter("manage_edit-appointments_sortable_columns", "my_column_register_sortable" );


function custom_appointments_column($column, $post_id) {
    switch ($column) {

//add_post_meta($post_id, 'appointment_patient_name', trim($_POST['patient_name']));
//        add_post_meta($post_id, 'appointment_hn', trim($_POST['hn']));
//        add_post_meta($post_id, 'appointment_age', trim($_POST['age']));
//        add_post_meta($post_id, 'appointment_gender', (trim($_POST['gender'])==1) ? "Male" : "Female");
//        add_post_meta($post_id, 'appointment_email', trim($_POST['email']));
//        add_post_meta($post_id, 'appointment_contact_number', trim($_POST['contact_number']));
//        add_post_meta($post_id, 'appointment_date', trim($_POST['appointment_date']));
//        add_post_meta($post_id, 'appointment_address', trim($_POST['address']));
//        add_post_meta($post_id, 'appointment_comments', trim($_POST['comments']));

        case 'book_author' :
            $terms = get_the_term_list($post_id, 'book_author', '', ',', '');
            if (is_string($terms))
                echo $terms;
            else
                _e('Unable to get author(s)', 'your_text_domain');
            break;

        case 'publisher' :
            echo get_post_meta($post_id, 'publisher', true);
            break;

        case 'patient_name' :
            echo get_post_meta($post_id, 'appointment_patient_name', true);
            break;

        case 'HN' :
            echo get_post_meta($post_id, 'appointment_hn', true);
            break;

        case 'age' :
            echo get_post_meta($post_id, 'appointment_age', true);
            break;

        case 'gender' :
            echo get_post_meta($post_id, 'appointment_gender', true);
            break;

        case 'contact_number' :
            echo get_post_meta($post_id, 'appointment_contact_number', true);
            break;

        case 'email' :
            echo get_post_meta($post_id, 'appointment_email', true);
            break;

        case 'appointment_date' :
            echo get_post_meta($post_id, 'appointment_date', true);
            break;

//        add_post_meta($post_id, 'appointment_patient_name', trim($_POST['patient_name']));
//        add_post_meta($post_id, 'appointment_age', trim($_POST['age']));
//        add_post_meta($post_id, 'appointment_email', trim($_POST['email']));
//        add_post_meta($post_id, 'appointment_contact_number', trim($_POST['contact_number']));
//        add_post_meta($post_id, 'appointment_date', trim($_POST['appointment_date']));
//        add_post_meta($post_id, 'appointment_address', trim($_POST['address']));
    }
}

/* * ** Find a Doctor** */

function find_a_doctor_shortcode() {
    $output = '';
    ob_start();
    include "find-a-doctor.php";
    $output .= ob_get_clean();
    return $output;
}

add_shortcode('find-a-doctor', 'find_a_doctor_shortcode');




/* * ** FInd a Doctor** */


/* * ** Book Appointment** */

function book_appointment_shortcode() {
    $output = '';
    ob_start();
    include "book-appointment.php";
    $output .= ob_get_clean();
    return $output;
}

add_shortcode('book-appointment', 'book_appointment_shortcode');

function book_appointment_script() {
    ?>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
    <script type='text/javascript' src='https://cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js'></script>
    <?php
}

add_action('wp_footer', 'book_appointment_script');

function book_appointment_css() {
    ?>
    <link href="https://wordpress-236748-1019821.cloudwaysapps.com/wp-content/themes/porto-child/bootstrap-glyphicons.css" rel="stylesheet" />
    <link rel='stylesheet'  href='https://cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css' type='text/css' media='all' />
    <?php
}

add_action('wp_head', 'book_appointment_css');

add_action('wp_ajax_submit_book_appointment', 'submit_book_appointment');
add_action('wp_ajax_nopriv_submit_book_appointment', 'submit_book_appointment');

function submit_book_appointment() {
    if (!isset($_POST['name_of_nonce_field']) || !wp_verify_nonce($_POST['name_of_nonce_field'], 'submit_book_appointment_nonce')) {
        exit('The form is not valid');
    }
    $response = array(
        'error' => false,
    );
    if (trim($_POST['patient_name']) == '') {
        $response['error'] = true;
        $response['message'] = 'Patient name is required';
        exit(json_encode($response));
    } elseif (trim($_POST['age']) == '') {
        $response['error'] = true;
        $response['message'] = 'Age is required';
        exit(json_encode($response));
    } elseif (trim($_POST['email']) == '') {
        $response['error'] = true;
        $response['message'] = 'Email is required';
        exit(json_encode($response));
    } elseif (trim($_POST['contact_number']) == '') {
        $response['error'] = true;
        $response['message'] = 'Contact number is required';
        exit(json_encode($response));
    } elseif (trim($_POST['appointment_date']) == '') {
        $response['error'] = true;
        $response['message'] = 'Please select appointment date';
        exit(json_encode($response));
    } elseif (time() >= strtotime($_POST['appointment_date'])) {
        $response['error'] = true;
        $response['message'] = 'Invalid appintment date';
        exit(json_encode($response));
    } elseif (trim($_POST['address']) == '') {
        $response['error'] = true;
        $response['message'] = 'Address is required';
        exit(json_encode($response));
    } elseif (trim($_POST['doctor']) == '') {
        $response['error'] = true;
        $response['message'] = 'Invalud Data';
        exit(json_encode($response));
    }


    $post_id = wp_insert_post(array(
        'post_type' => 'appointments',
        'post_title' => trim($_POST['patient_name']),
        'post_content' => '',
        'post_status' => 'publish',
        'comment_status' => 'closed', // if you prefer
        'ping_status' => 'closed', // if you prefer
    ));
    if ($post_id) {
        add_post_meta($post_id, 'appointment_patient_name', trim($_POST['patient_name']));
        add_post_meta($post_id, 'appointment_hn', trim($_POST['hn']));
        add_post_meta($post_id, 'appointment_age', trim($_POST['age']));
        add_post_meta($post_id, 'appointment_gender', (trim($_POST['gender']) == 1) ? "Female" : "Male");
        add_post_meta($post_id, 'appointment_email', trim($_POST['email']));
        add_post_meta($post_id, 'appointment_contact_number', trim($_POST['contact_number']));
        add_post_meta($post_id, 'appointment_date', trim($_POST['appointment_date']));
        add_post_meta($post_id, 'appointment_address', trim($_POST['address']));
        add_post_meta($post_id, 'appointment_comments', trim($_POST['comments']));
        add_post_meta($post_id, 'appointment_doctor', trim($_POST['doctor']));
        $response['message'] = 'Your appointment has been booked.';
        $to = $_POST['email'];
        $subject = 'Your appointment has been booked';
        $message = 'Thank you for booking an appointment.';
        $headers = 'From: info@sehrishkhan.com' . "\r\n" .
                'Reply-To: info@sehrishkhan.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

        $d = mail($to, $subject, $message, $headers);
        $response["d"] = $d;
        $response["other"] = $_POST;
        $subject = 'New Appointment Notification';
        $message = 'New Appointment has been booked.';
        mail("sehrishkhandeveloper@gmail.com", $subject, $message, $headers);
    }



    exit(json_encode($response));
}

function loader_slh() {
    global $post_type;
    ?>
    <style>
        .loader_slh {
            display: none;
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url('<?php echo get_template_directory_uri() ?>/images/loading.gif') 50% 50% no-repeat rgba(249, 249, 249, 0.57);
            /*text-indent:-9999px;*/

        }
    </style>
    <script>
        jQuery('<div class="loader_slh">loading</div>').prependTo('body');
        jQuery(window).load(function () {
            //jQuery(".loader_slh").fadeOut("slow");
        })
    </script>
    <?php
}

add_action('wp_footer', 'loader_slh');

/* * ** Book Appointment** */

function doctor_by_category_shortcode($atts) {
    $output = '';

    ob_start();
    include "show-doctor-by-cat.php";
    $output .= ob_get_clean();
    return $output;
}

add_shortcode('show-doctor-by-cat', 'doctor_by_category_shortcode');

function wdm_add_mce_button() {
    // check user permissions
    $terms = get_terms([
        'taxonomy' => "member_cat",
        'hide_empty' => false,
    ]);
    ?>
    <script type="text/javascript">
        function abc_(editor) {
            var abc_ = [{
                    type: 'listbox',
                    name: 'listbox',
                    label: 'Categories',
                    values: [
    <?php
    if (!empty($terms)):
        foreach ($terms as $key => $value) {
            ?>
                                {text: '<?= $value->name ?>', value: '<?= $value->term_id ?>'},
            <?php
        }
    endif;
    ?>
                    ]
                }
            ];
            return abc_;
        }

    </script>
    <?php
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
        return;
    }
    // check if WYSIWYG is enabled
    if ('true' == get_user_option('rich_editing')) {
        add_filter('mce_external_plugins', 'wdm_add_tinymce_plugin');
        add_filter('mce_buttons', 'wdm_register_mce_button');
    }
}

add_action('admin_head', 'wdm_add_mce_button');

// register new button in the editor
function wdm_register_mce_button($buttons) {
    array_push($buttons, 'doctor-cat-btn');
    return $buttons;
}

// declare a script for the new button
// the script will insert the shortcode on the click event
function wdm_add_tinymce_plugin($plugin_array) {
    $plugin_array['doctor-cat-btn'] = get_stylesheet_directory_uri() . '/js/doctor-cat-mce-button.js';
    return $plugin_array;
}
/*add_filter('add_to_cart_redirect', 'cw_redirect_add_to_cart');
function cw_redirect_add_to_cart() {
    global $woocommerce;
    $cw_redirect_url_checkout = $woocommerce->cart->get_checkout_url();
    return $cw_redirect_url_checkout;
}*/
add_filter( 'woocommerce_product_single_add_to_cart_text', 'cw_btntext_cart' );
add_filter( 'woocommerce_product_add_to_cart_text', 'cw_btntext_cart' );

function cw_btntext_cart() {
    return __( 'Next', 'woocommerce' );
}

// Removes Order Notes Title - Additional Information & Notes Field
add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 9999 );



// Remove Order Notes Field
add_filter( 'woocommerce_checkout_fields' , 'remove_order_notes' );

function remove_order_notes( $fields ) {
     unset($fields['order']['order_comments']);
     return $fields;
}
/**
 * @snippet       Change "Place Order" Button text @ WooCommerce Checkout
 * @sourcecode    https://rudrastyh.com/?p=8327#woocommerce_order_button_text
 * @author        Misha Rudrastyh
 */
add_filter( 'woocommerce_order_button_text', 'misha_custom_button_text' );
 
function misha_custom_button_text( $button_text ) {
   return 'Donate'; // new text is here 
}
/**
 * @snippet       WooCommerce Max 1 Product @ Cart
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @compatible    WC 3.7
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
  
add_filter( 'woocommerce_add_to_cart_validation', 'bbloomer_only_one_in_cart', 99, 2 );
   
function bbloomer_only_one_in_cart( $passed, $added_product_id ) {
   wc_empty_cart();
   return $passed;
}
add_filter( 'wc_add_to_cart_message_html', '__return_false' );

add_filter( 'woocommerce_thankyou_order_received_text', 'avia_thank_you' );
function avia_thank_you() {
 $added_text = '<p>Thank you for your donation!</p>';
 return $added_text ;
}
add_filter('wpcf7_autop_or_not', '__return_false');
/**
 * @snippet Redirect to Checkout Upon Add to Cart - WooCommerce
*/ 
add_filter( 'woocommerce_add_to_cart_redirect', 'bbloomer_redirect_checkout_add_cart' );
function bbloomer_redirect_checkout_add_cart() {
   return wc_get_checkout_url();
}
add_filter( 'term_description', 'shortcode_unautop' );
add_filter( 'term_description', 'do_shortcode' );
remove_filter( 'pre_term_description', 'wp_filter_kses' );