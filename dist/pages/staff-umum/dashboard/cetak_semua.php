<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'staff_umum') {
    header("Location: ../../index.php");
    exit;
}

// Include file koneksi ke database
include '../../koneksi.php';

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil semua file yang belum dicetak
$sql = "SELECT file_name FROM surat WHERE status_cetak = 0";
$result = $conn->query($sql);

if (!$result) {
    die("Query gagal: " . $conn->error);
}

if ($result->num_rows === 0) {
    die("Tidak ada dokumen yang belum dicetak.");
}

// Include PHPWord for document processing
require_once '../../../../vendor/autoload.php';
use PhpOffice\PhpWord\IOFactory;

$phpWord = new \PhpOffice\PhpWord\PhpWord();

// Loop through the results and process each file
while ($row = $result->fetch_assoc()) {
    $filePath = "../../../assets/uploads/surat_output/" . $row['file_name'];
    echo "Memproses file: " . $filePath . "<br>"; // Tambahkan log
    if (file_exists($filePath)) {
        try {
            // Load the document
            $source = IOFactory::load($filePath);
            // Copy content to the new document
            foreach ($source->getSections() as $sourceSection) {
                $section = $phpWord->addSection();
                foreach ($sourceSection->getElements() as $element) {
                    // Cek jenis elemen dan salin sesuai dengan jenisnya
                    if (method_exists($element, 'getText')) {
                        $text = $element->getText();
                        $section->addText($text, $element->getFontStyle(), $element->getParagraphStyle());
                    } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                        $textRun = $section->addTextRun();
                        foreach ($element->getElements() as $textElement) {
                            if (method_exists($textElement, 'getText')) {
                                $textRun->addText($textElement->getText(), $textElement->getFontStyle());
                            }
                        }
                    } elseif ($element instanceof \PhpOffice\PhpWord\Element\Image) {
                        // Menyalin gambar
                        $section->addImage($element->getSource(), $element->getStyle());
                    } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                        // Menyalin tabel
                        $newTable = $section->addTable($element->getStyle());
                        foreach ($element->getRows() as $row) {
                            $newRow = $newTable->addRow();
                            foreach ($row->getCells() as $cell) {
                                $newCell = $newRow->addCell($cell->getWidth(), $cell->getStyle());
                                foreach ($cell->getElements() as $cellElement) {
                                    if (method_exists($cellElement, 'getText')) {
                                        $newCell->addText($cellElement->getText(), $cellElement->getFontStyle(), $cellElement->getParagraphStyle());
                                    } elseif ($cellElement instanceof \PhpOffice\PhpWord\Element\Image) {
                                        // Menyalin gambar dalam tabel
                                        $newCell->addImage($cellElement->getSource(), $cellElement->getStyle());
                                    }
                                    // Tambahkan lebih banyak kondisi untuk elemen lain jika diperlukan
                                }
                            }
                        }
                    }
                    // Tambahkan lebih banyak kondisi untuk elemen lain jika diperlukan
                }
            }
        } catch (Exception $e) {
            echo "Gagal memproses file: " . $row['file_name'] . "<br>";
        }
    } else {
        echo "File tidak ditemukan: " . $row['file_name'] . "<br>";
    }
}

// Save the combined document
$combinedFileName = "../../../assets/uploads/surat_output/combined_" . time() . ".docx";
$phpWordWriter = IOFactory::createWriter($phpWord, 'Word2007');
$phpWordWriter->save($combinedFileName);

// Update the status of the documents
$updateSql = "UPDATE surat SET status_cetak = 1 WHERE status_cetak = 0";
$conn->query($updateSql);

// Download the combined file
header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
header("Content-Disposition: attachment; filename=\"" . basename($combinedFileName) . "\"");
readfile($combinedFileName);
exit;
?>
