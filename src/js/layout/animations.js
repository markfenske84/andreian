// Animation Triggers
let animatedElements = document.querySelectorAll('._animation');
window.addEventListener('scroll', function() {
    for (let animatedElement of animatedElements) {
        if (animatedElement.getBoundingClientRect().top < window.innerHeight) {
            animatedElement.classList.add('-block-animated');
        }
    } 
}); // End Animation Triggers 