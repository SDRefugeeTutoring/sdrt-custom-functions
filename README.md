# SDRT Custom Functions
This is a functionality plugin for the [San Diego Refugee Tutoring website](https://sdrefugeetutoring.com).

It currently does not have a build process because the custom CSS is just plain vanilla CSS and is minimal, and I have not setup any linting at all yet. 

The following is an explanation of the structure of the plugin to make it easier to navigate and keep any new fuctions consistent. 

## 📄 The Root Plugin File

This file is only used for the following purposes:

* Register the plugin with WordPress
* Set globals for convenience
* Load up the "bootstrappers"

The "Bootstrappers" handle including any and all relevant files that contain the core functions of each sub-folder. This is like a poor man's auto-loader. It's a bit lazy but I find it convenient when needing to troubleshoot issues, I can just comment out a whole folder via this file to help isolate and identify where problems arise from. I always name a bootstrapper with an underscore. Here's a quick list of them:

* **admin/_amin.php** -- loads everything in the `/admin` folder. 
* **includes/_inc.php** -- loads everything in the `/inc` folder.
* **plugin_customization/_plugins.php** -- loads everything in the `plugin_customization` folder. 
* **registration/_registration.php** -- loads everything necessary for the registration functionality which is spread across several folders. 
* **rsvps/_rsvp.php** -- loads everything in the `/rsvps` folder. 

Generally, there's no reason to edit this file unless this plugin will be greatly expanded, or to optimize it's performance (like create a real auto-loader).

## 📁 Admin

This folder holds custom functions related to the WordPress Admin area. They are the following:

* **Emails/ematil_templates.php** -- These are static emails that the site sends out related to the RSVP and Background Check functionality. 
* **admin_columns.php** -- this customizing the admin columns of the RSVP custom post type
* **better_cpt_search.php** -- this improves the search functionality of custom post types to include more meta data. 

## 📁 Assets

This folder holds all relevant CSS and JS files. If there were images we would put them here as well. Here's a quick description of each file:

* **admin-styles.css** -- Enqueued only in the admin area, and only hides alerts currently. This is to prevent volunteers who have admin access from seeing admin notices. 
* **autosize.min.js** -- this is used on the "RSVP > Emails" screen to auto-adjust the size of the textarea when users are typing into the textarea and avoid a scrollbox. It's a nice-to-have, not necessary file. But I think it's an improvement.
* **exportHTML.js** -- this is used on the Event pages to export the RSVPs to a PDF. 
* **rsvp-admin-styles.css** -- Just some basic styles to clean up the appearance of the RSVP single post in the admin
* **rsvp-styles.css** -- Styles for the front-end RSVP table

## 📁 Includes

This folder holds a lot of different files that have necessary helper functions for some of the important features of the site. I think of this mostly as the place for APIs and helper functions overall. 

* **hd-wp-settings-api** -- I found this class for WordPress settings API and I liked it, it was easy to implement and made sense to me. It's used primarily currently for creating the settings page on "RSVP > Emails". 
* **sdrt_scripts.php** -- anything that gets enqueued either in the frontend or backend gets enqueued in this file.
* **sdrt_user_meta.php** -- the users have some custom meta, that's all in here. 

## 📁 Plugin Customizations

Anything that extends a necessary plugin that is used on the site is held in here. Because activating/deactivating plugins that have extended functions can create fatal errors, I felt it safest to put all that functionality in one place. This can help if a certain plugin gets swapped out in the future. Additionally, the bootstrapper in this folder checks whether the plugin is active or not before including the files. This makes it so that I don't have to check whether the plugin is activated on every custom function I write. 

* **tribe_events** -- This is "The Events Calendar". Currently this is the only plugin that is customized extensively. 

There are actions that Caldera Forms triggers, but they don't require that CF is activated. There were previously GiveWP customizations in here, but the current theme made them absolete. If other plugin customizations are necessary I suggest they get added here. 

## 📁 Registration
This is one of the advanced functionalities of the website. Some background:

This organization is about tutoring school children. This requires that volunteers have passed a background check before being able to tutor (for obvious reasons). So users are not allowed to RSVP for tutoring events at all unless they have passed a background check. So this is how that happens:

1. The register for the website [here](https://sdrefugeetutoring.com/volunteer/)
    * This triggers a user account called `volunteer_pending`
    * It also triggers an action that pings Checkr to create an "invitation" for this user to fill out their background check. The invitation link is shown to them immediately and it's also emailed to them by Checkr. The volunteer goes to the Checkr website with their invitation link and pays for their background check there. It's necessary that they use the invitation link for the whole process to work correctly. 
2. After they pass their background check, Checkr sends a webhook to the website and they initiates upgrading their user account from `volunteer_pending` to `volunteer`. Only user accounts that have the `volunteer` role or higher can RSVP for tutoring events. 
3. The webhook also triggers an email to the volunteer saying they can now RSVP. The content of that email is in "RSVP > Emails". 

The status of a volunteer's background check is shown via their User Profile, and that is "user meta". 

## 📁 RSVPs

Everything in here is related to how volunteers RSVP for events, and volunteer coordinators can see and manage the RSVPs for the events. 

**OVERVIEW**
There is ONE Caldera Form that is used for ALL Tutoring Events, and another one for all non-tutoring events. This is added to the Event single post with an action.

Because users must be registered and logged in to RSVP for tutoring events, we don't need all their personal info for this RSVP form, we just collect it automatically from their user profile via hidden fields. Additionally, the Caldera Form also has hidden fields to capture the date and title of the tutoring event in order to populate the RSVP correctly. 

Each RSVP submission creates a RSVP which is a Custom Post Type. This allows for much more flexible queries of RSVPs and beign able to do more dynamic things with the information. 

Users who have the role "SDRT Leadership" are able to see all the RSVPs for individual events on the Single Event page on the front-end. The RSVPs for that specific event are output as a Table. And each row has an "Attended" column. This allows the volunteer coordinators to take attendance the night of the tutoring event. When the "Attended" is clicked for the volunteer who RSVPd, an email is triggered. The content of that email is in "RSVPS > Emails". 

There is also a "Print" function at the top of the RSVP table for volunteers if they choose to print out the RSVPs for that event on paper. 

Most of that functionality is included in this folder, with the exception of some tribe_events customizations and helper functions. 

## ❓ Questions?
For now, if you have any questions at all, contact Matt Cromwell at webmaster@sdrefugeetutoring.com
