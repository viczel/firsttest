# Тест API

На первом этапе будем пихать туда пачками данные и смотреть, разбросались ли они по разным базам

# Установка

composer require --dev "codeception/codeception"

Установил переменную среды PHPBIN C:\Users\admin\php-5630\php.exe

C:\Users\admin\data\testmultibase>vendor\codeception\codeception\codecept bootstrap

Could not open input file: @bin_dir@\codecept

Could not open input file: codecept

###Вместо мучений с этим добром сделал

php vendor\codeception\codeception\codecept bootstrap

php vendor\codeception\codeception\codecept generate:test unit DataGenerator

php vendor\codeception\codeception\codecept run unit DataGeneratorTest


