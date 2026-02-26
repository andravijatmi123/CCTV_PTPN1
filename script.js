/**
 * Fungsi untuk memperbarui jam digital secara real-time
 */
function updateClock() {
    const now = new Date();
    
    // Mengambil jam, menit, dan detik dengan format 2 digit
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    
    // Menggabungkan menjadi format jam digital
    const timeString = hours + '.' + minutes + '.' + seconds + ' WIB';
    
    // Mencari elemen dengan ID 'clock'
    const clockElement = document.getElementById('clock');
    
    // Jika elemen ditemukan di halaman tersebut, perbarui teksnya
    if (clockElement) {
        clockElement.textContent = timeString;
    }
}

// Menjalankan fungsi setiap 1000 milidetik (1 detik)
setInterval(updateClock, 1000);

// Panggil fungsi segera saat halaman dimuat pertama kali
document.addEventListener('DOMContentLoaded', updateClock);