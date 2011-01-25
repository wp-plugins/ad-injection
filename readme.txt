=== Ad Injection ===
Contributors: reviewmylife
Donate link: http://www.reviewmylife.co.uk/blog/2010/12/06/ad-injection-plugin-wordpress/
Tags: ad injection, adsense, advert injection, advert, ad, injection, advertising, affiliate, inject, injection, insert, widget, widgets, sidebar, monetize, monetise, banner, Amazon, ClickBank, TradeDoubler, Google, adBrite, post, WordPress, automatically, plugin, Adsense Injection, free, blog, ad rotation, A:B testing, split testing, WP Super Cache, W3 Total Cache, WP Cache
Requires at least: 2.8.6
Tested up to: 3.1
Stable tag: 0.9.6.4

Injects any adverts (e.g. AdSense) into the WordPress posts or widget area. Restrict who sees ads by post length/age/referrer or IP. Cache compatible.

== Description ==

Ad Injection injects any kind of advert (e.g. Google AdSense, Amazon Associates, ClickBank, TradeDoubler, etc) into the existing content of your WordPress posts and pages. You can control the number of adverts based on the post length, and it can restrict who sees adverts by post age, visitor referrer and IP address. Adverts can be configured in the post (random, top, and bottom positions) or in any widget/sidebar area. There's support for A:B split testing / ad rotation. And the dynamic restrictions (by IP and referrer) work with WP Super Cache, W3 Total Cache and WP Cache.

**New Features 0.9.6.x**

* Ad rotation / A:B split testing support for random, top, bottom and widget/sidebar adverts.
* Alternate content which can be defined for users who are dynamically blocked (by IP or referrer) from seeing adverts.
* Choose which paragraph to start the random ads via the UI.
* Dynamic features will work with W3 Total Cache and WP Cache as well as the previously suppoted WP Super Cache.

= Automatic advert injection =

The ads can be injected into existing posts without requiring any modification of the post. The injection can be done randomly between paragraphs, and there is an option to always inject the first advert at a specified paragraph (e.g. the first or second). Randomly positioning the adverts helps to reduce 'ad blindness'. Two additional adverts can be defined for the top and bottom of the content. Widget adverts can be defined as well.

= Widget support =

Widgets can be added to your sidebars, or other widget areas on any pages. The same ad display restrictions that you setup for your other ads will also apply to the widgets.

= Ad rotation / split testing =

You can define multiple adverts for the same ad space which are rotated according to the ratios you define. Works with random, top, bottom and sidget/sidebar ads.

= Ad quantity by post length =

The number of adverts can be set based on the length of the post. It is a good idea for longer posts to have more adverts than shorter posts for example. Adverts can also be turned off for very short posts.

= Search engines only mode =

You can specify that ads should only be shown to search engine visitors (or from any other referring websites) so that your regular visitors (who are unlikely to click your ads) get a better experience of your site. You can define which search engines or any other referring sites see your adverts. A visitor who enters the site by a search engine will see ads for the next hour.

= Ads on old posts only =

Adverts can be restricted to posts that are more than a defined numbers of days old. This prevents your regular visitors from having to see your ads.

= Category, tag and post type filters =

You can configure the adverts to only appear on specific categories, tags, or post types, or block the adverts from specific categories, tags or post types.

= Block ads from IP addresses =

IP addresses of people who shouldn't see your ads can be defined. These could be the IP addresses of your friends, family, or even yourself.

= Alternate content =

This is content that is displayed when ads are blocked for the user. Ads can only be blocked for specific users if you use one of the dynamic modes (mfunc or direct_dynamic). You could use this alternate content to show other content, some kind of layout filler, or even a different type of ad. I've added support for rotation of alternate content as well.

= Not tied to any ad provider =

The advert code can be copied and pasted directly from your ad provider (Google AdSense, adBrite, ClickBank, etc) which will help you to comply with any terms of service (TOS) that state their ad code may not be modified. 

= Flexible ad positioning =

Easy positioning options are provided for left, right, center, float left, and float right (or a random variant of these). Extra spacing can be set above and below the ad using the CSS margin and padding boxes. Or if that isn't flexible enough, you can write your own positioning code using HTML and CSS. And you can select which paragraph random ads should start from.

= Works with WP Super Cache, W3 Total Cache and WP Cache =

The dynamic features that require code to be executed for each page view (i.e. ad rotation, search engine visitors only, and ad blocking based on IP address) work with WP Super Cache, W3 Total Cache and WP Cache.

This plugin will automatically use the dynamic mfunc tag to ensure that the dynamic ad features still work when caching is on. 

If you use WP Super Cache in mod_rewrite mode displaying the adverts (even with the dynamic restrictions) whilst caching requires no MySQL database access. For W3 Total Cache and WP Cache Ad Injection will not require any extra MySQL database access for cached pages other than what these plugin already use.

= Inject PHP and JavaScript =

As the plugin will inject whatever content you like into the page you can write your own ad rotation or a/b split testing code for the ads you inject. PHP code can be automatically executed, even when using WP Super Cache.

= Hide UI panels that you don't need =

If there are any panels on the admin screen that you don't need, you can click on the show/hide button to hide them until you need them.

For more information visit [reviewmylife](http://www.reviewmylife.co.uk/blog/2010/12/06/ad-injection-plugin-wordpress/ "reviewmylife blog").

= Actively being developed =

As of January 2011 this plugin is being actively developed and maintained. Some major new features are planned for January / February 2011. I'm very open to implementing your feature requests.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the ad-injection folder to the '/wp-content/plugins/' directory (or just use the WordPress plugin installer to do it for you). The plugin must be in a folder called 'ad-injection'. So the main plugin file will be at /wp-content/plugins/ad-injection/ad-injection.php
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure your ads. Carefully go through all the sections to setup your ad placements. 
4. Make sure you select the option to say which ad injection mode to use. Dynamic features (ad rotation, and referrer/IP ad filtering) will only work with either 1) WP Super Cache/W3 Total Cache/WP Cache or 2) no caching plugin. 
5. Tick the box right at the top to enable your ads.
6. If you are using a caching plugin you may need to clear the cache to see your ads immediately.

**Recommended cache plugin settings**

* WP Super Cache - 0.9.9.8+ using mod_rewrite mode.
* W3 Total Cache - Page Cache: 'Disk (basic)' mode.
* WP Cache - Just turn the caching on.

Note: If you use a version of WP Super Cache prior to 0.9.9.8  it must be configured in 'Legacy' mode for the dynamic features to work. If you use WP Super Cache 0.9.9.8 or above you can use any of the caching modes (mod_rewrite and PHP are faster than legacy).

= How to uninstall =

You can uninstall by deactivating the plugin and deleting from the WordPress plugins control panel.

If you have been using mfunc mode with a caching plugin then you *must* also clear the cache afterwards, otherwise you'll get errors saying the Ad Injection includes can't be found. 

== Frequently Asked Questions ==

= Why was this plugin created? =

I used to use the excellent Adsense Injection by Dax Herrera, but found I needed more features and flexibility. His plugin inspired this one.

= How is this plugin different to Adsense Injection by Dax Herrera? =

One a basic level it can do the same job as Dax's excellent Adsense Injection. If you want it can just inject Adsense like his plugin does. I used to use his plugin, but found that I wanted a lot more features. Here is a quick list of the extra features.

* Inject any type of advert from any ad provider.
* Restrict ad display by referrer (e.g. can restrict display to search engine visitors).
* Can prevent specific IP addresses from seeing adverts.
* Can define randomly positioned adverts, and adverts at the top and bottom of the posts.
* Add adverts to the widget area.
* Ad rotation / split testing.
* Restrict adverts by category and tag.
* Vary number of adverts based on post length.
* You can inject raw JavaScript and PHP.
* The dynamic features (ad rotation, restricting ads by referrer and IP) work with WP Super Cache, W3 Total Cache and WP Cache.
* Define alternate content for users who are dynamically restricted from seeing ads.
* Compatible with the <!--noadsense--> <!--adsensestart--> in-page tags from Adsense Injection to make migration easy.
* Compatible with in-page tags from Whydowork Adsense and Quick Adsense.
* Extra positioning options - for example you can force the first advert to be right after the first paragraph so that it will be 'above the fold'.

Thanks to Dax by the way for providing the inspiration for this plugin!

= Does this plugin 'take' a percentage of my ad earnings? =

No! Absolutely not. Some ad plugins replace your publisher ID with their own for a certain percentage of adverts. Ad Injection does NOT do this. All your earnings are your own. Ad Injection makes no modifications to your ad code. What you paste into the ad boxes is what is injected into your pages.

= Is using this plugin allowed for Google AdSense? =

As far as I can tell using this plugin should be legal for AdSense **as long as you** make sure the ad quantities/placements comply with their TOS. However it is up to you to make sure that your website complies.

Ad Injection is designed as a generic plugin for injecting all types of adverts. It will not specifically check that the defined ad quantities or positions are in compliance of the AdSense TOS for you. For example Ad Injection will allow you to inject more ads than Google allows if you configure it to do so. 

Be careful if you use the float left/right positioning options. These options could cause your AdSense adverts to appear under other elements of your page if you have also set a float value for them (e.g. floating images, and adverts together could be problematic). However the new 'clear' option should allow you to make sure this doesn't happen. 

The best advice for any advert plugin is to manually check that you are happy with the advert quantities and positioning it produces, to ensure that you comply with the TOS for your ad program.

You use this plugin at your own risk.

= Do you have any testing recommendations? =

For testing your ad settings I'd recommend that you first disable any caching plugin, and then set Ad Injection to test mode.

If you are unsure as to why ads are appearing (or aren't) enable debug mode from the UI and look for the 'ADINJ DEBUG' tags in the HTML source of your webpage. 

When you are happy with the ad quantities / positions you can disable debug mode, re-enable your caching plugin, and set the Ad Injection mode to 'On'.

If you are testing the search engine referrer settings be aware that Ad Injection sets a one hour cookie when you visit via a site with a matching referrer. This means that after you have visited the site via the matching referrer the adverts will keep showing for the next hour. Clear your cookies to reset the behaviour. The Firefox 'Cookie Monster' plugin is very useful if you want to check the status of the cookie. Look for the 'adinj' cookie. Instead of clearing all your cookies you can just delete this one.

Using a second browser in 'privacy mode' is also a good way of testing your site with a clean slate. A browser like Google Chrome will allow you to test your site with no cookies definied if you start a new private browsing session.

= Do I need to have WP Super Cache (or anther caching plugin) installed? =

No! All the features of this plugin will work with no caching plugin installed. But if you do have WP Super Cache the dynamic features (ad rotation and enabling ads based on IP address and referrer) will still work. And your blog will run a lot faster than with no caching plugin. Usually a caching plugin would prevent dynamic plugin features from working. Just make sure you choose the mfunc dynamic insertion mode in the Ad Injection settings screen.

= Will the dynamic features work with other caching plugins? =

The dynamic features will work with any caching program that supports the mfunc tag. At the moment that is WP Super Cache, W3 Total Cache, and WP Cache.

= Which caching plugin is best? =

Both WP Super Cache and W3 Total Cache are likely to be faster than WP Cache.

If you aren't using the dynamic features (and you are using direct_static mode) then the only way to find out which is best between WP Super Cache and W3 Total Cache is to measure the performance yourself, as the speed depends on many factors that will be unique to your web server and web site.

If you are using dynamic features (i.e. with mfunc mode) then WP Super Cache (in mod_rewrite mode) and W3 Total Cache (in Page Cache: Disk (basic) mode) are likely to offer similar performance as they both return the dynamic files via PHP. 

Note that W3 Total Cache will not cache pages with mfunc tags if you use the Page Cache: Disk (enhanced) mode.

The speed of your website does depend on more factors than just page serve time so other features that the caching plugins offer (such as CDN and minification support) may swing the advantage either way.

WP Hyper Cache does not support mfunc tags so only use it if you don't want to use any of the dynamic features. If using WP Hyper Cache set the Ad Injection mode to 'direct_static'.

For reference: dynamic features are ad rotation, and blocking ad views by IP and referrer.

= Can I just have adverts on the home page? =

i.e. adverts on the home page, but not on single posts or pages.

Yes you can do this, there are two ways.

1. In the 'Single posts and pages' setting set the number of injected ads to 0. Then in the 'Home page' settings set the number of ads to whatever you want. 
2. Alternatively use the global exclude options at the top to exclude ads from all page types except the home page.

= How do I stop adverts from appearing on my contacts form page? =

To stop ads appearing on the contact page (or any other post/page) you have many of options. Here are three to choose from:

1. Add (copy and paste) <!--NoAds--> into the content of the post/page with the contact form. Just edit the contact page and paste this in - it will be invisible to the reader.
2. Add a disable_adverts custom field to the post/page containing the contact form and set the value to 1 (the number one as a single digit).
3. Add a tag to the page to mean that it shouldn't have adverts, and then add that tag to the tag filtering condition in the global settings area of the plugin configuration page.

= My adverts are overlapping with other page elements (e.g. images) =

You can try defining the 'clear' display setting so that multiple floated parts of your page do not overlap.

If you always have a floated image at the top of the page you can set which paragraph the first random advert will start from. If you needed finer control over where the random adverts appear you can use the <!--adstart--> and <!--adend--> tags in the page.

= I have configured four Google AdSense ad units but only three are showing. Why? =

Google's AdSense TOS only allow allow three ad units, and three link units per page. If you have for example tried to insert four ad units on your page then Google will (probably) disable the forth one automatically. Read Google's AdSense [program policies](https://www.google.com/adsense/support/bin/answer.py?hl=en&answer=48182 "AdSense program policies") for more info.

= What if I am using an incompatible caching plugin? =

Don't worry - everything will still work except for:

1. Filtering ads based on the IP address of the visitor.
2. Filtering the ads based on the HTTP referrer of the visitor.

If you aren't interested in these features then it doesn't matter! Just make sure you tick the box to say that you to use 'Direct static ad insertion' on the Ad Injection settings screen.

= Are there any known plugin conflicts? =

**WP Minify**

Problem: No ads appear when using mfunc mode.

If you use WP Minify and a caching plugin in combination with Ad Injection, you'll need to turn off the HTML minification in WP Minify. This is because HTML minification strips out the mfunc tags that Ad Injection uses. You can leave the CSS and JavaScript minification on if you already use them.

**FeedWordPress**

Problem: Random, top and bottom ads don't appear on syndicated posts.

By default FeedWordPress prevents the syndicated post contents from being passed to 'the_content' hook which is where the random, top and bottom ads are added. There's an easy fix: 

From the FeedWordPress settings page go to 'Posts & Links' and then in the 'Formatting' section set 'Formatting filters' to 'Expose syndicated posts to formatting filters'.

= Some technical details =

* Plugin stores all its settings in a single option (adinj_options).
* Uninstall support is provided to delete this option if you uninstall the plugin.
* Admin code is separated into a separate file so it is not loaded when your visitors view your pages.
* When used with a compatible caching plugin Ad Injection loads its dynamic settings from a static PHP file, and the ads from disk so no extra MySQL database queries are required.
* When mfunc mode is used the ads are saved as text files into the plugin folder. The plugin will therefore need write access to the plugins folder.
* The JavaScript for setting the referrer cookie is inserted using wp_enqueue_scripts.
* If there is anything I can do better please let me know - this is my first plugin so I still have a lot to learn!

== Troubleshooting ==

Here are some things to check if the ads are not appearing, or are appearing when you think they shouldn't.

1. Have you clicked the box to enable your ads?
2. Would the options you have selected allow the ads to appear on that page?
3. Have you cleared your cache (if you are using a caching plugin) to make sure that the page has the ads injected into it?
4. If you still aren't sure why the ads aren't there (or why they are), click 'Enable debug mode'. Make sure the page gets regenerated (either by reloading, or by clearing the cache and reloading). The search the source code of the page (the HTML) for 'ADINJ DEBUG' tags. This will give you information about the decisions that the plugin made.
5. Have you selected the correct insertion mode in the 'Ad insertion mode' section?
6. The plugin inserts adverts after the closing HTML paragraph tag </p>. If the ads aren't appearing where you expect, check where your </p> tags are.

= If you are using a caching plugin =

1. Have you enabled Ad Injection's 'mfunc' mode? (in the Ad insertion mode and dynamic ad display restrictions pane)
2. If you use a version of WP Super Cache prior to 0.9.9.8  it must be configured in 'Legacy' mode for the dynamic features to work. If you use WP Super Cache 0.9.9.8 or above you can use any of the caching modes (mod_rewrite and PHP are faster than legacy).
3. If you are using WP Minify as well then turn off the HTML minification as this strips out the mfunc tags that Ad Injection uses to check if the adverts should be inserted.

= If you are using WP Minify =

1. Turn off the HTML minification mode if you are also using a caching plugin. HTML minification strips out the mfunc tags that Ad Injection needs to inject its ads.
2. If you use the 'Place Minified JavaScript in footer' then try turning it off.

= If you are getting errors when using mfunc mode check the following =

1. Are there ad data directories in the plugin directory? The path will be: 

'/wp-content/plugins/ad-injection-data/.

If not create this directory and make sure it is writeable by the plugin (chmod 0755 will do, chmod 0750 is better).

2. Are there text files in the ads directories? The ad code that you enter into the ad boxes should get saved in text files in the ads directory.

3. Has the config file been created? It should be at '/wp-content/ad-injection-config.php'. If not make sure the '/wp-content/' directory is writeable (chmod 0750 is best, chmod 0755 will do).

= Errors after uninstalling the plugin =

If you get an error like:

'Warning: include_once(/homepages/xx/dxxxx/htdocs/blog/wp-content/plugins/ad-injection/adshow.php) [function.include-once]: failed to open stream: No such file or directory in /homepages/xx/dxxxx/htdocs/blog/ on line xx'

Then you need to delete your cache. The references to the Ad Injection includes are still in your cached files, deleting the cache will get rid of them.

= Reporting bugs =

If you do get any errors please use the 'Report a bug or give feedback' link on the plugin to send me the error details. If things go so badly wrong that you can't even get to the settings page please send me an email via [this contact form](http://www.reviewmylife.co.uk/contact-us/ "contact form").

== Screenshots ==

1. Easy to use interface which allows you to copy and paste your ad code directly from your ad provider. Options are provided to control when and where your ads appear.
2. The ads are automatically injected into the pages of your blog.
3. Can choose to show the ads only to search engine visitors, or define IP addresses that ads aren't shown to.

== Changelog ==

= 0.9.6.4 =
* Can choose between page lengths based on viewable characters or all characters (includes HTML markup).
* Fixes for widget padding options.
* Enable alt content for random ads.

= 0.9.6.3 =
* Option to enable/disable front page ads in case your front and home pages are different.

= 0.9.6.2 =
* Support for W3 Total Cache and WP Cache (as well as the already supported WP Super Cache).

= 0.9.6.1 =
* Ad rotation / A:B split testing support for random, top, bottom and widget adverts.
* Alternate content which can be defined for users who are dynamically blocked (by IP or referrer) from seeing adverts.
* Choose which paragraph to start the random ads via the UI.

= 0.9.5.2 =
New CSS padding options for widgets. Fixes for CSS margin options.
Update docs for due to new mfunc support in WP Super Cache. If you are using mfunc mode and upgrade to the latest version of WP Super Cache (0.9.9.8) you can now use the faster mod_rewrite mode or PHP mode instead of legacy mode.

= 0.9.5.1 =
New CSS padding options for widgets. Fixes for CSS margin options.
Update docs for due to new mfunc support in WP Super Cache. If you are using mfunc mode and upgrade to the latest version of WP Super Cache (0.9.9.8) you can now use the faster mod_rewrite mode or PHP mode instead of legacy mode.

= 0.9.5 =
New option to add spacing above and below widgets.
New options for randomly aligning random/top/bottom ads.
Fixes for several bugs reported over Christmas.

= 0.9.4.6 =
Save options in admin_menu hook so that WordPress is correctly initialised when saving. Allows 'pluggable' include to be removed, which should fix 'Cannot redeclare get_userdatabylogin' conflict with vbbridge.

= 0.9.4.5 =
Fix problem with mfunc mode widgets on archive pages.

= 0.9.4.4 =
New display option for defining CSS clear as left, right or both.
Suppress file system warnings.
Tested on WordPress 2.8.6 - it works!

= 0.9.4.3 =
Only write to config file in mfunc mode.

= 0.9.4.2 =
Allow plugin to work with PHP4.
Increase allowed home page ads to 10.
Must always save widget ads to disk in case mode is changed to mfunc later on.

= 0.9.4.1 =
Fix: Remove file contents if ad is 0 length.

= 0.9.4 =
Global tag and category restrictions.
Smoother JQuery show/hide blocks (especially on IE)

= 0.9.3.4 =
Clean up old settings restore block.

= 0.9.3.3 =
Add a status box to make it easy to see what the main settings are. 

= 0.9.3.2 =
Add test mode, and further reduce unnecessary file access.

= 0.9.3.1 =
Fix chmod comparison problem.

= 0.9.3 =
Invalidate the options cache after saving.

= 0.9.2 =
If you are using mfunc mode and have added ad widgets please re-save them to regenerate the ad files.
Save ad files to a new directory so they don't need to be re-created after upgrade.

= 0.9.1 =
Fix dynamic checking for widgets.
Fix potential PHP error message with widgets.

= 0.9.0 =
Widget support.
Only write to the ad files if necessary.
Chrome display fixes.
More informative save messages.
Other fixes.

= 0.8.9 =
Prevent config file being lost by bulk automatic update.
Error messages from adshow.php are hidden in HTML now rather than being visible to everyone.

= 0.8.8 =
Try to make sure ads don't appear on archive pages, 404s or search results, in case theme is working in a non-standard way. Reduce dependency on files.

= 0.8.7 =
More fault tolerant mfunc support.

= 0.8.6 =
Fix problems relating to over strict chmod usage. 
Add save message. 
More informative warnings.
Update links to reviewmylife.

= 0.8.5 =
Fix 'Something badly wrong in num_rand_ads_to_insert' message that occurs on page types that I haven't taken account of.

= 0.8.4 =
* Fix deletion of ad code and config file that happens during automatic update.

= 0.8.3 =
* First public release

== Upgrade Notice ==

= 0.9.6.4 =
Fixes for widget padding and enabling of random alt content. If you have any problems with disappearing ads re-save them to make them appear again.

= 0.8.3 =
First public release.

