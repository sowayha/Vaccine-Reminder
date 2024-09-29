<?php
/*
Plugin Name: Vaccine Reminder
Description: This plugin allows users to input their vaccine date, automatically generates an ICS file with 6 events to reminder (Android & IOS).
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
    

      // Set language-specific details for second event
      if ($form_language === 'arabic') {
        $summary = 'موعد الجرعة الثانية';
        $description2 = '  تذكير: تبقى شهران حتى موعد التطعيم القادم  ';
    } else {
        $summary = 'Next Vaccine Appointment';
        $description2 = 'Reminder: Your next vaccination is scheduled in 2 months.';
    }

      // Set language-specific details for 3th event
      if ($form_language === 'arabic') {
        $summary = 'موعد الجرعة الثانية';
        $description3 = '  تذكير: تبقى شهر حتى موعد التطعيم القادم  ';
    } else {
        $summary = 'Next Vaccine Appointment';
        $description3 = 'Reminder: Your next vaccination is scheduled in 1 months.';
    }

      // Set language-specific details for 4th event
      if ($form_language === 'arabic') {
        $summary = 'موعد الجرعة الثانية';
        $description4 = '  تذكير: تبقى 14 يوم حتى موعد التطعيم القادم  ';
    } else {
        $summary = 'Next Vaccine Appointment';
        $description4 = 'Reminder: Your next vaccination is scheduled in 14 Days.';
    }

      // Set language-specific details for 5th event
      if ($form_language === 'arabic') {
        $summary = 'موعد الجرعة الثانية';
        $description5 = '  تذكير: تبقى 3 ايام حتى موعد التطعيم القادم  ';
    } else {
        $summary = 'Next Vaccine Appointment';
        $description5 = 'Reminder: Your next vaccination is scheduled in 3 Days.';
    }

      // Set language-specific details for 6th event
      if ($form_language === 'arabic') {
        $summary = 'موعد الجرعة الثانية';
        $description6 = '  تذكير: تبقى يوم حتى موعد التطعيم القادم  ';
    } else {
        $summary = 'Next Vaccine Appointment';
        $description6 = 'Reminder: Your next vaccination is scheduled in 1 Day.';
    }

        // Set language-specific details for first event
        if ($form_language === 'arabic') {
            $summary = 'موعد الجرعة الثانية';
            $description = '  تذكير: تبقى ساعة حتى موعد التطعيم القادم  ';
        } else {
            $summary = 'Next Vaccine Appointment';
            $description = 'Reminder: Your next vaccination is scheduled in 1 Hour.';
        }

    
    
    // Define event details
    $events = array(
        array(
            'summary' => $summary,
            'description' => $description6,
            'location' => 'Vacsera',
            'start' => strtotime('+89 days', strtotime($first_date . ' 09:00:00')),
            'end' => strtotime('+89 days', strtotime($first_date . ' 10:00:00')),
        ),
        array(
            'summary' => $summary,
            'description' => $description2,
            'location' => 'Vacsera',
            'start' => strtotime('+30 days', strtotime($first_date . ' 09:00:00')),
            'end' => strtotime('+30 days', strtotime($first_date . ' 10:00:00')),
        ),
        array(
            'summary' => $summary,
            'description' => $description3,
            'location' => 'Vacsera',
            'start' => strtotime('+60 days', strtotime($first_date . ' 09:00:00')),
            'end' => strtotime('+60 days', strtotime($first_date . ' 10:00:00')),
        ),
        array(
            'summary' => $summary,
            'description' => $description4,
            'location' => 'Vacsera',
            'start' => strtotime('+76 days', strtotime($first_date . ' 09:00:00')),
            'end' => strtotime('+76 days', strtotime($first_date . ' 10:00:00')),
        ),
        array(
            'summary' => $summary,
            'description' => $description5,
            'location' => 'Vacsera',
            'start' => strtotime('+87 days', strtotime($first_date . ' 09:00:00')),
            'end' => strtotime('+87 days', strtotime($first_date . ' 10:00:00')),
        ),
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
        $uid = uniqid();
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
