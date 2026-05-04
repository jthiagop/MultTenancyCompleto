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
     * Configura o caminho do Chrome em uma instância existente do Browsershot.
     *
     * Aplica conjuntos de argumentos diferentes por SO: o conjunto agressivo
     * usado em produção (Linux) trava o handshake do DevTools no macOS local,
     * onde o sandbox e a infra de memória do Chrome se comportam de forma
     * diferente.
     */
    public static function configureChromePath(Browsershot $browsershot): Browsershot
    {
        $chromePath = self::getChromePath();

        $browsershot->setChromePath($chromePath);

        $browsershot->setOption('args', self::getChromeArgs());

        // Timeout do Browsershot. PHP deve ser configurado com limite MAIOR
        // que este valor para que a exceção de timeout consiga ser tratada
        // em vez de o PHP abortar a request inteira.
        $browsershot->timeout(60);

        // Para HTML totalmente inline (sem fontes/imagens externas) o evento
        // "load" dispara assim que o parser conclui. Já "networkidle0" pode
        // travar indefinidamente quando --disable-background-networking está
        // ativo (Linux) ou quando há service workers persistentes.
        $browsershot->setOption('waitUntil', 'load');

        return $browsershot;
    }

    /**
     * Retorna o conjunto de argumentos do Chromium adequado ao SO atual.
     *
     * Linux (produção): conjunto agressivo otimizado para containers e
     * servidores compartilhados sem GPU.
     * macOS / Windows (dev): conjunto mínimo — flags Linux como
     * --no-sandbox e --disable-features=site-per-process causam deadlock
     * no handshake do DevTools.
     *
     * @return list<string>
     */
    public static function getChromeArgs(): array
    {
        if (PHP_OS_FAMILY === 'Darwin' || PHP_OS_FAMILY === 'Windows') {
            return [
                '--headless=new',
                '--disable-gpu',
                '--no-first-run',
                '--no-default-browser-check',
                '--disable-extensions',
                '--disable-background-networking',
                '--disable-sync',
                '--disable-translate',
            ];
        }

        return [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-gpu',
            '--disable-software-rasterizer',
            '--disable-extensions',
            '--disable-background-networking',
            '--disable-sync',
            '--disable-translate',
            '--no-first-run',
            '--disable-default-apps',
            '--disable-hang-monitor',
            '--disable-popup-blocking',
            '--disable-prompt-on-repost',
            '--disable-client-side-phishing-detection',
            '--disable-component-update',
            '--disable-ipc-flooding-protection',
            '--disable-features=TranslateUI,site-per-process',
        ];
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
