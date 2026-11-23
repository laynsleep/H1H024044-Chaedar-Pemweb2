import klien from "./klien";
import { api } from "./konfigurasiApi";

export async function ambilDaftarProgramStudi() {
    if (api.programStudiTersedia === false) {
        return { data: [] }
    }

    const respons = await klien.get('/program-studi')

    return respons.data
}
