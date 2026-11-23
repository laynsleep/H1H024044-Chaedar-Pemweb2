const penyediaAktif = (import.meta.env.VITE_API_PROVIDER || 'fiber')
    .trim()
    .toLowerCase()

const daftarPenyedia = {
    fiber: {
        id: 'fiber',
        nama: 'Fiber',
        baseURL: import.meta.env.VITE_API_FIBER,
        kunciHalaman: 'halaman',
        programStudiTersedia: true,
    },
    laravel: {
        id: 'laravel',
        nama: 'Laravel',
        baseURL: import.meta.env.VITE_API_LARAVEL,
        kunciHalaman: 'page',
        programStudiTersedia: false,
    },
}

const penyedia = daftarPenyedia[penyediaAktif]

if (!penyedia) {
    throw new Error(
        `Penyedia API "${penyediaAktif}" tidak dikenal. Gunakan "fiber" atau "laravel".`
    )
}

if (!penyedia.baseURL) {
    throw new Error(`Base URL untuk penyedia "${penyediaAktif}" belum diatur di .env`)
}

export const api = penyedia

export function parameterHalaman(halaman) {
    return { [api.kunciHalaman]: halaman }
}

export function bodyLogin(email, kataSandi) {
    if (api.id === 'laravel') {
        return { email, password: kataSandi }
    }

    return { email, kata_sandi: kataSandi }
}

export function normalisasiMeta(meta) {
    if (!meta) return null

    if (api.id === 'laravel') {
        return {
            halaman: meta.current_page,
            total_halaman: meta.last_page,
            per_halaman: meta.per_page,
            total: meta.total,
        }
    }

    return {
        halaman: meta.halaman,
        total_halaman: meta.total_halaman,
        per_halaman: meta.per_halaman,
        total: meta.total,
    }
}

export function normalisasiGalat(data) {
    if (!data) return { pesan: '', galat: [] }

    if (Array.isArray(data.galat)) {
        return {
            pesan: data.pesan || '',
            galat: data.galat.map((item) => ({
                field: item.field,
                pesan: item.pesan,
            })),
        }
    }

    if (data.errors && typeof data.errors === 'object') {
        const galat = Object.entries(data.errors).flatMap(([field, pesan]) =>
            (Array.isArray(pesan) ? pesan : [pesan]).map((isi) => ({
                field,
                pesan: isi,
            }))
        )

        return {
            pesan: data.message || data.pesan || 'Data yang dikirim tidak valid',
            galat,
        }
    }

    return {
        pesan: data.pesan || data.message || '',
        galat: [],
    }
}
