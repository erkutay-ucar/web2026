<?php
// Değişkenleri başlangıçta tanımla
$ad_soyad = "";
$ders_adi = "";
$vize = "";
$final = "";
$sonuc = null; // Sonuç bilgilerini tutacak dizi

// Form gönderildi mi kontrol et
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Formdan gelen verileri al
    $ad_soyad = trim($_POST["ad_soyad"] ?? "");
    $ders_adi = trim($_POST["ders_adi"] ?? "");
    $vize = trim($_POST["vize"] ?? "");
    $final = trim($_POST["final"] ?? "");

    // Hata mesajlarını topla
    $hatalar = [];

    // Boş alan kontrolü
    if ($ad_soyad == "") {
        $hatalar[] = "Ad Soyad alanı boş bırakılamaz.";
    }
    if ($ders_adi == "") {
        $hatalar[] = "Ders Adı alanı boş bırakılamaz.";
    }
    if ($vize === "") {
        $hatalar[] = "Vize Notu alanı boş bırakılamaz.";
    }
    if ($final === "") {
        $hatalar[] = "Final Notu alanı boş bırakılamaz.";
    }

    // Sayısal kontrol (boş değilse kontrol et)
    if ($vize !== "" && !is_numeric($vize)) {
        $hatalar[] = "Vize Notu sayısal bir değer olmalıdır.";
    }
    if ($final !== "" && !is_numeric($final)) {
        $hatalar[] = "Final Notu sayısal bir değer olmalıdır.";
    }

    // 0-100 arası kontrol (sayısal ise kontrol et)
    if ($vize !== "" && is_numeric($vize)) {
        $vize = floatval($vize);
        if ($vize < 0 || $vize > 100) {
            $hatalar[] = "Vize Notu 0 ile 100 arasında olmalıdır.";
        }
    }
    if ($final !== "" && is_numeric($final)) {
        $final = floatval($final);
        if ($final < 0 || $final > 100) {
            $hatalar[] = "Final Notu 0 ile 100 arasında olmalıdır.";
        }
    }

    // Hata yoksa hesaplama yap
    if (empty($hatalar)) {

        // Ortalama hesapla: Vize %40 + Final %60
        $ortalama = ($vize * 0.40) + ($final * 0.60);
        $ortalama = round($ortalama, 2);

        // Harf notu ve katsayı belirle
        if ($ortalama >= 95) {
            $harf = "A1";
            $katsayi = 4.00;
        } elseif ($ortalama >= 90) {
            $harf = "A2";
            $katsayi = 3.75;
        } elseif ($ortalama >= 85) {
            $harf = "A3";
            $katsayi = 3.50;
        } elseif ($ortalama >= 80) {
            $harf = "B1";
            $katsayi = 3.25;
        } elseif ($ortalama >= 75) {
            $harf = "B2";
            $katsayi = 3.00;
        } elseif ($ortalama >= 70) {
            $harf = "B3";
            $katsayi = 2.75;
        } elseif ($ortalama >= 65) {
            $harf = "C1";
            $katsayi = 2.50;
        } elseif ($ortalama >= 60) {
            $harf = "C2";
            $katsayi = 2.25;
        } elseif ($ortalama >= 55) {
            $harf = "C3";
            $katsayi = 2.00;
        } elseif ($ortalama >= 50) {
            $harf = "D1";
            $katsayi = 1.75;
        } else {
            $harf = "F1";
            $katsayi = 0;
        }

        // Geçti / Kaldı durumu
        if ($ortalama >= 50) {
            $durum = "Geçti";
        } else {
            $durum = "Kaldı";
        }

        // Sonuçları diziye kaydet
        $sonuc = [
            "ad_soyad" => $ad_soyad,
            "ders_adi" => $ders_adi,
            "vize" => $vize,
            "final" => $final,
            "ortalama" => $ortalama,
            "harf" => $harf,
            "katsayi" => $katsayi,
            "durum" => $durum
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Not Hesaplama Sistemi</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Sayfa arka planı beyaz */
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        /* Card için küçük gölge */
        .card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Sonuç tablosu için küçük stil */
        .sonuc-card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <!-- Başlık -->
                <h2 class="text-center mb-4">Öğrenci Not Hesaplama</h2>

                <!-- Form Kartı -->
                <div class="card p-4 mb-4">
                    <form id="notForm" method="POST" action="">

                        <!-- Ad Soyad -->
                        <div class="mb-3">
                            <label for="ad_soyad" class="form-label">Ad Soyad</label>
                            <input type="text" class="form-control" id="ad_soyad" name="ad_soyad"
                                value="<?php echo htmlspecialchars($ad_soyad); ?>" placeholder="Öğrenci adı soyadı">
                        </div>

                        <!-- Ders Adı -->
                        <div class="mb-3">
                            <label for="ders_adi" class="form-label">Ders Adı</label>
                            <input type="text" class="form-control" id="ders_adi" name="ders_adi"
                                value="<?php echo htmlspecialchars($ders_adi); ?>" placeholder="Ders adını girin">
                        </div>

                        <!-- Vize Notu -->
                        <div class="mb-3">
                            <label for="vize" class="form-label">Vize Notu</label>
                            <input type="number" class="form-control" id="vize" name="vize"
                                value="<?php echo htmlspecialchars($vize); ?>" placeholder="0 - 100 arası" min="0"
                                max="100">
                        </div>

                        <!-- Final Notu -->
                        <div class="mb-3">
                            <label for="final" class="form-label">Final Notu</label>
                            <input type="number" class="form-control" id="final" name="final"
                                value="<?php echo htmlspecialchars($final); ?>" placeholder="0 - 100 arası" min="0"
                                max="100">
                        </div>

                        <!-- Hesapla Butonu -->
                        <button type="submit" class="btn btn-primary w-100">Hesapla</button>
                    </form>
                </div>

                <?php if ($sonuc != null): ?>
                    <!-- Sonuç Kartı -->
                    <div class="card sonuc-card p-4">
                        <h5 class="text-center mb-3">Sonuç</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Öğrenci</th>
                                <td><?php echo htmlspecialchars($sonuc["ad_soyad"]); ?></td>
                            </tr>
                            <tr>
                                <th>Ders</th>
                                <td><?php echo htmlspecialchars($sonuc["ders_adi"]); ?></td>
                            </tr>
                            <tr>
                                <th>Vize Notu</th>
                                <td><?php echo $sonuc["vize"]; ?></td>
                            </tr>
                            <tr>
                                <th>Final Notu</th>
                                <td><?php echo $sonuc["final"]; ?></td>
                            </tr>
                            <tr>
                                <th>Ortalama</th>
                                <td><strong><?php echo $sonuc["ortalama"]; ?></strong></td>
                            </tr>
                            <tr>
                                <th>Harf Notu</th>
                                <td><strong><?php echo $sonuc["harf"]; ?></strong></td>
                            </tr>
                            <tr>
                                <th>Katsayı</th>
                                <td><?php echo number_format($sonuc["katsayi"], 2); ?></td>
                            </tr>
                            <tr>
                                <th>Durum</th>
                                <td>
                                    <?php if ($sonuc["durum"] == "Geçti"): ?>
                                        <span class="badge bg-success">Dersi Geçti</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Dertsen Kaldı</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Bootstrap CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sayfa yüklenince otomatik  Sweetaler2 mesajlarını gösterir
        document.addEventListener("DOMContentLoaded", function () {

            <?php if (!empty($hatalar)): ?>
                // Hata mesajlarını swettalert2 kullanarak göster
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    html: '<?php echo implode("<br>", $hatalar); ?>',
                    confirmButtonText: 'Tamam',
                    confirmButtonColor: '#dc3545'
                });
            <?php endif; ?>

            <?php if ($sonuc != null): ?>
                // Geçtiyse sweetalert2 kullanarak geçme mesajını göster
                Swal.fire({
                    icon: 'success',
                    title: 'Hesaplama Başarılı!',
                    html: '<strong><?php echo htmlspecialchars($sonuc["ad_soyad"]); ?></strong> adlı öğrencinin ' +
                        '<strong><?php echo htmlspecialchars($sonuc["ders_adi"]); ?></strong> dersi ortalaması: ' +
                        '<strong><?php echo $sonuc["ortalama"]; ?></strong><br>' +
                        'Harf Notu: <strong><?php echo $sonuc["harf"]; ?></strong> | ' +
                        'Durum: <strong><?php echo $sonuc["durum"]; ?></strong>',
                    confirmButtonText: 'Tamam',
                    confirmButtonColor: '#198754'
                });
            <?php endif; ?>

        });
    </script>

</body>

</html>