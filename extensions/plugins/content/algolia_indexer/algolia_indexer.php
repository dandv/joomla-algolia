<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Plugin.Algolia_Indexer
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

JLoader::import('algolia.library');

use Joomla\CMS\Form\Form;
use Joomla\CMS\Plugin\PluginHelper;
use Phproberto\Joomla\Algolia\Plugin\BasePlugin;

/**
 * Algolia indexer.
 *
 * @since  __DEPLOY_VERSION__
 */
final class PlgContentAlgolia_Indexer extends BasePlugin
{
	/**
	 * Name of the indexer edit form.
	 */
	const INDEXER_FORM_NAME = 'com_algolia.indexer';

	/**
	 * Constructor
	 *
	 * @param   string  $subject  Subject
	 * @param   array   $config   Configuration
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		// Make `content` events available for `algolia_indexer` plugins
		PluginHelper::importPlugin('algolia_indexer');
	}

	/**
	 * Runs on content preparation
	 *
	 * @param   string  $context  The context for the data
	 * @param   object  $data     An object containing the data for the form.
	 *
	 * @return  boolean
	 */
	public function onContentPrepareData($context, $data)
	{

		return true;
		echo '<pre>'; print_r($data); echo '</pre>';
		die($context);
	}

	/**
	 * The form event. Load additional parameters when available into the field form.
	 * Only when the type of the form is of interest.
	 *
	 * @param   JForm     $form  The form
	 * @param   stdClass  $data  The data
	 *
	 * @return  void
	 */
	public function onContentPrepareForm(Form $form, $data)
	{
		if (!$this->app->isAdmin() || !$form instanceof Form)
		{
			return true;
		}

		$data = (array) $data;
		$indexerId = empty($data['extension_id']) ? 0 : (int) $data['extension_id'];

		if ($form->getName() !== static::INDEXER_FORM_NAME || !$indexerId)
		{
			return true;
		}

		return $this->injectIndexerParams($indexerId, $form);
	}

	/**
	 * Injects active indexer parameters into indexer form.
	 *
	 * @param   integer  $indexerId  Indexer identifier
	 * @param   Form     $form       Form instance
	 *
	 * @return  boolean
	 */
	private function injectIndexerParams($indexerId, Form $form)
	{
		$dispatcher = JEventDispatcher::getInstance();
		PluginHelper::importPlugin('algolia_indexer');

		$dispatcher->trigger('onAlgoliaInjectIndexerParams', array($indexerId, &$form));

		return true;
	}
}
