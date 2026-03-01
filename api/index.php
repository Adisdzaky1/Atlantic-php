<?php
// Inisialisasi variabel pesan
$message = '';
$messageClass = '';

// Proses hanya jika ada POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_POST['ip'] ?? '';

    // Blokir jika IP = 0.0.0.0
    if ($ip === '0.0.0.0') {
        $message = 'IP 0.0.0.0 tidak diizinkan';
        $messageClass = 'text-red-400 bg-red-900/20 border-red-500/30';
    } else {
        // Ambil token dan Gist ID dari environment variable
        $githubToken = getenv('GITHUB_TOKEN');
        $gistId = getenv('GIST_ID');

        // Validasi environment variable
        if (!$githubToken || !$gistId) {
            $message = 'Konfigurasi server tidak lengkap (token atau Gist ID tidak ditemukan)';
            $messageClass = 'text-red-400 bg-red-900/20 border-red-500/30';
        } else {
            // Ambil konten Gist saat ini
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.github.com/gists/{$gistId}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: token ' . $githubToken,
                'User-Agent: PHP Script'
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                $message = 'Gagal mengambil data Gist (kode HTTP: ' . $httpCode . ')';
                $messageClass = 'text-red-400 bg-red-900/20 border-red-500/30';
            } else {
                $gistData = json_decode($response, true);
                // Nama file di Gist (sesuaikan jika berbeda)
                $filename = 'gistfile1.txt';
                $existingContent = $gistData['files'][$filename]['content'] ?? '[]';
                $existingArray = json_decode($existingContent, true);

                // Pastikan hasil decode adalah array
                if (!is_array($existingArray)) {
                    $existingArray = [];
                }

                // Tambahkan IP baru
                $newItem = ['Ip' => $ip, 'Akses' => true];
                $existingArray[] = $newItem;

                // Siapkan data untuk update
                $updateData = [
                    'description' => 'Updated with new IP and Akses status',
                    'files' => [
                        $filename => [
                            'content' => json_encode($existingArray, JSON_PRETTY_PRINT)
                        ]
                    ]
                ];

                // Kirim permintaan PATCH ke GitHub
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://api.github.com/gists/{$gistId}");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: token ' . $githubToken,
                    'User-Agent: PHP Script',
                    'Content-Type: application/json'
                ]);
                $updateResponse = curl_exec($ch);
                $updateHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($updateHttpCode === 200) {
                    $message = "IP {$ip} berhasil ditambahkan";
                    $messageClass = 'text-emerald-400 bg-emerald-900/20 border-emerald-500/30';
                } else {
                    $message = 'Gagal memperbarui Gist (kode HTTP: ' . $updateHttpCode . ')';
                    $messageClass = 'text-red-400 bg-red-900/20 border-red-500/30';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add IP - Mewah Edition</title>
    <!-- Google Fonts: Poppins untuk kesan modern dan elegan -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome 6 (ikon mewah) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at 10% 20%, rgba(21, 28, 48, 1) 0%, rgba(11, 15, 25, 1) 90%);
        }
        /* Efek glassmorphism */
        .glass-card {
            background: rgba(20, 30, 50, 0.6);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 1px 2px rgba(255, 255, 255, 0.05);
        }
        .input-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }
        .input-glass:focus {
            border-color: #fbbf24;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.2);
            background: rgba(255, 255, 255, 0.1);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(251, 191, 36, 0.3);
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(251, 191, 36, 0.4);
        }
        .btn-gradient:active {
            transform: translateY(0);
        }
        .message-animation {
            animation: slideDown 0.4s ease-out;
        }
        @keyframes slideDown {
            0% { opacity: 0; transform: translateY(-10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <!-- Background dekoratif (opsional) -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-purple-700/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-80 h-80 bg-blue-700/20 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md">
        <!-- Card utama dengan efek glass -->
        <div class="glass-card rounded-3xl p-8 text-white border border-white/10 shadow-2xl">
            <!-- Header dengan ikon -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-amber-400 to-orange-600 mb-4 shadow-lg shadow-amber-600/30">
                    <i class="fas fa-shield-alt text-4xl text-white drop-shadow-lg"></i>
                </div>
                <h1 class="text-3xl font-bold tracking-tight bg-gradient-to-r from-amber-300 to-orange-400 bg-clip-text text-transparent">
                    Secure Access
                </h1>
                <p class="text-gray-400 text-sm mt-1">Tambahkan IP address ke daftar akses</p>
            </div>

            <!-- Form -->
            <form method="POST" class="space-y-6">
                <div>
                    <label for="ip" class="block text-sm font-medium text-gray-300 mb-2">
                        <i class="fas fa-network-wired mr-2 text-amber-400"></i>IP Address
                    </label>
                    <div class="relative">
                        <input type="text" id="ip" name="ip" required
                               class="w-full px-5 py-4 input-glass rounded-2xl text-white placeholder-gray-500 focus:outline-none transition-all"
                               placeholder="Contoh: 192.168.1.1">
                        <i class="fas fa-globe absolute right-5 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                    </div>
                </div>

                <button type="submit" class="btn-gradient w-full py-4 rounded-2xl font-semibold text-gray-900 flex items-center justify-center gap-2 text-lg">
                    <i class="fas fa-plus-circle"></i> Tambahkan IP
                </button>
            </form>

            <!-- Pesan respons -->
            <?php if ($message): ?>
            <div class="mt-6 p-4 rounded-xl border <?= htmlspecialchars($messageClass) ?> message-animation flex items-start gap-3">
                <?php if (strpos($messageClass, 'text-emerald-400') !== false): ?>
                    <i class="fas fa-check-circle text-emerald-400 text-xl mt-0.5"></i>
                <?php else: ?>
                    <i class="fas fa-exclamation-triangle text-red-400 text-xl mt-0.5"></i>
                <?php endif; ?>
                <p class="text-sm flex-1"><?= htmlspecialchars($message) ?></p>
            </div>
            <?php endif; ?>

            <!-- Footer kecil -->
            <div class="mt-8 text-center text-xs text-gray-600">
                <i class="fas fa-lock mr-1"></i> Terenkripsi & aman
            </div>
        </div>
    </div>
</body>
</html>
