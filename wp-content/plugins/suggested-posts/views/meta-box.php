<?php
wp_nonce_field( basename( __FILE__ ), 'supo_nonce' );
?>
<p>
<label for="meta-text" class="supo-row-title"><?php _e( 'Tags', 'supo-textdomain' )?></label>
</p>
<?php
foreach( $tags as $tag ) {
    $selected = in_array( $tag, $selected_tags);
    ?>
    <p>
        <div class="supo-row-content">
            <label for="meta-checkbox">
                <input type="checkbox" name="supo-meta-checkbox[]" id="meta-checkbox-<?=$tag?>" value="<?=$tag?>" <?= $selected ? 'checked="checked"': ''  ?> />
                <?php _e( $tag, 'supo-textdomain' )?>
            </label>
        </div>
    </p>
    <?php
}

