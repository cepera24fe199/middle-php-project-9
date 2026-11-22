CREATE TABLE IF NOT EXISTS urls (
    id integer AUTO_INCREMENT PRIMARY KEY,
    name varchar(255) NOT NULL UNIQUE,
    created_at datetime NOT NULL
);

CREATE TABLE IF NOT EXISTS url_checks (
    id integer AUTO_INCREMENT PRIMARY KEY,
    url_id integer NOT NULL,
    status_code integer,
    h1 longtext,
    title longtext,
    description longtext,
    created_at datetime NOT NULL,
    FOREIGN KEY (url_id) REFERENCES urls (id)
);
