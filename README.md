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

### Проблема с https у guzzle

 SSL certificate problem: unable to get local issuer certificate
 
 Download the latest cacert.pem from https://curl.haxx.se/ca/cacert.pem
 curl.cainfo=/path/to/downloaded/cacert.pem
 
 
###Новый тест для обращений к API
 php vendor\codeception\codeception\codecept generate:test unit LeadCreator
 
 php vendor\codeception\codeception\codecept run unit LeadCreatorTest
 
### Разные пути
 
 /bs-core/utils/version-info  
 
 /bs-core/dicts/custom/config
 
 
### После запуска лида в проверку получил
 
на команду php getDictionary.php --customer=b2 --path=/bs-core/main/leads/19
 
    [currentStatus] => TECH_FAULT
    [currentBusinessStatus] => FAILED

