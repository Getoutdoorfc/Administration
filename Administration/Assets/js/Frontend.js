// Fil: assets/js/frontend.js

jQuery(document).ready(function ($) {
  if ($("body.single-product").length) {
    var productType = $('input[name="product-type"]').val();

    if (productType === "rentals") {
      var availability = rental_product_params.availability;

      flatpickr("#rental_start_date", {
        dateFormat: "Y-m-d",
        minDate: "today",
        enable: availability,
        onChange: function (selectedDates, dateStr, instance) {
          // Update end date picker based on start date
          var minEndDate = selectedDates[0];
          endDatePicker.set("minDate", minEndDate);
        },
      });

      var endDatePicker = flatpickr("#rental_end_date", {
        dateFormat: "Y-m-d",
        minDate: "today",
        enable: availability,
      });
    }

    // Generisk AJAX-fejlhåndtering
    $.ajaxSetup({
      error: function (jqXHR, textStatus, errorThrown) {
        alert(administration_params.generic_error_message);
      },
    });
  }
});
