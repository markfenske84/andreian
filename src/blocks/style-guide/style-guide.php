<? 
/**
 * Block: Example Block
 */
?>

</section>

<section class="style-guide<? if(isset($block['className'])) { echo ' ' . $block['className']; } ?>">
    <div class="_flex _container">
        <div style="padding-right: 2rem;">
            <h1>Heading 1</h1>
            <h2>Heading 2</h2>
            <h3>Heading 3</h3>
            <h4>Heading 4</h4>
            <h5>Heading 5</h5>
            <h6>Heading 6</h6>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla euismod, nisl eget aliquam ultricies, nunc ipsum aliquet nunc, vitae ali quam nunc nisl quis nunc. Nulla facilisi. Nulla facilisi. Nulla facilisi. Nulla facilisi. Nulla facilisi.</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla euismod, nisl eget aliquam ultricies, nunc ipsum aliquet nunc, vitae ali quam nunc nisl quis nunc. Nulla facilisi. Nulla facilisi. Nulla facilisi. Nulla facilisi. Nulla facilisi.</p>
            <ul>
                <li>List Item Number One</li>
                <li>List Item Number Two</li>
                <li>List Item Number Three</li>
            </ul>
            <ol>
                <li>List Item Number One</li>
                <li>List Item Number Two</li>
                <li>List Item Number Three</li>
            </ol>

            <details>
                <summary>This is an FAQ / Default details item</summary>
                <div class="details-inner">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla euismod, nisl eget aliquam ultricies, nunc ipsum aliquet nunc, vitae ali quam nunc nisl quis nunc. Nulla facilisi. Nulla facilisi. Nulla facilisi. Nulla facilisi. Nulla facilisi.</div>
            </details>

            <blockquote>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla euismod, nisl eget aliquam ultricies, nunc ipsum aliquet nunc, vitae ali quam nunc nisl quis nunc. Nulla facilisi. Nulla facilisi. Nulla facilisi. Nulla facilisi. Nulla facilisi.</p>
            </blockquote>
        </div>

        <div>
            <h3>Color Palette</h3>
            <div class="color-swatches">
                <div class="swatch _bg -primary">
                    <p>Primary</p>
                </div>
                <div class="swatch _bg -secondary">
                    <p>Secondary</p>
                </div>
                <div class="swatch _bg -tertiary">
                    <p>Tertiary</p>
                </div>
                <div class="swatch _bg -quaternary">
                    <p>Quaternary</p>
                </div>
                <div class="swatch _bg -text">
                    <p>Text</p>
                </div>
            </div>

            <a href="#" class="_anchor">Simple Link</a><br>
            <a href="#" class="_anchor -stylized">Stylized Link</a><br>
            <a href="#" class="_button">Button -Unmodifed</a><br>
            <a href="#" class="_button -primary">Button -Primary</a><br>
            <a href="#" class="_button -secondary">Button -Secondary</a><br>
            <a href="#" class="_button -tertiary">Button -Tertiary</a><br>
            <a href="#" class="_button -quaternary">Button -Quaternary</a><br>
            <a href="#" class="_button -outline -white">Button -Outline -White</a>
        </div>

    </div>
</section>

<section class="_container">