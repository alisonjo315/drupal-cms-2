<?php

declare(strict_types=1);

namespace Drupal\Tests\drupal_cms_starter\Functional;

use Composer\InstalledVersions;
use Drupal\canvas\Entity\Component;
use Drupal\canvas\JsonSchemaDefinitionsStreamwrapper;
use Drupal\Core\DefaultContent\Exporter;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\drupal_cms_content_type_base\Traits\ContentModelTestTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;

#[Group('drupal_cms_starter')]
#[IgnoreDeprecations]
class ComponentValidationTest extends BrowserTestBase {

  use ContentModelTestTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function rebuildAll(): void {
    // The rebuild won't succeed without the `json-schema-definitions` stream
    // wrapper. This would normally happen automatically whenever a module is
    // installed, but in this case, all of that has taken place in a separate
    // process, so we need to refresh this process manually.
    // @see canvas_module_preinstall()
    $this->container->get('stream_wrapper_manager')
      ->registerWrapper(
        'json-schema-definitions',
        JsonSchemaDefinitionsStreamwrapper::class,
        JsonSchemaDefinitionsStreamwrapper::getType(),
      );

    parent::rebuildAll();
  }

  public function test(): void {
    // Apply this recipe once. It is a site template and therefore unlikely to
    // be applied again in the real world.
    $dir = InstalledVersions::getInstallPath('drupal/drupal_cms_starter');
    $this->applyRecipe($dir);

    $assert_session = $this->assertSession();
    // A non-existent page should respond with a 404.
    $this->drupalGet('/node/999999');
    $assert_session->statusCodeEquals(404);
    // A forbidden page should respond with a 403.
    $this->drupalGet('/admin');
    $assert_session->statusCodeEquals(403);

    // Assign additional permissions so we can ensure that the top tasks are
    // available.
    $account = $this->drupalCreateUser([
      'administer content types',
      'administer modules',
      'administer themes',
      'administer users',
    ]);
    $account->addRole('content_editor')->save();
    // Don't use one-time login links, because they will bypass the dashboard
    // upon login.
    $this->useOneTimeLoginLinks = FALSE;
    $this->drupalLogin($account);
    // We should be on the welcome dashboard, and we should see the lists of
    // recent content and Canvas pages.
    $assert_session->addressEquals('/admin/dashboard');
    $recent_content_list = $assert_session->elementExists('css', 'h2:contains("Recent content")')
      ->getParent();
    $this->assertTrue($recent_content_list->hasLink('Privacy policy'));
    $pages_list = $assert_session->elementExists('css', '.block-views > h2:contains("Recent pages")')
      ->getParent();
    $this->assertTrue($pages_list->hasLink('Page not found'));

    // The first item in the navigation footer should be the Help link, and it
    // should only appear once.
    $first_footer_item = $assert_session->elementExists('css', '#menu-footer .toolbar-block');
    $assert_session->elementExists('named', ['link', 'Help'], $first_footer_item);
    $assert_session->elementsCount('named', ['link', 'Help'], 1, $first_footer_item->getParent());

    // All top tasks should be available.
    $top_tasks = $this->container->get(EntityTypeManagerInterface::class)
      ->getStorage('menu_link_content')
      ->loadByProperties(['menu_name' => 'top-tasks']);
    $this->assertNotEmpty($top_tasks);
    /** @var \Drupal\menu_link_content\MenuLinkContentInterface $link */
    foreach ($top_tasks as $link) {
      $this->drupalGet($link->getUrlObject());
      $this->assertSame(200, $this->getSession()->getStatusCode(), 'The ' . $link->getTitle() . ' top task is unavailable.');
    }

    // If we apply the search recipe, a Canvas component should be created for
    // the search block and it should be available for use.
    $dir = InstalledVersions::getInstallPath('drupal/drupal_cms_search');
    $this->applyRecipe($dir);
    $this->assertTrue(Component::load('block.simple_search_form_block')?->status());

    // Confirm that the `target_uuid` property (implemented by Canvas) is
    // stripped out of exported entity reference fields.
    // @see \Drupal\drupal_cms_helper\EventSubscriber\DefaultContentSubscriber::preExport()
    $node = Node::load(1);
    $this->assertInstanceOf(Node::class, $node);
    $exported = $this->container->get(Exporter::class)->export($node)->data;
    $this->assertStringNotContainsString('target_uuid', serialize($exported));
  }

}
