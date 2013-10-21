<h2><?php _e( 'Memberful debug information', 'memberful' ) ?></h2>

<pre><code style="display:block;">
Generated on: <?php echo date("Y-m-d H:i:s O"); ?>


# Meta:
WordPress: <?php echo $wp_version ?>

Site URL: <?php echo site_url(); ?>

Home URL: <?php echo home_url(); ?>


# Plugins
<?php $chars_in_longest_name = 0; foreach($plugins as $plugin) { $chars_in_longest_name = max( $chars_in_longest_name, strlen( $plugin['Name'] ) ); } ?>
<?php foreach($plugins as $file => $plugin): ?>
<?php echo str_pad('['.(is_plugin_active( $file ) ? 'Active' : 'Inactive').'] ', 11), str_pad( $plugin['Name'], $chars_in_longest_name + 1 ), str_pad(' ('.$plugin['Version'].')', 11), '- ', $plugin['Author'], ' ', $plugin['PluginURI'] ?>

<?php endforeach; ?>

# Stats
Total users: <?php echo $total_users; ?>

Total mapping records: <?php echo $total_mapping_records ?>

Total mapped users: <?php echo $total_mapped_users ?>

Total unmapped users: <?php echo $total_unmapped_users ?>


# Config
<?php foreach($config as $key => $value): ?>
<?php echo $key; ?>: <?php echo esc_html( var_export( $value, true )); ?>

<?php endforeach; ?>

# ACL
<?php foreach($acl_for_all_posts as $post_id => $meta): ?>
<?php echo str_pad($post_id.':', 4); ?> <?php var_export($meta); ?>

<?php endforeach; ?>

# Mappings

<?php if ( $total_unmapped_users > 0 ): ?>
Unmapped users:
<?php echo str_pad('WP ID', 6), ' ', str_pad('Email', 30), ' ', 'Date registered' ?>
<?php foreach($unmapped_users as $unmapped_user): ?>

<?php echo str_pad($unmapped_user->ID, 6) ?> <?php echo str_pad($unmapped_user->user_email, 30) ?> <?php echo $unmapped_user->user_registered; ?>
<?php endforeach; ?>

<? endif; ?>

<?php if ( ! empty( $mapping_records ) ): ?>
Mapping records:
<?php echo str_pad('WP ID', 7), ' ', str_pad('Mem id', 7), ' ', str_pad('Last sync at', 32), ' ', str_pad('Refresh token', 32) ?>
<?php foreach($mapping_records as $record): ?>

<?php echo str_pad($record->wp_user_id, 7), ' ', str_pad($record->member_id, 7), ' ', str_pad(date('r', $record->last_sync_at), 32), ' ', str_pad($record->refresh_token, 32); ?>
<?php endforeach; ?>
<?php endif; ?>

</code>
</pre>

