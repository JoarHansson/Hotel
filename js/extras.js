const totalPrice = document.querySelector("#total-price");
const extraItems = document.querySelectorAll(".extra-items");

// update total price shown, based on which checkboxes are checked
extraItems.forEach((item) => {
  item.addEventListener("click", (event) => {
    if (event.target.checked === true) {
      totalPrice.textContent =
        Number(totalPrice.textContent) +
        Number(event.target.value.substr(-1, 1)); // last character in string is always the price
    } else if (event.target.checked === false) {
      totalPrice.textContent =
        Number(totalPrice.textContent) -
        Number(event.target.value.substr(-1, 1));
    }
  });
});
