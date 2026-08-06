document.querySelectorAll('.tabbed-panes').forEach((tabbedPanes) => {
    var tabs = tabbedPanes.querySelectorAll('[role="tab"]');
    var panes = tabbedPanes.querySelectorAll('[role="tabpanel"]');

    function activateTab(tab) {
        var paneId = tab.getAttribute('aria-controls');
        var pane = paneId ? document.getElementById(paneId) : null;

        tabs.forEach((t) => {
            t.classList.remove('-active');
            t.setAttribute('aria-selected', 'false');
            t.setAttribute('tabindex', '-1');
        });

        panes.forEach((p) => {
            p.classList.remove('-active');
            p.setAttribute('hidden', '');
        });

        tab.classList.add('-active');
        tab.setAttribute('aria-selected', 'true');
        tab.setAttribute('tabindex', '0');

        if (pane) {
            pane.classList.add('-active');
            pane.removeAttribute('hidden');
        }
    }

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => activateTab(tab));
    });
});
