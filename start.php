<?php
/**
 * SPAM Moderation
 * This service plugin allows the system to validate the content posted on the community against Akismet spam service.
 *
 * @package SpamModeration
 */

elgg_register_event_handler('init', 'system', 'spam_moderation_init');
elgg_register_event_handler('create', 'object', 'check_for_spam');
elgg_register_event_handler('update', 'object', 'check_for_spam');

/**
 *	Entry point for SPAM checking module. Determines the type of Object and calls the corresponding function.
 *	
 *	@TODO: Add check for more type of objects
 **/
function check_for_spam($event, $type, $entity) {
	if (elgg_instanceof($entity, 'object', 'blog')) {
		check_for_spam_in_blog($entity);
	} 
}

/**
 *	Checks for blog post content SPAM
 **/
function check_for_spam_in_blog($entity) {	
	$site = elgg_get_site_url();

	$api_key = elgg_get_plugin_setting('akismet_key', 'spam_moderation');

	// Check for a valid key for the plugin
	if(!isset($api_key) || empty($api_key)) {	
		register_error("API Key for Akismet is not set! Please contact the site administrator, for more details. ");
		forward(REFERER);
	} 
	
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

function spam_moderation_init() {

	// require libraries
	$base = elgg_get_plugins_path() . 'spam_moderation';
	elgg_register_library('akismet_php', "$base/vendors/akismet/Akismet.class.php");
	// elgg_register_library('spam_moderation', "$base/lib/spam_moderation.php");

	// elgg_load_library('spam_moderation');
	elgg_load_library('akismet_php');

	// @TODO
	// allow plugin authors to hook into this service
	// elgg_register_plugin_hook_handler('spam_check_akismet', 'spam_moderation', 'spam_check_akismet');
}

/**
 * Check if a content contains spam from Akismet.
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 */
function spam_check_akismet($hook, $entity_type, $returnvalue, $params) {
}
