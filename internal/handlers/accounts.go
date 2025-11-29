package handlers

import (
	"net/http"

	"github.com/VoLKoDaV13579/TestClaude/internal/database"
	"github.com/VoLKoDaV13579/TestClaude/internal/models"
	"github.com/gin-gonic/gin"
)

type AccountHandler struct{}

func NewAccountHandler() *AccountHandler {
	return &AccountHandler{}
}

type CreateAccountRequest struct {
	Name     string `json:"name" binding:"required"`
	Currency string `json:"currency" binding:"required"`
}

// ShowDashboard displays the user dashboard with accounts
func (h *AccountHandler) ShowDashboard(c *gin.Context) {
	userID, exists := c.Get("user_id")
	if !exists {
		c.Redirect(http.StatusFound, "/login")
		return
	}

	var user models.User
	if err := database.DB.Preload("Accounts").First(&user, userID).Error; err != nil {
		c.HTML(http.StatusInternalServerError, "error.html", gin.H{
			"title": "Error",
			"error": "Failed to load user data",
		})
		return
	}

	c.HTML(http.StatusOK, "dashboard.html", gin.H{
		"title":    "Dashboard",
		"user":     user,
		"accounts": user.Accounts,
	})
}

// CreateAccount creates a new account for the user
func (h *AccountHandler) CreateAccount(c *gin.Context) {
	userID, exists := c.Get("user_id")
	if !exists {
		c.JSON(http.StatusUnauthorized, gin.H{"error": "unauthorized"})
		return
	}

	var req CreateAccountRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "invalid input"})
		return
	}

	account := models.Account{
		UserID:   userID.(uint),
		Name:     req.Name,
		Balance:  0,
		Currency: req.Currency,
	}

	if err := database.DB.Create(&account).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "failed to create account"})
		return
	}

	c.JSON(http.StatusCreated, account)
}

// GetAccounts returns all accounts for the user
func (h *AccountHandler) GetAccounts(c *gin.Context) {
	userID, exists := c.Get("user_id")
	if !exists {
		c.JSON(http.StatusUnauthorized, gin.H{"error": "unauthorized"})
		return
	}

	var accounts []models.Account
	if err := database.DB.Where("user_id = ?", userID).Find(&accounts).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "failed to fetch accounts"})
		return
	}

	c.JSON(http.StatusOK, accounts)
}

// DeleteAccount deletes an account
func (h *AccountHandler) DeleteAccount(c *gin.Context) {
	userID, exists := c.Get("user_id")
	if !exists {
		c.JSON(http.StatusUnauthorized, gin.H{"error": "unauthorized"})
		return
	}

	accountID := c.Param("id")

	// Verify account belongs to user
	var account models.Account
	if err := database.DB.Where("id = ? AND user_id = ?", accountID, userID).First(&account).Error; err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "account not found"})
		return
	}

	if err := database.DB.Delete(&account).Error; err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "failed to delete account"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "account deleted successfully"})
}
