package repository

import (
	"time"

	"gorm.io/gorm/clause"

	"latihan-fiber-api/internal/config"
	"latihan-fiber-api/internal/model"
)

func CabutToken(hashToken string, penggunaID uint, kadaluwarsaPada time.Time) error {
	token := model.TokenDicabut{
		HashToken:       hashToken,
		PenggunaID:      penggunaID,
		KadaluwarsaPada: kadaluwarsaPada,
	}

	return config.DB.
		Clauses(clause.OnConflict{DoNothing: true}).
		Create(&token).Error
}

func TokenSudahDicabut(hashToken string) (bool, error) {
	var jumlah int64

	err := config.DB.
		Model(&model.TokenDicabut{}).
		Where("hash_token = ? AND kedaluwarsa_pada > ?", hashToken, time.Now()).
		Count(&jumlah).Error

	return jumlah > 0, err
}

func BersihkanTokenDicabut() error {
	return config.DB.
		Where("kedaluwarsa_pada <= ?", time.Now()).
		Delete(&model.TokenDicabut{}).Error
}
