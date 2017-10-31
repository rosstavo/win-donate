<?php

$atts = shortcode_atts(
    array (
        'title' => null,
        'to' => null,
        'giftaid' => 'show'
    ), $atts
);

$countries = explode(',', get_option( 'win_donate_countries' ) );

?>

<div class="win-donate win-widget clearfix">
    <input type="checkbox" id="refresh">

    <?php if ( $atts[ 'title' ] != null ) : ?>
        <h3><?php echo $atts[ 'title' ]; ?></h3>
    <?php endif; ?>

    <form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" method="post" id="donate-form">
        <input type="radio" name="donate-option" value="one-off" id="one-off" class="radio-button" checked>
        <label for="one-off" class="donate-option one-off">One-off</label>
        <input type="radio" name="donate-option" value="monthly" id="monthly" class="radio-button">
        <label for="monthly" class="donate-option monthly">Monthly</label>

        <span id="validation"></span>

        <?php if ( $atts[ 'to' ] != null ) : ?>
            <div class="form-group clearfix extra-pad">
                <p>I would like to donate £ <input type="text" name="amount" class="amount" id="amount" required> to <strong><?php echo $atts[ 'to' ]; ?></strong></p>
                <input type="hidden" name="countries" id="countries" value="<?php echo $atts[ 'to' ]; ?>">
            </div>
        <?php else : ?>
            <div class="form-group clearfix extra-pad">
                <p>I would like to donate £ <input type="text" name="amount" class="amount" id="amount" required> to
                    <select name="countries" id="countries" class="input-inline">
                        <option value="wherever the need is greatest" selected>wherever the need is greatest</option>

                        <?php foreach ( $countries as $country ) : ?>
                            <option value="<?php echo $country; ?>"><?php echo $country; ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
            </div>
        <?php endif; ?>

        <div class="form-group clearfix">
            <div class="col-1-2 col">
                <input type="checkbox" id="giftaid" name="giftaid">
                <label for="giftaid">
                    <span class="checkbox"></span>
                    <img src="<?php echo WIN_URL; ?>/img/giftaid.png" alt="Giftaid it?">
                </label>
            </div>
            <div class="col-1-2 col">
                <button class="submit" value="Donate" type="submit" name="paypal" disabled>Next &gt;</button>
            </div>
        </div>
    </form>
</div>

<?php if ( $atts[ 'giftaid' ] !== 'hide' && get_option( 'win_giftaid_disclaimer' ) ) : ?>
    <p><small><span style="color: #ff0000;">*</span> <?php echo get_option( 'win_giftaid_disclaimer' ); ?></small></p>
<?php endif; ?>
