# Laravel CRUD Generator

A powerful CRUD generator for Laravel applications that automatically generates models, controllers, requests, views, and routes.

## Features

- Generate complete CRUD operations with a single command
- Support for different field types (string, text, integer, boolean, enum)
- Automatic relationship handling (hasMany, belongsTo, belongsToMany)
- RESTful API endpoints generation
- Beautiful Blade views with Tailwind CSS
- Form validation
- Route model binding

## Installation

1. Clone the repository
2. Install dependencies:

```bash
composer install
npm install
```

3. Copy the environment file:

```bash
cp .env.example .env
```

4. Generate application key:

```bash
php artisan key:generate
```

5. Run migrations:

```bash
php artisan migrate
```

## Usage

Generate a complete CRUD for a model using the following command:

```bash
php artisan make:crud {model} --fields="field1:type1,field2:type2" --relations="relatedModel:relationType"
```

### Field Types

- `string`: VARCHAR field
- `text`: TEXT field
- `integer`: INTEGER field
- `boolean`: BOOLEAN field
- `enum`: ENUM field with options (e.g., `status:enum(open,closed)`)

### Relationship Types

- `hasMany`: One-to-Many relationship
- `belongsTo`: Many-to-One relationship
- `belongsToMany`: Many-to-Many relationship

### Basic Examples

1. Create a Project model with basic fields:

```bash
php artisan make:crud Project --fields="name:string,description:text,status:enum(open,closed)"
```

2. Create a Task model with a relationship to Project:

```bash
php artisan make:crud Task --fields="title:string,description:text,status:enum(pending,in_progress,completed)" --relations="project:belongsTo"
```

3. Create a Tag model with many-to-many relationship to Task:

```bash
php artisan make:crud Tag --fields="name:string,color:string" --relations="tasks:belongsToMany"
```

### Advanced Examples

4. Create a User model with multiple field types:

```bash
php artisan make:crud User --fields="name:string,email:string,password:string,age:integer,is_active:boolean,role:enum(admin,user,guest)"
```

5. Create a Post model with multiple relationships:

```bash
php artisan make:crud Post --fields="title:string,content:text,status:enum(draft,published)" --relations="author:belongsTo,categories:belongsToMany,comments:hasMany"
```

6. Create a Category model with nested relationships:

```bash
php artisan make:crud Category --fields="name:string,slug:string,parent_id:integer" --relations="parent:belongsTo,children:hasMany,posts:belongsToMany"
```

7. Create a Product model with complex fields:

```bash
php artisan make:crud Product --fields="name:string,sku:string,price:decimal,description:text,is_featured:boolean,status:enum(active,inactive,discontinued)" --relations="category:belongsTo,tags:belongsToMany,reviews:hasMany"
```

8. Create an Order model with multiple relationships:

```bash
php artisan make:crud Order --fields="order_number:string,total:decimal,status:enum(pending,processing,completed,cancelled)" --relations="customer:belongsTo,items:hasMany,payment:hasOne"
```

### Field Type Examples

Here are examples of different field types and their usage:

```bash
# String fields
php artisan make:crud Product --fields="name:string,sku:string,slug:string"

# Text fields
php artisan make:crud Article --fields="title:string,content:text,excerpt:text"

# Integer fields
php artisan make:crud Product --fields="name:string,stock:integer,price:integer"

# Boolean fields
php artisan make:crud User --fields="name:string,email:string,is_active:boolean,is_admin:boolean"

# Enum fields
php artisan make:crud Order --fields="order_number:string,status:enum(pending,processing,completed,cancelled)"
```

### Relationship Examples

Here are examples of different relationship types:

```bash
# One-to-Many relationship
php artisan make:crud Post --fields="title:string,content:text" --relations="comments:hasMany"

# Many-to-One relationship
php artisan make:crud Comment --fields="content:text" --relations="post:belongsTo"

# Many-to-Many relationship
php artisan make:crud Tag --fields="name:string" --relations="posts:belongsToMany"

# Multiple relationships
php artisan make:crud Post --fields="title:string,content:text" --relations="author:belongsTo,categories:belongsToMany,comments:hasMany"
```

## Generated Files

The generator creates the following files:

1. Model (`app/Models/{Model}.php`)
2. Migration (`database/migrations/{timestamp}_create_{table}_table.php`)
3. Controller (`app/Http/Controllers/{Model}Controller.php`)
4. Form Request (`app/Http/Requests/{Model}Request.php`)
5. Views (`resources/views/{model}/`)
    - index.blade.php
    - create.blade.php
    - edit.blade.php
    - show.blade.php
6. Routes (in `routes/web.php` and `routes/api.php`)

## Security

- All generated controllers use form requests for validation
- CSRF protection is enabled by default
- Route model binding is used for automatic model injection
- Input validation is automatically generated based on field types

## Contributing

Please feel free to submit issues and pull requests.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
