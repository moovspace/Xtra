<?php
namespace Xtra\Mysql;
use \Exception;
use Xtra\Mysql\MysqlConnect;

class CreateTable extends MysqlConnect
{
    public function Create()
    {
        try{
            $table = "
            CREATE TABLE IF NOT EXISTS `users` (
                `id` bigint(22) NOT NULL AUTO_INCREMENT,
                `username` varchar(32) NOT NULL DEFAULT '',
                `email` varchar(180) NOT NULL DEFAULT '',
                `pass` varchar(32) NOT NULL DEFAULT '',
                `language` varchar(10) NOT NULL DEFAULT 'en',
                `role` int(3) NOT NULL DEFAULT '1',
                `active` int(3) NOT NULL DEFAULT '0',
                `ban` int(3) NOT NULL DEFAULT '0',
                `ip` varchar(200) NOT NULL DEFAULT '',
                `code` varchar(32) NOT NULL DEFAULT 'abc321',
                `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `firstname` varchar(50) NOT NULL DEFAULT '',
                `lastname` varchar(50) NOT NULL DEFAULT '',
                `country` varchar(100) NOT NULL DEFAULT 'Poland',
                `district` varchar(100) NOT NULL DEFAULT '',
                `city` varchar(50) NOT NULL DEFAULT '',
                `address` varchar(100) NOT NULL DEFAULT '',
                `zipcode` varchar(10) NOT NULL DEFAULT '',
                `mobile` varchar(50) NOT NULL DEFAULT '',
                `mail` varchar(250) NOT NULL DEFAULT '',
                `www` varchar(250) NOT NULL DEFAULT '',
                `social` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                `about` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                `sex` enum('men','woman') DEFAULT 'men',
                `lng` decimal(10,6) NOT NULL DEFAULT '0.000000',
                `lat` decimal(10,6) NOT NULL DEFAULT '0.000000',
                PRIMARY KEY (`id`),
                UNIQUE KEY `ukey1` (`email`),
                UNIQUE KEY `ukey` (`username`),
                KEY `id` (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $r = $this->pdo->query($table);

        }catch(Exception $e){
            throw new Exception("Error create table: ". $e->getMessage(), 1);
        }
    }

    function ProductsTable(){
        $tbProducts = "
            CREATE TABLE IF NOT EXISTS `products` (
                `id` bigint(22) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL DEFAULT '',
                PRIMARY KEY (`id`),
                UNIQUE KEY `ukey1` (`email`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        // variantId => color, model, size, material
        $tbProductsVariants = "
            CREATE TABLE IF NOT EXISTS `products_variants` (
                `id` bigint(22) NOT NULL AUTO_INCREMENT,
                `categoryId` bigint(22) NOT NULL,
                `productId` bigint(22) NOT NULL,
                `parentId` bigint(22) NOT NULL,
                `variantId` varchar(100) NOT NULL DEFAULT '',
                `variantName` varchar(100) NOT NULL DEFAULT '',
                `stockTotal` bigint(22) NOT NULL,
                `wholeSale` bigint(22) NOT NULL,
                `price` decimal(11,2) NOT NULL DEFAULT 0.00,
                `priceSale` decimal(11,2) NOT NULL DEFAULT 0.00,
                PRIMARY KEY (`id`),
                UNIQUE KEY `ukey1` (`email`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
    }

    /**
     * [addUser Add user to database]
     * @param string $alias Username
     * @param string $email User email address
     * @param string $pass  User password
     * @return  User id or 0, -1 -> Error mysql, -2 -> error username, -3 -> error email, -4 -> user exists
     *
     */
    function AddUser($alias, $email, $pass)
    {
        $md5 = md5($pass);
        try{
            // Check username
            if(empty($alias) || strlen($alias) < 2){ return -2; }
            // Check email
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                // Add to database
                $r = $this->pdo->prepare("INSERT INTO users(username,email,pass) VALUES(:s1,:s2,:s3)");
                $r->execute(array(':s1' => $alias, ':s2' => $email, ':s3' => $md5));
                // Return user id or 0
                return (int) $this->pdo->lastInsertId();
            }else{
                return -3;
            }
        }catch(Exception $er){
            // Duplicate key user exists
            if($er->errorInfo[1] == 1062){
                return -4;
            }
            return -1;
        }
    }
}
?>
