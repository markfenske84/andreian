var announcementBar = document.getElementById('announcement-bar');

if(announcementBar) {

    // Function to get the current timestamp
    function getCurrentTimestamp() {
        return Math.floor(new Date().getTime() / 1000); // Convert to seconds
    }

    // Check if the announcement bar should be displayed based on localStorage
    var lastClosedTimestamp = parseInt(localStorage.getItem('announcement_bar_closed_timestamp'), 10);
    var dismissalReset = announcementBar.getAttribute('data-dismiss-length');
    var currentTimestamp = getCurrentTimestamp();

    if (!lastClosedTimestamp || currentTimestamp - lastClosedTimestamp >= parseDismissalReset(dismissalReset)) {
        // console.log('lastClosedTimestamp:', lastClosedTimestamp);
        // console.log('currentTimestamp:', currentTimestamp);
        // console.log('Difference:', currentTimestamp - lastClosedTimestamp);
        // console.log('Dismissal Reset:', parseDismissalReset(dismissalReset));

        // If not set to not display or the dismissal period has passed, show the announcement bar

        // Add click event listener to the close button
        var closeButton = document.getElementsByClassName('_close')[0];

        if (closeButton) {
            closeButton.addEventListener('click', function () {
                // Hide the announcement bar
                var announcementBar = document.getElementById('announcement-bar');
                if (announcementBar) {
                    announcementBar.style.display = 'none';

                    // Set the timestamp in localStorage when the bar was closed
                    localStorage.setItem('announcement_bar_closed_timestamp', getCurrentTimestamp());
                }
            });
        }
    } else {
        // console.log('lastClosedTimestamp:', lastClosedTimestamp);
        // console.log('currentTimestamp:', currentTimestamp);
        // console.log('Difference:', currentTimestamp - lastClosedTimestamp);
        // console.log('Dismissal Reset:', parseDismissalReset(dismissalReset));
        
        // If set to not display or within the dismissal period, hide the announcement bar
        var announcementBar = document.getElementById('announcement-bar');
        if (announcementBar) {
            announcementBar.style.display = 'none';
        }
    }

    // Helper function to parse dismissal reset duration to seconds
    function parseDismissalReset(dismissalReset) {
        switch (dismissalReset) {
            // case '1 minute':
            //     return 60;
            case '1 hour':
                return 60 * 60;
            case '1 day':
                return 24 * 60 * 60;
            case '1 week':
                return 7 * 24 * 60 * 60;
            case '1 month':
                // Adjust as needed
                return 30 * 24 * 60 * 60;
            case '1 year':
                // Adjust as needed
                return 365 * 24 * 60 * 60;
            case 'forever':
                return Infinity; // Set to a very large value for "forever"
            default:
                return 60; // Default to 1 minute in seconds
        }
    }

}