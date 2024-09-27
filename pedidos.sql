-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-09-2024 a las 08:08:26
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `uvg`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `cliente_username` varchar(255) NOT NULL,
  `estado` varchar(50) NOT NULL DEFAULT 'Pendiente',
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `cliente_username`, `estado`, `fecha`) VALUES
(1, 'cliente', 'Listo', '2024-09-24 04:29:03'),
(2, 'cliente', 'Listo', '2024-09-24 04:29:11'),
(3, 'cliente', 'Listo', '2024-09-24 04:29:12'),
(4, 'cliente', 'Listo', '2024-09-24 04:29:14'),
(5, 'cliente', 'Listo', '2024-09-24 04:29:15'),
(6, 'cliente', 'Listo', '2024-09-24 04:29:17'),
(7, 'cliente', 'Listo', '2024-09-24 04:29:18'),
(8, 'cliente', 'Listo', '2024-09-24 04:31:18'),
(9, 'cliente', 'Listo', '2024-09-24 04:31:20'),
(10, 'cliente', 'Listo', '2024-09-24 04:31:20'),
(11, 'cliente', 'Listo', '2024-09-24 04:44:21'),
(12, 'cliente', 'Listo', '2024-09-24 04:58:14'),
(13, 'cliente', 'Listo', '2024-09-24 05:03:42'),
(14, 'cliente', 'Listo', '2024-09-24 05:10:33'),
(15, 'cliente', 'Listo', '2024-09-24 05:10:34'),
(16, 'cliente', 'Listo', '2024-09-24 05:10:34'),
(17, 'cliente', 'Listo', '2024-09-24 05:10:35'),
(18, 'cliente', 'Listo', '2024-09-24 05:10:35'),
(19, 'cliente', 'Listo', '2024-09-24 05:10:35'),
(20, 'cliente', 'En Proceso', '2024-09-24 05:10:35'),
(21, 'cliente', 'Listo', '2024-09-24 05:10:35'),
(22, 'cliente', 'Pendiente', '2024-09-24 05:10:36'),
(23, 'cliente', 'Listo', '2024-09-24 05:10:36'),
(24, 'cliente', 'En Proceso', '2024-09-24 05:10:36'),
(25, 'cliente', 'En Proceso', '2024-09-24 05:10:36'),
(26, 'cliente', 'Listo', '2024-09-24 05:10:36'),
(27, 'cliente', 'Listo', '2024-09-24 05:10:37'),
(28, 'cliente', 'Pendiente', '2024-09-24 05:10:37'),
(29, 'cliente', 'Listo', '2024-09-24 05:10:37'),
(30, 'cliente', 'Pendiente', '2024-09-24 05:10:43'),
(31, 'cliente', 'Pendiente', '2024-09-24 05:45:20'),
(32, 'cliente', 'Listo', '2024-09-24 05:47:08');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
