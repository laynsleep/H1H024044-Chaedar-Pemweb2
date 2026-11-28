package handler_test

import (
	"latihan-fiber-api/internal/handler"
	"net/http/httptest"
	"testing"

	"github.com/gofiber/fiber/v3"
)

func TestStatusEndpoint(t *testing.T) {
	app := fiber.New()

	app.Get("/api/status", func(c fiber.Ctx) error {
		return c.JSON(fiber.Map{"sukses": true})
	})

	permintaan := httptest.NewRequest("GET", "/api/status", nil)

	respons, err := app.Test(permintaan)
	if err != nil {
		t.Fatal(err)
	}

	if respons.StatusCode != 200 {
		t.Errorf("status yang diharapkan 200, diperoleh %d",
			respons.StatusCode)
	}
}

func TestDetailMahasiswaIdBukanAngka(t *testing.T) {
	app := fiber.New()

	app.Get("/api/mahasiswa/:id", handler.DetailMahasiswa)

	permintaan := httptest.NewRequest("GET", "/api/mahasiswa/abc", nil)

	respons, err := app.Test(permintaan)
	if err != nil {
		t.Fatal(err)
	}

	if respons.StatusCode != 400 {
		t.Errorf("status yang diharapkan 400, diperoleh %d",
			respons.StatusCode)
	}
}
