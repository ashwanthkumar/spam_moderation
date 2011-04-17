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
 *	@date 17/04/2011 - Last Updated
 */

elgg_register_event_handler('init', 'system', 'spam_moderation_init');
elgg_register_event_handler('create', 'object', 'check_for_spam');
elgg_register_event_handler('update', 'object', 'check_for_spam'); // -- Called twice.. Shouldn't be a pblm i guess 
elgg_register_event_handler('create', 'annotation', 'check_for_spam_annotation');

// @todo complete it, with more types
function check_for_spam_annotation($event, $type, $entity) {
	$service = elgg_get_plugin_setting('spam_service', 'spam_moderation');
	
	if($entity->name == "group_topic_post") {
		return check_for_group_topic_reply($entity,$service);
	}
	// @todo More type of annotations to be checked
}

/**
 *	Entry point for SPAM checking module. Determines the type of Object and calls the corresponding function.
 *	
 *	@TODO: Add check for more type of objects
 **/
function check_for_spam($event, $type, $entity) {
	$service = elgg_get_plugin_setting('spam_service', 'spam_moderation');
	
	if (elgg_instanceof($entity, 'object', 'blog')) { // blog post checking
		return check_for_spam_in_blog($entity,$service);
	} else if(elgg_instanceof($entity, 'object', 'groupforumtopic')) { // Forum topic creation check
		return check_for_spam_in_groupforumtopic_reply($entity,$service);
	} 
	/*
		More types shall we added upon request or you're free to do it yourself :) 
	*/
}

/**
 *	Initialize the SPAM Moderation plugin
 **/
function spam_moderation_init() {

	// require libraries
	$base = elgg_get_plugins_path() . 'spam_moderation';
	elgg_register_library('spam_moderation', "$base/lib/spam_moderation.php");
	
	elgg_register_library('akismet_php', "$base/vendors/akismet/Akismet.class.php");
	elgg_register_library('mollom_php', "$base/vendors/mollom/Mollom.class.php");

	elgg_load_library('spam_moderation');
	elgg_load_library('akismet_php');
	elgg_load_library('mollom_php');
	
	// @TODO
	// allow plugin authors to hook into this service
	// elgg_register_plugin_hook_handler('spam_check_akismet', 'spam_moderation', 'spam_check_akismet');
}

/**
 * Service to check if a content contains spam from Akismet.
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 */
function spam_check_akismet($hook, $entity_type, $returnvalue, $params) {
	// @TODO
}
