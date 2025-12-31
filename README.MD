# PBase

A production-ready PHP website skeleton designed to help you quickly start building functional websites. This skeleton is based on real-world use cases and has been tested in production environments.

## Features

- **Database Skeleton**: Pre-configured database structure and migration system
- **Manager Layer**: Handles database access and management operations
- **Utility Layer**: Common utility functions and helpers
- **Internationalization**: Built-in i18n support using php-i18n
- **Security**: HTMLPurifier integration for input sanitization
- **Modular Architecture**: Clean separation of concerns with organized folder structure

## System Requirements

- PHP 8.0 or higher
- Composer (for dependency management)
- MySQL/MariaDB (or compatible database)

## Installation

1. Clone or download this repository
2. Install dependencies using Composer:
   ```bash
   composer install
   ```
3. Configure your database connection in `includes/config.php`
4. Run database migrations from the `database/` directory

## Project Structure

```
pbase/
├── admin/              # Administrative pages for site management
├── api/                # Backend API layer (bridge between frontend and backend)
├── css/                # CSS files for page styling
│   └── external/       # Third-party CSS libraries (e.g., Font Awesome)
├── database/           # Database schema files and migrations
├── documentation/      # API and design documentation (useful for AI agents)
├── images/             # Image resources (logos, icons, etc.)
├── includes/           # Common components (header, footer, config)
├── js/                 # JavaScript files for page interactions
│   └── external/       # Third-party JavaScript libraries (e.g., jQuery)
├── lib/                # Backend processing logic
│   ├── entity/         # Database object definitions
│   ├── manager/        # Database Manager classes
│   └── util/           # Utility functions
└── locale/             # Language files for internationalization
```

### Directory Details

- **css/**: Contains all CSS files used for page styling
  - **css/external/**: Hosts external third-party CSS libraries (e.g., Font Awesome, Bootstrap)

- **js/**: Contains JavaScript files for handling page interactions
  - **js/external/**: Hosts external third-party JavaScript libraries (e.g., jQuery)

- **lib/**: Contains backend processing logic including:
  - Database object definitions (entity classes)
  - Database Manager classes for data access
  - Utility functions and helpers

- **includes/**: Contains commonly used components:
  - Header and footer templates
  - Configuration files
  - Shared PHP includes

- **database/**: Contains database schema files and migrations
  - Schema files should be incremental (not modified in place)
  - For table structure changes, create new SQL files with `ALTER TABLE` statements
  - Follow naming convention: `####-description.sql`

- **admin/**: Administrative pages for site administrators
  - Includes page view management and other administrative functions

- **api/**: Backend API layer serving as the bridge between frontend and backend

- **documentation/**: Contains API and design documents
  - Useful for AI agents and developers to understand the system

- **images/**: All image resources including logos and other assets

## Dependencies

This project uses the following main dependencies (managed via Composer):

- **ezyang/htmlpurifier** (^4.18): HTML sanitization and filtering
- **philipp15b/php-i18n** (^4.0): Internationalization and localization support

## Usage

1. Start by configuring your database connection in `lib/DataConnection.php`
   - Should set environment variables such as DB_NAME, DB_USERNAME and DB_PASSWORD before starting the server.
2. Run the initial database migration from `database/0001-create-xxx.sql`
3. Define your entity definition in `lib/util/generate_entity.php` and run below in browser to generate entity class files for database record mapping.
   - http://<your-local-host>/lib/util/generate_entity.php
4. Customize the header and footer in `includes/header.php` and `includes/footer.php`
5. Add your application logic in the `lib/` directory
6. Create your pages using the included components
7. Run `composer install` to install dependencise.

## Contributing

When working in a team environment:
- Database schema files should be incremental (add new migration files, don't modify existing ones)
- Follow the existing folder structure
- Document API changes in the `documentation/` directory

## License

This project is licensed under the MIT License.
