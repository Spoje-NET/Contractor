<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

// require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Contractor/MiniMustache.php';
require_once __DIR__ . '/../src/Contractor/DocxTemplate.php';

use AbraFlexi\Contractor\DocxTemplate;

function log_out($msg) {
    file_put_contents(__DIR__ . '/verify_result.txt', $msg . "\n", FILE_APPEND);
}

// Clear log
file_put_contents(__DIR__ . '/verify_result.txt', "STARTING TYPE CHECK\n");

if (!class_exists('ZipArchive')) {
    log_out("Error: ZipArchive class not found");
    exit(1);
}

// Mock data
$data = [
    'contract' => [
        'kod' => 'TEST-001',
        'firma' => [
            'nazev' => 'Test Company',
        ]
    ]
];

// Create a dummy DOCX
$tempDocx = sys_get_temp_dir() . '/test_template.docx';
$zip = new ZipArchive();
if ($zip->open($tempDocx, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    // Note: The XML string below is one line to avoid issues, simplified
    $zip->addFromString('word/document.xml', '<?xml version="1.0"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body><w:p><w:r><w:t>Code: {{contract.kod}}</w:t></w:r></w:p><w:p><w:r><w:t>Split: {{</w:t></w:r><w:proofErr w:type="gram" /><w:r><w:t>contract.firma.nazev}}</w:t></w:r></w:p></w:body></w:document>');
    $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"></Types>');
    $zip->close();
}

try {
    log_out("Processing $tempDocx...");
    $template = new DocxTemplate($tempDocx);
    $template->render($data);
    
    $outputDocx = sys_get_temp_dir() . '/test_output.docx';
    $template->save($outputDocx);
    
    log_out("Saved to $outputDocx");
    
    $zipCheck = new ZipArchive();
    if ($zipCheck->open($outputDocx) === true) {
        $xml = $zipCheck->getFromName('word/document.xml');
        // log_out("Rendered XML: " . substr($xml, 0, 200)); 
        
        if (strpos($xml, 'Code: TEST-001') !== false && strpos($xml, 'Split: Test Company') !== false) {
            log_out("TEST PASSED");
        } else {
            log_out("TEST FAILED");
            log_out("XML content: " . $xml);
        }
    } else {
         log_out("FAILED TO OPEN OUTPUT DOCX");
    }

} catch (Exception $e) {
    log_out("Error: " . $e->getMessage());
}
