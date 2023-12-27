const formCancelBooking = document.querySelector("#form-cancel-booking");
const countDown = document.querySelector("#count-down");

minutes = 4;
seconds = 59;

// countdown 5 minutes
let interval = setInterval(() => {
  if (seconds === -1) {
    minutes--;
    seconds = 59;
  }

  if (String(seconds).length === 1) {
    countDown.textContent = String(minutes) + ":0" + String(seconds);
  } else {
    countDown.textContent = String(minutes) + ":" + String(seconds);
  }

  seconds--;
}, 1000);

// cancel the booking if the user became inactive (5 mins without action)
setTimeout(() => {
  formCancelBooking.submit();
}, 300000); // 5 minutes
