=== C9 Variables ===
Contributors: nitinvp
Donate link: https://cloudnineapps.com/products/wordpress-plugins/c9-variables
Tags: productivity, tools, content management, reusable content, modularize, modularization, easy maintain, simplify, cloudnineapps, cloud nine apps
Requires at least: 4.7
Tested up to: 4.9
Stable tag: 1.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Use variables to make smart reusable content. The basic plugin is fully functional and supports up to 10 variables. The Pro plugin supports unlimited variables and more productivity enhancements.

== Description ==

As a content author, you constantly try to keep the content on your site up-to-date. The challenge increases as many a times the same content is repeated at various places on your site. Add to that the content keeps changing over time making it more difficult to keep your site up-to-date. Here are some examples.

* Features of a product or service you offer (you may end up repeating these on the product page as well as blog posts)
* Links with promotion codes that change from time-to-time (again the links may spread across your site)
* Content promotion (such as, promoting content from development site to production site where you may need to update links to point to the production site)

**C9 Variables** helps in all these areas and many more. It facilitates authoring reusable content as variables. Then, you can refer to these variables from other content (such as, blog post or page) instead of repeating the content. **C9 Variables** will automatically show the latest content of variables when the post or page is loaded. If you need to make updates, simply update the variable in one place and the changes will be available automatically when the referring page or post is reloaded.

Here are some examples.

= Example#1: Variable for Product Features =

Lets say you want to create reusable content for managing your product features that you would like to show at multiple places on your site, such as, the product page, blog posts, etc.

* Create a variable using **C9 Variables**, say `ProductFeatures`, with the following content.
```html
<ul>
  <li>Create new content.</li>
  <li>Modify content.</li>
  <li>Search content.</li>
  <li>Delete content.</li>
</ul>
```
* Now you can refer to this variable from another post or page using the `c9-vars-insert` shortcode. For example, here is a blog post snippet referring to the above `ProductFeatures` variable.
```html
Welcome to the release of our latest product that offers the following key features.
[c9-vars-insert name="ProductFeatures"]
We are quite optimistic that this will help you tremendously in managing your site.
```
Notice how you did not have to repeat the entire list of features. Next time you add more to the feature list, you can simply update the `ProductFeatures` variable and all the content using this variable will get the latest feature list when reloaded. 

<a href="https://cloudnineapps.com/products/wordpress-plugins/c9-variables/c9-variables-demo/#product_features" target="_blank">See Demo</a>.

= Example#2: Variable for Managing Promotion Code =

Lets say you constantly run promotion on your site and you have links from various pages and posts on your site that should provide the latest promotion code. You want to ensure there are no obsolete promotion links. Here, you will create a variable to store the promotion code and use it from other content on your site. For the purpose of demonstration, lets use the promotion code value as `CLOUDNINE`.

* Create a variable, say `Promo Code`, with content as `CLOUDNINE`.
* Refer to this variable wherever you would like to access the promotion code. Example
```html
<a href='http://example.com/product?coupon=[c9-vars-insert name="Promo Code"]'>Click here</a> to claim your promotion code. 
```
Next time your promotion code changes, simply update the `Promo Code` variable and all references will be updated dynamically.

<a href="https://cloudnineapps.com/products/wordpress-plugins/c9-variables/c9-variables-demo/#promo_code" target="_blank">See Demo</a>.

= Example#3: Variable for Site =

Lets say you run a development site where you author your content first and then promote to production. This site contains links to other pages on your site. When you promote this content to the production site, you want to make sure the links now refer to the production site without having to do a search and replace or other manual and error prone tasks. **C9 Variables** makes this task simple.

* Create a variable for site, say `site`, with value as `example-dev.local` in your development site.
* Create the same variable `site` with value as `example.com` in your production site.
* Now, you can author links in your content hassle free using the `site` variable. Example
```html
<a href='http://[c9-vars-insert name="site"]/products/MyCoolProduct'>Click here</a> to visit the MyCoolProduct.
<a href='http://[c9-vars-insert name="site"]'>/blogs</a>
```
When this content is promoted to production, the links will be dynamically updated to use the production `site` value.

<a href="https://cloudnineapps.com/products/wordpress-plugins/c9-variables/c9-variables-demo/#site" target="_blank">See Demo</a>.

= Example#4: Nested Variables =

Yes, variables can be nested! That is, one variable can refer to another variable. In fact, a variable's content can use shortcodes from other plugins as well. Here's an example.

* Create a variable, say `Nested Content`, that refers to the above `ProductFeatures` variable with the following content.
```html
The following content is from the <strong>ProductFeatures</strong> variable.
[c9-vars-insert name=’productfeatures’]
```
* Now, wherever `Nested Content` variable is used, it will automatically substitute the content with the latest value for `ProductFeatures` as well.

<a href="https://cloudnineapps.com/products/wordpress-plugins/c9-variables/c9-variables-demo/#nested" target="_blank">See Demo</a>.

= Example#5: Variable for Magic Button! =

This is a common case of content management wherein the content is being actively developed in the development site for a particular release. The content is also published to production, but not made visible until the release date. This is accomplished by using a variable to act as a magic button!

* Create a variable for magic button, say `Magic Button`, that uses the HTML `div` tag and `display` attribute. You can also use a version based name, such as, `Magic Button V1.0`. To keep the content visible on the development site, set `Magic Button` variable value to the following.
```html
<div style="display: block;">
```
* To keep the content hidden on the production site, set `Magic Button` variable value to the following.
```html
<div style="display: none;">
```
* Author the content. Following is a sample content for demonstration purpose.
```html
[c9-vars-insert name=’magic-button’]
This content shows the features of the latest release.
</div>
```
* Push content to production without a worry that it'll be available to the users before the release date.
* On the release date, simply switch the `Magic Button` variable value in production to make the content visible.

<a href="https://cloudnineapps.com/products/wordpress-plugins/c9-variables/c9-variables-demo/#magic_button" target="_blank">See Demo</a>.

= Example#6: Variable for Content Promotion =

This is another common case in the documentation world and hence worth calling out. Lets say you are preparing documentation for the next release of your product and you are working in a __draft space__ that's only accessible to select customers and employees. You want the links to point to the `draft` space. Once the product is released, you want to the links to dynamically point to the `published` space.

* Create a variable for space, say `space`, with value as `draft`.
* Have your referring pages use the above variable when referring to the links. For example
```html
Restricted page
<a href='http://example.com/documentation?space=[c9-vars-insert name="space"]'>Documentation</a>
<a href='http://example.com/documentation/myproduct/space=[c9-vars-insert name="space"]'>My Product Documentation</a>
```
* Now, the select people with access to the above page will see the links that are pointing to the `draft` space.
* When you are ready for the release, you can simply update the `space` to `published` and make the above restricted page publicly accessible. And, all the content will be publicly available. Simple!

Keep building awesome reusable content!

== Installation ==

= C9 Variables Installation =

1. Use the WordPress **Plugins->Add New** action and search for **C9 Variables**. Click on **Install Now** to proceed with the installation. Alternately, upload `c9-variables-<version>.zip` using WordPress Admin **Plugins->Add New**.
2. Activate the plugin.
3. Optional: Tweak the 'Settings'. You can always do this later.

= C9 Variables Pro Installation =

1. <a href="https://cloudnineapps.com/products/wordpress-plugins/c9-variables-pro" target="_blank">Purchase or renew subscription</a>.
2. <a href="https://cloudnineapps.com/my-account/downloads" target="_blank">Download</a> the `c9-variables-pro-<version>.zip`. (See <a href="https://cloudnineapps.com/products/wordpress-plugins/c9-variables-pro/documentation/#purchase_license" target="_blank">detailed instructions</a>)
3. Upload the `c9-variables-pro-<version>.zip` using WordPress Admin **Plugins->Add New**.
4. Activate the plugin.
5. Specify the license key (available under **Account->My Licenses**) in the **Variables Pro->Settings** section. Here is the link for your convenience: <a href="https://cloudnineapps.com/my-licenses" target="_blank">https://cloudnineapps.com/my-licenses</a>
6. Optional: Tweak other 'Settings'. You can always do this later.

== Frequently Asked Questions ==

= Q. Why should I consider using variables to author my website content? =
As webmasters and entrepreneurs we often wear multiple hats and the more efficiently we can do things the better. Of course, without compromising the quality. Use of variables makes it easy to author content that you would want to reuse in multiple places or content that is subject to change (such as, when you promote site content from the development site to production). It also helps eliminate typical errors, such as, broken/obsolete links that can otherwise be difficult to manage. These are just some of the examples. When you start using **C9 Variables**, you may discover additional benefits.

= Q. How do I get started? =
Please see the <a href="https://cloudnineapps.com/products/wordpress-plugins/c9-variables/documentation" target="_blank">documentation</a> for details on how to get started.

= Q. Do you recommend any best practices for using variables? =
Yes, we do recommend and our detailed documentation has more details. But, here's a simple list for starters. Feel free to revise based on your needs.

* Use a variable title convention so that it's easy to organize variables. Example, all path variables end in `Path`.
* Use crisp and clear titles that indicate the purpose. But, avoid very long titles that will be harder to remember or understand. E.g., `Amazon Affiliate ID`.
* Keep variables content granularity at reasonable level. This is beneficial from both content reuse as well as performance perspective. For example, you could technically define every feature of your product as a separate variable. But, in general, it may be more meaningful to have a set of features in a variable.

= Documentation =

Please check out our detailed <a href="https://cloudnineapps.com/products/wordpress-plugins/c9-variables/documentation" target="_blank">documentation</a>.

== Screenshots ==

1. Edit Variable
2. Use Variable
3. Variable Selector
4. Insert Variable Shortcode
5. Variable List
6. Variable Settings
7. Content showing use of Variables
8. Resulting Content

== Changelog ==

= 1.0.0 =

* Initial release

== Upgrade Notice ==

Not applicable

== Features ==

= Basic Plugin Features =

**C9 Variables** is fully functional and it adds capabilities to both the WordPress Admin as well as the public interface.

* WordPress Admin
    * Add/modify/delete up to 10 Variables
    * Support for WordPress built-in roles: Super Admin, Administrator, Editor, Author, Contributor, Subscriber (<a href="https://cloudnineapps.com/products/wordpress-plugins/c9-variables/documentation/#roles_and_capabilities" target="_blank">see details</a>)
    * Page/Post Editor Enhancements
        * Integrated Editor button to lookup and insert a Variable
        * Organize Variable list by title
        * Use selected Variable in the post/page via a convenient shortcode
  * Settings
      * Debug Mode: On/Off (default: Off)
      * Anonymous Usage Tracking: On/Off (default: Off)
* WordPress Public
    * Replace Variable value when showing the post/page

= Pro Plugin Features =

**C9 Variables Pro** provides following features on top of the basic plugin features.

* WordPress Admin
    * Unlimited Variables
    * Mark favorite Variables
    * Page/Post Editor Enhancements
        * Variable lookup supports searching by favorites
        * Variable lookup supports sorting results by Last Used or Title
        * Variable lookup supports pagination and configurable page size
    * Settings
        * Default Variable Sort Order: Title, Last Used (default: Title)
        * Number of Results per Page: 10, 25, 50 (default: 10)
