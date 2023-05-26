##Simple Ecommerce
This is a simple ecommerce project to demonstrate what happens between when an order
is placed on a retailer platform such as ebay and when the order is received by the
customer.

This project integrates with the Ebay API platform to load orders/items and ships the
orders by integrating with the EasyPost API.

NB: The ebay integration currently uses a mocked response provided in the Ebay API
documentation.

NB: Although the EasyPost integration actually queries the EasyPost API, all queries
and responses are obtained using a sandbox/test account.
https://www.easypost.com/docs/api#shipments

###To run project, perform the following commands on the terminal, at the root of the project
1. docker-compose up -d
2. symfony console doctrine:migrations:migrate
3. php bin/console app:load:customer:orders -p ebay #to load orders into the system
4. php bin/console app:ship:easypost:order -o {order-ref} #to ship orders via EasyPost
using the order-ref obtained in step 3.


###To run tests

1. First open localhost:8081 on the browser and login with username `root` and password
`pass@123`. Note these are the details used during development.
NB: The database is already created called `ecommerceDataStorage`
2. Create a Test database called `ecommerceDataStorageTest`
3. Copy the structure of `ecommerceDataStorage` to `ecommerceDataStorageTest`
4. On the terminal, run the following commands to run the tests as required:


    symfony php bin/phpunit tests
    symfony php bin/phpunit tests/Integration/Retailer/Ebay/LoadEbayOrderServiceTest.php
    symfony php bin/phpunit tests/Integration/Shipper/EasyPostOrderShipmentServiceTest.php
    
    symfony php bin/phpunit tests/Entity/ItemTest.php
    symfony php bin/phpunit tests/Entity/OrderTest.php
    symfony php bin/phpunit tests/Entity/ShipmentTest.php