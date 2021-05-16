//thumbnail-scale-on-hover.js

//select all the overlays
//when an overlay is mousedover, add the hover class to the image and scale up.


var thumbs = document.getElementsByClassName('image-boundaries');
console.log(thumbs);
for(var i = 0; i < thumbs.length; i++ ) {
    thumbs[i].addEventListener('mouseenter', function(e) { 
        var image = e.target.firstElementChild.lastElementChild;
        image.className = 'hover-scale';
    });
    thumbs[i].addEventListener('mouseleave', function(e) { 
        image = e.target.firstElementChild.lastElementChild;
        console.log(image);
        image.className = 'gallery-thumb';
    });
}