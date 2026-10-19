# phpBB Post Reactions Extension

An extension for phpBB that enables modern emoji post reactions (👍, ❤️, 😂, 😮, 😢, 😡) on forum posts, fully integrated with ACP settings and phpBB's granular permission system.

---

## 🌟 Features

* **Interactive Emoji Bar**: Users can react to posts using interactive emojis.


* **ACP Settings Panel** (`ACP -> Extensions -> Post Reactions`):
* Enable or disable post reactions globally.


* Allow guests to see existing reactions and counts in read-only mode.


* Send native phpBB notifications to post authors when a new reaction is added.




* **Granular Forum & Role Permissions**:
* Adds a new permission option (`Can react to posts`) under Forum Permissions.


* Fully customizable per user group, forum, or role.





---

## 📋 Requirements

* **phpBB**: `>= 3.3.0` (may works on `3.2.0`)
* **PHP**: `>= 8.0.0`

---

## 🚀 Installation

1. **Download** or clone the repository into your phpBB installation:
```text
phpBB/ext/lenu/postreactions/

```


2. **Navigate** to your **Admin Control Panel (ACP)**.
3. Go to **Customise** > **Extension Management** > **Disabled Extensions**.
4. Find **Post Reactions** in the list and click **Enable**.

---

## 🔄 Upgrade Instructions

1. Go to **ACP** > **Customise** > **Extension Management** > **Enabled Extensions**.
2. Locate **Post Reactions** and click **Disable** *(do **not** click "Delete data")*.
3. Upload and overwrite the existing extension files in:
   ```text
   phpBB/ext/lenu/postreactions/
   ```
4. Return to **ACP** > **Customise** > **Extension Management** > **Disabled Extensions**.
5. Click **Enable** to finish the upgrade process and run any necessary database migrations automatically.
6. Purge the cache via **ACP** > **General** > **Purge the cache**.

---

## ⚙️ Configuration & Permissions

### General Settings

Go to **ACP** > **Extensions** > **Post Reactions** > **Settings**:

* **Enable post reactions**: Global toggle for the extension.


* **Show reactions to guests in read-only mode**: Displays reaction tallies to non-logged-in users while preventing reaction submission.


* **Notify post authors about new reactions**: Sends notification alerts when users react.



### Forum Permissions

Go to **ACP** > **Permissions** > **Forum permissions** (or **Forum roles**):

* Select the desired forum(s) and group(s).


* Under the permissions list, locate **Can react to posts** and set to **Yes**, **No**, or **Never**.



---

## 🔄 Uninstallation

1. Go to **ACP** > **Customise** > **Extension Management** > **Enabled Extensions**.
2. Locate **Post Reactions** and click **Disable**.
3. To delete all reaction data and settings, click **Delete data**.
4. Remove the `ext/lenu/postreactions` directory from your server.

---

## 📄 License

**GPLv2** © Lenuchan

---