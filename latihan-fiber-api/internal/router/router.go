package router

import (
	"latihan-fiber-api/internal/handler"
	"latihan-fiber-api/internal/middleware"

	"github.com/gofiber/fiber/v3"
)

func Daftarkan(app *fiber.App) {
	api := app.Group("/api")

	api.Get("/status", func(c fiber.Ctx) error {
		return c.JSON(fiber.Map{
			"sukses": true,
			"pesan":  "API Fiber aktif",
		})
	})

	api.Post("/auth/registrasi", handler.Registrasi)
	api.Post("/auth/login", handler.Login)

	terlindungi := api.Group("", middleware.Autentikasi())

	terlindungi.Get("/auth/profil", handler.Profil)

	mahasiswa := terlindungi.Group("/mahasiswa")

	mahasiswa.Get("/", handler.DaftarMahasiswa)
	mahasiswa.Post("/", middleware.HanyaPeran("admin", "dosen"), handler.BuatMahasiswa)
	mahasiswa.Get("/:id", handler.DetailMahasiswa)
	mahasiswa.Put("/:id", middleware.HanyaPeran("admin", "dosen"), handler.PerbaruiMahasiswa)
	mahasiswa.Delete("/:id", middleware.HanyaPeran("admin"), handler.HapusMahasiswa)
	mahasiswa.Post("/:id/pulihkan", middleware.HanyaPeran("admin"), handler.PulihkanMahasiswa)
	mahasiswa.Get("/nim/:nim", handler.CariMahasiswa)

	mataKuliah := terlindungi.Group("/matakuliah")

	mataKuliah.Get("/", handler.DaftarMataKuliah)
	mataKuliah.Post("/", middleware.HanyaPeran("admin", "dosen"), handler.BuatMataKuliah)
	mataKuliah.Get("/:id", handler.DetailMataKuliah)
	mataKuliah.Put("/:id", middleware.HanyaPeran("admin", "dosen"), handler.PerbaruiMataKuliah)
	mataKuliah.Delete("/:id", middleware.HanyaPeran("admin"), handler.HapusMataKuliah)

	api.Get("/program-studi", handler.DaftarProgramStudi)
}
