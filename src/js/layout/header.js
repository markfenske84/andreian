document.addEventListener("DOMContentLoaded", function() {

    // Detect the Mobile Menu Layout selected in the WP Customizer via localized data.
    const mobileMenuLayout = (window.krypton_localize && window.krypton_localize.mobile_menu_layout) ? window.krypton_localize.mobile_menu_layout : 'dropdown';

    const isPanelLayout = mobileMenuLayout === 'panel';

    /*** START --- Accessible Submenu Navigation ***/

    // Function to insert a button with a down arrow
    function insertButtonWithArrow(menuItem, direction = null) {
        // Avoid duplicating toggle buttons
        if (menuItem.querySelector('.toggle-button')) return;

        const button = document.createElement('button');

        let iconClass;
        if (direction) {
            iconClass = direction === 'right' ? 'fa-chevron-right' : 'fa-chevron-down';
        } else {
            iconClass = isPanelLayout ? 'fa-chevron-right' : 'fa-chevron-down';
        }

        button.innerHTML = `<i class="fa-solid ${iconClass}"></i>`;
        button.classList.add('toggle-button');

        // add an aria-label to the button
        const buttonLabel = menuItem.querySelector('a').innerText;
        button.setAttribute('aria-label', `Toggle submenu for ${buttonLabel}`);

        const subMenu = menuItem.querySelector('.sub-menu');
        menuItem.insertBefore(button, subMenu);

        // For desktop positioning (existing behaviour)
        const topLevelHeight = menuItem.offsetHeight;
        subMenu.style.top = `${topLevelHeight}px`;
    }

    // Attach event listeners to menu items (root level inside off-canvas)
    const rootMenuItems = document.querySelectorAll('#mobile-offcanvas .menu-item-has-children');

    rootMenuItems.forEach(item => {
        // Ensure each item has a toggle button.
        insertButtonWithArrow(item, isPanelLayout ? 'right' : 'down');

        const button = item.querySelector('.toggle-button');

        if ( isPanelLayout ) {
            // Slide-in panel behaviour
            button.addEventListener('click', function(e) {
                e.preventDefault();
                openPanel(item);
            });
        } else {
            // Expansion Dropdown behaviour (existing)
            button.addEventListener('click', function() {
                item.querySelector('.sub-menu').classList.toggle('show');
                item.classList.toggle('open');
            });
        }
    });

    /** -------------------------------------------
     * Slide-in Panel helper functions (panel mode)
     * ------------------------------------------- */
    function openPanel(menuItem) {
        const subMenu = menuItem.querySelector('.sub-menu');
        if (!subMenu) return;

        // Clone submenu to preserve original.
        const panelMenu = subMenu.cloneNode(true);
        // Remove any previously inserted toggle buttons to avoid duplicates.
        panelMenu.querySelectorAll('.toggle-button').forEach(btn => btn.remove());

        // Build panel wrapper
        const panel = document.createElement('div');
        panel.classList.add('mobile-slide-panel');

        const parentLabel = menuItem.querySelector('a').innerText;

        panel.innerHTML = `
            <div class="mobile-slide-panel__header">
                <button class="mobile-slide-panel__back" aria-label="Back"><i class="fa-solid fa-chevron-left"></i><span class="text">Back</span></button>
                <span class="mobile-slide-panel__title">${parentLabel}</span>
            </div>`;

        panel.appendChild(panelMenu);

        // Append panel into off-canvas content
        const offcanvasContent = document.querySelector('#mobile-offcanvas .mobile-offcanvas__content');
        if (!offcanvasContent) return;
        offcanvasContent.appendChild(panel);

        // Allow CSS transition
        requestAnimationFrame(() => {
            panel.classList.add('open');
        });

        // Bind back button
        const backButton = panel.querySelector('.mobile-slide-panel__back');
        backButton.addEventListener('click', () => closePanel(panel));

        // Initialize nested menu items inside this panel
        initNestedPanel(panel);
    }

    function closePanel(panel) {
        panel.classList.remove('open');
        panel.addEventListener('transitionend', () => {
            panel.remove();
        }, { once: true });
    }

    function initNestedPanel(container) {
        const nestedMenuItems = container.querySelectorAll('.menu-item-has-children');
        nestedMenuItems.forEach(item => {
            insertButtonWithArrow(item, isPanelLayout ? 'right' : 'down');
            const btn = item.querySelector('.toggle-button');
            if (!btn) return;
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                openPanel(item);
            });
        });
    }

    /*** END --- Accessible Submenu Navigation 
     * 
     * 
     *  START --- Mobile Menu Toggle ***/

    const mobileOffcanvasToggles = document.querySelectorAll('.mobile-offcanvas-toggle');
    mobileOffcanvasToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const mobileOffcanvas = document.querySelector('#mobile-offcanvas');
            mobileOffcanvas.classList.toggle('open');
            document.body.classList.toggle('mobile-offcanvas-open');
        });
    });
    // if clicking outside of the mobile offcanvas, close it
    document.addEventListener('click', function(event) {
        const mobileOffcanvas = document.querySelector('#mobile-offcanvas');
        if (event.target.closest('.mobile-offcanvas-toggle') === null && event.target.closest('#mobile-offcanvas') === null) {
            mobileOffcanvas.classList.remove('open');
            document.body.classList.remove('mobile-offcanvas-open');
        }
    });

    /*** END --- Mobile Menu Toggle
     * 
     * 
     *  START --- Something else... ***/

    /*** Slide-in Panel Sub-Navigation (placeholder) ***/
    if ( isPanelLayout ) {
        // TODO: Implement slide-in panel behaviour for mobile sub-menus.
        // This placeholder ensures the script doesn't break when panel layout is selected.
    }

    /*** Desktop navigation toggle buttons (always down arrow) ***/
    const desktopMenuItems = document.querySelectorAll('.site-header .menu-item-has-children');
    desktopMenuItems.forEach(item => {
        insertButtonWithArrow(item, 'down');
        const button = item.querySelector('.toggle-button');
        if (!button) return;

        // Preserve original dropdown behaviour
        button.addEventListener('click', function(){
            item.querySelector('.sub-menu').classList.toggle('show');
            item.classList.toggle('open');
        });
    });
});