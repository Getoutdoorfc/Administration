// Fil: assets/js/admin.js

jQuery(document).ready(function($) {
    var participantIndex = 0;

    $('#add_participant').on('click', function() {
        var participantHtml = '<div class="participant" data-index="' + participantIndex + '">' +
            '<h4><?php _e( 'Deltager', 'administration' ); ?> ' + (participantIndex + 1) + '</h4>' +
            '<label><?php _e( 'Navn:', 'administration' ); ?></label>' +
            '<input type="text" name="participants[' + participantIndex + '][name]" />' +
            // Tilvalg felter
            '</div>';

        $('#participants_wrapper').append(participantHtml);
        participantIndex++;
    });

    // Real-time validation for date and time input
    $('#date_time').on('input', function() {
        var dateTime = $(this).val();
        var regex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/;
        if (!regex.test(dateTime)) {
            // Show validation error
            $(this).addClass('error');
            // ...add error message...
        } else {
            $(this).removeClass('error');
            // ...remove error message...
        }
    });

    // AJAX call optimization
    $.ajax({
        url: ajaxurl, // Properly localized
        method: 'POST',
        data: {
            action: 'your_ajax_action',
            // ...data...
        },
        success: function(response) {
            // ...handle response...
        }
    });

    // Validering ved form submission
    $('form.cart').on('submit', function(e) {
        var isValid = true;

        // Validering af dato
        if ( $('#experience_date_picker').val() === '' ) {
            isValid = false;
            alert('<?php _e( 'Vælg venligst en dato.', 'administration' ); ?>');
        }

        // Validering af deltagere
        if ( $('.participant').length === 0 ) {
            isValid = false;
            alert('<?php _e( 'Tilføj venligst mindst én deltager.', 'administration' ); ?>');
        }

        if ( ! isValid ) {
            e.preventDefault();
        }
    });
});