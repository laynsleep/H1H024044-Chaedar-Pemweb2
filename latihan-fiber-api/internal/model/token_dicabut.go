package model

import "time"

type TokenDicabut struct {
	ID              uint      `gorm:"primaryKey" json:"id"`
	HashToken       string    `gorm:"size:64;uniqueIndex;not null" json:"-"`
	PenggunaID      uint      `gorm:"index" json:"pengguna_id"`
	KadaluwarsaPada time.Time `gorm:"index" json:"kadaluwarsa_pada"`
	DibuatPada      time.Time `gorm:"autoCreateTime" json:"dibuat_pada"`
}
