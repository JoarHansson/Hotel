const calender = document.querySelector("#calender");

const instructionText = document.querySelector("#instruction-text");
const pricePerDay = document.querySelector("#price-per-day");
const totalPrice = document.querySelector("#total-price");

const formMakeReservation = document.querySelector("#form-make-reservation");
const formInputDateFrom = document.querySelector("#date-from");
const formInputDateTo = document.querySelector("#date-to");

const arrivalDate = document.querySelector("#arrival-date");
const departureDate = document.querySelector("#departure-date");

let requestedDateFrom;
let requestedDateTo;

// make a reservation with the buttons(days) in the calender
calender.addEventListener("click", (event) => {
  if (event.target.tagName !== "BUTTON") {
    return;
  }

  // set start date of reservation
  if (
    requestedDateFrom === undefined &&
    event.target.classList.contains("calender-day")
  ) {
    requestedDateFrom = event.target.value;

    // convert dates 1-9 to 01-09:
    if (requestedDateFrom.length === 1) {
      requestedDateFrom = 0 + requestedDateFrom;
    }

    //  visually distinguish chosen start date:

    if (event.target.classList.contains("bg-blue-600")) {
      event.target.classList.remove("bg-blue-600", "text-cyan-50");
      event.target.classList.add(
        "font-extrabold",
        "bg-blue-300",
        "text-blue-950"
      );
    } else if (event.target.classList.contains("bg-purple-600")) {
      event.target.classList.remove("bg-purple-600", "text-cyan-50");
      event.target.classList.add(
        "font-extrabold",
        "bg-purple-300",
        "text-purple-950"
      );
    } else if (event.target.classList.contains("bg-yellow-600")) {
      event.target.classList.remove("bg-yellow-600", "text-cyan-50");
      event.target.classList.add(
        "font-extrabold",
        "bg-yellow-300",
        "text-yellow-950"
      );
    }

    instructionText.textContent = "Choose an end date";

    formInputDateFrom.value = "2024-01-" + requestedDateFrom;
    arrivalDate.textContent = "2024-01-" + requestedDateFrom;

    // update the price to the price of one day:
    totalPrice.textContent = pricePerDay.textContent;

    // console.log(1, requestedDateFrom, event.target.value, formInputDateFrom.value);
    return;
  }

  // set end date of reservation
  if (
    requestedDateTo === undefined &&
    event.target.classList.contains("calender-day") &&
    // end date can't be less than start date:
    Number(event.target.value) >= Number(requestedDateFrom)
  ) {
    requestedDateTo = event.target.value;

    // convert dates 1-9 to 01-09:
    if (requestedDateTo.length === 1) {
      requestedDateTo = 0 + requestedDateTo;
    }

    //  visually distinguish all chosen dates:
    const dateButtons = calender.querySelectorAll("button");
    const dateButtonsArray = Array.from(dateButtons);
    const dateButtonsSelected = dateButtonsArray.slice(
      requestedDateFrom - 1, // array starts at 0, calender starts at 1
      requestedDateTo
    );

    dateButtonsSelected.forEach((node) => {
      if (node.classList.contains("bg-blue-600")) {
        node.classList.remove("bg-blue-600", "text-cyan-50");
        node.classList.add("font-extrabold", "bg-blue-300", "text-blue-950");
      } else if (node.classList.contains("bg-purple-600")) {
        node.classList.remove("bg-purple-600", "text-cyan-50");
        node.classList.add(
          "font-extrabold",
          "bg-purple-300",
          "text-purple-950"
        );
      } else if (node.classList.contains("bg-yellow-600")) {
        node.classList.remove("bg-yellow-600", "text-cyan-50");
        node.classList.add(
          "font-extrabold",
          "bg-yellow-300",
          "text-yellow-950"
        );
      }
    });

    instructionText.textContent = "Confirm reservation or clear selection";

    formInputDateTo.value = "2024-01-" + requestedDateTo;
    departureDate.textContent = "2024-01-" + requestedDateTo;

    totalPrice.textContent =
      pricePerDay.textContent * dateButtonsSelected.length;

    // console.log(2, requestedDateTo, event.target.value, formInputDateTo.value);
    return;
  }

  // submit the form
  if (
    event.target.id === "button-submit-form" &&
    requestedDateFrom != undefined &&
    requestedDateTo != undefined
  ) {
    formMakeReservation.submit();

    return;
  }

  // clear selection
  if (event.target.id === "button-clear-selection") {
    requestedDateFrom = undefined;
    requestedDateTo = undefined;

    const dateButtons = calender.querySelectorAll("button.calender-day");

    dateButtons.forEach((dateButton) => {
      if (dateButton.classList.contains("bg-blue-300")) {
        dateButton.classList.remove(
          "font-extrabold",
          "bg-blue-300",
          "text-blue-950"
        );
        dateButton.classList.add("bg-blue-600", "text-cyan-50");
      } else if (dateButton.classList.contains("bg-purple-300")) {
        dateButton.classList.remove(
          "font-extrabold",
          "bg-purple-300",
          "text-purple-950"
        );
        dateButton.classList.add("bg-purple-600", "text-cyan-50");
      } else if (dateButton.classList.contains("bg-yellow-300")) {
        dateButton.classList.remove(
          "font-extrabold",
          "bg-yellow-300",
          "text-yellow-950"
        );
        dateButton.classList.add("bg-yellow-600", "text-cyan-50");
      }
    });

    // reset instruction text
    instructionText.textContent = "Choose a start date";

    // clear displayed total price and dates
    totalPrice.textContent = 0;
    arrivalDate.textContent = "";
    departureDate.textContent = "";

    return;
  }

  // if user tries to submit the form without both values set
  if (
    event.target.id === "button-submit-form" &&
    requestedDateTo === undefined
  ) {
    // give a visual hint to complete the form
    instructionText.classList.add("scale-125", "transition-transform");
    setTimeout(() => {
      instructionText.classList.remove("scale-125");
    }, 100);

    return;
  }
});
