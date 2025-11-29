package main

import (
	"log"
	"os"

	"github.com/VoLKoDaV13579/TestClaude/config"
	"github.com/VoLKoDaV13579/TestClaude/internal/database"
	"github.com/VoLKoDaV13579/TestClaude/internal/handlers"
	"github.com/VoLKoDaV13579/TestClaude/internal/middleware"
	"github.com/gin-gonic/gin"
)

func main() {
	// Load configuration
	cfg := config.Load()

	// Setup logger
	log.SetFlags(log.LstdFlags | log.Lshortfile)
	log.Println("Starting FinApp server...")

	// Connect to database
	if err := database.Connect(cfg); err != nil {
		log.Fatalf("Failed to connect to database: %v", err)
	}

	// Setup Gin
	if os.Getenv("GIN_MODE") == "" {
		gin.SetMode(gin.ReleaseMode)
	}

	router := gin.Default()

	// Load HTML templates
	router.LoadHTMLGlob("templates/*")
	router.Static("/static", "./static")

	// Initialize handlers
	homeHandler := handlers.NewHomeHandler()
	authHandler := handlers.NewAuthHandler(cfg)
	accountHandler := handlers.NewAccountHandler()

	// Public routes
	router.GET("/", homeHandler.ShowHome)
	router.GET("/register", authHandler.ShowRegisterPage)
	router.POST("/register", authHandler.Register)
	router.GET("/login", authHandler.ShowLoginPage)
	router.POST("/login", authHandler.Login)

	// Protected routes
	protected := router.Group("/")
	protected.Use(middleware.AuthMiddleware(cfg.JWTSecret))
	{
		protected.GET("/dashboard", accountHandler.ShowDashboard)
		protected.GET("/logout", authHandler.Logout)
	}

	// API routes
	api := router.Group("/api")
	api.Use(middleware.APIAuthMiddleware(cfg.JWTSecret))
	{
		api.GET("/accounts", accountHandler.GetAccounts)
		api.POST("/accounts", accountHandler.CreateAccount)
		api.DELETE("/accounts/:id", accountHandler.DeleteAccount)
	}

	// Start server
	addr := ":" + cfg.ServerPort
	log.Printf("Server starting on http://localhost%s", addr)
	if err := router.Run(addr); err != nil {
		log.Fatalf("Failed to start server: %v", err)
	}
}
