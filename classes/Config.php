<?php

Class Config
{
    public static function baseUrl(): string
    {
        $absolutePath = str_replace('\\', '/', __DIR__);

        // Determine the absolute file system path of the document root
        $documentRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
        
        // Compute the base URL by removing the document root from the absolute script path
        $baseUrl = str_replace($documentRoot, '', $absolutePath);
        
        // As it is an absolute path, it must start with a slash
        $baseUrl = '/' . ltrim($baseUrl, '/');

        return $baseUrl;        
    }
}