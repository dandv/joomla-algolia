<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Installer
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\Registry\Registry;

/**
 * Package installer
 *
 * @since  1.0.0
 */
class Pkg_AlgoliaInstallerScript
{
	/**
	 * Minimum PHP version required.
	 *
	 * @const
	 */
	const REQUIRED_PHP_VERSION = '7.0.0';

	/**
	 * Installer instance
	 *
	 * @var  JInstaller
	 */
	public $installer = null;

	/**
	 * Manifest of the extension being processed
	 *
	 * @var  SimpleXMLElement
	 */
	protected $manifest;

	/**
	 * Enable plugins if desired
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	private function enablePlugins($parent)
	{
		// Required objects
		$manifest  = $this->getManifest($parent);

		if ($nodes = $manifest->files)
		{
			foreach ($nodes->file as $node)
			{
				$extType = (string) $node->attributes()->type;

				if ($extType !== 'plugin')
				{
					continue;
				}

				$enabled = (string) $node->attributes()->enabled;

				if ($enabled !== 'true')
				{
					continue;
				}

				$extName  = (string) $node->attributes()->id;
				$extGroup = (string) $node->attributes()->group;

				$db = Factory::getDbo();
				$query = $db->getQuery(true);
				$query->update($db->quoteName("#__extensions"));
				$query->set("enabled=1");
				$query->where("type='plugin'");
				$query->where("element=" . $db->quote($extName));
				$query->where("folder=" . $db->quote($extGroup));

				$db->setQuery($query);
				$db->execute();
			}
		}
	}


	/**
	 * Get the common JInstaller instance used to install all the extensions
	 *
	 * @return Installer The JInstaller object
	 */
	public function getInstaller()
	{
		if (null === $this->installer)
		{
			$this->installer = new Installer;
		}

		return $this->installer;
	}

	/**
	 * Getter with manifest cache support
	 *
	 * @param   JInstallerAdapter  $parent  Parent object
	 *
	 * @return  SimpleXMLElement
	 */
	protected function getManifest($parent)
	{
		if (null === $this->manifest)
		{
			$this->loadManifest($parent);
		}

		return $this->manifest;
	}

	/**
	 * Install dependencies.
	 *
	 * @param   InstallerAdapter  $parent  Installer processing this script
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException  Issues found checking/installing dependencies
	 */
	protected function installDependencies($parent)
	{
		$manifest  = $this->getManifest($parent);

		if ($dependencies = $manifest->dependencies)
		{
			foreach ($dependencies->dependency as $dependency)
			{
				$description = trim((string) $dependency);
				$type  = (string) $dependency->attributes()->type;
				$name  = (string) $dependency->attributes()->name;
				$group = (string) $dependency->attributes()->group;
				$file  = (string) $dependency->attributes()->file;
				$url   = (string) $dependency->attributes()->url;
				$size   = (int) (string) $dependency->attributes()->size;
				$hash   = trim((string) $dependency->attributes()->hash);
				$version   = trim((string) $dependency->attributes()->version);

				$extension = $this->searchExtension($name, $type, null, $group);

				if ($extension)
				{
					$extensionManifest = new Registry($extension->{'manifest_cache'});

					$existingVersion = $extensionManifest->get('version');

					if (!$existingVersion)
					{
						$msg = sprintf(
							'Error installing dependency `%s`: unable to determine installed version.',
							$description
						);

						throw new \RuntimeException($msg);
					}

					$ok = preg_match('/^' . $version . '/', $existingVersion);

					// There is a version that does not match requirements. Let the user to solve the issue.
					if (!$ok)
					{
						$msg = sprintf(
							'Error installing dependency `%s`: unable to satisfy dependency version. Installed: %s. Requirements: %s.',
							$description,
							$existingVersion,
							$version
						);

						throw new \RuntimeException($msg);
					}

					continue;
				}

				if (empty($size))
				{
					$msg = sprintf(
						'Error installing dependency `%s`: missing expected file size in manifest.',
						$description
					);

					throw new \RuntimeException($msg);
				}

				if (empty($hash))
				{
					$msg = sprintf(
						'Error installing dependency `%s`: missing expected file hash in manifest.',
						$description
					);

					throw new \RuntimeException($msg);
				}

				if (empty($file) && empty($url))
				{
					$msg = sprintf(
						'Error installing dependency `%s`: missing file/URL in manifest.',
						$description
					);

					throw new \RuntimeException($msg);
				}

				$folder = (string) $dependencies->folder;
				$source = $parent->getParent()->getPath('source');

				if ($folder)
				{
					$source .= '/' . $folder;
				}

				if (!empty($file))
				{
					$filePath = $source . '/' . (string) $file;
				}
				elseif (!empty($url))
				{
					$fileName = InstallerHelper::downloadPackage($url);

					if (false === $fileName)
					{
						$msg = sprintf(
							'Error installing dependency `%s`: failed to download file from `%s`.',
							$description,
							$url
						);

						throw new \RuntimeException($msg);
					}

					$filePath = Factory::getConfig()->get('tmp_path') . '/' . $fileName;
				}

				if (!is_file($filePath))
				{
					$msg = sprintf(
						'Error installing dependency `%s`: missing file `%s`.',
						$description,
						$filePath
					);

					throw new \RuntimeException($msg);
				}

				$fileSize = @filesize($filePath);

				if (false === $fileSize || $fileSize !== $size)
				{
					$msg = sprintf(
						'Error installing dependency `%s`: wrong dependency file size `%s`.',
						$description,
						$filePath
					);

					throw new \RuntimeException($msg);
				}

				$fileHash = @md5_file($filePath);

				if (false === $fileHash || $fileHash !== $hash)
				{
					$msg = sprintf(
						'Error installing dependency `%s`: wrong dependency file hash `%s`.',
						$description,
						$filePath
					);

					throw new \RuntimeException($msg);
				}

				$package = InstallerHelper::unpack($filePath, true);

				if (false === $package)
				{
					$msg = sprintf(
						'Error installing dependency `%s`: Error unpacking package `%s`.',
						$description,
						$filePath
					);

					throw new \RuntimeException($msg);
				}

				if (!$this->getInstaller()->install($package['dir']))
				{
					$msg = sprintf(
						'Error installing dependency `%s`: Could not install extracted package from `%s`.',
						$description,
						$package['dir']
					);

					throw new \RuntimeException($msg);
				}
			}
		}
	}

	/**
	 * Shit happens. Patched function to bypass bug in package uninstaller
	 *
	 * @param   JInstallerAdapter  $parent  Parent object
	 *
	 * @return  void
	 */
	protected function loadManifest($parent)
	{
		$element = strtolower(str_replace('InstallerScript', '', get_called_class()));
		$elementParts = explode('_', $element);

		// Type not properly detected or not a package
		if (count($elementParts) != 2 || strtolower($elementParts[0]) !== 'pkg')
		{
			$this->manifest = $parent->get('manifest');

			return;
		}

		$manifestFile = __DIR__ . '/' . $element . '.xml';

		// Package manifest found
		if (file_exists($manifestFile))
		{
			$this->manifest = simplexml_load_file($manifestFile);

			return;
		}

		$this->manifest = $parent->get('manifest');
	}

	/**
	 * Method to run after an install/update/discover method
	 *
	 * @param   object  $type    type of change (install, update or discover_install)
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	public function postflight($type, $parent)
	{
		$this->enablePlugins($parent);
	}

	/**
	 * Method to run after an install/update/discover method
	 *
	 * @param   object  $type    type of change (install, update or discover_install)
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	public function preflight($type, $parent)
	{
		if (version_compare(PHP_VERSION, self::REQUIRED_PHP_VERSION) < 0)
		{
			$msg = Text::sprintf('PKG_ALGOLIA_ERROR_REQUIRED_VERSION', self::REQUIRED_PHP_VERSION, PHP_VERSION);

			throw new \RuntimeException($msg);
		}

		$this->installDependencies($parent);
	}

	/**
	 * Search a extension in the database
	 *
	 * @param   string  $element  Extension technical name/alias
	 * @param   string  $type     Type of extension (component, file, language, library, module, plugin)
	 * @param   string  $state    State of the searched extension
	 * @param   string  $folder   Folder name used mainly in plugins
	 *
	 * @return  integer           Extension identifier
	 */
	protected function searchExtension($element, $type, $state = null, $folder = null)
	{
		$db = Factory::getDBO();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName("#__extensions"))
			->where("type = " . $db->quote($type))
			->where("element = " . $db->quote($element));

		if (!is_null($state))
		{
			$query->where("state = " . (int) $state);
		}

		if (!is_null($folder))
		{
			$query->where("folder = " . $db->quote($folder));
		}

		$db->setQuery($query);

		return $db->loadObject();
	}
}
