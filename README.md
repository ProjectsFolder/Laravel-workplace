# Laravel Docker Project

## Docker Setup

### Quick Start
```bash
make install
```

### Manual Setup
```bash
# Copy environment file
cp .env.example .env

# Build and start containers
make build
make up

# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
make migrate
```

## Available Commands

- `make build` - Build Docker images
- `make up` - Start all containers
- `make down` - Stop all containers
- `make restart` - Restart all containers
- `make shell` - Access application container
- `make logs` - View logs
- `make migrate` - Run database migrations
- `make migrate-fresh` - Fresh migration with seeding
- `make seed` - Run database seeders
- `make test` - Run tests
- `make clean` - Remove all containers and volumes
- `make mysql` - Access MySQL console
- `make redis` - Access Redis console
- `make rabbitmq` - List RabbitMQ queues

## Services

- **Application**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
- **MySQL**: localhost:3306
- **MySQL Replica**: localhost:3307
- **Redis**: localhost:6379
- **RabbitMQ Management**: http://localhost:15672 (admin/admin)

## Project Structure

- PHP 7.4 with Laravel 6
- MySQL 5.7 (master + replica)
- Redis 6
- RabbitMQ 3 with management interface
- Nginx web server
