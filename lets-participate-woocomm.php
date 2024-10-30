<?php
/*
 * Plugin Name: Let's Participate WooCommerce Link
 * Plugin URI: https://www.letsparticipate.com
 * Description: Link WooCommerce and Let's Participate.
 * Version: 1.0.2
 * Author: Richard Plant, Luke Downing
 * Author URI: https://www.letsparticipate.com
*/

add_action('admin_menu', 'lp_plugin_menu');
add_action('admin_init', 'letspart_register_settings');

function lp_plugin_menu()
{
    add_menu_page('Lets Participate Settings', 'LP Settings', 'administrator', 'lp-plugin-settings', 'letspart_plugin_settings_page', 'dashicons-admin-generic');
}

/**
 * Adds a notification that the user must add a js token before the plugin will work
 */
function letspart_add_notice_no_js_token_or_eventid()
{
    ?>
    <div class="notice notice-error">
        <p>
            The Let's Participate plugin will not work unless you enter a valid JavaScript token and your organisation
            ID.
            <a href="<?php echo admin_url('options-general.php?page=lp-plugin-settings&highlight=token') ?>">Click
                here to add
                them.</a>
        </p>
    </div>
    <?php
}

if (empty(esc_attr(get_option('lp-js-token'))) || empty(esc_attr(get_option('lp-org-id')))) {
    add_action('admin_notices', 'letspart_add_notice_no_js_token_or_eventid');
}

function letspart_add_notice_no_woocommerce()
{
    ?>
    <div class="notice notice-error">
        <p>
            The Let's Participate plugin will not work unless WooCommerce is installed and activated.
        </p>
    </div>
    <?php
}

if (!class_exists('WooCommerce')) {
    add_action('admin_notices', 'letspart_add_notice_no_woocommerce');
}

/**
 * Registers our custom settings with WooCommerce
 */
function letspart_register_settings()
{

    // Add the options to the db if need be
    add_option('lp-js-token', "", "", "yes");
    add_option('lp-org-id', "", "", "yes");
    add_option('lp-part-lookup', "", "", "yes");
    add_option('lp-email-organisers', "", "", "yes");
    add_option('lp-field-town', "", "", "yes");

    // Register the settings
    register_setting('lp-group', 'lp-js-token');
    register_setting('lp-group', 'lp-org-id');
    register_setting('lp-group', 'lp-part-lookup');
    register_setting('lp-group', 'lp-email-organisers');
    register_setting('lp-group', 'lp-field-town');
}

function letspart_plugin_settings_page()
{
    ?>
    <div class="wrap">
        <div>
            <h2>Let's Participate WooCommerce plugin</h2>

            <form action="options.php" method="post">

                <?php settings_fields('lp-group'); ?>
                <?php do_settings_sections('lp-group'); ?>

                <style>
                    table {
                        width: 100%;
                        border-spacing: 20px;
                    }

                    th {
                        text-align: left;
                    }

                    <?php if (isset($_GET["highlight"]) && $_GET["highlight"] == "token" && empty( esc_attr( get_option( 'lp-js-token' ) ) )) { ?>
                    #table-entry-js-token td:last-child {
                        border: 2px solid red;
                    }

                    <?php } ?>

                    <?php if (isset($_GET["highlight"]) && $_GET["highlight"] == "token" && empty( esc_attr( get_option( 'lp-org-id' ) ) )) { ?>
                    #table-entry-org-id td:last-child {
                        border: 2px solid red;
                    }

                    <?php } ?>
                </style>

                <table>
                    <tr valign="top" id="table-entry-js-token">
                        <th scope="row">Javascript access token</th>
                        <td>
							<span>
								Enter the access token you have been provided with.
								Without this token, the plugin will not work correctly.
							</span>
                        </td>
                        <td valign="middle">
                            <input type="text" name="lp-js-token"
                                   value="<?php echo esc_attr(get_option('lp-js-token')) ?>"/>
                        </td>
                    </tr>

                    <tr valign="top" id="table-entry-org-id">
                        <th scope="row">Organisation ID</th>
                        <td>
							<span>
								Enter your organisation ID found on Let's Participate.
							</span>
                        </td>
                        <td valign="middle">
                            <input type="number" name="lp-org-id"
                                   value="<?php echo esc_attr(get_option('lp-org-id')) ?>"/>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Allow participant lookup</th>
                        <td>
							<span>
								When enabled, participants may use their details
								from previous events of yours to sign up again quickly.
								This also maintains their history so if you reward
								participants for attending more than one of your event,
								you should enable this.
							</span>
                        </td>
                        <td valign="middle">
                            <input type="checkbox" name="lp-part-lookup"
                                <?php echo esc_attr(get_option('lp-part-lookup')) ? "checked" : "" ?>/>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Send forgotten details email to organisers</th>
                        <td>
							<span>
								When a participant requests a reminder email for their details,
								it will usually be automatically generated and sent directly
								to them. If you would rather the organisers of your event receive
								this email so that you can handle this directly, enable this.
							</span>
                        </td>
                        <td valign="middle">
                            <input type="checkbox" name="lp-email-organisers"
                                <?php echo esc_attr(get_option('lp-email-organisers')) ? "checked" : "" ?>/>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Show town/province field</th>
                        <td>
							<span>
								When enabled, the participant will be required to
								enter their town/province prior to signing up.
							</span>
                        </td>
                        <td valign="middle">
                            <input type="checkbox" name="lp-field-town"
                                <?php echo esc_attr(get_option('lp-field-town')) ? "checked" : "" ?>/>
                        </td>
                    </tr>
                </table>

                <?php submit_button("Save changes"); ?>
            </form>
        </div>
    </div>
    <?php
}


add_action('init', 'letspart_start_session', 1);
add_action('wp_logout', 'letspart_end_session');
add_action('wp_login', 'letspart_end_session');

function letspart_start_session()
{
    if (!session_id()) {
        session_start();
    }
}

function letspart_end_session()
{
    session_destroy();
}


/**
 * Add js scripts to be used
 */
function letspart_setup_js_add()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('newscript', plugins_url('lets-participate.js', __FILE__));
    wp_enqueue_style('jquery-ui-date-picker-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
}

add_action('wp_enqueue_scripts', 'letspart_setup_js_add');


/**
 * @param $columns
 * Removes 'order actions' and 'shipping address' and adds 'walker id' to the orders table
 *
 * @return array
 */
function letspart_show_product_order($columns)
{

    $new_columns = (is_array($columns)) ? $columns : array();

    //remove column
    unset($new_columns['order_actions']);
    unset($new_columns['shipping_address']);

    //add column
    $new_columns['walker_id'] = __('Walker Id');

    return $new_columns;
}

add_filter('manage_edit-shop_order_columns', 'letspart_show_product_order', 15);

/**
 * Allows for a custom column in the orders table
 *
 * @param $column The column to be manipulated
 */
function letspart_custom_shop_order_column($column)
{
    global $post, $woocommerce, $the_order;

    switch ($column) {

        case 'walker_id' :

            echo "ID: " . get_post_meta($the_order->id, 'Walker Id', true);

            break;
    }
}

add_action('manage_shop_order_posts_custom_column', 'letspart_custom_shop_order_column', 10, 2);


/**
 * Let's Participate - WooCommerce Update
 * Function to remove unneeded fields from the checkout
 */
function letspart_custom_override_checkout_fields($fields)
{
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    //unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_postcode']);
    //unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_company']);

    $checkout_show_town = get_option('field-town');
    if (empty($checkout_show_town)) {
        unset($fields['billing']['billing_city']);
    }

    return $fields;
}

add_filter('woocommerce_checkout_fields', 'letspart_custom_override_checkout_fields');

/**
 * Let's Participate - WooCommerce Update
 * Function to add extra fields before the checkout
 */
add_action('woocommerce_before_checkout_billing_form', 'letspart_walked_before_fields');
function letspart_walked_before_fields($checkout)
{
    if (!esc_attr(get_option('lp-part-lookup'))) {
        return;
    }

    if (isset($_GET['lang'])) {
        $lp_lang = $_GET['lang'];
    } else {
        $lp_lang = "en";
    }

    switch ($lp_lang) {
        case "en":
            // Can't remember details strings
            $lp_cant_remember_details_label = "Walked before but can't remember your details? Click here!";
            $lp_cant_remember_details_title = "Can't remember your details?";
            $lp_cant_remember_details_p = "Don't worry, we're able to recover your details for you.
                Simply type the email you've signed up with previously
                below and click submit.
                We'll send you an email shortly with your correct details.";
            $lp_action_submit = "Submit";
            $lp_unassoc_email_p = "The email entered has never been associated with one of our events.
                Please enter the email used for previous events.";
            $lp_reminder_email_sent = "We'll send an email to the address provided with your walker details. Please check that email and then re-enter your details here";

            // Enabled js strings
            $lp_enabled_js = "Javascript must be enabled for checkout to work";

            // Previous details strings
            $lp_title_label = 'Have you participated in this event before?';
            $lp_action_yes = "Yes";
            $lp_action_no = "No";
            $lp_prev_walker_instructions = 'Please enter your participant ID and date of birth in the fields below.';
            $lp_walker_dob_label = 'Walker Date of Birth';
            $lp_walkerId_label = 'Walker ID';

            break;
        default:
            // Can't remember details strings
            $lp_cant_remember_details_label = "Walked before but can't remember your details? Click here!";
            $lp_cant_remember_details_title = "Can't remember your details?";
            $lp_cant_remember_details_p = "Don't worry, we're able to recover your details for you.
                Simply type the email you've signed up with previously
                below and click submit.
                We'll send you an email shortly with your correct details.";
            $lp_action_submit = "Submit";
            $lp_unassoc_email_p = "The email entered has never been associated with one of our events.
                Please enter the email used for previous events.";
            $lp_reminder_email_sent = "We'll send an email to the address provided with your walker details. Please check that email and then re-enter your details here";

            // Enabled js strings
            $lp_enabled_js = "Javascript must be enabled for checkout to work";

            // Previous details strings
            $lp_title_label = 'Have you participated in this event before?';
            $lp_action_yes = "Yes";
            $lp_action_no = "No";
            $lp_prev_walker_instructions = 'Please enter your participant ID and date of birth in the fields below.';
            $lp_walker_dob_label = 'Walker Date of Birth';
            $lp_walkerId_label = 'Walker ID';

            break;
    }

    // This is the modal for those who have forgotten their details
    ?>
    <style>
        #lp_forgotten_details_modal {
            display: none;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            position: fixed;
            background: rgba(0, 0, 0, 0.7);
            z-index: 100000000;
            justify-content: center;
            align-items: center;
        }

        #lp_forgotten_details_modal .content {
            width: 50%;
            height: auto;
            margin: auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
        }

        #lp_forgotten_details_modal .content input {
            margin-bottom: 10px;
        }

        #lp_forgotten_details_modal p:last-child {
            margin-top: 10px;
            display: none;
            color: #dd382d;
        }

        #reminder-email-sent {
            display: none;
        }

        #spinner {
            background: #fff;
            padding: 4px;
            border-radius: 99%;
            margin-left: 10px;
        }

        #part-details-form {
            display: none;
        }

        #letspart_custom_checkout_field {
            display: none;
        }
    </style>

    <!-- Spinner -->
    <img id="spinner" style="display: none" src="<?= plugin_dir_url(__FILE__); ?>spinner.gif"/>

    <div id="lp_forgotten_details_modal">
        <div class="content">
            <h3><?= __($lp_cant_remember_details_title, 'letsparticipate') ?></h3>
            <p>
                <?= __($lp_cant_remember_details_p, 'letsparticipate') ?>
            </p>
            <input type="hidden" id="emailOrganisers"
                   value="<?= esc_attr(get_option('lp-email-organisers')) ? "true" : "false" ?>"/>
            <input id="forgottenEmailField" type="email" placeholder="Your email">
            <button><?= __($lp_action_submit, 'letsparticipate') ?></button>
            <p>
                <?= __($lp_unassoc_email_p, 'letsparticipate') ?>
            </p>
        </div>
    </div>

    <div style="margin-bottom: 20px">
        <noscript style="color:red">
            <?= __($lp_enabled_js, 'letsparticipate') ?>
        </noscript>
        <h4 id="walked_before_title"><?= __($lp_title_label, 'letsparticipate') ?></h4>
        <button id="btn-walked-before"><?= __($lp_action_yes, 'letsparticipate') ?></button>
        <button id="btn-not-walked-before"><?= __($lp_action_no, 'letsparticipate') ?></button>
    </div>

    <div id="letspart_custom_checkout_field">

        <p><?= __($lp_prev_walker_instructions, 'letsparticipate') ?></p>
        <input id="lp_auth_token" type="hidden" value="<?= esc_attr(get_option('lp-js-token')) ?>"/>

        <p id="reminder-email-sent">
            <?= __($lp_reminder_email_sent, 'letsparticipate') ?>
        </p>

        <?php
        if (isset($_SESSION["lppid"]) && isset($_SESSION["lpdob"])) {
            $eventPartId = $_SESSION["lppid"];
            $dob = $_SESSION["lpdob"];
            unset($_SESSION["lppid"]);
            unset($_SESSION["lpdob"]);
        }

        //	Create the walker id field
        woocommerce_form_field('lp_walkerId', array(
            'type' => 'number',
            'class' => array('my-field-class form-row-wide'),
            'label' => __($lp_walkerId_label, 'letsparticipate'),
            'placeholder' => __('Walker ID (eg: 7431)'),
        ), isset($eventPartId) ? $eventPartId : $checkout->get_value('lp_walkerId'));
        //	Create the walker dob field
        woocommerce_form_field('lp_prevWalkerDOB', array(
            'type' => 'text',
            'class' => array('js-dob-picker form-row-first form-row-wide'),
            'label' => __($lp_walker_dob_label, 'letsparticipate'),
            'placeholder' => __('01-01-1950'),
        ), isset($dob) ? $dob : $checkout->get_value('lp_prevWalkerDOB'));


        echo explode("-", get_locale(), 2)[0];
        ?>

        <button id="check-details" style="width:100%">Check details</button>
        <a id="cant-remember-link"><p class="form-row-wide"><br><?= __($lp_cant_remember_details_label, 'letsparticipate') ?></p></a>
    </div>

    <div id="part-details-form">
    <?php
}

/**
 * Let's Participate - WooCommerce Update
 * Function to add extra fields to the checkout after the other billing forms
 */
function letspart_custom_checkout_field($checkout)
{

    if (isset($_GET['lang'])) {
        $lp_lang = $_GET['lang'];
    } else {
        $lp_lang = "en";
    }

    $lp_nationality_array = array(
        "Afghan" => "Afghan",
        "Albanian" => "Albanian",
        "Algerian" => "Algerian",
        "American" => "American",
        "Andorran" => "Andorran",
        "Angolan" => "Angolan",
        "Antiguans" => "Antiguans",
        "Argentinean" => "Argentinean",
        "Armenian" => "Armenian",
        "Australian" => "Australian",
        "Austrian" => "Austrian",
        "Azerbaijani" => "Azerbaijani",
        "Bahamian" => "Bahamian",
        "Bahraini" => "Bahraini",
        "Bangladeshi" => "Bangladeshi",
        "Barbadian" => "Barbadian",
        "Barbudans" => "Barbudans",
        "Batswana" => "Batswana",
        "Belarusian" => "Belarusian",
        "Belgian" => "Belgian",
        "Belizean" => "Belizean",
        "Beninese" => "Beninese",
        "Bhutanese" => "Bhutanese",
        "Bolivian" => "Bolivian",
        "Bosnian" => "Bosnian",
        "Brazilian" => "Brazilian",
        "British" => "British",
        "Bruneian" => "Bruneian",
        "Bulgarian" => "Bulgarian",
        "Burkinabe" => "Burkinabe",
        "Burmese" => "Burmese",
        "Burundian" => "Burundian",
        "Cambodian" => "Cambodian",
        "Cameroonian" => "Cameroonian",
        "Canadian" => "Canadian",
        "Cape Verdean" => "Cape Verdean",
        "Central African" => "Central African",
        "Chadian" => "Chadian",
        "Chilean" => "Chilean",
        "Chinese" => "Chinese",
        "Colombian" => "Colombian",
        "Comoran" => "Comoran",
        "Congolese" => "Congolese",
        "Costa Rican" => "Costa Rican",
        "Croatian" => "Croatian",
        "Cuban" => "Cuban",
        "Cypriot" => "Cypriot",
        "Czech" => "Czech",
        "Danish" => "Danish",
        "Djibouti" => "Djibouti",
        "Dominican" => "Dominican",
        "Dutch" => "Dutch",
        "East Timorese" => "East Timorese",
        "Ecuadorean" => "Ecuadorean",
        "Egyptian" => "Egyptian",
        "Emirian" => "Emirian",
        "Equatorial Guinean" => "Equatorial Guinean",
        "Eritrean" => "Eritrean",
        "Estonian" => "Estonian",
        "Ethiopian" => "Ethiopian",
        "Fijian" => "Fijian",
        "Filipino" => "Filipino",
        "Finnish" => "Finnish",
        "French" => "French",
        "Gabonese" => "Gabonese",
        "Gambian" => "Gambian",
        "Georgian" => "Georgian",
        "German" => "German",
        "Ghanaian" => "Ghanaian",
        "Greek" => "Greek",
        "Grenadian" => "Grenadian",
        "Guatemalan" => "Guatemalan",
        "Guinea-Bissauan" => "Guinea-Bissauan",
        "Guinean" => "Guinean",
        "Guyanese" => "Guyanese",
        "Haitian" => "Haitian",
        "Herzegovinian" => "Herzegovinian",
        "Honduran" => "Honduran",
        "Hungarian" => "Hungarian",
        "I-Kiribati" => "I-Kiribati",
        "Icelander" => "Icelander",
        "Indian" => "Indian",
        "Indonesian" => "Indonesian",
        "Iranian" => "Iranian",
        "Iraqi" => "Iraqi",
        "Irish" => "Irish",
        "Israeli" => "Israeli",
        "Italian" => "Italian",
        "Ivorian" => "Ivorian",
        "Jamaican" => "Jamaican",
        "Japanese" => "Japanese",
        "Jordanian" => "Jordanian",
        "Kazakhstani" => "Kazakhstani",
        "Kenyan" => "Kenyan",
        "Kittian and Nevisian" => "Kittian and Nevisian",
        "Kuwaiti" => "Kuwaiti",
        "Kyrgyz" => "Kyrgyz",
        "Laotian" => "Laotian",
        "Latvian" => "Latvian",
        "Lebanese" => "Lebanese",
        "Liberian" => "Liberian",
        "Libyan" => "Libyan",
        "Liechtensteiner" => "Liechtensteiner",
        "Lithuanian" => "Lithuanian",
        "Luxembourger" => "Luxembourger",
        "Macedonian" => "Macedonian",
        "Malagasy" => "Malagasy",
        "Malawian" => "Malawian",
        "Malaysian" => "Malaysian",
        "Maldivan" => "Maldivan",
        "Malian" => "Malian",
        "Maltese" => "Maltese",
        "Marshallese" => "Marshallese",
        "Mauritanian" => "Mauritanian",
        "Mauritian" => "Mauritian",
        "Mexican" => "Mexican",
        "Micronesian" => "Micronesian",
        "Moldovan" => "Moldovan",
        "Monacan" => "Monacan",
        "Mongolian" => "Mongolian",
        "Moroccan" => "Moroccan",
        "Mosotho" => "Mosotho",
        "Motswana" => "Motswana",
        "Mozambican" => "Mozambican",
        "Namibian" => "Namibian",
        "Nauruan" => "Nauruan",
        "Nepalese" => "Nepalese",
        "New Zealander" => "New Zealander",
        "Nicaraguan" => "Nicaraguan",
        "Nigerian" => "Nigerian",
        "Nigerien" => "Nigerien",
        "North Korean" => "North Korean",
        "Northern Irish" => "Northern Irish",
        "Norwegian" => "Norwegian",
        "Omani" => "Omani",
        "Pakistani" => "Pakistani",
        "Palauan" => "Palauan",
        "Panamanian" => "Panamanian",
        "Papua New Guinean" => "Papua New Guinean",
        "Paraguayan" => "Paraguayan",
        "Peruvian" => "Peruvian",
        "Polish" => "Polish",
        "Portuguese" => "Portuguese",
        "Qatari" => "Qatari",
        "Romanian" => "Romanian",
        "Russian" => "Russian",
        "Rwandan" => "Rwandan",
        "Saint Lucian" => "Saint Lucian",
        "Salvadoran" => "Salvadoran",
        "Samoan" => "Samoan",
        "San Marinese" => "San Marinese",
        "Sao Tomean" => "Sao Tomean",
        "Saudi" => "Saudi",
        "Scottish" => "Scottish",
        "Senegalese" => "Senegalese",
        "Serbian" => "Serbian",
        "Seychellois" => "Seychellois",
        "Sierra Leonean" => "Sierra Leonean",
        "Singaporean" => "Singaporean",
        "Slovakian" => "Slovakian",
        "Slovenian" => "Slovenian",
        "Solomon Islander" => "Solomon Islander",
        "Somali" => "Somali",
        "South African" => "South African",
        "South Korean" => "South Korean",
        "Spanish" => "Spanish",
        "Sri Lankan" => "Sri Lankan",
        "Sudanese" => "Sudanese",
        "Surinamer" => "Surinamer",
        "Swazi" => "Swazi",
        "Swedish" => "Swedish",
        "Swiss" => "Swiss",
        "Syrian" => "Syrian",
        "Taiwanese" => "Taiwanese",
        "Tajik" => "Tajik",
        "Tanzanian" => "Tanzanian",
        "Thai" => "Thai",
        "Togolese" => "Togolese",
        "Tongan" => "Tongan",
        "Trinidadian or Tobagonian" => "Trinidadian or Tobagonian",
        "Tunisian" => "Tunisian",
        "Turkish" => "Turkish",
        "Tuvaluan" => "Tuvaluan",
        "Ugandan" => "Ugandan",
        "Ukrainian" => "Ukrainian",
        "Uruguayan" => "Uruguayan",
        "Uzbekistani" => "Uzbekistani",
        "Venezuelan" => "Venezuelan",
        "Vietnamese" => "Vietnamese",
        "Welsh" => "Welsh",
        "Yemenite" => "Yemenite",
        "Zambian" => "Zambian",
        "Zimbabwean" => "Zimbabwean",
        "" => "Otros"
    );

    switch ($lp_lang) {
        case "en":
            // Form labels
            $lp_walker_dob_label = 'Walker Date of Birth';
            $lp_gender_label = 'Gender';
            $lp_nationality_label = 'Nationality';
            $lp_walkerDistance_label = "Desired walking distance";
            break;
        default:
            // Form labels
            $lp_walker_dob_label = 'Walker Date of Birth';
            $lp_gender_label = 'Gender';
            $lp_nationality_label = 'Nationality';
            $lp_walkerDistance_label = "Desired walking distance";
            break;
    }

    woocommerce_form_field('lp_walkerDOB', array(
        'type' => 'text',
        'class' => array('js-dob-picker form-row-first'),
        'label' => __($lp_walker_dob_label, 'letsparticipate'),
        'placeholder' => __('01-01-1950'),
    ), $checkout->get_value('lp_walkerDOB'));

    woocommerce_form_field('lp_walkerGender', array(
        'type' => 'select',
        'class' => array('form-row-last'),
        'label' => __($lp_gender_label),
        'options' => array(
            'm' => 'Male',
            'f' => 'Female',
            '-' => 'Decline to state'
        ),
    ), $checkout->get_value('lp_walkerGender'));
    woocommerce_form_field('lp_walkerDistance', array(
        'type' => 'select',
        'class' => array('form-row-wide'),
        'label' => __($lp_walkerDistance_label),
        'options' => array(
            '20' => '20km',
            '30' => '30km'
        ),
    ), $checkout->get_value('lp_walkerDistance'));
    woocommerce_form_field('lp_walkerNationality', array(
        'type' => 'select',
        'class' => array('form-row-first'),
        'label' => __($lp_nationality_label, 'letsparticipate'),
        'options' => $lp_nationality_array,
        'placeholder' => __(''),
    ), $checkout->get_value('lp_walkerNationality'));

    ?>
    </div>

    <?php
}

add_action('woocommerce_after_checkout_billing_form', 'letspart_custom_checkout_field');


/**
 * Let's Participate - WooCommerce Update
 * Function to catch missing data
 */
function letspart_custom_checkout_field_process()
{
    // Check if set, if its not set add an error.
    if (!$_POST['lp_walkerDOB']) {
        wc_add_notice(__('Please enter your date of birth'), 'error');
    }
    if (!$_POST['lp_walkerGender']) {
        wc_add_notice(__('Please enter your gender'), 'error');
    }
}

add_action('woocommerce_checkout_process', 'letspart_custom_checkout_field_process');


/**
 * Let's Participate - WooCommerce Update
 * Function to record extra fields meta with field value
 */
add_action('woocommerce_checkout_update_order_meta', 'letspart_custom_checkout_field_update_order_meta');

function letspart_custom_checkout_field_update_order_meta($order_id)
{
    if (!empty($_POST['lp_walkerId'])) {
        update_post_meta($order_id, 'Walker Id', sanitize_text_field($_POST['lp_walkerId']));
    }
    if (!empty($_POST['lp_prevWalkerDOB'])) {
        update_post_meta($order_id, 'Walker previous date of birth', sanitize_text_field($_POST['lp_prevWalkerDOB']));
    }
    if (!empty($_POST['lp_walkerDOB'])) {
        update_post_meta($order_id, 'Walker Date of Birth', sanitize_text_field($_POST['lp_walkerDOB']));
    }
    if (!empty($_POST['lp_walkerGender'])) {
        update_post_meta($order_id, 'Walker Gender', sanitize_text_field($_POST['lp_walkerGender']));
    }
    if (!empty($_POST['lp_walkerNationality'])) {
        update_post_meta($order_id, 'Walker Nationality', sanitize_text_field($_POST['lp_walkerNationality']));
    }
    if (!empty($_POST['lp_walkerDistance'])) {
        update_post_meta($order_id, 'Walker Distance', sanitize_text_field($_POST['lp_walkerDistance']));
    }
    if (!empty(get_locale())) {
        $lang = explode("_", get_locale(), 2)[0];

        if (in_array(strtoupper($lang), array("EN", "ES", "NL"))) {
            update_post_meta($order_id, 'Walker Language', sanitize_text_field($lang));
        } else {
            update_post_meta($order_id, 'Walker Language', 'nl');
        }
    }
}


/**
 * Let's Participate - WooCommerce Update
 * Function to add extra fields to the admin
 */
add_action('woocommerce_admin_order_data_after_billing_address', 'letspart_custom_checkout_field_display_admin_order_meta', 10, 1);

function letspart_custom_checkout_field_display_admin_order_meta($order)
{
    echo '<p><strong>' . __('Walker Id') . ':</strong> ' . get_post_meta($order->id, 'Walker Id', true) . '</p>';
    echo '<p><strong>' . __('Walker Date of Birth') . ':</strong> ' . get_post_meta($order->id, 'Walker Date of Birth', true) . '</p>';
    echo '<p><strong>' . __('Walker Gender') . ':</strong> ' . get_post_meta($order->id, 'Walker Gender', true) . '</p>';
    echo '<p><strong>' . __('Walker Nationality') . ':</strong> ' . get_post_meta($order->id, 'Walker Nationality', true) . '</p>';
    echo '<p><strong>' . __('Walker Distance') . ':</strong> ' . get_post_meta($order->id, 'Walker Distance', true) . '</p>';
    echo '<p><strong>' . __('Walker Language') . ':</strong> ' . get_post_meta($order->id, 'Walker Language', true) . '</p>';
}


/**
 * Let's Participate - TEST FOR WEBHOOK
 * Function to...
 */
add_action('woocommerce_api_order_data', 'letspart_woocommerce_cli_order_data');

function letspart_woocommerce_cli_order_data($order_data)
{
    $order_data["lp_data"]["lp_test"] = "True";
    $order_data["lp_data"]["lp_walkerId"] = get_post_meta($order->id, 'Walker Id', true);
    $order_data["lp_data"]["lp_walkerId_id"] = get_post_meta($order->id, 'lp_walkerId', true);

    return $order_data;
}


/**
 * Let's Participate - TEST FOR WEBHOOK
 * Function to add data to WooCommerce Webhook payload
 */
add_action('woocommerce_webhook_payload', 'letspart_woocommerce_webhook_payload');

function letspart_woocommerce_webhook_payload($payload, $resource, $resource_id, $id)
{
    $payload["lp_data"]["lp_test"] = "True payload";

    $payload["lp_data"]["lp_org"] = esc_attr(get_option('lp-org-id'));

    $payload["lp_data"]["lp_walkerId"] = get_post_meta($payload["order"]["id"], 'Walker Id', true);
    $payload["lp_data"]["lp_walkerPrevDOB"] = get_post_meta($payload["order"]["id"], 'Walker previous date of birth', true);
    $payload["lp_data"]["lp_walkerDOB"] = get_post_meta($payload["order"]["id"], 'Walker Date of Birth', true);
    $payload["lp_data"]["lp_walkerNationality"] = get_post_meta($payload["order"]["id"], 'Walker Nationality', true);
    $payload["lp_data"]["lp_walkerGender"] = get_post_meta($payload["order"]["id"], 'Walker Gender', true);
    $payload["lp_data"]["lp_walkerDistance"] = get_post_meta($payload["order"]["id"], 'Walker Distance', true);
    $payload["lp_data"]["lp_walkerLanguage"] = get_post_meta($payload["order"]["id"], 'Walker Language', true);

    return $payload;
}


/**
 * This is fired before get_header is called on all pages
 * @param $name the specific header file to use
 */
function letspart_getheader_hook($name)
{
    global $woocommerce;

    if (isset($_GET["lpco"]) && $_GET["lpco"] == true && !is_checkout()) {
        // If there is a dob or event_user_id set save them to the session
        if (isset($_GET["lppid"])) $_SESSION["lppid"] = $_GET["lppid"];
        if (isset($_GET["lpdob"])) $_SESSION["lpdob"] = $_GET["lpdob"];

        if (isset($_GET["lpsku"])) {
            $productId = wc_get_product_id_by_sku($_GET["lpsku"]);
            if ($productId > 0) {
                $woocommerce->cart->empty_cart();
                $woocommerce->cart->add_to_cart($productId);
            }
            // Product added to the cart, go to the checkout
            header("Location: " . $woocommerce->cart->get_checkout_url());
        } else {
            // There are no products to add, we should go to the cart so that they can choose one
            header("Location: " . get_permalink(wc_get_page_id('shop')));
        }
    }
}

add_action('get_header', 'letspart_getheader_hook');

/**
 * Let's Participate - TEST FOR WEBHOOK
 * Function to add date picker to checkout
 */
add_action('wp_footer', 'letspart_checkout_date_picker', 50);

function letspart_checkout_date_picker()
{
    if (is_checkout()) {
        //wp_enqueue_script( 'jquery' );
        //wp_enqueue_style('jquery-ui-date-picker-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
        //wp_enqueue_script('jquery-ui-core');
        //wp_enqueue_script('jquery-ui-datepicker');
        //wp_enqueue_script("wp-includes/js/jquery/ui/datepicker.min.js");
        //wp_enqueue_script( 'jquery-datepicker', 'http://jquery-ui.googlecode.com/svn/trunk/ui/jquery.ui.datepicker.js', array('jquery', 'jquery-ui-core' ) );
        //wp_enqueue_script('jquery-datepicker');
        add_action('wp_enqueue_scripts', 'letspart_setup_js_add'); ?>
        <!--<link rel="stylesheet" href="/extras/slimpicker.css">
        <script src="/extras/mootools-1.2.4-core-yc.js"></script>
        <script src="/extras/mootools-1.2.4.4-more-yc.js"></script>
        <script src="/extras/slimpicker.js"></script>
        <script type="text/javascript">
$$('#lp_walkerDOB').each( function(el){
    var picker = new SlimPicker(el);
});
        </script>-->
    <?php }
}


/**
 * Let's Participate - TEST FOR WEBHOOK
 * Function to limit cart to one item.
 * NOTE: removed as need to allow donation
 *
 * add_filter( 'woocommerce_add_cart_item_data', 'woo_custom_add_to_cart' );
 *
 * function woo_custom_add_to_cart( $cart_item_data ) {
 *
 * global $woocommerce;
 * $woocommerce->cart->empty_cart();
 *
 * // Do nothing with the data and return
 * return $cart_item_data;
 * }
 */


?>