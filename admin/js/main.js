document.addEventListener("DOMContentLoaded", function(){

  let menu = document.getElementById("bars");
  let nav = document.querySelector(".nav");
    if(menu){
      menu.addEventListener("click", function(){
        nav.classList.toggle("active");
        menu.classList.toggle('fa-times');
      })
    }

    const navLinks = document.querySelectorAll(".nav a");
    navLinks.forEach(link => {
      link.addEventListener("click", function() {
        nav.classList.remove("active");
      })
    })})
  

document.addEventListener("DOMContentLoaded", function(){
    function sizing() {
      this.style.height = 'auto';
      this.style.height = this.scrollHeight + 'px';
    }

    let input = document.querySelectorAll('.input');

    input.forEach(text=>{
      sizing.call(text);
      text.addEventListener('input', sizing)
    })
 })
