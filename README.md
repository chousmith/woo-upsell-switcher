# Woo Upsell Switcher
Plugin to handle the idea of different Upsells product links based on different user locations?

The name says "Woo" but at this time it is pretty much solely for switching the Infusionsoft One-click Upsell functionality, which really has very little / nothing to do with WooCommerce...

Currently "requires" the Infusionsoft One-click Upsell and Infusionsoft SDK plugins, meaning it adds an admin notice complaining if those 2 modules are not installed.

The Infusionsoft One-click Upsell plugin gives you Shortcodes for Upsell buttons like

`[upsell product_id="75" button_text="Buy This Other Product Now!" test="true"]`

This plugin provides an `[upsellswitch][/upsellswitch]` shortcode that to wrap around that `[upsell ...]` like

`[upsellswitch def="75" intl="77" ca="79"][upsell product_id="75" button_text="Buy This Other Product Now!" test="true"][/upsellswitch]`

Currently just works based off if the URL has GET args `orderId=#####` and `contactId=######`, it will attempt to use the Infusionsoft SDK to connect to your Infusionsoft setup, query that orderId, make sure the Contact record for that orderId matches the contactId provided, find the Location of that orderId, and then switch the `product_id="#"` in the wrapped `[upsell ...]` shortcode based on the following currently-hardcoded ideas...

* `def` must match the `product_id` specified in the `[upsell ...]` shortcode tag in order for this replacing to work right (at least for right now, due to initial time constraints)
* if the `$_GET['orderId']` order is found and the `$order->ContactId == $_GET['contactId']`, then the following `str_replace(...)` happens :
  * `intl` if the `$order->ShipCountry != 'United States'`
  * `ca` if the `$order->ShipCountry == 'United States' && $order->ShipState == 'CA'`

In the future, this may become more robust of switching ideas.
