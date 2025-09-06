// Mobile Effects for 8Qbet
// Simplified version focusing on essential mobile functionality

// Prevent touch callouts and zoom
document.addEventListener('touchstart', function() {}, false);

// Auto height for full screen elements
function autoheight(obj) {
    $(obj).height($(window).height());
}

// Initialize mobile effects
$(document).ready(function() {
    // Auto height for full screen elements
    autoheight(".ohei");
    
    // Prevent image drag
    function imgdragstart() { return false; }
    for(var i in document.images) {
        document.images[i].ondragstart = imgdragstart;
    }
    
    // Mobile detection
    var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (isMobile) {
        // Add mobile-specific classes
        $('body').addClass('mobile-device');
    }
});

// Smooth scroll to top
function scrollToTop() {
    var x = document.body.scrollTop || document.documentElement.scrollTop;
    var timer = setInterval(function() {
        x = x - 15;
        if (x < 100) {
            x = 0;
            window.scrollTo(x, x);
            clearInterval(timer);
        }
        window.scrollTo(x, x);
    }, 1);
}

// Utility function for mobile touch events
function addTouchEvents() {
    var startY = 0;
    var startX = 0;
    
    document.addEventListener('touchstart', function(e) {
        startY = e.touches[0].clientY;
        startX = e.touches[0].clientX;
    }, false);
    
    document.addEventListener('touchmove', function(e) {
        if (!e.touches[0]) return;
        
        var currentY = e.touches[0].clientY;
        var currentX = e.touches[0].clientX;
        
        // Prevent horizontal scroll on vertical swipe
        if (Math.abs(currentX - startX) > Math.abs(currentY - startY)) {
            e.preventDefault();
        }
    }, false);
}

// Initialize touch events
addTouchEvents();
