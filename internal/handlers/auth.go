package handlers

import (
	"net/http"

	"github.com/VoLKoDaV13579/TestClaude/config"
	"github.com/VoLKoDaV13579/TestClaude/internal/auth"
	"github.com/VoLKoDaV13579/TestClaude/internal/database"
	"github.com/VoLKoDaV13579/TestClaude/internal/models"
	"github.com/gin-gonic/gin"
	"gorm.io/gorm"
)

type AuthHandler struct {
	cfg *config.Config
}

func NewAuthHandler(cfg *config.Config) *AuthHandler {
	return &AuthHandler{cfg: cfg}
}

type RegisterRequest struct {
	Email    string `json:"email" binding:"required,email"`
	Password string `json:"password" binding:"required,min=8"`
	FullName string `json:"full_name" binding:"required"`
}

type LoginRequest struct {
	Email    string `json:"email" binding:"required,email"`
	Password string `json:"password" binding:"required"`
}

// ShowRegisterPage displays the registration page
func (h *AuthHandler) ShowRegisterPage(c *gin.Context) {
	c.HTML(http.StatusOK, "register.html", gin.H{
		"title": "Register",
	})
}

// ShowLoginPage displays the login page
func (h *AuthHandler) ShowLoginPage(c *gin.Context) {
	c.HTML(http.StatusOK, "login.html", gin.H{
		"title": "Login",
	})
}

// Register handles user registration
func (h *AuthHandler) Register(c *gin.Context) {
	var req RegisterRequest
	if err := c.ShouldBind(&req); err != nil {
		c.HTML(http.StatusBadRequest, "register.html", gin.H{
			"title": "Register",
			"error": "Invalid input. Please check your data.",
		})
		return
	}

	// Check if user already exists
	var existingUser models.User
	if err := database.DB.Where("email = ?", req.Email).First(&existingUser).Error; err == nil {
		c.HTML(http.StatusConflict, "register.html", gin.H{
			"title": "Register",
			"error": "User with this email already exists",
		})
		return
	}

	// Create new user
	user := models.User{
		Email:    req.Email,
		FullName: req.FullName,
	}

	if err := user.HashPassword(req.Password); err != nil {
		c.HTML(http.StatusInternalServerError, "register.html", gin.H{
			"title": "Register",
			"error": "Failed to process registration",
		})
		return
	}

	if err := database.DB.Create(&user).Error; err != nil {
		c.HTML(http.StatusInternalServerError, "register.html", gin.H{
			"title": "Register",
			"error": "Failed to create user",
		})
		return
	}

	c.Redirect(http.StatusFound, "/login?registered=true")
}

// Login handles user authentication
func (h *AuthHandler) Login(c *gin.Context) {
	var req LoginRequest
	if err := c.ShouldBind(&req); err != nil {
		c.HTML(http.StatusBadRequest, "login.html", gin.H{
			"title": "Login",
			"error": "Invalid input",
		})
		return
	}

	// Find user by email
	var user models.User
	if err := database.DB.Where("email = ?", req.Email).First(&user).Error; err != nil {
		if err == gorm.ErrRecordNotFound {
			c.HTML(http.StatusUnauthorized, "login.html", gin.H{
				"title": "Login",
				"error": "Invalid email or password",
			})
			return
		}
		c.HTML(http.StatusInternalServerError, "login.html", gin.H{
			"title": "Login",
			"error": "Server error",
		})
		return
	}

	// Check password
	if err := user.CheckPassword(req.Password); err != nil {
		c.HTML(http.StatusUnauthorized, "login.html", gin.H{
			"title": "Login",
			"error": "Invalid email or password",
		})
		return
	}

	// Generate JWT token
	token, err := auth.GenerateToken(user.ID, user.Email, h.cfg.JWTSecret, h.cfg.SessionTimeout)
	if err != nil {
		c.HTML(http.StatusInternalServerError, "login.html", gin.H{
			"title": "Login",
			"error": "Failed to generate token",
		})
		return
	}

	// Set cookie
	c.SetCookie("token", token, int(h.cfg.SessionTimeout.Seconds()), "/", "", false, true)
	c.Redirect(http.StatusFound, "/dashboard")
}

// Logout handles user logout
func (h *AuthHandler) Logout(c *gin.Context) {
	c.SetCookie("token", "", -1, "/", "", false, true)
	c.Redirect(http.StatusFound, "/")
}
