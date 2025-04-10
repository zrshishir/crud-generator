<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelationshipTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();

    // Create test models with relationships
    $this->artisan('make:crud', [
      'model' => 'Post',
      '--fields' => 'title:string,content:text',
      '--relations' => 'comments:hasMany,author:belongsTo',
    ])->assertSuccessful();

    $this->artisan('make:crud', [
      'model' => 'Comment',
      '--fields' => 'content:text',
      '--relations' => 'post:belongsTo',
    ])->assertSuccessful();

    $this->artisan('make:crud', [
      'model' => 'Author',
      '--fields' => 'name:string,email:string',
      '--relations' => 'posts:hasMany',
    ])->assertSuccessful();

    // Run migrations
    $this->artisan('migrate')->assertSuccessful();
  }

  /** @test */
  public function it_can_create_models_with_relationships()
  {
    // Create an author
    $authorResponse = $this->postJson('/api/authors', [
      'name' => 'Test Author',
      'email' => 'author@example.com',
    ]);

    $authorId = $authorResponse->json('id');

    // Create a post with author relationship
    $postResponse = $this->postJson('/api/posts', [
      'title' => 'Test Post',
      'content' => 'Test Content',
      'author_id' => $authorId,
    ]);

    $postId = $postResponse->json('id');

    // Create a comment with post relationship
    $commentResponse = $this->postJson('/api/comments', [
      'content' => 'Test Comment',
      'post_id' => $postId,
    ]);

    // Verify relationships
    $this->assertDatabaseHas('posts', [
      'id' => $postId,
      'author_id' => $authorId,
    ]);

    $this->assertDatabaseHas('comments', [
      'id' => $commentResponse->json('id'),
      'post_id' => $postId,
    ]);
  }

  /** @test */
  public function it_can_load_relationships()
  {
    // Create test data
    $author = $this->createAuthor();
    $post = $this->createPost($author->id);
    $comment = $this->createComment($post->id);

    // Test loading relationships
    $response = $this->getJson('/api/posts/' . $post->id);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'id',
        'title',
        'content',
        'author' => [
          'id',
          'name',
          'email',
        ],
        'comments' => [
          '*' => [
            'id',
            'content',
          ],
        ],
      ]);
  }

  /** @test */
  public function it_can_create_models_with_belongs_to_relationship()
  {
    // Create a post first
    $post = $this->createPost();

    // Create a comment with post relationship
    $response = $this->postJson('/api/comments', [
      'content' => 'Test Comment',
      'post_id' => $post->id,
    ]);

    $response->assertStatus(201)
      ->assertJson([
        'content' => 'Test Comment',
        'post_id' => $post->id,
      ]);

    // Verify the relationship
    $this->assertDatabaseHas('comments', [
      'id' => $response->json('id'),
      'post_id' => $post->id,
    ]);
  }

  /** @test */
  public function it_can_create_models_with_has_many_relationship()
  {
    // Create an author
    $author = $this->createAuthor();

    // Create multiple posts for the author
    $post1 = $this->createPost($author->id, 'Post 1');
    $post2 = $this->createPost($author->id, 'Post 2');

    // Verify the relationships
    $response = $this->getJson('/api/authors/' . $author->id);

    $response->assertStatus(200)
      ->assertJsonCount(2, 'posts')
      ->assertJsonStructure([
        'id',
        'name',
        'email',
        'posts' => [
          '*' => [
            'id',
            'title',
            'content',
          ],
        ],
      ]);
  }

  /** @test */
  public function it_validates_relationship_fields()
  {
    // Try to create a post with non-existent author
    $response = $this->postJson('/api/posts', [
      'title' => 'Test Post',
      'content' => 'Test Content',
      'author_id' => 999, // Non-existent ID
    ]);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['author_id']);

    // Try to create a comment with non-existent post
    $response = $this->postJson('/api/comments', [
      'content' => 'Test Comment',
      'post_id' => 999, // Non-existent ID
    ]);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['post_id']);
  }

  // Helper methods to create test data
  protected function createAuthor($name = 'Test Author', $email = 'author@example.com')
  {
    $response = $this->postJson('/api/authors', [
      'name' => $name,
      'email' => $email,
    ]);

    return (object) $response->json();
  }

  protected function createPost($authorId = null, $title = 'Test Post', $content = 'Test Content')
  {
    $data = [
      'title' => $title,
      'content' => $content,
    ];

    if ($authorId) {
      $data['author_id'] = $authorId;
    }

    $response = $this->postJson('/api/posts', $data);

    return (object) $response->json();
  }

  protected function createComment($postId, $content = 'Test Comment')
  {
    $response = $this->postJson('/api/comments', [
      'content' => $content,
      'post_id' => $postId,
    ]);

    return (object) $response->json();
  }
}
