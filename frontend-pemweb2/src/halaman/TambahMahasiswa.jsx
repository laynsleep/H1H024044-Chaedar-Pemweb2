import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { ambilDaftarProgramStudi } from '../api/layananProgramStudi'
import { simpanMahasiswa } from '../api/layananMahasiswa'
import { api } from '../api/konfigurasiApi'

const bentukAwal = {
    program_studi_id: '',
    nim: '',
    nama: '',
    email: '',
    angkatan: String(new Date().getFullYear()),
    ipk: '',
    aktif: true,
}

const padananField = {
    ProgramStudiID: 'program_studi_id',
    Nim: 'nim',
    Nama: 'nama',
    Email: 'email',
    Angkatan: 'angkatan',
    Ipk: 'ipk',
    Aktif: 'aktif',
}

function petakanNamaField(field) {
    if (!field) return ''
    if (padananField[field]) return padananField[field]

    return field.replace(/([a-z0-9])([A-Z])/g, '$1_$2').toLowerCase()
}

export default function TambahMahasiswa() {
    const [bentuk, setBentuk] = useState(bentukAwal)
    const [daftarProgramStudi, setDaftarProgramStudi] = useState([])
    const [sedangMemuat, setSedangMemuat] = useState(true)
    const [sedangMenyimpan, setSedangMenyimpan] = useState(false)
    const [galatField, setGalatField] = useState({})
    const [daftarGalat, setDaftarGalat] = useState([])
    const [galatUmum, setGalatUmum] = useState('')

    const navigasi = useNavigate()

    useEffect(() => {
        let dibatalkan = false

        async function muatProgramStudi() {
            try {
                const hasil = await ambilDaftarProgramStudi()
                if (dibatalkan === false) {
                    setDaftarProgramStudi(hasil.data || [])
                }
            } catch {
                if (dibatalkan === false) {
                    setGalatUmum('Gagal memuat daftar program studi')
                }
            } finally {
                if (dibatalkan === false) {
                    setSedangMemuat(false)
                }
            }
        }

        muatProgramStudi()

        return () => {
            dibatalkan = true
        }
    }, [])

    function perbarui(nama, nilai) {
        setBentuk((sebelumnya) => ({ ...sebelumnya, [nama]: nilai }))
        setGalatField({})
        setDaftarGalat([])
        setGalatUmum('')
    }

    function tampilkanGalat(nama) {
        const pesan = galatField[nama]
        if (!pesan) return null

        return (
            <small style={{ color: 'crimson', display: 'block', marginTop: 4 }}>
                {pesan}
            </small>
        )
    }

    async function tanganiKirim(peristiwa) {
        peristiwa.preventDefault()

        setGalatField({})
        setDaftarGalat([])
        setGalatUmum('')
        setSedangMenyimpan(true)

        const data = {
            program_studi_id: Number(bentuk.program_studi_id),
            nim: bentuk.nim.trim(),
            nama: bentuk.nama.trim(),
            email: bentuk.email.trim(),
            angkatan: Number(bentuk.angkatan),
            ipk: bentuk.ipk === '' ? 0 : Number(bentuk.ipk),
            aktif: bentuk.aktif,
        }

        try {
            const hasil = await simpanMahasiswa(data)

            navigasi('/mahasiswa', {
                state: {
                    pesanSukses: hasil.pesan || 'Data mahasiswa berhasil ditambahkan',
                },
            })
        } catch (galat) {
            const respons = galat.response ? galat.response.data : null

            if (respons && Array.isArray(respons.galat)) {
                const petakan = {}

                respons.galat.forEach((item) => {
                    const kunci = petakanNamaField(item.field)
                    if (kunci && item.pesan && !petakan[kunci]) {
                        petakan[kunci] = item.pesan
                    }
                })

                setGalatField(petakan)
                setDaftarGalat(respons.galat)
            }

            if (respons && respons.pesan) {
                setGalatUmum(respons.pesan)
            } else if (galat.response) {
                setGalatUmum('Permintaan gagal, periksa kembali data yang dikirim')
            } else {
                setGalatUmum('Tidak dapat menghubungi server')
            }
        } finally {
            setSedangMenyimpan(false)
        }
    }

    const gayaInput = { width: '100%', padding: 8, boxSizing: 'border-box' }

    return (
        <div style={{ maxWidth: 640, margin: '40px auto', textAlign: 'left' }}>
            <h1>Tambah Mahasiswa</h1>

            <button type="button" onClick={() => navigasi('/mahasiswa')}>
                Kembali ke daftar
            </button>

            {galatUmum && (
                <div
                    role="alert"
                    style={{
                        marginTop: 16,
                        padding: 12,
                        border: '1px solid crimson',
                        borderRadius: 4,
                        color: 'crimson',
                    }}
                >
                    {galatUmum}
                </div>
            )}

            {daftarGalat.length > 0 && (
                <div
                    role="alert"
                    style={{
                        marginTop: galatUmum ? 8 : 16,
                        padding: 12,
                        border: '1px solid crimson',
                        borderRadius: 4,
                        color: 'crimson',
                    }}
                >
                    <strong>Periksa kembali data berikut:</strong>
                    <ul style={{ marginBottom: 0 }}>
                        {daftarGalat.map((item, indeks) => (
                            <li key={`${item.field}-${indeks}`}>
                                {item.field}: {item.pesan}
                            </li>
                        ))}
                    </ul>
                </div>
            )}

            <form onSubmit={tanganiKirim} style={{ marginTop: 16 }}>
                <div style={{ marginBottom: 12 }}>
                    <label htmlFor="program_studi_id">Program Studi</label>

                    {api.programStudiTersedia ? (
                        <select
                            id="program_studi_id"
                            style={gayaInput}
                            value={bentuk.program_studi_id}
                            onChange={(peristiwa) =>
                                perbarui('program_studi_id', peristiwa.target.value)
                            }
                            aria-invalid={Boolean(galatField.program_studi_id)}
                        >
                            <option value="">Pilih program studi</option>
                            {daftarProgramStudi.map((programStudi) => (
                                <option key={programStudi.id} value={programStudi.id}>
                                    {programStudi.kode} - {programStudi.nama}
                                </option>
                            ))}
                        </select>
                    ) : (
                        <input
                            id="program_studi_id"
                            type="number"
                            style={gayaInput}
                            value={bentuk.program_studi_id}
                            onChange={(peristiwa) =>
                                perbarui('program_studi_id', peristiwa.target.value)
                            }
                            aria-invalid={Boolean(galatField.program_studi_id)}
                        />
                    )}

                    {tampilkanGalat('program_studi_id')}
                    {api.programStudiTersedia && sedangMemuat && (
                        <small>Memuat program studi…</small>
                    )}
                    {api.programStudiTersedia === false && (
                        <small>
                            Backend {api.nama} tidak menyediakan endpoint program studi,
                            masukkan ID manual.
                        </small>
                    )}
                </div>

                <div style={{ marginBottom: 12 }}>
                    <label htmlFor="nim">NIM</label>
                    <input
                        id="nim"
                        style={gayaInput}
                        value={bentuk.nim}
                        onChange={(peristiwa) => perbarui('nim', peristiwa.target.value)}
                        aria-invalid={Boolean(galatField.nim)}
                    />
                    {tampilkanGalat('nim')}
                </div>

                <div style={{ marginBottom: 12 }}>
                    <label htmlFor="nama">Nama</label>
                    <input
                        id="nama"
                        style={gayaInput}
                        value={bentuk.nama}
                        onChange={(peristiwa) => perbarui('nama', peristiwa.target.value)}
                        aria-invalid={Boolean(galatField.nama)}
                    />
                    {tampilkanGalat('nama')}
                </div>

                <div style={{ marginBottom: 12 }}>
                    <label htmlFor="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        style={gayaInput}
                        value={bentuk.email}
                        onChange={(peristiwa) => perbarui('email', peristiwa.target.value)}
                        aria-invalid={Boolean(galatField.email)}
                    />
                    {tampilkanGalat('email')}
                </div>

                <div style={{ marginBottom: 12 }}>
                    <label htmlFor="angkatan">Angkatan</label>
                    <input
                        id="angkatan"
                        type="number"
                        style={gayaInput}
                        value={bentuk.angkatan}
                        onChange={(peristiwa) => perbarui('angkatan', peristiwa.target.value)}
                        aria-invalid={Boolean(galatField.angkatan)}
                    />
                    {tampilkanGalat('angkatan')}
                </div>

                <div style={{ marginBottom: 12 }}>
                    <label htmlFor="ipk">IPK</label>
                    <input
                        id="ipk"
                        type="number"
                        step="0.01"
                        min="0"
                        max="4"
                        style={gayaInput}
                        value={bentuk.ipk}
                        onChange={(peristiwa) => perbarui('ipk', peristiwa.target.value)}
                        aria-invalid={Boolean(galatField.ipk)}
                    />
                    {tampilkanGalat('ipk')}
                </div>

                <div style={{ marginBottom: 16 }}>
                    <label htmlFor="aktif">
                        <input
                            id="aktif"
                            type="checkbox"
                            checked={bentuk.aktif}
                            onChange={(peristiwa) =>
                                perbarui('aktif', peristiwa.target.checked)
                            }
                        />
                        {' '}Aktif
                    </label>
                    {tampilkanGalat('aktif')}
                </div>

                <button type="submit" disabled={sedangMenyimpan}>
                    {sedangMenyimpan ? 'Menyimpan…' : 'Simpan Mahasiswa'}
                </button>
            </form>
        </div>
    )
}
