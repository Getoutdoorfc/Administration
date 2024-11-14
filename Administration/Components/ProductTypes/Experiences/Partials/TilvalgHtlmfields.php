<?php
defined( 'ABSPATH' ) || exit;

global $product;

$tilvalg = get_post_meta( $product->get_id(), '_experience_tilvalg', true );

if ( ! empty( $tilvalg ) ) :
    ?>

    <div class="experience-tilvalg">
        <?php foreach ( $tilvalg as $item ) : ?>
            <div class="tilvalg-item">
                <label><?php echo esc_html( $item['name'] ); ?>:</label>
                <?php
                $options = explode( ',', $item['options'] );
                ?>
                <select name="experience_tilvalg[<?php echo esc_attr( $item['name'] ); ?>]">
                    <?php foreach ( $options as $option ) : ?>
                        <option value="<?php echo esc_attr( trim( $option ) ); ?>"><?php echo esc_html( trim( $option ) ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>
