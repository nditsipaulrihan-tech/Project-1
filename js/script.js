function goToPage(selectElement){
  // let select = document.getElementById('selectBook');
  let page = selectElement.value;
  if(page){
    window.location.href=page;
  }
}

const a = document.querySelectorAll(".click");

  a.forEach(btn=>{
    btn.addEventListener("click", function(){
      let page = this.value;
      if(page){
    window.location.href=page;
    }
  })})

  const b = document.querySelectorAll(".buy");

  b.forEach(btn=>{
    btn.addEventListener("click", function(){
      let link = this.getAttribute("data-link");
      if(link){
    window.open(link, "_blank");
    }
  })})

  document.addEventListener("DOMContentLoaded", function(){
    document.querySelectorAll(".cont-btn").forEach(btn=>{
      btn.addEventListener("click", function(){
        let subject = this.dataset.subject;
        window.location.href = "contact.html?subject=" + subject;
      })})})

  const subjectSelect = document.getElementById("subject");

    if(subjectSelect){
      const urlParams = new URLSearchParams(window.location.search);
      const subjectValue = urlParams.get("subject");

      if(subjectValue){
        subjectSelect.value = subjectValue;
      }
    }


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