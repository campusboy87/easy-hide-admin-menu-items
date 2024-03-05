# Easy Hide Admin Menu Items

The plugin provides a user-friendly interface for hiding unused sidebar menu items in the WordPress admin panel.

![Simple Hidden Menu - Animate Screenshort](https://raw.githubusercontent.com/campusboy87/simple-hidden-menu/assets/assets/shm-main.gif)

## Description

The **Easy Hide Admin Menu Items** plugin is designed to enhance the usability of the WordPress admin panel by allowing users to hide unnecessary menu items. This is particularly useful on sites with extensive admin panels, where numerous menus and settings may complicate navigation and create confusion 🎨.

## Why is this convenient?

On some websites, the admin panel can feature numerous menus and various settings that may be rarely used or even unnecessary. **Easy Hide Admin Menu Items** enables site administrators to easily hide such elements, enhancing operational efficiency and improving the overall experience in managing the website.

![Plugin Interface: Easy Hide Admin Menu Items](https://github.com/campusboy87/easy-hide-admin-menu-items/assets/41396370/63661da0-7c60-4b35-8693-3836cbb5b599)

### Simplicity for the Average User

While there are numerous plugins available for hiding menus in the WordPress admin panel, many of them require additional settings and extra learning for the average user. Our plugin, **Easy Hide Admin Menu Items**, stands out from the alternatives with its simplicity and intuitive user interface.

Upon activation, a convenient toggle switch appears in the admin bar, allowing users to easily enable or disable hidden menus. Hovering over this toggle instantly reveals all hidden menus. Additionally, when hovering over any WordPress menu item, an icon appears, indicating that the menu can be hidden. If you choose to hide a specific menu, it seamlessly moves to the area of all hidden menus.

At any moment, you can quickly enable or hide menus, visually seeing all hidden items when hovering over the toggle. Thus, our plugin provides the simplest and most intuitive way to hide and manage menus, even for users without specialized WordPress management skills.

## Settings

![Easy Hide Admin Menu Items Plugin Settings](https://github.com/campusboy87/easy-hide-admin-menu-items/assets/41396370/25a510f5-e721-4b34-b091-f2216065927f)

## Plugin Modes

The plugin provides two operational modes:

- **Global Mode:**
    - Allows hiding menus globally for all users according to common settings.

- **Individual Mode for Each User:**
    - When this option is activated, each user can customize the visibility and hiding of menus according to their individual preferences.

Thus, users can choose between the global mode and individual settings based on their needs. The settings page for the plugin is accessible in the "Settings" section of the WordPress admin panel under "Settings > Menu Settings."

### [ ] User-Specific Settings

Enabling this checkbox allows each user to save their settings individually. This provides the ability to customize menu item visibility for each user according to their preferences.

Note: The checkbox mentioned enables or disables the user-specific settings functionality. When the option is enabled, settings are stored in `user_meta`, and when disabled, they are stored in `get_option`.

### [ ] Hide Submenus

Activate this option to easily manage the visibility of submenus in your WordPress admin panel. When this option is enabled, you can hide submenus, which is particularly useful when your admin panel is rich with various plugins, and some menu items may cause discomfort or distraction. Utilize WordPress more efficiently by choosing which submenus to display, creating a cleaner and more convenient interface for your work.

## Installation Instructions

To install the Easy Hide Admin Menu Items plugin from GitHub, follow these simple steps:

1. **Download the ZIP Archive:**
    - Go to the [repository page](https://github.com/campusboy87/ehami).
    - Click on the "Code" button and select "Download ZIP."

2. **Move the Plugin to WordPress:**
    - Navigate to the WordPress admin panel.
    - In the "Plugins" section, choose "Add New."
    - Click on the "Upload Plugin" button and select the previously downloaded ZIP file.
    - Install and activate the plugin.

3. **Configure the Plugin:**
    - After activation, the plugin is ready to use, but you can further customize it. Go to the "Settings" section of the admin panel and choose "Menu Settings."
    - Adjust the settings according to your preferences, selecting either the global mode or individual mode for each user.

Now the plugin is ready for use, and you can enjoy improved menu manageability in your WordPress admin panel!

## Changelog

**1.3.6:**
- Links in the hidden menu bar are now clickable.
- Added a setting to disable hiding icons.
- Updated project description.
- Performed testing for inaccuracies in the plugin.
- FIX: Corrected interface display issue for editors.
- FIX: Resolved saving error in user_meta for the last menu item.

**1.3.5:**
- Add notice review.

**1.3.4:**
- UPD: Contact details of the participants.

**1.3.3:**
- Added the ability to hide child menus.

**1.3.2:**
- Added support for WordPress 6.4.
- Added settings page.

**1.3.1:**
- Fixed minor bugs related to menu item visibility.
- Enhanced performance for larger WordPress installations.

**1.3:**
- Added a new option to hide submenus.

**1.2:**
- Added support for WordPress 6.4.
- Added a settings page.

**1.1:**
- Fixed minor issues related to menu item visibility.
- Improved performance for large WordPress installations.
