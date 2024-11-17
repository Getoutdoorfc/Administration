// AdminInterface.js
document.addEventListener("DOMContentLoaded", () => {
  const inputFields = document.querySelectorAll(".microsoft-setup-input");

  inputFields.forEach((input) => {
    input.addEventListener("input", () => {
      if (input.value.trim() === "") {
        input.classList.add("invalid");
        input.nextElementSibling.textContent = "Dette felt må ikke være tomt.";
      } else {
        input.classList.remove("invalid");
        input.nextElementSibling.textContent = "";
      }
    });

    input.addEventListener("focus", () => {
      const hint = input.getAttribute("data-hint");
      if (hint) {
        input.nextElementSibling.textContent = hint;
      }
    });

    input.addEventListener("blur", () => {
      input.nextElementSibling.textContent = "";
    });
  });
});
