<?php

$atts = shortcode_atts(
    array (
        'title' => null,
        'type' => 'sponsor',
        'giftaid' => 'show'
    ), $atts
);

$countries = explode( ',', get_option( 'win_sponsor_countries' ) );

?>

<div class="win-sponsor win-widget clearfix">
    <h3 style="margin-top:0;">Our online regular giving forms are temporarily unavailable. If you would like to sign up, please contact us via email or phone.</h3>
    <p>e: accounts@worldinneed.co.uk<br />t: (+44) (0) 1892 669834</p>
</div>
