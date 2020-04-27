<?php

namespace Drupal\markdown\Plugin\Markdown;

use Drupal\Component\Utility\NestedArray;

/**
 * Class ExtensibleMarkdownParser.
 */
abstract class ExtensibleParser extends BaseParser implements ExtensibleMarkdownParserInterface {

  /**
   * The Markdown Extension Manager service.
   *
   * @var \Drupal\markdown\MarkdownExtensionPluginManagerInterface
   */
  protected static $extensionManager;

  /**
   * MarkdownExtension plugins specific to a parser.
   *
   * @var array
   */
  protected static $extensions;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    // Merge in extensions from config.
    if (!empty($configuration['extensions']) && is_array($configuration['extensions'])) {
      $extensions = [];
      foreach ($configuration['extensions'] as $extension) {
        $extension['enabled'] = TRUE;
        $extensions[$extension['id']] = $extension;
      }
      $this->settings = NestedArray::mergeDeep($this->settings, $extensions);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function alterGuidelines(array &$guides = []) {
    // Allow enabled extensions to alter existing guides.
    foreach ($this->getExtensions() as $plugin_id => $extension) {
      if ($extension instanceof MarkdownGuidelinesAlterInterface) {
        $extension->alterGuidelines($guides);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getGuidelines() {
    $guides = parent::getGuidelines();

    // Allow enabled extensions to provide their own guides.
    foreach ($this->getExtensions() as $plugin_id => $extension) {
      if ($extension instanceof MarkdownGuidelinesInterface && ($element = $extension->getGuidelines())) {
        $guides['extensions'][$plugin_id] = $element;
      }
    }

    return $guides;
  }

  /**
   * {@inheritdoc}
   */
  public function getExtensions($enabled = NULL) {
    if (!isset(static::$extensions["$enabled:$this->pluginId"])) {
      static::$extensions["$enabled:$this->pluginId"] = $this->extensionManager()->getExtensions($this->pluginId, $enabled);
    }

    /* @type \Drupal\markdown\Plugin\Markdown\Extension\MarkdownExtensionInterface $extension */
    foreach (static::$extensions["$enabled:$this->pluginId"] as $id => $extension) {
      if (isset($this->settings[$id])) {
        $extension->setSettings($this->settings[$id]);
      }
    }

    /** @var \Drupal\markdown\Plugin\Markdown\Extension\MarkdownExtensionInterface[] $extensions */
    $extensions = static::$extensions["$enabled:$this->pluginId"];
    return $extensions;
  }

  /**
   * Retrieves the Markdown Extension Manager service.
   *
   * @return \Drupal\markdown\MarkdownExtensionPluginManagerInterface
   */
  protected function extensionManager() {
    if (!static::$extensionManager) {
      static::$extensionManager = \Drupal::service('plugin.manager.markdown.extension');
    }
    return static::$extensionManager;
  }

}
