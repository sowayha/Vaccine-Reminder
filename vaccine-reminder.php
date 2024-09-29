<?php
/*
Plugin Name: Vaccine Reminder
Description: This plugin allows users to input their vaccine date, automatically generates an ICS file for the next scheduled vaccine, and seamlessly integrates it into the user's calendar. The plugin also includes a built-in reminder system, ensuring users are notified in advance of their upcoming vaccine appointment. Compatible with both Android and iOS platforms.
Version: 1.0
Author: Soha Mahmoud
*/

// Display Vaccine Form (English)
function vr_display_vaccine_form() {
    ob_start(); ?>
    <form id="vaccine-form" method="post">
        <label for="first_vaccine_date" class="english_label">First Vaccine Date:</label>
        <input type="hidden" name="form_language" value="english">
        <input type="date" id="first_vaccine_date" name="first_vaccine_date" required>
        <input type="submit" value="Generate ICS">
    </form>
    <p>After downloading the file, please open it to add the event to your calendar.</p>
    <?php
    return ob_get_clean();
}
add_shortcode('vaccine_form', 'vr_display_vaccine_form');

// Display Vaccine Form (Arabic)
function vr_display_vaccine_form_ar() {
    ob_start(); ?>
    <form id="vaccine-form-ar" method="post">
        <label for="first_vaccine_date_ar" class="arabic_label">:     موعد الجرعة الاولي            </label>
        <input type="hidden" id="form_language" name="form_language" value="arabic">
        <input type="date" id="first_vaccine_date_ar" name="first_vaccine_date_ar" required>
        <input type="submit" value="ICS  إنشاء ملف ">
    </form>
    <p>.               بعد تنزيل الملف، يرجى فتحه لإضافة الحدث إلى تقويمك                 </p>
    <?php
    return ob_get_clean();
}
add_shortcode('vaccine_form_ar', 'vr_display_vaccine_form_ar');

// Enqueue necessary scripts
function vr_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('vr-custom-script', plugins_url('/js/custom-script.js', __FILE__), array('jquery'), null, true);
    wp_localize_script('vr-custom-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'vr_enqueue_scripts');

// Generate ICS File Function
function vr_generate_vaccine_ics_file($first_date, $form_language) {
    $first_vaccine_date = strtotime($first_date);
    $uid = uniqid(); // Generate a unique ID for the filename and UID for the event
    
    // Set language-specific details
    if ($form_language === 'arabic') {
        $summary = 'موعد الجرعة الثانية';
        $description = 'هذا هو التاريخ المحدد للتطعيم القادم.';
    } else {
        $summary = 'Next Vaccine Appointment';
        $description = 'This is the scheduled date for the next vaccination.';
    }
    
    // Define event details
    $events = array(
        array(
            'summary' => $summary,
            'description' => $description,
            'location' => 'Vacsera',
            'start' => strtotime('+90 days', strtotime($first_date . ' 09:00:00')),
            'end' => strtotime('+90 days', strtotime($first_date . ' 17:00:00')),
        )
    );

    // ICS content generation with UTF-8 encoding
    $ics_content = "BEGIN:VCALENDAR
VERSION:2.0
CALSCALE:GREGORIAN
PRODID:-//Eureka-digital//Vaccine Reminder Plugin
BEGIN:VTIMEZONE
TZID:America/New_York
BEGIN:STANDARD
DTSTART:20240101T020000
TZOFFSETFROM:-0400
TZOFFSETTO:-0500
TZNAME:EST
END:STANDARD
END:VTIMEZONE";

    foreach ($events as $event) {
        $start_time = date('Ymd\THis', $event['start']);
        $end_time = date('Ymd\THis', $event['end']);

        // Escape special characters in the summary and description
        $ics_content .= "
BEGIN:VEVENT
UID:$uid
DTSTAMP:" . gmdate('Ymd\THis\Z') . "
DTSTART:$start_time
DTEND:$end_time
SUMMARY:" . vr_escape_ics_text($event['summary']) . "
DESCRIPTION:" . vr_escape_ics_text($event['description']) . "
LOCATION:" . vr_escape_ics_text($event['location']) . "

BEGIN:VALARM
TRIGGER:-P2M
ACTION:DISPLAY
DESCRIPTION:Reminder 2 months before
END:VALARM

BEGIN:VALARM
TRIGGER:-P1M
ACTION:DISPLAY
DESCRIPTION:Reminder 1 month before
END:VALARM

BEGIN:VALARM
TRIGGER:-P14D
ACTION:DISPLAY
DESCRIPTION:Reminder 14 days before
END:VALARM

BEGIN:VALARM
TRIGGER:-P3D
ACTION:DISPLAY
DESCRIPTION:Reminder 3 days before
END:VALARM

BEGIN:VALARM
TRIGGER:-P1D
ACTION:DISPLAY
DESCRIPTION:Reminder 1 day before
END:VALARM

BEGIN:VALARM
TRIGGER:-PT60M
ACTION:DISPLAY
DESCRIPTION:Reminder 1 hour before
END:VALARM

END:VEVENT";
    }

    $ics_content .= "
END:VCALENDAR";

    // Return the ICS content
    return $ics_content;
}

// Helper function to escape ICS special characters
function vr_escape_ics_text($text) {
    $escaped_text = str_replace('\\', '\\\\', $text);
    $escaped_text = str_replace("\n", '\\n', $escaped_text);
    $escaped_text = str_replace(',', '\,', $escaped_text);
    $escaped_text = str_replace(';', '\;', $escaped_text);
    return $escaped_text;
}

// Handle ICS Generation Request
function vr_handle_generate_vaccine_ics() {
    if (isset($_POST['first_vaccine_date']) && isset($_POST['form_language'])) {
        $date = sanitize_text_field($_POST['first_vaccine_date']);
        $form_language = sanitize_text_field($_POST['form_language']);
        
        // Generate the ICS content
        $ics_content = vr_generate_vaccine_ics_file($date, $form_language);
        $uid = uniqid(); // Generate a unique ID for the filename
        
        // Ensure a valid JSON response
        wp_send_json_success(array(
            'ics_content' => $ics_content,
            'uid' => $uid,
        ));
    } else {
        // Handle error case
        wp_send_json_error(array(
            'message' => 'Date or form language not set'
        ));
    }
}

add_action('wp_ajax_generate_vaccine_ics', 'vr_handle_generate_vaccine_ics');
add_action('wp_ajax_nopriv_generate_vaccine_ics', 'vr_handle_generate_vaccine_ics');

?>
