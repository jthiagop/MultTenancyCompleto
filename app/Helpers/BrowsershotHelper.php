<?php

namespace App\Helpers;

use Spatie\Browsershot\Browsershot;

class BrowsershotHelper
{
    /**
     * Retorna uma instância do Browsershot configurada com o caminho correto do Chrome
     */
    public static function create(): Browsershot
    {
        $chromePath = self::getChromePath();
        
        return Browsershot::html('')
            ->setChromePath($chromePath);
    }
    
    /**
     * Configura o caminho do Chrome em uma instância existente do Browsershot
     */
    public static function configureChromePath(Browsershot $browsershot): Browsershot
    {
        $chromePath = self::getChromePath();
        
        return $browsershot->setChromePath($chromePath);
    }
    
    /**
     * Encontra o caminho do Chrome disponível para o ambiente atual
     * 
     * @return string
     * @throws \Exception
     */
    public static function getChromePath(): string
    {
        // 1. Primeiro tenta usar a variável de ambiente (se configurada)
        $envPath = env('CHROME_PATH');
        if ($envPath && file_exists($envPath) && is_executable($envPath)) {
            return $envPath;
        }
        
        // 2. Lista de caminhos possíveis para verificar
        $possiblePaths = [];
        
        // Caminho de produção (www-data)
        $possiblePaths[] = '/var/www/.cache/puppeteer/chrome/linux-143.0.7499.169/chrome-linux64/chrome';
        
        // Caminho local Linux (usuário atual)
        $homeDir = getenv('HOME') ?: getenv('USERPROFILE');
        if ($homeDir) {
            $possiblePaths[] = $homeDir . '/.cache/puppeteer/chrome/linux-143.0.7499.169/chrome-linux64/chrome';
        }
        
        // Caminho alternativo local
        $possiblePaths[] = getcwd() . '/node_modules/.cache/puppeteer/chrome/linux-143.0.7499.169/chrome-linux64/chrome';
        
        // Busca dinâmica: procura qualquer versão do Chrome no cache do usuário atual
        if ($homeDir && is_dir($homeDir . '/.cache/puppeteer/chrome')) {
            $versions = glob($homeDir . '/.cache/puppeteer/chrome/linux-*/chrome-linux64/chrome');
            if (!empty($versions)) {
                // Ordena por versão (mais recente primeiro)
                usort($versions, function($a, $b) {
                    preg_match('/linux-(\d+\.\d+\.\d+\.\d+)/', $a, $matchA);
                    preg_match('/linux-(\d+\.\d+\.\d+\.\d+)/', $b, $matchB);
                    return version_compare($matchB[1] ?? '0', $matchA[1] ?? '0');
                });
                $possiblePaths = array_merge($possiblePaths, $versions);
            }
        }
        
        // Busca dinâmica no cache do www-data (produção)
        if (is_dir('/var/www/.cache/puppeteer/chrome')) {
            $versions = glob('/var/www/.cache/puppeteer/chrome/linux-*/chrome-linux64/chrome');
            if (!empty($versions)) {
                usort($versions, function($a, $b) {
                    preg_match('/linux-(\d+\.\d+\.\d+\.\d+)/', $a, $matchA);
                    preg_match('/linux-(\d+\.\d+\.\d+\.\d+)/', $b, $matchB);
                    return version_compare($matchB[1] ?? '0', $matchA[1] ?? '0');
                });
                $possiblePaths = array_merge($possiblePaths, $versions);
            }
        }
        
        // Busca dinâmica: macOS ARM (M1/M2/M3)
        if ($homeDir && is_dir($homeDir . '/.cache/puppeteer/chrome')) {
            $macVersions = glob($homeDir . '/.cache/puppeteer/chrome/mac_arm-*/chrome-mac-arm64/Google Chrome for Testing.app/Contents/MacOS/Google Chrome for Testing');
            if (!empty($macVersions)) {
                usort($macVersions, function($a, $b) {
                    preg_match('/mac_arm-(\d+\.\d+\.\d+\.\d+)/', $a, $matchA);
                    preg_match('/mac_arm-(\d+\.\d+\.\d+\.\d+)/', $b, $matchB);
                    return version_compare($matchB[1] ?? '0', $matchA[1] ?? '0');
                });
                $possiblePaths = array_merge($possiblePaths, $macVersions);
            }
        }
        
        // Busca dinâmica: macOS Intel (x64)
        if ($homeDir && is_dir($homeDir . '/.cache/puppeteer/chrome')) {
            $macIntelVersions = glob($homeDir . '/.cache/puppeteer/chrome/mac-*/chrome-mac/Google Chrome for Testing.app/Contents/MacOS/Google Chrome for Testing');
            if (!empty($macIntelVersions)) {
                usort($macIntelVersions, function($a, $b) {
                    preg_match('/mac-(\d+\.\d+\.\d+\.\d+)/', $a, $matchA);
                    preg_match('/mac-(\d+\.\d+\.\d+\.\d+)/', $b, $matchB);
                    return version_compare($matchB[1] ?? '0', $matchA[1] ?? '0');
                });
                $possiblePaths = array_merge($possiblePaths, $macIntelVersions);
            }
        }
        
        // 3. Verifica cada caminho possível
        foreach ($possiblePaths as $path) {
            if ($path && file_exists($path) && is_executable($path)) {
                return $path;
            }
        }
        
        // 4. Se não encontrou, tenta usar o Puppeteer padrão (pode funcionar se estiver instalado globalmente)
        // Neste caso, não configuramos o caminho e deixamos o Puppeteer descobrir automaticamente
        throw new \Exception(
            'Chrome não encontrado. ' .
            'Configure a variável CHROME_PATH no arquivo .env ou instale o Chrome usando: ' .
            'npx puppeteer browsers install chrome'
        );
    }
}
