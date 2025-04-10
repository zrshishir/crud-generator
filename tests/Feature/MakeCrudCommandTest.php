<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MakeCrudCommandTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    // Clean up any test files that might have been created
    $this->cleanupTestFiles();
  }

  protected function tearDown(): void
  {
    // Clean up test files after each test
    $this->cleanupTestFiles();

    parent::tearDown();
  }

  protected function cleanupTestFiles()
  {
    // Clean up model files
    if (File::exists(app_path('Models/TestModel.php'))) {
      File::delete(app_path('Models/TestModel.php'));
    }

    // Clean up controller files
    if (File::exists(app_path('Http/Controllers/TestModelController.php'))) {
      File::delete(app_path('Http/Controllers/TestModelController.php'));
    }

    // Clean up request files
    if (File::exists(app_path('Http/Requests/TestModelRequest.php'))) {
      File::delete(app_path('Http/Requests/TestModelRequest.php'));
    }

    // Clean up view files
    $viewPath = resource_path('views/TestModel');
    if (File::exists($viewPath)) {
      File::deleteDirectory($viewPath);
    }

    // Clean up migration files
    $migrations = File::files(database_path('migrations'));
    foreach ($migrations as $migration) {
      if (str_contains($migration->getFilename(), 'create_test_models_table')) {
        File::delete($migration->getPathname());
      }
    }
  }

  /** @test */
  public function it_can_generate_a_basic_crud()
  {
    $this->artisan('make:crud', [
      'model' => 'TestModel',
      '--fields' => 'name:string,description:text',
    ])->assertSuccessful();

    // Check if model was created
    $this->assertFileExists(app_path('Models/TestModel.php'));
    $modelContent = File::get(app_path('Models/TestModel.php'));
    $this->assertStringContainsString('class TestModel extends Model', $modelContent);
    $this->assertStringContainsString("protected \$fillable = ['name', 'description']", $modelContent);

    // Check if controller was created
    $this->assertFileExists(app_path('Http/Controllers/TestModelController.php'));
    $controllerContent = File::get(app_path('Http/Controllers/TestModelController.php'));
    $this->assertStringContainsString('class TestModelController extends Controller', $controllerContent);
    $this->assertStringContainsString('public function index()', $controllerContent);
    $this->assertStringContainsString('public function store(TestModelRequest $request)', $controllerContent);
    $this->assertStringContainsString('public function show(TestModel $model)', $controllerContent);
    $this->assertStringContainsString('public function update(TestModelRequest $request, TestModel $model)', $controllerContent);
    $this->assertStringContainsString('public function destroy(TestModel $model)', $controllerContent);

    // Check if request was created
    $this->assertFileExists(app_path('Http/Requests/TestModelRequest.php'));
    $requestContent = File::get(app_path('Http/Requests/TestModelRequest.php'));
    $this->assertStringContainsString('class TestModelRequest extends FormRequest', $requestContent);
    $this->assertStringContainsString("'name' => 'required|string|max:255'", $requestContent);
    $this->assertStringContainsString("'description' => 'required|string'", $requestContent);

    // Check if views were created
    $this->assertFileExists(resource_path('views/TestModel/index.blade.php'));
    $this->assertFileExists(resource_path('views/TestModel/create.blade.php'));
    $this->assertFileExists(resource_path('views/TestModel/edit.blade.php'));
    $this->assertFileExists(resource_path('views/TestModel/show.blade.php'));

    // Check if migration was created
    $migrations = File::files(database_path('migrations'));
    $migrationFound = false;
    foreach ($migrations as $migration) {
      if (str_contains($migration->getFilename(), 'create_test_models_table')) {
        $migrationFound = true;
        $migrationContent = File::get($migration->getPathname());
        $this->assertStringContainsString("Schema::create('test_models', function (Blueprint \$table)", $migrationContent);
        $this->assertStringContainsString("\$table->string('name')", $migrationContent);
        $this->assertStringContainsString("\$table->text('description')", $migrationContent);
        break;
      }
    }
    $this->assertTrue($migrationFound, 'Migration file was not created');
  }

  /** @test */
  public function it_can_generate_crud_with_enum_fields()
  {
    $this->artisan('make:crud', [
      'model' => 'TestModel',
      '--fields' => 'name:string,status:enum(active,inactive)',
    ])->assertSuccessful();

    // Check if model was created with enum field
    $modelContent = File::get(app_path('Models/TestModel.php'));
    $this->assertStringContainsString("protected \$fillable = ['name', 'status']", $modelContent);

    // Check if migration was created with enum field
    $migrations = File::files(database_path('migrations'));
    foreach ($migrations as $migration) {
      if (str_contains($migration->getFilename(), 'create_test_models_table')) {
        $migrationContent = File::get($migration->getPathname());
        $this->assertStringContainsString("\$table->enum('status', ['active', 'inactive'])", $migrationContent);
        break;
      }
    }

    // Check if request was created with enum validation
    $requestContent = File::get(app_path('Http/Requests/TestModelRequest.php'));
    $this->assertStringContainsString("'status' => 'required|in:active,inactive'", $requestContent);
  }

  /** @test */
  public function it_can_generate_crud_with_relationships()
  {
    $this->artisan('make:crud', [
      'model' => 'TestModel',
      '--fields' => 'name:string',
      '--relations' => 'comments:hasMany,author:belongsTo',
    ])->assertSuccessful();

    // Check if model was created with relationships
    $modelContent = File::get(app_path('Models/TestModel.php'));
    $this->assertStringContainsString('public function comments()', $modelContent);
    $this->assertStringContainsString('return $this->hasMany(Comment::class)', $modelContent);
    $this->assertStringContainsString('public function author()', $modelContent);
    $this->assertStringContainsString('return $this->belongsTo(Author::class)', $modelContent);
  }

  /** @test */
  public function it_can_generate_crud_with_multiple_relationships()
  {
    $this->artisan('make:crud', [
      'model' => 'TestModel',
      '--fields' => 'name:string',
      '--relations' => 'comments:hasMany,author:belongsTo,tags:belongsToMany',
    ])->assertSuccessful();

    // Check if model was created with multiple relationships
    $modelContent = File::get(app_path('Models/TestModel.php'));
    $this->assertStringContainsString('public function comments()', $modelContent);
    $this->assertStringContainsString('return $this->hasMany(Comment::class)', $modelContent);
    $this->assertStringContainsString('public function author()', $modelContent);
    $this->assertStringContainsString('return $this->belongsTo(Author::class)', $modelContent);
    $this->assertStringContainsString('public function tags()', $modelContent);
    $this->assertStringContainsString('return $this->belongsToMany(Tag::class)', $modelContent);
  }

  /** @test */
  public function it_can_generate_crud_with_complex_fields()
  {
    $this->artisan('make:crud', [
      'model' => 'TestModel',
      '--fields' => 'name:string,description:text,price:decimal,is_active:boolean,status:enum(active,inactive)',
    ])->assertSuccessful();

    // Check if model was created with complex fields
    $modelContent = File::get(app_path('Models/TestModel.php'));
    $this->assertStringContainsString("protected \$fillable = ['name', 'description', 'price', 'is_active', 'status']", $modelContent);

    // Check if migration was created with complex fields
    $migrations = File::files(database_path('migrations'));
    foreach ($migrations as $migration) {
      if (str_contains($migration->getFilename(), 'create_test_models_table')) {
        $migrationContent = File::get($migration->getPathname());
        $this->assertStringContainsString("\$table->string('name')", $migrationContent);
        $this->assertStringContainsString("\$table->text('description')", $migrationContent);
        $this->assertStringContainsString("\$table->decimal('price'", $migrationContent);
        $this->assertStringContainsString("\$table->boolean('is_active')", $migrationContent);
        $this->assertStringContainsString("\$table->enum('status', ['active', 'inactive'])", $migrationContent);
        break;
      }
    }

    // Check if request was created with complex field validation
    $requestContent = File::get(app_path('Http/Requests/TestModelRequest.php'));
    $this->assertStringContainsString("'name' => 'required|string|max:255'", $requestContent);
    $this->assertStringContainsString("'description' => 'required|string'", $requestContent);
    $this->assertStringContainsString("'price' => 'required|numeric'", $requestContent);
    $this->assertStringContainsString("'is_active' => 'required|boolean'", $requestContent);
    $this->assertStringContainsString("'status' => 'required|in:active,inactive'", $requestContent);
  }

  /** @test */
  public function it_can_generate_crud_with_api_routes()
  {
    $this->artisan('make:crud', [
      'model' => 'TestModel',
      '--fields' => 'name:string',
    ])->assertSuccessful();

    // Check if API routes were added
    $apiRoutesContent = File::get(base_path('routes/api.php'));
    $this->assertStringContainsString("Route::apiResource('test-models', TestModelController::class)", $apiRoutesContent);
  }

  /** @test */
  public function it_can_generate_crud_with_web_routes()
  {
    $this->artisan('make:crud', [
      'model' => 'TestModel',
      '--fields' => 'name:string',
    ])->assertSuccessful();

    // Check if web routes were added
    $webRoutesContent = File::get(base_path('routes/web.php'));
    $this->assertStringContainsString("Route::resource('test-models', TestModelController::class)", $webRoutesContent);
  }
}
