// Enhanced gallery modal interactions
(function () {
    var modals = document.querySelectorAll('.gallery-modal');

    modals.forEach(function (modal) {
        // Elements
        var gallerySection = modal.previousElementSibling;
        if (!gallerySection) return;

        var tilesWrapper = gallerySection.querySelector('._tiles');
        if (!tilesWrapper) return;

        var tiles = Array.prototype.slice.call(tilesWrapper.querySelectorAll('._tile'));
        if (!tiles.length) return;

        var modalImg = modal.querySelector('img');
        var modalClose = modal.querySelector('._close');
        var captionEl = modal.querySelector('._caption');
        var prevBtn = modal.querySelector('._prev');
        var nextBtn = modal.querySelector('._next');

        var currentIndex = 0;

        // Utility to update modal content
        function showImage(index) {
            currentIndex = (index + tiles.length) % tiles.length; // looping
            var tile = tiles[currentIndex];

            modalImg.src = tile.getAttribute('data-img-url');
            modalImg.alt = tile.getAttribute('data-img-alt') || '';

            // Caption / description
            var caption = tile.getAttribute('data-img-caption') || tile.getAttribute('data-img-description') || '';
            if (caption) {
                captionEl.textContent = caption;
                captionEl.style.display = '';
            } else {
                captionEl.textContent = '';
                captionEl.style.display = 'none';
            }
        }

        // Open modal on tile click
        tilesWrapper.addEventListener('click', function (e) {
            var tile = e.target.closest('._tile');
            if (!tile) return;
            currentIndex = tiles.indexOf(tile);
            modal.classList.add('-active');
            showImage(currentIndex);
        });

        // Navigation buttons
        function showPrev() { showImage(currentIndex - 1); }
        function showNext() { showImage(currentIndex + 1); }

        if (prevBtn) prevBtn.addEventListener('click', showPrev);
        if (nextBtn) nextBtn.addEventListener('click', showNext);

        // Close modal
        function closeModal() {
            modal.classList.remove('-active');
            modalImg.src = '';
            modalImg.alt = '';
            captionEl.textContent = '';
        }

        if (modalClose) modalClose.addEventListener('click', closeModal);

        // Close when clicking on backdrop or any area that isn't the image or arrows
        modal.addEventListener('click', function (e) {
            var isImage = e.target.closest('img');
            var isArrow = e.target.closest('._prev') || e.target.closest('._next');
            if (!isImage && !isArrow) {
                closeModal();
            }
        });

        // Keyboard navigation when modal open
        document.addEventListener('keydown', function (e) {
            if (!modal.classList.contains('-active')) return;
            switch (e.key) {
                case 'ArrowLeft':
                    showPrev();
                    break;
                case 'ArrowRight':
                    showNext();
                    break;
                case 'Escape':
                    closeModal();
                    break;
            }
        });

        // Swipe / drag navigation
        var startX = 0;
        var isTouch = false;

        function onStart(e) {
            isTouch = e.type === 'touchstart';
            startX = isTouch ? e.touches[0].clientX : e.clientX;
        }

        function onEnd(e) {
            var endX = isTouch ? e.changedTouches[0].clientX : e.clientX;
            var diffX = endX - startX;
            if (Math.abs(diffX) > 50) {
                if (diffX > 0) {
                    showPrev();
                } else {
                    showNext();
                }
            }
        }

        // Attach listeners to the modal image (so dragging caption or arrows doesn't interfere)
        modalImg.addEventListener('mousedown', onStart);
        modalImg.addEventListener('touchstart', onStart);
        modalImg.addEventListener('mouseup', onEnd);
        modalImg.addEventListener('touchend', onEnd);
    });
})();