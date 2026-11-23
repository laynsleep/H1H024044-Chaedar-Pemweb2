import axios from 'axios'
import { api, normalisasiGalat, normalisasiMeta } from './konfigurasiApi'

const klien = axios.create({
    baseURL: api.baseURL,
    headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
    },
    timeout: 10000,
})

klien.interceptors.request.use((konfigurasi) => {
    const token = sessionStorage.getItem('token')
    if (token) {
        konfigurasi.headers.Authorization = `Bearer ${token}`
    }
    return konfigurasi
})

klien.interceptors.response.use(
    (respons) => {
        if (respons.data && respons.data.meta) {
            respons.data.meta = normalisasiMeta(respons.data.meta)
        }

        return respons
    },
    (galat) => {
        if (galat.response && galat.response.data) {
            galat.response.data = {
                ...galat.response.data,
                ...normalisasiGalat(galat.response.data),
            }
        }

        if (galat.response && galat.response.status === 401) {
            sessionStorage.removeItem('token')
            window.location.href = '/login'
        }

        return Promise.reject(galat)
    }
)

export default klien