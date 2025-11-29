.PHONY: help run build test clean docker-up docker-down

help:
	@echo "Available commands:"
	@echo "  make run         - Run the application"
	@echo "  make build       - Build the application"
	@echo "  make test        - Run tests"
	@echo "  make clean       - Clean build artifacts"
	@echo "  make docker-up   - Start PostgreSQL with Docker Compose"
	@echo "  make docker-down - Stop Docker Compose services"

run:
	go run cmd/server/main.go

build:
	go build -o bin/finapp cmd/server/main.go

test:
	go test -v ./...

clean:
	rm -rf bin/
	go clean

docker-up:
	docker-compose up -d
	@echo "Waiting for PostgreSQL to be ready..."
	@sleep 3

docker-down:
	docker-compose down
