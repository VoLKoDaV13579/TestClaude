# FinApp - Financial Management Platform

A secure financial management application built with Go, featuring user authentication and account management. Designed with enterprise-grade security standards suitable for medium financial organizations.

## Features

- 🔒 **Secure Authentication**: JWT-based authentication with bcrypt password hashing
- 👤 **User Registration & Login**: Complete user management system
- 💰 **Account Management**: Create, view, and delete multiple financial accounts
- 🌍 **Multi-Currency Support**: Support for USD, EUR, GBP, RUB and more
- 🎨 **Modern UI**: Clean, responsive web interface
- 🛡️ **Security Best Practices**:
  - Password hashing with bcrypt
  - JWT token authentication
  - HTTP-only cookies
  - SQL injection protection via GORM
  - Input validation

## Technology Stack

- **Backend**: Go 1.21+
- **Web Framework**: Gin
- **Database**: PostgreSQL
- **ORM**: GORM
- **Authentication**: JWT (golang-jwt/jwt)
- **Password Hashing**: bcrypt
- **Configuration**: godotenv

## Project Structure

```
.
├── cmd/
│   └── server/          # Application entry point
│       └── main.go
├── config/              # Configuration management
│   └── config.go
├── internal/
│   ├── auth/           # Authentication logic
│   │   └── jwt.go
│   ├── database/       # Database connection
│   │   └── database.go
│   ├── handlers/       # HTTP handlers
│   │   ├── auth.go
│   │   ├── accounts.go
│   │   └── home.go
│   ├── middleware/     # HTTP middleware
│   │   └── auth.go
│   └── models/         # Data models
│       ├── user.go
│       └── account.go
├── static/             # Static assets
│   └── css/
│       └── style.css
├── templates/          # HTML templates
│   ├── index.html
│   ├── login.html
│   ├── register.html
│   ├── dashboard.html
│   └── error.html
├── .env.example        # Environment variables template
├── go.mod
└── go.sum
```

## Prerequisites

- Go 1.21 or higher
- PostgreSQL 12 or higher
- Git

## Installation

1. Clone the repository:
```bash
git clone https://github.com/VoLKoDaV13579/TestClaude.git
cd TestClaude
```

2. Install dependencies:
```bash
go mod download
```

3. Setup PostgreSQL database:
```bash
# Create database
createdb finapp

# Or using psql
psql -U postgres
CREATE DATABASE finapp;
```

4. Configure environment variables:
```bash
cp .env.example .env
# Edit .env with your database credentials
```

5. Run the application:
```bash
go run cmd/server/main.go
```

The server will start on `http://localhost:8080`

## Configuration

Edit the `.env` file to configure the application:

```env
SERVER_PORT=8080
DB_HOST=localhost
DB_PORT=5432
DB_USER=postgres
DB_PASSWORD=postgres
DB_NAME=finapp
JWT_SECRET=your-secret-key-change-this-in-production
SESSION_TIMEOUT=24h
```

## API Endpoints

### Public Routes
- `GET /` - Welcome page
- `GET /register` - Registration page
- `POST /register` - User registration
- `GET /login` - Login page
- `POST /login` - User authentication

### Protected Routes
- `GET /dashboard` - User dashboard
- `GET /logout` - User logout

### API Routes (Require Authentication)
- `GET /api/accounts` - Get all user accounts
- `POST /api/accounts` - Create new account
- `DELETE /api/accounts/:id` - Delete account

## Security Features

1. **Password Security**
   - Passwords are hashed using bcrypt with default cost
   - Minimum password length: 8 characters
   - Passwords never stored in plain text

2. **Authentication**
   - JWT tokens with configurable expiration
   - HTTP-only cookies to prevent XSS attacks
   - Secure token validation

3. **Database Security**
   - GORM ORM prevents SQL injection
   - Parameterized queries
   - Soft deletes for data integrity

4. **Input Validation**
   - Email validation
   - Required field validation
   - Type-safe data binding

## Usage

### Creating an Account

1. Navigate to `http://localhost:8080`
2. Click "Register" or "Get Started"
3. Fill in your details (full name, email, password)
4. Submit the form

### Logging In

1. Go to the login page
2. Enter your email and password
3. Click "Login"

### Managing Financial Accounts

1. After logging in, you'll see your dashboard
2. Click "+ New Account" to create a financial account
3. Enter account name and select currency
4. View your accounts with balances
5. Delete accounts as needed

## Development

### Running in Development Mode

```bash
# Enable Gin debug mode
export GIN_MODE=debug
go run cmd/server/main.go
```

### Building for Production

```bash
# Build binary
go build -o finapp cmd/server/main.go

# Run production binary
./finapp
```

### Database Migrations

The application automatically runs migrations on startup. Models are defined in `internal/models/`.

## Testing

```bash
# Run all tests
go test ./...

# Run tests with coverage
go test -cover ./...
```

## License

This project is licensed under the MIT License.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## Security Considerations for Production

Before deploying to production:

1. ✅ Change `JWT_SECRET` to a strong, random value
2. ✅ Use environment variables for all sensitive data
3. ✅ Enable HTTPS/TLS
4. ✅ Set secure cookie flags in production
5. ✅ Configure CORS appropriately
6. ✅ Set up proper logging and monitoring
7. ✅ Implement rate limiting
8. ✅ Use connection pooling for database
9. ✅ Enable database SSL/TLS
10. ✅ Regular security audits and dependency updates

## Support

For issues and questions, please open an issue on GitHub.
