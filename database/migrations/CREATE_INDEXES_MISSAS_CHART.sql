-- ===================================================
-- Script de Otimização: Índices para Gráfico de Missas
-- ===================================================

-- IMPORTANTE: Sempre fazer backup antes de alterar o banco de dados!

-- 1. Índice composto para filtros de empresa e conciliação
CREATE INDEX idx_bank_statements_company_conciliado 
ON bank_statements(company_id, conciliado_com_missa);

-- 2. Índice para relacionamento com horario_missa
CREATE INDEX idx_bank_statements_horario_missa 
ON bank_statements(horario_missa_id);

-- 3. Índice para filtro de datas
CREATE INDEX idx_bank_statements_transaction_date 
ON bank_statements(transaction_datetime);

-- 4. Índice alternativo para dtposted (fallback)
CREATE INDEX idx_bank_statements_dtposted 
ON bank_statements(dtposted);

-- 5. Índice composto otimizado para a query específica do gráfico
-- Este é o mais importante para performance
CREATE INDEX idx_bank_statements_chart_query 
ON bank_statements(
    company_id, 
    conciliado_com_missa, 
    horario_missa_id, 
    transaction_datetime
);

-- ===================================================
-- Verificar índices criados
-- ===================================================

-- MySQL
SHOW INDEX FROM bank_statements;

-- PostgreSQL (se usar)
-- SELECT * FROM pg_indexes WHERE tablename = 'bank_statements';

-- ===================================================
-- Analisar performance antes e depois
-- ===================================================

-- ANTES de criar índices, executar:
-- EXPLAIN SELECT COUNT(*) FROM bank_statements 
-- WHERE company_id = ? 
--   AND conciliado_com_missa = 1 
--   AND horario_missa_id IS NOT NULL;

-- DEPOIS de criar índices, reexecutar EXPLAIN
-- A linha "type" deve mudar de "ALL" para "range" ou "ref"

-- ===================================================
-- LIMPEZA (caso precise remover índices)
-- ===================================================

-- DROP INDEX idx_bank_statements_company_conciliado ON bank_statements;
-- DROP INDEX idx_bank_statements_horario_missa ON bank_statements;
-- DROP INDEX idx_bank_statements_transaction_date ON bank_statements;
-- DROP INDEX idx_bank_statements_dtposted ON bank_statements;
-- DROP INDEX idx_bank_statements_chart_query ON bank_statements;

-- ===================================================
-- Estatísticas do Banco
-- ===================================================

-- Contar registros por empresa para entender volume
-- SELECT company_id, COUNT(*) as total_statements
-- FROM bank_statements
-- WHERE conciliado_com_missa = 1 AND horario_missa_id IS NOT NULL
-- GROUP BY company_id
-- ORDER BY total_statements DESC;

-- Verificar crescimento de dados
-- SELECT 
--     DATE(created_at) as data,
--     COUNT(*) as novos_registros
-- FROM bank_statements
-- GROUP BY DATE(created_at)
-- ORDER BY data DESC
-- LIMIT 30;
