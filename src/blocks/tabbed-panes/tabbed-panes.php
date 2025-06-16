<? 
    $tabbed_panes = get_field('tabbed_panes'); // repeater -> tab_label (text), pane_content (WYSIWYG)  ?>

<div 
    <? if(isset($block['anchor'])) { ?>
    id="<?= esc_attr( $block['anchor'] ); ?>" 
    <? } ?>
    class="
        tabbed-panes
        <? if(isset($block['className'])) { echo ' ' . $block['className']; } ?>" 
    role="tablist">

    <? if ($tabbed_panes): ?>

        <div class="_tabs -flex">

            <? foreach ($tabbed_panes as $index => $tabbed_pane):
                $tab_label = $tabbed_pane['tab_label'];
                $is_active = ($index === 0) ? '-active' : '';
                $slug = slugify($tab_label); ?>

                <button 
                    class="_tab <?= $is_active; ?>"
                    role="tab"
                    aria-selected="<?= ($index === 0) ? 'true' : 'false'; ?>"
                    data-slug="<?= $slug; ?>"
                    aria-controls="<?= $slug . '-pane'; ?>">

                    <?= $tab_label; ?>

                </button>

            <? endforeach; ?>

        </div>

        <div class="_panes">

            <? foreach ($tabbed_panes as $index => $tabbed_pane):
                $tab_label = $tabbed_pane['tab_label'];
                $is_active = ($index === 0) ? '-active' : '';
                $slug = slugify($tab_label); ?>

                <div 
                    class="_pane <?= $is_active; ?>"
                    role="tabpanel"
                    data-slug="<?= $slug; ?>"
                    aria-labelledby="<?= $slug . '-tab'; ?>"
                    tabindex="<?= ($index === 0) ? '0' : '-1'; ?>">

                    <?= $tabbed_pane['pane_content']; ?>

                </div>

            <? endforeach; ?>

        </div>

    <? endif; ?>

</div>