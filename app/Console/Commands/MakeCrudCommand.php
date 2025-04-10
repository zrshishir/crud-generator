<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeCrudCommand extends Command
{
  protected $signature = 'make:crud {model} 
                          {--fields= : Comma-separated list of fields with their types} 
                          {--relations= : Comma-separated list of relationships}';

  protected $description = 'Generate a complete CRUD for a model';

  public function handle()
  {
    $modelName = $this->argument('model');
    $fields = $this->parseFields($this->option('fields'));
    $relations = $this->parseRelations($this->option('relations'));

    $this->info("Generating CRUD for {$modelName}...");

    // Generate Model
    $this->generateModel($modelName, $fields, $relations);

    // Generate Migration
    $this->generateMigration($modelName, $fields);

    // Generate Controller
    $this->generateController($modelName, $fields);

    // Generate Request
    $this->generateRequest($modelName, $fields);

    // Generate Views
    $this->generateViews($modelName, $fields);

    // Generate Routes
    $this->generateRoutes($modelName);

    $this->info("CRUD generation completed successfully!");
  }

  protected function parseFields($fieldsString)
  {
    if (empty($fieldsString)) {
      return [];
    }

    $fields = [];
    foreach (explode(',', $fieldsString) as $field) {
      $parts = explode(':', $field);

      // Skip malformed field definitions
      if (count($parts) !== 2) {
        $this->warn("Skipping malformed field definition: {$field}");
        continue;
      }

      $fieldName = trim($parts[0]);
      $fieldType = trim($parts[1]);

      // Handle enum fields with options
      if (strpos($fieldType, 'enum(') === 0) {
        $options = substr($fieldType, 5, -1); // Remove enum( and )
        $fields[$fieldName] = [
          'type' => 'enum',
          'options' => explode(',', $options)
        ];
      } else {
        $fields[$fieldName] = $fieldType;
      }
    }

    return $fields;
  }

  protected function parseRelations($relationsString)
  {
    if (empty($relationsString)) {
      return [];
    }

    $relations = [];
    foreach (explode(',', $relationsString) as $relation) {
      $parts = explode(':', $relation);
      $relations[trim($parts[0])] = trim($parts[1]);
    }

    return $relations;
  }

  protected function generateModel($modelName, $fields, $relations)
  {
    $fillable = array_keys($fields);
    $fillableString = "'" . implode("', '", $fillable) . "'";

    $relationsCode = '';
    foreach ($relations as $relatedModel => $type) {
      $methodName = Str::camel($relatedModel);
      $relationsCode .= $this->generateRelationMethod($methodName, $type, $relatedModel);
    }

    $modelContent = <<<PHP
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class {$modelName} extends Model
{
    use HasFactory;

    protected \$fillable = [{$fillableString}];

    {$relationsCode}
}
PHP;

    File::put(app_path("Models/{$modelName}.php"), $modelContent);
    $this->info("Model created successfully.");
  }

  protected function generateRelationMethod($methodName, $type, $relatedModel)
  {
    $relatedModelClass = Str::studly($relatedModel);

    switch ($type) {
      case 'hasMany':
        return "public function {$methodName}()\n    {\n        return \$this->hasMany({$relatedModelClass}::class);\n    }\n\n";
      case 'belongsTo':
        return "public function {$methodName}()\n    {\n        return \$this->belongsTo({$relatedModelClass}::class);\n    }\n\n";
      case 'belongsToMany':
        return "public function {$methodName}()\n    {\n        return \$this->belongsToMany({$relatedModelClass}::class);\n    }\n\n";
      default:
        return '';
    }
  }

  protected function generateMigration($modelName, $fields)
  {
    $tableName = Str::snake(Str::plural($modelName));
    $fieldsCode = '';

    foreach ($fields as $field => $type) {
      if (is_array($type) && $type['type'] === 'enum') {
        $options = implode("', '", $type['options']);
        $fieldsCode .= "\$table->enum('{$field}', ['{$options}']);\n            ";
      } else {
        $fieldsCode .= "\$table->{$type}('{$field}');\n            ";
      }
    }

    $migrationContent = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            {$fieldsCode}
            \$table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('{$tableName}');
    }
};
PHP;

    $timestamp = date('Y_m_d_His');
    File::put(database_path("migrations/{$timestamp}_create_{$tableName}_table.php"), $migrationContent);
    $this->info("Migration created successfully.");
  }

  protected function generateController($modelName, $fields)
  {
    $controllerContent = <<<PHP
<?php

namespace App\Http\Controllers;

use App\Models\\{$modelName};
use App\Http\Requests\\{$modelName}Request;
use Illuminate\Http\Request;

class {$modelName}Controller extends Controller
{
    public function index()
    {
        return {$modelName}::all();
    }

    public function store({$modelName}Request \$request)
    {
        \$model = {$modelName}::create(\$request->validated());
        return response()->json(\$model, 201);
    }

    public function show({$modelName} \$model)
    {
        return response()->json(\$model);
    }

    public function update({$modelName}Request \$request, {$modelName} \$model)
    {
        \$model->update(\$request->validated());
        return response()->json(\$model);
    }

    public function destroy({$modelName} \$model)
    {
        \$model->delete();
        return response()->json(null, 204);
    }
}
PHP;

    File::put(app_path("Http/Controllers/{$modelName}Controller.php"), $controllerContent);
    $this->info("Controller created successfully.");
  }

  protected function generateRequest($modelName, $fields)
  {
    $rules = [];
    foreach ($fields as $field => $type) {
      $rules[] = "'{$field}' => 'required|" . $this->getValidationRule($type) . "'";
    }
    $rulesString = implode(",\n            ", $rules);

    $requestContent = <<<PHP
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class {$modelName}Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            {$rulesString}
        ];
    }
}
PHP;

    File::put(app_path("Http/Requests/{$modelName}Request.php"), $requestContent);
    $this->info("Request created successfully.");
  }

  protected function getValidationRule($type)
  {
    if (is_array($type) && $type['type'] === 'enum') {
      $options = implode(',', $type['options']);
      return 'in:' . $options;
    }

    switch ($type) {
      case 'string':
        return 'string|max:255';
      case 'text':
        return 'string';
      case 'integer':
        return 'integer';
      case 'boolean':
        return 'boolean';
      default:
        return 'string';
    }
  }

  protected function generateViews($modelName, $fields)
  {
    $viewsPath = resource_path("views/{$modelName}");

    // Check if directory exists before creating it
    if (!File::exists($viewsPath)) {
      File::makeDirectory($viewsPath, 0755, true);
    }

    // Generate index view
    $this->generateIndexView($modelName, $fields, $viewsPath);

    // Generate create view
    $this->generateCreateView($modelName, $fields, $viewsPath);

    // Generate edit view
    $this->generateEditView($modelName, $fields, $viewsPath);

    // Generate show view
    $this->generateShowView($modelName, $fields, $viewsPath);

    $this->info("Views created successfully.");
  }

  protected function generateIndexView($modelName, $fields, $viewsPath)
  {
    $fieldsList = '';
    foreach ($fields as $field => $type) {
      $fieldsList .= "<td>{{ \${$modelName}->{$field} }}</td>\n                ";
    }

    $content = <<<PHP
<x-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">{$modelName}s</h1>
            <a href="{{ route('{$modelName}s.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Create New</a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        {$fieldsList}
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (\${$modelName}s as \${$modelName})
                    <tr class="border-t">
                        {$fieldsList}
                        <td class="px-6 py-4">
                            <a href="{{ route('{$modelName}s.edit', \${$modelName}) }}" class="text-blue-500 hover:text-blue-700 mr-3">Edit</a>
                            <form action="{{ route('{$modelName}s.destroy', \${$modelName}) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout>
PHP;

    File::put("{$viewsPath}/index.blade.php", $content);
  }

  protected function generateCreateView($modelName, $fields, $viewsPath)
  {
    $formFields = '';
    foreach ($fields as $field => $type) {
      $formFields .= $this->generateFormField($field, $type, $modelName);
    }

    $content = <<<PHP
<x-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">Create {$modelName}</h1>

            <form action="{{ route('{$modelName}s.store') }}" method="POST" class="space-y-4">
                @csrf
                {$formFields}
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Create</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
PHP;

    File::put("{$viewsPath}/create.blade.php", $content);
  }

  protected function generateFormField($field, $type, $modelName, $isEdit = false)
  {
    $label = ucfirst(str_replace('_', ' ', $field));
    $value = $isEdit ? "{{ \${$modelName}->{$field} }}" : '';

    if (is_array($type) && $type['type'] === 'enum') {
      $optionsHtml = '';
      foreach ($type['options'] as $option) {
        $option = trim($option);
        $selected = $isEdit ? "{{ \${$modelName}->{$field} == '{$option}' ? 'selected' : '' }}" : '';
        $optionsHtml .= "<option value=\"{$option}\" {$selected}>{$option}</option>\n                    ";
      }

      return <<<PHP
                <div class="mb-4">
                    <label for="{$field}" class="block text-sm font-medium text-gray-700">{$label}</label>
                    <select name="{$field}" id="{$field}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        {$optionsHtml}
                    </select>
                    @error('{$field}')
                        <p class="text-red-500 text-xs mt-1">{{ \$message }}</p>
                    @enderror
                </div>
PHP;
    }

    $inputType = $type === 'text' ? 'textarea' : 'input';
    $inputAttributes = $type === 'text' ? 'rows="4"' : 'type="text"';

    return <<<PHP
                <div class="mb-4">
                    <label for="{$field}" class="block text-sm font-medium text-gray-700">{$label}</label>
                    <{$inputType} name="{$field}" id="{$field}" {$inputAttributes} class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" value="{$value}">
                    </{$inputType}>
                    @error('{$field}')
                        <p class="text-red-500 text-xs mt-1">{{ \$message }}</p>
                    @enderror
                </div>
PHP;
  }

  protected function generateEditView($modelName, $fields, $viewsPath)
  {
    $formFields = '';
    foreach ($fields as $field => $type) {
      $formFields .= $this->generateFormField($field, $type, $modelName, true);
    }

    $content = <<<PHP
<x-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">Edit {$modelName}</h1>

            <form action="{{ route('{$modelName}s.update', \${$modelName}) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                {$formFields}
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
PHP;

    File::put("{$viewsPath}/edit.blade.php", $content);
  }

  protected function generateShowView($modelName, $fields, $viewsPath)
  {
    $fieldsList = '';
    foreach ($fields as $field => $type) {
      $fieldsList .= <<<PHP
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">{$field}</h3>
                    <p class="mt-1">{{ \${$modelName}->{$field} }}</p>
                </div>
PHP;
    }

    $content = <<<PHP
<x-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">{$modelName} Details</h1>
                <div class="space-x-2">
                    <a href="{{ route('{$modelName}s.edit', \${$modelName}) }}" class="bg-blue-500 text-white px-4 py-2 rounded">Edit</a>
                    <a href="{{ route('{$modelName}s.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Back to List</a>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                {$fieldsList}
            </div>
        </div>
    </div>
</x-layout>
PHP;

    File::put("{$viewsPath}/show.blade.php", $content);
  }

  protected function generateRoutes($modelName)
  {
    $routeName = Str::kebab($modelName);

    // Add API routes
    $apiRoutes = <<<PHP
Route::apiResource('{$routeName}s', {$modelName}Controller::class);
PHP;

    File::append(base_path('routes/api.php'), "\n" . $apiRoutes);

    // Add web routes
    $webRoutes = <<<PHP
Route::resource('{$routeName}s', {$modelName}Controller::class);
PHP;

    File::append(base_path('routes/web.php'), "\n" . $webRoutes);

    $this->info("Routes created successfully.");
  }
}
