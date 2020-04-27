<?php

namespace Drupal\markdown;

use Drupal\Core\Session\AccountInterface;
use Drupal\markdown\Plugin\Markdown\MarkdownParserInterface;

interface MarkdownParserPluginManagerInterface extends MarkdownPluginManagerInterface {

  /**
   * Retrieves the a filter plugin instance based on passed configuration.
   *
   * @param string $parser
   *   The plugin identifier of the parser that should match against.
   * @param array $configuration
   *   An array of configuration relevant to the plugin instance, passed by
   *   reference.
   *
   * @return \Drupal\markdown_filter\Plugin\Filter\MarkdownFilterInterface|null
   *   A MarkdownFilter instance or NULL if it could not be determined.
   */
  public function getFilter($parser = 'commonmark', array &$configuration = []);

  /**
   * Retrieves a parser based on a filter and its settings.
   *
   * @param string|\Drupal\filter\Plugin\FilterInterface|\Drupal\filter\FilterFormatInterface $filter
   *   Optional A specific filter plugin to use, a string representing a filter
   *   format or a FilterFormatInterface object containing a "markdown" filter.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Optional. An account used to retrieve filters available filters if one
   *   wasn't already specified.
   *
   * @return \Drupal\markdown\Plugin\Markdown\MarkdownParserInterface
   *   A MarkdownParser plugin.
   */
  public function getParser($filter = NULL, AccountInterface $account = NULL);

}
