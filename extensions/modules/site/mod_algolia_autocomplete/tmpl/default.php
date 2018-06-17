<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Module.mod_algolia_autocomplete
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

$config = $index->algoliaConfig();

$titleField = 'title';

Factory::getDocument()->addStyleDeclaration("
@import 'https://fonts.googleapis.com/css?family=Montserrat:400,700';
.aa-input-container {
	display: inline-block;
	position: relative; }
.aa-input-search {
	width: 300px;
	padding: 12px 28px 12px 12px;
	border: 2px solid #e4e4e4;
	border-radius: 4px;
	-webkit-transition: .2s;
	transition: .2s;
	font-family: 'Montserrat', sans-serif;
	box-shadow: 4px 4px 0 rgba(241, 241, 241, 0.35);
	font-size: 11px;
	box-sizing: border-box;
	color: #333;
	-webkit-appearance: none;
	-moz-appearance: none;
	appearance: none; }
	.aa-input-search::-webkit-search-decoration, .aa-input-search::-webkit-search-cancel-button, .aa-input-search::-webkit-search-results-button, .aa-input-search::-webkit-search-results-decoration {
		display: none; }
	.aa-input-search:focus {
		outline: 0;
		border-color: #3a96cf;
		box-shadow: 4px 4px 0 rgba(58, 150, 207, 0.1); }
.aa-input-icon {
	height: 16px;
	width: 16px;
	position: absolute;
	top: 50%;
	right: 16px;
	-webkit-transform: translateY(-50%);
					transform: translateY(-50%);
	fill: #e4e4e4; }
.aa-hint {
	color: #e4e4e4; }
.aa-dropdown-menu {
	background-color: #fff;
	border: 2px solid rgba(228, 228, 228, 0.6);
	border-top-width: 1px;
	font-family: 'Montserrat', sans-serif;
	width: 300px;
	margin-top: 10px;
	box-shadow: 4px 4px 0 rgba(241, 241, 241, 0.35);
	font-size: 11px;
	border-radius: 4px;
	box-sizing: border-box; }
.aa-suggestion {
	padding: 12px;
	border-top: 1px solid rgba(228, 228, 228, 0.6);
	cursor: pointer;
	-webkit-transition: .2s;
	transition: .2s;
	display: -webkit-box;
	display: -ms-flexbox;
	display: flex;
	-webkit-box-pack: justify;
			-ms-flex-pack: justify;
					justify-content: space-between;
	-webkit-box-align: center;
			-ms-flex-align: center;
					align-items: center; }
	.aa-suggestion:hover, .aa-suggestion.aa-cursor {
		background-color: rgba(241, 241, 241, 0.35); }
	.aa-suggestion > span:first-child {
		color: #333; }
	.aa-suggestion > span:last-child {
		text-transform: uppercase;
		color: #a9a9a9; }
.aa-suggestion > span:first-child em, .aa-suggestion > span:last-child em {
	font-weight: 700;
	font-style: normal;
	background-color: rgba(58, 150, 207, 0.1);
	padding: 2px 0 2px 2px; }
"
);
?>
<div id="mod-algolia-autocomplete-<?=$module->id?>" class="mod-algolia-autocomplete">
	<form class="uk-form" action="formacion">
			<input id="mod-algolia-search-input" name="buscar" class="uk-form-width-large" type="text" placeholder="<?php echo JText::_('MOD_ALGOLIA_AUTOCOMPLETE_PLACEHOLDER'); ?>">
			<button type="submit" class="btn btn-primary"><?php echo JText::_('MOD_ALGOLIA_AUTOCOMPLETE_BUTTON'); ?></button>
	</form>

	<!-- Include AlgoliaSearch JS Client and autocomplete.js library -->
	<script src="https://cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js"></script>
	<script src="https://cdn.jsdelivr.net/autocomplete.js/0/autocomplete.min.js"></script>
	<!-- Initialize autocomplete menu -->
	<script>
	var client = algoliasearch('<?=$config->applicationId()?>', '<?=$config->apiKey()?>');
	var index = client.initIndex('<?=$config->indexName()?>');

	//initialize autocomplete on search input (ID selector must match)
	autocomplete('#mod-algolia-search-input',
	{ hint: false }, {
			source: autocomplete.sources.hits(index, {hitsPerPage: 5}),
			//value to be displayed in input control after user's suggestion selection
			displayKey: '<?=$titleField?>',
			//hash of templates used when rendering dataset
			templates: {
					//'suggestion' templating function used to render a single suggestion
					suggestion: function(suggestion) {
						var title = suggestion._highlightResult['<?=$titleField?>'].value;

						if (!title.length) {
							title = suggestion._highlightResult.title.value;
						}

						return '<a href="<?=Uri::root()?>' + suggestion.url + '"><span>' + title + '</span></a>';
					}
			}
	});
	</script>
</div>
