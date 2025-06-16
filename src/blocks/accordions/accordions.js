let accordions = document.querySelectorAll('.accordion summary');

accordions.forEach((summary) => {
    summary.addEventListener('click', (event) => {
        // Prevent default action (i.e., don't toggle the 'open' attribute)
        event.preventDefault();
        
        // Stop event propagation
        event.stopPropagation();

        // Check if the clicked accordion is already open
        var isOpen = summary.parentElement.hasAttribute('open');

        // Close all accordions
        accordions.forEach((s) => {
            s.parentElement.removeAttribute('open');
        });

        // Open or close the clicked accordion based on its current state
        if (!isOpen) {
            summary.parentElement.setAttribute('open', '');
        }
    });
});
