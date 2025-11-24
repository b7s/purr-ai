# Project Structure

## General

- **English**: All code and comments must be in English
- **Avoid comments**: All code must be commented only with parameters and return with type (follow PHPStan level 5). Use comments only if it is very complete
- **Locale/Translate**: Never use hardcoded text. Always check the "lang" language folder to see if it already exists, or create it if it doesn't, for all languages ​​in the folder in the corresponding file
- Only create ".md" documentation files if the user requests
- When you need to search for some data in the database, create a test code and run it to find what you are looking for. If you need to find something that is not in a tool/mcp, create code to search on Google. Remove the file after use and no longer need.

## Root Organization

```
app/                    # Application code (PSR-4: App\)
bootstrap/              # Framework bootstrap
config/                 # Configuration files
database/               # Migrations, seeders, factories, SQLite files
docs/                   # Comprehensive documentation (Docusaurus-ready)
lang/                   # Translation files (en_US, es_ES, pt_PT)
packages/               # Local package development (symlinked)
public/                 # Web root (index.php, assets)
resources/              # Views, JS, CSS source files
routes/                 # Route definitions (web, api, tenant)
scripts/                # Deployment and utility scripts
storage/                # Logs, cache, uploads
tests/                  # Pest tests (Unit, Feature, Browser, Tenant)
vendor/                 # Composer dependencies
```

## Application Structure (`app/`)

### Core Directories

- **Actions/**: Single-purpose action classes (e.g., `DuplicateCollectionDataAction`)
- **Console/Commands/**: Artisan commands
- **Enums/**: Typed enumerations for domain values (e.g., `CollectionTypeEnum`, `ContentStatusEnum`)
- **Http/**: Controllers, middleware, requests
- **Models/**: Eloquent models with relationships and scopes
- **Policies/**: Authorization policies for each model
- **Providers/**: Service providers (AppServiceProvider, TenancyServiceProvider, BladeDirectivesServiceProvider)
- **Queries/**: Query builder classes for complex queries
- **Services/**: Business logic and domain services
- **Support/**: Helper functions and utilities
- **Services/**: Separating logic into Services is a way to keep your application clean, organized, and maintainable

### Filament Structure (`app/Filament/`)

```
Filament/
├── Admin/              # Admin panel resources
├── Custom/             # Custom Filament components
├── Exports/            # Export definitions
├── Pages/              # Custom Filament pages
├── PublicPages/        # Public-facing pages
├── Resources/          # CRUD resources
│   └── */Api/          # API handlers and transformers per resource
└── Widgets/            # Dashboard widgets
```

### Services Organization (`app/Services/`)

Services encapsulate complex business logic:

- **ApiRouteService**: API route registration and management
- **ApiTokenAbilities/**: Token ability management
- **CodeSection/**: Code generation utilities
- **CollectionGenerator/**: Dynamic collection creation
- **ComponentSchema/**: Component schema handling
- **ContentCache**: Content caching strategies
- **ContentField/**: Field type handling
- **CustomComponentsActionsService**: Custom component actions
- **CustomInput/**: Custom input field services
- **FilamentResource/**: Form and table mounting services
- **Form/**: Form processing and validation
- **JsonFilterService**: JSON filtering utilities
- **Layout/**: Layout rendering services
- **LocalesService**: Locale management
- **PublicPageRender/**: Page content rendering
- **PublicFormSubmission/**: Public form handling
- **ResolveLocaleService**: Locale resolution
- **SecurityTokenHandler**: Token security
- **TailwindCompilerService**: Dynamic Tailwind compilation
- **Translation/**: Translation services

## Database Structure (`database/`)

```
database/
├── factories/          # Model factories for testing
├── migrations/         # Central database migrations
│   └── tenant/         # Tenant-specific migrations (isolated per tenant)
├── seeders/            # Database seeders
│   └── tenant/         # Tenant-specific seeders
├── tenants/            # Tenant database files (SQLite)
├── database.sqlite     # Central database
└── testing.sqlite      # Test database
```

## Configuration Patterns

- **Multi-tenancy**: `config/tenancy.php` defines tenant bootstrapping
- **CMS Settings**: `config/cms.php` for CMS-specific configuration
- **API Service**: `config/api-service.php` for API route configuration
- **Filament**: `config/filament.php` for admin panel settings

## Routing Structure (`routes/`)

- **web.php**: Central web routes
- **api.php**: Central API routes
- **tenant.php**: Tenant-specific routes (domain-based)
- **console.php**: Artisan command routes

## Testing Structure (`tests/`)

```
tests/
├── Browser/            # Playwright browser tests
├── Feature/            # Feature tests (HTTP, integration)
├── Tenant/             # Tenant-specific tests
├── Unit/               # Unit tests (isolated logic)
├── Pest.php            # Pest configuration
├── TestCase.php        # Base test case
└── TenantTestCase.php  # Tenant test case
```

## Package Development (`packages/`)

Local packages organized by vendor:

```
packages/
├── achyutn/filament-log-viewer/
├── awcodes/filament-curator/
├── bezhansalleh/filament-shield/
├── eightynine/filament-approvals/
├── juliomotol/filament-password-confirmation/
├── pdmfc/                          # Primary vendor packages
│   ├── filament-activitylog/
│   ├── filament-edit-profile/
│   ├── filament-laravel-pulse/
│   ├── filament-matinee/
│   ├── filament-menu-builder/
│   ├── filament-page-builder/
│   ├── filament-sortable/
│   └── filament-two-factor-authentication/
├── rupadana/filament-api-service/
└── tomatophp/filament-tenancy/
```

## Naming Conventions

- **Models**: Singular PascalCase (e.g., `CollectionData`, `ComponentSchema`)
- **Controllers**: PascalCase with `Controller` suffix
- **Services**: PascalCase with `Service` suffix
- **Actions**: PascalCase with `Action` suffix
- **Enums**: PascalCase with `Enum` suffix
- **Policies**: PascalCase with `Policy` suffix
- **Migrations**: Snake_case with timestamp prefix
- **Views**: Kebab-case (e.g., `collection-data.blade.php`)
- **Routes**: Kebab-case (e.g., `/collection-data`)

## Helper Files

Auto-loaded helper functions in `app/Support/Helpers/`:

- `tryIt.php`: Error handling utilities
- `cms.php`: CMS-specific helpers
- `locale.php`: Locale utilities
- `tenant.php`: Tenant context helpers

## Documentation Structure (`docs/`)

Organized for Docusaurus with categories:

- **getting-started/**: Installation, environment, development
- **architecture/**: System design and decisions
- **domain-guides/**: Feature-specific guides
- **api/**: API documentation and examples
- **advanced/**: Caching, events, queues, multitenancy
- **security/**: Authentication, authorization, activity log
- **operations/**: Deployment, SSL, Nginx configuration
- **contributing/**: Code style, testing, commit messages
- **reference/**: Complete API reference
- **troubleshooting/**: Common errors and FAQs
