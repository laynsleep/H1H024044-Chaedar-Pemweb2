package repository

import (
	"latihan-fiber-api/internal/config"
	"latihan-fiber-api/internal/model"
)

type ProgramStudiDenganJumlah struct {
	model.ProgramStudi
	JumlahMahasiswa int64 `json:"jumlah_mahasiswa"`
}

func AmbilProgramStudiDenganJumlah() ([]ProgramStudiDenganJumlah, error) {
	var daftar []ProgramStudiDenganJumlah

	err := config.DB.Table("program_studis").
		Select("program_studis.*, COUNT(mahasiswas.id) AS jumlah_mahasiswa").
		Joins("LEFT JOIN mahasiswas ON mahasiswas.program_studi_id = program_studis.id AND mahasiswas.deleted_at IS NULL").
		Group("program_studis.id").
		Order("program_studis.nama ASC").
		Scan(&daftar).Error

	return daftar, err
}
