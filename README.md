[WibWabWeb](http://wibwabweb.com) was a website builder service created by [ArrowQuick Solutions](http://arrowquick.com). It is based on WordPress multisite (v3.3.1 at its final incarnation).

It is no longer maintained, but is available here if you want to borrow ideas or fork it. ArrowQuick reserves the "WibWabWeb" name and trademarks.

## Development Process

The general process for making changes to the WibWabWeb software is to work with it on your localhost.

1. Checkout a working copy on your localhost.
2. Run the sandbox tool: `/path/to/www/wibwabweb-com/tools/sandbox` (You'll want to adjust your `~/.wibwabweb` file with your local configuration.)
3. Make your desired changes to the working copy.
4. Run the tests: `/path/to/www/wibwabweb-com/tools/test`
5. Commit the changes.
6. Turn off "sandbox" to put Apache/DNS back to normal: `/path/to/www/wibwabweb-com/tools/sandbox off`

## Blogs/Sites

WibWabWeb uses the standard multi-blog setup of Wordpress. The first blog (#1) is the main marketing/product site.

## Files

A `_sql` folder is included for the database schema.

The `wp-includes/aq-defs.php` file contains common constants you may want to reference.
Most non-WP changes are in the files in `wp-content/mu-plugins`.

The `.htaccess` file contains some server-specific configuration.

### Customized WP Files

In cases where WordPress does not provide hooks for modular changes, the original WP files had to be modified. These changes are commented with the "@AQ" custom code signal:

* html/wp-activate.php
* html/wp-admin/includes/dashboard.php
* html/wp-admin/includes/export.php
* html/wp-admin/options-discussion.php
* html/wp-admin/options-general.php
* html/wp-admin/options-reading.php
* html/wp-admin/options-writing.php
* html/wp-config.php
* html/wp-content/mu-plugins/cets_theme_info.php
* html/wp-content/mu-plugins/cets_plugin_stats.php
* html/wp-content/mu-plugins/wp-admin-menu-classes.php
* html/wp-content/plugins/google-maps-embed/cets_EmbedGmaps.php
* html/wp-content/plugins/stats/stats.php
* html/wp-content/plugins/wordpress-mu-domain-mapping/domain_mapping.php
* html/wp-content/plugins/wp-recaptcha/recaptcha.php
* html/wp-content/themes/wibwabweb/sidebar.php
* html/wp-content/themes/wibwabweb/js/jquery-equalheights.js
* html/wp-content/themes/amazing-grace/footer.php
* html/wp-content/themes/amazing-grace/index.php
* html/wp-content/themes/amazing-grace/header.php
* html/wp-content/themes/greyzed/footer.php
* html/wp-content/themes/greyzed/sidebar.php
* html/wp-content/themes/greyzed/header.php
* html/wp-content/themes/mystique/(various -- search for comments)
* html/wp-content/themes/snowblind/(various -- search for comments)
* html/wp-content/themes/vermillon/footer.php
* html/wp-includes/js/tinymce/plugins/spellchecker/classes/GoogleSpell.php
* html/wp-login.php
* html/wp-signup.php

New files have also been added to extend WP and add WibWabWeb-specific functionality:

* html/wp-content/plugins/display-plans.php
* html/wp-content/mu-plugins/*
* html/wp-admin/newsletters.php
* html/wp-admin/domains.php
* html/wp-admin/advertising.php
* html/wp-admin/account.php
* html/wp-admin/support.php
* html/wp-admin/marketing.php
* html/wp-admin/stats.php
* html/wp-admin/email.php


### Configuration

_Note: The sandbox tool automatically makes the necessary changes to run the WibWabWeb site on your localhost. These configuration changes are only necessary if you want to run the site as http://127.0.0.1._

For development and testing on your machine, you may have to make some changes to the configuration.

* Adjust the RewriteBase path in `.htaccess`.
* Adjust the database configuration and DOMAIN_CURRENT_SITE in `wp-config.php`.
* Adjust the "siteurl" and "home" keys in the "wp_X_options" database tables, where X is the blog ID. (You only need to change "wp_1_options"; after that you can login as admin and modify the other blogs through the control panel.)
* Adjust the values in the "wp_blogs" and "wp_site" tables for your test environment.

**Note:** The error message "Error establishing database connection" means something completely different in Wordpress! Make sure the "wp_blogs" and "wp_site" tables are correct.

## Writing Code

Changes to the WP files should be kept as limited as possible.

* The `wp-content` folder (plugins and themes) is the safest place for changes. WP allows for [wp-content/mu-plugins](http://codex.wordpress.org/Must_Use_Plugins) for functionality changes (see below).
* For 3rd-party libraries, you can include them in the appropriate folder. (For example, `wp-includes/js` for Javascript libraries.)
* If WP doesn't provide a pluggable method for a change, then you can modify the WP code inline. Be sure to add a comment about the change, with your name.
* As with any project, create a branch for any major feature or multi-commit change.

Guidelines when adding code:

* Incorporate new code into a class (if object-oriented) or a namespace (such as `ArrowQuick\Dashboard`).
* Every file should have stanza at the top with the copyright and licensing info.

Also check out these resources:

* [10 Most Common Mistakes in Wordpress Plugins](http://planetozh.com/blog/2009/09/top-10-most-common-coding-mistakes-in-wordpress-plugins)
* [Wordpress Coding Standards](http://codex.wordpress.org/WordPress_Coding_Standards)
* [Writing a Plugin](http://codex.wordpress.org/Writing_a_Plugin)

## Wording Changes

You can change the wording used in `__()` and `_e()` functions using the special "aq-wibwabweb" language (set as the default language in `wp-config.php`). This ["translation"](http://codex.wordpress.org/Translating_WordPress) consists of the default English text with our wording changes.

The language files are in `html/wp-content/languages`. The `.po` file is the file to use when editing; the `.mo` file is the compiled version. To make changes, you will need to use a [translation tool](http://codex.wordpress.org/Translating_WordPress#Translation_Tools). (I use Lokalize myself -- poedit is broken for plurals.)

You can get the original (POT) files from [the WP SVN repository](http://svn.automattic.com/wordpress-i18n/pot/tags/). To merge the latest POT into your language file using Lokalize:

1. Put the POT files in the `html/wp-content/languages` folder. Rename them to have the same name as the corresponding PO file (but with a POT extension).
2. Open the PO file in Lokalize.
3. Go to Project->Configure Project and set the "Template file folder" value to be blank.
4. Go to File->Update file from template.
5. Delete the POT files and review the changes to the POs.

## Debugging

You can add the WP_DEBUG constant to your local `wp-config.php` file (at the top) to show all PHP errors:

```php
define('WP_DEBUG',true);
```

Without this, all PHP errors will be logged into a `logfiles` folder in the site root (above `html`).

Note that Wordpress itself and many plugins don't have "error-less" code and may display PHP notices with this option turned on.

## Mu-Plugins

Files placed in the `wp-content/mu-plugins` directory are automatically included by WordPress. There's no need to activate them on a plugins page.

Because they are treated like actual plugins, you should give them a unique filename (that doesn't conflict with other WP files or plugins).

## Payments

Payments take place automatically through the [TrustCommerce](http://www.trustcommerce.com/) system. Credit card information is securely stored in their Citadel system.

* Billing always takes place on a monthly basis, starting on the date of activation.
* Address verification (AVS) takes place for all billing.
* Payment credentials are defined in the `aq-defs.php` file.

## Accounts

WordPress has been extended to track customers' billing accounts and domain names.

### Domains

WibWabWeb uses the [Domain Mapping plugin](http://wordpress.org/extend/plugins/wordpress-mu-domain-mapping) to map 3rd-party domains to the *.wibwabweb.com subdomains. It adds the `wp-content/sunrise.php` file for this purpose.

### Database

A single DB table, `accounts`, is used to track billing information. There should be 1 record in this table for each blog.

| Field | Notes |
|-------|-------|
| blog_id | equal to `wp_blogs.blog_id` |
| subscription_type | the type of billing plan |
| subscription_start | when the account started |
| subscription_end | when the site is set to expire (NULL for continuing subscriptions) |
| subscription_price | cost per subscription period |
| billing_id | billing ID of recurring transaction in TrustCommerce Citadel |
| credit_card | last 4 digits of credit card in TrustCommerce Citadel |
| credit_card_exp | expiration date of credit card in TrustCommerce Citadel (YYYY-MM-DD) |

### Code

The `/wp-admin/account.php` file contains the control panel page for subscription information. The `/wp-admin/domains.php` file contains the control panel page for domain info.

The file `wp-content/mu-plugins/accounts.php` contains the common routines.

## Announcements

WordPress Mu has been extended to display announcements at the top of the admin page upon login.

### Database

A single DB table, `announcements`, is used to track announcements. There should be 1 record in this table for each announcement.

| Field | Notes |
|-------|-------|
| id | the unique identifier of the announcement |
| html | the html of the announcement |
| publication_start | date the announcement should start displaying |
| publication_end | date the announcement should stop displaying |

### Code

The file `wp-content/mu-plugins/announcements.php` contains the function to get an array of the current announcements.

This code is called by an action registered in `wp-content/mu-plugins/admin-control-panel.php` and activated in `wp-admin/admin-header.php`.

## Themes programming

All themes, excluding readymade themes for a single site, should use the **Base** theme as a parent theme. This means that all new themes should be a child of Base, and readymade themes available for all sites should be converted to Base.

The Base theme provides the following:

* HTML5 and CSS reset based on [H5BP](http://html5boilerplate.com/)
* [Less framework](http://lessframework.com/) for CSS grids, including media queries
* Menu capability
* Widgets capability with default areas in sidebar and footer
* Theme options page for theme customization (logo upload, etc.)
* Code hooks at almost every level of the theme
* CSS `box-sizing` set to `border-box` for all elements for [easier layouts](http://paulirish.com/2012/box-sizing-border-box-ftw/)
* Microformats for posts, etc.
* "Full Width" (no sidebar) page template
* "Copyright Message" widget for page footers
* Various cleanup and extensions to default WP theme output

### Converting Themes to Base

#### Metadata (style.css)

Child themes need a "Template" line that references the "base" theme:

```css
/*
.
.
.
Template: base
.
.
.
*/
```

Any mentions "Wordpress" or "blogs" should be removed from the theme description. Other minor tweaks may be required to metadata to make it easier to read or less confusing.

#### Thumbnail

The theme thumbnail should be updated with a screenshot from the WibWabWeb test website.

#### Variable Elements

All variable elements of the theme (logo, colors, image montages) need to be customizable through an admin screen. Look at another theme such as the "Default" theme for how this is accomplished using [functions.php](http://codex.wordpress.org/Theme_Development#Theme_Functions_File).

#### Testing

* All themes should be tested in WibWabWeb, prior to launch, in a test site that includes content, subnavigation elements, etc.
* Themes should be tested against WP's theme standards using the [Theme-Check plugin](http://wordpress.org/extend/plugins/theme-check/).
* All 3rd-party themes should pass checks from the [Exploit Scanner](http://wordpress.org/extend/plugins/exploit-scanner/) and the [Theme Authenticity Checker](http://wordpress.org/extend/plugins/tac/).

#### Upgrading

If the theme was created by someone else, then we should add a comment and date if it has been modified for WibWabWeb.

Upgrading themes is trickier because of the customizations and modifications we make to the themes. Here's a way to update a modified theme:

1. Download the newest version of the theme. Extract it and name the folder something like `themename-new`.
2. Download the version of the theme that is currently being used. Extract it and name the folder `themename-old`.
    * You can find the version number of the current theme in the control panel or in the CSS file.
    * You can download old versions from the [Wordpress directory](http://wordpress.org/extend/themes/). Check out the "Theme SVN" link to get the URL, then export using SVN: `svn export http://themes.svn.wordpress.org/themename/x.x.x  themename-old`.
3. Apply the changes to your working copy: `diff -urN /path/to/themename-old /path/to/themename-new | patch -pX -d /path/to/workingcopy/wp-content/themes/themename`, where `X` is the number of levels in your `/path/to/themename-old` plus 1 (e.g., `/home/user/Desktop/themename-old` would be 5). **Note: This doesn't seem to work for added/deleted files and binary files. Maybe remove the `-N` option from diff?**
    1. Sometimes our file might be too different from the original. If it asks "Assume -R? [n]", say no. Then answer yes ("y") to "Apply anyway? [n]".
    2. If there were any failures, you'll have to apply the changes yourself. Take a look at the `.rej` files and the theme distribution for how to merge it. Then you can compare the file from the distribution to your working copy to make sure you got all the changes.
    3. Delete the `.orig` and `.rej` files when finished: `find . -name '*.orig' -o -name '*.rej' | xargs rm`
4. Commit the changes.

#### Additional Resources

  * [Theme Development](http://codex.wordpress.org/Theme_Development) on Wordpress Codex
  * [WordPress Theme Tutorial](http://themeshaper.com/wordpress-themes-templates-tutorial/#toc)
  * The Wordpress Codex has a good [theme development checklist](http://codex.wordpress.org/Theme_Development_Checklist) of important things to check.
