package middleware

import (
	"os"

	"github.com/gofiber/fiber/v3"
)

func KunciApi() fiber.Handler {
	return func(c fiber.Ctx) error {
		kunciKlien := c.Get("X-Api-Key")
		kunciRahasia := os.Getenv("API_KEY")
	
		if kunciKlien == "" || kunciKlien != kunciRahasia {
			return fiber.NewError(fiber.StatusUnauthorized, "Tidak memiliki akses")
		}
	
		return c.Next()
	}
}