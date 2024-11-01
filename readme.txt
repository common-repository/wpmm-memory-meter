=== Memory Meter ===
Contributors: ninetyninew
Tags: memory limit, memory usage, memory log, memory meter, memory
Donate link: https://ko-fi.com/ninetyninew
Stable tag: 2.1.0
Tested up to: 6.6.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

View and log website memory usage and check memory limits.

== Description ==

Logs memory usage as users browse your website providing latest logs and flagged logs (when memory usage is over specific percentages).

For users who have specific capabilities (e.g. administrators) a memory meter is shown in the admin bar showing memory usage as the website and dashboard is navigated and includes several configuration options.

= Features =

- Logs memory usage as users browse the website/dashboard
- Displays current memory usage by file size
- Displays current memory usage by percentage
- Memory limit display
- PHP memory limit display
- PHP max execution time display
- WP memory limit display
- WP max memory limit display
- Latest memory usage logs
- Flagged memory usage logs
- Memory usage page request logs for all users, instead of just users with `manage_options` capability (Premium)
- Memory usage shown in JS console (Premium)

= Usage =

After installation memory usage will be logged for any user as they browse the website and dashboard, you can see the logs via the Memory Meter menu in the WordPress dashboard. Note that page request information is only logged for users with the `manage_options` capability in the free version of this plugin.  The premium version logs page request information for all users regardless of their capability.

By default the memory meter will be shown in the admin bar if you are logged in as user with the `manage_options` capability such as an administrator (the capability can be modified if required, see the configuration tab via the Memory Meter page).

In this scenario you will see the meter in the admin bar at the top of your WordPress dashboard and the frontend of your website. Note that there is a user profile setting in WordPress to enable the admin bar, it is enabled by default, if this is not enabled you will not be able to view the memory meter in the admin bar.

If you hover over the memory meter you will see a number of options, such as logs and configuration. In addition to the admin bar, a menu item is included in your WordPress dashboard to give access to the logs, confirguration, etc.

Note that the memory meter doesn't need to be displayed to ensure memory usage is logged, this is done regardless of whether the memory meter is being displayed.

= Donate =

If this product has helped you, please consider [making a donation](https://ko-fi.com/ninetyninew).

== Screenshots ==

1. Memory meter in admin bar
2. Logs
3. Configuration

== Frequently Asked Questions ==

= Where can I find the memory meter? =

See the usage information above.

= How do I change the memory file size to KB or GB rather than MB? =

You can use the `wpmm_memory_meter_file_size_format` filter hook.

= What percentage of memory usage will make the meter change color? =

When your website is using greater than 75% of allocated memory the meter will change to orange (warning), if greater than 90% it will change to red (bad). If you want to change these default percentages you can use the `wpmm_memory_meter_memory_usage_percentage_warning` and `wpmm_memory_meter_memory_usage_percentage_bad` filter hooks.

= Who can see the memory meter/can I make it visible to specific users? =

The memory meter displays by default in the admin bar for any user with the `manage_options` capability, which is usually administrators. You can change the capability required to display the memory meter by using the `wpmm_memory_meter_capability` filter hook. Note that this filter hook does not effect memory usage logging, and the memory meter being displayed or not doesn't effect memory usage logging.

= Can I disable the memory meter colors? =

Yes, you can disable the colors using the `wpmm_memory_meter_memory_usage_colors` filter hook.

= Can I disable logs? =

Yes, you can disable the logs using the `wpmm_memory_meter_logs` filter hook.

== Installation ==

Before using this product, please ensure you review and accept our [terms and conditions](https://99w.co.uk/#terms-conditions) and [privacy policy](https://99w.co.uk/#privacy-policy).

Before using this product on a production website you should thoroughly test it on a staging/development environment, including all aspects of your website and potential data volumes, even if not directly related to the functionality the product provides.

The same process should also be completed when updating any aspect of your website in future, such as performing installations/updates, making changes to any configuration, custom web development, etc.

Always refer to the changelog before updating.

= Installation =

Please see [this documentation](https://wordpress.org/support/article/managing-plugins/#installing-plugins-1).

= Updates =

Please see [this documentation](https://wordpress.org/documentation/article/manage-plugins/#updating-plugins).

= Minimum Requirements =

* PHP 7.4.0
* WordPress 6.4.0

= BETA Functionality =

We may occasionally include BETA functionality, this is highlighted with a `(BETA)` label. Functionality with this label should be used with caution and is only recommended to be tested on a staging/development environment. The functionality is included so users can test the functionality/provide feedback before it becomes stable, at which point the `(BETA)` label will be removed. Note that there may be occasions where BETA functionality is determined unsuitable for use and removed entirely.

= Caching =

If you are using any form of caching then it is recommended that the cache lifespan/expiry should be set to 10 hours or less. This is recommended by most major caching solutions to avoid potential issues with WordPress nonces.

= Screen Sizes =

- Frontend: Where elements may be displayed on the frontend they will fit within the screen width
- Backend: Where interfaces may be displayed it is recommended to use a desktop computer with a resolution of 1920x1080 or higher, for lower resolutions any interfaces will attempt to fit within the screen width but some elements may be close together and/or larger than the screen width

= Translation =

We generally recommend [Loco Translate](https://wordpress.org/plugins/loco-translate/) to translate and/or adapt text strings within this product.

= Works With =

Where we have explicitly stated this product works with another product, this should only be assumed accurate if you are using the version of the other product which was the latest at the time the latest version of this product was released. This is because, while usually unlikely, the other product may have changed functionality which effects this product.

== Changelog ==

= 2.1.0 - 2024-08-23 =

* Add: .pot to languages folder
* Add: Requires plugins dependency header
* Add: wp_set_script_translations to JS assets
* Update: Freemius SDK
* Update: WordPress requires at least 6.4.0
* Update: WordPress tested up to 6.6.1

= 2.0.1 - 2024-07-10 =

* Update: composer.json and composer.lock to woocommerce/woocommerce-sniffs 1.0.0
* Update: Installation and updates information in readme.txt
* Update: phpcs.xml codesniffs
* Update: Freemius SDK
* Update: WordPress tested up to 6.5.5

= 2.0.0 - 2024-04-10 =

* Add: Logs page request information for any user with the manage_options capability, to log for all users regardless of capability use the premium version
* Add: Translation information in readme.txt
* Update: Configuration tab information
* Update: Descriptions and usage information in readme.txt
* Update: Freemius SDK
* Update: WordPress tested up to 6.5.2

= 1.3.6 - 2024-03-08 =

* Add: BETA functionality information to readme.txt
* Add: Caching information to readme.txt
* Add: Donation information to readme.txt
* Add: Works with information to readme.txt
* Update: Screen sizes information in readme.txt
* Update: WordPress tested up to 6.4.3
* Fix: Creation of dynamic property is deprecated PHP notices

= 1.3.5 - 2024-01-17 =

* Update: Changelog consistency
* Update: Freemius SDK

= 1.3.4 - 2023-12-15 =

* Update: Code consistency
* Update: Development assets
* Update: Screen sizes typo in readme.txt
* Update: Freemius SDK
* Update: WordPress requires at least 6.3.0
* Update: WordPress tested up to 6.4.2

= 1.3.3 - 2023-09-19 =

* Update: Freemius SDK
* Update: WordPress tested up to 6.3.1

= 1.3.2 - 2023-08-04 =

* Update: JS console usage now includes PHP prefix
* Update: Development assets
* Update: PHP requires at least 7.4.0
* Update: WordPress requires at least 6.1.0

= 1.3.1 - 2023-07-06 =

* Update: Freemius SDK

= 1.3.0 - 2023-06-24 =

* Update: Memory usage temporary placeholder HTML now includes the memory usage data in a data attribute rather than within the div to ensure the memory usage information is not deemed page content by search engines
* Update: WordPress tested up to 6.2.2
* Fix: Log dates/times not consistent with WordPress time zone setting
* Fix: Box shadow on boxes in PHP and WordPress configuration section not consistent with WordPress dashboard styling
* Fix: Links to WordPress.org increasing memory limit documentation require update due to changes in WordPress.org URLs

= 1.2.1 - 2022-12-23 =

* Update: Freemius SDK
* Update: WordPress tested up to 6.1.1
* Removed: Leave review nag

= 1.2.0 - 2022-10-22 =

* Update: JS assets now enqueued in footer
* Update: Code refactoring
* Update: Freemius SDK
* Update: PHP requires at least 7.0.0
* Update: WordPress requires at least 5.7.0
* Update: WordPress tested up to 6.0.3

= 1.1.1 - 2022-04-23 =

* Update: Code refactoring
* Update: WordPress tested up to 5.9.3

= 1.1.0 - 2022-03-25 =

* Note: This version includes several changes to asset enqueues it is recommended you clear all caches after updating to ensure all assets are reloaded
* Add: Minified CSS/JS assets created and enqueued
* Add: Translation function
* Update: CSS assets now SCSS
* Update: Text casing on some elements to make consistent with similar content in WordPress dashboard
* Fix: Translations may not load due to load_plugin_textdomain not hooked on init

= 1.0.0 - 2022-03-12 =

* New: Initial release