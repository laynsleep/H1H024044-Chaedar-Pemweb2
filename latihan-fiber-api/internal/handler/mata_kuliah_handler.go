package handler

import (
	"errors"
	"strconv"

	"github.com/gofiber/fiber/v3"
	"gorm.io/gorm"

	"latihan-fiber-api/internal/model"
	"latihan-fiber-api/internal/repository"
)

func DaftarMataKuliah(c fiber.Ctx) error {
	halaman, _ := strconv.Atoi(c.Query("halaman", "1"))
	perHalaman, _ := strconv.Atoi(c.Query("per_halaman", "10"))
	semester, _ := strconv.Atoi(c.Query("semester", "0"))

	if halaman < 1 {
		halaman = 1
	}

	if perHalaman < 1 || perHalaman > 100 {
		perHalaman = 10
	}

	filter := repository.FilterMataKuliah{
		Cari:       c.Query("cari", ""),
		Semester:   semester,
		Halaman:    halaman,
		PerHalaman: perHalaman,
	}

	daftar, total, err := repository.AmbilMataKuliah(filter)
	if err != nil {
		return fiber.NewError(fiber.StatusInternalServerError, "Gagal mengambil data")
	}

	totalHalaman := int(total) / perHalaman

	if int(total)%perHalaman != 0 {
		totalHalaman = totalHalaman + 1
	}

	return c.JSON(fiber.Map{
		"sukses": true,
		"data":   daftar,
		"meta": fiber.Map{
			"halaman":       halaman,
			"per_halaman":   perHalaman,
			"total":         total,
			"total_halaman": totalHalaman,
		},
	})
}

func DetailMataKuliah(c fiber.Ctx) error {
	id, err := strconv.Atoi(c.Params("id"))
	if err != nil {
		return fiber.NewError(fiber.StatusBadRequest, "ID harus berupa angka")
	}

	mataKuliah, err := repository.AmbilMataKuliahPerId(uint(id))
	if err != nil {
		if errors.Is(err, gorm.ErrRecordNotFound) {
			return fiber.NewError(fiber.StatusNotFound, "Mata kuliah tidak ditemukan")
		}

		return fiber.NewError(fiber.StatusInternalServerError, "Gagal mengambil data")
	}

	return c.JSON(fiber.Map{
		"sukses": true,
		"data":   mataKuliah,
	})
}

func BuatMataKuliah(c fiber.Ctx) error {
	var permintaan PermintaanMataKuliah

	err := c.Bind().Body(&permintaan)
	if err != nil {
		return fiber.NewError(fiber.StatusBadRequest, "Format body tidak valid")
	}

	mataKuliah := model.MataKuliah{
		ProgramStudiID: permintaan.ProgramStudiID,
		Kode:           permintaan.Kode,
		Nama:           permintaan.Nama,
		Sks:            permintaan.Sks,
		Semester:       permintaan.Semester,
	}

	err = repository.SimpanMataKuliah(&mataKuliah)
	if err != nil {
		return fiber.NewError(fiber.StatusUnprocessableEntity, "Gagal menyimpan data, periksa keunikan kode mata kuliah")
	}

	return c.Status(fiber.StatusCreated).JSON(fiber.Map{
		"sukses": true,
		"pesan":  "Data mata kuliah berhasil dibuat",
		"data":   mataKuliah,
	})
}

func PerbaruiMataKuliah(c fiber.Ctx) error {
	id, err := strconv.Atoi(c.Params("id"))
	if err != nil {
		return fiber.NewError(fiber.StatusBadRequest, "ID harus berupa angka")
	}

	mataKuliah, err := repository.AmbilMataKuliahPerId(uint(id))
	if err != nil {
		if errors.Is(err, gorm.ErrRecordNotFound) {
			return fiber.NewError(fiber.StatusNotFound, "Mata kuliah tidak ditemukan")
		}

		return fiber.NewError(fiber.StatusInternalServerError, "Gagal mengambil data")
	}

	var permintaan PermintaanMataKuliah

	err = c.Bind().Body(&permintaan)
	if err != nil {
		return fiber.NewError(fiber.StatusBadRequest, "Format body tidak valid")
	}

	if permintaan.ProgramStudiID > 0 {
		mataKuliah.ProgramStudiID = permintaan.ProgramStudiID
	}

	if permintaan.Kode != "" {
		mataKuliah.Kode = permintaan.Kode
	}

	if permintaan.Nama != "" {
		mataKuliah.Nama = permintaan.Nama
	}

	if permintaan.Sks > 0 {
		mataKuliah.Sks = permintaan.Sks
	}

	if permintaan.Semester > 0 {
		mataKuliah.Semester = permintaan.Semester
	}

	err = repository.PerbaruiMataKuliah(&mataKuliah)
	if err != nil {
		return fiber.NewError(fiber.StatusUnprocessableEntity, "Gagal memperbarui data")
	}

	return c.JSON(fiber.Map{
		"sukses": true,
		"pesan":  "Data mata kuliah berhasil diperbarui",
		"data":   mataKuliah,
	})
}

func HapusMataKuliah(c fiber.Ctx) error {
	id, err := strconv.Atoi(c.Params("id"))
	if err != nil {
		return fiber.NewError(fiber.StatusBadRequest, "ID harus berupa angka")
	}

	mataKuliah, err := repository.AmbilMataKuliahPerId(uint(id))
	if err != nil {
		if errors.Is(err, gorm.ErrRecordNotFound) {
			return fiber.NewError(fiber.StatusNotFound, "Mata kuliah tidak ditemukan")
		}

		return fiber.NewError(fiber.StatusInternalServerError, "Gagal mengambil data")
	}

	err = repository.HapusMataKuliah(&mataKuliah)
	if err != nil {
		return fiber.NewError(fiber.StatusInternalServerError, "Gagal menghapus data")
	}

	return c.JSON(fiber.Map{
		"sukses": true,
		"pesan":  "Data mata kuliah berhasil dihapus",
	})
}
