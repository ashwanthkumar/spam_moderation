<?php
/**
 *   Copyright SpamModeration Plugin Ashwanth Kumar <ashwanthkumar@googlemail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

 /**
 * 	This service plugin allows the system to validate the content posted on the community against Akismet spam service.
 *
 *	@package SpamModeration
 *	@date 14/04/2011 - Last Updated
 */

/**
 *	Checks for blog post content SPAM
 **/
function check_for_spam_in_blog($entity) {
	$enable_akismet = elgg_get_plugin_setting('enable_akismet', 'spam_moderation');
	$enable_keyword_filter = elgg_get_plugin_setting('enable_keyword_filter', 'spam_moderation');
	
	if($enable_akismet == 'yes') {
		check_for_spam_in_blog_akismet($entity);
	}
	
	if($enable_keyword_filter == 'yes') {
		check_for_spam_in_blog_keyword_filter($entity);
	}
}

/**
 *	Checks for SPAM in blog posts using Akismet web service
 **/
function check_for_spam_in_blog_akismet($entity) {
	// Check for a valid key for the plugin
	if(!isset($api_key) || empty($api_key)) {
		register_error("API Key for Akismet is not set! Please contact the site administrator, for more details. ");
		forward(REFERER);
	}

	$site = elgg_get_site_url();
	
	$api_key = elgg_get_plugin_setting('akismet_key', 'spam_moderation');
	
	$akismet = new Akismet("$site/blog/", $api_key);
	$user = get_entity($entity->owner_guid);
	
	// Setting the content properties for better SPAM detection
	$akismet->setCommentAuthor($user->name);
	$akismet->setCommentAuthorEmail($user->email);
	$akismet->setCommentContent($entity->description);
	$akismet->setPermalink($entity->getURL());

	if($akismet->isCommentSpam()) {
		// @TODO: Provide a method to override in case of a mis-diagnosis
		register_error("Content is marked as spam and is saved as draft. You cannot publish a post which is detected as SPAM. Contact site administrator for more details. ");

		$entity->is_spam = TRUE;
		$entity->status = "draft"; // Save the post only as draft

		// Forward back to the page where it came from, with the error message. 
		forward(REFERER);
	} else {
	  // store the post normally
	  $entity->is_spam = FALSE;
	}
}

/**
 *	Checks for SPAM in blog posts based on keywords specified by the user
 **/
function check_for_spam_in_blog_keyword_filter($entity) {
	$keywords = elgg_get_plugin_setting('keyword_list','spam_moderation');
	
	// Extract the keywords out of the list
	$list = explode(',',$keywords);
	
	// Building the regular expression 
	$regxp = "/";
	foreach($list as $word) {
		$regxp = "$regxp$word|";
	}
	$regxp .= "/";
	
	// Doing the check here
	$keywords_cnt = preg_match_all($regxp,$entity->description,$filter_chk);
	
	if($keywords_cnt > 0) {
		// Keyword instances found!
		// @TODO: Provide a method to override in case of a mis-diagnosis
		register_error("Content is marked as spam and is saved as draft. You cannot publish a post which is detected as SPAM. Contact site administrator for more details. ");
		
		$entity->is_spam = TRUE;
		$entity->status = "draft"; // Save the post only as a draft
		
		forward(REFERER);
	} else {
		$entity->is_spam = FALSE;
	}
}

/**
 *	Generic SPAM Checking handler for EgllEntity type of objects
 **/
function check_for_spam_in_generic_entities($entity) {
	$keywords = elgg_get_plugin_setting('keyword_list','spam_moderation');
	
	// Extract the keywords out of the list
	$list = explode(',',$keywords);
	
	// Building the regular expression 
	$regxp = "/";
	foreach($list as $word) {
		$regxp = "$regxp$word|";
	}
	$regxp .= "/";
	
	// Doing the check here
	$keywords_cnt = preg_match_all($regxp,$entity->description,$filter_chk);
}
