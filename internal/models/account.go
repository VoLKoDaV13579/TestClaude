package models

import (
	"time"

	"gorm.io/gorm"
)

type Account struct {
	ID        uint           `gorm:"primarykey" json:"id"`
	CreatedAt time.Time      `json:"created_at"`
	UpdatedAt time.Time      `json:"updated_at"`
	DeletedAt gorm.DeletedAt `gorm:"index" json:"-"`
	UserID    uint           `gorm:"not null;index" json:"user_id"`
	Name      string         `gorm:"not null" json:"name"`
	Balance   float64        `gorm:"not null;default:0" json:"balance"`
	Currency  string         `gorm:"not null;default:'USD'" json:"currency"`
}
