<?php
// $Id: views-view.tpl.php,v 1.13 2009/06/02 19:30:44 merlinofchaos Exp $
/**
 * @file views-view.tpl.php
 * Main view template
 *
 * Variables available:
 * - $css_name: A css-safe version of the view name.
 * - $header: The view header
 * - $footer: The view footer
 * - $rows: The results of the view query, if any
 * - $empty: The empty text to display if the view is empty
 * - $pager: The pager next/prev links to display, if any
 * - $exposed: Exposed widget form/info to display
 * - $feed_icon: Feed icon to display, if any
 * - $more: A link to view more, if any
 * - $admin_links: A rendered list of administrative links
 * - $admin_links_raw: A list of administrative links suitable for theme('links')
 *
 * @ingroup views_templates
 */
 
 global $user;
 
 if(arg(1))
 {
   
   $temp_user = user_load(array('name' => arg(1)));
   $user_uid = $temp_user->uid;
 }
 else
 {
  $user_uid = $user->uid;
 }
 
 //Check user privacy settings of " Following " List.
	$sel_following  =  db_query("SELECT value as following_sel, count(*) as following_count FROM {profile_values} WHERE fid=%d AND uid=%d", 15, $user_uid);
    $fetch_following = db_fetch_object($sel_following);
				   
	 ($fetch_following->following_sel) ? $select_following = $fetch_following->following_sel : $select_following = 0;
	 if($fetch_following->following_count==0) 
	   $select_following = 1;

if($select_following==1 || arg(1)=="")
 {
	   
?>
<div class="view view-<?php print $css_name; ?> view-id-<?php print $name; ?> view-display-id-<?php print $display_id; ?> view-dom-id-<?php print $dom_id; ?>">
  <?php if ($admin_links): ?>
    <div class="views-admin-links views-hide">
      <?php print $admin_links; ?>
    </div>
  <?php endif; ?>
  <?php if ($header): ?>
    <div class="view-header">
      <?php print $header; ?>
    </div>
  <?php endif; ?>

  <?php if ($exposed): ?>
    <div class="view-filters">
      <?php print $exposed; ?>
    </div>
  <?php endif; ?>

  <?php if ($attachment_before): ?>
    <div class="attachment attachment-before">
      <?php print $attachment_before; ?>
    </div>
  <?php endif; ?>
  <?php
 if($select_following==1)
 {
  ?>

  <?php if ($rows): ?>
    <div class="view-content">
      <?php print $rows; ?>
	    <?php if ($pager): ?>
         <div class="hline"></div>
         <?php endif; ?>
    </div>
  <?php elseif ($empty): ?>
    <div class="view-empty">
      <?php print $empty; ?>
    </div>
  <?php endif; ?>

  <?php if ($pager): ?>
    <?php print $pager; ?>
  <?php endif; ?>
<?php
}
else
{
?>
    <div class="view-content" align="center" style="color:#999999">
	<br /><br /><br /><br />
	<?php
	 if(arg(1))
    {
	?>
	 User is hiding <br />the " Following List ".
	<?php
	}
	else
	{
	?>
	 You are hiding <br />the " Following List ".
	<?php
	}
	?>
    
    </div>
<?php
}
?>
  <?php if ($attachment_after): ?>
    <div class="attachment attachment-after">
      <?php print $attachment_after; ?>
    </div>
  <?php endif; ?>

  <?php if ($more): ?>
    <?php print $more; ?>
  <?php endif; ?>

  <?php if ($footer): ?>
    <div class="view-footer">
      <?php print $footer; ?>
    </div>
  <?php endif; ?>

  <?php if ($feed_icon): ?>
    <div class="feed-icon">
      <?php print $feed_icon; ?>
    </div>
  <?php endif; ?>

</div> <?php /* class view */ ?>
<?php
}
?>