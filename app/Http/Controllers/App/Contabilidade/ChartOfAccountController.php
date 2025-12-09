<?php

namespace App\Http\Controllers\App\Contabilidade;

use App\Http\Controllers\Controller;
use App\Models\Contabilide\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChartOfAccountController extends Controller
{
    /**
     * Exibe a lista de contas do plano de contas.
     */
    public function index()
    {
        // Busca todas as contas da empresa ativa para a listagem e para o dropdown do modal.
        $contas = ChartOfAccount::forActiveCompany()->orderBy('code')->get();

        return view('app.contabilidade.index', compact('contas'));
    }

    /**
     * Salva uma nova conta contábil no banco de dados.
     */
    public function store(Request $request)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Nenhuma empresa selecionada.'], 403);
            }
            flash()->error('Nenhuma empresa selecionada.');
            return redirect()->back();
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|in:ativo,passivo,patrimonio_liquido,receita,despesa',
            'parent_id' => 'nullable|integer|exists:chart_of_accounts,id',
            'allows_posting' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();
        $validatedData['company_id'] = $activeCompanyId;
        
        // Converte allows_posting para boolean
        $validatedData['allows_posting'] = (bool) $validatedData['allows_posting'];

        // --- INÍCIO DA ADIÇÃO DO TRY-CATCH ---
        try {
            $conta = ChartOfAccount::create($validatedData);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Conta contábil criada com sucesso!',
                    'conta' => $conta
                ], 201);
            }

            return redirect()->back()->with('success', 'Conta contábil criada com sucesso!');
        } catch (\Exception $e) {
            // Registra o erro detalhado no arquivo de log para o desenvolvedor
            Log::error('Erro ao criar conta contábil: ' . $e->getMessage());

            // Retorna uma resposta de erro amigável para o usuário
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Ocorreu um erro inesperado ao salvar a conta.'], 500);
            }

            return redirect()->back()->with('error', 'Ocorreu um erro inesperado ao salvar a conta. Por favor, tente novamente.');
        }
        // --- FIM DA ADIÇÃO DO TRY-CATCH ---
    }

    /**
     * Exibe o formulário para editar uma conta.
     */
    public function edit($id)
    {
        $conta = ChartOfAccount::forActiveCompany()->findOrFail($id);
        
        // Sempre retorna JSON, pois a edição é feita via modal
        return response()->json([
            'success' => true,
            'conta' => $conta
        ]);
    }

    /**
     * Atualiza uma conta contábil existente.
     */
    public function update(Request $request, $id)
    {
        $conta = ChartOfAccount::forActiveCompany()->findOrFail($id);

        $validatedData = $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|in:ativo,passivo,patrimonio_liquido,receita,despesa',
            'parent_id' => 'nullable|integer|exists:chart_of_accounts,id',
            'allows_posting' => 'required|boolean',
        ]);
        
        // Converte allows_posting para boolean
        $validatedData['allows_posting'] = (bool) $validatedData['allows_posting'];

        $conta->update($validatedData);

        // Retorna JSON para requisições AJAX
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Conta contábil atualizada com sucesso!',
                'conta' => $conta
            ]);
        }

        return redirect()->route('contabilidade.plano-contas.index')->with('success', 'Conta contábil atualizada com sucesso!');
    }

    /**
     * Remove uma conta contábil.
     */
    public function destroy($id)
    {
        $conta = ChartOfAccount::forActiveCompany()->findOrFail($id);

        // Lógica para impedir a exclusão se a conta tiver filhas (opcional, mas recomendado)
        if ($conta->children()->exists()) {
            return redirect()->back()->with('error', 'Não é possível excluir uma conta que possui sub-contas.');
        }

        $conta->delete();

        return redirect()->route('plano-contas.index')->with('success', 'Conta contábil excluída com sucesso!');
    }

    /**
     * Importa plano de contas de um arquivo CSV ou Excel
     */
    public function import(Request $request)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Nenhuma empresa selecionada.'], 403);
            }
            return redirect()->back()->with('error', 'Nenhuma empresa selecionada.');
        }

        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:10240', // Máximo 10MB
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            
            Log::info('CSV Import - Iniciando importação', [
                'arquivo' => $file->getClientOriginalName(),
                'extensao' => $extension,
                'company_id' => $activeCompanyId
            ]);
            
            $data = [];
            
            if ($extension === 'csv') {
                $data = $this->parseCsv($file);
            } else {
                $data = $this->parseExcel($file);
            }

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'O arquivo está vazio ou não pôde ser processado.'
                ], 400);
            }

            $imported = 0;
            $updated = 0;
            $errors = [];
            $accountsMap = []; // Mapa para relacionar códigos com IDs

            foreach ($data as $index => $row) {
                try {
                    // Suporta múltiplos formatos de colunas
                    $code = trim(
                        $row['codigo'] ?? 
                        $row['Código'] ?? 
                        $row['cdclassinterna'] ?? 
                        $row['cdclassexterna'] ?? 
                        ''
                    );
                    
                    $tipoConta = strtoupper(trim(
                        $row['tipo'] ?? 
                        $row['Tipo'] ?? 
                        $row['tpconta'] ?? 
                        ''
                    ));
                    
                    $name = trim(
                        $row['descricao'] ?? 
                        $row['Descrição'] ?? 
                        $row['nmconta'] ?? 
                        $row['nome'] ?? 
                        ''
                    );

                    if (empty($code) || empty($name)) {
                        $errors[] = "Linha " . ($index + 2) . ": Código ou Descrição vazios";
                        continue;
                    }

                    // Determina o tipo da conta baseado no código
                    $accountType = $this->determineAccountType($code);
                    
                    // Determina o parent_id baseado na hierarquia do código
                    $parentId = $this->findParentId($code, $accountsMap, $activeCompanyId);

                    // Verifica se a conta já existe
                    $existingAccount = ChartOfAccount::where('company_id', $activeCompanyId)
                        ->where('code', $code)
                        ->first();

                    if ($existingAccount) {
                        // Atualiza a conta existente
                        $existingAccount->update([
                            'name' => $name,
                            'type' => $accountType,
                            'parent_id' => $parentId,
                        ]);
                        $accountsMap[$code] = $existingAccount->id;
                        $updated++;
                    } else {
                        // Cria nova conta
                        $account = ChartOfAccount::create([
                            'company_id' => $activeCompanyId,
                            'code' => $code,
                            'name' => $name,
                            'type' => $accountType,
                            'parent_id' => $parentId,
                        ]);
                        $accountsMap[$code] = $account->id;
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Linha " . ($index + 2) . ": " . $e->getMessage();
                    Log::error('Erro ao importar linha do plano de contas', [
                        'linha' => $index + 2,
                        'dados' => $row,
                        'erro' => $e->getMessage()
                    ]);
                }
            }

            $message = "Importação concluída! {$imported} conta(s) importada(s)";
            if ($updated > 0) {
                $message .= ", {$updated} conta(s) atualizada(s)";
            }
            $message .= ".";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " erro(s) encontrado(s).";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'updated' => $updated,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao importar plano de contas', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar arquivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exporta plano de contas em diferentes formatos
     */
    public function export(Request $request)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return response()->json(['success' => false, 'message' => 'Nenhuma empresa selecionada.'], 403);
        }

        $request->validate([
            'format' => 'required|in:excel,csv,pdf',
            'account_type' => 'required|string'
        ]);

        try {
            $format = $request->input('format');
            $accountType = $request->input('account_type');

            // Busca as contas
            $query = ChartOfAccount::where('company_id', $activeCompanyId)
                ->orderBy('code');

            if ($accountType !== 'all') {
                $query->where('type', $accountType);
            }

            $accounts = $query->get();

            if ($accounts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma conta encontrada para exportar.'
                ], 400);
            }

            // Gera o arquivo baseado no formato
            switch ($format) {
                case 'excel':
                    return $this->exportToExcel($accounts);
                case 'csv':
                    return $this->exportToCsv($accounts);
                case 'pdf':
                    return $this->exportToPdf($accounts);
                default:
                    return response()->json(['success' => false, 'message' => 'Formato inválido.'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao exportar plano de contas', [
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao exportar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exporta para Excel
     */
    private function exportToExcel($accounts)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'Código');
        $sheet->setCellValue('B1', 'Nome da Conta');
        $sheet->setCellValue('C1', 'Tipo');
        $sheet->setCellValue('D1', 'Conta Pai');

        // Estiliza o header
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0']
            ]
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // Dados
        $row = 2;
        foreach ($accounts as $account) {
            $sheet->setCellValue('A' . $row, $account->code);
            $sheet->setCellValue('B' . $row, $account->name);
            $sheet->setCellValue('C' . $row, $this->getTypeLabel($account->type));
            $sheet->setCellValue('D' . $row, $account->parent ? $account->parent->name : '');
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Gera o arquivo
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'plano_de_contas_' . date('Y-m-d_His') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Exporta para CSV
     */
    private function exportToCsv($accounts)
    {
        $filename = 'plano_de_contas_' . date('Y-m-d_His') . '.csv';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $handle = fopen($tempFile, 'w');

        // BOM para UTF-8
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        // Headers
        fputcsv($handle, ['Código', 'Nome da Conta', 'Tipo', 'Conta Pai']);

        // Dados
        foreach ($accounts as $account) {
            fputcsv($handle, [
                $account->code,
                $account->name,
                $this->getTypeLabel($account->type),
                $account->parent ? $account->parent->name : ''
            ]);
        }

        fclose($handle);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Exporta para PDF
     */
    private function exportToPdf($accounts)
    {
        // Usa TCPDF ou similar se disponível, caso contrário retorna erro
        if (!class_exists('TCPDF')) {
            return response()->json([
                'success' => false,
                'message' => 'Exportação para PDF não disponível. Use Excel ou CSV.'
            ], 400);
        }

        // Implementação básica com TCPDF seria aqui
        // Por enquanto, retorna mensagem de não implementado
        return response()->json([
            'success' => false,
            'message' => 'Exportação para PDF em desenvolvimento. Use Excel ou CSV.'
        ], 501);
    }

    /**
     * Retorna o label do tipo de conta
     */
    private function getTypeLabel($type)
    {
        $labels = [
            'ativo' => 'Ativo',
            'passivo' => 'Passivo',
            'patrimonio_liquido' => 'Patrimônio Líquido',
            'receita' => 'Receita',
            'despesa' => 'Despesa'
        ];

        return $labels[$type] ?? $type;
    }

    /**
     * Parse CSV file
     */
    private function parseCsv($file)
    {
        $data = [];
        $filePath = $file->getRealPath();
        
        // Log: Informações do arquivo
        Log::info('CSV Import - Arquivo recebido', [
            'nome' => $file->getClientOriginalName(),
            'tamanho' => $file->getSize(),
            'mime' => $file->getMimeType(),
            'path' => $filePath
        ]);
        
        // Detecta o encoding do arquivo
        $content = file_get_contents($filePath);
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        Log::info('CSV Import - Encoding detectado', ['encoding' => $encoding]);
        
        $handle = fopen($filePath, 'r');
        
        // Tenta detectar o delimitador
        $firstLine = fgets($handle);
        rewind($handle);
        $commaCount = substr_count($firstLine, ',');
        $semicolonCount = substr_count($firstLine, ';');
        $delimiter = $semicolonCount > $commaCount ? ';' : ',';
        
        Log::info('CSV Import - Delimitador detectado', [
            'delimiter' => $delimiter,
            'primeira_linha' => $firstLine
        ]);
        
        // Lê o cabeçalho
        $headers = fgetcsv($handle, 0, $delimiter);
        if (!$headers) {
            Log::error('CSV Import - Cabeçalho vazio ou inválido');
            return [];
        }
        
        Log::info('CSV Import - Headers originais', ['headers' => $headers]);

        // Normaliza os headers (remove acentos e converte para minúsculas)
        $headers = array_map(function($header) {
            $normalized = mb_strtolower(trim($header));
            // Remove acentos
            $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $normalized);
            return $normalized;
        }, $headers);
        
        Log::info('CSV Import - Headers normalizados', ['headers' => $headers]);

        $lineNumber = 1; // Já lemos o header
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $lineNumber++;
            
            if (count($row) !== count($headers)) {
                Log::warning('CSV Import - Linha com número incorreto de colunas', [
                    'linha' => $lineNumber,
                    'esperado' => count($headers),
                    'recebido' => count($row),
                    'dados' => $row
                ]);
                continue;
            }
            
            $rowData = array_combine($headers, $row);
            $data[] = $rowData;
            
            // Log apenas das primeiras 3 linhas para não poluir
            if ($lineNumber <= 4) {
                Log::info('CSV Import - Linha processada', [
                    'linha' => $lineNumber,
                    'dados' => $rowData
                ]);
            }
        }

        fclose($handle);
        
        Log::info('CSV Import - Parsing concluído', [
            'total_linhas' => count($data)
        ]);
        
        return $data;
    }

    /**
     * Parse Excel file
     */
    private function parseExcel($file)
    {
        // Se não tiver a biblioteca PhpSpreadsheet, tenta usar CSV
        if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            throw new \Exception('Biblioteca PhpSpreadsheet não instalada. Use arquivos CSV.');
        }

        Log::info('Excel Import - Carregando arquivo Excel', [
            'nome' => $file->getClientOriginalName(),
            'tamanho' => $file->getSize()
        ]);

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $data = [];

        // Lê o cabeçalho
        $headers = [];
        $headerRow = $worksheet->getRowIterator(1, 1)->current();
        foreach ($headerRow->getCellIterator() as $cell) {
            $headers[] = $cell->getValue();
        }
        
        Log::info('Excel Import - Headers originais', ['headers' => $headers]);
        
        // Normaliza headers
        $headers = array_map(function($header) {
            $normalized = mb_strtolower(trim($header));
            // Remove acentos
            $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $normalized);
            return $normalized;
        }, $headers);
        
        Log::info('Excel Import - Headers normalizados', ['headers' => $headers]);

        // Lê as linhas de dados
        $lineNumber = 1;
        foreach ($worksheet->getRowIterator(2) as $row) {
            $lineNumber++;
            $rowData = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            
            $colIndex = 0;
            foreach ($cellIterator as $cell) {
                if ($colIndex >= count($headers)) {
                    break;
                }
                $rowData[$headers[$colIndex]] = $cell->getValue();
                $colIndex++;
            }
            
            if (!empty(array_filter($rowData))) {
                $data[] = $rowData;
                
                // Log apenas das primeiras 3 linhas
                if ($lineNumber <= 4) {
                    Log::info('Excel Import - Linha processada', [
                        'linha' => $lineNumber,
                        'dados' => $rowData
                    ]);
                }
            }
        }

        Log::info('Excel Import - Parsing concluído', [
            'total_linhas' => count($data)
        ]);

        return $data;
    }

    /**
     * Determina o tipo da conta baseado no código
     */
    private function determineAccountType($code)
    {
        // Remove espaços e pega o primeiro número
        $code = trim($code);
        
        // Se o código começa com 2.03, é patrimônio líquido
        if (strpos($code, '2.03') === 0 || strpos($code, '2,03') === 0) {
            return 'patrimonio_liquido';
        }

        $firstDigit = (int) substr($code, 0, 1);

        switch ($firstDigit) {
            case 1:
                return 'ativo';
            case 2:
                return 'passivo';
            case 3:
                return 'receita';
            case 4:
                return 'despesa';
            default:
                return 'ativo'; // Padrão
        }
    }

    /**
     * Encontra o ID do pai baseado na hierarquia do código
     */
    private function findParentId($code, &$accountsMap, $companyId)
    {
        // Remove espaços e normaliza
        $code = trim($code);
        
        // Se o código não tem ponto ou vírgula, não tem pai
        if (strpos($code, '.') === false && strpos($code, ',') === false) {
            return null;
        }

        // Tenta com ponto primeiro
        $separator = strpos($code, '.') !== false ? '.' : ',';
        $parts = explode($separator, $code);
        
        // Remove a última parte para obter o código do pai
        array_pop($parts);
        
        if (empty($parts)) {
            return null;
        }

        $parentCode = implode($separator, $parts);

        // Verifica se já temos o ID no mapa
        if (isset($accountsMap[$parentCode])) {
            return $accountsMap[$parentCode];
        }

        // Busca no banco de dados
        $parent = ChartOfAccount::where('company_id', $companyId)
            ->where('code', $parentCode)
            ->first();

        if ($parent) {
            $accountsMap[$parentCode] = $parent->id;
            return $parent->id;
        }

        return null;
    }
}
