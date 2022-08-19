CREATE DATABASE IF NOT EXISTS etslp;

CREATE TABLE IF NOT EXISTS ingredientes (
    id INT not null  PRIMARY KEY AUTO_INCREMENT,
    nombre_producto VARCHAR(100),
    unidades_medida VARCHAR(100),
    descripcion_producto TEXT,
    marca_producto VARCHAR(100),
    fecha_ingreso DATE,
    cantidad INTEGER
);

INSERT INTO ingredientes (nombre_producto, unidades_medida, descripcion_producto, marca_producto, fecha_ingreso, cantidad)
VALUES ('Arroz', 'Kilogramos', 'Arroz blanco de grano largo', 'Gallo', '2020-05-01', 10),
       ('Fideos', 'Kilogramos', 'Fideos de trigo', 'Don Vito', '2020-05-02', 20),
       ('Lentejas', 'Kilogramos', 'Lentejas de color', 'La Serenísima', '2020-05-03', 30),
       ('Harina', 'Kilogramos', 'Harina de trigo', 'Tres Coronas', '2020-05-04', 40),
       ('Aceite', 'Litros', 'Aceite de girasol', 'Crisco', '2020-05-05', 50),
       ('Sal', 'Kilogramos', 'Sal fina', 'Cristal', '2020-05-06', 60),
       ('Azúcar', 'Kilogramos', 'Azúcar morena', 'Tres Coronas', '2020-05-07', 70),
       ('Tomates', 'Unidades', 'Tomates maduros', 'Tienda Inglesa', '2020-05-08', 80),
       ('Cebollas', 'Unidades', 'Cebollas blancas', 'Tienda Inglesa', '2020-05-09', 90),
       ('Pimienta', 'Gramos', 'Pimienta negra en grano', 'Tienda Inglesa', '2020-05-10', 100),
       ('Vinagre', 'Litros', 'Vinagre de vino blanco', 'Tienda Inglesa', '2020-05-11', 110),
       ('Agua', 'Litros', 'Agua mineral', 'Ciel', '2020-05-12', 120),
       ('Carne', 'Kilogramos', 'Carne de vaca', 'Tienda Inglesa', '2020-05-13', 130),
       ('Pollo', 'Kilogramos', 'Pollo fresco', 'Tienda Inglesa', '2020-05-14', 140),
       ('Cerveza', 'Litros', 'Cerveza negra', 'Quilmes', '2020-05-15', 150),
       ('Vino', 'Botellas', 'Vino tinto', 'Don Sebastián', '2020-05-16', 160),
       ('Leche', 'Litros', 'Leche entera', 'La Serenísima', '2020-05-17', 170),
       ('Manteca', 'Kilogramos', 'Manteca de cerdo', 'Tres Coronas', '2020-05-18', 180),
       ('Jugo', 'Litros', 'Jugo de naranja', 'Del Valle', '2020-05-19', 190),
       ('Frutas', 'Kilogramos', 'Manzanas', 'Tienda Inglesa', '2020-05-20', 200);
