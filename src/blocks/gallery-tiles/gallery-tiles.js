var modals = document.querySelectorAll('.gallery-modal');
modals.forEach(function (modal) {
    var tiles = modal.previousElementSibling.querySelector('._tiles');
    var modalImg = modal.querySelector('img');
    var modalClose = modal.querySelector('._close');

    if (!tiles || !modalImg || !modalClose) {
        return;
    }

    tiles.addEventListener('click', function (event) {
        var target = event.target.closest('._tile');
        if (target) {
            // Add 'active' class to the modal
            modal.classList.add('-active');

            // Set the img attributes in the modal
            modalImg.src = target.getAttribute('data-img-url');
            modalImg.alt = target.getAttribute('data-img-alt') || '';
        }
    });

    modalClose.addEventListener('click', function () {
        // Remove 'active' class from the modal
        modal.classList.remove('-active');

        // Clear the img attributes in the modal
        modalImg.src = '';
        modalImg.alt = '';
    });
    
});