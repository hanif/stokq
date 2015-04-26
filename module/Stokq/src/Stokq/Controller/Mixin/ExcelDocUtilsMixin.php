<?php

namespace Stokq\Controller\Mixin;

/**
 * Class ExcelDocUtilsMixin
 * @package Stokq\Controller\Mixin
 */
trait ExcelDocUtilsMixin
{
    /**
     * @var string
     */
    private $excelDocsWorkingDir;

    /**
     * @return string
     */
    public function getExcelDocsWorkingDir()
    {
        return $this->excelDocsWorkingDir;
    }

    /**
     * @param string $excelDocsWorkingDir
     */
    public function setExcelDocsWorkingDir($excelDocsWorkingDir)
    {
        $this->excelDocsWorkingDir = $excelDocsWorkingDir;
    }

    /**
     * @param callable $callback
     * @return \PHPExcel
     */
    public function createExcelDoc(callable $callback)
    {
        $excel = new \PHPExcel();
        $callback($excel);
        return $excel;
    }

    /**
     * @param \PHPExcel $excel
     * @param $filename
     * @throws \PHPExcel_Reader_Exception
     */
    public function downloadExcelDoc(\PHPExcel $excel, $filename)
    {
        $tmpWriter = function(\PHPExcel_Writer_IWriter $writer) use($filename)
        {
            if (!$this->excelDocsWorkingDir || !is_dir($this->excelDocsWorkingDir) || !is_writable($this->excelDocsWorkingDir)) {
                throw new \RuntimeException('Excel docs working directory is not set or not writable.');
            }

            $tempFile = sprintf('%s/%s-%s-%s.xlsx', $this->excelDocsWorkingDir, $filename, uniqid(), time());
            $writer->save($tempFile);
            readfile($tempFile);
            @unlink($tempFile);
        };

        $writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header(sprintf('Content-Disposition: attachment; filename="%s.xlsx"', strip_tags($filename)));
        header('Cache-Control: max-age=0');
        $tmpWriter($writer);
        exit;
    }
}