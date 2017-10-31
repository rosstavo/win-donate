<div class="wrap">

    <h2>
        <img src="<?php echo WIN_URL; ?>/img/logo.png" alt="World In Need logo" style="vertical-align: text-top; margin-right: 0.2em;">
        World In Need Donations
    </h2>

    <div class="card">
        <p>Include a donation form on a page or post with the shortcode <code>[donate_form]</code> and a child sponsorship form with <code>[regular_giving_form]</code>.</p>
        <p>For the <code>[donate_form]</code> shortcode, you can specify whether or not you'd like this form to just be for one campaign. All you need to do is add a "to" flag, like so: <code>[donate_form to="Afghanistan"]</code>. This will remove the dropdown in the form and replace it with your specified campaign.</p>
        <p>If you'd like to include a feeding programme form, specify the type like this: <code>[regular_giving_form type="feeding"]</code>. You can also include a title attribute, for instance <code>[regular_giving_form title="Here is my title"]</code></p>
        <p><span class="wp-ui-text-notification"><strong>Note: Only one form can be used per page.</strong></span></p>
        <hr />
        <p>For technical support, please send an email to <a href="mailto:ross@nevisonhardy.co.uk">ross@nevisonhardy.co.uk</a></p>
    </div>

    <form action="options.php" method="POST">
        <?php settings_fields( 'WIN-donate-settings' ); ?>
        <?php do_settings_sections( 'WIN-donate-settings' ); ?>

        <div class="card">
            <h2>Countries</h2>
            <p>Here you can define which countries appear as options in the World In Need donate or sponsorship forms.</p>
            <p class="description">Please include a comma between each, for instance: "Alpha, Beta, Charlie, Delta". The default option of "wherever the need is greatest" will be added afterwards automatically.</p>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="win_donate_countries">Donate:</label></th>
                    <td>
                        <input type="text" value="<?php echo get_option( 'win_donate_countries' ); ?>" name="win_donate_countries" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="win_donate_countries">Sponsor:</label></th>
                    <td>
                        <input type="text" value="<?php echo get_option( 'win_sponsor_countries' ); ?>" name="win_sponsor_countries" class="regular-text" />
                    </td>
                </tr>
            </table>
        </div>

        <div class="card">
            <h2>E-mails</h2>
            <p>These settings are for where notification e-mails will be sent upon a successful donation.</p>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="win_donate_email">Donate:</label></th>
                    <td>
                        <input type="text" value="<?php echo get_option( 'win_donate_email' ); ?>" name="win_donate_email" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="win_sponsor_email">Sponsor:</label></th>
                    <td>
                        <input type="text" value="<?php echo get_option( 'win_sponsor_email' ); ?>" name="win_sponsor_email" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="win_feeding_email">Feeding Programme:</label></th>
                    <td>
                        <input type="text" value="<?php echo get_option( 'win_feeding_email' ); ?>" name="win_feeding_email" class="regular-text" />
                    </td>
                </tr>
            </table>
        </div>

        <div class="card">
            <h2>Giftaid</h2>
            <p>Here you can add a Giftaid disclaimer that will appear below the donate forms. This will display by default.</p>
            <p>To hide it, write the shortcode like this: <code>[donate_form giftaid="hide"]</code></p>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="win_giftaid_disclaimer">Disclaimer:</label></th>
                    <td>
                        <textarea name="win_giftaid_disclaimer" class="regular-text" rows="5"><?php echo get_option( 'win_giftaid_disclaimer' ); ?></textarea>
                    </td>
                </tr>
            </table>
        </div>

        <div class="card">
            <h2>API Keys</h2>
            <p>These keys allow you to connect to Paypal and GoCardless.
            <span class="wp-ui-text-notification"><strong>Please leave these unaltered unless you are aware of what you are doing!</strong></span></p>

            <fieldset>
                <label for="win_sandbox">
                    <input name="win_sandbox" type="checkbox" id="win_sandbox" value="true" <?php checked( 'true', get_option( 'win_sandbox', 'true' ) ); ?> /> Use sandbox testing mode
                </label>
                <p class="description">If checked, Paypal and GoCardless will use their sandbox checkouts instead of the live checkouts. Used for testing purposes.</p>
            </fieldset>

            <hr />

            <h3>Paypal</h3>
            <table class="form-table">

                <tr>
                    <th scope="row">Sandbox:</th>
                    <td>
                        <label for="win_paypal_sandbox_client">Client</label>
                    </td>
                    <td>
                        <input type="text" value="<?php echo get_option( 'win_paypal_sandbox_client' ); ?>" name="win_paypal_sandbox_client" class="regular-text" />
                    </td>
                </tr>

                <tr>
                    <th scope="row"></th>
                    <td>
                        <label for="win_paypal_sandbox_secret">Secret</label>
                    </td>
                    <td>
                        <input type="text" value="<?php echo get_option( 'win_paypal_sandbox_secret' ); ?>" name="win_paypal_sandbox_secret" class="regular-text" />
                    </td>
                </tr>

                <tr>
                    <th scope="row">Live:</th>
                    <td>
                        <label for="win_paypal_live_client">Client</label>
                    </td>
                    <td>
                        <input type="text" value="<?php echo get_option( 'win_paypal_live_client' ); ?>" name="win_paypal_live_client" class="regular-text" />
                    </td>
                </tr>

                <tr>
                    <th scope="row"></th>
                    <td>
                        <label for="win_paypal_live_secret">Secret</label>
                    </td>
                    <td>
                        <input type="text" value="<?php echo get_option( 'win_paypal_live_secret' ); ?>" name="win_paypal_live_secret" class="regular-text" />
                    </td>
                </tr>

            </table>

            <hr>

            <h3>GoCardless</h3>

            <table class="form-table">
                <tr>
                    <th scope="row">Sandbox:</th>
                    <td>
                        <input type="text" value="<?php echo get_option( 'win_gocardless_sandbox_access' ); ?>" name="win_gocardless_sandbox_access" class="regular-text" />
                    </td>
                </tr>

                <tr>
                    <th scope="row">Live:</th>
                    <td>
                        <input type="text" value="<?php echo get_option( 'win_gocardless_live_access' ); ?>" name="win_gocardless_live_access" class="regular-text" />
                    </td>
                </tr>
            </table>
        </div>

        <?php submit_button(); ?>
    </form>
</div>
