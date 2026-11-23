import { BrowserRouter, Route, Routes, Navigate } from "react-router-dom";
import Login from "./halaman/Login";
import DaftarMahasiswa from "./halaman/DaftarMahasiswa";
import RuteTerlindungi from "./komponen/RuteRelindungi";

export default function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Navigate to="/mahasiswa" replace />} />
        <Route path="/login" element={<Login />} />
        <Route
          path="/mahasiswa"
          element={
            <RuteTerlindungi>
              <DaftarMahasiswa />
            </RuteTerlindungi>
          }
        />
      </Routes>
    </BrowserRouter>
  )
}