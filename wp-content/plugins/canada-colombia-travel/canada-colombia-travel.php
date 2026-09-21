<?php
/**
 * Plugin Name: Canada Colombia Travel Leads
 * Description: Trilingual travel quote form, private lead storage, and WhatsApp handoff.
 * Version: 1.1.0
 * Author: Flatlogic
 */
if (!defined('ABSPATH')) { exit; }

function cc_travel_lang($value): string {
    $lang = sanitize_key((string) $value);
    return in_array($lang, ['en', 'fr', 'es'], true) ? $lang : 'en';
}

function cc_travel_text(string $lang, string $key): string {
    $strings = [
        'en' => [
            'departure' => 'Departure city', 'arrival' => 'Arrival city', 'choose' => 'Choose a city',
            'depart_date' => 'Departure date', 'return_date' => 'Return date', 'travelers' => 'Travelers',
            'phone' => 'Phone / WhatsApp', 'name' => 'Your name', 'email' => 'Email',
            'notes' => 'Preferences or tourism plans', 'notes_placeholder' => 'Baggage, hotel, excursions…',
            'submit' => 'Continue to WhatsApp →', 'note' => 'Your request is saved before WhatsApp opens. No online payment is taken.',
            'colombia' => 'Colombia', 'required' => 'Please complete the required fields.',
            'ready' => 'Your request is ready.', 'saved' => 'Request saved!',
            'opening' => 'WhatsApp will open with your prefilled request.', 'open' => 'Open WhatsApp now',
        ],
        'fr' => [
            'departure' => 'Ville de départ', 'arrival' => 'Ville d’arrivée', 'choose' => 'Choisir une ville',
            'depart_date' => 'Date de départ', 'return_date' => 'Date de retour', 'travelers' => 'Voyageurs',
            'phone' => 'Téléphone / WhatsApp', 'name' => 'Votre nom', 'email' => 'Courriel',
            'notes' => 'Préférences ou projet touristique', 'notes_placeholder' => 'Bagages, hôtel, excursions…',
            'submit' => 'Continuer sur WhatsApp →', 'note' => 'Votre demande sera enregistrée avant l’ouverture de WhatsApp. Aucun paiement en ligne.',
            'colombia' => 'Colombie', 'required' => 'Veuillez remplir les champs obligatoires.',
            'ready' => 'Votre demande est prête.', 'saved' => 'Demande enregistrée!',
            'opening' => 'WhatsApp va s’ouvrir avec votre demande préremplie.', 'open' => 'Ouvrir WhatsApp maintenant',
        ],
        'es' => [
            'departure' => 'Ciudad de salida', 'arrival' => 'Ciudad de llegada', 'choose' => 'Elegir una ciudad',
            'depart_date' => 'Fecha de salida', 'return_date' => 'Fecha de regreso', 'travelers' => 'Viajeros',
            'phone' => 'Teléfono / WhatsApp', 'name' => 'Tu nombre', 'email' => 'Correo electrónico',
            'notes' => 'Preferencias o plan turístico', 'notes_placeholder' => 'Equipaje, hotel, excursiones…',
            'submit' => 'Continuar por WhatsApp →', 'note' => 'Guardaremos tu solicitud antes de abrir WhatsApp. No se realizan pagos en línea.',
            'colombia' => 'Colombia', 'required' => 'Completa los campos obligatorios.',
            'ready' => 'Tu solicitud está lista.', 'saved' => '¡Solicitud guardada!',
            'opening' => 'WhatsApp se abrirá con tu solicitud preparada.', 'open' => 'Abrir WhatsApp ahora',
        ],
    ];
    return $strings[$lang][$key] ?? $strings['en'][$key] ?? '';
}

add_action('init', function () {
    register_post_type('travel_lead', [
        'labels' => ['name' => 'Travel Leads', 'singular_name' => 'Travel Lead', 'menu_name' => 'Travel Leads'],
        'public' => false, 'show_ui' => true, 'show_in_menu' => true,
        'menu_icon' => 'dashicons-airplane', 'supports' => ['title'],
        'capability_type' => 'post', 'map_meta_cap' => true,
    ]);
});

add_action('admin_menu', function () {
    add_options_page('Travel Agency Settings', 'Travel Agency', 'manage_options', 'cc-travel-settings', 'cc_travel_settings_page');
});
add_action('admin_init', function () {
    register_setting('cc_travel', 'cc_whatsapp_number', ['sanitize_callback' => function($v){ return preg_replace('/\D+/', '', $v); }]);
});
function cc_travel_settings_page() { ?>
<div class="wrap"><h1>Travel Agency Settings</h1><form method="post" action="options.php"><?php settings_fields('cc_travel'); ?>
<table class="form-table"><tr><th><label for="cc_whatsapp_number">WhatsApp number</label></th><td><input class="regular-text" id="cc_whatsapp_number" name="cc_whatsapp_number" value="<?php echo esc_attr(get_option('cc_whatsapp_number')); ?>" placeholder="Example: 15145551234"><p class="description">Use country code + number, digits only. Until set, visitors can choose a WhatsApp contact after submitting.</p></td></tr></table><?php submit_button(); ?></form></div><?php }

add_shortcode('cc_quote_form', function ($atts) {
    $a = shortcode_atts(['lang' => 'en'], $atts);
    $lang = cc_travel_lang($a['lang']);
    static $form_instance = 0;
    $form_instance++;
    $prefix = 'cc-' . $lang . '-' . $form_instance;
    $canada = ['Toronto (YYZ)','Montréal (YUL)','Vancouver (YVR)','Calgary (YYC)','Ottawa (YOW)','Québec (YQB)','Edmonton (YEG)','Halifax (YHZ)','Winnipeg (YWG)'];
    $colombia = ['Bogotá (BOG)','Medellín (MDE)','Cartagena (CTG)','Cali (CLO)','Barranquilla (BAQ)','Pereira (PEI)','Bucaramanga (BGA)','Santa Marta (SMR)','Armenia (AXM)'];
    ob_start(); ?>
    <form class="travel-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
      <input type="hidden" name="action" value="cc_submit_quote"><input type="hidden" name="lang" value="<?php echo esc_attr($lang); ?>"><?php wp_nonce_field('cc_quote', 'cc_nonce'); ?>
      <div class="field"><label for="<?php echo esc_attr($prefix . '-departure'); ?>"><?php echo esc_html(cc_travel_text($lang, 'departure')); ?></label><select id="<?php echo esc_attr($prefix . '-departure'); ?>" name="departure" required><option value=""><?php echo esc_html(cc_travel_text($lang, 'choose')); ?></option><optgroup label="Canada"><?php foreach($canada as $c) echo '<option>'.esc_html($c).'</option>'; ?></optgroup><optgroup label="<?php echo esc_attr(cc_travel_text($lang, 'colombia')); ?>"><?php foreach($colombia as $c) echo '<option>'.esc_html($c).'</option>'; ?></optgroup></select></div>
      <div class="field"><label for="<?php echo esc_attr($prefix . '-arrival'); ?>"><?php echo esc_html(cc_travel_text($lang, 'arrival')); ?></label><select id="<?php echo esc_attr($prefix . '-arrival'); ?>" name="arrival" required><option value=""><?php echo esc_html(cc_travel_text($lang, 'choose')); ?></option><optgroup label="<?php echo esc_attr(cc_travel_text($lang, 'colombia')); ?>"><?php foreach($colombia as $c) echo '<option>'.esc_html($c).'</option>'; ?></optgroup><optgroup label="Canada"><?php foreach($canada as $c) echo '<option>'.esc_html($c).'</option>'; ?></optgroup></select></div>
      <div class="field"><label for="<?php echo esc_attr($prefix . '-depart-date'); ?>"><?php echo esc_html(cc_travel_text($lang, 'depart_date')); ?></label><input id="<?php echo esc_attr($prefix . '-depart-date'); ?>" type="date" name="depart_date" min="<?php echo esc_attr(wp_date('Y-m-d')); ?>" required></div>
      <div class="field"><label for="<?php echo esc_attr($prefix . '-return-date'); ?>"><?php echo esc_html(cc_travel_text($lang, 'return_date')); ?></label><input id="<?php echo esc_attr($prefix . '-return-date'); ?>" type="date" name="return_date" min="<?php echo esc_attr(wp_date('Y-m-d')); ?>"></div>
      <div class="field"><label for="<?php echo esc_attr($prefix . '-travelers'); ?>"><?php echo esc_html(cc_travel_text($lang, 'travelers')); ?></label><input id="<?php echo esc_attr($prefix . '-travelers'); ?>" type="number" name="travelers" min="1" max="20" value="1" required></div>
      <div class="field"><label for="<?php echo esc_attr($prefix . '-phone'); ?>"><?php echo esc_html(cc_travel_text($lang, 'phone')); ?></label><input id="<?php echo esc_attr($prefix . '-phone'); ?>" type="tel" name="phone" autocomplete="tel" required placeholder="+1 …"></div>
      <div class="field full"><label for="<?php echo esc_attr($prefix . '-name'); ?>"><?php echo esc_html(cc_travel_text($lang, 'name')); ?></label><input id="<?php echo esc_attr($prefix . '-name'); ?>" type="text" name="name" autocomplete="name" required></div>
      <div class="field full"><label for="<?php echo esc_attr($prefix . '-email'); ?>"><?php echo esc_html(cc_travel_text($lang, 'email')); ?></label><input id="<?php echo esc_attr($prefix . '-email'); ?>" type="email" name="email" autocomplete="email" required></div>
      <div class="field full"><label for="<?php echo esc_attr($prefix . '-notes'); ?>"><?php echo esc_html(cc_travel_text($lang, 'notes')); ?></label><textarea id="<?php echo esc_attr($prefix . '-notes'); ?>" name="notes" rows="3" placeholder="<?php echo esc_attr(cc_travel_text($lang, 'notes_placeholder')); ?>"></textarea></div>
      <button class="btn-primary" type="submit"><?php echo esc_html(cc_travel_text($lang, 'submit')); ?></button>
      <p class="form-note"><?php echo esc_html(cc_travel_text($lang, 'note')); ?></p>
    </form><?php return ob_get_clean();
});

add_action('admin_post_nopriv_cc_submit_quote', 'cc_submit_quote');
add_action('admin_post_cc_submit_quote', 'cc_submit_quote');
function cc_submit_quote() {
    if (!isset($_POST['cc_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['cc_nonce'])), 'cc_quote')) wp_die('Security check failed.');
    $fields = [];
    foreach (['lang','departure','arrival','depart_date','return_date','travelers','phone','name','email','notes'] as $key) {
        $value = isset($_POST[$key]) ? wp_unslash($_POST[$key]) : '';
        $fields[$key] = $key === 'email' ? sanitize_email($value) : ($key === 'notes' ? sanitize_textarea_field($value) : sanitize_text_field($value));
    }
    $fields['lang'] = cc_travel_lang($fields['lang']);
    if (!$fields['name'] || !$fields['email'] || !$fields['departure'] || !$fields['arrival']) wp_die(esc_html(cc_travel_text($fields['lang'], 'required')));
    $title = sprintf('%s — %s to %s', $fields['name'], $fields['departure'], $fields['arrival']);
    $lead_id = wp_insert_post(['post_type'=>'travel_lead','post_status'=>'private','post_title'=>$title]);
    if (is_wp_error($lead_id)) wp_die('Unable to save your request. Please try again.');
    foreach ($fields as $key=>$value) update_post_meta($lead_id, '_cc_'.$key, $value);

    if ($fields['lang'] === 'fr') {
        $message = "Bonjour, je souhaite réserver un voyage {$fields['departure']} → {$fields['arrival']}. Départ : {$fields['depart_date']}; retour : {$fields['return_date']}; voyageurs : {$fields['travelers']}. Nom : {$fields['name']}. Référence : #{$lead_id}";
    } elseif ($fields['lang'] === 'es') {
        $message = "Hola, quiero solicitar un viaje {$fields['departure']} → {$fields['arrival']}. Salida: {$fields['depart_date']}; regreso: {$fields['return_date']}; viajeros: {$fields['travelers']}. Nombre: {$fields['name']}. Referencia: #{$lead_id}";
    } else {
        $message = "Hello, I would like to reserve a trip {$fields['departure']} → {$fields['arrival']}. Departure: {$fields['depart_date']}; return: {$fields['return_date']}; travelers: {$fields['travelers']}. Name: {$fields['name']}. Reference: #{$lead_id}";
    }

    $number = get_option('cc_whatsapp_number');
    $wa = 'https://wa.me/' . ($number ? rawurlencode($number) : '') . '?text=' . rawurlencode($message);
    $token = wp_generate_password(32, false, false);
    set_transient('cc_wa_' . $token, $wa, 10 * MINUTE_IN_SECONDS);
    $thank_pages = ['en' => 'thank-you', 'fr' => 'merci', 'es' => 'gracias'];
    $page = get_page_by_path($thank_pages[$fields['lang']]);
    $url = $page ? get_permalink($page) : home_url('/');
    wp_safe_redirect(add_query_arg(['ref'=>$lead_id,'token'=>$token], $url)); exit;
}

add_shortcode('cc_thank_you', function($atts){
    $a = shortcode_atts(['lang'=>'en'], $atts);
    $lang = cc_travel_lang($a['lang']);
    $token = isset($_GET['token']) ? sanitize_key(wp_unslash($_GET['token'])) : '';
    $wa = $token ? get_transient('cc_wa_' . $token) : '';
    if (!$wa || strpos($wa,'https://wa.me/')!==0) return '<div class="notice">'.esc_html(cc_travel_text($lang, 'ready')).'</div>';
    return '<div class="notice"><strong>'.esc_html(cc_travel_text($lang, 'saved')).'</strong><p>'.esc_html(cc_travel_text($lang, 'opening')).'</p><a class="btn-primary" href="'.esc_url($wa).'">'.esc_html(cc_travel_text($lang, 'open')).' →</a></div><script>setTimeout(function(){window.location.href='.wp_json_encode($wa).';},1800);</script>';
});

add_filter('manage_travel_lead_posts_columns', function($c){ return ['cb'=>$c['cb'],'title'=>'Traveler / Route','date'=>'Received']; });
add_action('add_meta_boxes', function(){ add_meta_box('cc_lead_details','Reservation details',function($post){
  $labels=['name'=>'Name','email'=>'Email','phone'=>'Phone / WhatsApp','departure'=>'Departure','arrival'=>'Arrival','depart_date'=>'Departure date','return_date'=>'Return date','travelers'=>'Travelers','notes'=>'Notes','lang'=>'Language'];
  echo '<table class="widefat striped">'; foreach($labels as $k=>$label){ echo '<tr><th style="width:180px">'.esc_html($label).'</th><td>'.nl2br(esc_html(get_post_meta($post->ID,'_cc_'.$k,true))).'</td></tr>'; } echo '</table>';
},'travel_lead','normal','high'); });
