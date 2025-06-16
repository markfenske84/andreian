// Find all elements with the class "read-more"=
let detailsElements = document.querySelectorAll('.read-more');
if(detailsElements) {
    detailsElements.forEach(function(detailsElement) {
        // Add event listener to each details element
        detailsElement.addEventListener('toggle', function() {
            // Update the summary text when the details element is toggled
            let summary = detailsElement.querySelector('summary');
            if (detailsElement.open) {
                summary.innerText = '- Read Less';
            } else {
                summary.innerText = '+ Read More';
            }
        });
    });
}  