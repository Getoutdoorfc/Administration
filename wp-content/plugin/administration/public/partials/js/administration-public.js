// administration Public Scripts

jQuery(document).ready(function ($) {
  // Eksempel på en funktion til at håndtere klik på en oplevelse
  $(".administration-experience").on("click", function () {
    alert("Experience clicked: " + $(this).find("h2").text());
  });
});
