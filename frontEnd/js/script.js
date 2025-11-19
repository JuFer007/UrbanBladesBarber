window.addEventListener("scroll", function () {
  const navbar = document.querySelector(".navbar");
  if (window.scrollY > 50) {
    navbar.classList.remove("transparent");
    navbar.classList.add("scrolled");
  } else {
    navbar.classList.add("transparent");
    navbar.classList.remove("scrolled");
  }
});