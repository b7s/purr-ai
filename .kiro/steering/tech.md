# Technology Stack

## Core Stack

- **PHP**: 8.4+ with strict types (`declare(strict_types=1);` required in all files)
- **Laravel**: 12.x framework
- **Database**: SQLite (development), supports PostgreSQL/MySQL (production)
- **Web Server**: Nginx (required for production)
- **Cache/Queue**: Redis

## Frontend

- **Admin Panel**: Filament 4.x
- **UI Framework**: Livewire 3.x
- **JavaScript**: Alpine.js 3.x
- **CSS**: Tailwind CSS 4.x
- **Build Tool**: Vite 7.x
- **Package Manager**: npm 10+

## Key Laravel Packages

- **Multi-tenancy**: `stancl/tenancy` (database-per-tenant)
- **Authentication**: `laravel/sanctum` (API tokens)
- **Authorization**: `bezhansalleh/filament-shield` (RBAC)
- **Media**: `awcodes/filament-curator`
- **Activity Log**: `pdmfc/filament-activitylog`
- **Monitoring**: `laravel/pulse`
- **Broadcasting**: `laravel/reverb`
- **2FA**: `pdmfc/filament-two-factor-authentication`

## Development Tools

- **Testing**: Pest 4.x (PHPUnit wrapper)
- **Browser Testing**: Playwright
- **Code Style**: Laravel Pint (PSR-12)
- **Static Analysis**: PHPStan Level 5 (Larastan)
- **Refactoring**: Rector with Laravel rules
- **Debugging**: Laravel Debugbar, Telescope (dev only)

## Common Commands

### Development
```bash
# Initial setup
composer go-staging              # Full local installation with seeding

# Daily development
npm run dev                      # Start Vite dev server
php artisan queue:work           # Start queue workers
php artisan serve                # Start dev server (if not using Nginx)

# Create resources
php artisan make:filament-resource ModelName --generate
php artisan make:filament-user   # Create admin user
```

### Code Quality
```bash
composer lint                    # Run Pint + Rector + npm lint
composer test:types              # Run PHPStan Level 5
composer test:unit               # Run Pest tests
composer test                    # Run all tests (type coverage, unit, lint, types)

# Individual tools
./vendor/bin/pint                # Fix code style
./vendor/bin/pint --test         # Check code style without fixing
./vendor/bin/phpstan analyse     # Static analysis
./vendor/bin/rector process      # Refactor code
./vendor/bin/rector --dry-run    # Preview refactoring
pest                             # Run tests
```

### Git Workflow
```bash
composer push                    # REQUIRED: runs tests, guides commit, pushes
# Direct git commit/push is FORBIDDEN - always use composer push
```

### Multi-tenancy
```bash
php artisan tenants:migrate      # Run tenant migrations
php artisan tenants:seed         # Seed tenant databases
php artisan tenants:run -- [cmd] # Run command in tenant context
```

### Deployment
```bash
composer go-live                 # Automated production deployment
# Runs: git pull, tests, cache clear, optimize, migrate
```

### Database
```bash
php artisan migrate              # Run central migrations
php artisan db:seed              # Seed central database
php artisan migrate:fresh --seed # Fresh install with data
```

## Build System

- **Vite**: Bundles JS/CSS from `resources/` to `public/build/`
- **Tailwind**: Compiled via Vite with PostCSS
- **Asset Publishing**: `php artisan vendor:publish --tag=laravel-assets`
- **Production Build**: `npm run build` (minified, optimized)

## Package Development

Local packages in `packages/` are symlinked via Composer path repositories. Changes to packages require:
```bash
composer dump-autoload           # Reload autoloader
php artisan package:discover     # Rediscover packages
```
