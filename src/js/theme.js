// remove any section element with class '_container' that is empty with vanilla js
document.addEventListener('DOMContentLoaded', function() {
    var containers = document.querySelectorAll('section._container');
    containers.forEach(function(container) {
        if (container.innerHTML.trim() === '') {
        container.remove();
        }
    });
    }
); 