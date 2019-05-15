# Limit Modified Date

Prevent the *modified date* from changing when making minor changes to your content.

This plugin adds a checkbox to the Publish box, "Don't update the modified date".  When checked, saving your post does not update the post modified date.

Minor changes like adding tags or fixing typos won't bump the modified date.

This plugin will *maintain* the previous modified date. It **does not** let you specify a custom modified date.

When you're ready to publish a major content change, you can uncheck this setting before saving the post.

**Gutenberg Compatible**

We are fully compatible with both the Gutenberg block editor (the new WordPress content editor) and the classic editor.

**Customization**

Your theme will need to use `get_the_modified_date()` to display the modified date.

This plugin only applies to the `post` post type by default. You can customize which post types it appears on using the `limit_modified_date_post_types` filter.

```
add_filter( 'limit_modified_date_post_types', function( $post_types ) {
	$post_types[] = 'page';
	return  $post_types;
});
```

## Screenshots

![Published and modified date shown in theme](https://d16rm1n165bd05.cloudfront.net/items/2507442p2K253A1d1Q36/Screen%20Shot%202019-05-13%20at%207.36.49%20AM.png?X-CloudApp-Visitor-Id=78955b2d79e4b4c9650076a91b4db727&v=2854da61)

![Checkbox in post editor](https://d16rm1n165bd05.cloudfront.net/items/1o2B0D0z3e1B1s3p1B0U/screenshot.jpg?X-CloudApp-Visitor-Id=78955b2d79e4b4c9650076a91b4db727&v=8e588652)
