// Get all elements with class .video-modal-trigger
var triggers = document.querySelectorAll('.video-modal-trigger');

// Add click event to each .video-modal-trigger element
triggers.forEach(function (trigger) {
    trigger.addEventListener('click', function () {
        // Find the corresponding .video-modal element
        var modal = this.nextElementSibling;

        // Find the video or iframe element inside the modal
        var videoOrIframe = modal.querySelector('video, iframe');

        // Get the data-vid-url attribute value
        var vidUrl = this.getAttribute('data-vid-url');

        // Set the src attribute of video or iframe with the data-vid-url
        videoOrIframe.src = vidUrl;

        // Toggle the class -active on the .video-modal element
        modal.classList.toggle('-active');
    });
});

// Add click event to close button within .video-modal
var closeButtons = document.querySelectorAll('.video-modal ._close');

closeButtons.forEach(function (button) {
    button.addEventListener('click', function () {
        // Hide the parent .video-modal element
        var modal = this.closest('.video-modal');

        // Find the video or iframe element inside the modal
        var videoOrIframe = modal.querySelector('video, iframe');

        // Remove the src attribute of video or iframe
        videoOrIframe.src = '';

        modal.classList.remove('-active');
    });
});
