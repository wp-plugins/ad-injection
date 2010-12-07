=== Ad Injection ===
Contributors: reviewmylife
Donate link: http://www.reviewmylife.co.uk/blog/2010/12/06/ad-injection-plugin-wordpress/
Tags: ad injection, advert injection, advert, ad, injection, adsense, advertising, affiliate, inject, injection, insert, monetize, monetise, banner, Amazon, ClickBank, TradeDoubler, Google, adBrite, post, WordPress, automatically, plugin, free
Requires at least: 3.0.0
Tested up to: 3.0.2
Stable tag: 0.8.4

Injects any kind of advert into existing WordPress posts. Can restrict who sees the ads by post age, visitor referrer, IP address. Cache compatible.

== Description ==

It injects any kind of advert (e.g. Google AdSense, Amazon Associates, ClickBank, TradeDoubler, etc) into the existing content of your WordPress posts and pages. You can control the number of adverts based on the post length, and it can restrict who sees adverts by post age, visitor referrer and IP address. And the dynamic restrictions (by IP and referrer) work with WP Super Cache!

= Automatic advert injection =

The ads can be injected into existing posts without requiring any modification of the post. The injection can be done randomly between paragraphs, and there is an option to always inject the first advert after the first paragraph. Two separate adverts can be defined for the top and bottom of the content. Randomly positioning the adverts helps to reduce 'ad blindness'.

= Ad quantity by post length =

The number of adverts can be set based on the length of the post. It is a good idea for longer posts to have more adverts than shorter posts for example. Adverts can also be turned off for very short posts.

= Search engines only =

You can specify that ads should only be shown to search engine visitors (or from any other referring websites) so that your regular visitors (who are unlikely to click your ads) get a better experience of your site. You can define which search engines or any other referring sites see your adverts. A visitor who enters the site by a search engine will see ads for the next hour.

= Ads on old posts only =

Adverts can be restricted to posts that are more than a defined numbers of days old. This prevents your regular visitors from having to see your ads.

= Block ads from IP addresses =

IP addresses of people who shouldn't see your ads can be defined. These could be the IP addresses of your friends, family, or even yourself.

= Not tied to any ad provider =

The advert code can be copied and pasted directly from your ad provider (Google AdSense, adBrite, ClickBank, etc) which will help you to comply with any terms of service (TOS) that state their ad code may not be modified. 

= Flexible ad positioning =

Easy positioning options are provided for left, right, center, float left, and float right. Extra spacing can be set above and below the ad. Or if that isn't flexible enough, you can write your own positioning code using HTML and CSS.

= Works with WP Super Cache =

The dynamic features that require code to be executed for each page view (i.e. search engine visitors only, and ad blocking based on IP address) work with WP Super Cache! This plugin will automatically use the dynamic mfunc tag to ensure that the dynamic ad features still work when caching is on. Displaying the adverts (even with the dynamic restrictions) whilst caching with WP Super Cache requires no MySQL database access. Note: WP Super Cache must be configured in 'Legacy' mode for the dynamic features to work.

= Inject PHP and JavaScript =

As the plugin will inject whatever content you like into the page you can write your own ad rotation or a/b split testing code for the ads you inject. PHP code can be automatically executed, even when using WP Super Cache.

= Hide UI panels that you don't need =

If there are any panels on the admin screen that you don't need, you can click on the show/hide button to hide them until you need them.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the ad-injection folder to the '/wp-content/plugins/' directory. The plugin must be in a folder called 'ad-injection'. So the main plugin file will be at /wp-content/plugins/ad-injection/ad-injection.php
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure your ads. Carefully go through all the sections to setup your ad placements. 
4. Make sure you select the option to say which ad injection mode to use. You need to say whether you are using WP Super Cache (or compatible) or not. Dynamic features (referrer and IP ad filtering) will only work with either 1) WP Super Cache (or compatible) or 2) no caching plugin. 
5. Tick the box right at the top to enable your ads.
6. If you are using a caching plugin you may need to clear the cache to see your ads immediately.

== Frequently Asked Questions ==

= Why was this plugin created? =

I used to use the excellent Adsense Injection by Dax Herrera, but found I needed more features and flexibility. His plugin inspired this one.

= How is this plugin different to Adsense Injection by Dax Herrera? =

One a basic level it can do the same job as Dax's excellent Adsense Injection. If you want it can just inject Adsense like his plugin does. I used to use his plugin, but found that I wanted a lot more features. Here is a quick list of the extra features.

* Inject any type of advert from any ad provider.
* Restrict ad display by referrer (e.g. can restrict display to search engine visitors).
* Can prevent specific IP addresses from seeing adverts.
* Can define randomly positioned adverts, and adverts at the top and bottom of the posts.
* Vary number of adverts based on post length.
* You can inject raw JavaScript and PHP.
* The dynamic features (restricting ads by referrer and IP) work with WP Super Cache.
* Compatible with the <!--noadsense--> <!--adsensestart--> in-page tags from Adsense Injection to make migration easy.
* Compatible with in-page tags from Whydowork Adsense and Quick Adsense.
* Extra positioning options - for example you can force the first advert to be right after the first paragraph so that it will be 'above the fold'.

Thanks to Dax by the way for providing the inspiration for this plugin!

= Do I need to have WP Super Cache installed? =

No! All the features of this plugin will work with no caching plugin installed. But if you do have WP Super Cache the dynamic features (enabling ads based on IP address and referrer) will still work. And your blog will run a lot faster than with no caching plugin. Usually a caching plugin would prevent dynamic plugin features from working. Just make sure you have WP Super Cache configured in 'Legacy' mode and that you choose the option on Ad Injection to say that you are using WP Super Cache.

= Will the dynamic features work with other caching plugins? =

Potentially - if they are compatible with WP Super Cache's mfunc tags. I'm guessing they will work with the original WP Cache as I think the mfunc tags in WP Super Cache were inherited from here. However I haven't yet tested this. W3 Total Cache does have support for some kind of mfunc tags, but I haven't tested to see if they are compatible with the WP Super Cache ones. Do let me know!

= What if I am using an incompatible caching plugin? =

Don't worry - everything will still work except for:

1. Filtering ads based on the IP address of the visitor.
2. Filtering the ads based on the HTTP referrer of the visitor.

If you aren't interested in these features then it doesn't matter! Just make sure you tick the box to say that you to use 'Direct static ad insertion' on the Ad Injection settings screen.

= Are there any known plugin conflicts? =

If you use WP Minify and WP Super Cache in combination with this plugin, you'll need to turn off the HTML minification in WP Minify. This is because HTML minification strips out the mfunc tags that Ad Injection uses. You can leave the CSS and JavaScript minification on if you already use them.

= Some technical details =

* Plugin stores all its settings in a single option (adinj_options).
* Uninstall support is provided to delete this option if you uninstall the plugin.
* Admin code is separated into a separate file so it is not loaded when your visitors view your pages.
* When used with WP Super Cache the plugin loads its dynamic settings from a static PHP file so no MySQL database queries are required.
* The ads are saved as text files into the plugin folder. The plugin will therefore need write access to its own folder.
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

If you are using WP Super Cache.

1. Have you enabled the WP Super Cache 'mfunc' mode? (in the Ad insertion mode and dynamic ad display restrictions pane)
2. Are your WP Super Cache settings correct? It must be in 'Legacy' mode.
3. If you are using WP Minify as well then turn off the HTML minification as this strips out the mfunc tags that Ad Injection uses to check if the adverts should be inserted.

If you are using WP Minify

1. Turn off the HTML minification mode if you are also using WP Super Cache. HTML minification strips out the mfunc tags that Ad Injection needs to inject its ads.
2. If you use the 'Place Minified JavaScript in footer' then try turning it off.

If you are getting errors when using the plugin check the following.

1. Is there an 'ads' directory in the plugin directory? The path will probably be: '/wp-content/plugins/ad-injection/ads/'. If not create the ads directory and make sure it is writeable by the plugin (chmod 0755 will do, chmod 0750 is better).
2. Are there text files in the ads directory? The ad code that you enter into the ad boxes should get saved in text files in the ads directory.
3. Has the config file been created? It should be at '/wp-content/plugins/ad-injection/ad-injection-config.php'. If not make sure the '/wp-content/plugins/ad-injection/' directory is writeable (chmod 0750 is best, chmod 0755 will do).

If you do get any errors please use the 'Report a bug or give feedback' link on the plugin to send me the error details. If things go so badly wrong that you can't even get to the settings page please send me an email via [this contact form](http://www.reviewmylife.co.uk/blog/contact-us/ "contact form").

For more information visit [reviewmylife](http://www.reviewmylife.co.uk/blog/2010/12/06/ad-injection-plugin-wordpress/ "reviewmylife blog").

== Screenshots ==

1. Easy to use interface which allows you to copy and paste your ad code directly from your ad provider. Options are provided to control when and where your ads appear.
2. The ads are automatically injected into the pages of your blog.
3. Can choose to show the ads only to search engine visitors, or define IP addresses that ads aren't shown to.

== Changelog ==

= 0.8.4 =
* Fix deletion of ad code and config file that happens during automatic update.

= 0.8.3 =
* First public release

== Upgrade Notice ==

= 0.8.4 =
If you have already configured your ad code then this update will delete the ads. Sorry - this update fixes that bug. Before upgrading please save your ads. All other settings will be carried over.

= 0.8.3 =
First public release.

