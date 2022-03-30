# ListMedia

**Requires at least:** WordPress 4.2
**Tested up to:** WordPress 5.9.1
**Requires PHP:** 5.4
**Stable tag:** 1.4.1
**License:** GPLv2 or later
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

**This is a forked and modified version of the Medialist plugin by D. Relton (mauvedev) which can be found here:** https://wordpress.org/plugins/media-list/

The name has been changed to "ListMedia" to avoid conflicts with "MediaList" but _you should not activate both at the same time_ or some of the issues List Media was modified to address will be overridden.

ListMedia will find page attachments (or) posts using criteria you provide in a shortcode and then display them elegantly styled anywhere on a page. Ideal for displaying policies, accompanying documents, newsletters, supporting documentation, media, Posts and more. 

## Description

The ListMedia plugin is designed to make displaying posts or attached page media, documents and more an easy process. Upload your content, attach it to a page, then place a listmedia shortcode to display a neat list of items. You can customize the shortcode to only display a certain category or a certain number of items if you wish and also alter the style to suit your page.

## Features

**Note:** This plugin does not add a menu item to the WordPress dashboard, functionality and customization is purely in the form of a shortcode.

Features from MediaList Plugin:
* Incredibly lightweight
* List posts and posts by author
* List page attachments
* List posts and attachments by tag
* Define a category of items to display
* Define how many items to display
* Define the order in which items are displayed
* Toggle pagination on/off
* Displays the item type with identifiable icons, with the items download size
* Place the shortcode anywhere on a page/post
* Ability to use the shortcode many times on a single page. You can split categories, **On the same page**, thats fun.
* Ability to toggle sticky posts on/off
* Ability to paginate the list of items after a defined number of items
* Ability to search for items in the list
* Compatible with RML Real Media Library Plugin.

ListMedia Plugin:
* Checks for "Media Library Categories" plugin by Jeffry-WP so "Categories" doesn't get added to Media Library menu twice
* Define custom taxonomies and terms
* Define search filters
* Toggle showing all categories/terms assigned to item


## How to use the shortcode

Shortcodes are a simple way to add functionality to any wordpress page.
To use this plugins shortcode you need to write the shortcode into the page editor or guttenberg block where you want your item list to appear.

1. For a default list write **[listmedia]** with the square brackets included (this will display all attached media in a list on the page and will display pagination at 10 or more items).
2. To customise the shortcode we add some keywords, for example:
`[listmedia order= orderby= category= mediaitems= paginate= style=]` 
After each equals(=) sign, we need to provide a customisation. All possible customisations are listed below.

**Example:** 
`[listmedia type=post order=DESC orderby=date category=recipes,cakes]`

The above example will display a list of most recent posts by date in descending order that have recipes and or cakes as the category.

## Customize the shortcode with additional keywords

**Note:** Some keywords can have multiple options, ensure they are separated by a comma i.e [listmedia mediatype=pdf,audio].

* **type=attachment** (or) **post**
* **mediatype=excel,pdf,doc,zip,ppt,text,audio,images,other** (You can use more than 1 here.)
* **order=ASC** (or) **DESC**
* **orderby**=none, ID, author, title, name, type, date, modified, parent, rand, comment_count
* **category=uncategorized,**(any defined category taxonomy assigned to media or post items, you can use more than one here.)
* **mediaitems=10** (Provide a number of items you wish to display in the list before pagination)
* **paginate=0** (Setting paginate to 0 will disable pagination, mediaitems number will be the max items displayed, default is 10)
* **style="custom-class"** (Include a custom class or space separated list of classes appended to ListMedia elements)
* **author=author-username-here** (This will display posts uploaded by the specified author, case sensitive.)
* **search=1** (Setting search to 1 will enable a basic search facility.)
* **tags=** (any defined tag assigned to media or post items, you can use more than 1 here.)
* **rml_folder=(folder ID)** You can find the folder ID by selecting a folder, and click the three dots on the folder toolbar. A dialog opens and in the bottom right corner there is a text label with the ID.
* **taxonomy="custom_taxonony"** (any defined custom taxonomy assigned to media, post items, pages, or custom post types, you can only use ONE here) and then you must include *terms* too.
* **terms="term1,term2,term3"** (any defined term of the custom taxonomy keyword, you can use more than one here).
* **filters="string1,string2,string3"** (any word or words you want to suggest as filters for the list.  This is a good place to repeat the categories or taxonomy terms if displaying the full list).
* **showcats="1"** (can be either 1 or 0 or omitted, it determines whether or not the item entry shows all the categories or taxonomy terms assigned to the item).

**Notes:** 
* If using the RML Real Media Library Plugin, attach your files within RML folders to the desired page as normal or use the **globalitems=1** attribute.
* Search is a string search of text written to the page, NOT a database search.  For "filters" attribute to work the predefined text needs to actually be part of the entries. Even if you choose to omit or set "showcats" and/or "showtags" to zero the text is still written into the page and searchable, even though it's not shown.
* You can use "taxonomy" with "terms" and add "tags" but the value needs to be the tag taxonomy slug.
* The item type (post, page, event, pdf, jpg, doc, etc.,) is always searchable, even though it is not shown.

**Other:** 
The mediatype (other) currently supports exe,sql & xml files.

**Example Media Library Categories with Custom Taxonomy:**
`[listmedia order="ASC" orderby="title" taxonomy="media" terms="papers" search="1" type="attachment"]
`
The "Media Library Categories" plugin has the option to either use the Post Categories for the Media Library items or you can set a custom taxonomy if you'd like to use different categories for the Media Library.  In this case that custom taxonomy was simply named "media" and one of the terms in it is named "papers."

**Examples of MemberPress Downloads:**
`[listmedia order="ASC" orderby="title" taxonomy="mpdl-file-categories" terms="syallabus,handout,worksheet" showcats="1" search="1" filters="handout,worksheet" type="mpdl-file"]`

This would find all the MemberPress Downloads files in it's custom "categories" named "syallbus," "handout," and "worksheet."  It would show, under each item listed, what term(s) it is in, and the Search box would be followed by two suggested filter buttons "handout" and "worksheet."

`[listmedia order="ASC" orderby="title" taxonomy="mpdl-file-categories" terms="handout,worksheet" tags="mpdl-file-tags" type="mpdl-file"]`

This would create hidden lists of all the categories and tags assigned to an item.  The terms would still be searchable, just not shown.

While anyone could see the list of files, access to them would be restricted by whatever means you'd set up in MemberPress Rules for those files.

**Example with MemberPress Courses**
`[listmedia order"ASC" orderby="title" paginate="5" taxonomy="mpcs-course-categories" terms="program1,training" tags="mpcs-course-tags" showtags="1" type="mpcs-course"]`

You could also just use the Block Editor's "Post List" or "Query Loop" blocks, which can display a featured image and excerpt, but if you want to use List Media this is how you format it.

**Example The Events Calendar:**
`[listmedia order=DESC" order-by="date" taxonomy="tribe_events_cat" terms="conference" type="tribe_events"]`

This would show a list of events, newest to oldest, in the event category "conference" only.


## Override options for shortcode

For special use cases, you may want to override parts of the plugin using the following shortcode keywords.

* **sticky=0** (By default sticky posts will be ignored, setting this to 0 will pin sticky posts to the top of the list.)
* **max=200** (By default the plugin will only add 200 items to a list. You can override this by setting **max=** to a larger number. Or similarly a smaller number.)
* **globalitems=1** (By default attachments from the current page (or) post are able to be displayed. Setting this attribute to **1** will allow the list to display all items in the Media Library. It is **recommended** to set a category attribute first before using this override option.)

**Example:** 
`[listmedia sticky=0 max=1000 mediaitems=10 type=post order=DESC orderby=date category=recipes]`

The example will now pin sticky posts to the top and will also display up-to a thousand items and because mediaitems=10 there will be 10 items per page for a total of 100 possible pages.


## The defaults

A [listmedia] will by default have the following features unless changed in your shortcode with keywords

* Pagination Enabled
* Display a maximum of 200 items
* Sticky posts will be ignored
* A total of 10 items will display per list and paginate for items over this number
* Generate a list of the media items attached to the current page only and of any category
* Organize each list in ascending order by title

**Found a bug or incorrect translation?** Open an Issue on GitHub

If you have downloaded **Medialist** and are actively using it on your site, consider writing a review, let me know what you think.

**Credits**
* mauvedev (D. Relton)
* kmhcreative (K.M. Hansen)

## Installation

1. Upload the *list-media** folder to the **/wp-content/plugins/** directory.
2. Activate the plugin through the **Plugins** menu in WordPress.

## Frequently Asked Questions

**What file types are supported?**

At the moment the following file types are supported - pdf,doc,docx,ppt,pptx,xls,xlsx,txt,csv,cal,mp3,wav,wma,mid,jpg,gif,png,bmp,tiff,icon,odt,odp,ods,exe,sql,xml,zip,rar,dmg


**How do I add a category to media items in WordPress?**

Starting with version 1.2.0 the feature to list by category is available for both attachments and posts. Assign a category from the Media library and on the Page or Post Settings as you normally would. 

**I'm not seeing the list update when using page builders?**

The lists will initially load once on page-load, when the shortcode is changed you won't always see the changes in the page builder automatically. To see all the changes when editing a list shortcode, its best to preview the page.

**I've added the shortcode to a page, but it isn't displaying my attachment?**

Make sure that the attachment you have uploaded into the **Media Library** has been attached to the page the shortcode has been placed on. In the case of Posts, check you have assigned a category and your shortcode syntax is correct.

## Changelog
_1.4.1_
* Added support for MemberPress Courses
* Updated stylesheet to CSS3

_1.4.0_
* forked from MediaList plugin to GitHub (because I don't know SVN)
* Built-in multiple styles removed (you should do those in your theme)
* Switched from Swift icons to Dashicons
* Checks for "Media Library Categories" plugin by Jeffry-WP so "Categories" doesn't get added to Media Library menu twice
* Added support for custom taxonomies and terms (which adds support for MemberPress Downloads and The Events Calendar and many other custom post types with custom taxonomies)
* Added pre-defined list filters
* Added ability to show all categories/terms assigned to item
* Added support for DMG files
* Fixed pagination where it would only do 10 items or all items.

_1.3.9 & Earlier_
* See Changelog for MediaList plugin in WordPress repository