document.querySelectorAll('.tabbed-panes').forEach((tabbedPanes) => {
    var tabs = tabbedPanes.querySelectorAll('._tab');
    var panes = tabbedPanes.querySelectorAll('._pane');

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            var slug = tab.getAttribute('data-slug');

            // Remove '-active' class from all tabs
            tabs.forEach((t) => {
                t.classList.remove('-active');
            });

            // Remove '-active' class from all panes and add to the selected one
            panes.forEach((pane) => {
                pane.classList.remove('-active');
                if (pane.getAttribute('data-slug') === slug) {
                    pane.classList.add('-active');
                }
            });

            // Add '-active' class to the clicked tab
            tab.classList.add('-active');
        });
    });
});
