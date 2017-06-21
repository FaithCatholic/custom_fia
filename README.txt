Custom Facebook Instant Articles is a small module that enables a views RSS feed to display items as an instant article.

1. Install the module as usual, and make sure the configuring user has the 'administer site configuration' permission. (Setting this permission is not necessary for super-users with pre-existing admin privileges.)

2. Create a new display view mode for node content under /admin/structure/display-modes/view (Currently this module only works with nodes.)

3. Enable the previously-created display mode for the relevant content type under /admin/structure/types/manage/TYPE. Set custom field formatters where appropriate.

4. Enter the Facebook page ID, the previously-set view mode, and set any appropriate field mappings at /admin/config/services/custom-fia

5. Add a feed page display to a new or existing view, then set the row type plugin to "Custom FB Fields", and set the relevant node display view mode.

6. Set an appropriate view path for the display, and provide it to Facebook for feed importation.
