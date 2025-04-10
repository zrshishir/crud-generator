<?php

namespace Tests\Feature;

use App\Models\TestModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneratedControllerTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();

    // Create a test model for our tests
    $this->artisan('make:crud', [
      'model' => 'TestModel',
      '--fields' => 'name:string,description:text,status:enum(active,inactive)',
    ])->assertSuccessful();

    // Run migrations
    $this->artisan('migrate')->assertSuccessful();
  }

  /** @test */
  public function it_can_list_all_models()
  {
    // Create some test models
    TestModel::create([
      'name' => 'Test Model 1',
      'description' => 'Description 1',
      'status' => 'active',
    ]);

    TestModel::create([
      'name' => 'Test Model 2',
      'description' => 'Description 2',
      'status' => 'inactive',
    ]);

    // Test API endpoint
    $response = $this->getJson('/api/test-models');
    $response->assertStatus(200)
      ->assertJsonCount(2)
      ->assertJsonStructure([
        '*' => ['id', 'name', 'description', 'status', 'created_at', 'updated_at'],
      ]);

    // Test web endpoint
    $response = $this->get('/test-models');
    $response->assertStatus(200)
      ->assertViewIs('TestModel.index')
      ->assertViewHas('TestModels');
  }

  /** @test */
  public function it_can_create_a_model()
  {
    // Test API endpoint
    $response = $this->postJson('/api/test-models', [
      'name' => 'New Test Model',
      'description' => 'New Description',
      'status' => 'active',
    ]);

    $response->assertStatus(201)
      ->assertJson([
        'name' => 'New Test Model',
        'description' => 'New Description',
        'status' => 'active',
      ]);

    $this->assertDatabaseHas('test_models', [
      'name' => 'New Test Model',
      'description' => 'New Description',
      'status' => 'active',
    ]);

    // Test web endpoint
    $response = $this->post('/test-models', [
      'name' => 'Web Test Model',
      'description' => 'Web Description',
      'status' => 'inactive',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('test_models', [
      'name' => 'Web Test Model',
      'description' => 'Web Description',
      'status' => 'inactive',
    ]);
  }

  /** @test */
  public function it_validates_required_fields()
  {
    // Test API endpoint
    $response = $this->postJson('/api/test-models', []);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['name', 'description', 'status']);

    // Test web endpoint
    $response = $this->post('/test-models', []);

    $response->assertSessionHasErrors(['name', 'description', 'status']);
  }

  /** @test */
  public function it_validates_enum_fields()
  {
    // Test API endpoint
    $response = $this->postJson('/api/test-models', [
      'name' => 'Test Model',
      'description' => 'Description',
      'status' => 'invalid_status',
    ]);

    $response->assertStatus(422)
      ->assertJsonValidationErrors(['status']);

    // Test web endpoint
    $response = $this->post('/test-models', [
      'name' => 'Test Model',
      'description' => 'Description',
      'status' => 'invalid_status',
    ]);

    $response->assertSessionHasErrors(['status']);
  }

  /** @test */
  public function it_can_show_a_model()
  {
    // Create a test model
    $model = TestModel::create([
      'name' => 'Test Model',
      'description' => 'Description',
      'status' => 'active',
    ]);

    // Test API endpoint
    $response = $this->getJson('/api/test-models/' . $model->id);

    $response->assertStatus(200)
      ->assertJson([
        'id' => $model->id,
        'name' => 'Test Model',
        'description' => 'Description',
        'status' => 'active',
      ]);

    // Test web endpoint
    $response = $this->get('/test-models/' . $model->id);

    $response->assertStatus(200)
      ->assertViewIs('TestModel.show')
      ->assertViewHas('TestModel');
  }

  /** @test */
  public function it_can_update_a_model()
  {
    // Create a test model
    $model = TestModel::create([
      'name' => 'Test Model',
      'description' => 'Description',
      'status' => 'active',
    ]);

    // Test API endpoint
    $response = $this->putJson('/api/test-models/' . $model->id, [
      'name' => 'Updated Test Model',
      'description' => 'Updated Description',
      'status' => 'inactive',
    ]);

    $response->assertStatus(200)
      ->assertJson([
        'id' => $model->id,
        'name' => 'Updated Test Model',
        'description' => 'Updated Description',
        'status' => 'inactive',
      ]);

    $this->assertDatabaseHas('test_models', [
      'id' => $model->id,
      'name' => 'Updated Test Model',
      'description' => 'Updated Description',
      'status' => 'inactive',
    ]);

    // Test web endpoint
    $response = $this->put('/test-models/' . $model->id, [
      'name' => 'Web Updated Model',
      'description' => 'Web Updated Description',
      'status' => 'active',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('test_models', [
      'id' => $model->id,
      'name' => 'Web Updated Model',
      'description' => 'Web Updated Description',
      'status' => 'active',
    ]);
  }

  /** @test */
  public function it_can_delete_a_model()
  {
    // Create a test model
    $model = TestModel::create([
      'name' => 'Test Model',
      'description' => 'Description',
      'status' => 'active',
    ]);

    // Test API endpoint
    $response = $this->deleteJson('/api/test-models/' . $model->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('test_models', [
      'id' => $model->id,
    ]);

    // Test web endpoint
    $model = TestModel::create([
      'name' => 'Web Test Model',
      'description' => 'Web Description',
      'status' => 'active',
    ]);

    $response = $this->delete('/test-models/' . $model->id);

    $response->assertRedirect();

    $this->assertDatabaseMissing('test_models', [
      'id' => $model->id,
    ]);
  }
}
