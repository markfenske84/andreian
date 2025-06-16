<? 
/**
 * Initialization function for the Webfor theme.
 */

if ( ! defined( 'ABSPATH' ) ) exit; 

?>


<style>
    .svg-data-uri {}
    .svg-data-uri .container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        grid-gap: 20px;
    }
    @media screen and (max-width: 767px) {
        .svg-data-uri .container {
            grid-template-columns: 1fr;
        }
    }
    .wrapper textarea {
        width: 100%;
        min-height: 222px;
        padding: 10px;
        font-size: 16px;
        border-radius: 0;
    }
    #preview {
        width: calc(100% - 20px);
        height: 200px;
        border: 1px dashed #dadcde;
        padding: 10px;
        background-image: none;
        background-repeat: no-repeat;
        background-position: 0 0;
    }
</style>

<div class="svg-data-uri">
    <h2>SVG to CSS Converter</h2>
    <h3>Instructions</h3>
    <p>Copy and paste your SVG into the first textarea.  The second textarea will automatically update with the CSS background-image property.  The SVG preview will also update with the SVG you pasted.  If you're using Adobe XD, right-click on the asset and select <strong>Copy SVG Code</strong> from the dropdown.  Next paste that value into the first textarea field under <strong>Enter SVG</strong>.</p>
    <p>Once the <strong>Ready for CSS</strong> textarea displays the generated code, click inside the field to copy it to your clipboard and use in your code.</p>
    <hr>
    <div class="container">
        <div class="wrapper">
            <h3>Enter SVG:</h3>
            <textarea name="svg" id="svg" spellcheck="false" onchange="convert()"></textarea>
        </div>
        <div class="wrapper">
            <h3>Ready for CSS:</h3>
            <textarea name="result" id="result" spellcheck="false"></textarea>
            <p id="svg-clipboard-notice" class="clipboard-notice">Copied to Clipboard</p>
        </div>
        <div class="wrapper">
            <h3>SVG Preview:</h3>
            <div name="preview" id="preview"></div>
        </div>
    </div>
</div>

<script>
    const svgToTinyDataUri = (() => {
        // Source: https://github.com/tigt/mini-svg-data-uri
        const reWhitespace = /\s+/g,
            reUrlHexPairs = /%[\dA-F]{2}/g,
            reSpacedXmlTags = /> </g,
            reHexColor = /['"]#([\da-fA-F]{6}|[\da-fA-F]{3})['"]/g,
            hexDecode = { "%20": " ", "%3D": "=", "%3A": ":", "%2F": "/" },
            specialHexDecode = (match) => hexDecode[match] || match.toLowerCase(),
            colorLowerCase = (match) => match.toLowerCase(),
            addNameSpace = (svg) => {
            if (svg.indexOf(`http://www.w3.org/2000/svg`) < 0) {
                svg = svg.replace(/<svg/g, `<svg xmlns='http://www.w3.org/2000/svg'`);
            }
            return svg;
            },
            svgToTinyDataUri = (svg) => {
            svg = String(svg);
            if (svg.charCodeAt(0) === 0xfeff) svg = svg.slice(1);
            svg = addNameSpace(svg)
                .trim()
                .replace(reHexColor, colorLowerCase)
                .replace(reWhitespace, ` `)
                .replace(reSpacedXmlTags, `><`)
                .replaceAll(`"`, `'`);
            svg = encodeURIComponent(svg);
            svg = svg.replace(reUrlHexPairs, specialHexDecode);
            return `data:image/svg+xml,` + svg;
            };
        svgToTinyDataUri.toSrcset = (svg) => svgToTinyDataUri(svg).replace(/ /g, `%20`);
        return svgToTinyDataUri;
        })();

        const svgarea = document.querySelector(`#svg`);
        const result = document.querySelector(`#result`);
        const preview = document.querySelector(`#preview`);

        svgarea.addEventListener("input", function () {
        const resultCss = `background-image: url("` + svgToTinyDataUri(svg.value) + `");`;
        preview.setAttribute(`style`, resultCss);
        result.value = resultCss;
    });

    // if the user clicks on the result textarea, copy the value to the clipboard and display the notice message for 5 seconds before hiding
    if (result) {
        result.addEventListener('click', function () {
            if (result.value.trim() !== '') { // Check if textarea is not empty
                result.select();
                document.execCommand('copy');
                document.querySelector(`#svg-clipboard-notice`).style.display = `inline-block`;
                setTimeout(() => {
                    document.querySelector(`#svg-clipboard-notice`).style.display = `none`;
                }, 5000);
            }
        });
    }

</script>

<?