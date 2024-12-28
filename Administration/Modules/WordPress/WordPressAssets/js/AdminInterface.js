// AdminInterface.js
document.addEventListener("DOMContentLoaded", () => {
  const inputFields = document.querySelectorAll(".microsoft-setup-input");

  inputFields.forEach((input) => {
    const errorElement = input.parentElement.querySelector(".validation-error");
    const fieldName = input.getAttribute("name");

    input.addEventListener("input", () => {
      const value = input.value.trim();

      if (value === "") {
        input.classList.add("invalid");
        errorElement.textContent = "Dette felt må ikke være tomt.";
      } else {
        let isValid = true;
        if (
          fieldName === "administration_microsoft_client_id" ||
          fieldName === "administration_microsoft_tenant_id"
        ) {
          const regex = /^[a-zA-Z0-9-]+$/;
          if (!regex.test(value)) {
            isValid = false;
            errorElement.textContent =
              "Ugyldigt format. Kun bogstaver, tal og bindestreger er tilladt.";
          }
        }
        if (fieldName === "administration_microsoft_client_secret") {
          // You can add specific validation for client secret if needed
        }

        if (isValid) {
          input.classList.remove("invalid");
          errorElement.textContent = "";
        } else {
          input.classList.add("invalid");
        }
      }
    });

    input.addEventListener("focus", () => {
      const hint = input.getAttribute("data-hint");
      if (hint) {
        errorElement.textContent = hint;
      }
    });

    input.addEventListener("blur", () => {
      errorElement.textContent = "";
    });
  });
});
