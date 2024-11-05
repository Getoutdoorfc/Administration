// JavaScript til admin-sider

jQuery(document).ready(function ($) {
  // Eksempel på en funktion til at håndtere formularindsendelse
  $("#administration-form").on("submit", function (e) {
    e.preventDefault();

    var data = {
      action: "save_administration_settings",
      nonce: $("#administration_nonce").val(),
      settings: $(this).serialize(),
    };

    $.post(ajaxurl, data, function (response) {
      if (response.success) {
        alert("Settings saved successfully.");
      } else {
        alert("Failed to save settings.");
      }
    });
  });
});
