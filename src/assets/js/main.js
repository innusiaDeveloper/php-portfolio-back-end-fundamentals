("use strict");
/* -------------------------------------------------------------------------------------- */
/* ------------------------------------Burger Menu--------------------------------------- */
/* -------------------------------------------------------------------------------------- */

document.querySelector(".burger").addEventListener("click", function () {
  this.classList.toggle("active");
  document.querySelector(".nav").classList.toggle("open");
});
