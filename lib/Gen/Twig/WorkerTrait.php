<?php

namespace Gen\Twig;

trait WorkerTrait
{
    public function getTwig(\Gen\Config $config, $path, $twigTemplate, $data = []) {
        $loader = new \Twig_Loader_Filesystem([$config->get('src') . '/' . $config->get('templates'), $path]);
        $twig = new \Twig_Environment($loader);

        if (is_dir($config->get('src') . '/' . $config->get('extensions'))) {
            require_once __DIR__ . '/ExtensionBase.php';
            foreach (glob($config->get('src') . '/' . $config->get('extensions') . '/*.php') as $file) {
                require_once $file;
                $extension = '\\Gen\\Twig\\' . basename($file, '.php');
                $twig->addExtension(new $extension($path, $twigTemplate, $config, $data));
            }
        }
        return $twig;
    }

    public function writeHtmlFromTwig(\Twig_Environment $twig, \Gen\Config $config, $path, $twigTemplate, $htmlFile, $data = [])
    {
        $template = $twig->loadTemplate($twigTemplate);
        $path     = str_replace($config->get('src') . '/' . $config->get('content'), $config->get('dest'), $path);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        file_put_contents($path . '/' . $htmlFile, $template->render($data));
        return $path . '/' . $htmlFile;
    }
}
