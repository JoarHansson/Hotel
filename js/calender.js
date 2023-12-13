const calender = document.querySelector(".calender");
const instructionText = document.querySelector("#instruction-text");
const formMakeBooking = document.querySelector("#form-make-booking");
const formInputDateFrom = document.querySelector("#date-from");
const formInputDateTo = document.querySelector("#date-to");

let requestedDateFrom;
let requestedDateTo;

// make a booking with the buttons(days) in the calender
calender.addEventListener("click", (event) => {
  if (event.target.tagName !== "BUTTON") {
    return;
  }

  // set start date of booking
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
    event.target.classList.remove("bg-slate-600");
    event.target.classList.add("font-bold", "bg-slate-700");

    instructionText.textContent = "Choose an end date";

    formInputDateFrom.value = "2024-01-" + requestedDateFrom;

    // console.log(1, requestedDateFrom, event.target.value, formInputDateFrom.value);
    return;
  }

  // set end date of booking
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
      node.classList.remove("bg-slate-600");
      node.classList.add("font-bold", "bg-slate-700");
    });

    instructionText.textContent = "Confirm booking or clear selection";

    formInputDateTo.value = "2024-01-" + requestedDateTo;

    // console.log(2, requestedDateTo, event.target.value, formInputDateTo.value);
    return;
  }

  // submit the form
  if (
    event.target.id === "button-submit-form" &&
    requestedDateFrom != undefined &&
    requestedDateTo != undefined
  ) {
    formMakeBooking.submit();

    return;
  }

  // clear selection
  if (event.target.id === "button-clear-selection") {
    requestedDateFrom = undefined;
    requestedDateTo = undefined;
    location.reload();

    return;
  }
});
