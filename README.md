=== Plugin Name ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: http://example.com/
Tags: comments, spam
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This Base module for Cloud Base - A module for managing Glider Club aircraft and flights.

== Description ==

This is the long description.  No limit, and you can use Markdown (as well as in the following sections).

For backwards compatibility, if this section is missing, the full length of the short description will be used, and
Markdown parsed.

A few notes about the sections above:

*   "Contributors" is a comma separated list of wp.org/=== Plugin Name ===
Contributors: (this should be a list of wordpress.org userid's)
Donate link: http://example.com/
Tags: comments, spam
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This Base module for Cloud Base - A module for managing Glider Club aircraft and flights.

== Description ==

This is the long description.  No limit, and you can use Markdown (as well as in the following sections).

For backwards compatibility, if this section is missing, the full length of the short description will be used, and
Markdown parsed.

A few notes about the sections above:

*   "Contributors" is a comma separated list of wp.org/wp-plugins.org usernames
*   "Tags" is a comma separated list of tags that apply to the plugin
*   "Requires at least" is the lowest version that the plugin will work on
*   "Tested up to" is the highest version that you've *successfully used to test the plugin*. Note that it might work on
higher versions... this is just the highest one you've verified.
*   Stable tag should indicate the Subversion "tag" of the latest stable version, or "trunk," if you use `/trunk/` for
stable.

    Note that the `readme.txt` of the stable tag is the one that is considered the defining one for the plugin, so
if the `/trunk/readme.txt` file says that the stable tag is `4.3`, then it is `/tags/4.3/readme.txt` that'll be used
for displaying information about the plugin.  In this situation, the only thing considered from the trunk `readme.txt`
is the stable tag pointer.  Thus, if you develop in trunk, you can update the trunk `readme.txt` to reflect changes in
your in-development version, without having that information incorrectly disclosed about the current stable version
that lacks those changes -- as long as the trunk's `readme.txt` points to the correct stable tag.

    If no stable tag is provided, it is assumed that trunk is stable, but you should specify "trunk" if that's where
you put the stable version, in order to eliminate any doubt.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `cloud-base.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('cloud_base_hook'); ?>` in your templates

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* A change since the previous version.
* Another change.

= 0.5 =
* List versions from most recent at top to oldest at bottom.

== Upgrade Notice ==

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 0.5 =
This version fixes a security related bug.  Upgrade immediately.

# CloudBase
A WordPress plugin to provide many features necessary to manage a volunteer flight school/club. It makes use of Wordpresses built in features for membership. Does not make extensive use of “blogging” features. Pages are built with short-codes. This plugin was built on a Wordpress plugin boilerplate originally developed by Tom McFarlin. The basic boilerplate was extender to add a RESTfull section.

It provides a basis that can be leveraged by other plugins to provide specific organization needs.

## Dependencies
CloudBase depends on Wordpress user roles, see next section. In order to assign multiple roles to one user a plugin such as ‘Members’ is required.

## Roles
CloudBase expands on Wordpress user roles.
* CFIG
* CFI
* Tow Pilot
* Operations
* Chief CFIG
* Chief tow pilot
* Field Managers
* Assistant Field Managers
* Maintenance
* Instruction scheduler
* Instructor scheduler
* Tow Pilot scheduler
* Inactive
* Subscriber*, built in role, used for all active members. 

## Cloud base - Front end - Shortcodes

### Display_Flights

Displays the flight sheet. Members, instructors, tow pilots, tow planes and gliders are all provided in dropdown lists and can be selected to build a flight record. Start/stop buttons record take off and landing times.  

Not currently tested/used as a separate plugin is used by PGC to maintain compatibility with historic data in the PDP.

### Display_status

Display status of all aircraft, can be a summery list of aircraft competition ids which are color coded according to currently status. Or a detailed list with registration numbers, expire dates and notes. If the logged in user has Maintence authority, they are able to update status and expiration dates. 

In the admin area of Cloudbase settings, there are tabs where status types and color-coding can be set up, Equipment type(tow Plane and Glider types are pre-defined) And a tab where equipment(tow planes, gliders, tractors, licenses etc can be entered.

### No_fly list

This shortcode displays a list of members who are on the no-fly list. It scans member sign-offs for critical sign-offs. Members who have expired critical sign-off will be listed

Sign-offs are set up in the CloudBase settings tab. See below.

Of limited value at the moment as we are somewhat lazy keeping sing-offs up to date.

### Display_signoffs

List of members current sign-offs along with expire dates. Expired sign-offs will be in red.

### Signoff_summary

??


### Update_signoffs

Displays a form where a member can be selected from a drop down menu, their sign-offs that the current logged in user is authorized to update will be displayed. The logged in user can then update or add new sign-offs for the selected member. 

### Squawk_sheet

Displays a form where aircraft issues may me reported. Aircraft are displayed in a drop down menu, a text area is provided where the problem can be described. This information along with date and reporting member are emailed to the Maintence staff. Below the form is a list of previously reported squawks and their status. Maintence members can change the status of squawks from this list.

## Admin area
CloudBase provides a settings page. Once activated it will appear in the Wordpress dashboard under setting/CloudBase. On that page are eight tabs:
* Basic configuration 
* Equipment types
* Sign-off types
* Equipment
* Status types
* Flight Types
* Tow Fees
* Aircraft Events

This list may be extended by other plugins.

#### Basic configuration
Enter the origination name, abbreviation name and if you use feet or meters.

#### Equipment types
Default types are tow planes and gliders. Any additional equipment can be added here, self-launching glider, tractor, fuel tank etc.

#### Sign-off types
Sign-offs as required for your operation. Enter the authority to endorse the sign-off, date entered and period. Expiration dates will be calculated. It knows the difference between yearly and yearly end of the month. The sign-off can also be marked apply to all, in which case it will be added to all existing members with an expired date and will be added to all new members.

#### Equipment
Individual aircraft and equipment is entered here. Enter registration number competition number, registration expire date. 

#### Status types
This one is a bit silly but only way to make it flexable. Would expect types such as grounded, available, etc.

#### Flight types
Again such as regular, instructional, AOF.

#### Tow Fees
This is the big one, every organization appears to have a different tow fee structure. I’ve tried to make this as flex. able vas possible, but im sure as this gets used new option will be required.  

#### Aircraft Events types
Configure events that might occur to an aircraft. Annual inspections, 100 hour inspections, registration, oil changes, etc. 

## RESTfull interface

The CloudBase plugin provides a RESTfull interface for many of its features.   

Base address is IP/wp-json/cloud_base/v1 

* Aircraft Events /event\_types 
* Aircraft Types /aircraft\_types
* aircraft /aircraft
* Aircraft Event types /event\_types
* Tow Fees /fees 
* Flight Types /flight_types
* Flights (log) /flights'
* Pilots /pilots'
* Sign off types /sign\_off\_types
* Sign offs /sign\_off
* Equipment squawks /squawks
* Aircraft Status /aircraft\_status (perhaps should be folded into aircraft?)

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* A change since the previous version.
* Another change.

= 0.5 =
* List versions from most recent at top to oldest at bottom.

== Upgrade Notice ==

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 0.5 =
This version fixes a security related bug.  Upgrade immediately.

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.


Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`