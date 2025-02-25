<?php
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "phpapi1";

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    try{
        $sqlCategories = "create table if not exists categories(
                        id integer auto_increment primary key,
                        name varchar(100),
                        active integer default 1,
                        create_at timestamp default current_timestamp,
                        update_at timestamp default current_timestamp on update current_timestamp
                        )";

        $pdo->query($sqlCategories);

        $sqlProducts = "create table if not exists products(
                        id integer auto_increment primary key,
                        name varchar(100),
                        description text,
                        price double,
                        stock integer,
                        category_id integer,
                        foreign key (category_id) references categories(id) on delete cascade,
                        image text,
                        active integer default 1,
                        `order` integer,
                        display tinyint,
                        create_at timestamp default current_timestamp,
                        update_at timestamp default current_timestamp on update current_timestamp                  
                        )";
        $pdo->query($sqlProducts);

        $sqlUsers = "create table if not exists users(
                        id integer auto_increment primary key,
                        name varchar(100),
                        email varchar(255),
                        password text,
                        phone varchar(20),
                        address varchar(20),
                        role varchar(20),
                        active integer default 1,
                        create_at timestamp default current_timestamp,
                        update_at timestamp default current_timestamp on update current_timestamp                 
        )";
        $pdo->query($sqlUsers);

   
        $sqlCoupons = "create table if not exists coupons(
                id integer auto_increment primary key,
                code varchar(50),
                discount double,
                expires_at date,
                active integer default 1,
                create_at timestamp default current_timestamp,
                update_at timestamp default current_timestamp on update current_timestamp
        )";
        $pdo->query($sqlCoupons); 

        $sqlOrders = "create table if not exists orders(
                id integer auto_increment primary key,
                name varchar(100),
                user_id integer,
                total_price double,
                status varchar(20) default 'pending',
                active integer default 1,
                create_at timestamp default current_timestamp,
                update_at timestamp default current_timestamp on update current_timestamp,
                foreign key (user_id) references users(id) on delete cascade
        )";

        $pdo->query($sqlOrders);
        
        $sqlOrderItems = "create table if not exists order_items(
                        id integer auto_increment primary key,
                        order_id integer,
                        products_id integer,
                        quantity integer,
                        price double,
                        subtotal double,
                        active integer default 1,
                        create_at timestamp default current_timestamp,
                        update_at timestamp default current_timestamp on update current_timestamp,
                        foreign key (order_id) references orders(id) on delete cascade
        )";
        $pdo->query($sqlOrderItems);

        $sqlPayments = "create table if not exists payments(
                        id integer auto_increment primary key,
                        order_id integer,
                        user_id integer,
                        amount double,
                        payment_method varchar(20),
                        status varchar(20) default 'pending',
                        active integer default 1,
                        display tinyint,
                        create_at timestamp default current_timestamp,
                        update_at timestamp default current_timestamp on update current_timestamp,
                        foreign key (order_id) references orders(id) on delete cascade,
                        foreign key (user_id) references users(id) on delete cascade
        )";
        $pdo->query($sqlPayments);

        $sqlCarts = "create table if not exists carts(
                        id integer auto_increment primary key,
                        user_id integer,
                        products_id integer,
                        quantity integer,
                        active integer default 1,
                        create_at timestamp default current_timestamp,
                        update_at timestamp default current_timestamp on update current_timestamp,
                        foreign key (user_id) references users(id) on delete cascade,
                        foreign key (products_id) references products(id) on delete cascade
        )";
        $pdo->query($sqlCarts);	

        echo "Tables created successfully";
    }catch(PDOException $e){
        die("Connection failed: ". $e->getMessage());
    }
?>