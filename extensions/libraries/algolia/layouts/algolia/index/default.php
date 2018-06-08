<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Component.Frontend
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

extract($displayData);

Factory::getDocument()->addStyleSheet('//cdn.jsdelivr.net/npm/instantsearch.js@2.8.0/dist/instantsearch.min.css');
Factory::getDocument()->addScript('//cdn.jsdelivr.net/npm/instantsearch.js@2.3/dist/instantsearch.min.js');

$config = $index->algoliaConfig();
?>
<div class="algolia-index">
	<script type="text/html" id="hit-template">
		<article>
			<header>
				<h2>{{{ title }}}</h2>
			</header>
			{{#introtext}}
				<div class="description">
					{{{ introtext }}}
				</div>
			{{/introtext}}
			<a href="{{{url}}}" class="btn btn-primary">View</a>
		</article>
	</script>
	<h1><?=$index->get('name')?></h1>
	<input type="text" placeholder="Find articles" name="search" id="algolia-searchbox"/>
	<div id="selectedFilters"> </div>
	<div id="algolia-hits"></div>
	<div id="algolia-stats-pagination"></div>
	<div id="algolia-pagination"></div>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			var search = instantsearch({
			  appId: '<?=$config->applicationId()?>',
			  apiKey: '<?=$config->apiKey()?>',
			  indexName: '<?=$config->indexName()?>',
			  filters: 'status:1',
			  urlSync: true,
			  searchParameters: {
			    hitsPerPage: 5
			  }
			});

			search.addWidget(
			  instantsearch.widgets.searchBox({
			    container: '#algolia-searchbox',
			    autofocus: true,
			    poweredBy: false,
			    magnifier: {
			    	template: ''
			    }
			  })
			);

			search.addWidget(
			  instantsearch.widgets.currentRefinedValues({
			    container: '#selectedFilters',
			    clearAll: 'after',
			    clearsQuery: true,
			    templates: {
			    	header: '<span>Filtros activos: </span>',
			    	item: (selection) => {
			    		if (selection.type === 'query') {
			    			return selection.name;
			    		}

			    		if ('.' === selection.name.charAt(5)) {
			    			return selection.name.substr(6, selection.name.length);
			    		}

			    		return selection.name;
			    	},
			    	clearAll: '<div>Quitar todos</div>'
			    }
			  })
			);

			search.addWidget(
			  instantsearch.widgets.refinementList({
			    container: '#algolia-categorias',
			    attributeName: 'category_title',
			    operator: 'or',
			    sortBy: ["name:asc","count:desc"],
			    limit: 10,
			    showMore: true,
			    templates: {
			      header: '<h3>Categories</h3>',
			      item: function(object) {
			      	const label = object.label;
			      	return '<label class="ais-refinement-list--label">'
					  		+ '<input class="ais-refinement-list--checkbox" value="' + object.value + '" type="checkbox" ' + (object.isRefined ? 'checked' : '') + '>'
					      	+ ' ' + label
					  		+ '<span class="ais-refinement-list--count"> (' + object.count + ')</span>'
							+ '</label>';
			      }
			    }
			  })
			);

			search.addWidget(
			  instantsearch.widgets.refinementList({
			    container: '#algolia-anos',
			    attributeName: 'ano',
			    operator: 'or',
			    sortBy: ["name:asc","count:desc"],
			    limit: 10,
			    showMore: true,
			    templates: {
			      header: '<h3>Año</h3>',
			      item: function(object) {
			      	const label = object.label;
			      	return '<label class="ais-refinement-list--label">'
					  		+ '<input class="ais-refinement-list--checkbox" value="' + object.value + '" type="checkbox" ' + (object.isRefined ? 'checked' : '') + '>'
					      	+ ' ' + label
					  		+ '<span class="ais-refinement-list--count"> (' + object.count + ')</span>'
							+ '</label>';
			      }
			    }
			  })
			);

			search.addWidget(
			  instantsearch.widgets.refinementList({
			    container: '#algolia-circuitos',
			    attributeName: 'circuitos',
			    operator: 'or',
			    sortBy: ["name:asc","count:desc"],
			    limit: 10,
			    showMore: true,
			    templates: {
			      header: '<h3 class="uk-panel-title">Tags</h3>',
			      item: function(object) {
			      	const label = object.label;
			      	return '<label class="ais-refinement-list--label">'
					  		+ '<input class="ais-refinement-list--checkbox" value="' + object.value + '" type="checkbox" ' + (object.isRefined ? 'checked' : '') + '>'
					      	+ ' ' + label
					  		+ '<span class="ais-refinement-list--count"> (' + object.count + ')</span>'
							+ '</label>';
			      }
			    }
			  })
			);

			search.addWidget(
			  instantsearch.widgets.refinementList({
			    container: '#algolia-tags',
			    attributeName: '_tags',
			    operator: 'or',
			    sortBy: ["name:asc","count:desc"],
			    limit: 10,
			    showMore: true,
			    templates: {
			      header: '<h3 class="uk-panel-title">Tags</h3>',
			      item: function(object) {
			      	const label = object.label;
			      	return '<label class="ais-refinement-list--label">'
					  		+ '<input class="ais-refinement-list--checkbox" value="' + object.value + '" type="checkbox" ' + (object.isRefined ? 'checked' : '') + '>'
					      	+ ' ' + label
					  		+ '<span class="ais-refinement-list--count"> (' + object.count + ')</span>'
							+ '</label>';
			      }
			    }
			  })
			);

			search.addWidget(
			  instantsearch.widgets.refinementList({
			    container: '#algolia-authors',
			    attributeName: 'author_name',
			    operator: 'or',
			    sortBy: ["name:asc","count:desc"],
			    limit: 10,
			    showMore: true,
			    templates: {
			      header: '<h3 class="uk-panel-title">Authors</h3>',
			      item: function(object) {
			      	const label = object.label;
			      	return '<label class="ais-refinement-list--label">'
					  		+ '<input class="ais-refinement-list--checkbox" value="' + object.value + '" type="checkbox" ' + (object.isRefined ? 'checked' : '') + '>'
					      	+ ' ' + label
					  		+ '<span class="ais-refinement-list--count"> (' + object.count + ')</span>'
							+ '</label>';
			      }
			    }
			  })
			);

			search.addWidget(
			  instantsearch.widgets.hits({
			    container: '#algolia-hits',
			    templates: {
			      item: document.getElementById('hit-template').innerHTML
			    }
			  })
			);

			search.addWidget(
			  instantsearch.widgets.stats({
			    container: '#algolia-stats-pagination',
			    templates: {
			    	body: function(stats) {
			    		const page = parseInt(stats.page);
			    		const hitsPerPage = parseInt(stats.hitsPerPage);

			    		const from = page ? (page * hitsPerPage + 1) : 1;
			    		const to = page ? (page + 1) * hitsPerPage : hitsPerPage;
			    		return '<span>Resultados ' + from + ' - ' + to + ' de ' + stats.nbHits + '</span>';
			    	}
			    }
			  })
			);

			search.addWidget(
				instantsearch.widgets.pagination({
				  container: '#algolia-pagination',
				  maxPages: 5
				})
			);

			search.start();
		});
	</script>
</div>
