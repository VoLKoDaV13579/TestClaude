package middleware

import (
	"net/http"
	"strings"

	"github.com/VoLKoDaV13579/TestClaude/internal/auth"
	"github.com/gin-gonic/gin"
)

// AuthMiddleware validates JWT tokens from cookies or Authorization header
func AuthMiddleware(jwtSecret []byte) gin.HandlerFunc {
	return func(c *gin.Context) {
		var tokenString string

		// Try to get token from cookie first
		cookie, err := c.Cookie("token")
		if err == nil {
			tokenString = cookie
		} else {
			// Try Authorization header
			authHeader := c.GetHeader("Authorization")
			if authHeader != "" {
				parts := strings.Split(authHeader, " ")
				if len(parts) == 2 && parts[0] == "Bearer" {
					tokenString = parts[1]
				}
			}
		}

		if tokenString == "" {
			c.Redirect(http.StatusFound, "/login")
			c.Abort()
			return
		}

		claims, err := auth.ValidateToken(tokenString, jwtSecret)
		if err != nil {
			c.SetCookie("token", "", -1, "/", "", false, true)
			c.Redirect(http.StatusFound, "/login")
			c.Abort()
			return
		}

		// Set user info in context
		c.Set("user_id", claims.UserID)
		c.Set("user_email", claims.Email)

		c.Next()
	}
}

// APIAuthMiddleware validates JWT tokens for API endpoints
func APIAuthMiddleware(jwtSecret []byte) gin.HandlerFunc {
	return func(c *gin.Context) {
		var tokenString string

		// Try to get token from cookie first
		cookie, err := c.Cookie("token")
		if err == nil {
			tokenString = cookie
		} else {
			// Try Authorization header
			authHeader := c.GetHeader("Authorization")
			if authHeader != "" {
				parts := strings.Split(authHeader, " ")
				if len(parts) == 2 && parts[0] == "Bearer" {
					tokenString = parts[1]
				}
			}
		}

		if tokenString == "" {
			c.JSON(http.StatusUnauthorized, gin.H{"error": "unauthorized"})
			c.Abort()
			return
		}

		claims, err := auth.ValidateToken(tokenString, jwtSecret)
		if err != nil {
			c.JSON(http.StatusUnauthorized, gin.H{"error": "invalid or expired token"})
			c.Abort()
			return
		}

		// Set user info in context
		c.Set("user_id", claims.UserID)
		c.Set("user_email", claims.Email)

		c.Next()
	}
}
