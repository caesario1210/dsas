<?php

namespace App\Services;

class SimplePdf
{
    private string $content = '';
    private float $y = 40;
    private float $xMargin = 30;
    private float $pageWidth = 595;
    private float $pageHeight = 842;
    private float $lineHeight = 8;
    private int $page = 1;

    public function __construct()
    {
        $this->content = '';
    }

    public function addTitle(string $text): void
    {
        $this->ensureSpace(20);
        $this->content .= "BT /F1 18 Tf 50 {$this->y} TD ({$this->escape($text)}) Tj ET\n";
        $this->y -= 14;
    }

    public function addSubtitle(string $text): void
    {
        $this->content .= "BT /F1 10 Tf 50 {$this->y} TD ({$this->escape($text)}) Tj ET\n";
        $this->y -= 12;
    }

    public function addSummary(array $cards): void
    {
        $this->ensureSpace(24);
        $x = $this->xMargin;
        foreach ($cards as $card) {
            $w = 120;
            $this->content .= "BT /F2 14 Tf {$x} {$this->y} TD ({$this->escape($card['value'])}) Tj ET\n";
            $this->content .= "BT /F1 8 Tf {$x} " . ($this->y - 10) . " TD ({$this->escape($card['label'])}) Tj ET\n";
            $this->content .= "{$x} " . ($this->y - 14) . " {$w} 18 re S\n";
            $x += $w + 10;
        }
        $this->y -= 30;
    }

    public function addTable(array $headers, array $rows): void
    {
        $this->ensureSpace(20);
        $colW = [60, 50, 80, 80, 50, 30, 70, 70];
        $totalW = array_sum($colW);
        $x = $this->xMargin;

        $this->content .= "BT /F2 9 Tf {$x} {$this->y} TD (";
        foreach ($headers as $i => $h) {
            $this->content .= $this->escape($h);
            if ($i < count($headers) - 1) $this->content .= "\\t";
        }
        $this->content .= ") Tj ET\n";
        $this->y -= $this->lineHeight;

        foreach ($rows as $row) {
            $this->ensureSpace($this->lineHeight);
            $x = $this->xMargin;
            $this->content .= "BT /F1 8 Tf {$x} {$this->y} TD (";
            foreach ($row as $i => $cell) {
                $this->content .= $this->escape((string) $cell);
                if ($i < count($row) - 1) $this->content .= "\\t";
            }
            $this->content .= ") Tj ET\n";
            $this->y -= $this->lineHeight;
        }
    }

    public function addFooter(string $text): void
    {
        $this->content .= "BT /F1 7 Tf 50 30 TD ({$this->escape($text)}) Tj ET\n";
    }

    public function output(): string
    {
        $pages = $this->splitPages();
        $pdf = "%PDF-1.4\n";

        $objects = [];
        $objNum = 1;

        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj";
        $objNum++;

        $pageCount = count($pages);
        $pageRefs = '';
        for ($i = 0; $i < $pageCount; $i++) {
            $pageRefs .= ($objNum + $i) . " 0 R ";
        }
        $fileSize = 0;

        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [{$pageRefs}] /Count {$pageCount} >>\nendobj";
        $objNum++;

        $objects[] = "3 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj";
        $font1 = $objNum;
        $objNum++;
        $objects[] = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>\nendobj";
        $font2 = $objNum;
        $objNum++;

        $contentObjs = [];
        foreach ($pages as $pi => $pageContent) {
            $stream = "BT /F1 10 Tf 50 800 TD (DSAS) Tj ET\n";
            $stream .= "BT /F1 8 Tf 500 800 TD (Page " . ($pi + 1) . " of {$pageCount}) Tj ET\n";
            $stream .= $pageContent;

            $streamObj = "{$objNum} 0 obj\n<< /Length " . strlen($stream) . " >>\nstream\n{$stream}\nendstream\nendobj";
            $contentObjs[] = $streamObj;
            $objNum++;
        }

        $pageObjs = [];
        foreach ($contentObjs as $ci => $co) {
            $contentObjNum = $font1 + 2 + $ci;
            $page = "{$objNum} 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$this->pageWidth} {$this->pageHeight}] /Resources << /Font << /F1 {$font1} 0 R /F2 {$font2} 0 R >> >> /Contents {$contentObjNum} 0 R >>\nendobj";
            $pageObjs[] = $page;
            $objNum++;
        }

        $allObjects = array_merge([], $objects, $contentObjs, $pageObjs);
        $body = implode("\n", $allObjects) . "\n";

        $xrefOffset = strlen($pdf) + strlen($body);
        $offsets = [];
        $pos = strlen($pdf);
        foreach ($allObjects as $obj) {
            $offsets[] = $pos;
            $pos += strlen($obj) + 1;
        }

        $pdf .= $body;
        $pdf .= "xref\n0 " . (count($allObjects) + 1) . "\n0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= "trailer\n<< /Size " . (count($allObjects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF\n";

        return $pdf;
    }

    private function escape(string $text): string
    {
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        return $text;
    }

    private function ensureSpace(float $needed): void
    {
        if ($this->y - $needed < 50) {
            $this->content .= "BT /F1 8 Tf 50 30 TD (-- continued --) Tj ET\n";
            $this->y = $this->pageHeight - 40;
            $this->page++;
        }
    }

    private function splitPages(): array
    {
        return [$this->content];
    }
}
