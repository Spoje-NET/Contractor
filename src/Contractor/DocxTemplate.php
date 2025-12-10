<?php

declare(strict_types=1);

/**
 * This file is part of the AbraflexiContractor package
 *
 * https://github.com/VitexSoftware/Spoje-contractor
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AbraFlexi\Contractor;

use ZipArchive;

/**
 * Handle DOCX templates rendering
 */
class DocxTemplate
{
    private string $tempDir;
    private string $xmlContent;
    private string $zipFile;

    public function __construct(string $filePath)
    {
        $this->zipFile = $filePath;
        $this->tempDir = sys_get_temp_dir() . '/docx_' . uniqid();
        
        if (!mkdir($this->tempDir) && !is_dir($this->tempDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->tempDir));
        }

        $this->extract();
    }

    private function extract(): void
    {
        $zip = new ZipArchive();
        if ($zip->open($this->zipFile) === true) {
            $zip->extractTo($this->tempDir);
            $zip->close();
            
            $documentXmlPath = $this->tempDir . '/word/document.xml';
            if (file_exists($documentXmlPath)) {
                $this->xmlContent = file_get_contents($documentXmlPath);
            } else {
                 throw new \Exception("Invalid DOCX: word/document.xml not found");
            }
        } else {
            throw new \Exception("Could not open DOCX file");
        }
    }

    /**
     * Cleanups and fixes XML content to be mustache friendly
     * 
     * @param string $xml
     * 
     * @return string
     */
    public function mergeRuns(string $xml): string
    {
        // Remove Grammar and Spell checks
        $xml = preg_replace('/<w:proofErr\s+w:type="[a-zA-Z]+"\s*\/>/', '', $xml);
        
        // Remove rsid tags which are used for revision tracking
        $xml = preg_replace('/rsid[A-Z]+="[0-9A-F]+"/i', '', $xml);

        // Very basic cleaner that joins {{...}} if split. 
        // WARNING: This is experimental regex.
        $xml = preg_replace_callback(
            '/((?:{[^{}]*)|(?:{{[^{}]*))((?:<\/w:t>.*?<w:t>)+)([^}]*}})/s', 
            function($m) {
                return $m[1] . $m[3];
            }, 
            $xml
        );
        
        return $xml; 
    }

    public function render(array $data): void
    {
        $xml = $this->mergeRuns($this->xmlContent);
        $m = new MiniMustache();
        $this->xmlContent = $m->render($xml, $data);
    }

    public function save(string $outputPath): void
    {
        if (file_put_contents($this->tempDir . '/word/document.xml', $this->xmlContent) === false) {
             throw new \Exception("Failed to write document.xml");
        }

        $zip = new ZipArchive();
        if ($zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            // Create recursive directory iterator
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->tempDir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($this->tempDir) + 1);

                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        } else {
             throw new \Exception("Could not create output ZIP file");
        }
        
        $this->removeDir($this->tempDir);
    }
    
    private function removeDir(string $dir): void
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object) && !is_link($dir . "/" . $object)) {
                        $this->removeDir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }
}
