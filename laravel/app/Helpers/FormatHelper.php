<?php

namespace App\Helpers;

class FormatHelper
{
    /**
     * Formata um CPF ou CNPJ com pontuação
     */
    public static function formatCpfCnpj(?string $document): string
    {
        if (empty($document)) {
            return '-';
        }

        // Remove caracteres não numéricos
        $document = preg_replace('/[^0-9]/', '', $document);

        // Formata como CPF (11 dígitos)
        if (strlen($document) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $document);
        }

        // Formata como CNPJ (14 dígitos)
        if (strlen($document) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $document);
        }

        // Retorna o documento sem formatação se não for CPF nem CNPJ
        return $document;
    }

    /**
     * Remove a formatação de CPF/CNPJ, deixando apenas números
     */
    public static function unformatCpfCnpj(?string $document): string
    {
        if (empty($document)) {
            return '';
        }

        return preg_replace('/[^0-9]/', '', $document);
    }

    /**
     * Formata um número de telefone
     */
    public static function formatPhone(?string $phone): string
    {
        if (empty($phone)) {
            return '-';
        }

        // Remove caracteres não numéricos
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Formata como celular (11 dígitos) com DDD
        if (strlen($phone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        }

        // Formata como telefone fixo (10 dígitos) com DDD
        if (strlen($phone) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $phone);
        }

        // Retorna o telefone sem formatação se não for um formato conhecido
        return $phone;
    }

    /**
     * Formata um valor monetário
     */
    public static function formatCurrency(float $value, string $currency = 'BRL'): string
    {
        $formatter = new \NumberFormatter('pt_BR', \NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($value, $currency);
    }

    /**
     * Formata uma data para o formato brasileiro
     */
    public static function formatDateBr(?string $date, string $format = 'd/m/Y'): string
    {
        if (empty($date)) {
            return '-';
        }

        try {
            $date = new \DateTime($date);

            return $date->format($format);
        } catch (\Exception $e) {
            return $date;
        }
    }

    /**
     * Formata uma data e hora para o formato brasileiro
     */
    public static function formatDateTimeBr(?string $dateTime, string $format = 'd/m/Y H:i:s'): string
    {
        return self::formatDateBr($dateTime, $format);
    }
}
