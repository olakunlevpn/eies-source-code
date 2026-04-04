## 4.8.14 - 2026-04-01
- **New:** Added time limit reminder emails for students
- **Update:** Added hints for email triggers in Email Manager
- **Update:** Added email templates for course rating notifications for admins and instructors
- **Update:** Added an additional Password Changed email trigger when resetting password via the Lost Password form
- **Fixed:** Fixed email confirmation not being sent on registration via LMS Guest Checkout
- **Fixed:** Fixed global variables disappearing when using LMS Form Editor in Become Instructor and Enterprise Request emails

## 4.8.13 - 2026-03-18
- **Fixed:** Fixed an issue where the course title filter did not apply in Student Assignments

## 4.8.12 - 2026-03-12
- **New:** Added the ability to set deadlines for Assignments  
- **Fixed:** Fixed an issue where the Sales and Subscriptions tabs switched when selecting a date  

## 4.8.11 - 2026-03-03
- **Update:** Updated the design of the Instructor and Student profile pages with a more modern interface and improved usability.
- **Fixed:** Global variables disappeared in Email Manager when using custom fields from LMS Form Editor.
- **Fixed:** Minor bug fixes.

## 4.8.10 - 2026-02-19
- **Fixed:** Minor bug fixes.

## 4.8.9 - 2026-02-10
- **New:** Added ability to issue certificates based on the course purchase method, such as issuing certificates only for One-Time purchases
- **Fixed:** When adding a student via "Manage Students" with the Subscriptions add-on enabled, the "Start Course" button was not displayed on the Single Course page for the student.

## 4.8.8 - 2026-01-28
- **Update:** Improved the layout of email blocks in the Email Manager add-on
- **Update:** Prompt character limits in the AI Lab add-on are determined by the selected model instead of a shared global limit
- **Fixed:** Stripe did not allow completing a purchase when a 100% discount coupon was applied
- **Fixed:** Calendar events were not created for students from Google Meet lessons
- **Fixed:** Zoom lesson date format follows WordPress date and time settings

## 4.8.7 - 2025-12-25
- **New:** Built-in discount coupon system for courses, subscriptions, and bundles
- **Fixed:** Admin Commission and Instructor Earnings values were not saved in plugin settings
- **Fixed:** The "Subscription Only" filter did not display courses with an active subscription setting
- **Fixed:** An error occurred when students uploaded files in Assignments

## 4.8.6 - 2025-12-18
- **Fixed:** Minor bug fixes.

## 4.8.5 - 2025-12-10
- **Fixed:** Lesson data in the Course Analytics detail table failed to load when the course contained a large number of lessons
- **Fixed:** The "Reminder to Inactive Students" email was sent daily after the period set in the "Send After" option

## 4.8.4 - 2025-12-04
- **Improvement:** Performance improvements applied to User Account pages
- **Fixed:** Sale Period functionality did not work properly for Memberships & Subscriptions

## 4.8.3 - 2025-11-19
- **Fixed:** QR codes in the Certificate Builder preview now redirect to the homepage of the current site
- **Fixed:** Some formatting was lost when reviewing Student Assignments from the admin panel

## 4.8.2 - 2025-11-12
- **Fixed:** Minor bug fixes.

## 4.8.1 - 2025-11-12
- **Fixed:** Minor bug fixes.

## 4.8.0 - 2025-11-10
- **New:** Subscriptions Add-on
- **New:** Tax functionality for course sales
- **New:** "Personal Information" fields added to the checkout form
- **Fixed:** Data visualization issues on the Analytics → Students → Detailed Report page

## 4.7.20 - 2025-10-16
- **Fixed:** Bulgarian-language certificates contained unwanted spaces in the text
 
## 4.7.19 - 2025-10-08
- **Update:** Zoom Conference Add-on now includes built-in Zoom settings and no longer depends on the eRoom plugin to create or manage meetings

## 4.7.18 - 2025-09-29
- **Fixed:** Fixed an issue where timers were not displaying in the Course Player for Zoom Lessons, Drip Content, and Stream Lessons

## 4.7.17 - 2025-09-24
- **Improvement:** AI Lab add-on now includes new models — GPT-5, GPT-5 Mini, and GPT-5 Nano
 
## 4.7.16 - 2025-09-23
- **Fix:** Minor bug fixes.

## 4.7.15 - 2025-08-20
- **Fixed:** Resolved issue preventing repurchase of time-limited courses through WooCommerce Checkout
- **Fixed:** Resolved compatibility issue with WooCommerce Checkout for users with expiring or canceled Paid Memberships Pro subscriptions

## 4.7.14 - 2025-08-13
- **New:** Added functionality to send reminder emails to students who haven’t logged in for a prolonged period
- **New:** Tooltips added to email templates within the Email Manager
- **New:** Course rejection functionality added — administrators can reject submitted courses directly via Course Builder and the admin panel
- **New:** A new “Course Rejection” email template has been added for instructors
- **Update:** Emails within the Email Manager are categorized by recipient type
- **Fixed:** Bundle counts in “Reports & Analytics” on the Enrolled Courses page were incorrect when using WooCommerce Checkout
- **Fixed:** Internal links and anchors within PDF files did not function correctly in PDF Lessons

## 4.7.13 - 2025-08-07
- **New:** Instructors can now add custom notes to orders on the "My Sales" page
- **Fixed:** Fixed an issue where course generation via AI Lab occasionally failed during content saving

## 4.7.12 - 2025-07-30
- **Improvement:** Support for creating "Image Matching" and "Image Choice" quiz questions added to the AI Lab add-on
- **Fixed:** Fixed incorrect page navigation direction in PDF Lessons in RTL layout

## 4.7.11 - 2025-07-24
- **Improvement:** Redesigned the Bundle creation page
- **Improvement:** Bundles can now be sold using Points
- **Improvement:** Bundle cards now show the price in Points
- **Improvement:** Added a new email template and trigger for Course Expiration Notification for Students
- **Improvement:** Added 5 new smart-tags to the Assignment Status Change email: {{assignment_result}}, {{assignment_comment}}, {{assignment_mark}}, {{assignment_url}}, {{instructor_name}}
- **Improvement:** Added a new email template and trigger for Lesson completion notification sent to instructors
- **Update:** Increased character limit for prompts in AI-powered features
- **Fixed:** The Assignment Status Changed email was sent even when the assignment hadn’t been reviewed
- **Fixed:** Video Preview didn’t work on iOS devices on the Single Course page

## 4.7.10 - 2025-07-16
- **Improvement:** Added a new lesson type — PDF Lesson
- **Improvement:** Added AI-based duration estimation for the Lesson Duration field via the AI Lab add-on
- **Improvement:** AI-generated lesson and course content now includes visual formatting such as lists and highlights
- **Improvement:** Optimized Video Preview loading speed on the Single Course page — videos now load only on playback, reducing page load time
- **Fixed:** CSS files could not be uploaded via the File Manager, even if the format was allowed in the Media File Manager settings

## 4.7.9 - 2025-07-10
- **Improvement:** Added new smart-tags to all existing email templates
- **Improvement:** When adding multiple elements in Certificate Builder, each new element now shifts to the right for easier placement
- **Fixed:** The Price field no longer remains visible when using the "Affiliate this course" option if "One-time purchase" is disabled in Course Builder
- **Fixed:** Fixed a deprecated warning in Certificate Builder when using PHP 8.2
- **Fixed:** Student registration dates now display correctly in the "Students" section and follow the date/time format from WordPress settings
- **Fixed:** When adding the same course multiple times to the cart using "WooCommerce Checkout," a checkout error occurred
- **Fixed:** Quiz attempt results were not showing when instructors viewed student attempt details

## 4.7.8 - 2025-07-02
- **Improvement:** Added two new editable email templates for course completion — one for the instructor and one for the student
- **Improvement:** Added new smart tags to the "Announcement from the Instructor" email for showing course name, student username, and instructor name
- **Improvement:** Added new smart tags to the "You made a Sale!" email to include the student’s email and purchase date
- **Improvement:** Added a new editable "Student Enrolled in Course" email template sent to instructors
- **Improvement:** Added a new editable "Quiz Completed" email template sent to instructors
- **Improvement:** You can now disable certificates for any selected course
- **Improvement:** Global certificates can now be selected in Course Builder — previously only custom certificates were available
- **Improvement:** Added the ability to re-generate course content when creating a full course if generation was interrupted or incomplete due to unexpected errors

## 4.7.7 - 2025-06-25
- **Improvement:** Added image and video search in the Course Builder File Manager via Unsplash, Pexels, and Pixabay integration
- **Improvement:** Subtitles can now be added to video lessons with HTML and External link source types
- **Improvement:** Video files in the File Manager now display a preview thumbnail and a "Video" badge
- **Improvement:** AI Lab can now generate images for Text Lessons and Assignments when creating a full course When generating a full course using the AI Lab add-on, images can now be automatically generated for Text Lessons and Assignments during Curriculum creation

## 4.7.6 - 2025-06-18
- **Improvement:** Added editable email template for new user registration sent to the admin
- **Improvement:** Added editable email template sent to a student when they are removed from a course
- **Improvement:** Added editable password-reset email template for all users
- **Improvement:** Added 2 editable email templates for approving or rejecting Become Instructor requests
- **Improvement:** Added new Grade element to Certificate Builder to show grades for courses, quizzes, and assignments on certificates
- **Improvement:** Added editable email template for removing a user from a group
- **Improvement:** AI Lab now generates more accurate content for Title, Content, and Full Article fields
- **Improvement:** Redesigned the variable insertion panel in email templates
- **Improvement:** Variables can now be added to email subjects
- **Fix:** Passing Grade now shows the actual value in the Quiz Completed email
- **Fix:** Quiz Completed email can now display grades in Points and Grades as well as percentages

## 4.7.5 - 2025-06-11
- **Improvement:** Added 3 new Single Course styles built entirely with Elementor and fully customizable
- **Improvement:** You can now assign any Elementor-built page as the Single Course page for a specific course
- **Improvement:** You can now create your own course page styles using Elementor
- **Fix:** Products not appearing on product category pages when the “Display courses on WooCommerce shop page” setting was enabled
- **Fix:** Quiz embedded via the “Online Testing” Add-on stored one attempt’s results and showed them to all guests; now each guest sees their own results

## 4.7.4 - 2025-06-05
- **Fix:** Minor bug fixes.

## 4.7.3 - 2025-06-04
- **Improvement:** AI Lab now includes new GPT-4-based text generation models.
- **Improvement:** Improved image generation quality in AI Lab.
- **Improvement:** Added an option to hide the Video Questions block in video lessons.
- **Improvement:** When creating a course with AI Lab you can choose which content to generate and set per-lesson limits.
- **Improvement:** Added language selection for content and lesson generation in AI Lab.
- **Improvement:** AAdded a page to view all current students and their details in the Admin Dashboard and in the instructor’s profile.
- **Improvement:** Added compatibility with VDOCipher and Presto Player for the Video Questions feature in video lessons.

## 4.7.2 - 2025-05-27
- **Improvement:** When signing in with Google or Facebook, the user’s first and last names are now imported from the social profile.
- **Improvement:** Instructors can now attach downloadable lesson materials to Assignments.

## 4.7.1 - 2025-05-22
- **Fix:** Small bug fixes.

## 4.7.0 - 2025-05-21
- **New: AI Lab add-on:** a powerful AI tool for generating complete courses, lessons, quizzes, thumbnails, and more.
- **New:** Added AI image generation in the Media File Manager add-on.
- **Enhancement:** Redesigned Media File Manager.

## 4.6.22 - 2025-05-14
- **Fix:** Small bug fixes.

## 4.6.21 - 2025-05-07
- **Enhancement:** Added the ability to embed knowledge-check quizzes directly into video lessons.
- **Enhancement:** Added ability to enable “Lock Lessons in Order” on a per-course basis.
- **Fix:** Issue where adding file-extension exceptions in SCORM Addon settings did not work.

## 4.6.20 - 2025-05-02
- **Fix:** Fixed the “Disable video seeking” option — viewers can no longer fast-forward or rewind the video.

## 4.6.19 - 2025-04-29
- **Fix:** Restored the option to promote courses and bundles via Google for WooCommerce and Facebook for WooCommerce plugins.
- **Fix:** Removed the certificate preview from search results on the website.

## 4.6.18 - 2025-04-08
- **Enhancement:** Added support for instructor comments at specific video timestamps, visible to students as hoverable timeline markers.

## 4.6.17 - 2025-04-03
- **Fix:** Added LMS course category dropdowns to WooCommerce Coupons for assigning specific categories.
- **Fix:** Fixed freezes in Certificate Builder on Windows OS when users were selecting a font for text blocks.
- **Fix:** Added .txt file upload support in the Media File Manager addon.
- **Fix:** Fixed user certificate generation in the student Public Account.

## 4.6.16 - 2025-03-31
- **Fix:** Resolved an issue where the View Order button in the Order Confirmation email did not redirect to a custom page.
- **Fix:** Fixed an issue when Certificates were sent to students even when the email option was disabled in the Email Manager.

## 4.6.15 - 2025-03-25
- **Enhancement:** Added compatibility with the Polylang plugin. 
- **Fix:** The "Continue" button link in the "Progress updates for students" email now correctly pulls from LMS settings.

## 4.6.14 - 2025-03-19
- **Enhancement:** Added Google Fonts integration for the Certificate Builder.

## 4.6.13 - 2025-03-13
- **Fix:** Analytics Engagement now loads correctly with custom database prefixes.
- **Fix:** Courses & Bundles now appear in search when manually creating WooCommerce orders.
- **Fix:** Order count in Reports & Analytics now accurately reflects actual orders.
- **Fix:** Adjusted download button position for WooCommerce virtual products on the Thank You page.

## 4.6.12 - 2025-03-05
- **New feature:** Introduced an option to show courses on the Shop page when WooCommerce Checkout is active.
- **Enhancement:** Added compatibility with WooCommerce High-Performance Order Storage.
- **Enhancement:** Synced course prices and featured images with the WooCommerce product card to keep details updated automatically.
- **Fix:** Corrected My Sales page not displaying the Enterprise purchases.

## 4.6.11 - 2025-02-27
- **Fix:** Improved the "Display as public name" setting logic in user accounts for certificates.
- **Enhancement:** Updated the "Become Instructor" email template for new clients.
- **Enhancement:** Added new variables: Application Date and User Email to the "Become Instructor" email template.
- **Enhancement:** Added a new variable: Submission Date to the "Enterprise Request" email template.
- **Fix:** The course price on the My Sales page for instructors now displays with the discount applied.
- **Enhancement:** Added a setting that allows using either the student's current name or their first entered name when generating a certificate.

## 4.6.10 - 2025-02-13
- **New feature:** Added a new page in Analytics to display reports and details about Bundles.
- **Enhancement:** Updated the content in "Register on the Website" email template.
- **Enhancement:** Updated the content in "Account Premoderation" email template.
- **Enhancement:** Introduced a new email template for notifying users when they receive a certificate.
- **Fix:** Resolved an issue where uploading .js, .rar, and .yaml files to Assignments was not possible for students.
- **Fix:** Corrected a shortcode in Quizzes not copying properly.
- **Fix:** Resolved an issue where the header and footer of the Twenty Twenty-Four theme were not displayed on the Single Course Page.

## 4.6.9 - 2025-02-06
- **Enhancement:** Added support for the File Manager addon to upload WebP, GIF, and SVG file types in the Course Builder.
- **Fix:** Fixed an issue where the Origins column for affiliation points didn't display the username of the source that shared the referral link.
- **Fix:** Resolved an issue where Dropdown and Radio fields were filled out by default and did not respond to the Required Field setting.
- **Fix:** Fixed a problem where taxes for courses and products were not displayed when purchased through WooCommerce.
- **Fix:** Corrected the display of bundles sold through WooCommerce 9.6.0 for instructors on the My Sales page.

## 4.6.8 - 2025-01-30
- **Fix:** Fixed an issue where the progress of new students was not recorded in Single Course Engagement.
- **Fix:** Resolved a problem where Assignments analytics data didn’t load if the Assignments addon was disabled.
- **Fix:** Small bug fixes.

## 4.6.7 - 2025-01-27
- **Fix:** Small bug fixes.

## 4.6.6 - 2025-01-23
- **Enhancement:** Added a detailed information section and tables for Instructors and Students in the Memberships section of Analytics page.
- **Enhancement:** Enhancement: Updated Analytics Assignment queries for better performance and accuracy.

## 4.6.5 - 2025-01-16
- **Enhancement:** Added Course Views data on the Course Page in Analytics.
- **Enhancement:** Introduced default sorting for tables in Analytics for easier navigation.
- **Fix:** Resolved an issue where videos in Assignments wouldn’t play in Safari.
- **Fix:** Updated the grading logic in Student Assignments Review.
- **Fix:** Fixed an issue where duplicate attempts were marked as passed when pressing "Completed" in Assignments.
- **Fix:** Corrected missing translations on the Thank You page in WooCommerce Checkout.

## 4.6.4 - 2025-01-08
- **Enhancement:** Added a Send Test Email feature to Analytics templates for easier email branding and testing.
- **Fix:** Added option to download e-books via WooCommerce checkout on Thank You and Order Details pages.
- **Fix:** Resolved an issue where instructors couldn't view submitted assignments via the dashboard in the Student Assignments tab.
- **Fix:** Courses deleted from the admin dashboard are now also removed from prerequisites on Single Course page.
- **Fix:** Fixed border issues and mobile video rendering in the Single Course video preview.
- **Fix:** Addressed an issue where the hour timer was not displayed for expired courses.
- **Fix:** Progress of lessons within a course is now displayed accurately in the Engagement by Lesson table.
- **Fix:** Fixed an issue where the email address was not displayed in the table on the Thank You page.
- **Fix:** Resolved several bugs in the Send Test Email.
- **Fix:** Fixed an issue where videos added to the assignment description in the Course Builder were not displayed in the assignment content.

## 4.6.3 - 2024-12-25
- **Enhancement:** Added integration with VdoCipher for Video Lessons.
- **Enhancement:** Introduced Right-to-Left (RTL) support for Course Player components.
- **Enhancement:** Added Right-to-Left (RTL) support for Course Page Styles.
- **Fix:** Small bug fixes.

## 4.6.2 - 2024-12-18
- **Enhancement:** Added option to change logo when printing detailed order view in My Sales for LMS payments.

## 4.6.1 - 2024-12-13
- **Enhancement:** Introduced 'All Time' filter for Grades in the table view.

## 4.6.0 - 2024-12-12
- **New**: Added a new Grades addon.
- **Fix:** _load_textdomain_just_in_time issue fixed.

## 4.5.9 - 2024-12-09
- **New feature:** Added a new email order template in the Orders section for instructors.
- **New feature:** Redesigned Email Order templates for students and admins.
- **Enhancement:** Improved performance for redirects when Drip Content is enabled and Assignments are unchecked
- **Fix:** Resolved an issue where overdue sale prices appeared on the Course tab when creating bundles.
- **Fix:** Fixed multiple issues in the Cart functionality.
- **Fix:** Fixed the "My Points" notification icon not disappearing from the dashboard.

## 4.5.8 - 2024-11-28
- **Fix:** Resolved an issue where no points were awarded upon course completion.
- **Fix:** Corrected Page breaks and text formatting discrepancies between the Certificate Builder preview and final certificates.
- **Fix:** Fixed fatal error appearing on the Statistics page.
- **Fix:** Addressed an issue when Guest Access was not functioning for Trial courses via the Enroll button

## 4.5.7 - 2024-11-25
- **Enhancement:** Added option to disable course rating globally on the site.
- **Fix:** Fixed an issue with fields from Form Builder displaying on User Public Profile page even if 'Show in public profile' is disabled.

## 4.5.6 - 2024-11-20
- **New:** Introduced new Sales page for instructors to manage course sales.

## 4.5.5 - 2024-11-14
- **Enhancement:** Added option to select a static logo for WooCommerce order details printouts.
- **Enhancement:** Added bank transfer details on Orders and Thank You pages for easier payment processing.

## 4.5.4 - 2024-11-07
- **Enhancement:** Added 3 new elements to Certificate Builder: QR Code, Course Duration, Shape.

## 4.5.3 - 2024-10-30
- **Enhancement:** Added visual improvements for the My Orders section.

## 4.5.2 - 2024-10-21
- **New:** Added an option to receive weekly progress updates in Instructor Notifications Settings.
- **New:** Introduced email digest reports for admins, instructors, and students.
- **Enhancement:** Added a new view for selecting certificates and a preview option in the course builder for a more intuitive user experience.
- **Fix:** Corrected the display order of the price and discount price in Affiliate courses.
- **Fix:** Small bug fixes.

## 4.5.1 - 2024-10-09
- **New:** Added new pages to User account in Reports and Analytics: Revenue, Engagement, Course, Reviews, Student.
- **Enhancement:** Added Global search fields for tables in Reports and Analytics.
- **Enhancement:** Added to Chart Preorders students who signed up for a course but did not purchase it.
- **Enhancement:** Added tables to track Student engagement by lessons in Reports and Analytics.
- **Enhancement:** Added navigation to Course Builder and Course Page in course tables in Reports and Analytics.
- **Enhancement:** Visual improvements for course data and charts on the page.
- **Fix:** Time Limit in Coming Soon courses shows more days than needed on the page.
- **Fix:** Lesson Type is not displayed for Google Meet lessons in Course Engagement.

## 4.5.0 - 2024-08-29
- **Enhancement:** Added Advanced Analytics feature for course reports on Revenue, Engagement, Users, and Reviews.

## 4.4.24 - 2024-07-26
- **Fix:** Certificate does not show texts in Japanese and Chinese.

## 4.4.23 - 2024-07-19
- **Fix:** Small bug fixes.

## 4.4.22 - 2024-07-17
- **Enhancements:** Added translations of plugin features into Italian, German, Portuguese, French and Spanish.
- **Fix:** Upcoming course timers show three-digit numbers as two-digit numbers.
- **Fix:** The background image is not removed from the certificate.
- **Fix:** Bundles are only displayed for admin, but not displayed for other visitors.

## 4.4.21 - 2024-07-09
- **Fix:** Non-English characters in drip content are not displayed correctly.

## 4.4.20 - 2024-07-02
- **Enhancements:** Added a field for selecting the required bundles in the Course Bundles block, so that the admin can adjust which bundles to display and which not.

## 4.4.19 - 2024-06-25
- **New:** Added the ability to include a video preview for courses to give students a chance to learn about the course in a video.

## 4.4.18 - 2024-06-24
- **New:** Added a stats dashboard for students to view their courses, progress and achievements.
- **New:** Added a setting to disable and enable the stats dashboard for a student.

## 4.4.17 - 2024-06-18
- **Enhancements:** The client now remains on the same page if he/she logs in via the registration login popup or course player or signs up via social login.
- **Enhancements:** Added a tooltip for course categories when there are many categories in one course.
- **Enhancements:** Statuses (New, Hot, Special) are now displayed in Related and Popular Courses blocks.
- **Fix:** Fixed bugs in different Course page styles.

## 4.4.16 - 2024-06-14
- **Fix:** Small bug fixes.

## 4.4.15 - 2024-06-06
- **New:** Added an Audio lesson addon.
- **Fix:** It is possible to add audio lessons by uploading files, embedding or with shortcodes.

## 4.4.14 - 2024-06-05
- **New:** Added Course Bundles Gutenberg block.
- **Fix:** Clicks from affiliate links on Udemy were not counted due to Rakuten scripts.

## 4.4.13 - 2024-05-29
- **Enhancements:** Made it possible to add widgets to the sidebar on the course page.

## 4.4.12 - 2024-05-28
- **Fix**: Announcement content did not wrap to the next line in Email.
- **Fix**: The buy button on the bundled courses page was not visible.

## 4.4.11 - 2024-05-23
- **New**: Added seven new styles for Course pages: Timeless, Sleek with Sidebar, Dynamic, Minimalistic, Modern with Curriculum, Dynamic with Short Sidebar, Bold with Full With Cover.

## 4.4.10 - 2024-05-16
- **Enhancements**: Optimized the speed of the course bundles.

## 4.4.9 - 2024-05-06
- **Enhancements**: Added AJAX Loading for Assignments and Certificates pages in LMS.
- **Fix**: Small bug fixes.

## 4.4.8 - 2024-05-01
- **New**: Added new Basic info, Course requirements and Intended audience blocks for the course in the Course Builder.

## 4.4.7 - 2024-04-29
- **Enhancements**: Course Player is fully optimized for HTTP requests, database queries and loading assets.
- **Enhancements**: Video lessons and quizzes have been optimized.
- **Enhancements**: Zoom, live stream lessons and assignments have been optimized.
- **Enhancements**: Removed old Course Builder nuxy files
- **Enhancements**: Removed old Course Player.
- **Fix**: Course Builder gets warnings from Certificate Builder.
- **Fix**: Students could not finish the lesson due to exceeding the maximum number in the lesson ID.

## 4.4.6 - 2024-04-25
- **Fix**: Removed linear icons and replaced them with IcoMoon for optimization.

## 4.4.5 - 2024-04-15
- **Enhancements**: When a course is in draft or pending review, the Start Course button for both instructor and admin opens the course in preview.
- **Enhancements**: Changed the logic of URLs in courses.

## 4.4.4 - 2024-04-08
- **Fix**: If there are no popups or signup forms on the page when a guest tries to sign up using a referral link, the referral link option does not work.
- **Fix**: The certificate tab is not displayed for a student.

## 4.4.3 - 2024-04-04
- **Enhancements**: Added filter on instructor names in Certificate builder for admin.
- **Enhancements**: Added ability for instructors to create certificates.
- **Enhancements**: Added a separate page with certificates that instructors have created themselves.
- **Enhancements**: Added certificate list in the admin panel, where admin manages all certificates and instructor can see and edit delete only certificates created by him.
- **Enhancements**: Added filter for changing custom field in certificate builder.
- **Fix**: Videos embedded with iframes in lessons and account emails using the Email Manager aren't displaying correctly.
- **Fix**: Certificate error is highlighted in Course Player when Query Monitor is enabled.

## 4.4.2 - 2024-04-01
- **Fix**: Students who have purchased a pre-ordered upcoming course can take the course via Curriculum.

## 4.4.1 - 2024-03-20
- **Enhancements**: Added an option to display coowned courses in the instructor's public profile.
- **Enhancements**: Updated the view on the page of quizzes created with the Online Testing addon.
- **Fix**: Student progress is not displayed for the group creator.
- **Fix**: Student files from Assignments are displayed in the admin's media library.

## 4.4.0 - 2024-03-14
- **Enhancements**: Added a new Certificate builder.
- **Enhancements**: Added sidebar for selecting certificate templates.
- **Enhancements**: Added ability to insert different elements such as course time, description, progress, etc.
- **Enhancements**: It is possible to add a certificate separately to a course or to a category of courses.

## 4.3.9 - 2024-02-29
- **New**: Added a new Social Login addon.
- **Enhancements**: Added Loader to show audio and video saving process in percentage.
- **Fix**: Add Enrolled Students as Attendees option is not available when creating a Google Meet lesson from Course Builder.

## 4.3.8 - 2024-02-23
- **Enhancements**: Improved performance and speed of Course Player by conducting query optimization.

## 4.3.7 - 2024-02-20
- **Enhancements**: Added the ability for students to save draft assignments.
- **Enhancements**: Audio/video question playback now stops when a user makes a second audio/video playback.
- **Enhancements**: Simplified the activation of the Question Media addon.
- **Fix**: The discounted course price is not displayed correctly in the Course Bundle.
- **Fix**: Single Course Co-Instructors undefined key avatar error is fixed.

## 4.3.6 - 2024-02-14
- **NEW**: Added a new Question Media addon.
- **NEW**: Now it is possible to add video, audio and pictures to questions in a quiz.

## 4.3.5 - 2024-01-29
- **FIXED**: Failed to translate additional fields to WPML added via LMS Form editor.
- **Enhancements**: Added required attribute to each checkbox field in the LMS Form editor.
- **Enhancements**: Now if you click a window outside the popup for forms the popup is hidden.
- **Enhancements**: Added form-builder-fields component.

## 4.3.4 - 2024-01-24
- **FIXED**: Downloading a certificate only works in the courses to which the certificate is linked.

## 4.3.3 - 2024-01-15
- **Enhancements**: The upcoming course will have its own badge in the admin panel.
- **Enhancements**: If the admin logs into the Manage Course upcoming course, he will see the student's subscription date.
- **FIXED**: If sections are swapped when "Lock lessons in order" is enabled in the Drip Content addon, the sections do not open correctly.
- **FIXED**: If the Course expiration in the Access section was set for an upcoming course, the days for expiration were accrued from the date the course was offered to the student.
- **FIXED**: If a student wants to buy a course again after the course has expired, the course is not purchased.
- **FIXED**: The words Earnings and Sales on the Payouts page are not translated.

## 4.3.2 - 2024-01-04
- **UPD**: Added Guest access to trial courses setting.
- **UPD**: Added dropdown for sections in the Curriculum section.
- **DEV**: Changed completely the templates stm-lms-templates/course/classic/parts/tabs/curriculum.php, stm-lms-templates/course/udemy/parts/tabs/curriculum.php
- **FIXED**: Preview is not displayed in a course that has Trial Course enabled and any lesson has the Lesson Preview option enabled.
- **FIXED**: A warning appears next to the Add New Bundle title.
- **FIXED**: Courses imported from Udemy come with the curriculum displayed incorrectly.
- **FIXED**: Small bug fixes.

## 4.3.1 - 2023-12-22
- **UPD**: The buttons of Passed and Failed statuses when grading a task were made in different colors: green and red.
- **UPD**: Added the "back" button from the Student Assignments table to Assignments for Instructor account.
- **UPD**: Added the ability to minimize and maximize the student response within the Course player.
- **UPD**: Removed the pagination under the dashboard of the Assignments, if the number of Assignments is small.
- **FIXED**: Quiz time countdown goes to minus in the Online Testing addon for guest users.
- **FIXED**: When going to the second page in Assignments, the page name in the link changes to Page not found.
- **FIXED**: If the user changes the user-account page to a custom one in the LMS Settings and try to access the Student assignment with the old account, the user will be redirected to the old user account and shown a 404 page. 

## 4.3.0 - 2023-12-14
- **UPD**: Added a new feature to make video and audio recordings in Assignments.
- **UPD**: Added a new feature to add emojis for the passed or failed assignments.
- **UPD**: Added a new setting to enable file uploads in the Assignment addon settings.
- **UPD**: Improved the dashboard for the assignments in the admin panel and for the Instructor account.

## 4.2.5 - 2023-12-07
- **FIXED**: Minor bug fixes

## 4.2.4 - 2023-12-07
- **FIXED**: Minor bug fixes

## 4.2.3 - 2023-12-05
- **FIXED**: If a Paid Membership plan with categories is created and a student purchases that course, the course purchased through Woocheckout is not available to take.
- **FIXED**: Courses purchased through Woo Checkout will not appear in My Orders in the student's profile.
- **FIXED**: The purchased course is not added to Enrolled Courses when paying via Woo Checkout.

## 4.2.2 - 2023-11-29
- **UPD**: Redesigned the popup for the "Buy for a group" form.

## 4.2.1 - 2023-11-24
- **FIXED**: Course details and the Start Course button are not displayed after the upcoming course timer has expired.

## 4.2.0 - 2023-11-23
- **NEW**: Added new Upcoming course status addon.
- **FIXED**: Students cannot buy courses with Points.
- **FIXED**: If a custom name is specified for the points, the default name Coin is shown.

## 4.1.6 - 2023-11-16
- **FIXED**: Replaced links in the Edit button in Udemy Importer addon settings.

## 4.1.5 - 2023-11-08
- **UPD**: Added badge for trial lessons in Course Player.
- **FIXED**: A student is removed from an enrolled course after canceling a duplicate order in Woocommerce.
- **FIXED**: If Premoderation is enabled, the course from the instructor is published immediately.
- **FIXED**: Bundles and Group courses are removed after canceling a duplicate order in Woocommerce.

## 4.1.4 - 2023-11-02
- **FIXED**: The number of lessons is not available in the Modern Course Layout.
- **FIXED**: If the Email Manager addon is enabled and a student is added via Manage Students, he receives an email from the Enterprise template.
- **FIXED**: With the timezones like +1:30, and +2:30 (i.e. not integers), Drip Content is not working either by date or by day.
- **FIXED**: Small bug fixes.

## 4.1.3 - 2023-10-20
- **FIXED**: Draft Assignment is displayed to students in My Assignments.

## 4.1.2 - 2023-10-16
- **FIXED**: Curriculum icons do not match the course builder icons.

## 4.1.1 - 2023-10-12
- **UPD**: Integrated Media File Manager addon for uploading picture via Tiny MCE

## 4.1.0 - 2023-10-10
- **UPD**: New Course Player.
- **UPD**: New student view interface.
- **UPD**: Switch between Light and Dark Themes.
- **UPD**: Enhanced Mobile-first view for students.
- **UPD**: New view for Item Match and Image Match.

## 4.0.17 - 2023-10-03
- **FIXED**: The certificate elements (start of course, end of course and current date) did not take the date format setting from WP.
- **FIXED**: If you enable the First Name and Last Name fields via the Forms Editor addon in the registration form and enter data there, the account fields are empty.
- **FIXED**: If the student or instructor's name is changed, a new certificate is issued each time a download is made.
- **FIXED**: One ID is shown in certificates for all users.

## 4.0.16 - 2023-09-18
- **UPD**: Email a student who left a comment in the Discussions section if their comment was replied to.
- **UPD**: The course information on the course page is centered on the left edge when viewed on mobile.
- **FIXED**: Email is sent in plain text template when sending a quiz without saving, although email branding is enabled.
- **FIXED**: The picture does not load in a question when the Media File Manager Addon is enabled.

## 4.0.15 - 2023-09-11
- **FIXED**: Small bug fixes

## 4.0.14 - 2023-09-05
- **FIXED**: After creating a Meeting in the Google Meet form, the page should not reload
- **FIXED**: When there were more than 40 files in the Media Library, the window showed only 40 of them

## 4.0.13 - 2023-08-22
- **FIXED**: After completing the quiz, the Quiz Complete email should include the result of the quiz
- **FIXED**: Unlock the Lesson After a Certain Time feature cannot be disabled
- **FIXED**: Password for Zoom lesson is not applied when instructor creates lesson
- **FIXED**: The Header was incorrectly positioned in the Email Branding
- **FIXED**: Templates are not showing correctly in the Email Branding

## 4.0.12 - 2023-08-14
- **UPD**: When Modern Style is activated in the Udemy template, users have the option to customize additional fields
- **UPD**: Took measures to prevent conflicts in the Udemy template between data in the course builder and data with Udemy
- **FIXED**: Gradebook shows 0 for students in the course, percentage of completion of lessons, turning in assignments, exams, and so on

## 4.0.11 - 2023-08-10
- **UPD**: Compatibility with WordPress 6.3
- **FIXED**: Become Instructor Form could not be submitted when checkbox fields were used
- **FIXED**: Google Classroom is not opening the required classes
- **FIXED**: Need to show Enter your position in the Position field
- **FIXED**: There was no sign or notification for required fields in Forms Editor
- **FIXED**: If the required fields are not filled in, the Become Instructor form should not be submitted

## 4.0.10 - 2023-08-04
- **UPD**: Implemented Guest Checkout functionality for Bundles addon
- **UPD**: Added Event Visibility settings for rallies in Google Meet
- **FIXED**: When selecting a certificate in Course Builder, only the last 5 created were shown
- **FIXED**: Scorm file of large size was not linked to the course
- **FIXED**: The Head part of email templates had a text/plain tag and emails were broken
- **FIXED**: Instructor Paypal Email was not saved in Statistics & Payouts addon in PHP 8.0

## 4.0.9 - 2023-07-19
- **UPD**: Added a separate page for creating a Google Meet on the new Course Builder 
- **FIXED**: Google Meet was not working with PHP 7.4

## 4.0.8 - 2023-07-18
- **UPD**: Updated Google API Client in the plugin to the latest version
- **FIXED**: Fixed PHP Deprecated Bugs

## 4.0.7 - 2023-07-11
- **UPD**: Transferred all the hooks from the old Course Builder to the new one
- **UPD**: Added a new type of admin notification popups
- **FIXED**: Udemy courses can't be imported 
- **FIXED**: The text inside the notice is centered
- **FIXED**: There were warning errors on the course page when email branding was enabled
- **FIXED**: Lesson Content and Lesson Materials fields for Stream Lesson and Zoom Conference are not working.
- **FIXED**: Media Library Addon was not working in MasterStudy Pro 4.0.0

## 4.0.6 - 2023-07-05
- **UPD**: Freemius SDK updated to 2.5.10

## 4.0.5 - 2022-06-27
- **FIXED**: PHP Errors fixed for older versions of MasterStudy LMS Free plugin.

## 4.0.4 - 2022-06-26
- **UPD**: Added Google Meet addon to integrate with video conferencing platform developed by Google.

## 4.0.3 - 2022-06-08
- **UPD**: The Gradebook addon refactored.

## 4.0.2 - 2022-06-02
- **FIXED**: The certificate did not display the end date of SCORM courses.

## 4.0.1 - 2022-05-16
- **UPD**: The recommended size of the certificate background increased to 1600х1050.

## 4.0.0 - 2022-05-08
- **NEW**: Course Builder with new design and a range of enhancements released.
- **UPD**: Add a search box to the page with the add-ons list.
- **FIXED**: Group leaders could not add more than 2 users to the Enterprise groups.

## 3.9.8 - 2022-02-08
- **FIXED**: Trial Lessons are not loaded for Guest users.
- **FIXED**: Drip Content is not linked with Assignments.

## 3.9.7 - 2022-01-16
- **NEW**: Send Test Email option is added to Email manager addon.
- **NEW**: Sender Name & Sender Email options added for  Email manager addon.

## 3.9.6 - 2022-01-10
- **NEW**: Pagination for Assignments & My Assignments Pages.

## 3.9.5 - 2022-12-29
- **NEW**: Email will be sent to Students when Quiz Successfully completed.
- **NEW**: Email Event for Administrator and user when the Course is enrolled through a Membership plan.
- **UPD**: The content of the default Email Events is replaced.

## 3.9.4 - 2022-12-20
- **FIXED**: Instructors couldn't set decimal prices for Courses Bundle.

## 3.9.3 - 2022-12-14
- **FIXED**: Minor bug fixes

## 3.9.2 - 2022-12-09
- **NEW**: Email Branding option is added to the Email manager addon.
- **NEW**: WISWIG editor is added to create custom HTML email templates.
- **FIXED**: When courses were added to the user, the site administrator did not receive an Email.
- **FIXED**: The user did not receive an email, after being added to the group.

## 3.9.1 - 2022-11-11
- **FIXED**: Minor bug fixes

## 3.9.0 - 2022-10-31
- **UPD**: Compatibility with WordPress 6.1

## 3.8.9 - 2022-10-26
- **FIXED**: Video Lesson fields appearance issue fixed.

## 3.8.8 - 2022-10-26
- **FIXED**: Media File Manager Popup did not open on third-party themes.

## 3.8.7 - 2022-10-14
- **NEW**: Preloader added to LMS pages and sections.
- **UPDATE:** Visual improvements in MasterStudy LMS Wizard.

## 3.8.6 - 2022-10-07
- **UPD**: Free version refactoring implemented.

## 3.8.5 - 2022-09-27
- **UPD**: String translations in plugins and Nuxy are updated.
- **FIXED**: LMS form editor elements cannot be translated.
- **FIXED**: Placeholder text 'Search' in file manager cannot be translated.
- **FIXED**: Instructor received email notification "Instructor added Course" instead of Administrator.
- **FIXED**: PHP warnings on the Courses bundle page when Debugging was enabled.
- **FIXED**: The course could be bought using coins, although the coin price is not set.
- **FIXED**: When the discount period expires, the sale price is always displayed, when WooCommerce checkout is used.
- **FIXED**: Minor bug fixes.

## 3.8.4 - 2022-09-14
- **FIXED**: Announcement menu item not displayed in instructors user account and floating menu.

## 3.8.3 - 2022-09-09
- **FIXED**: Installing Free Plugin issue fixed.

## 3.8.2 - 2022-08-30
- **NEW**: Quick premium support button in WP dashboard (for applying the issue tickets) and personal support account creation. 
- **UPD**: 'stm_lms_float_menu_items' filter renamed to 'stm_lms_menu_items. Extra arguments removed. 
- **UPD**: Directories Structure refactored.
- **FIXED**: Certificate builder elements are moved scarcely in Firefox.

## 3.8.1 - 2022-08-17
- **NEW**: Added new font for Certificate builder "Amiri" (Arabic) to display all symbols in Arabic.  
- **FIXED**: Using a single quote (') in the Certificate Builder break the certificate layout.
- **FIXED**: Failed to enter a price with a decimal separator when creating a course from the front.

## 3.8.0 - 2022-08-08
- **FIXED**: The custom fields created through the LMS form editor cannot be used in the LMS email manager.
- **FIXED**: Checkbox fields created using the LMS Forms editor, reset in a user profile.
- **FIXED**: The Wishlisted Bundle courses are not displayed on the Favourite courses page.
- **FIXED**: Fatal errors when the WPML plugin is active.

## 3.7.9 - 2022-06-23
- **FIXED**: Certificate Builder issue with RTL 
- **FIXED**: Even when membership expired, the course is still listed under "enrolled courses."
- **FIXED**: Password field is duplicated when creating a Zoom Conference lesson
- **FIXED**: Some strings were not visible for translation 

## 3.7.8 - 2022-06-09
- **NEW**: Reordering of the Profile Menu Items
- **FIXED**: Uploaded SCORM course couldn't be deleted
- **FIXED**: Course Bundles style issue with default WordPress themes
- **FIXED**: Curriculum lesson preview arrow icon issue in classic course style

## 3.7.7 - 2022-05-04
- **UPD**: Color Scheme Appliance

## 3.7.6 - 2022-04-06
- **UPD**: Spanish and Portuguese (additional) strings are added

## 3.7.5 - 2022-04-06
- **FIXED**: Adding the category to the  created questions in front end builder
- **FIXED**: Related Courses block is not displaying in Udemy Style

## 3.7.4 - 2022-03-24
- **FIXED**: 'Enroll with Membership' button in Get Course button is shown even when the plans are not created and configured

## 3.7.3 - 2022-03-17
- **UPD**: Remastering of Add New Course interface and Single Course page in mobile version

## 3.7.2 - 2022-03-07
- **FIXED**: Minor bug with style code displaying in Statistics section on dashboard
- **FIXED**: Course category tabs are not visible on Course page with activated Udemy style
- **FIXED**: In the quiz section, when a question or answer is too long, it overlaps
- **FIXED**: Prerequisites addon does not work, when course style is Default
- **FIXED**: Some Pro version areas and fields are active in Course Settings (backend) of MasterStudy LMS Free version

## 3.7.1 - 2022-03-01
- **UPD**: Security update

## 3.7.0 - 2022-02-14
- **FIXED**: System allows to add participants more than the specified limit after creating a group through Buy for group button (Group Courses addon)
- **FIXED**: Single Course cover image is not displaying in Modern Course Style
- **FIXED**: Bug with custom fields in Forms Editor addon
- **FIXED**: Text under the countdown is not showing on Zoom Meeting Lesson
- **FIXED**: Featured Course covers are not displaying after selecting Modern style for the Courses page
- **FIXED**: For students notification is not showing after checking the student's assignments by instructor
- **FIXED**: Removed Add New button in Student Assignments section on dashboard
- **FIXED**: Default fields dislocation  in Profile From of LMS Forms Editor addon
- **FIXED**: Issue with generating of First Name and Last Name fields on Certificate Builder addon
- **FIXED**: Bug with custom checkboxes in LMS Forms Editor addon

## 3.6.9 - 2022-01-13
- **NEW**: Media File Manager Addon
- **FIXED**: Option for defining the displayed course bundles in STM Courses Bundles Settings Widget
- **FIXED**: Announcement is not displaying on course modern style page

## 3.6.8 - 2021-12-20
- **FIXED**: Bug with missed the inserted lesson images on frontend course builder was fixed
- **FIXED**: Missed icons on the list of available lessons while creating the course in frontend builder was fixed

## 3.6.7 - 2021-11-25
- **UPD**: Udemy style renamed to Modern Style
- **UPD**: Modern Style is active by default
- **FIXED**: SCORM courses uploading issue
- **FIXED**: Certificates date translation issue
- **FIXED**:  Wizard navigation menu issue

## 3.6.6 - 2021-10-18
- **UPD**: Code refactoring and optimization

## 3.6.5 - 2021-09-21
- **ADDED**: New Quiz type: Image Matching (Single Choice, Multi Choice)
- **FIXED**: SCORM courses loading bug

## 3.6.4 - 2021-09-02
- **ADDED**: New Pro Addons page
- **FIXED**: Minor bug fixes

## 3.6.3 - 2021-08-12
- **ADDED**: Admin Dashboard notification

## 3.6.2 - 2021-08-02
- **FIXED**: Students did not get a reply email when assignment status changed
- **FIXED**: Video Duration icon displaying when Video Duration field is blank in Udemy Affiliate layout
- **FIXED**: The same course materials attaching several times in the Udemy Affiliate layout
- **FIXED**: Membership Approval (Paid Membership Pro plugin ) compatibility
- **FIXED**: Translation issues with several non-editable strings

## 3.6.1 - 2021-07-28
- **UPD**: Compatibility with WordPress v. 5.8
- **UPD**: Affiliate Link Block in Instructor/Student Profile
- **FIXED**: Co-instructors list was not fully displayed by adding the co-instructor from the frontend
- **FIXED**: Course Bundles fatal error
- **FIXED**: Translation issues with several non-editable strings

## 3.6.0 - 2021-06-15
- **UPD**: BuddyPress templates according to theme new features and rules
- **FIXED**: Minor style issues and bugs

## 3.5.9 - 2021-06-10
- **UPD**: Redesigned user account and user public profile
- **ADDED**: New floating menu for user account
- **FIXED**: Checkbox doubling in forms created by LMS Forms Editor in User Profile settings
- **FIXED**: Drop-down selection in forms created by LMS Forms Editor in User Profile Settings

## 3.5.8 - 2021-06-01
- **FIXED**: Courses appearance by categories bug

## 3.5.7 - 2021-05-12
- **FIXED**: Hiding all post types and media if WooCommerce plugin is not activated

## 3.5.6 - 2021-05-11
- **ADDED**: Full integration with WooCommerce
- **FIXED**: Error sending mail with different product types in WooCommerce
- **FIXED**: Minor styling issues

## 3.5.5 - 2021-05-03
- **FIXED**: Chinese, Arabic and some other fonts aren't displaying in Certificate
- **FIXED**: Certificate Title entities in Certificate builder
- **FIXED**: Double adding student to course
- **FIXED**: Buy button functionality when clicking in modal
- **FIXED**: Minor bug and frontend fixes
- **ADDED**: Compatibility with eRoom v1.1.5

## 3.5.4 - 2021-03-30
- ADDED: LMS Forms Editor addon
- FIXED: Broken order of the categories and subcategories in Frontend Course Builder
- FIXED: Broken categories dropdown in Frontend Course Builder (French letters)
- FIXED: Bundle image error when using MasterStudy PRO without MasterStudy theme
- FIXED: Drip content error  when using MasterStudy PRO without MasterStudy theme
- FIXED: Course description bug in Frontend Course Builder
- FIXED: Point System shows zero value
- FIXED: Points are not credited after enabling the Point statistics add-on
- FIXED: Zero values in point statistics settings
- FIXED: Minor frontend bugs
- UPDATE: Profile style "classic" moved to PRO
- UPDATE: Cancel Subscription in Classic profile style moved to sidebar
- ADDED: Image preview to Assignments

## 3.5.3 - 2021-02-19
- ADDED: Added action before announcement send

## 3.5.2 - 2021-02-04
- FIXED: Certificate Template name editing bug
- FIXED: Assignment pagination 
- FIXED: Draft courses excluded from Gradebook
- FIXED: Manage course notice about disabled tabs 
- FIXED: Drip content dependencies 
- UPDATED: Settings style now built on CFTO framework

## 3.5.1 - 2021-01-11
- ADDED: Randomize questions option to frontend course builder
- FIXED: Adding video lesson error on frontend course builder
- UPDATE: Added time zones for zoom conference lessons
- FIXED: SCORM course doesn't start
- FIXED: Frontend bugs in public profile
- FIXED: Assignments statistics errors in gradebook
- FIXED: Course bundle description field

## 3.5.0 - 2020-12-28
- ADDED: Quiz Randomize option for frontend course builder
- UPDATE: Replace routes by creating a WordPress pages
- FIXED: Assignment count is not displayed correctly
- FIXED: WPML Language switcher issues
- FIXED: Assignment long name issue
- FIXED: Course Bundle description field issue  

## 3.4.6 - 2020-12-07
- ADDED: Student assignment can now be managed by admin from WordPress dashboard
- ADDED: Added course title in a lesson comment (Email manager)
- ADDED: Added login, course title and assignment title in user assignment submission (Email manager)
- ADDED: Added setting for assignment upload extension
- UPDATE: Assignment send button disabled if assignment is empty
- FIXED: Stream +1 day bug fixed
- FIXED: Co-courses bug fixed when disabled

## 3.4.5
- FIXED: Point system conflict with one time purchase
- IMPROVEMENT: Field for custom point price added
- IMPROVEMENT: Co-instructor courses added to the Instructor's public profile

## 3.4.4
- IMPROVEMENT: Permission to add file formats for assignments:mp4, m4v, mov, wmv, avi, mpg
- FIXED: Date in Zoom lesson
- FIXED: Disabled content tab on the frontend course builder for Zoom lessons
- FIXED: Changed lesson duration field type

## 3.4.3
- ADDED: New fields in Certificate builder (course start date, course end date, current date, progress, co-instructor, course details)
- ADDED: Certificate linking with a specific course.
- ADDED: Items centering in Certificates
- ADDED: Course materials to Udemy style
- ADDED: Password field for Zoom lessons 
- FIXED: Course purchase in subscription


## 3.4.2
- Course expiration feature added
- 'permission_callback' => '__return_true' wp rest api added
- Freemius SDK refactoring
- Readme file added
- Negative price when adding course fixed
- Added allowedfullscreen tag to wp_kses allowed tags

## 3.4.1
- Course sidebar button item id error fixed
- Guest checkout button fixed

## 3.4.0
- Added Certificate builder
- Manage course duration field save fixed
- Course bundle course featured image fixed 
- Frontend course editor several image adding URL fixed
- Frontend course editor now has unique class in <body tag
- Price position in sticky panel fixed

## 3.3.0
- Added Drafts feature to frontend course editor
- Related courses in Classic course style
- Auto-enroll for free course fixed

## 3.2.0
- Frontend Course editor UI/UX fields reworked
- Addons global settings design changed 

## 3.1.1
- Minor visual bugfix

## 3.1.0
- Added the ability to upload videos to video lessons
- Added video player in the lesson with the ability to restart from the point you stopped at.

## 3.0.0
- Feature: Added Shortcodes (`stm_lms_certificate_checker`, 
`stm_lms_course_bundles`, `stm_lms_google_classroom`)
- Feature: Added Elementor widgets (`stm_lms_certificate_checker`, 
`stm_lms_course_bundles`, `stm_lms_google_classroom`)
- Fixed: Live stream timer
- Fixed: Point history broken link

## 2.4.1
- Accepting draft assignments fixed

## 2.4.0 
- Scorm zip file uploading fixed
- Email sending for instructor and student fixed
- Visual bugs fixed
- Lesson question time showing for user fixed
- Logout now redirects to login page
- Russian language updated

## 1.7.0  
- Lesson Style Classic added.
- Email Template Manager added.
- Assign Students to Course added.
- Gradebook – Students progress fixes.
- Assignments – Vertical Scroll removed.
- Assignments – Sending Email Notifications bug fixed.
- SCORM – Curriculum disabling issue fixed.

## 1.6.1  
- Minor bug fixes.

## 1.6
- New Course Attachments added.
- New Course Finished Statistics added.
- Minor bug fixes.

## 1.5 
- New Courses Filter added.
- New Instructor menu added.
- Minor bug fixes.

## 1.4.7  
- SCORM Courses integration added.
- Minor bug fixes.

## 1.4.6 
- Minor bug fixes.

## 1.4.5 
- ZOOM Conference feature added.

## 1.4.4  
- Google Classroom feature added.
- Minor bug fixes.

## 1.4.3  
- Minor bug fixes.

## 1.4.1  
- Multi-instructors feature added.
- Unique code for certificates feature added (with Code checker WPBakery Page Builder element).
- Instructors can create Course Categories.
- Minor bug fixes.

## 1.4.0  
- Course Bundle feature added.

## 1.3.9 
- Questions Bank system added.
- Course Affiliate Link feature added.
- Minor bug fixes.

## 1.3.8  
- Points system added.

## 1.3.7  
- Assignments feature added.
- Minor bug fixes.

## 1.3.6  
- Group (Team) Courses feature added.
- Minor bug fixes.

## 1.3.5  
- Forgot Password problem fixed.
- Currency symbol issue fixed.
- Minor bug fixes.

## 1.3.4  
- Lessons Live Streaming feature added.
- Minor bug fixes.

## 1.3.3  
- Drip Content feature added.
- Sequential Lessons feature added.
- The GradeBook feature added.

## 1.3.2  
- Minor bug fixes.

## 1.3.1  
- Added new question types.
- Minor bug fixes.

## 1.3  
- Prerequisites problem fixed.
- WPML Switcher fixed.
- Minor bug fixes.

## 1.2  
- Minor bug fixes.

## 1.1  
- Teacher Course Sales & Earnings Statistics added.
- Automatic Payouts feature added.

## 1.0  
- Release.
