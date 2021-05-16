//spinner is in html already visible
//when image is finished loading, hide the spinner
//gracefully transition/fade image into view



var image = document.getElementById('image');
var spinner = document.getElementById('spinner');
var containerImage = document.getElementById('container-image');

containerImage.style.display = 'none';

function loaded() {
    spinner.style.display = 'none';
    containerImage.style.display = 'block';
}

if (image.complete) {
  loaded()
} else {
  image.addEventListener('load', loaded)
  image.addEventListener('error', function() {
      
  })
}