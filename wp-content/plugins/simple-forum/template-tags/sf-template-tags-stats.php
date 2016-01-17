<?php
/*
Simple:Press Forum
Template Tag(s) - Stats
$LastChangedDate: 2009-07-05 17:30:23 -0700 (Sun, 05 Jul 2009) $
$Rev: 2168 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

/* 	=====================================================================================

	sf_stats_tag($onlinestats=true, $forumstats=true, $memberstats=true, $topstats=true)

	displays the most recent forum stats in sidebar format

	parameters:

		$onlinestats		show the users online						true/false
		$forumstats			show the group, forum, topic, post stats	true/false
		$memberstats		show the member stats						true/false
		$topstats			show the top poster stats					true/false

 	===================================================================================*/

function sf_stats_tag($onlinestats=true, $forumstats=true, $memberstats=true, $topstats=true)
{
	global $wpdb, $sfvars;

	sf_initialise_globals($sfvars['forumid']);

	# do the header
	$out = '';

	# if requested, output the users online stats
	if ($onlinestats)
	{
		$guests = 0;
		$label = ' '.__("Guests", "sforum");

		$online = sf_get_online_total();
		if($online)
		{
			$members = sf_get_members_online();

			$out.='<ul class="sfstatonline"><h3>'.__("Currently Online", "sforum").': </h3>'."\n";
			if($members)
			{
				foreach ($members as $user)
				{
					$out.= '<li class="sfstatadmin">'.$user->display_name.'</li>'."\n";
				}
			}
			if ($online > count($members))
			{
				$guests = ($online - count($members));
				if ($guests == 1) $label = ' '.__("Guest", "sforum");
				$out.= '<li class="sfstatguest">'.$guests.$label.'</li>'."\n";
			}
			$out.='<li class="sfstatmax">'.__("Maximum Online", "sforum").': '.sf_update_max_online(count($online)).'</li>'."\n";
			$out.='</ul>'."\n";
		}
	}

	# if requested, output the forum stats
	if ($forumstats) {
		$cnt = sf_get_stats_counts();
		$out.= '<ul class="sfstatforums"><h3>'.__("Forum Stats", "sforum").': </h3>'."\n";
		$out.= '<li class="sfstatforum">'.__("Forums: ", "sforum").'</li>'."\n";
		$out.= '<li class="sfstatgroup">'.__("Groups: ", "sforum").$cnt->groups.'</li>'."\n";
		$out.= '<li class="sfstatforum">'.__("Forums: ", "sforum").$cnt->forums.'</li>'."\n";
		$out.= '<li class="sfstattopic">'.__("Topics: ", "sforum").$cnt->topics.'</li>'."\n";
		$out.= '<li class="sfstatpost">'.__("Posts: ", "sforum").$cnt->posts.'</li>'."\n";
		$out.='</ul>'."\n";
	}

	if($memberstats || $topstats)
	{
		$sfcontrols = get_option('sfcontrols');
	}

	# if requested, output the members stats
	if ($memberstats) {
		$members = $sfcontrols['membercount'];
		$guests  = $sfcontrols['guestcount'];

		$out.= '<ul class="sfstatmembers"><h3>'.__("Members", "sforum").': </h3>'."\n";
		$out.='<li class="sfstatmembernum">'.sprintf(__("There are %s members", "sforum"), $membercount).'</li>'."\n";
		if ($guests)
		{
			$out.='<li class="sfstatguestnum">'.sprintf(__("There are %s guests", "sforum"), $guests).'</li>'."\n";
		}
		$out.='</ul>'."\n";
	}

	# if requested, output the top posters
	if ($topstats)
	{
		$stats = sf_get_post_stats();
		if ($stats)
		{
			$out.='<ul class="sfstattop"><h3>'.__("Top Posters:", "sforum").'</h3>'."\n";
			$done = 0;
			foreach ($stats as $stat)
			{
				if($stat->admin == false && $stat->moderator == false && $stat->posts > 0)
				{
					$out.='<li class="sfstattopname">'.stripslashes($stat->display_name).' - '.$stat->posts.'</li>'."\n";
					$done++;
				}
				if($done == 6) break;
			}
		$out.='</ul>'."\n";
		}
	}
	echo $out;
	return;
}

/* 	=====================================================================================

	sf_linked_topic_post_count()

	displays the number of topic posts in the currently displayed linked blog post

	parameters: None

	For use in the comments theme template

 	===================================================================================*/

function sf_linked_topic_post_count()
{
	global $wp_query;

	$result = '';
	$postid = $wp_query->post->ID;
	$checkrow = sf_blog_links_postmeta('read', $postid, '');

	if($checkrow)
	{
		# link found for this post
		$keys = explode('@', $checkrow->meta_value);
		$result = sf_get_posts_count_in_topic($keys[1]);
	}
	echo $result;
	return;
}


?>