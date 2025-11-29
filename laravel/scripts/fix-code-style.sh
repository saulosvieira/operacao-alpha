#!/bin/bash

# Script para formatar o código automaticamente

# Verifica se o PHP_CodeSniffer está instalado
if ! command -v phpcs &> /dev/null; then
    echo "PHP_CodeSniffer não encontrado. Instalando..."
    composer require --dev squizlabs/php_codesniffer
fi

# Verifica se o PHP-CS-Fixer está instalado
if ! command -v php-cs-fixer &> /dev/null; then
    echo "PHP-CS-Fixer não encontrado. Instalando..."
    curl -L https://cs.symfony.com/download/php-cs-fixer-v3.phar -o php-cs-fixer
    chmod a+x php-cs-fixer
    sudo mv php-cs-fixer /usr/local/bin/php-cs-fixer
fi

echo "Formatando código com PHP_CodeSniffer..."
./vendor/bin/phpcbf --standard=phpcs.xml --extensions=php --ignore=vendor/*,bootstrap/*,storage/*,public/*,node_modules/* .

echo "Formatando código com PHP-CS-Fixer..."
./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --allow-risky=yes

echo "Formatando código com Laravel Pint..."
./vendor/bin/pint

echo "Formatação concluída!"
