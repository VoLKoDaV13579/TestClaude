.PHONY: help run build test clean docker-up docker-down docker-build docker-logs docker-restart

help:
	@echo "Available commands:"
	@echo "  make run             - Run the application locally"
	@echo "  make build           - Build the application binary"
	@echo "  make test            - Run tests"
	@echo "  make clean           - Clean build artifacts"
	@echo "  make docker-build    - Build Docker images"
	@echo "  make docker-up       - Start all services with Docker Compose"
	@echo "  make docker-down     - Stop Docker Compose services"
	@echo "  make docker-logs     - Show logs from all services"
	@echo "  make docker-restart  - Restart all Docker services"

run:
	go run cmd/server/main.go

build:
	go build -o bin/finapp cmd/server/main.go

test:
	go test -v ./...

clean:
	rm -rf bin/
	go clean

docker-build:
	docker-compose build

docker-up:
	docker-compose up -d
	@echo "Services are starting..."
	@echo "PostgreSQL will be available at localhost:5432"
	@echo "Application will be available at http://localhost:8080"

docker-down:
	docker-compose down

docker-logs:
	docker-compose logs -f

docker-restart:
	docker-compose restart
