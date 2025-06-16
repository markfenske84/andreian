document.addEventListener("DOMContentLoaded", function() {

    /*** START --- Accessible Submenu Navigation ***/

    // Function to insert a button with a down arrow
    function insertButtonWithArrow(menuItem) {
        const button = document.createElement('button');
        button.innerHTML = '<i class="fa-solid fa-chevron-down"></i>'; // Down arrow character
        button.classList.add('toggle-button');
        // add an aria-label to the button
        const buttonLabel = menuItem.querySelector('a').innerText;
        button.setAttribute('aria-label', `Toggle submenu for ${buttonLabel}`);
        const subMenu = menuItem.querySelector('.sub-menu');
        menuItem.insertBefore(button, subMenu);
        // Get height of the top-level .menu-item-has-children
        const topLevelHeight = menuItem.offsetHeight;
        // Set top property for the sub-menu
        subMenu.style.top = `${topLevelHeight}px`;
    }

    // Attach event listeners to menu items
    const menuItems = document.querySelectorAll('.menu-item-has-children');
    menuItems.forEach(item => {
        // Insert button with arrow into menu items with class 'menu-item-has-children'
        insertButtonWithArrow(item);
        // Add event listener to toggle 'show' class on button click
        const button = item.querySelector('.toggle-button');
        button.addEventListener('click', function() {
            item.querySelector('.sub-menu').classList.toggle('show');
            // target item parent to toggle class of open
            item.classList.toggle('open');
        });
    }); 

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

    
});