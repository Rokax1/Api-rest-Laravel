
/*CREATE TABLE IF NOT EXISTS api-rest
use api-rest

create table users(
id              int(255) auto_incremental not null,
name            varchar(255) not null,
surname         varchar(50),
role            varchar(20),
email           varchar(255) not null,
password        varchar(255) not null,
description     text,
image           varchar(255),
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,
remember_token  varchar(255),
CONSTRAINT pk_users primary key (id)
)ENGINE=InnoDb;



CREATE TABLE categories(
id              int(255) auto_incremental not null,
name            varchar(100) not null,
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,
CONSTRAINT pk_categories primary key (id)
)ENGINE=InnoDb;

CREATE TABLE posts(
id              int(255) auto_incremental not null,
user_id         int(255)  not null,
category_id     int(255)  not null,
title           varchar(255) not null,
content         text not null,
image           varchar(255),
created_at      datetime DEFAULT NULL,
updated_at      datetime DEFAULT NULL,
CONSTRAINT pk_post PRIMARY KEY(id),
CONSTRAINT fk_post_user FOREIGN KEY(user_id) REFERENCES users (id),
CONSTRAINT fk_post_category FOREIGN KEY(category_id) REFERENCES categories (id)
)ENGINE=InnoDb;*/