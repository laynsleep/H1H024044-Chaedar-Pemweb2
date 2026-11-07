package repository

import (
	"latihan-fiber-api/internal/config"
	"latihan-fiber-api/internal/model"
)

type FilterMataKuliah struct {
	Cari       string
	Semester   int
	Halaman    int
	PerHalaman int
}

func AmbilMataKuliah(filter FilterMataKuliah) ([]model.MataKuliah, int64, error) {
	var daftar []model.MataKuliah
	var total int64

	kueri := config.DB.Model(&model.MataKuliah{})

	if filter.Cari != "" {
		pola := "%" + filter.Cari + "%"
		kueri = kueri.Where("nama LIKE ? OR kode LIKE ?", pola, pola)
	}

	if filter.Semester > 0 {
		kueri = kueri.Where("semester = ?", filter.Semester)
	}

	err := kueri.Count(&total).Error
	if err != nil {
		return nil, 0, err
	}

	lompat := (filter.Halaman - 1) * filter.PerHalaman

	err = kueri.Preload("ProgramStudi").
		Order("nama ASC").
		Limit(filter.PerHalaman).
		Offset(lompat).
		Find(&daftar).Error

	if err != nil {
		return nil, 0, err
	}

	return daftar, total, nil
}

func AmbilMataKuliahPerId(id uint) (model.MataKuliah, error) {
	var mataKuliah model.MataKuliah

	err := config.DB.Preload("ProgramStudi").First(&mataKuliah, id).Error

	return mataKuliah, err
}

func SimpanMataKuliah(mataKuliah *model.MataKuliah) error {
	return config.DB.Create(mataKuliah).Error
}

func PerbaruiMataKuliah(mataKuliah *model.MataKuliah) error {
	return config.DB.Save(mataKuliah).Error
}

func HapusMataKuliah(mataKuliah *model.MataKuliah) error {
	return config.DB.Delete(mataKuliah).Error
}
