CREATE DATABASE tschi_dms;
use tschi_dms;
CREATE TABLE usertype(
    id int(11),
    name varchar(255),
    PRIMARY KEY(id)
);
CREATE TABLE login(
    id int(11),
    email varchar(255),
    password varchar(255),
    usertype_id int(11),
    PRIMARY KEY(id)
    FOREIGN KEY (usertype_id) References usertype(id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE profile(
    id int(11),
    first_name varchar(255),
    middle_name varchar(255),
    last_name varchar(255),
    login_id int(11),
    PRIMARY KEY(id),
    FOREIGN KEY (login_id) References login(id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE category(
    id int(11),
    name varchar(255),
    PRIMARY KEY(id)
);
CREATE TABLE status(
    id int(11),
    name varchar(255),
    PRIMARY KEY(id)
);
CREATE TABLE file(
    id int(11),
    name varchar(255),
    description text,
    upload_date datetime,
    category_id int(11),
    status_id int(11),
    login_id int(11),
    PRIMARY KEY(id),
    FOREIGN KEY (category_id) References category(id) ON DELETE CASCADE ON UPDATE CASCADE
    FOREIGN KEY (status_id) References status(id) ON DELETE CASCADE ON UPDATE CASCADE
    FOREIGN KEY (login_id) References login(id) ON DELETE CASCADE ON UPDATE CASCADE
);