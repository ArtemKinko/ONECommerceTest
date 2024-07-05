CREATE TABLE category
(
    id   serial not null primary key,
    name text   not null
);

CREATE TABLE products
(
    id          serial not null primary key,
    name        text   not null,
    category_id int    not null references category (id),
    price       float  not null
);

CREATE TABLE request
(
    id         serial    not null primary key,
    product_id int       not null references products (id),
    amount     int       not null,
    order_time timestamp not null
);

CREATE TABLE statistic
(
    id           serial not null primary key,
    request_date date   not null,
    category_id  int    not null references category (id),
    amount       int    not null
);

CREATE FUNCTION trigger() RETURNS trigger AS
'
    DECLARE
        c_id int;
    BEGIN
        c_id := (SELECT category_id
                 FROM products
                 WHERE id = new.product_id);
        IF (SELECT count(*)
            FROM (SELECT *
                  FROM statistic as s
                  WHERE date_part(''year'', request_date) = date_part(''year'', new.order_time)
                    AND date_part(''month'', request_date) = date_part(''month'', new.order_time)
                    AND date_part(''day'', request_date) = date_part(''day'', new.order_time)
                    AND category_id = c_id) as "s") = 0
        THEN
            INSERT INTO statistic (request_date, category_id, amount)
            VALUES (new.order_time, c_id, new.amount);
        ELSE
            UPDATE statistic
            SET amount = amount + new.amount
            WHERE date_part(''year'', request_date) = date_part(''year'', new.order_time)
              AND date_part(''month'', request_date) = date_part(''month'', new.order_time)
              AND date_part(''day'', request_date) = date_part(''day'', new.order_time)
              AND category_id = c_id;
        END IF;
        RETURN new;
    END;
' LANGUAGE plpgsql;

CREATE TRIGGER statistic_append
    AFTER INSERT
    ON request
    FOR EACH ROW
EXECUTE PROCEDURE trigger();

