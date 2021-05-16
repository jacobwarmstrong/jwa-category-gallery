//Image Lightbox hover

//variables and functions
var $categoryBtn = $('.btn');
var $tagBadges = $('.badge');
var $window = $(window);
var timer;
var imageInfo = document.getElementById('image-info');
var closeButton = document.getElementById('close-button');
var leftButton = document.getElementById('left-arrow');
var rightButton = document.getElementById('right-arrow');
var areas = [imageInfo, closeButton];
var onInfo = false;

function switchDisplay(state) {
    var opacity;
    switch(state) {
        case 'on':
            opacity = 1.0;
            break;
        case 'off':
            opacity = 0;
            break;
        default:
            opacity = 0;
            console.log('your switch failed');
    }
    for(i=0;i<areas.length;i++) {
        areas[i].style.opacity = opacity;
    }
}

function changeState() {
    if (onInfo == false) {
        switchDisplay('off');
    } else {
        switchDisplay('on');
    }
}

function addDesktopMouseEvents() {
    document.addEventListener('mousemove', function() {
        switchDisplay('on');
        clearTimeout(timer);
        timer = setTimeout(changeState, 1200);
    });

    for(i=0;i<areas.length;i++) {
        areas[i].addEventListener('mouseover', function() {
            onInfo = true;
            switchDisplay('on');
        });
        areas[i].addEventListener('mouseout', function() {
            onInfo = false;
            switchDisplay('off');
        });
    }
}

function removeDesktopMouseEvents() {
    console.log('reached!!!');
    document.removeEventListener('mousemove', function() {
        switchDisplay('on');
        clearTimeout(timer);
        timer = setTimeout(changeState, 1200);
    }, false);

    for(i=0;i<areas.length;i++) {
        areas[i].removeEventListener('mouseover', function() {
            onInfo = true;
            switchDisplay('on');
        }, false);
        areas[i].removeEventListener('mouseout', function() {
            onInfo = false;
            switchDisplay('off');
        }, false);
    }
}

//logic

if(leftButton != null) {
    areas.push(leftButton);
}
if(rightButton != null) {
    areas.push(rightButton);
}

console.log( $window.width() );
console.log($window.width() <= 425);

switchDisplay('on');

//on load 
if ($window.width() >= 600) {
    
    switchDisplay('off');
    
    addDesktopMouseEvents();
    
    $categoryBtn.removeClass('btn-secondary');
    $categoryBtn.addClass('btn-outline-light');
    $tagBadges.each(function() {
        $(this).addClass('badge-pill-outline-light');
        $(this).removeClass('badge-primary');
    });
    
}

//on resize check again
$window.resize(function() {
    if ($window.width() >= 600) {
        console.log($window.width())
        switchDisplay('off');
        addDesktopMouseEvents();
        
        $categoryBtn.removeClass('btn-secondary');
        $categoryBtn.addClass('btn-outline-light');
        $tagBadges.each(function() {
            $(this).addClass('badge-pill-outline-light');
            $(this).removeClass('badge-primary');
        });
    } else {
        removeDesktopMouseEvents();
        switchDisplay('on');
        $categoryBtn.removeClass('btn-outline-light');
        $categoryBtn.addClass('btn-secondary');
        $tagBadges.each(function() {
            $(this).addClass('badge-primary');
            $(this).removeClass('badge-pill-outline-light');
        });
    }
});
