// jQuery(document).ready(function($) {
//     // Handle English form submission
//     $('#vaccine-form').on('submit', function(e) {
//         e.preventDefault();
//         var firstVaccineDate = $('#first_vaccine_date').val();
//         handleFormSubmission(firstVaccineDate, 'english');
//     });

//     // Handle Arabic form submission
//     $('#vaccine-form-ar').on('submit', function(e) {
//         e.preventDefault();
//         var firstVaccineDateAr = $('#first_vaccine_date_ar').val();
//         handleFormSubmission(firstVaccineDateAr, 'arabic');
//     });

//     function handleFormSubmission(vaccineDate) {
//         $.ajax({
//             url: ajax_object.ajax_url,
//             type: 'POST',
//             data: {
//                 action: 'generate_vaccine_ics',
//                 first_vaccine_date: vaccineDate
//             },
//             success: function(response) {
//                 if (response.success) {
//                     var icsContent = response.data.ics_content;
//                     var uid = response.data.uid;

//                     var blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
//                     var link = document.createElement('a');
//                     link.href = window.URL.createObjectURL(blob);
//                     link.download = "2nd-Dose-Schedule_" + uid + '.ics';
//                     document.body.appendChild(link);
//                     link.click();
//                     document.body.removeChild(link);
//                 } else {
//                     alert('Failed to generate the ICS file.');
//                 }
//             },
//             error: function(response) {
//                 alert('An error occurred while generating the ICS file.');
//             }
//         });
//     }
// });


jQuery(document).ready(function($) {
    // Handle English form submission
    $('#vaccine-form').on('submit', function(e) {
        e.preventDefault();
        var firstVaccineDate = $('#first_vaccine_date').val();
        var formLanguage = $('#vaccine-form input[name="form_language"]').val(); // Capture the language from the form
        handleFormSubmission(firstVaccineDate, formLanguage);
    });

    // Handle Arabic form submission
    $('#vaccine-form-ar').on('submit', function(e) {
        e.preventDefault();
        var firstVaccineDateAr = $('#first_vaccine_date_ar').val();
        var formLanguageAr = $('#vaccine-form-ar input[name="form_language"]').val(); // Capture the language from the form
        handleFormSubmission(firstVaccineDateAr, formLanguageAr);
    });

    function handleFormSubmission(vaccineDate, formLanguage) {
        // Check if a valid date is selected
        if (!vaccineDate) {
            alert('Please select a valid vaccine date.');
            return;
        }

        // Log data for debugging
        console.log('Form Language: ' + formLanguage);
        console.log('Vaccine Date: ' + vaccineDate);
        console.log('AJAX URL: ' + ajax_object.ajax_url);

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'generate_vaccine_ics',
                first_vaccine_date: vaccineDate,
                form_language: formLanguage // Add form_language to data
            },
            success: function(response) {
                console.log(response); // Log the response for debugging
                if (response.success) {
                    var icsContent = response.data.ics_content;
                    var uid = response.data.uid;

                    // Create a Blob object to download the ICS file
                    var blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = "vaccine_" + uid + '.ics'; // Ensure filename format
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    alert('Failed to generate the ICS file.');
                }
            },
            error: function(response) {
                console.log(response); // Log the error response for debugging
                alert('An error occurred while generating the ICS file.');
            }
        });
    }
});
