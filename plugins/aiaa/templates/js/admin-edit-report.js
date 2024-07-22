

jQuery(document).ready(function($) {
  const modal = document.getElementById('imageView'); 
  const modalImg = document.getElementById('modal-content'); 

  jQuery('.toZoom').on('click', function(e) {
    var src = $(this).attr('src');
    modal.style.display = "block";
    modalImg.src = src;
  });

  jQuery('.close').on('click', function(e) {
    modal.style.display = "none";
    modalImg.src = "";
  });
});
