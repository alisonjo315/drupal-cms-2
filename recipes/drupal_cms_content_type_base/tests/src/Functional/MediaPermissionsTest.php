<?php

declare(strict_types=1);

namespace Drupal\Tests\drupal_cms_content_type_base\Functional;

use Drupal\FunctionalTests\Core\Recipe\RecipeTestTrait;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\media\Traits\MediaTypeCreationTrait;
use Drupal\user\Entity\Role;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;

#[Group('drupal_cms_content_type_base')]
#[IgnoreDeprecations]
final class MediaPermissionsTest extends BrowserTestBase {

  use RecipeTestTrait;
  use MediaTypeCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['media', 'media_test_source'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  public function testMediaPermissionsAutomaticallyGrantedToContentEditors(): void {
    $content_editor = Role::load('content_editor');
    $this->assertEmpty($content_editor);
    $type1 = $this->createMediaType('test')->id();

    $dir = realpath(__DIR__ . '/../../..');
    $this->applyRecipe($dir);

    $type2 = $this->createMediaType('test')->id();
    $content_editor = Role::load('content_editor');
    $this->assertInstanceOf(Role::class, $content_editor);
    // Permissions should have been granted for the pre-existing media type AND
    // the one we created after applying the recipe.
    $this->assertTrue($content_editor->hasPermission("create $type1 media"));
    $this->assertTrue($content_editor->hasPermission("create $type2 media"));
    $this->assertTrue($content_editor->hasPermission("delete any $type1 media"));
    $this->assertTrue($content_editor->hasPermission("delete any $type2 media"));
    $this->assertTrue($content_editor->hasPermission("edit any $type1 media"));
    $this->assertTrue($content_editor->hasPermission("edit any $type2 media"));

    // If a permission is revoked, it should not be restored if another media
    // type is created.
    $content_editor->revokePermission("delete any $type1 media")->save();
    $this->createMediaType('test');
    $this->assertFalse(
      Role::load('content_editor')?->hasPermission("delete any $type1 media"),
    );
  }

}
