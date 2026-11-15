package handler

import (
	"time"

	"latihan-fiber-api/internal/auth"
	"latihan-fiber-api/internal/config"
	"latihan-fiber-api/internal/model"
	"latihan-fiber-api/internal/repository"
	"latihan-fiber-api/internal/validasi"

	"github.com/gofiber/fiber/v3"
	"golang.org/x/crypto/bcrypt"
)

func Registrasi(c fiber.Ctx) error {
	var permintaan PermintaanRegistrasi

	err := c.Bind().Body(&permintaan)
	if err != nil {
		return fiber.NewError(fiber.StatusBadRequest, "Format body tidak valid")
	}

	galat := validasi.Periksa(permintaan)
	if galat != nil {
		return c.Status(fiber.StatusUnprocessableEntity).JSON(fiber.Map{
			"sukses": false,
			"pesan":  "Data yang dikirim tidak valid",
			"galat":  galat,
		})
	}

	hash, err := bcrypt.GenerateFromPassword([]byte(permintaan.KataSandi), bcrypt.DefaultCost)
	if err != nil {
		return fiber.NewError(fiber.StatusInternalServerError, "Gagal memproses kata sandi")
	}

	pengguna := model.Pengguna{
		Nama:      permintaan.Nama,
		Email:     permintaan.Email,
		KataSandi: string(hash),
		Peran:     "mahasiswa",
	}

	err = config.DB.Create(&pengguna).Error
	if err != nil {
		return fiber.NewError(fiber.StatusUnprocessableEntity, "Email sudah terdaftar")
	}

	return c.Status(fiber.StatusCreated).JSON(fiber.Map{
		"sukses": true,
		"pesan":  "Registrasi berhasil",
		"data":   pengguna,
	})
}

func Login(c fiber.Ctx) error {
	var permintaan PermintaanLogin

	err := c.Bind().Body(&permintaan)
	if err != nil {
		return fiber.NewError(fiber.StatusBadRequest, "Format body tidak valid")
	}

	galat := validasi.Periksa(permintaan)
	if galat != nil {
		return c.Status(fiber.StatusUnprocessableEntity).JSON(fiber.Map{
			"sukses": false,
			"pesan":  "Data yang dikirim tidak valid",
			"galat":  galat,
		})
	}

	var pengguna model.Pengguna
	err = config.DB.Where("email = ?", permintaan.Email).First(&pengguna).Error
	if err != nil {
		return fiber.NewError(fiber.StatusUnauthorized, "Email atau kata sandi tidak sesuai")
	}

	err = bcrypt.CompareHashAndPassword([]byte(pengguna.KataSandi), []byte(permintaan.KataSandi))
	if err != nil {
		return fiber.NewError(fiber.StatusUnauthorized, "Email atau kata sandi tidak sesuai")
	}

	konf := config.Muat()

	token, err := auth.TerbitkanToken(pengguna.ID, pengguna.Peran, konf.JwtSecret, konf.JwtTtlMenit)
	if err != nil {
		return fiber.NewError(fiber.StatusInternalServerError, "Gagal menerbitkan token")
	}

	return c.JSON(fiber.Map{
		"sukses": true,
		"pesan":  "Login berhasil",
		"data": fiber.Map{
			"pengguna": pengguna,
			"token":    token,
			"tipe":     "Bearer",
		},
	})
}

func Profil(c fiber.Ctx) error {
	idPengguna := c.Locals("id_pengguna")

	var pengguna model.Pengguna

	err := config.DB.First(&pengguna, idPengguna).Error
	if err != nil {
		return fiber.NewError(fiber.StatusNotFound, "Pengguna tidak ditemukan")
	}

	return c.JSON(fiber.Map{
		"sukses": true,
		"data":   pengguna,
	})
}

func Segarkan(c fiber.Ctx) error {
	idPengguna, cocok := c.Locals("id_pengguna").(uint)
	if !cocok {
		return fiber.NewError(fiber.StatusUnauthorized, "Identitas pengguna tidak ditemukan pada token")
	}

	peran, cocok := c.Locals("peran").(string)
	if !cocok {
		return fiber.NewError(fiber.StatusUnauthorized, "Peran pengguna tidak ditemukan pada token")
	}

	konf := config.Muat()

	token, err := auth.TerbitkanToken(idPengguna, peran, konf.JwtSecret, konf.JwtTtlMenit)
	if err != nil {
		return fiber.NewError(fiber.StatusInternalServerError, "Gagal menerbitkan token baru")
	}

	return c.JSON(fiber.Map{
		"sukses": true,
		"pesan":  "Token berhasil disegarkan",
		"data": fiber.Map{
			"token":                   token,
			"kadaluwarsa_dalam_menit": konf.JwtTtlMenit,
		},
	})
}

func Logout(c fiber.Ctx) error {
	tokenMentah, cocok := c.Locals("token_mentah").(string)
	if !cocok || tokenMentah == "" {
		return fiber.NewError(fiber.StatusUnauthorized, "Token tidak ditemukan pada permintaan")
	}

	idPengguna, _ := c.Locals("id_pengguna").(uint)

	kadaluwarsa := time.Now().Add(time.Duration(config.Muat().JwtTtlMenit) * time.Minute)

	if klaim, ok := c.Locals("klaim").(*auth.Klaim); ok && klaim.ExpiresAt != nil {
		kadaluwarsa = klaim.ExpiresAt.Time
	}

	err := repository.CabutToken(auth.HashToken(tokenMentah), idPengguna, kadaluwarsa)
	if err != nil {
		return fiber.NewError(fiber.StatusInternalServerError, "Gagal mencabut token")
	}

	_ = repository.BersihkanTokenDicabut()

	return c.JSON(fiber.Map{
		"sukses": true,
		"pesan":  "Logout berhasil, token telah dicabut",
	})
}
