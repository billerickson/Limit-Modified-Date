# Limit Modified Date

Prevent the "modified date" from changing when making minor changes to your content.

When "Don't update the modified date" is checked, saving content does not update the post modified date. This gives you more control when using `get_the_modified_date()` in your theme. Minor changes like adding tags or fixing typos won't bump the modified date.

When you're ready to publish a major content change, you can uncheck this setting before saving the post.

This plugin works with both the Gutenberg block editor and the Classic editor.

![screenshot](https://d16rm1n165bd05.cloudfront.net/items/1o2B0D0z3e1B1s3p1B0U/screenshot.jpg?X-CloudApp-Visitor-Id=78955b2d79e4b4c9650076a91b4db727&v=8e588652)


## Customization ##

This plugin only applies to the `post` post type by default. You can customize which post types it appears on using the `limit_modified_date_post_types` filter.
