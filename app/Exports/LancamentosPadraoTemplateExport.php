<?php

namespace App\Exports;

use App\Models\LancamentoPadrao;
use App\Models\Contabilide\ChartOfAccount;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class LancamentosPadraoTemplateExport
{
    protected $companyId;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    public function generate()
    {
        $spreadsheet = new Spreadsheet();
        
        // Remove a sheet padrão
        $spreadsheet->removeSheetByIndex(0);
        
        // Cria a aba de Lançamentos
        $lancamentosSheet = $spreadsheet->createSheet();
        $lancamentosSheet->setTitle('Lançamentos');
        $this->createLancamentosSheet($lancamentosSheet);
        
        // Cria a aba de Dados das Contas
        $contasSheet = $spreadsheet->createSheet();
        $contasSheet->setTitle('Dados_Contas');
        $this->createContasSheet($contasSheet);
        
        // Define a primeira aba como ativa
        $spreadsheet->setActiveSheetIndex(0);
        
        // Oculta a aba Dados_Contas
        $contasSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);
        
        return $spreadsheet;
    }

    protected function createLancamentosSheet($sheet)
    {
        // Headers
        $sheet->setCellValue('A1', 'ID (Não Alterar)');
        $sheet->setCellValue('B1', 'Descrição');
        $sheet->setCellValue('C1', 'Tipo');
        $sheet->setCellValue('D1', 'Conta Débito (Selecionar)');
        $sheet->setCellValue('E1', 'Conta Crédito (Selecionar)');
        
        // Estiliza o header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ]
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        
        // Busca os lançamentos
        $lancamentos = LancamentoPadrao::where('company_id', $this->companyId)->get();
        
        // Dados
        $row = 2;
        foreach ($lancamentos as $lancamento) {
            $sheet->setCellValue('A' . $row, $lancamento->id);
            $sheet->setCellValue('B' . $row, $lancamento->description);
            $sheet->setCellValue('C' . $row, $lancamento->type);
            
            // Formata conta de débito
            if ($lancamento->conta_debito_id) {
                $contaDebito = ChartOfAccount::find($lancamento->conta_debito_id);
                if ($contaDebito) {
                    $sheet->setCellValue('D' . $row, $contaDebito->code . ' - ' . $contaDebito->name);
                }
            }
            
            // Formata conta de crédito
            if ($lancamento->conta_credito_id) {
                $contaCredito = ChartOfAccount::find($lancamento->conta_credito_id);
                if ($contaCredito) {
                    $sheet->setCellValue('E' . $row, $contaCredito->code . ' - ' . $contaCredito->name);
                }
            }
            
            $row++;
        }
        
        // Se não houver dados, pelo menos cria uma linha vazia
        if ($row == 2) {
            $row = 3;
        }
        
        // Conta quantas contas existem para o range
        $totalContas = ChartOfAccount::where('company_id', $this->companyId)->count();
        $rangeContas = $totalContas > 0 ? $totalContas : 1;
        
        // Aplica validação de dados (dropdown) nas colunas D e E
        $validation = $sheet->getCell('D2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Erro de Entrada');
        $validation->setError('Selecione um valor da lista.');
        $validation->setFormula1('Dados_Contas!$A$1:$A$' . $rangeContas);
        
        // Aplica para todas as linhas
        $lastRow = $row - 1;
        for ($i = 2; $i <= $lastRow; $i++) {
            $sheet->getCell('D' . $i)->setDataValidation(clone $validation);
            $sheet->getCell('E' . $i)->setDataValidation(clone $validation);
        }
        
        // Auto-size nas colunas
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setWidth(40);
        $sheet->getColumnDimension('E')->setWidth(40);
    }

    protected function createContasSheet($sheet)
    {
        // Busca todas as contas
        $contas = ChartOfAccount::where('company_id', $this->companyId)
            ->orderBy('code')
            ->get();
        
        // Dados
        $row = 1;
        foreach ($contas as $conta) {
            $sheet->setCellValue('A' . $row, $conta->code . ' - ' . $conta->name);
            $row++;
        }
        
        // Se não houver contas, pelo menos cria uma célula vazia
        if ($row == 1) {
            $sheet->setCellValue('A1', '');
        }
    }
}

