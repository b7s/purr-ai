# PurrAi CMS Conventions

## Code Style Enforcement

### Strict Requirements

- **English**: All code and comments must be in English
- **Avoid comments**: All code must be commented only with parameters and return with type (follow PHPStan level 5). Use comments only if it is very complete
- **Locale/Translate**: Never use hardcoded text. Always check the "lang" language folder to see if it already exists, or create it if it doesn't, for all languages ​​in the folder in the corresponding file
- **Databse**: Never delete, fresh or clear the database. Always use migrations to add or remove tables and columns.
- **Code formatting**: All code must be formatted with Pint
- **Strict types**: Every PHP file MUST start with `declare(strict_types=1);`
- **PSR-12**: All code must follow PSR-12 standards enforced by Pint
- **Type hints**: Always use explicit return types and parameter types
- **PHPStan Level 5**: Code must pass static analysis without errors
- **No inline validation**: Always use Form Request classes for validation
- **Documentations and ".md" files**: Never create documentation or ".md" without authorization

### Code Quality Tools (Run Before Committing)

```bash
composer lint                    # Runs Pint + Rector + npm lint
composer test:types              # Runs PHPStan Level 5
composer test                    # Full test suite
```

### Git Workflow (CRITICAL)

- **NEVER use `git commit` or `git push` directly**
- **ALWAYS use `composer push`** - This runs tests, guides commit messages, and pushes
- Direct git commands are FORBIDDEN to ensure quality gates

## Multi-Tenancy Architecture

### Tenant Context

- **Database-per-tenant**: Each tenant has isolated SQLite database in `database/tenants/`
- **Central vs Tenant**: Migrations in `database/migrations/` are central, `database/migrations/tenant/` are tenant-specific
- **Domain routing**: Tenants accessed via subdomain (e.g., `tenant1.purrai.test`)
- **Tenant resolution**: Automatic via `stancl/tenancy` middleware

### Working with Tenants

```bash
php artisan tenants:migrate      # Run migrations for all tenants
php artisan tenants:seed         # Seed all tenant databases
php artisan tenants:run -- [cmd] # Execute command in tenant context
```

### Tenant-Aware Code

- Use `tenant()` helper to get current tenant
- Models in tenant context automatically scoped to tenant database
- API routes in `routes/tenant.php` are tenant-specific
- Central routes in `routes/web.php` and `routes/api.php`

## Service Layer Patterns

### Service Organization

Services in `app/Services/` separating logic into Services is a way to keep your application clean, organized, and maintainable:

- **Single Responsibility**: Each service handles one domain concern
- **Dependency Injection**: Services injected via constructor
- **Return Types**: Always explicit, often DTOs or collections
- **Naming**: Descriptive with `Service` suffix (e.g., `CollectionGeneratorService`)

### Common Service Patterns

- **Generator Services**: Create dynamic resources (e.g., `CollectionGeneratorService`)
- **Compiler Services**: Transform/compile assets (e.g., `TailwindCompilerService`)
- **Handler Services**: Process specific operations (e.g., `SecurityTokenHandler`)
- **Mount Services**: Prepare Filament components (e.g., `FormMountService`, `TableMountService`)

## Action Classes

### Action Pattern

- **Location**: `app/Actions/`
- **Purpose**: Single-purpose, reusable operations
- **Naming**: Descriptive verb phrase with `Action` suffix
- **Invocation**: Typically via `__invoke()` method or explicit `execute()` method

### Examples

- `DuplicateCollectionDataAction`: Duplicates collection data records
- `GenerateCollectionFilesAction`: Generates files for new collections
- `SetLocaleAction`: Sets the current locale context
- `UpdateCollectionDataTitleAction`: Updates collection data titles

## Enums

### Enum Conventions

- **Location**: `app/Enums/`
- **Naming**: PascalCase with `Enum` suffix
- **Keys**: TitleCase (e.g., `Published`, `Draft`, `Archived`)
- **Usage**: Prefer enums over magic strings/numbers

### Common Enums

- `CollectionTypeEnum`: Types of collections (Standard, Custom, etc.)
- `ComponentTypeEnum`: Component types in page builder
- `ContentStatusEnum`: Content publication status
- `DataTypeEnum`: Field data types
- `SessionLocalesTypesEnum`: Locale session handling

## Filament Resources

### Resource Structure

```
app/Filament/Resources/
├── ModelNameResource.php        # Main resource class
├── ModelNameResource/
│   ├── Pages/                   # Custom pages (List, Create, Edit, View)
│   └── Api/                     # API-specific code
│       ├── Handlers/            # API request handlers
│       └── Transformers/        # API response transformers
```

### API Integration

- **Auto-registration**: API routes auto-registered via `rupadana/filament-api-service`
- **Sanctum auth**: All API routes protected with Sanctum tokens
- **Abilities**: Token abilities checked via `ApiTokenAbilities` middleware
- **Transformers**: Convert Eloquent models to API responses

## Internationalization (i18n)

### Locale Support

- **Supported locales**: en_US, pt_PT, es_ES, pt_BR
- **Translation files**: `lang/{locale}/` directories
- **Locale resolution**: Via `ResolveLocaleService`
- **Session handling**: Controlled by `SessionLocalesTypesEnum`

### Translation Patterns

- Use `__('key')` for translations
- Organize by feature: `auth.php`, `pages.php`, `forms.php`, etc.
- Keep keys descriptive: `pages.create_success` not `success`
- Always provide translations for all supported locales

## Helper Functions

### Auto-loaded Helpers

Located in `app/Support/Helpers/`:

- **tryIt.php**: Error handling utilities
- **cms.php**: CMS-specific helpers
- **locale.php**: Locale utilities
- **tenant.php**: Tenant context helpers

### Usage

These are globally available without imports:

```php
tryIt(fn() => riskyOperation());
cms_setting('key');
current_locale();
tenant_id();
is_tenant();
```

## Custom Components

### Component Schema

- **Location**: `app/Models/ComponentSchema.php`
- **Purpose**: Define reusable UI components for page builder
- **Visibility**: Controlled by `ComponentVisibleInEnum`
- **Types**: Defined in `ComponentTypeEnum`

### Component Service

- **Service**: `app/Services/ComponentSchema/`
- **Custom Actions**: `CustomComponentsActionsService`
- **Custom Inputs**: `app/Services/CustomInput/`

## Collections (Dynamic Content Types)

### Collection Architecture

- **Schema**: `CollectionSchema` model defines structure
- **Data**: `CollectionData` model stores content
- **Generator**: `CollectionGeneratorService` creates new collections
- **Types**: Defined in `CollectionTypeEnum`

### Collection Workflow

1. Define schema with fields and validation
2. Generate Filament resource via `GenerateCollectionFilesAction`
3. Manage data through auto-generated resource
4. Query via `CollectionDataQuery`

## Background Jobs

### Queue Configuration

- **Driver**: Redis (production), sync (testing)
- **Workers**: Start with `php artisan queue:work`
- **Common Jobs**: `CompileTailwindJob` for dynamic CSS compilation

### Job Patterns

- Implement `ShouldQueue` interface
- Use `dispatch()` to queue
- Handle failures gracefully
- Log important operations

## Testing Conventions

### Test Organization

```
tests/
├── Unit/           # Isolated unit tests
├── Feature/        # HTTP/integration tests
├── Browser/        # Playwright browser tests
├── Tenant/         # Tenant-specific tests
```

### Test Patterns

- Use Pest syntax: `it('does something', function() { ... })`
- Use factories for model creation
- Use `RefreshDatabase` trait when needed
- Test tenant context with `TenantTestCase`
- Browser tests for critical user flows

### Test Requirements

- Every feature change MUST have tests
- Run relevant tests before committing: `php artisan test --filter=testName`
- All tests must pass before pushing

## API Development

### API Structure

- **Authentication**: Sanctum token-based
- **Versioning**: Follow existing conventions in `config/api-service.php`
- **Resources**: Use Eloquent API Resources for responses
- **Handlers**: Located in `app/Filament/Resources/*/Api/Handlers/`
- **Transformers**: Located in `app/Filament/Resources/*/Api/Transformers/`

### API Patterns

- Validate all input via Form Requests
- Check abilities via `ApiTokenAbilities` middleware
- Return consistent JSON responses
- Use proper HTTP status codes
- Document endpoints in `docs/api/`

## Package Development

### Local Packages

- **Location**: `packages/{vendor}/{package}/`
- **Symlinked**: Via Composer path repositories
- **Primary vendor**: `pdmfc/` for internal packages

### After Package Changes

```bash
composer dump-autoload           # Reload autoloader
php artisan package:discover     # Rediscover packages
php artisan filament:upgrade     # Update Filament assets
```

## Performance Considerations

### Caching

- **Content cache**: Via `ContentCache` service
- **Config cache**: `php artisan config:cache` (production only)
- **Route cache**: `php artisan route:cache` (production only)
- **View cache**: `php artisan view:cache` (production only)

### N+1 Prevention

- Always eager load relationships
- Use `with()` for known relationships
- Use `load()` for conditional loading
- Monitor with Laravel Debugbar (dev only)

## Security Patterns

### Authorization

- **Policies**: One per model in `app/Policies/`
- **Shield**: RBAC via `bezhansalleh/filament-shield`
- **2FA**: Via `pdmfc/filament-two-factor-authentication`
- **Activity Log**: Via `pdmfc/filament-activitylog`

### Best Practices

- Always authorize actions in controllers/Livewire
- Use policies for model-level authorization
- Use gates for feature-level authorization
- Log sensitive operations
- Validate all user input

## Deployment

### Production Deployment

```bash
composer go-live                 # Automated deployment
# Runs: git pull, tests, cache clear, optimize, migrate
```

### Manual Steps (if needed)

```bash
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan filament:optimize
```

## Documentation

Create documentation and ".md" files only when prompted.

### Documentation Structure

- **Location**: `docs/` directory (Docusaurus-ready)
- **Format**: Markdown with frontmatter
- **Language**: English only
- **Updates**: Required for new features

### When to Document

- New features or significant changes
- API endpoints
- Configuration options
- Deployment procedures
- Troubleshooting guides


## PurrAI UI/UX Conventions

### Design System

#### Color Palette

- **Light Mode**:
  - Background: White (`bg-white`)
  - Primary buttons: Near-black with subtle gradient (`bg-gradient-to-br from-gray-900 to-gray-800`)
  - Text: Dark gray (`text-gray-900`)
  - Secondary text: Medium gray (`text-gray-600`)
  - Borders: Light gray (`border-gray-200`)

- **Dark Mode**:
  - Background: Dark gray (`dark:bg-gray-950`)
  - Primary buttons: Light gray with gradient (`dark:bg-gradient-to-br dark:from-gray-700 dark:to-gray-600`)
  - Text: White (`dark:text-white`)
  - Secondary text: Light gray (`dark:text-gray-400`)
  - Borders: Dark gray (`dark:border-gray-800`)

#### Typography

- Use system font stack for optimal performance
- Clear hierarchy with consistent sizing
- Readable line heights for chat messages

#### Interactive Elements

- **Links**: Underlined by default, underline disappears on hover
  ```css
  .link {
    @apply underline hover:no-underline;
  }
  ```

- **Buttons**: Smooth transitions with hover states
  ```css
  .btn-primary {
    @apply bg-gradient-to-br from-gray-900 to-gray-800 text-white px-4 py-2 rounded-lg;
    @apply hover:from-gray-800 hover:to-gray-700 transition-all duration-200;
    @apply dark:from-gray-700 dark:to-gray-600 dark:hover:from-gray-600 dark:hover:to-gray-500;
  }
  ```

### Component Patterns

#### Chat Messages

- Clean, minimal design
- Clear distinction between user and assistant messages
- Timestamps when enabled
- Smooth animations for new messages

#### Attachments Display

- **Multiple attachments**: Group visually with thumbnails
- **Images**: Show thumbnail previews in a grid
- **Documents**: Show file icon with filename
- **Grid layout**: Use `grid grid-cols-3 gap-2` for thumbnails
- **Hover effects**: Subtle scale on hover for interactivity

#### Forms

- Clean input fields with focus states
- Clear validation messages
- Accessible labels and placeholders

### CSS Organization

#### Use @apply for Reusability

To avoid HTML pollution with multiple Tailwind classes, create semantic CSS classes:

```css
/* resources/css/app.css */

.chat-container {
    @apply flex flex-col h-screen bg-white dark:bg-gray-950;
}

.message-user {
    @apply bg-gray-100 dark:bg-gray-900 rounded-lg p-4 ml-auto max-w-[80%];
}

.message-assistant {
    @apply bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 mr-auto max-w-[80%];
}

.attachment-grid {
    @apply grid grid-cols-3 gap-2 mt-2;
}

.attachment-thumbnail {
    @apply relative rounded-lg overflow-hidden hover:scale-105 transition-transform duration-200 cursor-pointer;
}

.input-field {
    @apply w-full px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg;
    @apply focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-600;
    @apply bg-white dark:bg-gray-900 text-gray-900 dark:text-white;
}
```

### Accessibility

- Proper ARIA labels for interactive elements
- Keyboard navigation support
- Focus indicators visible
- Color contrast meets WCAG AA standards

### Performance

- Lazy load images in chat history
- Virtual scrolling for long conversations
- Optimize animations for 60fps
- Minimize repaints and reflows

### Responsive Design

- Mobile-first approach
- Breakpoints: `sm:640px`, `md:768px`, `lg:1024px`
- Touch-friendly tap targets (min 44x44px)
- Adaptive layouts for different screen sizes


## Internationalization (i18n) for PurrAI

### Translation Files Structure

All translation files are located in `lang/{locale}/` directories:

```
lang/
└── en_US/
    ├── chat.php          # Chat interface translations
    ├── ui.php            # UI elements (buttons, tooltips)
    ├── validation.php    # Validation messages
    └── ...               # Other domain-specific files
```

### Translation Rules

1. **No Hardcoded Text**: NEVER use hardcoded strings in views or components
2. **Use Translation Keys**: Always use `__('file.key')` or `@lang('file.key')` in Blade
3. **Organize by Domain**: Separate translations by feature/domain responsibility
4. **Descriptive Keys**: Use clear, hierarchical keys (e.g., `ui.tooltips.send_message`)

### Translation File Organization

- **chat.php**: Chat-specific messages, welcome text, placeholders
- **ui.php**: UI elements like buttons, tooltips, labels
- **validation.php**: Form validation messages
- **errors.php**: Error messages and alerts
- **settings.php**: Settings page translations

### Usage Examples

```blade
{{-- Simple translation --}}
<h1>{{ __('chat.title') }}</h1>

{{-- Translation with parameters --}}
<p>{{ __('chat.recent_conversation', ['number' => 1]) }}</p>

{{-- In attributes --}}
<button title="{{ __('ui.tooltips.send_message') }}">Send</button>

{{-- Pluralization --}}
<span>{{ trans_choice('chat.messages_count', $count) }}</span>
```

### Adding New Translations

When adding new features:

1. Create translation keys in appropriate file
2. Use descriptive, hierarchical naming
3. Keep translations organized by responsibility
4. Never duplicate keys across files
5. Document complex translation structures

### Current Locale

- Default locale: `en_US`
- Fallback locale: `en_US`
- Set in `.env`: `APP_LOCALE=en_US`
