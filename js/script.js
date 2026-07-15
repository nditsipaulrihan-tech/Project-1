function goToPage(selectElement){
  // let select = document.getElementById('selectBook');
  let page = selectElement.value;
  if(page){
    window.location.href=page;
  }
}