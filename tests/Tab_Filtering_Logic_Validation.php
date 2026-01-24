<?php

/**
 * Test: Tab Filtering Logic Validation
 * 
 * This file demonstrates the filtering logic used in the reconciliation tabs.
 * It's a standalone validation script to test the filter conditions.
 */

// Simulated conciliation records (PHP array)
$conciliations = [
    (object)['id' => 1, 'amount' => 100.50],      // Recebimento (positive)
    (object)['id' => 2, 'amount' => -50.25],      // Pagamento (negative)
    (object)['id' => 3, 'amount' => 200.00],      // Recebimento (positive)
    (object)['id' => 4, 'amount' => -150.75],     // Pagamento (negative)
    (object)['id' => 5, 'amount' => 75.30],       // Recebimento (positive)
];

// Filtering logic (PHP equivalent to Blade filter)
$conciliacoesTodas = $conciliations;
$conciliacoesRecebimentos = array_filter($conciliations, fn($c) => $c->amount > 0);
$conciliacoesPagamentos = array_filter($conciliations, fn($c) => $c->amount < 0);

// Tab counts
$tabs = [
    ['key' => 'all', 'label' => 'Todos', 'count' => count($conciliacoesTodas)],
    ['key' => 'received', 'label' => 'Recebimentos', 'count' => count($conciliacoesRecebimentos)],
    ['key' => 'paid', 'label' => 'Pagamentos', 'count' => count($conciliacoesPagamentos)],
];

// Test Results
echo "=== TAB FILTERING LOGIC TEST ===\n\n";

foreach ($tabs as $tab) {
    echo sprintf(
        "Tab: %-15s | Count: %d\n",
        $tab['label'],
        $tab['count']
    );
}

echo "\n=== DETAILED BREAKDOWN ===\n\n";

echo "Todos (All):\n";
foreach ($conciliacoesTodas as $c) {
    echo sprintf("  - ID: %d, Amount: R$ %.2f\n", $c->id, $c->amount);
}

echo "\nRecebimentos (Income):\n";
foreach ($conciliacoesRecebimentos as $c) {
    echo sprintf("  - ID: %d, Amount: R$ %.2f\n", $c->id, $c->amount);
}

echo "\nPagamentos (Expenses):\n";
foreach ($conciliacoesPagamentos as $c) {
    echo sprintf("  - ID: %d, Amount: R$ %.2f\n", $c->id, $c->amount);
}

echo "\n=== EXPECTED RESULTS ===\n\n";
echo "Total Conciliations: 5\n";
echo "Expected Todos Count: 5 ✓\n";
echo "Expected Recebimentos Count: 3 (IDs: 1, 3, 5) ✓\n";
echo "Expected Pagamentos Count: 2 (IDs: 2, 4) ✓\n";

echo "\n=== ACTUAL RESULTS ===\n\n";
echo "Todos Count: " . count($conciliacoesTodas) . "\n";
echo "Recebimentos Count: " . count($conciliacoesRecebimentos) . "\n";
echo "Pagamentos Count: " . count($conciliacoesPagamentos) . "\n";

// Validation
$todosCorrect = count($conciliacoesTodas) === 5;
$recebimentosCorrect = count($conciliacoesRecebimentos) === 3;
$pagamentosCorrect = count($conciliacoesPagamentos) === 2;

echo "\n=== VALIDATION ===\n\n";
echo ($todosCorrect ? "✓" : "✗") . " Todos tab has correct count (5)\n";
echo ($recebimentosCorrect ? "✓" : "✗") . " Recebimentos tab has correct count (3)\n";
echo ($pagamentosCorrect ? "✓" : "✗") . " Pagamentos tab has correct count (2)\n";

$allCorrect = $todosCorrect && $recebimentosCorrect && $pagamentosCorrect;
echo "\n" . ($allCorrect ? "✓ ALL TESTS PASSED" : "✗ SOME TESTS FAILED") . "\n";

return $allCorrect ? 0 : 1;
