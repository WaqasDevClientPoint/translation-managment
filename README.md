# Translation Management Service

A scalable and high-performance translation management system built with Laravel.

## Features

- Multi-locale support (en, fr, es, etc.)
- Context-based tagging (mobile, desktop, web)
- High-performance JSON export for frontend applications
- Token-based authentication
- Efficient caching system
- Scalable to 100k+ translations
- Docker support
- CDN integration


## Usage

### API Endpoints

#### Authentication
```
POST /api/login
```

## API Documentation

### Authentication
```
POST /api/login
```

Request:
```json
{
    "email": "test@example.com",
    "password": "password"
}
```

Response:
```json
{
    "token": "1|abcdef123456..."
}
```

### Translations API

All protected endpoints require the Authorization header:
```
Authorization: Bearer your_token
```

#### List Translations
```bash
GET /api/translations?locale=en&page=1
```

#### Search Translations
```bash
GET /api/translations/search?query=welcome&locale=en&tags[]=mobile
```

Parameters:
- query (required): Search term (minimum 2 characters)
- locale (optional): Language code (e.g., en, fr, es)
- tags (optional): Array of tags to filter by

#### Create Translation
```bash
POST /api/translations
Content-Type: application/json

{
    "key": "welcome.message",
    "value": "Welcome to our app",
    "language_id": 1,
    "tags": ["mobile", "web"]
}
```

#### Update Translation
```bash
PUT /api/translations/{id}
Content-Type: application/json

{
    "key": "welcome.message",
    "value": "Updated welcome message",
    "language_id": 1,
    "tags": ["mobile", "web"]
}
```

#### Delete Translation
```bash
DELETE /api/translations/{id}
```

#### Export Translations
```bash
GET /api/translations/export?locale=en
```

## Technical Stack

- PHP 8.2
- Laravel 10.x
- MySQL 8.0
- Redis for caching
- Docker & Docker Compose
- AWS S3 (optional, for CDN)

## Installation

1. Clone the repository: https://github.com/WaqasDevClientPoint/translation-managment.git
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Run `php artisan key:generate`
5. Run `php artisan migrate`
6. Create an API user using the command above

### Generate Test Data

```bash
php artisan translations:generate 100000
```

### Create API User

```bash
php artisan api:create-user
```
## Docker Setup

### Prerequisites
- Docker
- Docker Compose

### Quick Start
1. Clone the repository
2. Copy `.env.example` to `.env`
3. Run Docker containers:
```bash
docker-compose up -d
```

4. Install dependencies:
```bash
docker-compose exec app composer install
```

5. Generate application key:
```bash
docker-compose exec app php artisan key:generate
```

6. Run migrations:
```bash
docker-compose exec app php artisan migrate
```

7. Create API user:
```bash
docker-compose exec app php artisan api:create-user
```

### Docker Services
- **app**: PHP 8.2 + Laravel
- **mysql**: MySQL 8.0
- **redis**: Redis for caching
- **nginx**: Nginx web server

### Accessing the Application
- API: `http://localhost:8000/api`
- MySQL: `localhost:3306`
- Redis: `localhost:6379`

### Common Docker Commands
```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f

# Access container shell
docker-compose exec app bash
```

## Performance Considerations

1. Database Optimization
   - Proper indexing on frequently queried columns
   - Composite indexes for common query patterns
   - Efficient table relationships

2. Caching Strategy
   - Redis caching for frequently accessed data
   - Cache tags for selective cache invalidation
   - Cached responses for export endpoint

3. Query Optimization
   - Eager loading relationships
   - Chunked processing for large datasets
   - Pagination for list endpoints

## Testing

Run the test suite:

```bash
php artisan test
```

Performance tests:
```bash
php artisan test --filter=TranslationPerformanceTest
```

## Design Choices

1. **Service Layer Pattern**
   - Separation of concerns
   - Reusable business logic
   - Easier testing and maintenance

2. **Repository Pattern**
   - Database abstraction
   - Swappable implementations
   - Clean controller code

3. **Caching Strategy**
   - Redis for better performance
   - Tag-based cache invalidation
   - Selective caching based on request patterns

4. **Database Schema**
   - Normalized structure
   - Efficient indexing
   - Scalable relationships



