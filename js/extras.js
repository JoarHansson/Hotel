const totalPrice = document.querySelector("#total-price");
const extraItems = document.querySelectorAll(".extra-items");

const formExtras = document.querySelector("#form-extras");
const buttonSubmitExtras = document.querySelector("#button-submit-extras");

// update total price shown, based on which checkboxes are checked
extraItems.forEach((item) => {
  item.addEventListener("click", (event) => {
    let cost;
    event.target.classList.forEach((className) => {
      if (className.includes("cost")) {
        cost = className.substr(-1, 1); // last character in string is the price
      }
    });
    if (event.target.checked === true) {
      totalPrice.textContent = Number(totalPrice.textContent) + Number(cost);
    } else if (event.target.checked === false) {
      totalPrice.textContent = Number(totalPrice.textContent) - Number(cost);
    }
  });
});

buttonSubmitExtras.addEventListener("click", () => {
  formExtras.submit();
});
